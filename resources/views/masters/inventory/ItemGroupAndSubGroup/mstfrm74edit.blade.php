@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[74,'index'])}}" class="btn singlebt">Item Group & Sub Group</a>
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
         <form id="frm_mst_edit" method="POST" onsubmit="return validateForm()" class="needs-validation"  > 
          @CSRF
          {{isset($objResponse->ITEMGID) ? method_field('PUT') : '' }}
          <div class="inner-form">
          
              
              <div class="row">
                  <div class="col-lg-2 pl"><p>Item Group Code</p></div>
                  <div class="col-lg-2 pl">
                  
                    <label> {{$objResponse->GROUPCODE}} </label>
                    <input type="hidden" name="ITEMGID" id="ITEMGID" value="{{ $objResponse->ITEMGID }}" />
                    <input type="hidden" name="GROUPCODE" id="GROUPCODE" value="{{ $objResponse->GROUPCODE }}" autocomplete="off"  maxlength="20"   />
                    <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
                  
                </div>
                </div>

                <div class="row">
                
                  <div class="col-lg-2 pl"><p>Item Group Name</p></div>
                  <div class="col-lg-5 pl">
                    <input type="text" name="GROUPNAME" id="GROUPNAME" class="form-control mandatory" value="{{ old('GROUPNAME',$objResponse->GROUPNAME) }}" maxlength="200" tabindex="1"  />
                    <span class="text-danger" id="ERROR_GROUPNAME"></span> 
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Purchase AC Set</p></div>
                  <div class="col-lg-3 pl">
                    <div class="col-lg-7 pl">
                        <input type="text" {{$objResponse->STATUS =="A" ? 'disabled':''}} name="PUR_AC_SET_POPUP" id="PUR_AC_SET_POPUP" value="{{$PurAccountListName}}" class="form-control mandatory" readonly tabindex="3" required />
                        <input type="hidden" value="{{$PurAccountListID}}" name="PURCHASE_AC_SETID_REF" id="PURCHASE_AC_SETID_REF" />
                        <span class="text-danger" id="ERROR_PAC_SETID_REF"></span>
                    </div>
                  </div>
                  <div class="col-lg-2 pl"><p>Sale AC Set</p></div>
                  <div class="col-lg-3 pl">
                    <div class="col-lg-7 pl">
                        <input type="text" {{$objResponse->STATUS =="A" ? 'disabled':''}} name="SALE_AC_SET_POPUP" id="SALE_AC_SET_POPUP" value="{{$salesAccountListName}}" class="form-control mandatory" readonly tabindex="4" required/>
                        <input type="hidden" name="SALES_AC_SETID_REF" value="{{$salesAccountListID}}" id="SALES_AC_SETID_REF" />
                        <span class="text-danger" id="ERROR_SAC_SETID_REF"></span>
                    </div>
                  </div>
                </div>  

              <div class="row">
                <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-1 pl ">
                <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
                 value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
                </div>
                
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
                </div>
             </div>


             <div class="row">
                  <div class="col-lg-6 pl">
                    <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:300px;" >
                      <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                        <thead id="thead1"  style="position: sticky;top: 0">
                          <tr>
                          <th>
                              Item Sub Group Code 
                              <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count"> 
                              <input type="hidden" id="focusid" >
                              <input type="hidden" id="errorid" >
                              
                          </th>
                          <th>Description</th>
                          <th width="5%">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                        @if(!empty($objDataResponse))
                        @php $n=1; @endphp
                        @foreach($objDataResponse as $key => $row)
                          <tr  class="participantRow">
                              <td><input {{ $row->USED > 0 ?'readonly':''}}  class="form-control w-100" type="text" name={{"ISGCODE_".$key}} id ={{"txtisgcode_".$key}}  value="{{ $row->ISGCODE }}" maxlength="20" autocomplete="off" style="text-transform:uppercase;width:100%;" onkeypress="return AlphaNumaric(event,this)" ></td>
                              <td>
                              <input  class="form-control w-100" {{ $row->USED > 0 ?'readonly':''}} type="text" name={{"DESCRIPTIONS_".$key}} id ={{"txtdesc_".$key}} value="{{ $row->DESCRIPTIONS }}" maxlength="200" autocomplete="off" style="width:100%;" >
                              </td>

                              <td align="center" >
                                  <button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                                  <button class="btn remove" title="Delete" data-toggle="tooltip" {{ $row->USED > 0 ?'disabled':''}}  ><i class="fa fa-trash" ></i></button>
                              </td>
                          </tr>
                          @php $n++; @endphp
                          @endforeach 

                          @else
                          <tr  class="participantRow">
                            <td><input  class="form-control w-100" type="text" name="ISGCODE_0" id ="txtisgcode_0"  maxlength="20" autocomplete="off" style="text-transform:uppercase;width:100%;" onkeypress="return AlphaNumaric(event,this)" ></td>
                              <td><input  class="form-control w-100" type="text" name="DESCRIPTIONS_0" id ="txtdesc_0" maxlength="200" autocomplete="off" style="width:100%;" ></td>

                              <td align="center" >
                                  <button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                                  <button class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                              </td>
                          </tr>
                          @endif     
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
            <button onclick="setfocus();" class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">
              <div id="alert-active" class="activeOk"></div>OK</button>
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Alert -->

{{-- pur ac set --}}
<div id="pur_ac_set_idref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md ">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='pac_idref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Purchase AC Set</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="puracset_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%"><span class="check_th">&#10004;</span></td>
          <td  class="ROW2"  style="width: 40%"><input type="text" id="puracset_codesearch"  class="form-control" autocomplete="off" onkeyup="searchPACCode()" ></td>
          <td  class="ROW3"  style="width: 40%"><input type="text" id="puracset_namesearch"   class="form-control" autocomplete="off" onkeyup="searchPACName()" ></td>
        </tr>
        </tbody>
      </table>


      <table id="puracset_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="country_body">
        @foreach ($getPurchaseAccountList as $index=>$PurchaseAccountList)
        <tr >
          <td class="ROW1" style="width: 12%"> <input type="checkbox" name="SELECT_PACSET_REF[]"  id="pacidref_{{$index}}" class="cls_pacidref" value="{{ $PurchaseAccountList->PR_AC_SETID }}" ></td>
          <td  class="ROW2" style="width: 39%">{{ $PurchaseAccountList->AC_SET_CODE }}
          <input type="hidden" id="txtpacidref_{{$index}}" data-desc="{{ $PurchaseAccountList->AC_SET_CODE }}" data-descname="{{ $PurchaseAccountList->AC_SET_DESC }}" value="{{ $PurchaseAccountList->PR_AC_SETID }}"/>
          </td>
          <td  class="ROW3" style="width: 39%">{{ $PurchaseAccountList->AC_SET_DESC }}</td>
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

{{-- sale ac set --}}

<div id="sale_ac_set_idref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md ">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='sale_idref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sale AC Set</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="saleacset_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%"><span class="check_th">&#10004;</span></td>
          <td  class="ROW2"  style="width: 40%"><input type="text" id="saleacset_codesearch"  class="form-control" autocomplete="off" onkeyup="searchSALECode()" ></td>
          <td  class="ROW3"  style="width: 40%"><input type="text" id="saleacset_namesearch"   class="form-control" autocomplete="off" onkeyup="searchSALEName()" ></td>
        </tr>
        </tbody>
      </table>

      <table id="saleacset_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="sale_body">
        @foreach ($getSalesAccountList as $index=>$SalesAccountList)
        <tr >
          <td class="ROW1" style="width: 12%"> <input type="checkbox" name="SELECT_SALESET_REF[]"  id="saleidref_{{$index}}" class="cls_saleidref" value="{{ $SalesAccountList->SL_AC_SETID }}" ></td>
          <td  class="ROW2" style="width: 39%">{{ $SalesAccountList->AC_SET_CODE }}
          <input type="hidden" id="txtsaleidref_{{$index}}" data-desc="{{ $SalesAccountList->AC_SET_CODE }}" data-descname="{{ $SalesAccountList->AC_SET_DESC }}" value="{{ $SalesAccountList->SL_AC_SETID }}"/>
          </td>
          <td  class="ROW3" style="width: 39%">{{ $SalesAccountList->AC_SET_DESC }}</td>
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

///--------------------------

$("#PUR_AC_SET_POPUP").on("click",function(event){ 
  $("#pur_ac_set_idref_popup").show();
});

$("#PUR_AC_SET_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#pur_ac_set_idref_popup").show();
  }
});

$("#pac_idref_close").on("click",function(event){ 
  $("#pur_ac_set_idref_popup").hide();
});

$('.cls_pacidref').click(function(){
  var id          =   $(this).attr('id');
  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  texdesc = texdesc+"-"+texdescname;

  $("#PUR_AC_SET_POPUP").val(texdesc);
  $("#PURCHASE_AC_SETID_REF").val(txtval);

  $("#PUR_AC_SET_POPUP").blur(); 
  $("#puracset_codesearch").val(''); 
  $("#puracset_namesearch").val(''); 
  
  $("#pur_ac_set_idref_popup").hide();
  searchPACCode();

  event.preventDefault();
});

function searchPACCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("puracset_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("puracset_tab2");
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

function searchPACName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("puracset_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("puracset_tab2");
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


$("#SALE_AC_SET_POPUP").on("click",function(event){ 
  $("#sale_ac_set_idref_popup").show();
});

$("#SALE_AC_SET_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#pur_ac_set_idref_popup").show();
  }
});

$("#sale_idref_close").on("click",function(event){ 
  $("#sale_ac_set_idref_popup").hide();
});

$('.cls_saleidref').click(function(){
  var id          =   $(this).attr('id');
  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  texdesc = texdesc+"-"+texdescname;

  $("#SALE_AC_SET_POPUP").val(texdesc);
  $("#SALES_AC_SETID_REF").val(txtval);

  $("#SALE_AC_SET_POPUP").blur(); 
  $("#saleacset_codesearch").val(''); 
  $("#saleacset_namesearch").val(''); 
  
  $("#sale_ac_set_idref_popup").hide();

  searchSALECode();

  event.preventDefault();
});

function searchSALECode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("saleacset_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("saleacset_tab2");
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

function searchSALEName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("saleacset_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("saleacset_tab2");
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

  let puracset_tab1 = "#puracset_tab1";
  let puracset_tab2 = "#puracset_tab2";
  let puracset_headers = document.querySelectorAll(puracset_tab1 + " th");

  puracset_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(puracset_tab2, ".cls_pacidref", "td:nth-child(" + (i + 1) + ")");
    });
  });

  let saleacset_tab1 = "#saleacset_tab1";
  let saleacset_tab2 = "#saleacset_tab2";
  let saleacset_headers = document.querySelectorAll(puracset_tab1 + " th");

  saleacset_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(saleacset_tab2, ".cls_saleidref", "td:nth-child(" + (i + 1) + ")");
    });
  });

///--------------------------
function setfocus(){
    var focusid=$("#focusid").val();
    $("#"+focusid).focus();
    $("#closePopup").click();
} 

function validateForm(UserAction){

    $("#focusid").val('');
    $("#errorid").val('');
    var txtisgcode  =   $.trim($("[id*=txtisgcode]").val());
    var txtdesc     =   $.trim($("[id*=txtdesc]").val());

    if(txtisgcode ==="" && txtdesc !=""){
        $("#focusid").val('txtisgcode_0');
        $("#errorid").val('1');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text('Please enter item sub group.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        return false;
    }
    else if(txtisgcode !="" && txtdesc ===""){
        $("#focusid").val('txtdesc_0');
        $("#errorid").val('1');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text('Please enter description.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        return false;
    }
    else{
        event.preventDefault();

        var ExistArray = []; 
        var allblank1 = [];  
        var allblank2 = []; 
        var allblank3 = []; 
        var texid1    = "";
        var texid2    = ""; 
        var texid3    = "";

        $("[id*=txtisgcode]").each(function(){
 
            if($.trim($(this).val()) ==="" && $.trim($(this).parent().parent().find('[id*="txtdesc"]').val()) != "" ){
              allblank1.push('true');
              texid1 = $(this).attr('id');
            }
            else if($.trim($(this).val()) !="" && $.trim($(this).parent().parent().find('[id*="txtdesc"]').val()) === "" ){
              allblank2.push('true');
              texid2 = $(this).parent().parent().find('[id*="txtdesc"]').attr('id');
            }
            else if (ExistArray.indexOf($.trim($(this).val())) > -1) {
              allblank3.push('true');
              texid3 = $(this).attr('id');
            }
            else{
              allblank1.push('false');
              allblank2.push('false');
              allblank3.push('false');
            }

            ExistArray.push($.trim($(this).val()));
           
        });

        if(jQuery.inArray("true", allblank1) !== -1){
            $("#errorid").val('1');
            $("#focusid").val(texid1);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Please enter item sub group code.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else if(jQuery.inArray("true", allblank2) !== -1){
            $("#errorid").val('1');
            $("#focusid").val(texid2);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Please enter description.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else if(jQuery.inArray("true", allblank3) !== -1){
            $("#errorid").val('1');
            $("#focusid").val(texid3);
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Duplicate data.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else{
              $("#errorid").val('');
              $("#alert").modal('show');
              $("#AlertMessage").text('Do you want to save to record.');
              $("#YesBtn").data("funcname",UserAction);  
              $("#YesBtn").focus();
              highlighFocusBtn('activeYes');
          }

    }
  
}

$(document).ready(function(e) {

    var rcount = <?php echo json_encode($objCount); ?>;

    $('#Row_Count').val(rcount);

    $("#example2").on('click', '.add', function() {
        var $tr = $(this).closest('tbody');
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


        $clone.find('input:hidden').val('');
        $clone.find('input:text').val('');
        $tr.closest('table').append($clone);
        var rowCount = $('#Row_Count').val();
        rowCount = parseInt(rowCount)+1;
        $('#Row_Count').val(rowCount);
        
        $clone.find('.remove').removeAttr('disabled'); 
        $clone.find('[id*="txtisgcode"]').removeAttr('readonly'); 
        $clone.find('[id*="txtdesc"]').removeAttr('readonly'); 
        $clone.find('[id*="txtdesc"]').val('');

        /*
        $clone.find('[id*="txtID"]').val('0'); 
        $clone.find('[id*="chkmdtry"]').prop("checked", false); 
        $clone.find('[id*="deactive-checkbox"]').prop("checked", false); 
        */
        event.preventDefault();

    });

    $("#example2").on('click', '.remove', function() {

        var rowCount = $('#Row_Count').val();
        if (rowCount > 1) {
            $(this).closest('.participantRow').remove(); 
        } 
        if (rowCount <= 1) { 
            $(document).find('.remove').prop('disabled', true);  
        }

        event.preventDefault();

    });

});


$('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[74,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formDataMst = $( "#frm_mst_edit" );
     formDataMst.validate();

    $("#GROUPNAME").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_GROUPNAME").hide();
        validateSingleElemnet("GROUPNAME");

    });
    $("#GROUPNAME").keydown(function(){
       
        $("#ERROR_GROUPNAME").hide();
        validateSingleElemnet("GROUPNAME");

    });

    $( "#GROUPNAME" ).rules( "add", {
        required: true,
        //StringRegex: true,  //from custom.js
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field"
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
            url:'{{route("mastermodify",[74,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.GROUPNAME){
                        showError('ERROR_GROUPNAME',data.errors.GROUPNAME);
                    }
                   if(data.exist=='norecord') {
                      $("#errorid").val('1'); 
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

                   }
                   if(data.save=='invalid') {
                      $("#errorid").val('1'); 
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
            url:'{{route("mastermodify",[74,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.GROUPNAME){
                        showError('ERROR_GROUPNAME',data.errors.GROUPNAME);
                    }
                   if(data.exist=='norecord') {
                      $("#errorid").val('1'); 
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

                   }
                   if(data.save=='invalid') {
                      $("#errorid").val('1'); 
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

        if($("#errorid").val() ===""){
            window.location.href = '{{route("master",[74,"index"]) }}';
        }

        //window.location.href = '{{route("master",[74,"index"]) }}';

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

      $("#GROUPCODE").focus();

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
  //$("#GROUPNAME").focus(); 
});
</script>


@endpush