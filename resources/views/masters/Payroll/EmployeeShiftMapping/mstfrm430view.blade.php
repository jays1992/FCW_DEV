
@extends('layouts.app')
@section('content')
<!-- <form id="frm_mst_se" onsubmit="return validateForm()"  method="POST"  >     -->
 
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Employee Shift Mapping</a>
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
      <form id="frm_mst_comp_edit" method="POST"  > 
        @CSRF
        {{isset($objCondition->EMP_SHIFTMAPID) ? method_field('PUT') : '' }}
      <div class="inner-form">
                
      <div class="row">
        <div class="col-lg-2 pl"><p>Doc No*</p></div>
        <div class="col-lg-2 pl">
          @if(isset($objSON->SYSTEM_GRSR) && $objSON->SYSTEM_GRSR == "1")
          <input {{$ActionStatus}} type="text" name="DOC_NO" id="DOC_NO" value="{{ $objCondition->DOC_CODE }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
        @elseif(isset($objSON->MANUAL_SR) && $objSON->MANUAL_SR == "1")
          <input {{$ActionStatus}} type="text" name="DOC_NO" id="DOC_NO" value="{{ old('DOC_NO') }}" class="form-control mandatory" maxlength="{{isset($objSON->MANUAL_MAXLENGTH)?$objSON->MANUAL_MAXLENGTH:''}}" autocomplete="off" style="text-transform:uppercase"  >
        @else
          <input {{$ActionStatus}} type="text" name="DOC_NO" id="DOC_NO" value="{{ $objCondition->DOC_CODE }}"  class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
          @endif
        </div>
        <div class="col-lg-2 pl"><p>Doc Date*</p></div>
          <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="date" name="DOC_DT" id="DOC_DT" value="{{ $objCondition->DOC_DATE }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
          </div> 
          
          <div class="col-lg-2 pl"><p>Department*</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="DEPTID_REF" id="dept_popup" value="{{ $objCondition->DCODE }}"  class="form-control mandatory"  autocomplete="off" readonly/>
            <input {{$ActionStatus}} type="hidden" name="DEPTID_REF" id="DEPTID_REF" value="{{ $objCondition->DEPID }}" class="form-control" autocomplete="off" />
              <span class="text-danger" id="ERROR_EMPID_REF"></span>                             
            </div>
          </div>
      
      <div class="row">
        <div class="table-responsive table-wrapper-scroll-y " style="height:330px;margin-top:10px;">
          Note:- 1 row mandatory in Tab
          <table id="example2" class="display nowrap table table-striped table-bordered itemlist itemlistscroll w-150" style="height:auto !important;">
          <thead id="thead1" style="position: sticky;top: 0; white-space:none;">
              <tr>
              <th>Employee Code</th>
              <th>Shift</th>
              </tr>
            </thead>
            <tbody id="MaterialBdy">
              @if(!empty($objConditiontemp))
              @foreach($objConditiontemp as $key => $row)
              <tr  class="participantRow" >
                <td style="width:27%;">
                  <input {{$ActionStatus}}  class="form-control w-100" type="text"    name="EMPCODE_REF[]"  id="EMPCODE_REF_{{$key}}" value="{{ $row->EMPCODE }}" readonly>
                  <input {{$ActionStatus}}  class="form-control w-100" type="hidden"  name="EMPID_REF[]"    id="EMPID_REF_{{$key}}" value="{{ $row->EMPID }}" readonly>
                </td>           
                <td style="width:16%;">
                  <select {{$ActionStatus}} class="form-control" name="SHIFTID_REF[]" id="SHIFTID_REF_{{$key}}" >
                    @foreach ($objshift as $key=>$val)
                    <option {{isset($row->SHIFTID_REF) && $row->SHIFTID_REF == $val-> SHIFTID ?'selected="selected"':''}} value="{{ $val-> SHIFTID }}">{{ $val->SHIFT_NAME }}</option>
                    @endforeach
                  </select>
                </td>
                {{-- <td style="width:10%;"><button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td> --}}
              </tr>
              <tr></tr>
              @endforeach 
            @endif 
            </tbody>
          </table>
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
              <button class="btn alertbt" name='OkBtn1' id="OkBtn1" onclick="getFocus()" style="display:none;margin-left: 90px;">
              <div id="alert-active" class="activeOk1"></div>OK</button>
              <input type="hidden" id="FocusId" >
          </div><!--btdiv-->
      <div class="cl"></div>
        </div>
      </div>
    </div>
    </div>
    
    <div id="deptpopup" class="modal" role="dialog"  data-backdrop="static">
    <div class="modal-dialog modal-md" style="width: 600px;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" id='gl_closePopup' >&times;</button>
        </div>
      <div class="modal-body">
      <div class="tablename"><p>Department Details</p></div>
      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
      <table id="MachTable" class="display nowrap table  table-striped table-bordered" width="100%">
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
          <input type="text" autocomplete="off"  class="form-control" id="codesearch" onkeyup="searchAstCode(this.id,'AstTable2','1')" />
        </td>
        <td class="ROW3"  style="width: 40%">
          <input type="text" autocomplete="off"  class="form-control" id="namesearch" onkeyup="searchAstCode(this.id,'AstTable2','3')" />
        </td>
      </tr>
      </tbody>
      </table>
        <table id="AstTable2" class="display nowrap table  table-striped table-bordered" width="100%">
          <thead id="thead2">
            
          </thead>
          <tbody id="tbody_dept_code">       
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
    <!-- btnSaveCountry -->
    @push('bottom-css')
    <style>
    /* 
    
    .select2-container__default .select2-results__group{
     color: #0f69cc;
    } */
    
    
    </style>
    @endpush
    @push('bottom-scripts')
    <script>
      
    function getFocus(){
    var FocusId=$("#FocusId").val();
    $("#"+FocusId).focus();
    $("#closePopup").click();
    }
    
    $(document).ready(function(e) {
    var formConditionMst = $( "#frm_mst_comp_edit" );
    formConditionMst.validate();
    
      $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[$FormId,"add"])}}';
      window.location.href=viewURL;
      });
    
      $('#btnExit').on('click', function() {
        var viewURL = '{{route('home')}}';
        window.location.href=viewURL;
      });
    
     
          $("#DOC_NO").blur(function(){
            $(this).val($.trim( $(this).val() ));
            $("#ERROR_DOC_NO").hide();
            validateSingleElemnet("DOC_NO");
          });
    
              $( "#DOC_NO" ).rules( "add", {
                  required: true,
                  nowhitespace: true,
                  StringNumberRegex: true,
                  messages: {
                  required: "Required field.",
                  minlength: jQuery.validator.format("min {0} char")
                  }
              });
            });
    
      function validateSingleElemnet(element_id){
        var validator =$("#frm_mst_comp_edit" ).validate();
           if(validator.element( "#"+element_id+"" )){
            if(element_id=="DOC_NO" || element_id=="DOC_NO" ) {
              checkDuplicateCode();
            }
    
           }
      }
    
      function checkDuplicateCode(){
          var conditionForm = $("#frm_mst_comp_edit");
          var formData = conditionForm.serialize();
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
          $.ajax({
              url:'{{route("master",[$FormId,"codeduplicate"])}}',
              type:'POST',
              data:formData,
              success:function(data) {
                  if(data.exists) {
                      $(".text-danger").hide();
                      showError('ERROR_DOC_NO',data.msg);
                      $("#DOC_NO").focus();
                  }                                
              },
              error:function(data){
                console.log("Error: Something went wrong.");
              },
          });
      }
    
              $( "#btnSave" ).click(function() {
                  var formConditionMst = $("#frm_mst_comp_edit");
                  if(formConditionMst.valid()){
                  $("#FocusId").val('');
                  var DOC_NO          =   $.trim($("[id*=DOC_NO]").val());
                  var DOC_DT          =   $.trim($("[id*=DOC_DT]").val());
                  var DEPTID_REF      =   $.trim($("[id*=DEPTID_REF]").val()); 
                  
                  if(DOC_NO ===""){
                  $("#FocusId").val('DOC_NO');
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").show();
                  $("#OkBtn").hide();
                  $("#AlertMessage").text('Please enter value in Company Holiday Code.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk1');
                  return false;
                  }
                  if(DOC_DT ===""){
                  $("#FocusId").val('DOC_DT');
                  $("#ProceedBtn").focus();
                  $("#DOC_DT").blur();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").show();
                  $("#OkBtn").hide();
                  $("#AlertMessage").text('Please enter value in Company Holiday Code.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk1');
                  return false;
                  }
                  
                  if(DEPTID_REF ===""){
                  $("#FocusId").val('dept_popup');
                  $("#ProceedBtn").focus();
                  $("#dept_popup").blur();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text('Please Select Department.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk1');
                  return false;
    
                }else{
                $("#alert").modal('show');
                $("#AlertMessage").text('Do you want to save to record.');
                $("#YesBtn").data("funcname","fnSaveData");
                $("#YesBtn").focus();
                $("#OkBtn1").hide();
                $("#OkBtn").hide();
                highlighFocusBtn('activeYes');
                }
              }  
          });
    
      
      $("#YesBtn").click(function(){
          $("#alert").modal('hide');
          var customFnName = $("#YesBtn").data("funcname");
           window[customFnName]();
        });
    
    
     window.fnSaveData = function (){
      event.preventDefault();
      var formConditionMst = $("#frm_mst_comp_edit");
      var formData = formConditionMst.serialize();
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{ route("mastermodify",[$FormId,"update"]) }}',
          type:'POST',
          data:formData,
          success:function(data) {
              if(data.errors) {
                  $(".text-danger").hide();
                  if(data.country=='duplicate') {
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#OkBtn").hide();
                    $("#AlertMessage").text(data.msg);
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
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
                //  window.location.href='{{ route("master",[4,"index"])}}';
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
          $(".text-danger").hide();
          window.location.href = '{{route("master",[$FormId,"index"]) }}';
          
      });
      
      $("#OkBtn1").click(function(){
        $("#alert").modal('hide');
        $("#YesBtn").show();
        $("#NoBtn").show(); 
        $("#OkBtn").hide();
        $(".text-danger").hide();
        $("[id*='txtcmpt']").focus();
        });
      
      $("#btnUndo").click(function(){
          $("#AlertMessage").text("Do you want to erase entered information in this record?");
          $("#alert").modal('show');
          $("#YesBtn").data("funcname","fnUndoYes");
          $("#YesBtn").show();
          $("#NoBtn").data("funcname","fnUndoNo");
          $("#NoBtn").show();
          $("#OkBtn").hide();
          $("#NoBtn").focus();
          highlighFocusBtn('activeNo');
      });
    
    
      $("#btnApprove").click(function() {
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnApproveData");
            $("#YesBtn").focus();
            highlighFocusBtn('activeYes');
      });
    
    
    
      
        window.fnApproveData = function (){
            event.preventDefault();
            var getDataForm = $("#frm_mst_comp_edit");
            var formData = getDataForm.serialize();
            $.ajaxSetup({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("mastermodify",[$FormId,"Approve"])}}',
                type:'POST',
                data:formData,
                success:function(data) {
                    if(data.errors) {
                        $(".text-danger").hide();
                        if(data.errors.DOC_NO){
                            showError('ERROR_NAME',data.errors.DOC_NO);
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
        };
    
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
            $(".text-danger").hide();
            window.location.href = '{{route("master",[$FormId,"index"]) }}';
          });
    
    
    
    
     window.fnUndoYes = function (){
      window.location.href = "{{route('master',[$FormId,'add'])}}";
     }
    
    
     window.fnUndoNo = function (){
      $("#txtctcode").focus();
     }
    
     function highlighFocusBtn(pclass){
      $(".activeYes").hide();
      $(".activeNo").hide();
      $("."+pclass+"").show();
      } 
     
    let AstTable2 = "#AstTable2";
    let MachTable = "#MachTable";
    let headers     = document.querySelectorAll(AstTable2 + " th");
    headers.forEach(function(element, i) {
      element.addEventListener("click", function() {
      w3.sortHTML(MachTable, ".clsdpid", "td:nth-child(" + (i + 1) + ")");
      });
    });
    
    function searchAstCode(search_id,table_id,index_no) {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById(search_id);
      filter = input.value.toUpperCase();
      table = document.getElementById(table_id);
      tr = table.getElementsByTagName("tr");
      for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[index_no];
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
    
    $('#dept_popup').click(function(event){
    var CODE = ''; 
    var FORMID = "{{$FormId}}";
    dept_code(CODE,FORMID); 
    $("#deptpopup").show();
    event.preventDefault();
    });
    
    $("#gl_closePopup").click(function(event){
        $("#deptpopup").hide();
        event.preventDefault();
      });
    
      function dept_code(CODE,FORMID){
        $("#tbody_dept_code").html('loading...');
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          url:'{{route("master",[$FormId,"getDeptDetails"])}}',
          type:'POST',
          data:{'CODE':CODE},
          success:function(data) {
          $("#tbody_dept_code").html(data); 
          bindDeptCode();
    
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $("#tbody_dept_code").html('');                        
          },
        });
    }
    
    
    function bindDeptCode(){
    
    $('[id*="chkIdDeptCode"]').change(function(){
    var fieldid = $(this).parent().parent().attr('id');
    var txtval          =    $("#txt"+fieldid+"").val();  
    var deptcode    =   $("#txt"+fieldid+"").data("deptcode");
    
    var fieldid2 = $(this).parent().parent().children('[id*="empid"]').attr('id');
    var txtempid =  $("#txt"+fieldid2+"").val();
    var txtempcode = $("#txt"+fieldid2+"").data("empcode");
    
    $('#dept_popup').val(deptcode);
    $('#DEPTID_REF').val(txtval);
    $("#deptpopup").hide();
    event.preventDefault();
    
    var customid = txtval;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("master",[$FormId,"getMaterial"])}}',
        type:'POST',
        data:{'id':customid},
        success:function(data) {
            $('#MaterialBdy').html(data);
            event.preventDefault();
        },
      });
    
    
    $('#example2').find('.participantRow').each(function(){
      $(this).find('[id*="EMPCODE_REF_"]').val(txtempid);
      $(this).find('[id*="EMPID_REF_"]').val(txtempcode);
      });
    });
    }
    
    
      
    $(document).ready(function(e) {
    var today = new Date(); 
    var currentdate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
    $('#DOC_DT').val(currentdate);
    });
    
    </script>
    
    @endpush