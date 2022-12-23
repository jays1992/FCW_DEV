@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Leave Approval</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled" ><i class="fa fa-plus"></i> Add</button>
                <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                <button class="btn topnavbt" id="btnSave"  tabindex="3"  ><i class="fa fa-save"></i> Save</button>
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
         <form id="frm_mst_add" method="POST" onsubmit="return validateForm()" class="needs-validation"  > 
         
          @CSRF
          <div class="inner-form">
              
            {{-- @php
                dd($objLeaveList);
            @endphp --}}
                <div class="row">
                  <div class="col-lg-2 pl"><p>Employee Code*</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="EMPCODE" id="EMPCODE" class="form-control mandatory"  autocomplete="off" readonly/>
                      <input type="hidden" name="EMPID_REF" id="EMPID_REF" class="form-control" autocomplete="off" />
                      {{-- <select name="EMPID_REF" id="EMPID_REF" class="form-control mandatory" onchange="getEmpCode(this.value)" tabindex="4">
                        <option value="" selected="">Select</option>
                        @foreach($objEmpList as $val)
                        <option value="{{$val->EMPID}}">{{$val->EMPCODE}}</option>
                        @endforeach
                      </select> --}}
                      <span class="text-danger" id="ERROR_PAYPERIODID_REF"></span>                             
                    </div>

                    <div class="col-lg-2 pl"><p>Name</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" id="FNAME" class="form-control" readonly  maxlength="100" > 
                    </div>
                    <div class="col-lg-2 pl"><p>Leave Application No *</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="LEAVE_APPID_REF" id="txtitem_popup"  class="form-control mandatory"  autocomplete="off" readonly/>
                      <input type="hidden" name="LEAVE_APPIDCODE" id="LEAVE_APPIDCODE" class="form-control" autocomplete="off" />
                    <input type="hidden" id="focusid" >
                    <span class="text-danger" id="ERROR_EMPID_REF"></span>                             
                  </div>
                  </div>   
                
                  <div class="row">
                  <div class="col-lg-2 pl"><p>Leave Application Date</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="LEAVE_APP_DT" id="LEAVE_APP_DT" class="form-control" readonly  maxlength="100" > 
                    </div>

                    <div class="col-lg-2 pl"><p>Pay Period Code</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="PAY_PERIOD_CODE" id="PAY_PERIOD_CODE" class="form-control" readonly  maxlength="100" > 
                    </div>

                    <div class="col-lg-2 pl"><p>Description</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="PAY_PERIOD_DESC" id="PAY_PERIOD_DESC" class="form-control" readonly  maxlength="100" > 
                    </div>                    
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Leave Applied from Date</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="LEAVE_APP_FRDT" id="LEAVE_APP_FRDT" class="form-control" readonly  maxlength="100" > 
                    </div>

                    <div class="col-lg-2 pl"><p>Leave Applied To Date</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="LEAVE_APP_TODT" id="LEAVE_APP_TODT" class="form-control" readonly  maxlength="100" > 
                    </div>

                    <div class="col-lg-2 pl"><p>Total Days</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="totalday" id="totalday" class="form-control" readonly  maxlength="100" > 
                    </div>                    
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Reason of Leave</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="REASON_LEAVE" id="REASON_LEAVE" class="form-control" readonly  maxlength="100" > 
                    </div>

                    <div class="col-lg-2 pl"><p>Address During the leave </p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="ADDRESS_LEAVE" id="ADDRESS_LEAVE" class="form-control" readonly  maxlength="100" > 
                    </div>

                    <div class="col-lg-2 pl"><p>Contact no During the leave </p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="MONO_INLEAVE1" id="MONO_INLEAVE1" class="form-control" readonly  maxlength="100" > 
                    </div>                    
                </div>
                <br>

                <div class="row">
                <div class="col-lg-2 pl"><p>Approved By </p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="APPROVAL1_BY" id="APPROVAL1_BY" class="form-control"  maxlength="100" > 
                    </div>

                    <div class="col-lg-2 pl"><p>Date of Approval </p></div>
                    <div class="col-lg-2 pl">
                      <input type="date" name="APPROVAL1_DT" id="APPROVAL1_DT" class="form-control"  maxlength="100" > 
                    </div>

                    <div class="col-lg-2 pl"><p>Approved</p></div>
                    <div class="col-lg-2 pl">
                      <input type="radio" name="APPROVAL1_ST" value="A" id="APPROVAL1_ST" class="form-checkbox">
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Rejected</p></div>
                    <div class="col-lg-2 pl">
                    <input type="radio" name="APPROVAL1_ST" value="R" id="APPROVAL1_ST" class="form-checkbox">
                  </div>

                  <div class="col-lg-2 pl"><p>Hold</p></div>
                    <div class="col-lg-2 pl">
                    <input type="radio" name="APPROVAL1_ST" value="H" id="APPROVAL1_ST" class="form-checkbox">
                  </div>

                  <div class="col-lg-2 pl"><p>Partial Approved</p></div>
                    <div class="col-lg-2 pl">
                    <input type="radio" name="APPROVAL1_ST" value="P" id="APPROVAL1_ST" class="form-checkbox">
                  </div>
                </div>

                <div class="row">
                <div class="col-lg-2 pl"><p>Approved Days (Partial)</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="APPROVAL1_PARTIALDAYS" id="APPROVAL1_PARTIALDAYS" onkeypress="return onlyNumberKey(event)" class="form-control"  maxlength="100" > 
                  </div>

                  <div class="col-lg-2 pl"><p>Rejected / Partial Reason</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="APPROVAL1_REASON" id="APPROVAL1_REASON" class="form-control"  maxlength="100" style="width: 430px;"> 
                  </div>
                </div>

              <div class="row">
                <div id="Material" class="tab-pane fade in active">
                  <div class="row">
                    <div class="col-lg-4" style="padding-left: 15px;"></div></div>
                      <div class="table-responsive table-wrapper-scroll-y" style="height:500px;margin-top:10px;" >
                        <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                        <thead id="thead1"  style="position: sticky;top: 0">                      
                          <tr>                          
                          <th rowspan="2"  width="3%">Type of Leave <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count" value="1"></th>                         
                          <th rowspan="2" width="3%">From Date</th>
                          <th rowspan="2" width="3%">To Date</th>
                          <th rowspan="2" width="3%">No of Days</th>
                          <th rowspan="2" width="3%">Remarks</th>
                          <th rowspan="2" width="3%">Leave Balance</th>
                        </tr>                      
                      </thead>
                      <tbody id="LVAMaterialBdy">
                          <tr  class="participantRow">
                              <td>
                                <select name="LTID_REF_0" id="LTID_REF_0" class="form-control mandatory" tabindex="4">
                                  <option value="" selected="">Select</option>
                                  @foreach($objLvtypList as $val)
                                  <option value="{{$val->LTID}}">{{$val->LEAVETYPE_CODE}}- {{$val->LEAVETYPE_DESC}}</option>
                                  @endforeach
                                </select>                               
                              <td>                              
                                <input  class="form-control" type="date" name="LEAVE_APP_FRDT_0" id ="LEAVE_APP_FRDT_0" onchange="leaveFromDate(this.id)" autocomplete="off" style="width: 99%"></td>
                              </td>
                              <td><input  class="form-control" type="date" name="LEAVE_APP_TODT_0" id ="LEAVE_APP_TODT_0" onchange="leaveFromDate(this.id)"  autocomplete="off" style="width: 99%"></td></td>
                              <td><input type="text" name="TOTAL_DAYS_0" id="TOTAL_DAYS_0" class="form-control" readonly  maxlength="100" > </td>
                              <td><input  class="form-control" type="text" name="REMARKS_0" id ="REMARKS_0"  autocomplete="off" style="width: 99%"></td>
                              <td><input  class="form-control" type="text" name="LEAVE_BLANCE_0" id ="LEAVE_BLANCE_0" readonly autocomplete="off" style="width: 99%"></td>
                              
                          </tr>
                        </tbody>
                      </table>
                    </div>
                </div>

              </div>
          </div>
        </form>
    </div><!--purchase-order-view-->
    
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
	  <div class="tablename"><p>Leave Application Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ITEMProCodeTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%">Leave Application No</th>
        <th  class="ROW3"style="width: 40%">Date</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td class="ROW2"  style="width: 40%">
        <input type="text" autocomplete="off"  class="form-control" />
      </td>
      <td class="ROW3"  style="width: 40%">
        <input type="text" autocomplete="off"  class="form-control"/>
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

<div id="emppopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='emp_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Employee Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="MachTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%">Code</th>
        <th  class="ROW3"style="width: 40%">Description</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td class="ROW2"  style="width: 40%">
        <input type="text" autocomplete="off"  class="form-control" id="empcodesearch" onkeyup="EmpCodeFunction()" />
      </td>
      <td class="ROW3"  style="width: 40%">
        <input type="text" autocomplete="off"  class="form-control" id="empdatesearch" onkeyup="EmpNameFunction()" />
      </td>
    </tr>
    </tbody>
    </table>
      <table id="EmpTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_subglacct">       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>




<div id="typelvpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='type_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Type of Leave Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="TypeTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%">Code</th>
        <th  class="ROW3"style="width: 40%">Description</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td class="ROW2"  style="width: 40%">
        <input type="text" autocomplete="off"  class="form-control" id="empcodesearch" onkeyup="EmpCodeFunction()" />
      </td>
      <td class="ROW3"  style="width: 40%">
        <input type="text" autocomplete="off"  class="form-control" id="empdatesearch" onkeyup="EmpNameFunction()" />
      </td>
    </tr>
    </tbody>
    </table>
      <table id="TypeTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_subgltype">       
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


function setfocus(){
    var focusid=$("#focusid").val();
    $("#"+focusid).focus();
    $("#closePopup").click();
} 

function validateForm(){

    $("#focusid").val('');
    var EMPID_REF                 =   $.trim($("[id*=EMPID_REF]").val());
    var txtitem_popup           =   $.trim($("#txtitem_popup").val());
    $("#OkBtn1").hide();

    if(EMPID_REF ===""){
      $("#focusid").val('EMPID_REF');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter Employee Code.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    else if(txtitem_popup ===""){
      $("#focusid").val('txtitem_popup');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter Leave Application No.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    
    else{
        event.preventDefault();
          var allblank1 = [];
          var focustext1= "";        
          $('#example2').find('.participantRow').each(function(){ 
          if($.trim($(this).find("[id*=LTID_REF]").val()) ==""){            
            allblank1.push('false');
            focustext1 = $(this).find("[id*=LTID_REF]").attr('id');
          }
      });

        if(jQuery.inArray("false", allblank1) !== -1){
            $("#focusid").val(focustext1);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").hide();  
            $("#OkBtn").show();
            $("#AlertMessage").text('Please select Type of Leave in Material Tab.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else{
              $("#alert").modal('show');
              $("#AlertMessage").text('Do you want to save to record.');
              $("#YesBtn").data("funcname","fnSaveData");  
              $("#YesBtn").focus();
              highlighFocusBtn('activeYes');
          }

    }
  
}

  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("transaction",[404,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formResponseMst = $( "#frm_mst_add" );
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
      var validator =$("#frm_mst_add" ).validate();
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
        var getDataForm = $("#frm_mst_add");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[404,"codeduplicate"])}}',
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

    //validate
    $( "#btnSave" ).click(function() {
        if(formResponseMst.valid()){

            validateForm();

        }
    });
    
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
            url:'{{route("transaction",[404,"save"])}}',
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
                    $("#frm_mst_add").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn1").focus();

                  //  window.location.href='{{ route("transaction",[404,"index"])}}';
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
      var name = el.attr('name') || null;
    if(name){
      var nameLength = name.split('_').pop();
      var i = name.substr(name.length-nameLength.length);
      var prefix1 = name.substr(0, (name.length-nameLength.length));
      el.attr('name', prefix1+(+i+1));
    }
  });

  $clone.find('input:text').val('');
  $clone.find('input:hidden').val('');
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
    //window.location.href = "{{route('transaction',[404,'index'])}}";

    });


    
    $("#OkBtn").click(function(){
      $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('transaction',[404,'add'])}}";

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
//PROD CODE popoup click
$('#txtitem_popup').click(function(event){

if ($('#Tax_State').length) 
{
  var taxstate = $('#Tax_State').val();
}
else
{
  var taxstate = '';
}

var CODE = ''; 
var LEAVE_APP_DT = '';
var FORMID = "{{$FormId}}";
loadItem_prod_code(taxstate,CODE,FORMID,LEAVE_APP_DT); 

 $("#proidpopup").show();
event.preventDefault();
});

$("#gl_closePopup").click(function(event){
    $("#ItemProCodeSearch").val('');
    $("#proidpopup").hide();
    event.preventDefault();
  });

  function loadItem_prod_code(taxstate,CODE,FORMID,LEAVE_APP_DT){

    $("#tbody_prod_code").html('loading...');
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $.ajax({
      url:'{{route("transaction",[404,"getItemDetails_prod_code"])}}',
      type:'POST',
      data:{'taxstate':taxstate,'CODE':CODE,'LEAVE_APP_DT':LEAVE_APP_DT},
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

$('#ITEMSCodeTable2').off(); 


$('[id*="chkIdProdCode"]').change(function(){
  
  //var fieldid = $(this).attr('id');
  var fieldid = $(this).parent().parent().attr('id');

  var txtval          =    $("#txt"+fieldid+"").val();  
  var LEAVE_APP_NO            =   $("#txt"+fieldid+"").data("code");
  var LEAVE_APP_DT    =   $("#txt"+fieldid+"").data("lappdt");
  var PAY_PERIOD_CODE =   $("#txt"+fieldid+"").data("paycode");
  var PAY_PERIOD_DESC =   $("#txt"+fieldid+"").data("payid");
  var LEAVE_APP_FRDT  =   $("#txt"+fieldid+"").data("lappfdt");
  var LEAVE_APP_TODT  =   $("#txt"+fieldid+"").data("lapptdt");
  var REASON_LEAVE    =   $("#txt"+fieldid+"").data("relv");
  var ADDRESS_LEAVE   =   $("#txt"+fieldid+"").data("addlv");
  var MONO_INLEAVE1   =   $("#txt"+fieldid+"").data("monoinlv1");

  $('#txtitem_popup').val(LEAVE_APP_NO);
  $('#LEAVE_APPIDCODE').val(txtval);
  $("#proidpopup").hide();
  $("#LEAVE_APP_DT").val(LEAVE_APP_DT);
  $("#PAY_PERIOD_CODE").val(PAY_PERIOD_CODE); 
  $("#PAY_PERIOD_DESC").val(PAY_PERIOD_DESC);
  $("#LEAVE_APP_FRDT").val(LEAVE_APP_FRDT);
  $("#LEAVE_APP_TODT").val(LEAVE_APP_TODT);
  $("#REASON_LEAVE").val(REASON_LEAVE);
  $("#ADDRESS_LEAVE").val(ADDRESS_LEAVE);
  $("#MONO_INLEAVE1").val(MONO_INLEAVE1);

  var lfromTodate   = new Date(LEAVE_APP_TODT) - new Date(LEAVE_APP_FRDT);
  var totalDays = (lfromTodate / (1000 * 60 * 60 * 24)+1);
  $("#totalday").val(totalDays);
  $("#ItemProCodeSearch").val('');

    var customid = txtval;
          $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
          });
          $.ajax({
              url:'{{route("transaction",[404,"getALeaveNoMaterial"])}}',
              type:'POST',
              data:{'id':customid},
              success:function(data) {
                //alert(data);

                $('#LVAMaterialBdy').html('');
                  $('#LVAMaterialBdy').html(data);
                  event.preventDefault();
              },
              error:function(data){
                console.log("Error: There is no Item Available.");
                $('#LVAMaterialBdy').html(MaterialClone);
              },
          });       
  event.preventDefault();
  });
}

    function getEmpCode(EMPID){
		$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
		
        $.ajax({
            url:'{{route("transaction",[404,"getEmpCode"])}}',
            type:'POST',
            data:{EMPID:EMPID},
            success:function(data) {
               $("#FNAME").val(data);                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });	
  }

    
  function leaveFromDate(id){
  var ROW_ID = id.split('_').pop();
  var FROM_DT    =   $('#FROM_DT_'+ROW_ID+'').val();
  var TO_DT    =   $('#TO_DT_'+ROW_ID+'').val();
  var lfromTodate   = new Date(TO_DT) - new Date(FROM_DT);
  var totalDays = (lfromTodate / (1000 * 60 * 60 * 24)+1);
  var LEAVE_BLANCE    =   $('#LEAVE_BLANCE_0').val();
  var totleave = LEAVE_BLANCE - totalDays;
  if(isNaN(totalDays)){ return 0;}
    $('#TOTAL_DAYS_'+ROW_ID+'').val(totalDays); 
    $('#LEAVE_BLANCE_'+ROW_ID+'').val(totleave);
}

//Machine Starts
//------------------------
let sgltid = "#EmpTable2";
      let sgltid2 = "#MachTable";
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
        table = document.getElementById("EmpTable2");
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
        input = document.getElementById("empdatesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("EmpTable2");
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

$("#EMPCODE").focus(function(event){
  
  $('#tbody_subglacct').html('Loading...');
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{route("transaction",[404,"getemplCode"])}}',
      type:'POST',
      success:function(data) {
          $('#tbody_subglacct').html(data);
          bindEmpEvents();
      },
      error:function(data){
          console.log("Error: Something went wrong.");
          $('#tbody_subglacct').html('');
      },
  });        
   $("#emppopup").show();
   event.preventDefault();
}); 

$("#emp_closePopup").on("click",function(event){ 
  $("#emppopup").hide();
  event.preventDefault();
});
function bindEmpEvents(){

      $('.clsemp').click(function(){
  
          var id = $(this).attr('id');
          var txtval =    $("#txt"+id+"").val();
          var texdesc =   $("#txt"+id+"").data("desc");
          var FNAME =   $("#txt"+id+"").data("ccname");
          var LEAVE_BLANCE =   $("#txt"+id+"").data("lvopbl");
          var oldID =   $("#EMPID_REF").val();
         
          $("#EMPCODE").val(texdesc);
          $("#EMPCODE").blur();
          $("#EMPID_REF").val(txtval);
          $("#FNAME").val(FNAME);
          $("#LEAVE_BLANCE_0").val(LEAVE_BLANCE);
         
          if (txtval != oldID)
          {
            $("#txtchecklist_popup").val('');
            $("#CHECKLIST_REF").val('');
            $("#CHECKLISTNAME").val('');
          }
          $("#emppopup").hide();
          $("#machinecodesearch").val(''); 
          $("#machinedatesearch").val(''); 
         
          EmpCodeFunction();
          $(this).prop("checked",false);
          event.preventDefault();
      });
}

$('#Material').on('click','[id*="LEAVETYPE"]',function(event){

  var id = $(this).attr('id');
  $('#tbody_subgltype').html('Loading...');
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{route("transaction",[404,"gelvtypeCode"])}}',
      type:'POST',
      success:function(data) {
          $('#tbody_subgltype').html(data);
          bindMetrialLeaveEvents(id);
      },
      error:function(data){
          console.log("Error: Something went wrong.");
          $('#tbody_subgltype').html('');
      },
  });        
   $("#typelvpopup").show();
  event.preventDefault();          
});

$("#type_closePopup").on("click",function(event){ 
  $("#typelvpopup").hide();
  event.preventDefault();
});
function bindMetrialLeaveEvents(textid){

  $('.clslvtype').click(function(){
  var id = $(this).attr('id');
  var txtval =    $("#txt"+id+"").val();
  var texdesc =   $("#txt"+id+"").data("desc");
  var ccname =   $("#txt"+id+"").data("ccname");
  $("#"+textid).val(ccname);
  $("#EMPCODE").blur();
  $("#EMPID_REF").val(txtval);
  $("#typelvpopup").hide();
  $(this).prop("checked",false);
  event.preventDefault();
});
     
}


function getTotalDays(id,value){

var ROW_ID = id.split('_').pop();
var LEAVE_APP_FRDT    =   $('#LEAVE_APP_FRDT_'+ROW_ID+'').val();
var LEAVE_APP_TODT    =   $('#LEAVE_APP_TODT_'+ROW_ID+'').val();
var TOTAL_DAYS        =   $('#TOTAL_DAYS_'+ROW_ID+'').val();
//alert(TOTAL_DAYS);
if((new Date(LEAVE_APP_TODT)) < (new Date(LEAVE_APP_FRDT))){
    $("#FocusId").val('LEAVE_APP_TODT');    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('LA From Date Greater Then LA To Date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    var LEAVE_APP_TODT    =   $('#LEAVE_APP_TODT_'+ROW_ID+'').val('');
    return false;
  } else{
var lfromTodate   = new Date(LEAVE_APP_TODT) - new Date(LEAVE_APP_FRDT);
  var totalDays = (lfromTodate / (1000 * 60 * 60 * 24)+1);
  var totleave = TOTAL_DAYS - totalDays;
  if(isNaN(totalDays)){ return 0;}
    $('#ITEMSPECI_'+ROW_ID+'').val(totalDays); 
    $('#TOTAL_DAYS_'+ROW_ID+'').val(totleave);
    $('#LEAVE_APP_FRDT_'+ROW_ID+'').val(LEAVE_APP_FRDT);
    //$('#TOTAL_DAYS_'+ROW_ID+'').val(TOTAL_DAYS);
  }   
}

</script>

<script>
    function onlyNumberKey(evt) {
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
            return false;
        return true;
    }
</script>

@endpush