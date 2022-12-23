@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[93,'index'])}}" class="btn singlebt">General Ledger Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button id="btnSave"   class="btn topnavbt" tabindex="2"><i class="fa fa-save"></i> Save</button>
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
         <form id="frm_mst_industry" method="POST"  > 
          @CSRF
          {{isset($objResponse->GLID) ? method_field('PUT') : '' }}
          <div class="inner-form">
          
              
              <div class="row">
                  <div class="col-lg-1 pl"><p>GL Code</p></div>
                  <div class="col-lg-2 pl">
                  
                    <label> {{$objResponse->GLCODE}} </label>
                    <input type="hidden" name="GLID" id="GLID" value="{{ $objResponse->GLID }}" />
                    <input type="hidden" name="GLCODE" id="GLCODE" value="{{ $objResponse->GLCODE }}" autocomplete="off"  maxlength="20"   />
                    <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
                  
                </div>

                <div class="col-lg-1 pl col-md-offset-1"><p>Name</p></div>
                  <div class="col-lg-4 pl">
                    <input type="text" name="GLNAME" id="GLNAME" class="form-control mandatory" value="{{ old('GLNAME',$objResponse->GLNAME) }}" maxlength="100" tabindex="2"  />
                    <span class="text-danger" id="ERROR_GLNAME"></span> 
                  </div>

                </div>

                <div class="row">
                  <div class="col-lg-1 pl"><p>Alias</p></div>
                  <div class="col-lg-2 pl">
                    <input type="text" name="ALIAS" id="ALIAS" value="{{ old('ALIAS',$objResponse->ALIAS) }}" class="form-control" autocomplete="off" maxlength="50" tabindex="3" />
                    <span class="text-danger" id="ERROR_ALIAS"></span> 
                  </div>
               
                  <div class="col-lg-1 pl col-md-offset-1"><p>Account Sub Group</p></div>
                  <div class="col-lg-2 pl">

                  <input type="text" name="ASGID_REF_POPUP" id="ASGID_REF_POPUP" class="form-control mandatory" readonly tabindex="4" value="{{$objAsgName->ASGCODE}} - {{$objAsgName->ASGNAME}}" />
                  <input type="hidden" name="ASGID_REF" id="ASGID_REF" value="{{ old('ASGID_REF',$objResponse->ASGID_REF) }}" />
                  <span class="text-danger" id="ERROR_ASGID_REF"></span> 

                  </div>
                </div>
              

              <div class="row">
                <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-1 pl pr">
                <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
                 value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="7"  >
                </div>
                
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="8" placeholder="dd/mm/yyyy"  />
                </div>
             </div>


             <div class="row">
                  <br/>
                </div>
                
                <div class="row">
                  <div class="col-lg-3 pl"><p>Checks Flag</p></div>
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl "><p>Cost Centre Applicable</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="CC" id="CC" class="form-control " tabindex="5" >
                      
                      <option {{$objResponse->CC =="1" ? 'selected="selected"':''}} value="1">Yes</option>
                      <option {{$objResponse->CC =="0" ? 'selected="selected"':''}} value="0">No</option>
                    </select>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Sub Ledger</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="SUBLEDGER" id="SUBLEDGER" class="form-control " tabindex="6" >
                      
                      <option {{$objResponse->SUBLEDGER =="1" ? 'selected="selected"':''}} value="1">Yes</option>
                      <option {{$objResponse->SUBLEDGER =="0" ? 'selected="selected"':''}} value="0">No</option>
                    </select>
                    </div>
                  </div>	
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl"><p>Bank Account</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="BANKAC" id="BANKAC" class="form-control " tabindex="7" >
                      
                      <option {{$objResponse->BANKAC =="1" ? 'selected="selected"':''}} value="1">Yes</option>
                      <option {{$objResponse->BANKAC =="0" ? 'selected="selected"':''}} value="0">No</option>
                    </select>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Belong to GST</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="GST" id="GST" class="form-control " tabindex="8" >
                      
                      <option {{$objResponse->GST =="1" ? 'selected="selected"':''}} value="1">Yes</option>
                      <option {{$objResponse->GST =="0" ? 'selected="selected"':''}} value="0">No</option>
                    </select>
                    </div>
                  </div>	
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl"><p>GST Calculate on this GL</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="GST_ON_THISGL" id="GST_ON_THISGL" class="form-control " tabindex="9" >
                      
                      <option {{$objResponse->GST_ON_THISGL =="1" ? 'selected="selected"':''}} value="1">Yes</option>
                      <option {{$objResponse->GST_ON_THISGL =="0" ? 'selected="selected"':''}} value="0">No</option>
                    </select>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Belong to TDS</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="TDS" id="TDS" class="form-control" tabindex="10" >
                      
                      <option {{$objResponse->TDS =="1" ? 'selected="selected"':''}} value="1">Yes</option>
                      <option {{$objResponse->TDS =="0" ? 'selected="selected"':''}} value="0">No</option>
                    </select>
                    </div>
                  </div>	
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl"><p>Inventory Values are affected</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="IVAFFECTED" id="IVAFFECTED" class="form-control" tabindex="11" >
                      
                      <option {{$objResponse->IVAFFECTED =="1" ? 'selected="selected"':''}} value="1">Yes</option>
                      <option {{$objResponse->IVAFFECTED =="0" ? 'selected="selected"':''}} value="0">No</option>
                    </select>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Interest Calculation</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="ICALCULATION" id="ICALCULATION" class="form-control " tabindex="12" >
                      
                      <option {{$objResponse->ICALCULATION =="1" ? 'selected="selected"':''}} value="1">Yes</option>
                      <option {{$objResponse->ICALCULATION =="0" ? 'selected="selected"':''}} value="0">No</option>
                    </select>
                    </div>
                  </div>	
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl"><p>Use for Payroll</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="UPAYROLL" id="UPAYROLL" class="form-control " tabindex="13" >
                      
                      <option {{$objResponse->UPAYROLL =="1" ? 'selected="selected"':''}} value="1">Yes</option>
                      <option {{$objResponse->UPAYROLL =="0" ? 'selected="selected"':''}} value="0">No</option>
                    </select>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Belong to VAT </p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="VAT" id="VAT" class="form-control " tabindex="14" >
                      
                      <option {{$objResponse->VAT =="1" ? 'selected="selected"':''}} value="1">Yes</option>
                      <option {{$objResponse->VAT =="0" ? 'selected="selected"':''}} value="0">No</option>
                    </select>
                    </div>
                  </div>	
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl "><p>Belong to Service Tax</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="TAX" id="TAX" class="form-control " tabindex="15" >
                      
                      <option {{$objResponse->TAX =="1" ? 'selected="selected"':''}} value="1">Yes</option>
                      <option {{$objResponse->TAX =="0" ? 'selected="selected"':''}} value="0">No</option>
                    </select>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Belong to Sale (Revenue)</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="SALE" id="SALE" class="form-control " tabindex="16"  >
                      
                      <option {{$objResponse->SALE =="1" ? 'selected="selected"':''}} value="1">Yes</option>
                      <option {{$objResponse->SALE =="0" ? 'selected="selected"':''}} value="0">No</option>
                    </select>
                    </div>
                  </div>	
                </div>
                
                <div class="row">
                  <div class="col-lg-2 pl "><p>Belong to Purchase</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="PURCHASE" id="PURCHASE" class="form-control " tabindex="17" >
                      
                      <option {{$objResponse->PURCHASE =="1" ? 'selected="selected"':''}} value="1">Yes</option>
                      <option {{$objResponse->PURCHASE =="0" ? 'selected="selected"':''}} value="0">No</option>
                    </select>
                    </div>
                  </div>	
                  
                  <div class="col-lg-2 pl col-md-offset-2"><p>Belong to TCS</p></div>
                  <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                    <select name="TCS" id="TCS" class="form-control " tabindex="18"  >
                     
                      <option {{$objResponse->TCS =="1" ? 'selected="selected"':''}} value="1">Yes</option>
                      <option {{$objResponse->TCS =="0" ? 'selected="selected"':''}} value="0">No</option>
                    </select>
                    </div>
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


<div id="agrefpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='agrefpopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Account Sub Group</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="ag_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%" >Code</th>
            <th class="ROW3" style="width: 40%" >Name</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
            <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="ag_codesearch" onkeyup="searchGLCode()" /></td>
            <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="ag_namesearch" onkeyup="searchGLName()" /></td>
          </tr>
        </tbody>
      </table>
  
      <table id="ag_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($objAccountSubGroupList as $index=>$AsgList)

        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_ASGID_REF[]" id="agref_{{ $AsgList->ASGID }}" class="clsagref" value="{{ $AsgList->ASGID }}" ></td>
          <td class="ROW2" style="width: 39%">{{ $AsgList->ASGCODE }}
          <input type="hidden" id="txtagref_{{ $AsgList->ASGID }}" data-desc="{{ $AsgList->ASGCODE }} - {{ $AsgList->ASGNAME }}" value="{{ $AsgList-> ASGID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $AsgList->ASGNAME }}</td>
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


$("#ASGID_REF_POPUP").on("click",function(event){ 
  $("#agrefpopup").show();
});

$("#ASGID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#agrefpopup").show();
  }
});

$("#agrefpopup_close").on("click",function(event){ 
  $("#agrefpopup").hide();
});

$('.clsagref').click(function(){
    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texdesc =   $("#txt"+id+"").data("desc")

    $("#ASGID_REF_POPUP").val(texdesc);
    $("#ASGID_REF").val(txtval);
    $("#ASGID_REF_POPUP").blur(); 
    $("#CC").focus(); 
    $("#agrefpopup").hide();

    $("#ag_codesearch").val(''); 
    $("#ag_namesearch").val(''); 
    searchGLCode();
    $(this).prop("checked",false);
    event.preventDefault();

});

function searchGLCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ag_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("ag_tab2");
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

function searchGLName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("ag_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("ag_tab2");
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

  
  let ag_tab1 = "#ag_tab1";
  let ag_tab2 = "#ag_tab2";
  let ag_headers = document.querySelectorAll(ag_tab1 + " th");

  ag_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(ag_tab2, ".clsagref", "td:nth-child(" + (i + 1) + ")");
    });
  });

$('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[93,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formDataMst = $( "#frm_mst_industry" );
     formDataMst.validate();

    $("#GLNAME").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_GLNAME").hide();
        validateSingleElemnet("GLNAME");

    });

    $( "#GLNAME" ).rules( "add", {
        required: true,
        //StringRegex: true,  //from custom.js
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field"
        }
    });

    $("#ASGID_REF").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_ASGID_REF").hide();
        validateSingleElemnet("ASGID_REF");
    });

    $( "#ASGID_REF" ).rules( "add", {
        required: true,
        //StringRegex: true,  //from custom.js
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
      var validator =$("#frm_mst_industry" ).validate();
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

        var getDataForm = $("#frm_mst_industry");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[93,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.GLNAME){
                        showError('ERROR_GLNAME',data.errors.GLNAME);
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
                    $("#frm_mst_industry").trigger("reset");

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

        var getDataForm = $("#frm_mst_industry");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[93,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.GLNAME){
                        showError('ERROR_GLNAME',data.errors.GLNAME);
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
                    $("#frm_mst_industry").trigger("reset");

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
        window.location.href = '{{route("master",[93,"index"]) }}';

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

      $("#GLCODE").focus();

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
  //$("#GLNAME").focus(); 
});
</script>


@endpush