@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Employee Declaration</a>
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
                <button class="btn topnavbt" id="btnAttach"  disabled="disabled" ><i class="fa fa-link"></i> Attachment</button>
                <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>

              </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_add" method="POST" onsubmit="return validateForm(actionType)" class="needs-validation"  > 
          @CSRF
          <div class="inner-form">              
                <div class="row">
                  <div class="col-lg-2 pl"><p>Doc No*</p></div>
                    <div class="col-lg-2 pl"> 
                    <input type="text" name="DOC_NO" id="DOC_NO" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
                    <script>docMissing(@json($docarray['FY_FLAG']));</script>
                    <span class="text-danger" id="ERROR_DOC_NO_REF"></span>                             
                    </div>

                    <div class="col-lg-2 pl"><p>Date*</p></div>
                    <div class="col-lg-2 pl">
                      <input type="date" name="REMB_DT" id="REMB_DT" onchange='checkPeriodClosing("{{$FormId}}",this.value,1),getDocNoByEvent("DOC_NO",this,@json($doc_req))' class="form-control"  maxlength="100" > 
                    </div>

                  <div class="col-lg-2 pl"><p>Financial Year*</p></div>
                    <div class="col-lg-2 pl">
                      <select name="FYID_REF" id="FYID_REF" class="form-control mandatory" onchange="getFYearName(this.value)" tabindex="4">
                        <option value="" selected="">Select</option>
                        @foreach($objFYear as $val)
                        <option value="{{$val->FYID}}">{{$val->FYCODE}}</option>
                        @endforeach
                      </select>
                      <span class="text-danger" id="ERROR_FYID_REF"></span>                             
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-2 pl"><p>Description</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" id="FYDESCRIPTION" class="form-control" readonly  maxlength="100" > 
                    </div>
                    <div class="col-lg-2 pl"><p>Employee Code *</p></div>
                    <div class="col-lg-2 pl">
                    <select name="EMPID_REF" id="EMPID_REF" class="form-control mandatory" onchange="getEmpName(this.value)" tabindex="4">
                      <option value="" selected="">Select</option>
                      @foreach($objDataList as $val)
                      <option value="{{$val->EMPID}}">{{$val->EMPCODE}}</option>
                      @endforeach
                    </select>
                    <input type="hidden" id="focusid" >
                    <span class="text-danger" id="ERROR_EMPID_REF"></span>                             
                  </div>            
                  
                  <div class="col-lg-2 pl"><p>Name</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="FNAME" id="FNAME" class="form-control" readonly  maxlength="100" > 
                    </div>
                </div>


                <div class="row">
                <div class="col-lg-2 pl"><p>Own Flat</p></div>
                    <div class="col-lg-2 pl">
                      <input type="checkbox" name="FLAT" id="FLAT" value="OWNFLAT"> 
                    <input type="hidden" id="focusid" >
                  </div>

                  <div class="col-lg-2 pl"><p>Rented Flat</p></div>
                  <div class="col-lg-2 pl">
                    <input type="checkbox" name="FLAT" id="FLAT" value="RENTEDFLAT">  
                  </div>

                  <div class="col-lg-2 pl"><p>Residence at Metropolitian City</p></div>
                  <div class="col-lg-2 pl">
                    <input type="checkbox" name="RESIDENCE" id="RESIDENCE" value="RESIDENCEMETROP">  
                  </div>
                </div> 


                <div class="row">
                  <div class="col-lg-2 pl"><p>Residence at Non-Metropolitian City </p></div>
                      <div class="col-lg-2 pl">
                        <input type="checkbox" name="RESIDENCE" id="RESIDENCE" value="RESIDENCENONMETROP"> 
                    </div>

                    <div class="col-lg-2 pl"><p>Senior Citizen </p></div>
                      <div class="col-lg-2 pl">
                        <input type="checkbox" name="SENIORCITIZEN" id="SENEXHAND" value="1"> 
                    </div>

                    <div class="col-lg-2 pl"><p>Ex-Defence Personnel </p></div>
                      <div class="col-lg-2 pl">
                        <input type="checkbox" name="EXDEFENCEPER" id="SENEXHAND" value="1"> 
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-2 pl"><p>Handicapped </p></div>
                      <div class="col-lg-2 pl">
                        <input type="checkbox" name="HANDICAPPED" id="SENEXHAND" value="1"> 
                    </div>

                    <div class="col-lg-2 pl"><p>SC / ST </p></div>
                      <div class="col-lg-2 pl">
                        <input type="checkbox" name="CAST_CATEGORY" id="SCSTGEN" value="1"> 
                    </div>

                    <div class="col-lg-2 pl"><p>General </p></div>
                      <div class="col-lg-2 pl">
                        <input type="checkbox" name="CAST_CATEGORY" id="SCSTGEN" value="1"> 
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-2 pl"><p>Married </p></div>
                      <div class="col-lg-2 pl">
                        <input type="checkbox" name="MARITAL_STATUS" id="MARUNMAROTHER" value="MARRIED"> 
                    </div>

                    <div class="col-lg-2 pl"><p>Unmarried </p></div>
                      <div class="col-lg-2 pl">
                        <input type="checkbox" name="MARITAL_STATUS" id="MARUNMAROTHER" value="UNMARRIED"> 
                    </div>
                    
                    <div class="col-lg-2 pl"><p>Others </p></div>
                      <div class="col-lg-2 pl">
                        <input type="checkbox" name="MARITAL_STATUS" id="MARUNMAROTHER" value="OTHERS"> 
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-2 pl"><p>No of Kids </p></div>
                      <div class="col-lg-2 pl">
                        <input type="text" name="KIDSNO" id="KIDSNO" class="form-control" maxlength="100" onkeypress="return onlyNumberKey(event)"> 
                    </div>

                    <div class="col-lg-2 pl"><p>Other than Salary Income </p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="OTHER_INCOME" id="OTHER_INCOME" class="form-control" maxlength="100" onkeypress="return onlyNumberKey(event)"> 
                  </div>
  
                    <div class="col-lg-2 pl"><p>Name of Flat Owner</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="NAMEOFLATOWNER" id="NAMEOFLATOWNER" class="form-control" maxlength="100" > 
                    </div>
                  </div>
  
                  <div class="row">
                    <div class="col-lg-2 pl"><p>Gender</p></div>
                    <div class="col-lg-2 pl">
                      <select name="GENDER_REF" id="GENDER_REF" class="form-control mandatory" tabindex="4">
                        <option value="" selected="">Select</option>
                        @foreach($objGnder as $val)
                        <option value="{{$val->GID}}">{{$val->DESCRIPTIONS}}</option>
                        @endforeach
                      </select> 
                    </div>

                    <div class="col-lg-2 pl"><p>PAN No</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="PANNO" id="PANNO" class="form-control" maxlength="100" > 
                    </div>

                    <div class="col-lg-2 pl"><p>Father Name</p></div>
                    <div class="col-lg-2 pl">
                      <input type="text" name="FLAT_FATHERNAME" id="FLAT_FATHERNAME" class="form-control" maxlength="100" > 
                    </div>
                  </div> 

                  <div class="row">
                    <ul class="nav nav-tabs">
                      <li class="active"><a data-toggle="tab" href="#Material" id="MAT_TAB" >Rent</a></li>
                      <li><a data-toggle="tab" href="#Savings">Savings</a></li>
                    </ul>
                    Note:- 1 row mandatory in Tab
                    <div class="tab-content">
                    <div id="Material" class="tab-pane fade in active">
                        <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                          <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                            <thead id="thead1"  style="position: sticky;top: 0">                      
                              <tr>                          
                              <th rowspan="2"  width="3%">Pay Period</th>                         
                              <th rowspan="2" width="3%">Amount</th>
                              <th rowspan="2" width="3%">Action </th>
                            </tr>                      
                              
                          </thead>
                            <tbody>
                              <tr  class="participantRow">
                                <td>
                                  <select name="PAYPERIOD_REF[]" id="PAYPERIOD_REF_0" class="form-control mandatory" tabindex="4">
                                  <option value="" selected="">Select</option>
                                  @foreach($objList as $val)
                                  <option value="{{$val->PAYPERIODID}}">{{$val->PAY_PERIOD_CODE}}</option>
                                  @endforeach
                                </select>
                              </td>
                                  <td><input  class="form-control" type="text" name="AMOUNT[]" id ="AMOUNT_0" onkeypress="return onlyNumberKey(event)" autocomplete="off" style="width: 99%"></td>
                                  
                                  <td align="center">
                                  <button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                                  <button class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                                  </td>
                              </tr>
                            </tbody>
                          </table>
                      </div>	
                  </div>

                      <div id="Savings" class="tab-pane fade in ">
                          <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                            <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                              <thead id="thead1"  style="position: sticky;top: 0">                      
                                <tr>                          
                                <th rowspan="2"  width="3%">Section</th>                         
                                <th rowspan="2" width="3%">Amount</th>
                                <th rowspan="2" width="3%">Remarks</th>
                                <th rowspan="2" width="3%">Action </th>
                              </tr>                      
                                
                            </thead>
                              <tbody>
                                <tr  class="participantRow">
                                  <td>
                                    <select name="SECTION_REF[]" id="SECTION_REF_0" class="form-control mandatory" tabindex="4">
                                    <option value="" selected="">Select</option>
                                    @foreach($objSection as $val)
                                    <option value="{{$val->SECTIONID}}">{{$val->SECTION_CODE}}</option>
                                    @endforeach
                                  </select>
                                </td>
                                    <td><input  class="form-control" type="text" name="AMOUNT[]" id ="AMOUNT_0"     onkeypress="return onlyNumberKey(event)" autocomplete="off" style="width: 99%"></td>
                                    <td><input  class="form-control" type="text" name="REMARKS[]" id ="REMARKS_0"   autocomplete="off" style="width: 99%"></td>
                                    
                                    <td align="center">
                                    <button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                                    <button class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
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
    var DOC_NO             =   $.trim($("[id*=DOC_NO]").val());
    var REMB_DT            =   $.trim($("[id*=REMB_DT]").val());
    var FYID_REF           =   $.trim($("[id*=FYID_REF]").val());
    var EMPID_REF          =   $.trim($("[id*=EMPID_REF]").val());
    var NAMEOFLATOWNER     =   $.trim($("[id*=NAMEOFLATOWNER]").val());
    var FLAT               =   ($('input[type=checkbox][id=FLAT]:checked').length == 0);
    var RESIDENCE          =   ($('input[type=checkbox][id=RESIDENCE]:checked').length == 0);
    var SENEXHAND          =   ($('input[type=checkbox][id=SENEXHAND]:checked').length == 0);
    var SCSTGEN            =   ($('input[type=checkbox][id=SCSTGEN]:checked').length == 0);
    var MARUNMAROTHER      =   ($('input[type=checkbox][id=MARUNMAROTHER]:checked').length == 0);
    var KIDSNO             =   $.trim($("[id*=KIDSNO]").val());
    var OTHER_INCOME       =   $.trim($("[id*=OTHER_INCOME]").val());

    
    $("#OkBtn1").hide();
    if(DOC_NO ===""){
      alertMsg('DOC_NO','Please enter Doc No.');
    }
    else if(REMB_DT ===""){
      alertMsg('REMB_DT','Please enter Date.');
    }
    else if(FYID_REF ===""){
      alertMsg('FYID_REF','Please enter Financial Year.');
    }
    else if(EMPID_REF ===""){
      alertMsg('EMPID_REF','Please enter Employee Code.');
    }
    else if(FLAT) {
      alertMsg('FLAT','Please enter Own Flat & Rented Flat.');
    }

    else if(RESIDENCE) {
      alertMsg('RESIDENCE','Please enter Residence Metropolitian & Residence Non-Metropolitian.');
    }
    else if(SENEXHAND) {
      alertMsg('SENEXHAND','Please enter Senior Citizen & Ex-Defence & Handicapped.');
    }
    else if(SCSTGEN) {
      alertMsg('SCSTGEN','Please enter SC / ST & General.');
    }
    else if(MARUNMAROTHER) {
      alertMsg('MARUNMAROTHER','Married & Unmarried & Others.');
    }

    else if(NAMEOFLATOWNER ==="") {
      alertMsg('NAMEOFLATOWNER','Please enter Name of Flat Owner.');
    }
    
    else if(KIDSNO ===""){
      alertMsg('KIDSNO','Please enter No of Kids.');
    } 
    
    else if(OTHER_INCOME ==="") {
      alertMsg('OTHER_INCOME','Please enter Other than Salary Income.');
    }
    else{
        event.preventDefault();
          var allblank1 = [];
          var focustext1= "";
          var textmsg = "";

          $('#example2').find('.participantRow').each(function(){
          var AMOUNT = $.trim($(this).find("[id*=AMOUNT]").val());
          if($.trim($(this).find("[id*=PAYPERIOD_REF]").val()) ==""){
            allblank1.push('false');
            focustext1 = $(this).find("[id*=PAYPERIOD_REF]").attr('id');
            textmsg = 'Please enter Pay Period';
          }
            else if($.trim($(this).find("[id*=AMOUNT]").val()) ==""){
              allblank1.push('false');
              focustext1 = $(this).find("[id*=AMOUNT]").attr('id');
              textmsg = 'Please enter Amount';
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
              $("#YesBtn").data("funcname",actionType);  
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
          if(element_id=="ATTCODE" || element_id=="attcode" ) {
            checkDuplicateCode();
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
        if(formResponseMst.valid()){
          validateForm("fnSaveData");
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
  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count').val(rowCount1);
  $clone.find('.remove').removeAttr('disabled'); 
  event.preventDefault();
});

//delete row
$("#Savings").on('click', '.remove', function() {
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
$("#Savings").on('click', '.add', function() {
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
      });
   
    
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
      });

    
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



    function getEmpName(EMPID){
      $("#FNAME").val('');		
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });	

        $.ajax({
            url:'{{route("transaction",[$FormId,"getEmpName"])}}',
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

    function getFYearName(FYID){
		$("#FYDESCRIPTION").val('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
		
        $.ajax({
            url:'{{route("transaction",[$FormId,"getFYearName"])}}',
            type:'POST',
            data:{FYID:FYID},
            success:function(data) {
               $("#FYDESCRIPTION").val(data);                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });	
  }

function getPayPeriod(id,EMPID){
var ROW_ID = id.split('_').pop();
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url:'{{route("transaction",[$FormId,"getPayPeriod"])}}',
        type:'POST',
        data:{EMPID:EMPID},
        success:function(data) {
          $('#EMPCODE_NAME_'+ROW_ID+'').val(data);                
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
  $('[id="FLAT"]').change(function(){
  if(this.checked){
     $('[id="FLAT"]').not(this).prop('checked', false);
  }    
});
$('[id="RESIDENCE"]').change(function(){
  if(this.checked){
     $('[id="RESIDENCE"]').not(this).prop('checked', false);
  }    
});
$('[id="SENEXHAND"]').change(function(){
  if(this.checked){
     $('[id="SENEXHAND"]').not(this).prop('checked', false);
  }    
});
$('[id="SCSTGEN"]').change(function(){
  if(this.checked){
     $('[id="SCSTGEN"]').not(this).prop('checked', false);
  }    
});
$('[id="MARUNMAROTHER"]').change(function(){
  if(this.checked){
     $('[id="MARUNMAROTHER"]').not(this).prop('checked', false);
  }    
});

});
    
function onlyNumberKey(evt) {
    var ASCIICode = (evt.which) ? evt.which : evt.keyCode
    if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
        return false;
    return true;
}
</script>

@endpush