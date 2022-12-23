@extends('layouts.app')
@section('content')

  <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[1,'index'])}}" class="btn singlebt">Calculation Template</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSave" ><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
<div class="container-fluid purchase-order-view filter">     
    <form id="frm_mst_calculation" method="POST"  > 
          @CSRF
    <div class="inner-form">
              
      <div class="row">
        <div class="col-lg-2 pl"><p>Calculation Template Code</p></div>
        <div class="col-lg-2 pl">
          <div class="col-lg-12 pl">
            <input type="text" name="CTCODE" id="txtctcode" value="{{ old('CTCODE') }}" autocomplete="off" tabindex="1" class="form-control mandatory"  maxlength="30" style="text-transform:uppercase"  >
            <span class="text-danger" id="ERROR_CTCODE"></span> 
          </div>
        </div>

        <div class="col-lg-2 pl"><p>Module</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="MODULE_Details" id="MODULE_Details" class="form-control mandatory"  autocomplete="off" readonly/>
            <input type="hidden" name="MODULE" id="MODULE" class="form-control" autocomplete="off" />
            <span class="text-danger" id="ERROR_MODULE"></span> 
        </div>

        <div class="col-lg-2 pl"><p>TYPE</p></div>
        <div class="col-lg-2 pl">
            <select name="TYPE" id="TYPE" class="form-control"  autocomplete="off" >
              <option value='OTHER'>OTHER</option>
              <option value='DISCOUNT'>DISCOUNT</option>
            </select>
           
            <span class="text-danger" id="ERROR_MODULE"></span> 
        </div>
        
    </div>
		
		<div class="row">
			<div class="col-lg-2 pl"><p>Calculation Template Description</p></div>
			<div class="col-lg-6 pl">
				<input type="text" name="CTDESCRIPTION" id="txtctdesc" value="{{ old('CTDESCRIPTION') }}" autocomplete="off" tabindex="2" class="form-control"  maxlength="200" >     
			</div>
		</div>	
				
		<div class="row">
			<div class="col-lg-2 pl"><p>De-Activated</p></div>
			<div class="col-lg-1 pl">
				<input type="checkbox" name="DEACTIVATED" id="deactive" value="{{ old('DEACTIVATED') }}"  tabindex="3" disabled >
			</div>
			
			<div class="col-lg-2 pl col-md-offset-1"><p>Date of De-Activated</p></div>
			<div class="col-lg-2 pl">
			<div class="col-lg-8 pl">
			<input type="date" name="DODEACTIVATED" id="decativateddate" value="{{ old('DODEACTIVATED') }}" tabindex="4"  class="form-control datepicker" placeholder="dd/mm/yyyy" disabled>
			</div>
			</div>
		</div>
		<div class="row">
			<div class="table-responsive table-wrapper-scroll-y " style="height:330px;margin-top:10px;">
			<table id="example2" class="display nowrap table table-striped table-bordered itemlist itemlistscroll w-150" style="height:auto !important;">
				<thead id="thead1" style="position: sticky;top: 0; white-space:none;">
					  <tr>
						<th style="vertical-align:middle; font-family: 'Roboto',sans-serif; font-size:11px; font-weight: 600;">Calculation Component Name <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count">  </th>
						<th width="5%"  style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">SQ No</th>
						<th  style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">Basis</th>
						<th  style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">GL</th>
						<th width="5%"  style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;"> Formula</th>
						<th width="5%"  style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">Rate %</th>
						<th  style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">Formula</th>
						<th width="5%"  style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">Amount</th>
						<th style="white-space:normal;vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">GST Calc on</th>
						<th style="white-space:normal;vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">As per Actual</th>
						<th style="white-space:normal;vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">Landed Cost Included</th>
						<th style="white-space:normal;vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;" width="5%">Action</th>
					  </tr>
					</thead>
					<tbody>
						<tr  class="participantRow">
							<td style="width:10%;"><input  class="form-control" type="text" name="COMPONENT_0" id="txtcmpt_0" maxlength="200"  autocomplete="off" style="text-transform:uppercase" ></td>
							<td style="width:1%;"><input  class="form-control" type="text" name="SQNO_0" id="txtsqno_0" maxlength="4" value="1" readonly  ></td>
              <td style="width:15%;">
              <input name="BASIS_popup_0" id="txtbasis_popup_0" class="form-control selvt" autocomplete="off" readonly />
              </td>
              <td style="width:15%;" hidden>
              <input type="hidden" name="BASIS_0" id="hdnbasis_popup_0" class="form-control" autocomplete="off" />
              </td>
              <td style="width:15%;">
                  <input type="text" name="GLID_popup_0" id="txtgl_popup_0" class="form-control"  autocomplete="off" readonly/>
							</td>
              <td style="width:15%;" hidden>
                  <input type="hidden" name="GLID_REF_0" id="hdngl_popup_0" class="form-control" autocomplete="off" />
							</td>
							<td  style="text-align:center; width:5%;" ><input type="checkbox" name="FORMULAYESNO_0" id="chkfrm_0" class="filter-none" value="" style="float: revert;" ></td>
							<td style="width:5%;"><input  class="form-control four-digits" type="text" name="RATEPERCENTATE_0" id="txtprct_0" maxlength="9" style="text-align: right;" autocomplete="off" ></td>
							<td style="width:15%;"><input  class="form-control" type="text" name="FORMULA_0" id="txtfrm_0" maxlength="200" disabled="disabled" autocomplete="off" ></td>
							<td style="width:10%;"><input  class="form-control two-digits" type="text" name="AMOUNT_0" id="txtamt_0" style="text-align: right;" autocomplete="off" ></td>
							<td style="text-align:center;" ><input type="checkbox" name="GST_0" id="chkgst_0" class="filter-none"  style="float: revert;" ></td>
							<td style="text-align:center;" ><input type="checkbox" name="ACTUAL_0" id="chkact_0" class="filter-none" value=""  style="float: revert;" ></td>
							<td style="text-align:center;" ><input type="checkbox" name="LANDEDCOST_0" id="chklndc_0" class="filter-none" value=""  style="float: revert;" ></td>
							<td align="center" ><button class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
						</tr>
						<tr></tr>
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
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;">
                <div id="alert-active" class="activeOk1"></div>OK</button>
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Alert -->

<div id="MODULE_Modal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='MODULE_Modal_Close' >&times;</button>
      </div>
      <div class="modal-body">
	      <div class="tablename"><p>Module</p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="MODULE_Table" class="display nowrap table  table-striped table-bordered">
            <thead>
              <tr>
                <th class="ROW1">Select</th> 
                <th class="ROW2">Code</th>
                <th class="ROW3">Name</th>
              </tr>
            </thead>
            <tbody>

              <tr>
                <th class="ROW1"><span class="check_th">&#10004;</span></th>
                <td class="ROW2"><input type="text" id="MODULE_Code_Search" class="form-control" autocomplete="off" onkeyup="MODULE_Code_Function()"></td>
                <td class="ROW3"><input type="text" id="MODULE_Name_Search" class="form-control" autocomplete="off" onkeyup="MODULE_Name_Function()"></td>
              </tr>

            </tbody>
            </table>
            <table id="MODULE_Table2" class="display nowrap table  table-striped table-bordered" >
              <thead id="thead2">          
              </thead>
              <tbody id="MODULE_Body1" >
              <?php
              if(!empty($module)){
                foreach ($module as $key=>$val){
                  $checked="";
                  /*
                  if($request['value'] !=""){
                    $checked=   in_array($val-> MODULEID,explode(",",$request['value']))?"checked":'';
                  }
                  else{
                      $checked="";
                  }*/
              ?>
              <tr id="MODULE_TDID_<?php echo $key;?>" class="MODULE_Row">
                  <td class="ROW1" ><input <?php echo $checked;?> type="checkbox" class="MODULE_CHECK" id="txtMODULE_CHECK_<?php echo $key;?>" value="<?php echo $val-> MODULEID;?>"></td>
                  <td class="ROW2" ><?php echo $val-> MODULECODE;?>
                  <input type="hidden" id="txtMODULE_TDID_<?php echo $key;?>" data-desc="<?php echo $val-> MODULECODE;?>"  value="<?php echo $val-> MODULEID;?>"/>
                  </td>
                  <td class="ROW3"><?php echo $val-> MODULENAME;?></td>
              </tr>
              <?php
              }
            }
            ?>

              </tbody>
            </table>
          </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- GLID Dropdown -->
<div id="glidpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='gl_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>GL Account</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="example2345" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%" >GLCode</th>
        <th class="ROW3" style="width: 40%" >GLName</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="glcodesearch" onkeyup="myFunction()" /></td>
      <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="glnamesearch" onkeyup="myNameFunction()"/></td>
    </tr>
    </tbody>
    </table>
      <table id="example23" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <!-- <tr>
            <th>GLCode</th>
            <th>GLName</th>
          </tr> -->
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_fieldid"/>
            <input type="hidden" name="fieldid2" id="hdn_fieldid2"/></td>
          </tr>
        </thead>
        <tbody>
        @foreach ($objglcode as $index=>$glRow)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_GLID_REF[]"  id="glidcode_{{ $index }}" class="clsglid" value="{{ $index  }}" /></td>
          <td class="ROW2" style="width: 39%">{{ $glRow-> GLCODE }}
          <input type="hidden" id="txtglidcode_{{ $index }}" data-desc="{{ $glRow-> GLCODE }}"  value="{{ $glRow->GLID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $glRow-> GLNAME }}</td>
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
<!-- GL Dropdown-->

<!-- Basis Dropdown -->
<div id="basispopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='basis_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Basis</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="basisexample2345" class="display nowrap table  table-striped table-bordered w3-table-all" width="100%">
    <thead>
      <tr>
        <th class="ROW1" style="width: 10%" align="center">Select</th> 
        <th class="ROW2" style="width: 40%" >Code</th>
        <th class="ROW3" style="width: 40%" >Desc</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
      <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="bscodesearch" onkeyup="mybasisFunction()" /></td>
      <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="bsnamesearch" onkeyup="mybasisNameFunction()" /></td>
    </tr>
    </tbody>
    </table>
      <table id="basisexample23" class="display nowrap table  table-striped table-bordered  w3-table-all" width="100%">
        <thead id="thead2">
          <!-- <tr>
            <th>GLCode</th>
            <th>GLName</th>
          </tr> -->
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_basisfieldid"/>
            <input type="hidden" name="fieldid2" id="hdn_basisfieldid2"/></td>
          </tr>
        </thead>
        <tbody>
        {{-- <tr id="basis_0" class="clsbasisid">
          <td width="50%"> A
          <input type="hidden" id="txtbasis_0" data-desc="Item Taxable Amount"  value="Item Taxable Amount"/>
          </td>
          <td>Item Taxable Amount</td>
        </tr>
        <tr id="basis_1" class="clsbasisid">
          <td > B
          <input type="hidden" id="txtbasis_1" data-desc="Item GST Amount"  value="Item GST Amount"/>
          </td>
          <td>Item GST Amount</td>
        </tr>
        <tr id="basis_2" class="clsbasisid">
          <td > C
          <input type="hidden" id="txtbasis_2" data-desc="Amount After GST Item"  value="Amount After GST Item"/>
          </td>
          <td>Amount After GST Item</td>
        </tr>
        <tr id="basis_3" class="clsbasisid">
          <td > D
          <input type="hidden" id="txtbasis_3" data-desc="Individual"  value="Individual"/>
          </td>
          <td>Individual</td>
        </tr> --}}

        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_BASICID_REF[]"  id="basis_0" class="clsbasisid" value="0" /></td>
          <td class="ROW2" style="width: 39%"> A
              <input type="hidden" id="txtbasis_0" data-desc="Item Taxable Amount"  value="Item Taxable Amount"/>
          </td>
          <td class="ROW3" style="width: 39%">Item Taxable Amount</td>
        </tr>
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_BASICID_REF[]"  id="basis_1" class="clsbasisid" value="1" /></td>
          <td class="ROW2" style="width: 39%"> B
              <input type="hidden" id="txtbasis_1" data-desc="Item GST Amount"  value="Item GST Amount"/>
          </td>
          <td class="ROW3" style="width: 39%">Item GST Amount</td>
        </tr>
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_BASICID_REF[]"  id="basis_2" class="clsbasisid" value="2" /></td>
          <td class="ROW2" style="width: 39%"> C
              <input type="hidden" id="txtbasis_2" data-desc="Amount After GST Item"  value="Amount After GST Item"/>
          </td>
          <td class="ROW3" style="width: 39%">Amount After GST Item</td>
        </tr>
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_BASICID_REF[]"  id="basis_3" class="clsbasisid" value="3" /></td>
          <td class="ROW2" style="width: 39%"> D
              <input type="hidden" id="txtbasis_3" data-desc="Individual"  value="Individual"/>
          </td>
          <td class="ROW3" style="width: 39%">Individual</td>
        </tr>
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Basis Dropdown-->

@endsection

@push('bottom-scripts')
<script>
// $(document).ready(function(e){
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

// START MODULE PROGRAM

let MODULE_tid = "#MODULE_Table2";
let MODULE_tid2 = "#MODULE_Table";
let MODULE_headers = document.querySelectorAll(MODULE_tid2 + " th");

MODULE_headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(MODULE_tid, ".MODULE_Row", "td:nth-child(" + (i + 1) + ")");
  });
});

function MODULE_Code_Function() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("MODULE_Code_Search");
  filter = input.value.toUpperCase();
  table = document.getElementById("MODULE_Table2");
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

function MODULE_Name_Function() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("MODULE_Name_Search");
      filter = input.value.toUpperCase();
      table = document.getElementById("MODULE_Table2");
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

$('#MODULE_Details').click(function(event){
  $("#MODULE_Modal").show();
  event.preventDefault();
});

$("#MODULE_Modal_Close").click(function(event){
  $("#MODULE_Modal").hide();
  event.preventDefault();
});


$(".MODULE_CHECK").change(function(){

  var txtval   = [];
  var texdesc  = [];
  
  $('#MODULE_Table2').find('.MODULE_Row').each(function(){
    var text_id     = $.trim($(this).find("[id*=txtMODULE_TDID]").val());
    var text_attr   = $.trim($(this).find("[id*=txtMODULE_TDID]").attr('id'));
    var text_check  = $.trim($(this).find("[id*=txtMODULE_CHECK]").attr('id'));
    var text_des    = $("#"+text_attr).data("desc");

    if($("#"+text_check).prop("checked") == true){
      txtval.push(text_id);
      texdesc.push(text_des);
    }
    
  });

  $('#MODULE_Details').val(texdesc);
  $('#MODULE').val(txtval);

  $("#MODULE_Code_Search").val(''); 
  $("#MODULE_Name_Search").val(''); 
  
  event.preventDefault();

});

// END MODULE PROGRAM


      let tid = "#basisexample23";
      let tid2 = "#basisexample2345";
      let headers = document.querySelectorAll(tid2 + " th");

      // Sort the table element when clicking on the table headers
      headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tid, ".clsbasisid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      let tidgl = "#example23";
      let tidgl2 = "#example2345";
      let headersgl = document.querySelectorAll(tidgl2 + " th");

      // Sort the table element when clicking on the table headers
      headersgl.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tidgl, ".clsglid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      $('#example2').on ("focus","[id*='txtgl_popup']",function(event){
         $("#glidpopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="hdngl_popup"]').attr('id');
        $('#hdn_fieldid').val(id);
        $('#hdn_fieldid2').val(id2);
      });

      $("#gl_closePopup").on("click",function(event){
        $("#glidpopup").hide();
      });

      $(".clsglid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc")
        var txtid= $('#hdn_fieldid').val();
        var txt_id2= $('#hdn_fieldid2').val();
        
        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        $("#glidpopup").hide();
        $("#glcodesearch").val(''); 
        $("#glnamesearch").val(''); 
        $(this).prop("checked",false);
        myFunction();
      });

      $('#example2').on ("focus","[id*='txtbasis_popup']",function(event){
          $("#basispopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="hdnbasis_popup"]').attr('id');
        $('#hdn_basisfieldid').val(id);
        $('#hdn_basisfieldid2').val(id2);        
      });

      $("#basis_closePopup").on("click",function(event){
        $("#basispopup").hide();
      });

      

      $(".clsbasisid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc")
        var txtid= $('#hdn_basisfieldid').val();
        var txt_id2= $('#hdn_basisfieldid2').val();
      
        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        $("#basispopup").hide();
        $("#bscodesearch").val(''); 
        $("#bsnamesearch").val('');
        mybasisFunction();  
        $(this).prop("checked",false);
      });

      //delete row
    $("#example2").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('tbody').length;
    if (rowCount > 1) {
    $(this).closest('tbody').remove();   
    $('[id*="txtsqno"]').each(function(idx, elem){
          $(this).val(idx+1);
    }); 
    } 
    if (rowCount <= 1) { 
    $(document).find('.remove').prop('disabled', false);  
    }
    event.preventDefault();
    });

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
        $clone.find('input:hidden').val('');
        $tr.closest('table').append($clone);         
        var rowCount = $('#Row_Count').val();
		    rowCount = parseInt(rowCount)+1;
        $('#Row_Count').val(rowCount);
        $clone.find('.remove').removeAttr('disabled'); 
        $clone.find('[id*="txtcmpt"]').val('');
        $clone.find('[id*="txtprct"]').val('');
        $clone.find('[id*="txtfrm"]').val('');
        $clone.find('[id*="txtfrm"]').attr('disabled',true);
        $clone.find('[id*="txtamt"]').val('');
        $clone.find('[id*="txtamt"]').removeAttr('disabled');
        $('[id*="txtsqno"]').each(function(idx, elem){
          $clone.find('[id*="txtsqno"]').val(idx+1);
        }); 
        $clone.find('[id*="chkfrm"]').prop('checked', false);
        $clone.find('[id*="chkgst"]').prop('checked', false);
        $clone.find('[id*="chkact"]').prop('checked', false);
        $clone.find('[id*="chklndc"]').prop('checked', false);
        event.preventDefault();
    });

// });
$(document).ready(function(e) {
  var formCalculationMst = $( "#frm_mst_calculation" );
        formCalculationMst.validate();

        $("[id*='txtprct']").ForceNumericOnly();
        $("[id*='txtamt']").ForceNumericOnly();

    $('#Row_Count').val("1");
  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[1,"add"])}}';
                  window.location.href=viewURL;
    });
    $('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });  

    
    
    $('#example2').on("focusout",'[id*="txtprct"]', function( event ) {
        if($(this).val() != '') {
            $(this).parent().parent().find('[id*="txtamt"]').prop('disabled', true);
            $(this).parent().parent().find('[id*="txtamt"]').val('');
            if(intRegex.test($(this).val())){
              $(this).val($(this).val()+'.0000');
            }
        } else {
            $('[id*="txtamt"]').removeAttr('disabled');
        }
    });
    $('#example2').on("focusout",'[id*="txtamt"]', function( event ) {
        if($(this).val() != '') {
            $(this).parent().parent().find('[id*="txtprct"]').prop('disabled', true);
            $(this).parent().parent().find('[id*="txtprct"]').val('');
            if(intRegex.test($(this).val())){
              $(this).val($(this).val()+'.00');
            }
        } else {
            $(this).parent().parent().find('[id*="txtprct"]').removeAttr('disabled');
        }
    });
    $('#example2').on("change",'[id*="chkfrm"]', function( event ) {
            if ($(this).is(':checked') == false) {
                $(this).parent().parent().find('[id*="txtfrm"]').attr('disabled',true);
                $(this).parent().parent().find('[id*="txtfrm"]').val('');
               
                event.preventDefault();
            }
            else
            {
                $(this).parent().parent().find('[id*="txtfrm"]').removeAttr('disabled');
                event.preventDefault();
            }
        });

        var formCalculationMst = $( "#frm_mst_calculation" );
        formCalculationMst.validate();

        //Calculation Template code

        $("#txtctcode").blur(function(){
          $(this).val($.trim( $(this).val()));
          $("#ERROR_CTCODE").hide();
          validateSingleElemnet("txtctcode"); 
        });

        $( "#txtctcode" ).rules( "add", {
          required: true,
          nowhitespace: true,
          StringNumberRegex: true,
          messages: {
              required: "Required field.",
              minlength: jQuery.validator.format("min {0} char")
          }
        });

        $("#MODULE_Details").blur(function(){
          $(this).val($.trim( $(this).val()));
          $("#ERROR_MODULE").hide();
          validateSingleElemnet("MODULE_Details"); 
        });

        $( "#MODULE_Details" ).rules( "add", {
          required: true,
          messages: {
              required: "Required field.",
              minlength: jQuery.validator.format("min {0} char")
          }
        });



        //Calculation Template Rate
         

            

});

    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_calculation" ).validate();
         if(validator.element( "#"+element_id+"" )){
            //check duplicate code
          if(element_id=="txtctcode" || element_id=="CTCODE" ) {
            checkDuplicateCode();
          }

         }
    }

    // //check duplicate Calculation code
    function checkDuplicateCode(){
        
        //validate and save data
        var calculationForm = $("#frm_mst_calculation");
        var formData = calculationForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[1,"codeduplicate"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_CTCODE',data.msg);
                    $("#CTCODE").focus();
                }                                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
    }

    //validate
    $( "#btnSave" ).click(function() {
        var formCalculationMst = $("#frm_mst_calculation");
        if(formCalculationMst.valid()){
                $("#FocusId").val('');
                var CTCODE          =   $.trim($("[id*=txtctcode]").val());
                var MODULE_Details  =   $.trim($("[id*=MODULE_Details]").val());
                if(CTCODE ===""){
                    $("#FocusId").val('CTCODE');
                    $("#ProceedBtn").focus();
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('Please enter value in Calculation Code.');
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;
                }
                else if(MODULE_Details ===""){
                    $("#FocusId").val('MODULE_Details');
                    $("#ProceedBtn").focus();
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('Please select module.');
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    return false;
                }
                else
                {
                    var allblank = [];
                    var allblank2 = [];
                    var allblank3 = [];
                    var allblank4 = [];
                    var allblank5 = [];
                    var allblank6 = [];
                        // $('#udfforsebody').find('.form-control').each(function () {
                          $("[id*=txtcmpt]").each(function(){
                            if($(this).val()!="")
                            {
                                allblank3.push('true');
                                $('.selvt').each(function () {
                                    var d_value = $(this).val();
                                    if(d_value != ""){
                                        allblank.push('true');
                                        if($(this).parent().parent().find('[id*="txtgl_popup"]').val() != "")
                                        {
                                            allblank2.push('true');
                                        }
                                        else{
                                            allblank2.push('false');
                                        }
                                        if($(this).parent().parent().find('[id*="chkfrm"]').is(":checked") != false)
                                        {
                                            if($(this).parent().parent().find('[id*="txtfrm"]').val() != "")
                                            {
                                                allblank4.push('true');
                                                if($(this).parent().parent().find('[id*="txtprct"]').val() != "" && $(this).parent().parent().find('[id*="txtprct"]').val()!='.0000')
                                                {
                                                    allblank5.push('true');
                                                }
                                                else{
                                                    allblank5.push('false');
                                                } 
                                            }
                                            else{
                                                allblank4.push('false');
                                            } 
                                        } 
                                        if($(this).parent().parent().find('[id*="chkfrm"]').is(":checked") == false)
                                        {
                                          if($(this).parent().parent().find('[id*="txtfrm"]').val() == "")
                                            {
                                              if($(this).parent().parent().find('[id*="txtamt"]').val()!='')
                                              {
                                              allblank6.push('true');
                                              }
                                              else
                                              {
                                                if($(this).parent().parent().find('[id*="txtprct"]').val()!='' && $(this).parent().parent().find('[id*="txtprct"]').val()!='.0000')
                                                {
                                                  allblank5.push('true');
                                                }
                                                else
                                                {
                                                  allblank5.push('false');
                                                  allblank6.push('false');
                                                }
                                              }
                                              
                                            }
                                        }
                                    }
                                    else{
                                        allblank.push('false');
                                    } 
                                    
                                    
                                });
                            }
                            else{
                                        allblank3.push('false');
                                    } 
                        });

                        if(jQuery.inArray("false", allblank3) !== -1){
                                $("#alert").modal('show');
                                $("#AlertMessage").text('Please enter Calculation Component Name.');
                                $("#YesBtn").hide(); 
                                $("#NoBtn").hide();  
                                $("#OkBtn1").show();
                                $("#OkBtn1").focus();
                                highlighFocusBtn('activeOk1');
                            }
                            else if(jQuery.inArray("false", allblank) !== -1){
                            $("#alert").modal('show');
                            $("#AlertMessage").text('Please select value in Basis.');
                            $("#YesBtn").hide(); 
                            $("#NoBtn").hide();  
                            $("#OkBtn").hide();
                            $("#OkBtn1").show();
                            $("#OkBtn1").focus();
                            highlighFocusBtn('activeOk1');
                            }
                            else if(jQuery.inArray("false", allblank2) !== -1){
                            $("#alert").modal('show');
                            $("#AlertMessage").text('Please select value in GL Account.');
                            $("#YesBtn").hide(); 
                            $("#NoBtn").hide();  
                            $("#OkBtn").hide();
                            $("#OkBtn1").show();
                            $("#OkBtn1").focus();
                            highlighFocusBtn('activeOk1');
                            }
                            else if(jQuery.inArray("false", allblank4) !== -1){
                            $("#alert").modal('show');
                            $("#AlertMessage").text('Please enter Formula.');
                            $("#YesBtn").hide(); 
                            $("#NoBtn").hide();  
                            $("#OkBtn").hide();
                            $("#OkBtn1").show();
                            $("#OkBtn1").focus();
                            highlighFocusBtn('activeOk1');
                            }
                            else if(jQuery.inArray("false", allblank5) !== -1){
                            $("#alert").modal('show');
                            $("#AlertMessage").text('Please enter Rate value.');
                            $("#YesBtn").hide(); 
                            $("#NoBtn").hide();  
                            $("#OkBtn").hide();
                            $("#OkBtn1").show();
                            $("#OkBtn1").focus();
                            highlighFocusBtn('activeOk1');
                            }
                            else if(jQuery.inArray("false", allblank6) !== -1){
                            $("#alert").modal('show');
                            $("#AlertMessage").text('Please enter Amount.');
                            $("#YesBtn").hide(); 
                            $("#NoBtn").hide();  
                            $("#OkBtn").hide();
                            $("#OkBtn1").show();
                            $("#OkBtn1").focus();
                            highlighFocusBtn('activeOk1');
                            }
                            else{
                                    $("#alert").modal('show');
                                    $("#AlertMessage").text('Do you want to save to record.');
                                    $("#YesBtn").data("funcname","fnSaveData"); 
                                    $("#YesBtn").focus();
                                    $("#OkBtn1").hide();
                                    $("#OkBtn").hide();
                                    highlighFocusBtn('activeYes');
                                }
                }
        }  
    });//btnSaveCalculationTemplate

  
    
    $("#YesBtn").click(function(){

        $("#alert").modal('hide');
        var customFnName = $("#YesBtn").data("funcname");
            window[customFnName]();

    }); //yes button


   window.fnSaveData = function (){

        //validate and save data
        event.preventDefault();

        var calculationForm = $("#frm_mst_calculation");
        var formData = calculationForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[1,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.CTCODE){
                        showError('ERROR_CTCODE',data.errors.CTCODE);
                    }
                    if(data.errors.CTDESCRIPTION){
                        showError('ERROR_CTDESCRIPTION',data.errors.CTDESCRIPTION);
                    }
                   if(data.country=='duplicate') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn1").show();
                      $("#OkBtn").hide();
                      $("#AlertMessage").text(data.msg);
                      $("#alert").modal('show');
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                   }
                   if(data.save=='invalid') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn1").show();
                      $("#OkBtn").hide();
                      $("#AlertMessage").text(data.msg);
                      $("#alert").modal('show');
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
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
                    highlighFocusBtn('activeOk');
                  //  window.location.href='{{ route("master",[4,"index"])}}';
                }
                else{                   
                    console.log("succes MSG="+data.msg);
                    
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#OkBtn").hide();
                    $("#AlertMessage").text(data.msg);
                    $(".text-danger").hide();
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                  //  window.location.href='{{ route("master",[4,"index"])}}';
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
        window.location.href = '{{route("master",[1,"index"]) }}';
        
    });
    
    $("#OkBtn1").click(function(){

      $("#alert").modal('hide');

      $("#YesBtn").show();  //reset
      $("#NoBtn").show();   //reset
      $("#OkBtn").hide();
      $("#OkBtn1").hide();
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
        $("#OkBtn1").hide();
        $("#OkBtn").hide();
        $("#NoBtn").focus();
        highlighFocusBtn('activeNo');
        
    }); ////Undo button

    // Numeric only control handler

    
  


   window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('master',[1,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#txtctcode").focus();
   }//fnUndoNo

   function myFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("glcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("example23");
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

  function myNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("glnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("example23");
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

  function mybasisFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bscodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("basisexample23");
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

  function mybasisNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bsnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("basisexample23");
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
    

</script>

@endpush