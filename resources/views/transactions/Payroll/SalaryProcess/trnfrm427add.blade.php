@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Salary Process</a>
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
              
                <div class="row">
                  <div class="col-lg-2 pl"><p>Salary Process No*</p></div>
                  <div class="col-lg-2 pl">  
                    <input type="text" name="DOCNO" id="DOCNO" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
                    <script>docMissing(@json($docarray['FY_FLAG']));</script> 
                    <span class="text-danger" id="ERROR_DOCNO"></span> 
                  </div>

                  <div class="col-lg-2 pl"><p>Salary Process Date*</p></div>
                    <div class="col-lg-2 pl">
                      <input type="date" name="DOCDT" id="DOCDT" onchange='checkPeriodClosing("{{$FormId}}",this.value,1),getDocNoByEvent("DOCNO",this,@json($doc_req))' class="form-control"  maxlength="100" > 
                    </div>

                  <div class="col-lg-2 pl"><p>Month*</p></div>
                    <div class="col-lg-2 pl">
                      <select name="MONTH_REF" id="MONTH_REF" class="form-control mandatory" tabindex="4">
                        <option value="" selected="">Select</option>
                        @foreach($monthList as $val)
                        <option value="{{$val->MTID}}">{{$val->MTCODE}}-{{$val->MTDESCRIPTION}}</option>
                        @endforeach
                      </select>
                      <span class="text-danger" id="ERROR_MONTH_REF"></span>                             
                    </div>
                  </div> 

                  <div class="row">
                    <div class="col-lg-2 pl"><p>Year *</p></div>
                    <div class="col-lg-2 pl">
                    <select name="YEAR_REF" id="YEAR_REF" class="form-control mandatory" tabindex="4">
                      <option value="" selected="">Select</option>
                      @foreach($yearList as $val)
                      <option value="{{$val->YRID}}">{{$val->YRCODE}}</option>
                      @endforeach
                    </select>
                    <input type="hidden" id="focusid" >
                    <span class="text-danger" id="ERROR_YEAR_REF"></span>                             
                  </div>
                  
                  <div class="col-lg-2 pl"><p>Department*</p></div>
                    <div class="col-lg-2 pl">
                      <input type="radio" name="TEXT_DID_REF" id="DEPTEMP" class="checkclick" onclick="showHideModal('show')" >
                      <input type="hidden" name="DID_REF" id="DID_REF" class="form-control" autocomplete="off" >
                    </div>

                    <div class="col-lg-2 pl"><p>Employee*</p></div>
                    <div class="col-lg-2 pl">
                      <input type="radio" name="EMPLOYEE" id="EMPLOYEE" class="checkclick" onclick="EmpShowHideModal('show')" >
                      <input type="hidden" name="EMPID_REF" id="EMPID_REF" class="form-control" autocomplete="off" >
                    </div>
                </div>


                  <div class="row">
                    <ul class="nav nav-tabs">
                      <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
                    </ul>
                  
                    <div class="tab-content">
                      <div id="Material" class="tab-pane fade in active">
                        <div class="table-responsive table-wrapper-scroll-y" style="height:350px;margin-top:10px;" >
                          <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
                            <thead id="thead1"  style="position: sticky;top: 0">
                              <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody id="tbodymaterial">
                              <tr  class="participantRow">
                              <td style="width:20%;"><input type="text" name="DEPARTCODE[]" id="DEPARTCODE" class="form-control"  autocomplete="off"  readonly/></td>
                              <td style="width:60%;"><input  type="text" name="DEPARTNAME[]" id="DEPARTNAME" class="form-control" autocomplete="off" readonly></td>
                              <td align="center" style="width:10%;" ><button class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                              </tr>
                            <tr></tr>
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
              <input type="hidden" id="FocusId" >
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="DEP_MODAL" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:80%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" onclick="showHideModal('hide')" >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Department Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="DEP_TABLE1" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr>
      <th style="width:10%; text-align:center;">Select</th> 
      <th style="width:30%;">Department Code</th>
      <th style="width:60%;">Department Name</th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td style="width:10%; text-align:center;"><input type="checkbox" class="Main_js-selectall" data-target=".Main_js-selectall1" /></td>
        <td style="width:30%;"><input type="text" class="form-control" autocomplete="off" id="depcodesearch"  onkeyup='colSearch("DEP_TABLE2","depcodesearch",1)'></td>
        <td style="width:60%;"><input type="text" class="form-control" autocomplete="off" id="depnamesearch"  onkeyup='colSearch("DEP_TABLE2","depnamesearch",2)'></td>
      </tr>
    </tbody>
    </table>
      <table id="DEP_TABLE2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">          
        </thead>
        <tbody style="font-size:13px;">
        @foreach ($department as $key=>$val)
        <tr>
          <td style="width:10%;" align="center"><input type="checkbox" name="SELECT_DID_REF[]" data-desc="{{ $val->DCODE }}" data-desc2="{{ $val->NAME }}" class="checkbox" value="{{ $val-> DEPID }}" onChange="bindDep(this.value,'{{ $val-> DCODE }} - {{ $val-> NAME }}')" ></td>
          <input type="hidden" id="txtnor_taxidref_{{ $val->DEPID }}" data-desc="{{ $val->DCODE }}" data-desc2="{{ $val->NAME }}"  value="{{ $val->DEPID }}"/>
          <td style="width:30%;">{{ $val-> DCODE }}</td>
          <td style="width:60%;">{{ $val-> NAME }}</td>
        </tr>
        @endforeach
        </tbody>
      </table>

      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%" >
        <thead id="thead2"></thead>
        <tbody id="tbody_ItemID"></tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="EMP_MODAL" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:80%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" onclick="EmpShowHideModal('hide')" >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Employee Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="EMP_TABLE1" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr>
      <th style="width:10%;text-align:center;">Select</th> 
      <th style="width:30%;">Employee Code</th>
      <th style="width:60%;">Employee Name</th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td style="width:10%;" align="center"><input type="checkbox" class="Emp_js-selectall" data-target=".Emp_js-selectall1" /></td>
        <td style="width:30%;"><input type="text" class="form-control" autocomplete="off" id="empcodesearch"  onkeyup='colSearch("EMP_TABLE2","empcodesearch",1)'></td>
        <td style="width:60%;"><input type="text" class="form-control" autocomplete="off" id="empnamesearch"  onkeyup='colSearch("EMP_TABLE2","empnamesearch",2)'></td>
      </tr>
    </tbody>
    </table>
      <table id="EMP_TABLE2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">          
        </thead>
        <tbody style="font-size:13px;">
        @foreach ($employee as $key=>$val)
        <tr>
          <td style="width:10%;" align="center"> <input type="checkbox" name="SELECT_EMPID_REF[]" data-desc="{{ $val->EMPCODE }}" data-desc2="{{ $val->FNAME }}" class="checkbox" value="{{ $val-> EMPID }}" onChange="bindEmp(this.value,'{{ $val-> EMPCODE }} - {{ $val-> FNAME }}')" ></td>
          <input type="hidden" id="txtnor_taxidref_{{ $val->EMPID }}" data-desc="{{ $val->EMPCODE }}" data-desc2="{{ $val->FNAME }}"  value="{{ $val->EMPID }}"/>
          <td style="width:30%;">{{ $val-> EMPCODE }}</td>
          <td style="width:60%;">{{ $val-> FNAME }}</td>
        </tr>
        @endforeach
        </tbody>
      </table>

      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%" >
        <thead id="thead2"></thead>
        <tbody id="tbody_ItemID"></tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- Alert -->
@endsection


@push('bottom-scripts')
<script>

function setfocus(){
    var focusid=$("#focusid").val();
    $("#"+focusid).focus();
    $("#closePopup").click();
} 

/*************************************   All Search Start  ************************** */
let input, filter, table, tr, td, i, txtValue;
function colSearch(ptable,ptxtbox,pcolindex) {
  //var input, filter, table, tr, td, i, txtValue;
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


function showHideModal(type){
  if(type=='show'){
    $("#DEP_MODAL").show();
  }
  else{
    $("#DEP_MODAL").hide();
  }
}

function bindvalue()
{
  $('#Material').find('.participantRow').each(function(){
    var item_array = [];
        if ($("#DEPTEMP").prop("checked")) {
          if($.trim($(this).find("[id*=DEPID_]").val())!="")
          {
              var depid = $(this).val();
              item_array.push(depid);
              var data = item_array.join(",");
              $('#DID_REF').val(data);
              $('#EMPLOYEE').val('');
          }
        }
        else if ($("#EMPLOYEE").prop("checked")) {
          if($.trim($(this).find("[id*=EMPID_]").val())!="")
          {
              var depid = $(this).val();
              item_array.push(depid);
              var data = item_array.join(",");
              $('#DID_REF').val('');
              $('#EMPLOYEE').val(data);
          }
        }
        else{
          if($.trim($(this).find("[id*=EMPID_]").val())!="")
          {
              var depid = $(this).val();
              item_array.push(depid);
              var data = item_array.join(",");
              $('#DID_REF').val('');
              $('#EMPLOYEE').val(data);
          }
        }
    });
}

function bindDep(id,desc){
  $('#TEXT_DID_REF').val(desc);
  var programming = id.get().join(',');
  $('#DID_REF').val(programming);
  $('.checkbox').prop('checked', false);
  showHideModal('hide');
  getDataArray();
  event.preventDefault();
}

function getDataArray(){
  var DID_REF           = $("#DID_REF").val();
  $("#tbodymaterial").html('');
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  $.ajax({
    url:'{{route("transaction",[$FormId,"getDataArray"])}}',
    type:'POST',
    data:{DID_REF:DID_REF},
    success:function(data) {
      $("#tbodymaterial").html(data);
    },
    error:function(data){
      console.log("Error: Something went wrong.");
      $("#tbodymaterial").html('');
    },
  });
  bindvalue();
}

$(".Main_js-selectall").change(function () {
    var checkr5 = $(".Main_js-selectall").prop("checked", this.checked);
    var item_array = [];
    $('#DEP_TABLE2').find('.checkbox').each(function(){
      var depid = $(this).val();
      item_array.push(depid);
      var programming = item_array.join(",");
      $('#DID_REF').val(programming);
      $('#EMPLOYEE').val('');
    });
    getMaterialData(item_array,'DEP');
    $('.Main_js-selectall').prop("checked", false);
    bindvalue();
    
});

function getMaterialData(item_array,type){
$("#tbody_ItemID").html('');
$("#tbody_DPI").html('');
$("#tbodyid").empty();
$("#tbodyid").html('loading data...');    

$.ajaxSetup({
  headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

$.ajax({
    url:'{{route("transaction",[$FormId,"getMaterialData"])}}',
    type:'POST',
    data:{'item_array':item_array,type:type},
    success:function(data) {
      $("#tbodymaterial").html(data);
      $("#DEP_MODAL").hide();                              
    },
    error:function(data){
      console.log("Error: Something went wrong.");
      $("#tbodyid").html('');                        
    },
}); 
}


function EmpShowHideModal(type){
  if(type=='show'){
    $("#EMP_MODAL").show();
  }
  else{
    $("#EMP_MODAL").hide();
  }
}

function bindEmp(id,desc){
  $('#TEXT_EMPID_REF').val(desc);
  $('#EMPID_REF').val(id);
  $('.checkbox').prop('checked', false);
  EmpShowHideModal('hide');
  getEmployee();
  event.preventDefault();
}

function getEmployee(){
  var EMPID_REF           = $("#EMPID_REF").val();
  $("#tbodymaterial").html('');
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  $.ajax({
    url:'{{route("transaction",[$FormId,"getEmployee"])}}',
    type:'POST',
    data:{EMPID_REF:EMPID_REF},
    success:function(data) {
      $("#tbodymaterial").html(data);
    },
    error:function(data){
      console.log("Error: Something went wrong.");
      $("#tbodymaterial").html('');
    },
  });
  bindvalue();
}

$(".Emp_js-selectall").change(function () {
    var item_array = [];
    $('#EMP_TABLE2').find('.checkbox').each(function(){
      var empid = $(this).val();
      item_array.push(empid);
      var programming = item_array.join(",");
      $('#DID_REF').val('');
      $('#EMPLOYEE').val(programming);
    });
    getAllMaterialData(item_array,'EMP');
    $('.Emp_js-selectall').prop("checked", false);
    bindvalue();
    
});

function getAllMaterialData(item_array,type){
$("#tbody_ItemID").html('');
$("#tbody_DPI").html('');
$("#tbodyid").empty();
$("#tbodyid").html('loading data...');    

$.ajaxSetup({
  headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

$.ajax({
    url:'{{route("transaction",[$FormId,"getAllMaterialData"])}}',
    type:'POST',
    data:{'item_array':item_array,type:type},
    success:function(data) {
      $("#tbodymaterial").html(data);
      $("#EMP_MODAL").hide();                              
    },
    error:function(data){
      console.log("Error: Something went wrong.");
      $("#tbodyid").html('');                        
    },
}); 
}

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

   // getTotalRowValue();
    bindvalue();
    event.preventDefault();
});

$("#costpopup").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('.participantRow2').length;
    if (rowCount > 1) {
    $(this).closest('.participantRow2').remove();     
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
  $clone.find('input:text').removeAttr('disabled');
  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count1').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count1').val(rowCount1);
  $clone.find('.remove').removeAttr('disabled'); 
  event.preventDefault();
});


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

function validateForm(){
    $("#focusid").val('');
    var DOCNO      =   $.trim($("[id*=DOCNO]").val());
    var DOCDT      =   $.trim($("[id*=DOCDT]").val());
    var MONTH_REF  =   $.trim($("[id*=MONTH_REF]").val());
    var YEAR_REF   =   $.trim($("[id*=YEAR_REF]").val());
    var DEPTEMP    =   ($("#DEPTEMP").prop("checked"));
    var EMPLOYEE   =   ($("#EMPLOYEE").prop("checked"));


    $("#OkBtn1").hide();
    if(DOCNO ===""){
      alertMsg('DOCNO','Please enter DOCNO.');
    }
    else if(DOCDT ===""){
      alertMsg('DOCDT','Please select Salary Process Date.');
    }

    else if(MONTH_REF ===""){
      alertMsg('MONTH_REF','Please select Month for Salary Process');
    }
    else if(YEAR_REF ===""){
      alertMsg('YEAR_REF','Please select Year for Salary Process');
    }
    else if(DEPTEMP && EMPLOYEE) {
      alertMsg('DEPTEMP','Please select Department or Employee for Salary Process');
    }
    else if(checkPeriodClosing('{{$FormId}}',$("#DOCDT").val(),0) ==0){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text(period_closing_msg);
      $("#alert").modal('show');
      $("#OkBtn1").focus();
    }
    else{
        event.preventDefault();
        $("#alert").modal('show');
        $("#AlertMessage").text('Do you want to save to record.');
        $("#YesBtn").data("funcname","fnSaveData");  
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

 var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();
    $("#DOCNO").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_DOCNO").hide();
        validateSingleElemnet("DOCNO");
    });

    $( "#DOCNO" ).rules( "add", {
        required: true,
       normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_add" ).validate();
         if(validator.element( "#"+element_id+"" )){
          if(element_id=="DOCNO" || element_id=="DOCNO" ) {
            //checkDuplicateCode();
          }
         }
      }

    function checkDuplicateCode(){
        var getDataForm = $("#frm_mst_add");
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
                    showError('ERROR_DOCNO',data.msg);
                    $("#DOCNO").focus();
                }                                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
    }

    $( "#btnSave" ).click(function() {
        if(formResponseMst.valid()){
        validateForm();
        }
    });
    
    $("#YesBtn").click(function(){
        $("#alert").modal('hide');
        var customFnName = $("#YesBtn").data("funcname");
          window[customFnName]();
        }); 

    window.fnSaveData = function (){
        event.preventDefault();
        var getDataForm = $("#frm_mst_add");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[$FormId,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
              if(data.success) {                   
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#OkBtn").hide();
                $("#AlertMessage").text(data.msg);
                $("#alert").modal('show');
                $("#OkBtn1").focus();
              }
              else{
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").hide();
                $("#OkBtn").show();
                $("#AlertMessage").text(data.msg);
                $("#alert").modal('show');
                $("#OkBtn").focus();
              }
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
      
   } 

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

      $(document).ready(function(e) {
        var d = new Date(); 
        var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
        $('#DOCDT').val(today);
      });

    $('[class="checkclick"]').change(function(){
      if(this.checked){
        $('[class="checkclick"]').not(this).prop('checked', false);
      }    
    });

    function onlyNumberKey(evt) {
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
            return false;
        return true;
    }
    
</script>

@endpush