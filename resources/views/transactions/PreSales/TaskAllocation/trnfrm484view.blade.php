
@extends('layouts.app')
@section('content')
<!-- <form id="frm_mst_se" onsubmit="return validateForm()"  method="POST"  >     -->
 
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Task Allocation</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo" disabled="disabled" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled" {{($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                        
                </div>
            </div><!--row-->
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->

    <div class="container-fluid purchase-order-view filter">     
      <form id="frm_mst_edit" method="POST"> 
        @CSRF
        {{isset($objResponse->LEAD_ID) ? method_field('PUT') : '' }}

        <div class="inner-form">
          <div class="row">
            <div class="col-lg-1 pl"><p>Lead No</p></div>
            <div class="col-lg-3 pl">{{isset($objResponse->LEAD_NO) && $objResponse->LEAD_NO !=''?$objResponse->LEAD_NO:''}}
          </div>
            
            <div class="col-lg-1 pl"><p>Lead Date</p></div>
              <div class="col-lg-3 pl">{{isset($objResponse->LEAD_DT) && $objResponse->LEAD_DT !=''?date('d-m-Y',strtotime($objResponse->LEAD_DT)):''}}
            </div> 
              
            <div class="col-lg-1 pl"><p>Customer</p></div>
            <div class="col-lg-1 pl">
              <input type="radio" name="CUSTOMER" id="CUSTOMER" {{isset($objResponse->CUSTOMER_TYPE) && $objResponse->CUSTOMER_TYPE =='Customer'?'checked':''}}disabled>
            </div>
    
            <div class="col-lg-1 pl"><p>Prospect</p></div>
            <div class="col-lg-1 pl">
              <input type="radio" name="CUSTOMER" id="PROSPECT" {{isset($objResponse->CUSTOMER_TYPE) && $objResponse->CUSTOMER_TYPE =='Prospect'?'checked':''}} disabled>
            </div>
          </div>
      
          <div class="row">
            <div class="col-lg-1 pl"><p id="CUSTOMER_TITLE">{{isset($objResponse->CUSTOMER_TYPE)?$objResponse->CUSTOMER_TYPE :''}}</p></div>
            <div class="col-lg-3 pl">{{isset($objResponse->CCODE) && $objResponse->CCODE !=''?$objResponse->CCODE:''}} {{isset($objResponse->CUSTNAME) && $objResponse->CUSTNAME !=''?'- '.$objResponse->CUSTNAME:''}} {{isset($objResponse->PCODE) && $objResponse->PCODE !=''?$objResponse->PCODE:''}} {{isset($objResponse->PROSNAME) && $objResponse->PROSNAME !=''?'- '.$objResponse->PROSNAME:''}}
            </div>
    
              <div class="col-lg-1 pl"><p>Company</p></div>
              <div class="col-lg-3 pl">{{isset($objResponse->COMPANY_NAME) && $objResponse->COMPANY_NAME !=''?$objResponse->COMPANY_NAME:''}}
              </div>
             
              <div class="col-lg-1 pl"><p>First Name</p></div>
              <div class="col-lg-3 pl">{{isset($objResponse->FIRST_NAME) && $objResponse->FIRST_NAME !=''?$objResponse->FIRST_NAME:''}}
              </div>
            </div>
            
            <div class="row">
            <div class="col-lg-1 pl"><p>Last Name</p></div>
            <div class="col-lg-3 pl">{{isset($objResponse->LAST_NAME) && $objResponse->LAST_NAME !=''?$objResponse->LAST_NAME:''}}
            </div>
          </div>
        <div class="row">
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#TaskAllocation" id="MAT_TAB" >Task Allocation</a></li>
          </ul>

          <div class="tab-content">
            <div id="TaskAllocation" class="tab-pane fade in active" style="margin-left: 16px; margin-top: 10px; width: 97%;">
              <div class="row">
                <div class="col-lg-2 pl"><p>Task Type*</p></div>
                <div class="col-lg-2 pl">
                  <input {{$ActionStatus}} type="text" name="TASK_TYPE" id="TASK_TYPE" value="{{isset($TASKALL->TASK_TYPECODE) && $TASKALL->TASK_TYPECODE !=''?$TASKALL->TASK_TYPECODE:''}} {{isset($TASKALL->TASK_TYPENAME) && $TASKALL->TASK_TYPENAME !=''?'- '.$TASKALL->TASK_TYPENAME:''}}" onclick="getData('{{route('transaction',[$FormId,'getTaskType'])}}','Task Type','TASK_REF')" class="form-control mandatory" autocomplete="off" readonly>
                  <input type="hidden" name="TASK_REF" id="TASK_REF" value="{{isset($TASKALL->TASKID_REF) && $TASKALL->TASKID_REF !=''?$TASKALL->TASKID_REF:''}}" class="form-control mandatory" autocomplete="off" readonly>
                </div>
        
                <div class="col-lg-2 pl"><p>Assigned To*</p></div>
                  <div class="col-lg-2 pl">
                    <input {{$ActionStatus}} type="text" name="ASSIGN_TO" id="ASSIGN_TO" value="{{isset($TASKALL->EMPCODE) && $TASKALL->EMPCODE !=''?$TASKALL->EMPCODE:''}} {{isset($TASKALL->FNAME) && $TASKALL->FNAME !=''?'- '.$TASKALL->FNAME:''}}" onclick="getData('{{route('transaction',[$FormId,'getAssignedTo'])}}','Assigned To','ASSIGN_TO_REF_ID')" class="form-control mandatory" autocomplete="off" readonly>
                    <input type="hidden" name="ASSIGNTO_REFID" id="ASSIGN_TO_REF_ID" value="{{isset($TASKALL->TASK_ASSIGNTO_REF) && $TASKALL->TASK_ASSIGNTO_REF !=''?$TASKALL->TASK_ASSIGNTO_REF:''}}" class="form-control mandatory" autocomplete="off" readonly>                           
                  </div>
          
                  <div class="col-lg-2 pl"><p>Priority*</p></div>
                  <div class="col-lg-2 pl">
                    <input {{$ActionStatus}} type="text" name="PRIORITY_NAME" id="PRIORITY_NAME" value="{{isset($TASKALL->PRIORITYCODE) && $TASKALL->PRIORITYCODE !=''?$TASKALL->PRIORITYCODE:''}} {{isset($TASKALL->DESCRIPTIONS) && $TASKALL->DESCRIPTIONS !=''?'- '.$TASKALL->DESCRIPTIONS:''}}" onclick="getData('{{route('transaction',[$FormId,'getPriority'])}}','Priority','PRIORITY_REF')" class="form-control mandatory" autocomplete="off" readonly>
                    <input type="hidden" name="PRIORITY_REF_ID" id="PRIORITY_REF" value="{{isset($TASKALL->PRIORITYID_REF) && $TASKALL->PRIORITYID_REF !=''?$TASKALL->PRIORITYID_REF:''}}" class="form-control mandatory" autocomplete="off" readonly>                           
                  </div>
                </div>
      
                <div class="row">
                  <div class="col-lg-2 pl"><p>Due Date*</p></div>
                  <div class="col-lg-2 pl">
                    <input {{$ActionStatus}} type="date" name="DUE_DATE_NAME" id="DUE_DATE_ID" value="{{isset($TASKALL->DUE_DATE) && $TASKALL->DUE_DATE !=''?$TASKALL->DUE_DATE:''}}" class="form-control mandatory" autocomplete="off">  
                  </div>
          
                  <div class="col-lg-2 pl"><p>Status*</p></div>
                    <div class="col-lg-2 pl">
                      <select {{$ActionStatus}} name="STATUS_NAME" id="STATUS" class="form-control">
                        <option {{isset($TASKALL->TASK_STATUS) && $TASKALL->TASK_STATUS == 'Meeting'?'selected="selected"':''}} value="Meeting">Meeting</option>
                        <option {{isset($TASKALL->TASK_STATUS) && $TASKALL->TASK_STATUS == 'Mail'?'selected="selected"':''}} value="Mail">Mail</option>
                        <option {{isset($TASKALL->TASK_STATUS) && $TASKALL->TASK_STATUS == 'Call'?'selected="selected"':''}}value="Call">Call</option>
                        </select>                             
                    </div>
            
                    <div class="col-lg-2 pl"><p>Reminder*</p></div>
                    <div class="col-lg-2 pl">
                      <input {{$ActionStatus}} type="date" name="REMINDER_NAME" id="REMINDER" value="{{isset($TASKALL->TASK_REMINDER_DATE) && $TASKALL->TASK_REMINDER_DATE !=''?$TASKALL->TASK_REMINDER_DATE:''}}" class="form-control mandatory" autocomplete="off"> 
                    </div>
                  </div>
      
                  <div class="row">
                    <div class="col-lg-2 pl"><p>Subject*</p></div>
                    <div class="col-lg-2 pl">
                      <textarea {{$ActionStatus}} class="form-control" name="TASK_SUBJECT_NAME" id="TASK_SUBJECT" value="{{isset($TASKALL->SUBJECT) && $TASKALL->SUBJECT !=''?$TASKALL->SUBJECT:''}}" style="width: 398px; height: 45px;">{{isset($TASKALL->SUBJECT) && $TASKALL->SUBJECT !=''?$TASKALL->SUBJECT:''}}</textarea>
                    </div>
      
                    <div class="col-lg-2 pl" style="margin-left: 206px;"><p>Task Detail*</p></div>
                    <div class="col-lg-2 pl">
                      <textarea {{$ActionStatus}} class="form-control" name="TASK_DETAILS_NAME" id="TASK_DETAILS" value="{{isset($TASKALL->TASK_DETAIL) && $TASKALL->TASK_DETAIL !=''?$TASKALL->TASK_DETAIL:''}}" style="width: 403px;height: 45px;">{{isset($TASKALL->TASK_DETAIL) && $TASKALL->TASK_DETAIL !=''?$TASKALL->TASK_DETAIL:''}}</textarea>
                    </div>
                    </div>
                    {{-- <button class="btn topnavbt" id="Add"><i class="fa fa-floppy-o"></i> Add</button> --}}
                  </div>
                </div>
              </div>
            </div>
      
            <div id="TaskAllocation" class="container-fluid purchase-order-view">	
              <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
              <table id="example3" class="display nowrap table table-striped table-bordered">
                <thead id="thead1"  style="position: sticky;top: 0">                      
                  <tr>                          
                  <th rowspan="2">Task Type</th>
                  <th rowspan="2">Assigned To</th>
                  <th rowspan="2">Priority</th>
                  <th rowspan="2">Due Date</th>
                  <th rowspan="2">Status</th>
                  <th rowspan="2">Reminder</th>
                  <th rowspan="2">Subject</th>
                  <th rowspan="2">Task Detail</th>
                  <th rowspan="2">Action</th>
                </tr>                      
                  
              </thead>
                <tbody>
                  @if(!empty($MAT))
                  @php $n=1; @endphp
                  @foreach($MAT as $key => $row)
                  <tr  class="participantRow">   
                    <td>{{isset($row->TASK_TYPECODE) && $row->TASK_TYPECODE !=''?$row->TASK_TYPECODE:''}}<input type="hidden" name="TASK_TYPE"                   id ="TASK_TYPE_{{$key}}"                value="{{isset($row->TASK_TYPECODE) && $row->TASK_TYPECODE !=''?$row->TASK_TYPECODE:''}} {{isset($TASKALL->TASK_TYPENAME) && $TASKALL->TASK_TYPENAME !=''?'- '.$TASKALL->TASK_TYPENAME:''}}" class="form-control mandatory" autocomplete="off"></td>
                    <td>{{isset($row->EMPCODE) && $row->EMPCODE !=''?$row->EMPCODE:''}}                  <input type="hidden" name="ASSIGN_TO"                   id ="ASSIGN_TO_{{$key}}"                value="{{isset($row->EMPCODE) && $row->EMPCODE !=''?$row->EMPCODE:''}} {{isset($TASKALL->FNAME) && $TASKALL->FNAME !=''?'- '.$TASKALL->FNAME:''}}" class="form-control mandatory" autocomplete="off"></td>
                    <td>{{isset($row->PRIORITYCODE) && $row->PRIORITYCODE !=''?$row->PRIORITYCODE:''}}<input type="hidden"    name="PRIORITY_NAME"               id ="PRIORITY_NAME_{{$key}}"            value="{{isset($row->PRIORITYCODE) && $row->PRIORITYCODE !=''?$row->PRIORITYCODE:''}} {{isset($TASKALL->DESCRIPTIONS) && $TASKALL->DESCRIPTIONS !=''?'- '.$TASKALL->DESCRIPTIONS:''}}" class="form-control mandatory" autocomplete="off"></td>
                    <td>{{isset($row->DUE_DATE) && $row->DUE_DATE !=''?$row->DUE_DATE:''}}               <input type="hidden" name="DUE_DATE"                    id ="DUE_DATE_{{$key}}"                 value="{{isset($row->DUE_DATE) && $row->DUE_DATE !=''?$row->DUE_DATE:''}}" class="form-control mandatory" autocomplete="off"></td>
                    <td>{{isset($row->TASK_STATUS) && $row->TASK_STATUS !=''?$row->TASK_STATUS:''}}<input type="hidden" name="STATUS"                            id ="STATUS_{{$key}}"                   value="{{isset($row->TASK_STATUS) && $row->TASK_STATUS !=''?$row->TASK_STATUS:''}}"                     class="form-control mandatory" autocomplete="off"></td>
                    <td>{{isset($row->TASK_REMINDER_DATE) && $row->TASK_REMINDER_DATE !=''?$row->TASK_REMINDER_DATE:''}}<input type="hidden" name="REMINDER"     id ="REMINDER_{{$key}}"                 value="{{isset($row->TASK_REMINDER_DATE) && $row->TASK_REMINDER_DATE !=''?$row->TASK_REMINDER_DATE:''}}"              class="form-control mandatory" autocomplete="off"></td>
                    <td>{{isset($row->SUBJECT) && $row->SUBJECT !=''?$row->SUBJECT:''}}<input type="hidden" name="TASK_SUBJECT"                                  id ="TASK_SUBJECT_ID_{{$key}}"          value="{{isset($row->SUBJECT) && $row->SUBJECT !=''?$row->SUBJECT:''}}"    class="form-control mandatory" autocomplete="off"></td>
                    <td>{{isset($row->TASK_DETAIL) && $row->TASK_DETAIL !=''?$row->TASK_DETAIL:''}}<input type="hidden" name="TASK_DETAILS"                      id ="TASK_DETAILS_{{$key}}"             value="{{isset($row->TASK_DETAIL) && $row->TASK_DETAIL !=''?$row->TASK_DETAIL:''}}"                     class="form-control mandatory" autocomplete="off"></td>
                    <td hidden><input type="hidden" name="TASK_REF"              id ="TASK_REF_{{$key}}"         value="{{isset($row->TASKID_REF) && $row->TASKID_REF !=''?$row->TASKID_REF:''}}" class="form-control mandatory" autocomplete="off"></td>
                    <td hidden><input type="hidden" name="ASSIGN_TO_REF"         id ="ASSIGN_TO_REFID_{{$key}}"  value="{{isset($row->TASK_ASSIGNTO_REF) && $row->TASK_ASSIGNTO_REF !=''?$row->TASK_ASSIGNTO_REF:''}}" class="form-control mandatory" autocomplete="off"></td>
                    <td hidden><input type="hidden" name="PRIORITY_REF"          id ="PRIORITY_REF_{{$key}}"         value="{{isset($row->PRIORITYID_REF) && $row->PRIORITYID_REF !=''?$row->PRIORITYID_REF:''}}" class="form-control mandatory" autocomplete="off"></td>
                    {{-- <td style="width: 110px;"><input type="radio" name="selectRow" onchange="getDataVal('{{$key}}')"></td> --}}
                  <td><a {{$ActionStatus}} {{$key ==0?'':'disabled'}}  class="btn add" title="Edit" id="editrow_{{$key}}" data-toggle="tooltip" style="color: rgb(43, 41, 41);"><i class="fa fa-edit" onclick="getDataVal('{{$key}}')" style="cursor: pointer;"></i></a></td>
                  </tr>
                  @php $n++; @endphp
                @endforeach 
              @endif
              </tbody>
            </table>
        </div>
        </div>

      </div>
    </div>
  </div>
</div>
</form>
</div>

  @endsection
  @section('alert')
  <!-- Alert -->
  <div id="alert" class="modal"  role="dialog"  data-backdrop="static">
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
                <input type="hidden" id="focusid" >
              
          </div><!--btdiv-->
          <div class="cl"></div>
        </div>
      </div>
    </div>
  </div>

  
<div id="modalpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='modalclosePopup' >&times;</button>
      </div>

      <div class="modal-body">

        <div class="tablename"><p id='tital_Name'></p></div>
        <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="MachTable" class="display nowrap table  table-striped table-bordered">
            <thead>
              <tr>
                <th class="ROW1">Select</th> 
                <th class="ROW2">Code</th>
                <th class="ROW3">Description</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="ROW1"><span class="check_th">&#10004;</span></td>
                <td class="ROW2"><input type="text" autocomplete="off"  class="form-control" id="codesearch"  onkeyup='colSearch("tabletab2","codesearch",1)' /></td>
                <td class="ROW3"><input type="text" autocomplete="off"  class="form-control" id="namesearch"  onkeyup='colSearch("tabletab2","namesearch",2)' /></td>
              </tr>
            </tbody>
          </table>

          <table id="tabletab2" class="display nowrap table  table-striped table-bordered" >
            <thead id="thead2"></thead>
            <tbody id="getData_tbody"></tbody>
          </table>

        </div>

        <div class="cl"></div>

      </div>
    </div>
  </div>
</div>
  
  @endsection
  @push('bottom-css')
  @endpush
  @push('bottom-scripts')
  <script>
  
  
/*************************************   All Search Start  ************************** */

    function getDataVal(id){
      $('#TASK_TYPE').val($('#TASK_TYPE_'+id).val());
      $('#ASSIGN_TO').val($('#ASSIGN_TO_'+id).val());
      $('#PRIORITY_NAME').val($('#PRIORITY_NAME_'+id).val());
      $('#DUE_DATE_ID').val($('#DUE_DATE_'+id).val());
      $('#STATUS').val($('#STATUS_'+id).val());
      $('#REMINDER').val($('#REMINDER_'+id).val());
      $('#TASK_SUBJECT').val($('#TASK_SUBJECT_ID_'+id).val());
      $('#TASK_DETAILS').val($('#TASK_DETAILS_'+id).val());
      $('#TASK_REF').val($('#TASK_REF_'+id).val());
      $('#ASSIGN_TO_REF_ID').val($('#ASSIGN_TO_REFID_'+id).val());
      $('#PRIORITY_REF').val($('#PRIORITY_REF_'+id).val());
    }

  function getData(path,msg,id){

    var listid = $("#"+id).val();

    $('#getData_tbody').html('Loading...'); 

    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url:path,
        type:'POST',
        data:{listid:listid},
        success:function(data) {
        $('#getData_tbody').html(data);
        bindTaskTypeEvents()
        bindAssignedToEvents()
        bindPriorityEvents()
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $('#getData_tbody').html('');
        },
      });

      $("#tital_Name").text(msg);
      $("#modalpopup").show();
      event.preventDefault();
    }

    $("#modalclosePopup").on("click",function(event){ 
      $("#modalpopup").hide();
      event.preventDefault();
    });

    function bindTaskTypeEvents(){
        $('.clsacttype').click(function(){
        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc");
        $("#TASK_TYPE").val(texdesc);
        $("#TASK_REF").val(txtval);
        $("#modalpopup").hide();
        });
      }

    function bindAssignedToEvents(){
        $('.clsassgnto').click(function(){
        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc");
        $("#ASSIGN_TO").val(texdesc);
        $("#ASSIGN_TO_REF_ID").val(txtval);
        $("#modalpopup").hide();
        });
      }

    function bindPriorityEvents(){
        $('.clspriort').click(function(){
        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc");
        $("#PRIORITY_NAME").val(texdesc);
        $("#PRIORITY_REF").val(txtval);
        $("#modalpopup").hide();
        });
      }

/************************************* All Search Start  ************************** */

    let input, filter, table, tr, td, i, txtValue;
      function colSearch(ptable,ptxtbox,pcolindex) {
        input = document.getElementById(ptxtbox);
        filter = input.value.toUpperCase();
        table = document.getElementById(ptable);
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[pcolindex];
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
    
/************************************* All Search End  ************************** */

      function setfocus(){
        var focusid=$("#focusid").val();
        $("#"+focusid).focus();
        $("#closePopup").click();
      }
    
    function alertMsg(id,msg){
      $("#focusid").val(id);
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text(msg);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    
    function validateForm(actionType){
        $("#focusid").val('');

        var TASK_REF         =   $.trim($("#TASK_REF").val());
        var ASSIGN_TO_REF_ID =   $.trim($("#ASSIGN_TO_REF_ID").val());
        var PRIORITY_REF     =   $.trim($("#PRIORITY_REF").val());
        var DUE_DATE_ID      =   $.trim($("#DUE_DATE_ID").val());
        var STATUS           =   $.trim($("#STATUS").val());
        var REMINDER         =   $.trim($("#REMINDER").val());
        var TASK_SUBJECT     =   $.trim($("#TASK_SUBJECT").val());
        var TASK_DETAILS     =   $.trim($("#TASK_DETAILS").val());
        
        $("#OkBtn1").hide();

        if(TASK_REF ===""){
          alertMsg('TASK_TYPE','Please Select Task Type.');
        }
        else if(ASSIGN_TO_REF_ID ===""){
          alertMsg('ASSIGN_TO','Please Select Assigned To.');
        }
        else if(PRIORITY_REF ===""){
          alertMsg('PRIORITY_NAME','Please Select Priority.');
        }
        else if(DUE_DATE_ID ===""){
          alertMsg('DUE_DATE_ID','Please Select Due Date.');
        }
        else if(STATUS ===""){
          alertMsg('STATUS','Please Select Status.');
        }
        else if(REMINDER ===""){
          alertMsg('REMINDER','Please Select Reminder.');
        }
        else if(TASK_SUBJECT ===""){
          alertMsg('TASK_SUBJECT','Please enter Subject.');
        }
        else if(TASK_DETAILS ===""){
          alertMsg('TASK_DETAILS','Please enter Task Detail.');
        }
          else{
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname",actionType);  
            $("#YesBtn").focus();
            highlighFocusBtn('activeYes');
        }
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
    
        function validateSingleElemnet(element_id){
          var validator =$("#frm_mst_edit" ).validate();
             if(validator.element( "#"+element_id+"" )){
             }
          }
    
        function checkDuplicateCode(){
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
    
          function submitData(type){
              if(formResponseMst.valid()){
                validateForm("fnSaveData");
              }
          }

          function submitDataAp(type){
              if(formResponseMst.valid()){
                validateForm("fnApproveData");
              }
          }

          window.fnSaveData = function (){
              submitForm('update');
          };

          window.fnApproveData = function (){
              submitForm('approve');
          }

                        
        function submitForm(requestType){
            var getDataForm = $("#frm_mst_edit");
            var formData = getDataForm.serialize() + "&requestType=" + requestType ;
            //var formData = getDataForm.append(requestType);

            $.ajaxSetup({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transactionmodify",[$FormId,"update"])}}',
                type:'POST',
                data:formData,
                success:function(data) {
                  if(data.success) {                   
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").show();
                  $("#AlertMessage").text(data.msg);
                  $(".text-danger").hide();
                  $("#alert").modal('show');
                  $("#OkBtn").focus();
                  }     
                  if(data.errors) {
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text(data.msg);
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                  }
                },
                error:function(data){
                console.log("Error: Something went wrong.");
                },
              });
          }

        $("#YesBtn").click(function(){
            $("#alert").modal('hide');
            var customFnName = $("#YesBtn").data("funcname");
            window[customFnName]();
        });

        $("#NoBtn").click(function(){
          $("#alert").modal('hide');
          var custFnName = $("#NoBtn").data("funcname");
          window[custFnName]();
        });

        $("#NoBtn").click(function(){
            $("#alert").modal('hide');
            var custFnName = $("#NoBtn").data("funcname");
            window[custFnName]();
        });
  
        $("#OkBtn").click(function(){
            $("#alert").modal('hide');
            $("#YesBtn").show();
            $("#NoBtn").show();
            $("#OkBtn").hide();
            $("#OkBtn1").hide();
            $(".text-danger").hide();
            location.reload(); 
            //window.location.href = "{{route('transaction',[$FormId,'index'])}}";
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
        });
  
        $("#OkBtn1").click(function(){
        $("#alert").modal('hide');
        $("#YesBtn").show();
        $("#NoBtn").show();
        $("#OkBtn").hide();
        $("#OkBtn1").hide();
        $(".text-danger").hide();
        window.location.href = "{{route('transaction',[$FormId,'index'])}}";
        });

        $("#OkBtn").click(function(){
          $("#alert").modal('hide');
          //window.location.href = "{{route('transaction',[$FormId,'index'])}}";
        });

      window.fnUndoYes = function (){
        window.location.href = "{{route('transaction',[$FormId,'add'])}}";
      }

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
    @endpush
  