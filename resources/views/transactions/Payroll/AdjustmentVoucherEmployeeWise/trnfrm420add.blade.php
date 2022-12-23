@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Adjustment Voucher</a>
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
                  <div class="col-lg-2 pl"><p>Doc No*</p></div>
                    <div class="col-lg-2 pl">
                      {{-- @if(!empty($objDD)) --}}
                      {{-- @if($objDD->SYSTEM_GRSR == "1") --}}
                          <input type="text" name="DOC_NO" id="DOC_NO" value="{{ old('DOC_NO') }}" class="form-control mandatory" tabindex="1" maxlength="100" autocomplete="off" style="text-transform:uppercase" autofocus >
                      {{-- @endif --}}
                      {{-- @if($objDD->MANUAL_SR == "1") --}}
                          {{-- <input type="text" name="DOC_NO" id="DOC_NO" value="{{ old('DOC_NO') }}" class="form-control mandatory"  maxlength="{{$objDD->MANUAL_MAXLENGTH}}" tabindex="1" autocomplete="off" style="text-transform:uppercase" autofocus > --}}
                      {{-- @endif --}}
                  {{-- @endif  --}}
                    <span class="text-danger" id="ERROR_DOC_NO_REF"></span>                             
                    </div>

                    <div class="col-lg-2 pl"><p>Date*</p></div>
                    <div class="col-lg-2 pl">
                      <input type="date" name="REMB_DT" id="REMB_DT" onchange="checkPeriodClosing('{{$FormId}}',this.value,1)" class="form-control"  maxlength="100" > 
                    </div>

                  <div class="col-lg-2 pl"><p>Pay Period Code*</p></div>
                    <div class="col-lg-2 pl">
                      <select name="PAYPERIODID_REF" id="PAYPERIODID_REF" class="form-control mandatory" onchange="getPayPrName(this.value)" tabindex="4">
                        <option value="" selected="">Select</option>
                        @foreach($objList as $val)
                        <option value="{{$val->PAYPERIODID}}">{{$val->PAY_PERIOD_CODE}}</option>
                        @endforeach
                      </select>
                      <span class="text-danger" id="ERROR_PAYPERIODID_REF"></span>                             
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-2 pl"><p>Description</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" id="PAY_PERIOD_DESC" class="form-control" readonly  maxlength="100" > 
                    </div>
                    <div class="col-lg-2 pl"><p>Employee Code *</p></div>
                    <div class="col-lg-2 pl">
                    {{-- <select name="EMPID_REF" id="EMPID_REF" class="form-control mandatory" onchange="getEmpName(this.value)" tabindex="4">
                      <option value="" selected="">Select</option>
                      @foreach($objDataList as $val)
                      <option value="{{$val->EMPID}}">{{$val->EMPCODE}}</option>
                      @endforeach
                    </select>
                    <input type="hidden" id="focusid" >
                    <span class="text-danger" id="ERROR_EMPID_REF"></span>--}}

                    <input type="text" name="EMPID_REF" id="txtitem_popup"  class="form-control mandatory"  autocomplete="off" readonly/>
                    <input type="hidden" name="EMPID_REF" id="EMPID_REF" class="form-control" autocomplete="off" />
                      <input type="hidden" id="focusid" >
                      <span class="text-danger" id="ERROR_EMPID_REF"></span>                             
                  </div>            
                </div>
                
                <div class="row">
                      <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#Material" id="MAT_TAB" >Earning/Deduction Head</a></li>
                      </ul>
                      Note:- 1 row mandatory in Tab
                      <div class="tab-content">
                      <div id="Material" class="tab-pane fade in active">
                          <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                            <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                              <thead id="thead1"  style="position: sticky;top: 0">                      
                                <tr>  
                                <th rowspan="2"  width="3%">Adjustment Type </th>                         
                                <th rowspan="2"  width="3%">Earning Head Code </th>                         
                                <th rowspan="2" width="3%">Employee Name </th>
                                <th rowspan="2" width="3%">Designation</th>
                                <th rowspan="2" width="3%">Department</th>
                                <th rowspan="2" width="3%">Amount +</th>
                                <th rowspan="2" width="3%">Amount -</th>
                                <th rowspan="2" width="3%">Remarks</th>
                                <th rowspan="2" width="3%">Action </th>
                              </tr>                      
                                
                            </thead>
                              <tbody>
                                <tr  class="participantRow">

                                  <td><select name="ADJUSTMENT_TYPE[]" id="ADJUSTMENT_TYPE_0" onchange="getEarHeadName(this.id,this.value)" class="form-control mandatory">
                                    <option value="" selected="">Select</option>
                                    <option value="Earning Head">Earning Head</option>
                                    <option value="Deduction Head">Deduction Head</option>
                                  </select>
                                </td>
                                  <td><select name="EARNIGHEAD_REF[]" id="EARNIGHEAD_REF_0" class="form-control mandatory">
                                    <option value="" selected="">Select</option>
                                    @foreach($objEarnHead as $val)
                                    <option value="{{$val->EARNING_HEADID}}">{{$val->EARNING_HEADCODE}}</option>
                                    @endforeach
                                  </select>
                                </td>
                                  <td><input  class="form-control" type="text" name="FNAME[]"       id ="FNAME_0"         autocomplete="off" style="width: 99%" readonly></td>
                                  <td><input  class="form-control" type="text" name="DESGCODE[]"    id ="DESGCODE_0"      autocomplete="off" style="width: 99%" readonly></td>
                                  <td><input  class="form-control" type="text" name="DCODE[]"       id ="DCODE_0"         autocomplete="off" style="width: 99%" readonly></td>
                                  <td><input  class="form-control" type="text" name="AMOUNTPLUS[]"  id ="AMOUNTPLUS_0"  onkeypress="return onlyNumberKey(event)" autocomplete="off" style="width: 99%"></td>
                                  <td><input  class="form-control" type="text" name="AMOUNTSUB[]"   id ="AMOUNTSUB_0"   onkeypress="return onlyNumberKey(event)" autocomplete="off" style="width: 99%"></td>
                                  <td><input  class="form-control" type="text" name="REMARKS[]"     id ="REMARKS_0"     autocomplete="off" style="width: 99%"></td>
                                  
                                  <td>
                                    <button class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                                    <button class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                                  </td>
                                </tr>
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
	  <div class="tablename"><p>Employee Code Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ITEMProCodeTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%">Employee Code</th>
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

function validateForm(){
    $("#focusid").val('');

    var DOC_NO                =   $.trim($("[id*=DOC_NO]").val());
    var REMB_DT               =   $.trim($("[id*=REMB_DT]").val());
    var PAYPERIODID_REF       =   $.trim($("[id*=PAYPERIODID_REF]").val());
    var txtitem_popup         =   $.trim($("[id*=txtitem_popup]").val());
    var REMAMOUNT             =   $.trim($("[id*=REMAMOUNT]").val());
    
    $("#OkBtn1").hide();
    if(DOC_NO ===""){
      $("#focusid").val('DOC_NO');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter Doc No.');
      $("#alert").modal('show');
      $("#OkBtn").focus();
      return false;
    }
    else if(REMB_DT ===""){
      $("#focusid").val('REMB_DT');
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").hide();  
      $("#OkBtn").show();              
      $("#AlertMessage").text('Please enter Date.');
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
      $("#AlertMessage").text('Please enter Pay Period Code.');
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
      $("#AlertMessage").text('Please enter Employee Code.');
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
          var AMOUNT = $.trim($(this).find("[id*=AMOUNT]").val());
          if($.trim($(this).find("[id*=ADJUSTMENT_TYPE]").val()) ==""){
            allblank1.push('false');
            focustext1 = $(this).find("[id*=ADJUSTMENT_TYPE]").attr('id');
            textmsg = 'Please enter Adjustment Type';
          }
          else if($.trim($(this).find("[id*=EARNIGHEAD_REF]").val()) ==""){
            allblank1.push('false');
            focustext1 = $(this).find("[id*=EARNIGHEAD_REF]").attr('id');
            textmsg = 'Please enter Earning Head Code	';
          }
            else if($.trim($(this).find("[id*=AMOUNTPLUS]").val()) ==""){
              allblank1.push('false');
              focustext1 = $(this).find("[id*=AMOUNTPLUS]").attr('id');
              textmsg = 'Please enter AMOUNT+';
            }
            else if($.trim($(this).find("[id*=AMOUNTSUB]").val()) ==""){
              allblank1.push('false');
              focustext1 = $(this).find("[id*=AMOUNTSUB]").attr('id');
              textmsg = 'Please enter AMOUNT-';
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
          else if(checkPeriodClosing('{{$FormId}}',$("#REMB_DT").val(),0) ==0){
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text(period_closing_msg);
            $("#alert").modal('show');
            $("#OkBtn1").focus();
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
            url:'{{route("transaction",[$FormId,"save"])}}',
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



//Employee CODE popoup click
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
var FORMID = "{{$FormId}}";
loadItem_prod_code(taxstate,CODE,FORMID); 

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
      url:'{{route("transaction",[$FormId,"getItemDetails_prod_code"])}}',
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

$('#ITEMSCodeTable2').off(); 


$('[id*="chkIdProdCode"]').change(function(){
  
  //var fieldid = $(this).attr('id');
  var fieldid = $(this).parent().parent().attr('id');
  var txtval          =    $("#txt"+fieldid+"").val();  
  var LEAVE_APP_NO    =   $("#txt"+fieldid+"").data("code");
  var fieldid2 = $(this).parent().parent().children('[id*="fnameid"]').attr('id');
  var fieldid3 = $(this).parent().parent().children('[id*="desgid"]').attr('id');
  var fieldid4 = $(this).parent().parent().children('[id*="deptid"]').attr('id');
  var txtfvalue2 =  $("#txt"+fieldid2+"").val();
  var txtfname2 = $("#txt"+fieldid2+"").data("fname");
  var txtfname3 = $("#txt"+fieldid3+"").data("desg");
  var txtfname4 = $("#txt"+fieldid4+"").data("dept");

  $('#txtitem_popup').val(LEAVE_APP_NO);
  $('#EMPID_REF').val(txtval);
  $("#proidpopup").hide();
  event.preventDefault();

  $('#example2').find('.participantRow').each(function(){
    $(this).find('[id*="FNAME_"]').val(txtfname2);
    $(this).find('[id*="DESGCODE_"]').val(txtfname3);
    $(this).find('[id*="DCODE_"]').val(txtfname4);
    });

    $("#Material").on('click', '.add', function() {
    $('#example2').find('.participantRow').each(function(){
    $(this).find('[id*="FNAME_"]').val(txtfname2);
    $(this).find('[id*="DESGCODE_"]').val(txtfname3);
    $(this).find('[id*="DCODE_"]').val(txtfname4);
    });
  });

  });
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