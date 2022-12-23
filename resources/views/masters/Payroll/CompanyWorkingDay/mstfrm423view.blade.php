
@extends('layouts.app')
@section('content')
<!-- <form id="frm_mst_se" onsubmit="return validateForm()"  method="POST"  >     -->
 
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Company Holiday</a>
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
      <form id="frm_mst_comp_edit" method="POST"> 
        @CSRF
        {{isset($HDR->CP_WORKINGID) ? method_field('PUT') : '' }}

        <div class="inner-form">
        <div class="row">
        <div class="col-lg-2 pl"><p>Company Working Code</p></div>
        <div class="col-lg-2 pl">
          @if(isset($objSON->SYSTEM_GRSR) && $objSON->SYSTEM_GRSR == "1")
          <input type="text" name="COMPANY_WORKING_CODE" id="COMPANY_WORKING_CODE" value="{{ $HDR->COMPANY_WORKING_CODE }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" disabled >
            @elseif(isset($objSON->MANUAL_SR) && $objSON->MANUAL_SR == "1")
            <input type="text" name="COMPANY_WORKING_CODE" id="COMPANY_WORKING_CODE" value="{{ old('COMPANY_WORKING_CODE') }}" class="form-control mandatory" maxlength="{{isset($objSON->MANUAL_MAXLENGTH)?$objSON->MANUAL_MAXLENGTH:''}}" autocomplete="off" style="text-transform:uppercase" disabled >
            @else
          <input type="text" name="COMPANY_WORKING_CODE" id="COMPANY_WORKING_CODE" value="{{ $HDR->COMPANY_WORKING_CODE }}"  class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" disabled >
          @endif
          <input type="hidden" name="CP_WORKINGID"  id="CP_WORKINGID" value="{{ $HDR->CP_WORKINGID }}" disabled>
        </div>

        <div class="col-lg-2 pl"><p>Company Working Date*</p></div>
          <div class="col-lg-2 pl">
          <input type="date" name="COMPANY_WORKING_DATE"  id="COMPANY_WORKING_DATE" value="{{ isset($HDR->COMPANY_WORKING_DATE)?$HDR->COMPANY_WORKING_DATE:''}}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" disabled>
          </div> 
          
          <div class="col-lg-2 pl"><p>Year*</p></div>
          <div class="col-lg-2 pl">
            <input type="text" name="YRIDREF" id="YRIDREF"     value="{{ isset($HDR->YRDESCRIPTION)?$HDR->YRDESCRIPTION:''}}" class="form-control mandatory"  autocomplete="off" readonly disabled />
            <input type="hidden" name="YRID_REF" id="YRID_REF" value="{{ isset($HDR->YRID_REF)?$HDR->YRID_REF:''}}" class="form-control" autocomplete="off" disabled/>
            <span class="text-danger" id="ERROR_YRID_REF"></span>                            
          </div>
        </div>
  
          <div class="row">
          <div class="col-lg-2 pl"><p>Month*</p></div>
          <div class="col-lg-2 pl">
            <input type="text" name="MTID_popup" id="MTID_popup" value="{{ isset($HDR->MTCODE)?$HDR->MTCODE:''}} - {{ isset($HDR->MTDESCRIPTION)?$HDR->MTDESCRIPTION:''}}" class="form-control mandatory"  autocomplete="off" readonly disabled/>
            <input type="hidden" name="MTID_REF" id="MTID_REF"   value="{{ isset($HDR->MTID_REF)?$HDR->MTID_REF:''}}" class="form-control" autocomplete="off" disabled/>
            <span class="text-danger" id="ERROR_MTID_REF"></span>                             
          </div>
      </div>
  
      <div class="row">
        <div class="table-responsive table-wrapper-scroll-y " style="height:472px;margin-top:10px;">
        <table id="example2" class="display nowrap table table-striped table-bordered itemlist itemlistscroll w-150" style="height:auto !important;">
          <thead id="thead1" style="position: sticky;top: 0; white-space:none;">
              <tr>
              <th> Date </th>
              <th> Working Day </th>
              </tr>
            </thead>
            <tbody id="MTIDMaterialBdy">
              @if(!empty($MAT))
                @foreach($MAT as $key => $row)
              <tr class="participantRow">
                  <td><input type="text" name="popupMENU[]" id="popupMENU_{{$key}}" value="{{ $row->WORIDAY_DATE }}"  class="form-control"  autocomplete="off"  readonly disabled/></td>
                  <td><input type="checkbox" name="Weeklyoff[]" id="Weeklyoff_{{$key}}" {{isset($row->CP_WORKINGID_REF) && $row->CP_WORKINGID_REF == 8 ? 'checked' : ''}}  autocomplete="off"  readonly disabled/></td>
                </tr>
                <tr>
              </tr>
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
  <div id="alert" class="modal"  role="dialog"  data-backdrop="static">
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
  <!-- Alert -->
    
  <div id="yearpopup" class="modal" role="dialog"  data-backdrop="static">
    <div class="modal-dialog modal-md" style="width: 600px;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" id='emp_closePopup' >&times;</button>
        </div>
      <div class="modal-body">
      <div class="tablename"><p>Year Details</p></div>
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
          <input type="text" autocomplete="off"  class="form-control" id="yearcodesearch" onkeyup="searchAstCode(this.id,'YearTable2','1')" />
        </td>
        <td class="ROW3"  style="width: 40%">
          <input type="text" autocomplete="off"  class="form-control" id="yeardessearch" onkeyup="searchAstCode(this.id,'YearTable2','1')" />
        </td>
      </tr>
      </tbody>
      </table>
        <table id="YearTable2" class="display nowrap table  table-striped table-bordered" width="100%">
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
  
  @endsection
  <!-- btnSaveCountry -->
  @push('bottom-css')
  <style>
  table#example2 {
      width: 406px;
  }
  
  input[type=checkbox], input[type=radio] {
      line-height: normal;
      margin-left: 50px;
  }
  </style>
  @endpush
  @push('bottom-scripts')
  <script>
        //delete row
      $("#example2").on('click', '.remove', function() {
      var rowCount = $(this).closest('table').find('tbody').length;
      if (rowCount > 1) {
      $(this).closest('tbody').remove();   
      } 
      if (rowCount <= 1) { 
      $(document).find('.remove').prop('disabled', false);  
      }
      event.preventDefault();
      });
  
      
  function getFocus(){
    var FocusId=$("#FocusId").val();
    $("#"+FocusId).focus();
    $("#closePopup").click();
  }
  
  //add row
          // $(".add").click(function() { 
          $("#example2").on('click', '.add', function() {
          var $tr = $(this).closest('table');
          var allTrs = $tr.find('tbody').last();      
          var lastTr = allTrs[allTrs.length-1];
          var $clone = $(lastTr).clone();
          $clone.find('td').each(function(){
              var el = $(this).find(':first-child');
              var id = el.attr('id') || null;
              if(id) {
                  var i = id.substr(id.length-1);
                  var prefix = id.substr(0, (id.length-1));
                  el.attr('id', prefix+(+i+1));
              }
              var name = el.attr('name') || null;
              if(name) {
                  var i = name.substr(name.length-1);
                  var prefix1 = name.substr(0, (name.length-1));
                  el.attr('name', prefix1+(+i+1));
              }
          });
          $clone.find('input:text').val('');
          $tr.closest('table').append($clone);         
          var rowCount = $('#Row_Count').val();
          rowCount = parseInt(rowCount)+1;
          $('#Row_Count').val(rowCount);
          $clone.find('.remove').removeAttr('disabled'); 
          $clone.find('[id*="txtdesc"]').val('');
          $clone.find('[id*="chkmdtry"]').prop('checked', false);
          event.preventDefault();
      });
  
  // });
  $(document).ready(function(e) {
    var formConditionMst = $( "#frm_mst_comp_edit" );
    formConditionMst.validate();
  
      $('#Row_Count').val("1");
    $('#btnAdd').on('click', function() {
        var viewURL = '{{route("master",[$FormId,"add"])}}';
                    window.location.href=viewURL;
      });
      $('#btnExit').on('click', function() {
        var viewURL = '{{route('home')}}';
        window.location.href=viewURL;
      });
  
          //Terms & Condition code
          $("#COMPANY_WORKING_CODE").blur(function(){
                $(this).val($.trim( $(this).val() ));
                $("#ERROR_COMPANY_WORK_CODE").hide();
                validateSingleElemnet("COMPANY_WORKING_CODE");
                  
              });
  
              $( "#COMPANY_WORKING_CODE" ).rules( "add", {
                  required: true,
                  //nowhitespace: true,
                  //StringNumberRegex: true, //from custom.js
                  messages: {
                      required: "Required field.",
                      //minlength: jQuery.validator.format("min {0} char")
                  }
              });
  
            });
  
      //validae single element
      function validateSingleElemnet(element_id){
        var validator =$("#frm_mst_comp_edit" ).validate();
           if(validator.element( "#"+element_id+"" )){
              //check duplicate code
            if(element_id=="LOBCOMPANY_HOLIDAY_CODE_NO" || element_id=="COMPANY_WORKING_CODE" ) {
              checkDuplicateCode();
            }
  
           }
      }
  
      // //check duplicate Calculation code
      function checkDuplicateCode(){
          //validate and save data
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
                showError('ERROR_COMPANY_HOLIDAY_CODE',data.msg);
                $("#COMPANY_WORKING_CODE").focus();
                }                                
              },
              error:function(data){
                console.log("Error: Something went wrong.");
              },
          });
      }
  
      //validate
      $( "#btnSave" ).click(function() {
          var formConditionMst = $("#frm_mst_comp_edit");
          if(formConditionMst.valid()){
            $("#FocusId").val('');
            var COMPANY_WORKING_CODE          =   $.trim($("[id*=COMPANY_WORKING_CODE]").val());
            var YRID_REF                   =   $.trim($("[id*=YRID_REF]").val());
            var MTID_REF                   =   $.trim($("[id*=MTID_REF]").val());
                  
            if(COMPANY_WORKING_CODE ===""){
              $("#FocusId").val('COMPANY_WORKING_CODE');
              $("#ProceedBtn").focus();
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#OkBtn").hide();
              $("#AlertMessage").text('Please enter value in Company Working Code.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk1');
              return false;
            }
            else if(YRID_REF ===""){
            $("#FocusId").val('YRIDREF');
            $("#ProceedBtn").focus();
            $("#YRID_REF").blur();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please Select Year.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
            return false;
              }
            else if(MTID_REF ===""){
            $("#FocusId").val('MTID_popup');
            $("#ProceedBtn").focus();
            $("#MTID_REF").blur();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please Select Month.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
            return false;
              }
            else {
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
            $("#YesBtn").focus();
            $("#OkBtn1").hide();
            $("#OkBtn").hide();
            highlighFocusBtn('activeYes');
            }
          }  
      });//btnSaveCountry
  
      $("#YesBtn").click(function(){
  
          $("#alert").modal('hide');
          var customFnName = $("#YesBtn").data("funcname");
              window[customFnName]();
      }); //yes button
  
        window.fnSaveData = function (){
          //validate and save data
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
        
     } // fnSaveData
  
      $('#chkforsale').change(function()
      {
        if ($(this).is(':checked') == true) {
            $('#chkforpurchase').attr('disabled',true);
            $('#chkforpurchase').attr('checked',false);
            event.preventDefault();
        }
        else
        {
          $('#chkforpurchase').removeAttr('disabled');
          event.preventDefault();
        }
      });
  
      $('#chkforpurchase').change(function()
      {
        if ($(this).is(':checked') == true) {
            $('#chkforsale').attr('disabled',true);
            $('#chkforsale').attr('checked',false);
            event.preventDefault();
        }
        else
        {
          $('#chkforsale').removeAttr('disabled');
          event.preventDefault();
        }
      });
  
  $('#example2').on("change",'[id*="decativateddate"]', function( event ) {
      var today = new Date();     //Mon Nov 25 2013 14:13:55 GMT+0530 (IST) 
      var d = new Date($(this).val()); 
      today.setHours(0, 0, 0, 0) ;
      d.setHours(0, 0, 0, 0) ;
      if (d < today) {
          $(this).val('');
          $("#alert").modal('show');
          $("#AlertMessage").text('Date cannot be less than Current date');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk1');
          event.preventDefault();
      }
      else
      {
          event.preventDefault();
      }           
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
          $(".text-danger").hide();
          window.location.href = '{{route("master",[$FormId,"index"]) }}';
          
      });
      
      $("#OkBtn1").click(function(){
        $("#alert").modal('hide');
        $("#YesBtn").show();  //reset
        $("#NoBtn").show();   //reset
        $("#OkBtn").hide();
        $(".text-danger").hide();
        $("[id*='txtcmpt']").focus();
        
        });
       ///ok button
      
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
      }); ////Undo button
  
     window.fnUndoYes = function (){
        //reload form
        window.location.href = "{{route('master',[$FormId,'add'])}}";
     }//fnUndoYes
  
     window.fnUndoNo = function (){
        $("#txtctcode").focus();
     }//fnUndoNo
  

    //Start All Search popup value
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
  //End All Search popup value
  
      function showError(pId,pVal){
        $("#"+pId+"").text(pVal);
        $("#"+pId+"").show();
      }//showError
  
      function highlighFocusBtn(pclass){
         $(".activeYes").hide();
         $(".activeNo").hide();
         
         $("."+pclass+"").show();
      }
  
      $(function() { $("#txtctcode").focus(); });
      // $(document).ready(function(){
      //  // Initialize select2
      //   $("[id*='drpbasis']").select2();
      // });
      $('#MTID_popup').click(function(event){
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
  
          $("#yearpopup").show();
          event.preventDefault();
          });
  
          $("#emp_closePopup").click(function(event){
              $("#ItemProCodeSearch").val('');
              $("#yearpopup").hide();
              event.preventDefault();
            });
  
            function loadItem_prod_code(taxstate,CODE,FORMID){
  
              $("#tbody_subglacct").html('loading...');
              $.ajaxSetup({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
              });
              $.ajax({
                url:'{{route("master",[$FormId,"getMTID"])}}',
                type:'POST',
                data:{'taxstate':taxstate,'CODE':CODE},
                success:function(data) {
                $("#tbody_subglacct").html(data); 
                bindMTID();
                bindYearEvents();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $("#tbody_subglacct").html('');                        
                },
              });
  
          }
  
        function bindMTID(){
          $('.clsMTID').click(function(){
      
              var id = $(this).attr('id');
              var txtval =    $("#txt"+id+"").val();
              var texdesc =   $("#txt"+id+"").data("desc");
              var MaterialClone = $('#hdnmaterial').val();
              var ScheduleClone = $('#hdnmaterial2').val();
              $("#MTID_popup").val(texdesc);
              $("#MTID_popup").blur();
              $("#MTID_REF").val(txtval);
              getmonthyearwisedate();
              $("#yearpopup").hide();
              $("#MTIDcodesearch").val(''); 
              $("#MTIDnamesearch").val(''); 
              event.preventDefault();
          });
    }
  
  $("#YRIDREF").focus(function(event){
    $('#tbody_subglacct').html('Loading...');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("master",[$FormId,"getyearCode"])}}',
        type:'POST',
        success:function(data) {
            $('#tbody_subglacct').html(data);
            bindYearEvents();
        },
        error:function(data){
            console.log("Error: Something went wrong.");
            $('#tbody_subglacct').html('');
        },
    });        
     $("#yearpopup").show();
     event.preventDefault();
  }); 
  
  $("#emp_closePopup").on("click",function(event){ 
    $("#yearpopup").hide();
    event.preventDefault();
  });
  
  function bindYearEvents(){
  
    $('.clsyear').click(function(){
        var idyear = $(this).attr('id');
        var txtvaly =    $("#txt"+idyear+"").val();
        var texdesc =   $("#txt"+idyear+"").data("desc");
        var oldID =   $("#YRID_REF").val();
        
        $("#YRIDREF").val(texdesc);
        $("#YRIDREF").blur();
        $("#YRID_REF").val(texdesc);
        getmonthyearwisedate();
        $("#yearpopup").hide();
        $("#machinecodesearch").val(''); 
        $("#machinedatesearch").val(''); 
        $(this).prop("checked",false);
        event.preventDefault();
    });
  }
    
  function getmonthyearwisedate(){
    var month = $("#MTID_REF").val();
    var year = $("#YRID_REF").val();
    if(month !='' && year !=''){
      $('#MTIDMaterialBdy').html('');
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
  
      $.ajax({
          url:'{{route("master",[$FormId,"getMTIDMaterial"])}}',
          type:'POST',
          data:{month:month,year:year},
          success:function(data) {
            $('#MTIDMaterialBdy').html(data);
          }
      });
  
    }
  }
  
  $(document).ready(function(e) {
    var today = new Date(); 
    var currentdate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
    $('#COMPANY_WORKING_DATE').val(currentdate);
  });
      
  
  </script>
  
  @endpush