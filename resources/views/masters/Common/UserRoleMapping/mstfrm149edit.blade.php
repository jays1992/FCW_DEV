@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[149,'index'])}}" class="btn singlebt">User - Role Mapping</a>
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
          {{isset($objResponse->URMID) ? method_field('PUT') : '' }}
          <div class="inner-form">
          
            
                <div class="row">
                  <div class="col-lg-2 pl"><p>User</p></div>
                  <div class="col-lg-2 pl">
                    
                    <input type="text" name="" id="" class="form-control mandatory" autocomplete="off" tabindex="1" readonly value="{{isset($objUserName->UCODE) && $objUserName->UCODE !=''?$objUserName->UCODE:''}}" />
                    <input type="hidden" name="USERID_REF" id="USERID_REF"  value="{{ $objResponse->USERID_REF }}" />
                    <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
                    <input type="hidden" name="URMID" id="URMID" value="{{ $objResponse->URMID }}" />
                    <span class="text-danger" id="ERROR_USERID_REF"></span> 
                   
                  </div>

                  <div class="col-lg-3 pl">
                    <input type="text" id="USER_DETAILS" class="form-control" readonly value="{{isset($objUserName->DESCRIPTIONS) && $objUserName->DESCRIPTIONS !=''?$objUserName->DESCRIPTIONS:''}}" />
                  </div>

                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Role</p></div>
                  <div class="col-lg-2 pl">
                      <input type="text" name="ROLLID_REF_POPUP" id="ROLLID_REF_POPUP" class="form-control mandatory" autocomplete="off" tabindex="2" readonly value="{{isset($objRoleName->RCODE) && $objRoleName->RCODE !=''?$objRoleName->RCODE:''}}"  />
                      <input type="hidden" name="ROLLID_REF" id="ROLLID_REF" value="{{ old('ROLLID_REF',$objResponse->ROLLID_REF) }}" />
                    <span class="text-danger" id="ERROR_ROLLID_REF"></span> 
                  </div>

                  <div class="col-lg-3 pl">
                    <input type="text" id="ROLL_DETAILS" class="form-control" readonly  value="{{isset($objRoleName->DESCRIPTIONS) && $objRoleName->DESCRIPTIONS !=''?$objRoleName->DESCRIPTIONS:''}}" />
                  </div>
                </div>

                
                <div class="row">

                <div class="col-lg-2 pl"><p>Effective Date</p></div>
                  <div class="col-lg-2 pl">
                    <input type="date" name="EFDATE" id="EFDATE" class="form-control mandatory"  tabindex="3" autocomplete="off"  value="{{isset($objResponse->EFDATE) && $objResponse->EFDATE !="" && $objResponse->EFDATE !="1900-01-01" ? $objResponse->EFDATE:''}}" placeholder="dd/mm/yyyy" />
                    <span class="text-danger" id="ERROR_EFDATE"></span> 
                  </div>

                  <div class="col-lg-1 pl"><p>End Date</p></div>
                  <div class="col-lg-2 pl">
                    <input type="date" name="ENDDATE" id="ENDDATE" class="form-control mandatory"  tabindex="4" autocomplete="off"  value="{{isset($objResponse->ENDDATE) && $objResponse->ENDDATE !="" && $objResponse->ENDDATE !="1900-01-01" ? $objResponse->ENDDATE:''}}" placeholder="dd/mm/yyyy" />
                    <span class="text-danger" id="ERROR_ENDDATE"></span> 
                  </div>

                  
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Reason of End Date</p></div>
                  <div class="col-lg-5 pl">
                    <input type="text" name="REASONOED" id="REASONOED" class="form-control mandatory" value="{{ old('REASONOED',$objResponse->REASONOED) }}" tabindex="5" maxlength="100" autocomplete="off"  />
                    <span class="text-danger" id="ERROR_REASONOED"></span> 
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
<!-- Alert -->

<div id="userrefpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='userrefpopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>USER</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="user_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
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
          <td class="ROW2" style="width: 40%"  ><input type="text" class="form-control" autocomplete="off" id="user_codesearch" onkeyup="searchUserCode()" /></td>
          <td class="ROW3" style="width: 40%" ><input type="text" class="form-control" autocomplete="off" id="user_namesearch" onkeyup="searchUserName()" /></td>
        </tr>
        </tbody>
      </table>
      
      <table id="user_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($objUserList as $key=>$val)

        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_USERID_REF[]" id="userref_{{ $val->USERID }}" class="clsuserref" value="{{ $val->USERID }}" /></td>
          <td  class="ROW2" style="width: 39%">{{ $val->UCODE }}
          <input type="hidden" id="txtuserref_{{ $val->USERID }}" data-code="{{ $val->UCODE }}" data-desc="{{ $val->DESCRIPTIONS }}" value="{{ $val-> USERID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $val->DESCRIPTIONS }}</td>
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


<div id="rolerefpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='rolerefpopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>ROLE</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="role_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
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
          <td class="ROW2" style="width: 40%"><input type="text" class="form-control" autocomplete="off"  id="role_codesearch" onkeyup="searchRoleCode()" /></td>
          <td class="ROW3" style="width: 40%"><input type="text" class="form-control" autocomplete="off"  id="role_namesearch" onkeyup="searchRoleName()" /></td>
        </tr>
        </tbody>
      </table>
      
      <table id="role_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($objRoleList as $key=>$val)

        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_ROLLID_REF[]" id="roleref_{{ $val->ROLLID }}" class="clsroleref" value="{{ $val->ROLLID }}" /></td>
          <td class="ROW2" style="width: 39%">{{ $val->RCODE }}
          <input type="hidden" id="txtroleref_{{ $val->ROLLID }}" data-code="{{ $val->RCODE }}" data-desc="{{ $val->DESCRIPTIONS }}" value="{{ $val-> ROLLID }}"/>
          </td>
          <td  class="ROW3" style="width: 39%">{{ $val->DESCRIPTIONS }}</td>
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



@endsection
<!-- btnSave -->

@push('bottom-scripts')
<script>
$("#USERID_REF_POPUP").on("click",function(event){ 
  $("#userrefpopup").show();
});

$("#USERID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#userrefpopup").show();
  }
});

$("#userrefpopup_close").on("click",function(event){ 
  $("#userrefpopup").hide();
});

$('.clsuserref').click(function(){
    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texcode =   $("#txt"+id+"").data("code")
    var texdesc =   $("#txt"+id+"").data("desc")

    $("#USERID_REF_POPUP").val(texcode);
    $("#USER_DETAILS").val(texdesc);
    $("#USERID_REF").val(txtval);
    $("#USERID_REF_POPUP").blur(); 
    $("#ROLLID_REF_POPUP").focus(); 
    $("#userrefpopup").hide();

    $("#user_codesearch").val(''); 
    $("#user_namesearch").val(''); 
    searchUserCode();
    event.preventDefault();

});

function searchUserCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("user_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("user_tab2");
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

function searchUserName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("user_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("user_tab2");
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

$("#ROLLID_REF_POPUP").on("click",function(event){ 
  $("#rolerefpopup").show();
});

$("#ROLLID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#rolerefpopup").show();
  }
});

$("#rolerefpopup_close").on("click",function(event){ 
  $("#rolerefpopup").hide();
});

$('.clsroleref').click(function(){
    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texcode =   $("#txt"+id+"").data("code")
    var texdesc =   $("#txt"+id+"").data("desc")

    $("#ROLLID_REF_POPUP").val(texcode);
    $("#ROLL_DETAILS").val(texdesc);
    $("#ROLLID_REF").val(txtval);
    $("#ROLLID_REF_POPUP").blur(); 
    $("#EFDATE").focus(); 
    $("#rolerefpopup").hide();

    $("#role_codesearch").val(''); 
    $("#role_namesearch").val(''); 
    searchRoleCode();
    event.preventDefault();

});

function searchRoleCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("role_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("role_tab2");
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

function searchRoleName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("role_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("role_tab2");
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

"use strict";
	var w3 = {};
  w3.getElements = function (id) {
    if (typeof id == "object") {
      return [id];
    } else {
      return document.querySelectorAll(id);
    }
  };
	w3.sortHTML = function(id, sel, sortvalue) {
    var a, b, i, ii, y, bytt, v1, v2, cc, j;
    a = w3.getElements(id);
    for (i = 0; i < a.length; i++) {
      for (j = 0; j < 2; j++) {
        cc = 0;
        y = 1;
        while (y == 1) {
          y = 0;
          b = a[i].querySelectorAll(sel);
          for (ii = 0; ii < (b.length - 1); ii++) {
            bytt = 0;
            if (sortvalue) {
              v1 = b[ii].querySelector(sortvalue).innerText;
              v2 = b[ii + 1].querySelector(sortvalue).innerText;
            } else {
              v1 = b[ii].innerText;
              v2 = b[ii + 1].innerText;
            }
            v1 = v1.toLowerCase();
            v2 = v2.toLowerCase();
            if ((j == 0 && (v1 > v2)) || (j == 1 && (v1 < v2))) {
              bytt = 1;
              break;
            }
          }
          if (bytt == 1) {
            b[ii].parentNode.insertBefore(b[ii + 1], b[ii]);
            y = 1;
            cc++;
          }
        }
        if (cc > 0) {break;}
      }
    }
  };

  
  let user_tab1 = "#user_tab1";
  let user_tab2 = "#user_tab2";
  let user_headers = document.querySelectorAll(user_tab1 + " th");

  user_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(user_tab2, ".clsuserref", "td:nth-child(" + (i + 1) + ")");
    });
  });

  let role_tab1 = "#role_tab1";
  let role_tab2 = "#role_tab2";
  let role_headers = document.querySelectorAll(role_tab1 + " th");

  role_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(role_tab2, ".clsroleref", "td:nth-child(" + (i + 1) + ")");
    });
  });


$('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[149,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formDataMst = $( "#frm_mst_edit" );
     formDataMst.validate();

     $("#ROLLID_REF_POPUP").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_ROLLID_REF").hide();
        validateSingleElemnet("ROLLID_REF_POPUP");
    });

    $( "#ROLLID_REF_POPUP" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#EFDATE").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_EFDATE").hide();
        validateSingleElemnet("EFDATE");
    });

    $( "#EFDATE" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
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
          validateForm('fnSaveData');
        }

    });//btnSave

    
    //validate and approve
    $("#btnApprove").click(function() {
        
        if(formDataMst.valid()){

          validateForm('fnApproveData');
           
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
            url:'{{route("mastermodify",[149,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.ROLLID_REF){
                        showError('ERROR_ROLLID_REF',data.errors.ROLLID_REF);
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
            url:'{{route("mastermodify",[149,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.ROLLID_REF){
                        showError('ERROR_ROLLID_REF',data.errors.ROLLID_REF);
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
        window.location.href = '{{route("master",[149,"index"]) }}';

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

      $("#USERID_REF").focus();

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
  //$("#ROLLID_REF").focus(); 
});

function validateForm(ActionType){
    $(".showmsg").remove();
    var EFDATE    = $("#EFDATE").val();
    var ENDDATE   = $("#ENDDATE").val();
    var REASONOED = $("#REASONOED").val();
    var EXISTDATE = "<?php echo isset($objResponse->EFDATE) && $objResponse->EFDATE !=''?date('Y-m-d',strtotime($objResponse->EFDATE)):'' ?>";

    if(EXISTDATE !="" &&  validateFromToDate(EXISTDATE,EFDATE) !=true){
        $("#EFDATE").after('<div class="showmsg error">Less date not allow.</div>');
        $("#EFDATE").focus();
        return false;
    }
    else if(ENDDATE !="" &&  validateFromToDate(EFDATE,ENDDATE) !=true){
        $("#ENDDATE").after('<div class="showmsg error">End date should not less from effective date.</div>');
        $("#ENDDATE").focus();
        return false;
    }

    if(ENDDATE !="" && REASONOED ===""){
        $("#REASONOED").after('<div class="showmsg error">Please Enter Reason.</div>');
        $("#REASONOED").focus();
        return false;
    }
    else{
        $("#alert").modal('show');
        $("#AlertMessage").text('Do you want to save to record.');
        $("#YesBtn").data("funcname",ActionType);
        $("#YesBtn").focus();
        highlighFocusBtn('activeYes');
    }

}

function validateFromToDate(FDate,TDate){
    var today = new Date(FDate); 
    var d = new Date(TDate);
    today.setHours(0, 0, 0, 0) ;
    d.setHours(0, 0, 0, 0) ;

    if(d < today){
        return false;
    }
    else {
      return true;
    }
}

function validateFromCurDate(FDate){
    var today = new Date(); 
    var d = new Date(FDate);
    today.setHours(0, 0, 0, 0) ;
    d.setHours(0, 0, 0, 0) ;

    if(d < today){
        return false;
    }
    else {
      return true;
    }
}
</script>


@endpush