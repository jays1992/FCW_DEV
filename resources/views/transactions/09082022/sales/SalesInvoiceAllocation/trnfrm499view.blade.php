
@extends('layouts.app')
@section('content')
<!-- <form id="frm_mst_se" onsubmit="return validateForm()"  method="POST"  >     -->
 
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Sales Invoice Allocation</a>
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
      <form id="frm_mst_edit" onsubmit="return validateForm(actionType)" method="POST"  class="needs-validation"> 
        @CSRF
        {{isset($HRD->SALINVALID) ? method_field('PUT') : '' }}
      <div class="inner-form">
      <div class="row">
        <div class="col-lg-2 pl"><p>Doc No*</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="DOC_NO" id="DOC_NO" value="{{isset($HRD->SALINVAL_NO) && $HRD->SALINVAL_NO !=''?$HRD->SALINVAL_NO:''}}" class="form-control mandatory"  autocomplete="off" readonly >
        </div>
        <div class="col-lg-2 pl"><p>Doc Date*</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="date" name="DOC_DT" id="DOC_DT" value="{{isset($HRD->SALINVAL_DATE) && $HRD->SALINVAL_DATE !=''?$HRD->SALINVAL_DATE:''}}" class="form-control mandatory"  autocomplete="off" >
        </div> 
          
        <div class="col-lg-2 pl"><p>From Date</p></div>
          <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="date" name="FROM_DATE" id="FROM_DATE" value="{{isset($HRD->SALINVAL_FROM_DATE) && $HRD->SALINVAL_FROM_DATE !=''?$HRD->SALINVAL_FROM_DATE:''}}" class="form-control mandatory"  autocomplete="off" >
        </div>
        </div>
        <div class="row">
          <div class="col-lg-2 pl"><p>To Date*</p></div>
          <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="date" name="TO_DATE" id="TO_DATE" value="{{isset($HRD->SALINVAL_TO_DATE) && $HRD->SALINVAL_TO_DATE !=''?$HRD->SALINVAL_TO_DATE:''}}" class="form-control mandatory"  autocomplete="off" >
          </div>
        </div> 

        <div class="row">
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
          </ul>
          Note:- 1 row mandatory in Tab
        <div class="tab-content">

          <div id="Material" class="tab-pane fade in active">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
              <table id="example1" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0">                      
                  <tr>                          
                  <th rowspan="2" width="3%">Sales Invoice Number</th>
                  <th rowspan="2" width="1%">Sales Invoice Date</th>
                  <th rowspan="2" width="3%">Customer Name</th>
                  <th rowspan="2" width="3%">Sales Invoice Amount</th>
                  <th rowspan="2" width="3%">Sales Person</th>
                  <th rowspan="2" width="3%">Bifurcated Amount</th>
                  <th rowspan="2" width="3%">Action </th>
                </tr>                      
                  
              </thead>
                <tbody>
                  @if(!empty($MAT))
                  @foreach($MAT as $key => $row)
                  <tr  class="participantRow">
                    <td><input {{$ActionStatus}} type="text" name="SALESINVOICENO[]"          id ="SALESINVOICENO_{{$key}}"  value="{{isset($row->SINO) && $row->SINO !=''?$row->SINO:''}}" onclick="getSaleInvNo(this.id,this.value)"  class="form-control mandatory"  autocomplete="off" readonly/></td>
                    <td hidden><input type="hidden" name="SALESINVID_REF[]" id="SALESINVID_REF_{{$key}}"   value="{{isset($row->SIID) && $row->SIID !=''?$row->SIID:''}}" class="form-control" autocomplete="off" /></td>
                    
                    <td><input {{$ActionStatus}} type="date" name="SALESINVOICEDATE"          id="SALESINVOICEDATE_{{$key}}" value="{{isset($row->SIDT) && $row->SIDT !=''?$row->SIDT:''}}" class="form-control" readonly></td>                    
                    <td><input {{$ActionStatus}} type="text" name="CUSTOMERNAME[]"            id="CUSTOMERNAME_{{$key}}"     value="{{isset($row->NAME) && $row->NAME !=''?$row->NAME:''}}" class="form-control" readonly></td>
                    <td><input {{$ActionStatus}} type="text" name="SALESINVOICEAMT[]"         id="SALESINVOICEAMT_{{$key}}"  value="{{isset($row->SALINVOICE_AMT) && $row->SALINVOICE_AMT !=''?$row->SALINVOICE_AMT:''}}" class="form-control" readonly></td>
                    
                    <td><input {{$ActionStatus}} type="text" name="EMPLOYEENAME[]"            id="EMPLOYEENAME_{{$key}}"     value="{{isset($row->EMPCODE) && $row->EMPCODE !=''?$row->EMPCODE:''}} {{isset($row->FNAME) && $row->FNAME !=''?'- '.$row->FNAME:''}}" onclick="getEmplyName(this.id,this.value)" class="form-control mandatory" autocomplete="off" readonly></td>
                    <td hidden><input type="hidden" name="EMPID_REF[]"      id="EMPID_REF_{{$key}}"        value="{{isset($row->EMPID) && $row->EMPID !=''?$row->EMPID:''}}" class="form-control" autocomplete="off" /></td>

                    <td><input {{$ActionStatus}} type="text" name="BIFURCATEAMOUNT[]"         id="BIFURCATEAMOUNT_{{$key}}"  value="{{isset($row->BIFURCATED_AMT) && $row->BIFURCATED_AMT !=''?$row->BIFURCATED_AMT:''}}" class="form-control" onkeypress="return isNumberKey(this, event);"></td>

                    <td align="center">
                    <button {{$ActionStatus}} class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                    <button {{$ActionStatus}} class="btn remove" title="Delete" data-toggle="tooltip"><i class="fa fa-trash" ></i></button>
                    </td>
                  </tr>
                  @endforeach
                  @endif
                </tbody>
              </table>
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

<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content" >
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button></div>
      <div class="modal-body">
	      <div class="tablename"><p id='title_name'></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered">
            <thead>
              <tr id="none-select" class="searchalldata" hidden >
                <td> 
                  <input type="text" name="fildinv_id1" id="hdn_INVID1"/>
                  <input type="text" name="fildinv_id2" id="hdn_INVID2"/>
                  <input type="text" name="fildinv_id3" id="hdn_INVID3"/>
                  <input type="text" name="fildinv_id4" id="hdn_INVID4"/>
                  <input type="text" name="fildinv_id6" id="hdn_INVID6"/>
                  <input type="text" name="fildinv_id7" id="hdn_INVID7"/>
                  <input type="text" name="fildinv_id9" id="hdn_INVID9"/>
                  <input type="text" name="fildinv_id10" id="hdn_INVID10"/>
                  <input type="text" name="fildinv_id18" id="hdn_INVID18"/>
                  <input type="text" name="fildinv_id19" id="hdn_INVID19"/>
                  <input type="text" name="fildinv_id20" id="hdn_INVID20"/>
                  <input type="text" name="hdn_INVID21" id="hdn_INVID21" value="0"/>
                  <input type="text" name="fildinv_id22" id="hdn_INVID22"/>
                  <input type="text" name="fildinv_id23" id="hdn_INVID23"/>
                  <input type="text" name="fildinv_id24" id="hdn_INVID24"/>
                  <input type="text" name="fildinv_id25" id="hdn_INVID25"/>
                </td>
              </tr>
              <tr>
                <th class="ROW1" id="all-check">Select</th> 
                <th class="ROW2"><p id='th_code'></th>
                <th class="ROW3"><p id='th_name'></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th class="ROW1"><span class="check_th">&#10004;</span></th>
                <td class="ROW2"><input type="text" autocomplete="off"  class="form-control" id="codesearch"  onkeyup='colSearch("tabletab2","codesearch",1)' /></td>
                <td class="ROW3"><input type="text" autocomplete="off"  class="form-control" id="namesearch"  onkeyup='colSearch("tabletab2","namesearch",2)' /></td>
            </tr>
            </tbody>
          </table>
          <table id="tabletab2" class="display nowrap table  table-striped table-bordered">
            <thead id="thead2"></thead>
            <tbody id="tbody_divpopp"></tbody>
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

$("#btnSave" ).click(function() {
    var formResponseMst = $("#frm_mst_edit");
    if(formResponseMst.valid()){
      validateForm();
    }
});

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
        var DOC_NO        =   $.trim($("#DOC_NO").val());
        var DOC_DT        =   $.trim($("#DOC_DT").val());
        var FROM_DATE     =   $.trim($("#FROM_DATE").val());
        var TO_DATE       =   $.trim($("#TO_DATE").val());
       
        
        $("#OkBtn1").hide();
        if(DOC_NO ===""){
          alertMsg('DOC_NO','Please enter Doc No.');
        }
        else if(DOC_DT ===""){
          alertMsg('DOC_DT','Please Select Doc Date.');
        }
        else if(FROM_DATE ===""){
          alertMsg('FROM_DATE','Please Select From Date.');
        }
        else if(TO_DATE ===""){
        alertMsg('TO_DATE','Please Select To Date');
      }
      else{
        event.preventDefault();
          var allblank1 = [];
          var focustext1= "";
          var textmsg = "";

          $('#example1').find('.participantRow').each(function(){
            if($.trim($(this).find("[id*=SALESINVID_REF]").val()) ==""){
            allblank1.push('false');
            focustext1 = $(this).find("[id*=SALESINVOICENO]").attr('id');
            textmsg = 'Please Select Sales Invoice Number in Material Tab';
            }
            
            else if($.trim($(this).find("[id*=EMPID_REF]").val()) ==""){
              allblank1.push('false');
              focustext1 = $(this).find("[id*=EMPLOYEENAME]").attr('id');
              textmsg = 'Please Select Sales Person in Material Tab';
            }         

            else if($.trim($(this).find("[id*=BIFURCATEAMOUNT]").val()) ==""){
              allblank1.push('false');
              focustext1 = $(this).find("[id*=BIFURCATEAMOUNT]").attr('id');
              textmsg = 'Please enter Bifurcated Amount in Material Tab';
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
            highlighFocusBtn('activeOk1');
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
            normalizer: function(value) {
                return $.trim(value);
            },
            messages: {
              required: "Required field."
            }
        });
    
        function validateSingleElemnet(element_id){
          var validator =$("#frm_mst_edit" ).validate();
             if(validator.element( "#"+element_id+"" )){
              if(element_id=="ATTCODE" || element_id=="attcode" ) {
                checkDuplicateCode();
              }
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
  var formResponseMst = $("#frm_mst_edit");
  if(formResponseMst.valid()){
    $("#alert").modal('show');
    $("#AlertMessage").text('Do you want to save to record.');
    $("#YesBtn").data("funcname",type);
    $("#YesBtn").focus();
    highlighFocusBtn('activeYes');
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

    //add row Material
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
      $tr.closest('table').append($clone);         
      var rowCount1 = $('#Row_Count').val();
      rowCount1 = parseInt(rowCount1)+1;
      $('#Row_Count').val(rowCount1);
      $clone.find('.remove').removeAttr('disabled'); 
      event.preventDefault();
    });

    //delete row Material
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
    
        $("#ITEMID_closePopup").on("click",function(event){ 
        $("#ITEMIDpopup").hide();
        event.preventDefault();
        });

      function getSaleInvNo(id){

        var ROW_ID = id.split('_').pop();
        var INVFROMDATE =  $("#FROM_DATE").val();
        var INVTODATE   =  $("#TO_DATE").val();

        $('#tbody_divpopp').html('Loading...');
          $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
          
            $.ajax({
                url:'{{route("transaction",[$FormId,"getINVDetails"])}}',
                type:'POST',
                data:{'INVFROMDATE':INVFROMDATE,'INVTODATE':INVTODATE},
                success:function(data) {
                    $('#tbody_divpopp').html(data);
                    bindSalesInvoiceNumber(ROW_ID);
                },
                error:function(data){
                    console.log("Error: Something went wrong.");
                    $('#tbody_divpopp').html('');
                },
            });
            $("#title_name").text('Invoice No Details');
            $("#th_code").text('Invoice No');
            $("#th_name").text('Invoice Date');    
            $("#ITEMIDpopup").show();
            event.preventDefault();
        }

      function bindSalesInvoiceNumber(ROW_ID){
        $('.clsinvno').click(function(){
        var idslinv = $(this).attr('id');
        var txtval =    $("#txt"+idslinv+"").val();
        var texdesc =   $("#txt"+idslinv+"").data("desc");
        var texcdate =   $("#txt"+idslinv+"").data("cdate");
        var texcname =   $("#txt"+idslinv+"").data("cname");
        var texcsiamt =   $("#txt"+idslinv+"").data("csiamt");

        $('#SALESINVOICENO_'+ROW_ID+'').val(texdesc);
        $('#SALESINVID_REF_'+ROW_ID+'').val(txtval);
        $('#SALESINVOICEDATE_'+ROW_ID+'').val(texcdate);
        $('#CUSTOMERNAME_'+ROW_ID+'').val(texcname);
        $('#SALESINVOICEAMT_'+ROW_ID+'').val(texcsiamt);
        $("#ITEMIDpopup").hide();
        });
      } 

function getEmplyName(id){

var ROW_ID = id.split('_').pop();
$('#tbody_divpopp').html('Loading...');
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  
    $.ajax({
        url:'{{route("transaction",[$FormId,"getEmplyDetails"])}}',
        type:'POST',
        success:function(data) {
            $('#tbody_divpopp').html(data);
            bindSalesPerson(ROW_ID);
        },
        error:function(data){
            console.log("Error: Something went wrong.");
            $('#tbody_divpopp').html('');
        },
    });
    $("#title_name").text('Sales Person Details');
    $("#th_code").text('Code');
    $("#th_name").text('Name');     
    $("#ITEMIDpopup").show();
    event.preventDefault();
}

function bindSalesPerson(ROW_ID){
    $('.clsemp').click(function(){
    var idemp = $(this).attr('id');
    var txtval =    $("#txt"+idemp+"").val();
    var texdesc =   $("#txt"+idemp+"").data("desc");

    $('#EMPLOYEENAME_'+ROW_ID+'').val(texdesc);
    $('#EMPID_REF_'+ROW_ID+'').val(txtval);
    $("#ITEMIDpopup").hide();
    });
} 

      
    $(document).ready(function(e) {
    var d = new Date(); 
    var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    $('#DOC_DT').val(today);
    });
        
    function onlyNumberKey(evt) {
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
            return false;
        return true;
    }
    </script>    
    <script type="text/javascript">
      function isNumberKey(txt, evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode == 46) {
          //Check if the text already contains the . character
          if (txt.value.indexOf('.') === -1) {
            return true;
          } else {
            return false;
          }
        } else {
          if (charCode > 31 &&
            (charCode < 48 || charCode > 57))
            return false;
        }
        return true;
      }
    </script>
    @endpush
  