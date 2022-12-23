@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[74,'index'])}}" class="btn singlebt">Item Group & Sub Group</a>
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
                  <div class="col-lg-2 pl"><p>Item Group Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">
                        {{-- <input type="text" name="GROUPCODE" id="GROUPCODE" value="{{ old('GROUPCODE') }}" class="form-control mandatory" autocomplete="off" maxlength="10" tabindex="1" style="text-transform:uppercase" onkeypress="return AlphaNumaric(event,this)" /> --}}
                        @if(!empty($objDD))
                            @if($objDD->SYSTEM_GRSR == "1")
                                <input type="text" name="GROUPCODE" id="GROUPCODE" value="{{ $objDOCNO }}" class="form-control mandatory" tabindex="1" maxlength="10" autocomplete="off" readonly style="text-transform:uppercase" autofocus >
                            @endif
                            @if($objDD->MANUAL_SR == "1")
                                <input type="text" name="GROUPCODE" id="GROUPCODE" value="{{ old('GROUPCODE') }}" class="form-control mandatory"  maxlength="{{$objDD->MANUAL_MAXLENGTH}}" tabindex="1" autocomplete="off" style="text-transform:uppercase" autofocus >
                            @endif
                        @endif 
                        <span class="text-danger" id="ERROR_GROUPCODE"></span> 
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Item Group Name</p></div>
                  <div class="col-lg-5 pl">
                    <input type="text" name="GROUPNAME" id="GROUPNAME" class="form-control mandatory" value="{{ old('GROUPNAME') }}" maxlength="100" tabindex="2"  />
                    <span class="text-danger" id="ERROR_GROUPNAME"></span> 
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Purchase AC Set</p></div>
                  <div class="col-lg-3 pl">
                    <div class="col-lg-7 pl">
                        <input type="text" name="PUR_AC_SET_POPUP" id="PUR_AC_SET_POPUP" class="form-control mandatory" readonly tabindex="3" required />
                        <input type="hidden" name="PURCHASE_AC_SETID_REF" id="PURCHASE_AC_SETID_REF" />
                        <span class="text-danger" id="ERROR_PAC_SETID_REF"></span>
                    </div>
                  </div>
                  <div class="col-lg-2 pl"><p>Sale AC Set</p></div>
                  <div class="col-lg-3 pl">
                    <div class="col-lg-7 pl">
                        <input type="text" name="SALE_AC_SET_POPUP" id="SALE_AC_SET_POPUP" class="form-control mandatory" readonly tabindex="4" required/>
                        <input type="hidden" name="SALES_AC_SETID_REF" id="SALES_AC_SETID_REF" />
                        <span class="text-danger" id="ERROR_SAC_SETID_REF"></span>
                    </div>
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
                          </th>
                          <th>Description</th>
                          <th width="5%">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr  class="participantRow">
                            <td><input  class="form-control w-100" type="text" name="ISGCODE_0" id ="txtisgcode_0"  maxlength="20" autocomplete="off" style="text-transform:uppercase;width:100%;" onkeypress="return AlphaNumaric(event,this)" ></td>
                              <td><input  class="form-control w-100" type="text" name="DESCRIPTIONS_0" id ="txtdesc_0" maxlength="200" autocomplete="off" style="width:100%;" ></td>

                              <td align="center" >
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
        </form>
    </div><!--purchase-order-view-->
@endsection
@section('alert')
<!-- Alert -->
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
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


///---------------------

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

///---------------------


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

function validateForm(){

    $("#focusid").val('');
    var txtisgcode  =   $.trim($("[id*=txtisgcode]").val());
    var txtdesc     =   $.trim($("[id*=txtdesc]").val());
    
    $("#OkBtn1").hide(); 

    var GROUPCODE          =   $.trim($("#GROUPCODE").val());
      if(GROUPCODE ===""){
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").hide();  
        $("#OkBtn").show();              
        $("#AlertMessage").text('Please enter Item Group Code.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        return false;
      }
    else if(txtisgcode ==="" && txtdesc !=""){
        $("#focusid").val('txtisgcode_0');
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
              $("#alert").modal('show');
              $("#AlertMessage").text('Do you want to save to record.');
              $("#YesBtn").data("funcname","fnSaveData");  
              $("#YesBtn").focus();
              highlighFocusBtn('activeYes');
          }

    }
  
}

$(document).ready(function(e) {

    $('#Row_Count').val("1");
  
    $("#example2").on('click', '.add', function() {
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('tbody').last();
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
        $tr.closest('table').append($clone);         
        var rowCount = $('#Row_Count').val();
		    rowCount = parseInt(rowCount)+1;
        $('#Row_Count').val(rowCount);
        $clone.find('.remove').removeAttr('disabled'); 
        $clone.find('[id*="txtdesc"]').val('');
        $clone.find('[id*="chkmdtry"]').prop('checked', false);

        event.preventDefault();
    });

    $("#example2").on('click', '.remove', function() {

        var rowCount = $('#Row_Count').val();

        if (rowCount > 1) {
            $(this).closest('tbody').remove();     
        } 
        
        if (rowCount <= 1) { 
            $(document).find('.remove').prop('disabled', false);  
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

 var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();

    $("#GROUPCODE").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_GROUPCODE").hide();
      validateSingleElemnet("GROUPCODE");
         
    });

    $( "#GROUPCODE" ).rules( "add", {
        required: true,
        nowhitespace: true,
        //StringNumberRegex: true, //from custom.js
        messages: {
            required: "Required field.",
        }
    });

    $("#GROUPNAME").blur(function(){
        $(this).val($.trim( $(this).val() ));
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
            required: "Required field."
        }
    });

    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_add" ).validate();
         if(validator.element( "#"+element_id+"" )){
            //check duplicate code
          if(element_id=="GROUPCODE" || element_id=="groupcode" ) {
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
            url:'{{route("master",[74,"codeduplicate"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_GROUPCODE',data.msg);
                    $("#GROUPCODE").focus();
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
        $("#OkBtn1").hide();

        var getDataForm = $("#frm_mst_add");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[74,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.GROUPCODE){
                        //showError('ERROR_GROUPCODE',data.errors.GROUPCODE);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").hide();
                        $("#OkBtn").show();
                        $("#AlertMessage").text("Item Group Code is "+data.errors.GROUPCODE);
                        $("#alert").modal('show');
                        $("#OkBtn").focus();
                    }
                    if(data.errors.GROUPNAME){
                        //showError('ERROR_GROUPNAME',data.errors.GROUPNAME);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").hide();
                        $("#OkBtn").show();
                        $("#AlertMessage").text("Item Group Name is "+data.errors.GROUPNAME);
                        $("#alert").modal('show');
                        $("#OkBtn").focus();
                    }
                   if(data.exist=='duplicate') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();
                      $("#OkBtn1").hide();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

                   }
                   if(data.save=='invalid') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();
                      $("#OkBtn1").hide();

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

                  //  window.location.href='{{ route("master",[74,"index"])}}';
                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
      
   } // fnSaveData



    
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
    window.location.href = "{{route('master',[74,'index'])}}";

    }); 


    
    $("#OkBtn").click(function(){
      $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('master',[74,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#GROUPCODE").focus();
   }//fnUndoNo


    function showError(pId,pVal){

      $("#"+pId+"").text(pVal);
      $("#"+pId+"").show();

    }//showError

    function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }

    $(function() { 
      $("#GROUPCODE").focus();  
    });

    window.onload = function(){
      var strdd = <?php echo json_encode($objDD); ?>;
      if($.trim(strdd)==""){     
        $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").show();
          $("#OkBtn1").hide();
          $("#AlertMessage").text('Please contact to administrator for creating document numbering.');
          $("#alert").modal('show');
          $("#OkBtn").focus();
          highlighFocusBtn('activeOk');
      } 
    };

    
    
</script>

@endpush