@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[188,'index'])}}" class="btn singlebt">Deduction Head</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button id="btnSave"   class="btn topnavbt" tabindex="4"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt"  id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove"{{($objRights->APPROVAL1 || $objRights->APPROVAL2 || $objRights->APPROVAL3 || $objRights->APPROVAL4 || $objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-lock"></i> Approved</button>
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</a>
                        <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_edit" method="POST"  > 
          @CSRF
          {{isset($objResponse->DEDUCTION_HEADID) ? method_field('PUT') : '' }}
          <div class="inner-form">
          
              
              <div class="row">
                  <div class="col-lg-2 pl"><p>Deduction Head Code</p></div>
                  <div class="col-lg-2 pl">
                  
                    <label> {{$objResponse->DEDUCTION_HEADCODE}} </label>
                    <input type="hidden" name="DEDUCTION_HEADID" id="DEDUCTION_HEADID" value="{{ $objResponse->DEDUCTION_HEADID }}" />
                    <input type="hidden" name="DEDUCTION_HEADCODE" id="DEDUCTION_HEADCODE" value="{{ $objResponse->DEDUCTION_HEADCODE }}" autocomplete="off"  maxlength="20"   />
                    <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
                  
                </div>
                </div>

                <div class="row">
                
                  <div class="col-lg-2 pl"><p>Description</p></div>
                  <div class="col-lg-5 pl">
                    <input type="text" name="DEDUCTION_HEAD_DESC" id="DEDUCTION_HEAD_DESC" class="form-control mandatory" value="{{ old('DEDUCTION_HEAD_DESC',$objResponse->DEDUCTION_HEAD_DESC) }}" maxlength="200" tabindex="1"  />
                    <span class="text-danger" id="ERROR_DEDUCTION_HEAD_DESC"></span> 
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-2 pl"><p>Deduction Head Type Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">
                        <input type="text" name="DEDUCTION_TYPEID_REF_POPUP" id="DEDUCTION_TYPEID_REF_POPUP" readonly  value="{{ old('DEDUCTION_TYPE_DESC',$objResponse->DEDUCTION_TYPECODE.'-'.$objResponse->DEDUCTION_TYPE_DESC) }}" class="form-control mandatory" autocomplete="off" maxlength="20" tabindex="1" style="text-transform:uppercase" />
                        <input type="hidden" name="DEDUCTION_TYPEID_REF" id="DEDUCTION_TYPEID_REF"  value="{{ old('DEDUCTION_TYPEID_REF',$objResponse->DEDUCTION_TYPEID_REF) }}" />
                        <span class="text-danger" id="ERROR_DEDUCTION_TYPEID_REF"></span> 
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-2 pl"><p>GL Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">
                        <input type="text" name="GLID_REF_POPUP" id="GLID_REF_POPUP" readonly  value="{{ old('GLNAME',$objResponse->GLCODE.'-'.$objResponse->GLNAME) }}" class="form-control mandatory" autocomplete="off" tabindex="1" style="text-transform:uppercase" />
                        <input type="hidden" name="GLID_REF" id="GLID_REF" value="{{$objResponse->GLID_REF}}" />
                        <span class="text-danger" id="ERROR_GLID_REF"></span> 
                    </div>
                  </div>
                </div>
          
          
             
              

              <div class="row">
                <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-1 pl pr">
                <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
                 value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
                </div>
                
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
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
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!--POST Deduction Head Type  Popup-->
<div id="postlevel_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='postlevelidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Deduciton Type List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="postlevel_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Deduction Type Code</th>
            <th  class="ROW3"style="width: 40%">Description</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="postlevel_codesearch" onkeyup="searchpostlevelCode()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="postlevel_namesearch" onkeyup="searchpostlevelName()" /></td>
        </tr>
      </table>


      <table id="postlevel_tab" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="postlevel_body">
        @foreach ($DeductionTypeList as $index=>$DeductionHeadType)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_DEDUCTION_TYPEID_REF[]"  id="postidref_{{ $DeductionHeadType->DEDUCTION_TYPEID }}" class="postlevel_tab" value="{{ $DeductionHeadType->DEDUCTION_TYPEID }}" ></td>
          <td class="ROW2" style="width: 39%">{{ $DeductionHeadType->DEDUCTION_TYPECODE }}
          <input type="hidden" id="txtpostidref_{{ $DeductionHeadType->DEDUCTION_TYPEID }}" data-desc="{{ $DeductionHeadType->DEDUCTION_TYPECODE.'-'.$DeductionHeadType->DEDUCTION_TYPE_DESC  }}" data-descname="{{ $DeductionHeadType->DEDUCTION_TYPE_DESC }}" value="{{ $DeductionHeadType-> DEDUCTION_TYPEID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $DeductionHeadType->DEDUCTION_TYPE_DESC }}</td>
        </tr>
        @endforeach
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!--POST Genral Ledger  Popup-->
<div id="gl_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='glidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>GL List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="postlevel_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">GL Code</th>
            <th  class="ROW3"style="width: 40%">GL Name</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
            <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="gl_codesearch" onkeyup="searchglCode()" /></td>
            <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="gl_namesearch" onkeyup="searchglName()" /></td>
          </tr>
        </tbody>
      </table>


      <table id="gl_tab" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="gl_body">
        @foreach ($GenralLedger as $index=>$GenralLedger)
        
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_GLID_REF[]"  id="glidref_{{ $GenralLedger->GLID }}" class="gl_tab" value="{{ $GenralLedger->GLID }}" ></td>
          <td class="ROW2" style="width: 39%">{{ $GenralLedger->GLCODE }}
          <input type="hidden" id="txtglidref_{{ $GenralLedger->GLID }}" data-desc="{{ $GenralLedger->GLCODE.'-'.$GenralLedger->GLNAME  }}" data-descname="{{ $GenralLedger->GLNAME }}" value="{{ $GenralLedger-> GLID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $GenralLedger->GLNAME }}</td>
        </tr>
        @endforeach
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
$('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[188,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formDataMst = $( "#frm_mst_edit" );
     formDataMst.validate();

     $("#DEDUCTION_HEAD_DESC").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_DEDUCTION_HEAD_DESC").hide();
        validateSingleElemnet("DEDUCTION_HEAD_DESC");
    });


    $("#DEDUCTION_HEAD_DESC").keydown(function(){
       
        $("#ERROR_DEDUCTION_HEAD_DESC").hide();
        validateSingleElemnet("DEDUCTION_HEAD_DESC");
    });


    $( "#DEDUCTION_HEAD_DESC" ).rules( "add", {
        required: true,
        //StringRegex: true,  //from custom.js
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });
    $( "#DEDUCTION_TYPEID_REF_POPUP" ).rules( "add", {
        required: true,
        //StringRegex: true,  //from custom.js
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#DEDUCTION_TYPEID_REF_POPUP").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_DEDUCTION_TYPEID_REF_POPUP").hide();
        validateSingleElemnet("DEDUCTION_TYPEID_REF_POPUP");
    });
    $( "#GLID_REF_POPUP" ).rules( "add", {
        required: true,
        //StringRegex: true,  //from custom.js
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#GLID_REF_POPUP").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_GLID_REF_POPUP").hide();
        validateSingleElemnet("GLID_REF_POPUP");
    });

    $("#DODEACTIVATED").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_DODEACTIVATED").hide();
      validateSingleElemnet("DODEACTIVATED");
    });

    $( "#DODEACTIVATED" ).rules( "add", {
        required: true,
        DateValidate:true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field"
        }
    });

    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_edit" ).validate();
          validator.element( "#"+element_id+"" );
    }

    //validate
    $( "#btnSave" ).click(function() {

        if(formDataMst.valid()){
            //set function nane of yes and no btn 
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
            $("#YesBtn").focus();
            highlighFocusBtn('activeYes');

        }

    });//btnSave

    
    //validate and approve
    $("#btnApprove").click(function() {
        
        if(formDataMst.valid()){
            //set function nane of yes and no btn 
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name of approval
            $("#YesBtn").focus();
            highlighFocusBtn('activeYes');

        }

    });//btnSave


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
            url:'{{route("mastermodify",[188,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.DEDUCTION_HEAD_DESC){
                        showError('ERROR_DEDUCTION_HEAD_DESC',data.errors.DEDUCTION_HEAD_DESC);
                    }
                   if(data.exist=='norecord') {

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
                    $("#frm_mst_edit").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn").focus();

                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });

    };// fnSaveData


    // save and approve 
    window.fnApproveData = function (){
        
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
            url:'{{route("mastermodify",[188,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.DEDUCTION_HEAD_DESC){
                        showError('ERROR_DEDUCTION_HEAD_DESC',data.errors.DEDUCTION_HEAD_DESC);
                    }
                   if(data.exist=='norecord') {

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
                    $("#frm_mst_edit").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn").focus();

                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });

    };// fnApproveData

    //no button
    $("#NoBtn").click(function(){

      $("#alert").modal('hide');
      var custFnName = $("#NoBtn").data("funcname");
          window[custFnName]();

    }); //no button

   
    $("#OkBtn").click(function(){

        $("#alert").modal('hide');

        $("#YesBtn").show();
        $("#NoBtn").show();
        $("#OkBtn").hide();

        $(".text-danger").hide();
        window.location.href = '{{route("master",[188,"index"]) }}';

    }); ///ok button

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

   
    $("#OkBtn").click(function(){
      
        $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.reload();

   }//fnUndoYes


   window.fnUndoNo = function (){

      $("#PLCODE").focus();

   }//fnUndoNo


    //
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
<script type="text/javascript">
$(function () {
	
	var today = new Date(); 
    var dodeactived_date = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
    $('#DODEACTIVATED').attr('min',dodeactived_date);

	$('input[type=checkbox][name=DEACTIVATED]').change(function() {
		if ($(this).prop("checked")) {
		  $(this).val('1');
		  $('#DODEACTIVATED').removeAttr('disabled');
		}
		else {
		  $(this).val('0');
		  $('#DODEACTIVATED').prop('disabled', true);
		  $('#DODEACTIVATED').val('');
		  
		}
	});

});

$(function() { 
  //$("#DEDUCTION_HEAD_DESC").focus(); 
});

// POST LEVEL popup function

// Deduction Head Type popup function

$("#DEDUCTION_TYPEID_REF_POPUP").on("click",function(event){ 
  $("#postlevel_popup").show();
});

$("#DEDUCTION_TYPEID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#postlevel_popup").show();
  }
});

$("#postlevelidref_close").on("click",function(event){ 
  $("#postlevel_popup").hide();
});

$('.postlevel_tab').click(function(){


  var id          =   $(this).attr('id');


  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#DEDUCTION_TYPEID_REF_POPUP").val(texdesc);
 // $("#DEDUCTION_TYPEID_REF_POPUP").val(texdesc);
  $("#DEDUCTION_TYPEID_REF").val(txtval);

 
  
  $("#DEDUCTION_TYPEID_REF_POPUP").blur(); 

  
  $("#postlevel_popup").hide();
  $("#postlevel_codesearch").val('');
  $("#postlevel_namesearch").val('');
  searchpostlevelCode();
  event.preventDefault();
  $(this).prop("checked",false);

});



function searchpostlevelCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("postlevel_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("postlevel_tab");
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

function searchpostlevelName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("postlevel_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("postlevel_tab");
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

// General Ledger popup function

$("#GLID_REF_POPUP").on("click",function(event){ 
  $("#gl_popup").show();
});

$("#GLID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#gl_popup").show();
  }
});

$("#glidref_close").on("click",function(event){ 
  $("#gl_popup").hide();
});

$('.gl_tab').click(function(){


  var id          =   $(this).attr('id');


  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#GLID_REF_POPUP").val(texdesc);
 // $("#GLID_REF_POPUP").val(texdesc);
  $("#GLID_REF").val(txtval);
  
  $("#GLID_REF_POPUP").blur();  
  $("#gl_popup").hide();
  $("#gl_codesearch").val('');
  $("#gl_namesearch").val('');
  searchglCode();
  $(this).prop("checked",false);
  event.preventDefault();
});



function searchglCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("gl_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("gl_tab");
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

function searchglName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("gl_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("gl_tab");
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


</script>


@endpush