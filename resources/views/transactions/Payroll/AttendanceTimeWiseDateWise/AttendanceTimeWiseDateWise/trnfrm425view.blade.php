@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Attendance - Time wise - Date</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button id="btnSave"   class="btn topnavbt" disabled="disabled"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt"  id="btnUndo" disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</a>
                        <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
   
    <div class="container-fluid purchase-order-view filter">     
      <form id="frm_mst_edit" method="POST" onsubmit="return validateForm()" class="needs-validation"  >       
        @CSRF
        {{isset($HDR->ATTTNDID) ? method_field('PUT') : '' }}
       <div class="inner-form">              
             <div class="row">
               <div class="col-lg-2 pl"><p>Doc No*</p></div>
                 <div class="col-lg-2 pl">
                  <input {{$ActionStatus}}  type="text" name="DOC_NO" id="DOC_NO" value="{{ $HDR->DOCNO }}" class="form-control mandatory" tabindex="1" maxlength="100" autocomplete="off" style="text-transform:uppercase" autofocus >
                 <span class="text-danger" id="ERROR_DOC_NO_REF"></span>                             
                 </div>

                 <div class="col-lg-2 pl"><p>Date*</p></div>
                 <div class="col-lg-2 pl">
                   <input {{$ActionStatus}}  type="date" name="DOC_DT" id="DOC_DT" value="{{ $HDR->DOCDATE }}" class="form-control"  maxlength="100" > 
                 </div>

               <div class="col-lg-2 pl"><p>Pay Period Code*</p></div>
                 <div class="col-lg-2 pl">
                   <select {{$ActionStatus}}  name="PAYPERIODID_REF" id="PAYPERIODID_REF" class="form-control mandatory" onchange="getPayPrName(this.value)" tabindex="4">
                     <option value="" selected="">Select</option>
                     @foreach($objList as $val)
                     <option {{isset($HDR->PAYPERIODID_REF) && $HDR->PAYPERIODID_REF == $val-> PAYPERIODID ?'selected="selected"':''}} value="{{ $val-> PAYPERIODID }}">{{$val->PAY_PERIOD_CODE}}-{{$val->PAY_PERIOD_DESC}}</option>
                     @endforeach
                   </select>
                   <span class="text-danger" id="ERROR_PAYPERIODID_REF"></span>                             
                 </div>
               </div>

               <div class="row">
                 <div class="col-lg-2 pl"><p>Description</p></div>
                 <div class="col-lg-2 pl">
                   <input {{$ActionStatus}}  type="text" id="PAY_PERIOD_DESC" value="{{ $HDR->PAY_PERIOD_DESC }}" class="form-control" readonly  maxlength="100" > 
                 </div>
                 <div class="col-lg-2 pl"><p>Shift *</p></div>
                    <div class="col-lg-2 pl">
                    <input {{$ActionStatus}}  type="text" name="TXTSHIFT" id="TXTSHIFT" value="{{ $HDR->SHIFT_NAME }}"  class="form-control mandatory"  autocomplete="off" readonly/>
                    <input type="hidden" name="SHIFTID_REF" id="SHIFTID_REF" value="{{ $HDR->SHIFTID_REF }}" class="form-control" autocomplete="off" />
                      <input type="hidden" id="focusid" >
                      <span class="text-danger" id="ERROR_SHIFTID_REF"></span>                             
                    </div> 
                    @php $DEF_TIME_FROM = date('H:i', strtotime($HDR->DEF_TIME_FROM))
                    @endphp
                    <div class="col-lg-2 pl"><p>Defined From Time</p></div>
                    <div class="col-lg-2 pl">
                      <input {{$ActionStatus}}  type="time" id="DEFROMTIME" name="DEFROMTIME" value="{{ $DEF_TIME_FROM }}"  class="form-control" readonly  maxlength="100" > 
                    </div>           
             </div>
             <div class="row">
                    @php $DEF_TIME_TO = date('H:i', strtotime($HDR->DEF_TIME_TO))
                    @endphp
                <div class="col-lg-2 pl"><p>Defined To Time</p></div>
                <div class="col-lg-2 pl">
                  <input {{$ActionStatus}}  type="time" id="DEFTOTIME" name="DEFTOTIME" value="{{ $DEF_TIME_TO }}" class="form-control" readonly  maxlength="100" > 
                </div>
             </div>

             <br>
             
             <div class="row">
                      <div class="tab-content">
                      <div id="Material" class="tab-pane fade in active">
                          <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                            <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                              <thead id="thead1"  style="position: sticky;top: 0">                      
                                <tr>  
                                <th>Employee Code </th>                         
                                <th>Name </th>                         
                                <th>From Time </th>
                                <th>To Time</th>
                                <th>Total Hours</th>
                                <th>Attendance Status</th>
                                <th>Remarks</th>
                                <th>Action </th>
                              </tr>                      
                                
                            </thead>
                              <tbody>
                              @if(!empty($MAT))
                              @php $n=1; @endphp
                              @foreach($MAT as $key => $row)
                                  @php  $TIME_FROM = date('H:i', strtotime($row->TIME_FROM));
                                        $TIME_TO = date('H:i', strtotime($row->TIME_TO));
                                        $TOTALHOURS = date('H:i', (strtotime($row->TIME_TO)-strtotime($row->TIME_FROM)));
                                  @endphp
                                <tr  class="participantRow">
                                  <td><input {{$ActionStatus}}  class="form-control" type="text" name="txtEMPID[]"    id={{"txtEMPID_".$key}}   value="{{ $row->EMPCODE}}"  autocomplete="off" readonly onclick="getEmployee(this.id)"/></td>
                                  <td hidden><input type="hidden" name="EMPID_REF[]"   id={{"EMPID_REF_".$key}}    autocomplete="off" value="{{ $row->EMPID_REF}}" /></td>
                                  <td><input {{$ActionStatus}}  class="form-control" type="text" name="FNAME[]"       id ={{"FNAME_".$key}}     value="{{ $row->FNAME}}"   autocomplete="off" style="width: 99%" readonly></td>
                                  <td><input {{$ActionStatus}}  class="form-control" type="time" name="FROMTIME[]"    id ={{"FROMTIME_".$key}}  value="{{ $TIME_FROM}}" autocomplete="off" style="width: 99%" onchange="getFromTime(this.id)"></td>
                                  <td><input {{$ActionStatus}}  class="form-control" type="time" name="TOTIME[]"      id ={{"TOTIME_".$key}}    value="{{ $TIME_TO}}" autocomplete="off" style="width: 99%" onchange="getFromTime(this.id)"></td>
                                  <td><input {{$ActionStatus}}  class="form-control" type="text" name="TOTALHOURS[]"  id ={{"TOTALHOURS_".$key}} value="{{ $TOTALHOURS}}" autocomplete="off" style="width: 99%" readonly></td>
                                  
                                  <td><select {{$ActionStatus}}  name="ASID_REF[]" id={{"ASID_REF_".$key}} class="form-control mandatory">
                                    <option value="" selected="">Select</option>
                                    @foreach($objAttendance as $val)
                                      <option {{isset($row->ATTENDANCE_STATUS) && $row->ATTENDANCE_STATUS == $val-> ATTENDANCE_CODEID ?'selected="selected"':''}} value="{{$val->ATTENDANCE_CODEID}}">{{$val->ATTENDANCE_CODE}}-{{$val->ATTENDANCE_CODE_DESC}}</option>
                                    @endforeach
                                  </select>
                                </td>
                                  <td><input {{$ActionStatus}}  class="form-control" type="text" name="REMARKS[]"     id ={{"REMARKS_".$key}}   value="{{ $row->REMARKS}}"  autocomplete="off" style="width: 99%"></td>
                                  
                                  <td>
                                    <button {{$ActionStatus}}  class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                                    <button {{$ActionStatus}}  class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                                  </td>
                                </tr>
                              @endforeach
                              @else
                                <tr  class="participantRow">
                                  <td><input {{$ActionStatus}}  class="form-control" type="text" name="txtEMPID[]"    id="txtEMPID_0"     autocomplete="off" readonly onclick="getEmployee(this.id)"/></td>
                                  <td hidden><input type="hidden"              name="EMPID_REF[]"   id="EMPID_REF_0"    autocomplete="off" /></td>
                                  <td><input {{$ActionStatus}}  class="form-control" type="text" name="FNAME[]"       id ="FNAME_0"       autocomplete="off" style="width: 99%" readonly></td>
                                  <td><input {{$ActionStatus}}  class="form-control" type="time" name="FROMTIME[]"    id ="FROMTIME_0"    autocomplete="off" style="width: 99%" onchange="getFromTime(this.id)"></td>
                                  <td><input {{$ActionStatus}}  class="form-control" type="time" name="TOTIME[]"      id ="TOTIME_0"      autocomplete="off" style="width: 99%" onchange="getFromTime(this.id)"></td>
                                  <td><input {{$ActionStatus}}  class="form-control" type="text" name="TOTALHOURS[]"  id ="TOTALHOURS_0"  autocomplete="off" style="width: 99%" readonly></td>
                                  
                                  <td>
                                    <select {{$ActionStatus}}  name="ASID_REF[]" id="ASID_REF_0" class="form-control mandatory">
                                      <option value="" selected="">Select</option>
                                    @foreach($objAttendance as $val)
                                      <option value="{{$val->ATTENDANCE_CODEID}}">{{$val->ATTENDANCE_CODE}}-{{$val->ATTENDANCE_CODE_DESC}}</option>
                                    @endforeach
                                    </select>
                                  </td>
                                  <td>
                                    <input {{$ActionStatus}}  class="form-control" type="text" name="REMARKS[]"     id ="REMARKS_0"     autocomplete="off" style="width: 99%">
                                  </td>
                                  <td>
                                    <button {{$ActionStatus}}  class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                                    <button {{$ActionStatus}}  class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                                  </td>
                                </tr>
                              @php $n++; 
                              @endphp
                              @endif
                              </tbody>
                            </table>
                        </div>	
                    </div>
                </div>
              </div>
       </div>
     </form>
 </div><!--purchase-order-view-->
 
@endsection
@section('alert')
<!-- Alert -->
<div id="alert" class="modal"  role="dialog"  data-backdr op="static">
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
            <button onclick="setfocus();"  class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">
            <div id="alert-active" class="activeOk"></div>OK</button>
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;">
              <div id="alert-active" class="activeOk1"></div>OK</button>
            
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>


<div id="proidpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='gl_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p id='title_name'></p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ITEMProCodeTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%">Code</th>
        <th  class="ROW3"style="width: 40%">Name</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td class="ROW2"  style="width: 40%">
        <input type="text" autocomplete="off"  class="form-control" id="empcodesearch" onkeyup="EmpCodeFunction()" />
      </td>
      <td class="ROW3"  style="width: 40%">
        <input type="text" autocomplete="off"  class="form-control" id="empnamesearch" onkeyup="EmpNameFunction()" />
      </td>
    </tr>
    </tbody>
    </table>
      <table id="ITEMProCodeTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_prod_code">       
        </tbody>
      </table>
    </div>
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

//------------------------
   let sgltid = "#ITEMProCodeTable2";
   let sgltid2 = "#ITEMProCodeTable";
   let sglheaders = document.querySelectorAll(sgltid2 + " th");

   // Sort the table element when clicking on the table headers
   sglheaders.forEach(function(element, i) {
     element.addEventListener("click", function() {
       w3.sortHTML(sgltid, ".clsmachine", "td:nth-child(" + (i + 1) + ")");
     });
   });

   function EmpCodeFunction() {
     var input, filter, table, tr, td, i, txtValue;
     input = document.getElementById("empcodesearch");
     filter = input.value.toUpperCase();
     table = document.getElementById("ITEMProCodeTable2");
     tr = table.getElementsByTagName("tr");
     for (i = 0; i < tr.length; i++) {
       td = tr[i].getElementsByTagName("td")[1];
       if (td) {
         txtValue = td.textContent || td.innerText;
         if (txtValue.toUpperCase().indexOf(filter) > -1) {
           tr[i].style.display = "";
         } else {
           tr[i].style.display = "none";
         }
       }       
     }
   }

function EmpNameFunction() {
     var input, filter, table, tr, td, i, txtValue;
     input = document.getElementById("empnamesearch");
     filter = input.value.toUpperCase();
     table = document.getElementById("ITEMProCodeTable2");
     tr = table.getElementsByTagName("tr");
     for (i = 0; i < tr.length; i++) {
       td = tr[i].getElementsByTagName("td")[2];
       if (td) {
         txtValue = td.textContent || td.innerText;
         if (txtValue.toUpperCase().indexOf(filter) > -1) {
           tr[i].style.display = "";
         } else {
           tr[i].style.display = "none";
         }
       }       
 }
}

function setfocus(){
 var focusid=$("#focusid").val();
 $("#"+focusid).focus();
 $("#closePopup").click();
} 

function validateForm(saveAction){
 $("#focusid").val('');

 var DOC_NO                =   $.trim($("#DOC_NO").val());
    var DOC_DT                =   $.trim($("#DOC_DT").val());
    var PAYPERIODID_REF       =   $.trim($("#PAYPERIODID_REF").val());
    var SHIFTID_REF           =   $.trim($("#SHIFTID_REF").val());
    //var REMAMOUNT             =   $.trim($("[id*=REMAMOUNT]").val());$.trim($("#DOC_NO").val());
    
    $("#OkBtn1").hide();
    if(DOC_NO ===""){
      $("#focusid").val('DOC_NO');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter Document No.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    else if(DOC_DT ===""){
      $("#focusid").val('DOC_DT');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please select Document Date.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    else if(PAYPERIODID_REF ===""){
      $("#focusid").val('PAYPERIODID_REF');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please select Pay Period.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    else if(SHIFTID_REF ===""){
      $("#focusid").val('SHIFTID_REF');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please select Shift.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    
    else{
        event.preventDefault();
          var allblank1 = [];
          var focustext1= "";
          var textmsg = "";

          $('#example2').find('.participantRow').each(function(){
          
              if($.trim($(this).find("[id*=EMPID_REF]").val()) ==""){
                allblank1.push('false');
                focustext1 = $(this).find("[id*=EMPID_REF]").attr('id');
                textmsg = 'Please select Employee';
              }
              else if($.trim($(this).find("[id*=FROMTIME]").val()) ==""){
                allblank1.push('false');
                focustext1 = $(this).find("[id*=FROMTIME]").attr('id');
                textmsg = 'Please enter From Time	';
              }
              else if($.trim($(this).find("[id*=TOTIME]").val()) ==""){
                allblank1.push('false');
                focustext1 = $(this).find("[id*=TOTIME]").attr('id');
                textmsg = 'Please enter To Time';
              }
              else if($.trim($(this).find("[id*=ASID_REF]").val()) ==""){
                allblank1.push('false');
                focustext1 = $(this).find("[id*=ASID_REF]").attr('id');
                textmsg = 'Please select Attendance Status';
              }
          });

        if(jQuery.inArray("false", allblank1) !== -1){
            $("#focusid").val(focustext1);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").hide();  
            $("#OkBtn").show();
            $("#AlertMessage").text(textmsg);
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          } 
          else{
              $("#alert").modal('show');
              $("#AlertMessage").text('Do you want to save to record.');
              $("#YesBtn").data("funcname",saveAction); 
              $("#YesBtn").focus();
              highlighFocusBtn('activeYes');
          }

    }

}


function showAlert(msg,smgid){
  $("#FocusId").val(smgid);
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}


$('#btnAdd').on('click', function() {
   var viewURL = '{{route("transaction",[$FormId,"add"])}}';
   window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
 var viewURL = '{{route('home')}}';
 window.location.href=viewURL;
});

var formResponseMst = $( "#frm_mst_edit" );
  formResponseMst.validate();

 $("#DESCRIPTIONS").blur(function(){
     $(this).val($.trim( $(this).val() ));
     $("#ERROR_DESCRIPTIONS").hide();
     validateSingleElemnet("DESCRIPTIONS");
 });

 $( "#DESCRIPTIONS" ).rules( "add", {
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
   var validator =$("#frm_mst_edit" ).validate();
      if(validator.element( "#"+element_id+"" )){
         //check duplicate code
       if(element_id=="ATTCODE" || element_id=="attcode" ) {
         checkDuplicateCode();
       }

      }

 }

 // //check duplicate exist code
 function checkDuplicateCode(){
     
     //validate and save data
     var getDataForm = $("#frm_mst_edit");
     var formData = getDataForm.serialize();
     $.ajaxSetup({
         headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
     });
     $.ajax({
         url:'{{route("transaction",[$FormId,"codeduplicate"])}}',
         type:'POST',
         data:formData,
         success:function(data) {
             if(data.exists) {
                 $(".text-danger").hide();
                 showError('ERROR_ATTCODE',data.msg);
                 $("#ATTCODE").focus();
             }                                
         },
         error:function(data){
           console.log("Error: Something went wrong.");
         },
     });
 }

 $( "#btnSave" ).click(function() {
  var formSalesOrder = $("#frm_mst_edit");
  if(formSalesOrder.valid()){
    validateForm("fnSaveData");
  }
});
$( "#btnApprove" ).click(function() {
  var formSalesOrder = $("#frm_mst_edit");
  if(formSalesOrder.valid()){
    validateForm("fnApproveData");
  }
});

 
        
window.fnApproveData = function (){

event.preventDefault();
var trnsoForm = $("#frm_mst_edit");
var formData = trnsoForm.serialize();

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("transactionmodify",[$FormId,"Approve"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.PAYPERIODID_REF){
                showError('ERROR_PAYPERIODID_REF',data.errors.PAYPERIODID_REF);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in VQ NO.');
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
            $("#AlertMessage").text(data.msg);
            $(".text-danger").hide();
            $("#alert").modal('show');
            $("#OkBtn").focus();
            window.location.href='{{ route("transaction",[$FormId,"index"])}}';
        }
        else if(data.cancel) {                   
            console.log("cancel MSG="+data.msg);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text(data.msg);
            $(".text-danger").hide();
            $("#alert").modal('show');
            $("#OkBtn1").focus();
        }
        else 
        {                   
            console.log("succes MSG="+data.msg);
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
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
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
        url:'{{ route("transactionmodify",[$FormId,"update"]) }}',
         type:'POST',
         data:formData,
         success:function(data) {
            
             if(data.errors) {
                 $(".text-danger").hide();                    
                 if(data.errors.DESCRIPTIONS){
                    // showError('ERROR_DESCRIPTIONS',data.errors.DESCRIPTIONS);
                     $("#YesBtn").hide();
                     $("#NoBtn").hide();
                     $("#OkBtn1").hide();
                     $("#OkBtn").show();
                     $("#AlertMessage").text("Attribute Description is "+data.errors.DESCRIPTIONS);
                     $("#alert").modal('show');
                     $("#OkBtn").focus();
                 }
                if(data.exist=='duplicate') {

                   $("#YesBtn").hide();
                   $("#NoBtn").hide();
                   $("#OkBtn1").hide();
                   $("#OkBtn").show();

                   $("#AlertMessage").text(data.msg);

                   $("#alert").modal('show');
                   $("#OkBtn").focus();

                }
                if(data.save=='invalid') {

                   $("#YesBtn").hide();
                   $("#NoBtn").hide();
                   $("#OkBtn1").hide();
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
                 $("#OkBtn").hide();

                 $("#AlertMessage").text(data.msg);

                 $(".text-danger").hide();
                 $("#frm_mst_edit").trigger("reset");

                 $("#alert").modal('show');
                 $("#OkBtn1").focus();

               //  window.location.href='{{ route("transaction",[$FormId,"index"])}}';
             }
             
         },
         error:function(data){
           console.log("Error: Something went wrong.");
         },
     });
   
} // fnSaveData


//delete row
$("#Material").on('click', '.remove', function() {
 var rowCount = $(this).closest('table').find('.participantRow').length;
 if (rowCount > 1) {
 $(this).closest('.participantRow').remove();     
 } 
 if (rowCount <= 1) { 
       $("#YesBtn").hide();
       $("#NoBtn").hide();
       $("#OkBtn").hide();
       $("#OkBtn1").show();
       $("#AlertMessage").text('There is only 1 row. So cannot be remove.');
       $("#alert").modal('show');
       $("#OkBtn1").focus();
       highlighFocusBtn('activeOk1');
       return false;
 }
 event.preventDefault();
});


//add row
$("#Material").on('click', '.add', function() {
var $tr = $(this).closest('table');
var allTrs = $tr.find('.participantRow').last();
var lastTr = allTrs[allTrs.length-1];
var $clone = $(lastTr).clone();

$clone.find('td').each(function(){
 var el = $(this).find(':first-child');
 var id = el.attr('id') || null;
   if(id){
       var idLength = id.split('_').pop();
       var i = id.substr(id.length-idLength.length);
       var prefix = id.substr(0, (id.length-idLength.length));
       el.attr('id', prefix+(+i+1));
   }
});

$clone.find('input:text').val('');
$clone.find('input:hidden').val('');
$clone.find('select').val('');
$tr.closest('table').append($clone);         
var rowCount1 = $('#Row_Count').val();
rowCount1 = parseInt(rowCount1)+1;
$('#Row_Count').val(rowCount1);
$clone.find('.remove').removeAttr('disabled'); 
event.preventDefault();
});

 
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

     $(".text-danger").hide(); 
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
     $("#NoBtn").focus();
     highlighFocusBtn('activeNo');
     
 }); ////Undo button

 
 $("#OkBtn1").click(function(){

 $("#alert").modal('hide');
 $("#YesBtn").show();  //reset
 $("#NoBtn").show();   //reset
 $("#OkBtn").hide();
 $("#OkBtn1").hide();
 $(".text-danger").hide();
 window.location.href = "{{route('transaction',[$FormId,'index'])}}";

 });


 
 $("#OkBtn").click(function(){
   $("#alert").modal('hide');

 });////ok button


window.fnUndoYes = function (){
   
   //reload form
   window.location.href = "{{route('transaction',[$FormId,'add'])}}";

}//fnUndoYes

 function showError(pId,pVal){

   $("#"+pId+"").text(pVal);
   $("#"+pId+"").show();

 }//showError

 function highlighFocusBtn(pclass){
    $(".activeYes").hide();
    $(".activeNo").hide();
    
    $("."+pclass+"").show();
 }  

function getPayPrName(PAYPERIODID){
 $("#PAY_PERIOD_DESC").val('');
 
 $.ajaxSetup({
         headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
     });
 
     $.ajax({
         url:'{{route("transaction",[$FormId,"getPayPrName"])}}',
         type:'POST',
         data:{PAYPERIODID:PAYPERIODID},
         success:function(data) {
            $("#PAY_PERIOD_DESC").val(data);                
         },
         error:function(data){
           console.log("Error: Something went wrong.");
         },
     });	
}

function getEarHeadName(id,EARNINGVALUE){

 var ROW_ID = id.split('_').pop();

 $.ajaxSetup({
         headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
     });

     $.ajax({
         url:'{{route("transaction",[$FormId,"getEarHeadName"])}}',
         type:'POST',
         data:{EARNINGVALUE:EARNINGVALUE},
         success:function(data) {
           $('#EARNIGHEAD_REF_'+ROW_ID+'').html(data);
         },
         error:function(data){
           console.log("Error: Something went wrong.");
         },
     });	
 }

 function getDedHeadName(id,DEDUCTION_HEADID){

 var ROW_ID = id.split('_').pop();

 $.ajaxSetup({
         headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
     });

     $.ajax({
         url:'{{route("transaction",[$FormId,"getDedHeadName"])}}',
         type:'POST',
         data:{DEDUCTION_HEADID:DEDUCTION_HEADID},
         success:function(data) {
           $('#DEDHEAD_DES_'+ROW_ID+'').val(data);                
         },
         error:function(data){
           console.log("Error: Something went wrong.");
         },
     });	
 }

$(document).ready(function(e) {
var d = new Date(); 
var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
$('#REMB_DT').val(today);

});



//Shift CODE popoup click
$('#TXTSHIFT').click(function(event){

if ($('#Tax_State').length) 
{
  var taxstate = $('#Tax_State').val();
}
else
{
  var taxstate = '';
}

var CODE = ''; 
var FORMID = "{{$FormId}}";
loadItem_prod_code(taxstate,CODE,FORMID); 
$("#title_name").text('Shift Details');
 $("#proidpopup").show();
event.preventDefault();
});

$("#gl_closePopup").click(function(event){
    $("#ItemProCodeSearch").val('');
    $("#proidpopup").hide();
    event.preventDefault();
  });

  function loadItem_prod_code(taxstate,CODE,FORMID){

    $("#tbody_prod_code").html('loading...');
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $.ajax({
      url:'{{route("transaction",[$FormId,"getShiftDetails"])}}',
      type:'POST',
      data:{'taxstate':taxstate,'CODE':CODE},
      success:function(data) {
      $("#tbody_prod_code").html(data); 
      bindProdCode();

      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_prod_code").html('');                        
      },
    });

}

function bindProdCode(){

$('[id*="chkIdProdCode"]').change(function(){

  //var fieldid = $(this).attr('id');
  var fieldid     = $(this).parent().parent().attr('id');
  var txtval      = $("#txt"+fieldid+"").val();  
  var shiftCode   = $("#txt"+fieldid+"").data("code");
  var fieldid5    = $(this).parent().parent().children('[id*="stimeid"]').attr('id');
  var fieldid6    = $(this).parent().parent().children('[id*="endtimeid"]').attr('id');
  var txtstime    = $("#txt"+fieldid5+"").data("stime");
  var txtendtime  = $("#txt"+fieldid6+"").data("endtime");

  $('#TXTSHIFT').val(shiftCode);
  $('#SHIFTID_REF').val(txtval);
  $("#proidpopup").hide();
  event.preventDefault();
  $("#DEFROMTIME").val(txtstime);
  $("#DEFTOTIME").val(txtendtime);

  });
}

function getEmployee(id){

  var rowid = id.split('_').pop();

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getEmpDetails"])}}',
    type:'POST',
    data:{rowid:rowid},
    success:function(data) {
      $("#tbody_prod_code").html(data); 
      $("#title_name").text('Employee Details');
      $("#proidpopup").show();
    },
    error:function(data){
      console.log("Error: Something went wrong.");
      $("#tbody_prod_code").html('');                        
    },
  });
}

function selectEmployee(rowid,key,id){

  var code  =  $("#empinfo"+key+"").data("desc101");
  var name  =  $("#empinfo"+key+"").data("desc102");

  $("#EMPID_REF_"+rowid).val(id);
  $("#txtEMPID_"+rowid).val(code);
  $("#FNAME_"+rowid).val(name);
  $("#proidpopup").hide();
}



function getFromTime(id){

  var rowid         =   id.split('_').pop();
  var start_time1    =   $("#FROMTIME_"+rowid).val();
  var end_time1      =   $("#TOTIME_"+rowid).val();
  
  

  var start_time    =   start_time1.split(':');
  var end_time      =   end_time1.split(':');
  var total_hours   =   parseInt(start_time[0], 10),

    hours2 = parseInt(end_time[0], 10),
    mins1 = parseInt(start_time[1], 10),
    mins2 = parseInt(end_time[1], 10);
    var hours = hours2 - total_hours, mins = 0;

    if(hours < 0) hours = 24 + hours;
    if(mins2 >= mins1) {
        mins = mins2 - mins1;
    }
    else {
      mins = (mins2 + 60) - mins1;
      hours--;
    }
    if(mins < 9)
    {
      mins = '0'+mins;
    }
    if(hours < 9)
    {
      hours = '0'+hours;
    }
    if(hours+':'+mins == 'NaN:NaN'){
      $("#TOTALHOURS_"+rowid).val(00+':'+00);
    }else{
    $("#TOTALHOURS_"+rowid).val(hours+':'+mins);
    }

}

function onlyNumberKey(evt) {
    var ASCIICode = (evt.which) ? evt.which : evt.keyCode
    if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
        return false;
    return true;
}
</script>

@endpush