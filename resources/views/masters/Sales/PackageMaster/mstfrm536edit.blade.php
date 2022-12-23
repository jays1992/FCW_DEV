@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Package Master</a></div>
    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
      <button class="btn topnavbt" id="btnSaveFormData" onclick="saveAction('update')" ><i class="fa fa-floppy-o"></i> Save</button>
      <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
      <button style="display:none" class="btn topnavbt buttonload_approve" > <i class="fa fa-refresh fa-spin"></i> {{Session::get('approve')}}</button>
      <button class="btn topnavbt" id="btnApprove" onclick="saveAction('approve')" {{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}} ><i class="fa fa-thumbs-o-up"></i> Approved</button>
      <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
      <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>
 
<form id="master_form" method="POST"  >
  <div class="container-fluid filter">    
    @csrf
    <div class="inner-form">         
      <div class="row">
        <div class="col-lg-1 pl"><p>Document No</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="hidden"  name="DOC_ID"     id="DOC_ID" value="{{isset($HDR->PKMID)?$HDR->PKMID:''}}" >
          <input {{$ActionStatus}} type="text"    name="DOC_NO"  id="DOC_NO"  value="{{isset($HDR->PKMCODE)?$HDR->PKMCODE:''}}"  class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
        </div>

        <div class="col-lg-1 pl"><p>Document Date*</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="date" name="DOC_DATE" id="DOC_DATE" value="{{isset($HDR->PKMDATE)?$HDR->PKMDATE:''}}"  class="form-control" autocomplete="off" placeholder="dd/mm/yyyy" readonly >
        </div>

        <div class="col-lg-1 pl"><p>Business Unit*</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text"   name="BUNAME" id="BUNAME" value="{{isset($HDR->BUNAME)?$HDR->BUNAME:''}}"  class="form-control"  autocomplete="off"  onclick="getBusinessUnitMaster()" readonly/>
          <input {{$ActionStatus}} type="hidden" name="BUID_REF" id="BUID_REF" value="{{isset($HDR->BUID_REF)?$HDR->BUID_REF:''}}"      class="form-control"  autocomplete="off" />  
        </div>

        <div class="col-lg-1 pl"><p>Package Name*</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text"   name="PACKAGE_NAME" id="PACKAGE_NAME" value="{{isset($HDR->PKMNAME)?$HDR->PKMNAME:''}}"  class="form-control"  autocomplete="off" />
        </div>
      </div>

      <div class="row">	
        <div class="col-lg-1 pl"><p>Package Description</p></div>
        <div class="col-lg-11 pl">
          <textarea {{$ActionStatus}} name="PACKAGE_DESC" id="PACKAGE_DESC" cols="118" rows="10" tabindex="3" >{{isset($HDR->DESCRIPTIONS)?$HDR->DESCRIPTIONS:''}}</textarea>
        </div>
      </div>  

      <br/>

      <div class="row">
        <div class="col-lg-1 pl"><p>SAC Code*</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text"   name="HSN_DESC" id="HSN_DESC" value="{{isset($HDR->HSN_DESC)?$HDR->HSN_DESC:''}}" class="form-control"  autocomplete="off"  onclick="getSacMaster()" readonly/>
          <input {{$ActionStatus}} type="hidden" name="HSNID_REF" id="HSNID_REF"  value="{{isset($HDR->HSNID_REF)?$HDR->HSNID_REF:''}}"      class="form-control"  autocomplete="off" />  
        </div>
      </div>

      <div class="row">
        <div class="col-lg-1 pl"><p>De-Activated</p></div>
        <div class="col-lg-2 pl pr">
          <input {{$ActionStatus}} type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{isset($HDR->DEACTIVATED) && $HDR->DEACTIVATED == 1 ? "checked" : ""}} value='{{isset($HDR->DEACTIVATED) && $HDR->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
        </div>
        
        <div class="col-lg-1 pl"><p>Date of De-Activated</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{isset($HDR->DEACTIVATED) && $HDR->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($HDR->DODEACTIVATED) && $HDR->DODEACTIVATED !="" && $HDR->DODEACTIVATED !="1900-01-01" ? $HDR->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
        </div>
      </div>

    </div>

    <div class="container-fluid purchase-order-view">
      <div class="row">
        <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#Material" id="MAT_TAB">Details</a></li>
          <li><a data-toggle="tab" href="#udf" id="UDF_TAB">UDF</a></li>
        </ul>
                                            
        <div class="tab-content">

        <div id="Material" class="tab-pane fade in active">
					  <div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;" >
						  <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
							  <thead id="thead1"  style="position: sticky;top: 0">
								  <tr>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>UOM</th>
                    <th>Qty</th>
                    <th>Action</th>
								  </tr>
							  </thead>
							  <tbody>
                  @if(isset($DETAILS) && !empty($DETAILS))
                  @foreach($DETAILS as $key=>$row)
								  <tr class="participantRow">
                    <td><input {{$ActionStatus}}  type="text" name="popupITEMID[]" id="popupITEMID_{{$key}}" value="{{isset($row->ITEM_CODE)?$row->ITEM_CODE:''}}" onclick="getItem(this.id)" class="form-control"  autocomplete="off"  readonly/></td>
                    <td hidden><input {{$ActionStatus}} type="hidden" name="ITEMID_REF[]" id="ITEMID_REF_{{$key}}" value="{{isset($row->ITEMID_REF)?$row->ITEMID_REF:''}}" class="form-control" autocomplete="off" /></td>
                    <td><input {{$ActionStatus}} type="text" name="ItemName[]" id="ItemName_{{$key}}" value="{{isset($row->ITEM_NAME)?$row->ITEM_NAME:''}}" class="form-control"  autocomplete="off"  readonly  /></td>
                    <td><input {{$ActionStatus}} type="text" name="popupMUOM[]" id="popupMUOM_{{$key}}" value="{{isset($row->UOM_DESC)?$row->UOM_DESC:''}}" onclick="getUomMaster(this.id)" class="form-control"  autocomplete="off"  readonly /></td>
                    <td hidden><input {{$ActionStatus}} type="hidden" name="MAIN_UOMID_REF[]" id="MAIN_UOMID_REF_{{$key}}" value="{{isset($row->UOMID_REF)?$row->UOMID_REF:''}}" class="form-control"  autocomplete="off" /></td>
                    <td><input type="text" {{$ActionStatus}}   name="QTY[]" id="QTY_{{$key}}" value="{{isset($row->QUANTITY)?$row->QUANTITY:''}}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off"   /></td>
                    
                    <td align="center" >
                      <button {{$ActionStatus}} class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                      <button {{$ActionStatus}} class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                    </td>

								  </tr>
                  @endforeach
                  @endif
							  </tbody>
					    </table>
					  </div>	
				  </div>

          <div id="udf" class="tab-pane fade">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
              <table id="example4" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0">
                  <tr>
                    <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3" value="{{count($objUdf)}}"></th>
                    <th>Value / Comments</th>
                  </tr>
                </thead>                         
                <tbody>
                  @foreach($objUdf as $udfkey => $udfrow)
                  <tr  class="participantRow4">
                    <td><input name={{"udffie_popup_".$udfkey}} id={{"txtudffie_popup_".$udfkey}} value="{{$udfrow->LABEL}}" class="form-control @if ($udfrow->ISMANDATORY==1) mandatory @endif" autocomplete="off" maxlength="100" disabled/></td>
                    <td hidden><input type="text" name='{{"udffie_".$udfkey}}' id='{{"hdnudffie_popup_".$udfkey}}' value="{{$udfrow->UDFID}}" class="form-control" maxlength="100" /></td>
                    <td hidden><input type="text" name={{"udffieismandatory_".$udfkey}} id={{"udffieismandatory_".$udfkey}} class="form-control" maxlength="100" value="{{$udfrow->ISMANDATORY}}" /></td>            
                    <td id="{{"tdinputid_".$udfkey}}">
                    @php
                    $dynamicid  = "udfvalue_".$udfkey;
                    $chkvaltype = strtolower($udfrow->VALUETYPE); 
                    $udf_value  = isset($udfrow->UDF_VALUE)?$udfrow->UDF_VALUE:'';
  
                    if($chkvaltype=='date'){
                      $strinp = '<input '.$ActionStatus.' type="date" placeholder="dd/mm/yyyy" name="'.$dynamicid.'" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control" value="" /> ';       
                    }
                    else if($chkvaltype=='time'){
                      $strinp= '<input '.$ActionStatus.' type="time" placeholder="h:i" name="'.$dynamicid.'" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control"  value=""/> ';
                    }
                    else if($chkvaltype=='numeric'){
                      $strinp = '<input '.$ActionStatus.' type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control" value=""  autocomplete="off" /> ';
                    }
                    else if($chkvaltype=='text'){
                      $strinp = '<input '.$ActionStatus.' type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control" value=""  autocomplete="off" /> ';
                    }
                    else if($chkvaltype=='boolean'){

                      $boolval = ''; 
                      if($udf_value =='on' || $udf_value  =='1'){
                        $boolval="checked";
                      }

                      $strinp = '<input '.$ActionStatus.' type="checkbox" name="'.$dynamicid. '" id="'.$dynamicid.'"  '.$boolval.' class=""  /> ';
                    }
                    else if($chkvaltype=='combobox'){
                      $strinp       ='';
                      $txtoptscombo = strtoupper($udfrow->DESCRIPTIONS); ;
                      $strarray     = explode(',',$txtoptscombo);
                      $opts         = '';
                      $chked        ='';

                      for ($i = 0; $i < count($strarray); $i++) {
                        $chked='';
                        if($strarray[$i]==$udf_value){
                          $chked='selected="selected"';
                        }

                        $opts = $opts.'<option value="'.$strarray[$i].'" '.$chked.'  >'.$strarray[$i].'</option> ';
                      }

                      $strinp = '<select '.$ActionStatus.' name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" >'.$opts.'</select>' ;
                    }
                    echo $strinp;
                    @endphp
                    </td>
                  </tr>
                  @endforeach                             
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</form>
@endsection
@section('alert')
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
          <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData"><div id="alert-active" class="activeYes"></div>Yes</button>
          <button class="btn alertbt" name='NoBtn' id="NoBtn"   data-funcname="fnUndoNo" ><div id="alert-active" class="activeNo"></div>No</button>
          <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk"></div>OK</button>
          <button class="btn alertbt" name='OkBtn1' id="OkBtn1" onclick="getFocus()" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk1"></div>OK</button>
          <input type="hidden" id="FocusId" >
        </div>
		  <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="modal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:50%;" >
    <div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" onclick="closeEvent('modal')" >&times;</button></div>
      <div class="modal-body">
	      <div class="tablename"><p id='modal_title'></p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="modal_table1" class="display nowrap table  table-striped table-bordered" >
            <thead>
              <tr>
                <th style="width:10%;">Select</th> 
                <th style="width:45%;" id='modal_th1'></th>
                <th style="width:45%;" id='modal_th2'></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th style="width:10%;"></th>
                <td style="width:45%;"><input type="text" id="text1" class="form-control" autocomplete="off" onkeyup="searchData(1)"></td>
                <td style="width:45%;"><input type="text" id="text2" class="form-control" autocomplete="off" onkeyup="searchData(2)"></td>
              </tr>
            </tbody>
          </table>

          <table id="modal_table2" class="display nowrap table  table-striped table-bordered" >
            <tbody id="modal_body" style="font-size:14px;"></tbody>
          </table>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:80%">
    <div class="modal-content" >
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button></div>
      <div class="modal-body">
	      <div class="tablename"><p>Item Details</p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar filter">
          <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead>
              <tr id="none-select" class="searchalldata" hidden>
                <td> 
                  <input type="hidden" id="hdn_ItemID"/>
                </td>
              </tr>

              <tr>
                <th style="width:10%;" id="all-check">Select</th>
                <th style="width:20%;">Item Code</th>
                <th style="width:20%;">Name</th>
                <th style="width:10%;">Main UOM</th>
                <th style="width:10%;">Rate</th>
                <th style="width:10%;">Item Group</th>
                <th style="width:10%;">Item Category</th>
                <th style="width:10%;">Business Unit</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th style="width:10%;"></th>
                <td style="width:20%;"><input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
                <td style="width:20%;"><input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
                <td style="width:10%;"><input type="text" id="ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
                <td style="width:10%;"><input type="text" id="ItemQTYsearch" class="form-control" autocomplete="off" readonly></td>
                <td style="width:10%;"><input type="text" id="ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
                <td style="width:10%;"><input type="text" id="ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
                <td style="width:10%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
              </tr>
            </tbody>
          </table>

          <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead id="thead2"></thead>
            <tbody id="tbody_ItemID" style="font-size: 13px">
              <div class="loader" style="display:none;"></div>
            </tbody>
          </table>

        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('bottom-scripts')
<script>
$(document).ready(function() {
  CKEDITOR.replace( 'PACKAGE_DESC' );
});
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

let tid1    = "#modal_table1";
let tid2    = "#modal_table2";
let headers = document.querySelectorAll(tid1 + " th");

headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(tid2, ".clsipoid", "td:nth-child(" + (i + 1) + ")");
  });
});

function searchData(cno){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById('text'+cno);
  filter = input.value.toUpperCase();
  table = document.getElementById("modal_table2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[cno];
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

function closeEvent(id){
  $("#"+id).hide();
}

function getBusinessUnitMaster(){
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
    url:'{{route("master",[$FormId,"getBusinessUnitMaster"])}}',
    type:'POST',
    success:function(data) {
      var html = '';

      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr>';
          html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindBusinessUnitMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" ></td>';
          html +='<td style="width:45%;" >'+value.DATA_CODE+'</td>';
          html +='<td style="width:45%;" >'+value.DATA_DESC+'</td>';
          html +='</tr>';
        });
      }
      else{
        html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
      }

      $("#modal_body").html(html);
    },
    error: function (request, status, error) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
      $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
    },
  });

  $("#modal_title").text('Business Unit');
  $("#modal_th1").text('Code');
  $("#modal_th2").text('Description');
  $("#modal").show();
}

function bindBusinessUnitMaster(data){
  var code  = $("#"+data.id).data("code");
  var desc  = $("#"+data.id).data("desc");

  $("#BUID_REF").val(data.value);
  $("#BUNAME").val(code+' - '+desc);
  
  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  $("#modal").hide(); 
}

function getSacMaster(){
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
    url:'{{route("master",[$FormId,"getSacMaster"])}}',
    type:'POST',
    success:function(data) {
      var html = '';

      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr>';
          html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindSacMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" ></td>';
          html +='<td style="width:45%;" >'+value.DATA_CODE+'</td>';
          html +='<td style="width:45%;" >'+value.DATA_DESC+'</td>';
          html +='</tr>';
        });
      }
      else{
        html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
      }

      $("#modal_body").html(html);
    },
    error: function (request, status, error) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
      $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
    },
  });

  $("#modal_title").text('SAC Master');
  $("#modal_th1").text('Code');
  $("#modal_th2").text('Description');
  $("#modal").show();
}

function bindSacMaster(data){
  var code  = $("#"+data.id).data("code");
  var desc  = $("#"+data.id).data("desc");

  $("#HSNID_REF").val(data.value);
  $("#HSN_DESC").val(code+' - '+desc);
  
  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  $("#modal").hide(); 
}

function getItem(id){
  loadItem();
  $('#hdn_ItemID').val(id.split('_').pop());  
  $('.js-selectall1').prop('checked',false); 
  $("#ITEMIDpopup").show();
}

function searchItem(e) {
  if(e.which == 13){
    loadItem()
  }
}

function loadItem(){
    var taxstate    = ''; 
    var CODE        = $.trim($("#Itemcodesearch").val()); 
    var NAME        = $.trim($("#Itemnamesearch").val()); 
    var MUOM        = $.trim($("#ItemUOMsearch").val()); 
    var GROUP       = $.trim($("#ItemGroupsearch").val()); 
    var CTGRY       = $.trim($("#ItemCategorysearch").val()); 
    var BUNIT       = $.trim($("#ItemBUsearch").val()); 
    var APART       = ''; 
    var CPART       = ''; 
    var OPART       = ''; 
  
  $("#tbody_ItemID").html('<tr><td colspan="11">Please wait your request is under process ...</td></tr>');

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("master",[$FormId,"loadItem"])}}',
    type:'POST',
    data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART},
    success:function(data) {
      var html = '';

      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr class="clsitemid">';
          html +='<td style="width:10%;"><input type="checkbox" id="chkId'+key+'"  value="'+key+'" class="js-selectall1"  ></td>';
          html +='<td style="width:20%;">'+value.ICODE+'</td>';
          html +='<td style="width:20%;" id="itemname_'+key+'" >'+value.INAME+'</td>';
          html +='<td style="width:10%;" id="itemuom_'+key+'" >'+value.UOMCODE+'</td>';
          html +='<td style="width:10%;" id="uomqty_'+key+'" >'+value.STDCOST+'</td>';
          html +='<td style="width:10%;" id="irate_'+key+'">'+value.GROUPCODE+'</td>';
          html +='<td style="width:10%;" id="itax_'+key+'">'+value.ICCODE+'</td>';
          html +='<td style="width:10%;">'+value.BUCODE+'</td>';
         
          html +='<td hidden>';
          html +='<input type="text" id="uniquerowid_'+key+'" value='+value.ITEMID+' >';
          html +='<input type="text" id="txt_item_id_'+key+'" value='+value.ITEMID+' >';
          html +='<input type="text" id="txt_item_code_'+key+'" value='+value.ICODE+' >';
          html +='<input type="text" id="txt_item_name_'+key+'" value='+value.INAME+' >';
          html +='<input type="text" id="txt_main_uom_id_'+key+'" value='+value.MAIN_UOMID_REF+' >';
          html +='<input type="text" id="txt_main_uom_code_'+key+'" value='+value.UOMCODE+' >';
          html +='<input type="text" id="txt_item_rate_'+key+'" value='+value.STDCOST+' >';
          html +='</td>';

          html +='</tr>';
        });
      }
      else{
        html +='<tr><td colspan="8"> Record not found.</td></tr>'; 
      }

      $("#tbody_ItemID").html(html);
      bindItemEvents(); 
    },
    error: function (request, status, error) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
      $("#tbody_ItemID").html('');                        
    },
  });
}

$("#ITEMID_closePopup").click(function(event){
  $("#ITEMIDpopup").hide();
  resetItemPopup();
});

function bindItemEvents(){
  $('#ItemIDTable2').off(); 
  $('[id*="chkId"]').change(function(){

    var index         = $(this).val();
    var item_id       = $("#txt_item_id_"+index).val();
    var item_code     = $("#txt_item_code_"+index).val();
    var item_name     = $("#txt_item_name_"+index).val();
    var main_uom_id   = $("#txt_main_uom_id_"+index).val();
    var main_uom_code = $("#txt_main_uom_code_"+index).val();
    var item_rate     = $("#txt_item_rate_"+index).val();
    var uniquerowid   = $("#uniquerowid_"+index).val();
    var row_id        = $('#hdn_ItemID').val();

    if($(this).is(":checked") == true){

      var checkExist  = false;
      $('#example2').find('.participantRow').each(function(){
        if(uniquerowid == $(this).find('[id*="ITEMID_REF_"]').val()){
          checkExist  = true;
        }            
      });

      if(checkExist ==true){
        $("#ITEMIDpopup").hide();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Item already exists.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk1');
        $('#hdn_ItemID').val('');
        return false;  
      }                            
      else{
        $('#ITEMID_REF_'+row_id).val(item_id);
        $('#popupITEMID_'+row_id).val(item_code);
        $('#ItemName_'+row_id).val(item_name);
        $('#MAIN_UOMID_REF_'+row_id).val(main_uom_id);
        $('#popupMUOM_'+row_id).val(main_uom_code);
      }

      $('#hdn_ItemID').val('');
      $("#ITEMIDpopup").hide();
      
    }

  });
  resetItemPopup();
}

function resetItemPopup(){
  $("#Itemcodesearch").val(''); 
  $("#Itemnamesearch").val(''); 
  $("#ItemUOMsearch").val(''); 
  $("#ItemQTYsearch").val(''); 
  $("#ItemGroupsearch").val(''); 
  $("#ItemCategorysearch").val(''); 
  $("#ItemBUsearch").val(''); 
  $('.remove').removeAttr('disabled'); 
}

$("#Material").on('click', '.remove', function(){
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

$("#Material").on('click', '.add', function(){
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
  $clone.find('.remove').removeAttr('disabled'); 
  event.preventDefault();
});

function getUomMaster(textid){

  var row_no      = textid.split('_').pop();
  var ITEMID_REF  = $.trim($("#ITEMID_REF_"+row_no).val());

  if(ITEMID_REF ===""){
    $("#FocusId").val('popupITEMID_'+row_no);        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select item.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else{

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      url:'{{route("master",[$FormId,"getUomMaster"])}}',
      type:'POST',
      data:{ITEMID_REF:ITEMID_REF},
      success:function(data) {
        var html = '';

        if(data.length > 0){
          $.each(data, function(key, value) {

            html +='<tr>';
            html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindUomMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'"  data-textid="'+textid+'" ></td>';
            html +='<td style="width:45%;" >'+value.DATA_CODE+'</td>';
            html +='<td style="width:45%;" >'+value.DATA_DESC+'</td>';
            html +='</tr>';

          });
        }
        else{
          html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
        }

        $("#modal_body").html(html);
      },
      error: function (request, status, error) {
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(request.responseText);
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
      },
    });

    $("#modal_title").text('UOM Master');
    $("#modal_th1").text('Code');
    $("#modal_th2").text('Description');
    $("#modal").show();

  }
}

function bindUomMaster(data){
  
  var textid          = $("#"+data.id).data("textid");
  var textid          = textid.split('_').pop();
  var code            = $("#"+data.id).data("code");
  var desc            = $("#"+data.id).data("desc");
 
  $("#MAIN_UOMID_REF_"+textid).val(data.value);
  $("#popupMUOM_"+textid).val(code,' - ',desc);
 
  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  $("#modal").hide(); 
}

function saveAction(action){
  validateForm(action);
}

function validateForm(action){

  for ( instance in CKEDITOR.instances ) {
    CKEDITOR.instances.PACKAGE_DESC.updateElement();
  }

  var flag_exist    = [];
  var flag_status   = [];
  var flag_focus    = '';
  var flag_message  = '';
  var flag_tab_type = '';

  $("[id*=txtudffie_popup]").each(function(){
    if($.trim($(this).val())!=""){
      if($.trim($(this).parent().parent().find('[id*="udffieismandatory"]').val()) == "1"){
        if($.trim($(this).parent().parent().find('[id*="udfvalue"]').val()) != ""){
          flag_status.push('true');
        }
        else{
          flag_status.push('false');
          flag_focus    = $(this).parent().parent().find('[id*="udfvalue"]').attr('id');
          flag_message  = 'Please enter  Value / Comment in UDF Tab';
          flag_tab_type = 'UDF_TAB';
        }
      }             
    }             
  });

  var input = document.getElementsByName('ITEMID_REF[]');
  for (var i = 0; i < input.length; i++) {

    var ITEMID_REF = $.trim(document.getElementsByName('ITEMID_REF[]')[i].value);
  
    if(ITEMID_REF ===""){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('popupITEMID[]')[i].id;
      flag_message  = 'Please select item';
      flag_tab_type = 'MAT_TAB';
    }
    else if($.trim(document.getElementsByName('MAIN_UOMID_REF[]')[i].value) ===""){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('popupMUOM[]')[i].id;
      flag_message  = 'Please select uom';
      flag_tab_type = 'MAT_TAB';
    }
    else if($.trim(document.getElementsByName('QTY[]')[i].value) ===""){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('QTY[]')[i].id;
      flag_message  = 'Please enter qty';
      flag_tab_type = 'MAT_TAB';
    }
    else if(jQuery.inArray(ITEMID_REF, flag_exist) !== -1){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('ITEMID_REF[]')[i].id;
      flag_message  = 'This item is already exist';
      flag_tab_type = 'MAT_TAB';
    }

    flag_exist.push(ITEMID_REF);

  }

  if($.trim($("#DOC_NO").val()) ===""){
    $("#FocusId").val('DOC_NO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter document no.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#DOC_DATE").val()) ===""){
    $("#FocusId").val('DOC_DATE');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select document date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#BUID_REF").val()) ===""){
    $("#FocusId").val('BUNAME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select business unit.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#PACKAGE_NAME").val()) ===""){
    $("#FocusId").val('PACKAGE_NAME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter package name.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }  
  else if($.trim($("#HSNID_REF").val()) ===""){
    $("#FocusId").val('HSN_DESC');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select sac code.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($("#deactive-checkbox_0").is(":checked") == true && $.trim($("#DODEACTIVATED").val()) ===""){
    $("#FocusId").val('DODEACTIVATED');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Date of De-Activated.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else if(jQuery.inArray("false", flag_status) !== -1){
    $("#"+flag_tab_type).click();
    $("#FocusId").val(flag_focus);        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text(flag_message);
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }   
  else{
    $("#alert").modal('show');
    $("#AlertMessage").text('Do you want to '+action+' to record.');
    $("#YesBtn").data("funcname","fnSaveData");
    $("#YesBtn").data("action",action);
    $("#OkBtn1").hide();
    $("#OkBtn").hide();
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#YesBtn").focus();
    highlighFocusBtn('activeYes');
  }
}

$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName  = $("#YesBtn").data("funcname");
  var action        = $("#YesBtn").data("action");

  if(action ==="save"){
    window[customFnName]('{{route("master",[$FormId,"save"])}}');
  }
  else if(action ==="update"){
    window[customFnName]('{{route("master",[$FormId,"update"])}}');
  }
  else if(action ==="approve"){
    window[customFnName]('{{route("master",[$FormId,"Approve"])}}');
  }
  else{
    window.location.href = '{{route("master",[$FormId,"index"]) }}';
  }
});

window.fnSaveData = function (path){

  event.preventDefault();
  var trnsoForm = $("#master_form");
  var formData = trnsoForm.serialize();

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $("#btnSaveFormData").hide(); 
  $(".buttonload").show(); 
  $("#btnApprove").prop("disabled", true);

  $.ajax({
    url:path,
    type:'POST',
    data:formData,
    success:function(data) {
      $(".buttonload").hide(); 
      $("#btnSaveFormData").show();   
      $("#btnApprove").prop("disabled", false);
       
      if(data.success){                   
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(data.msg);
        $(".text-danger").hide();
        $("#alert").modal('show');
        $("#OkBtn").focus();
      }
      else{                   
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(data.msg);
        $(".text-danger").hide();
        $("#alert").modal('show');
        $("#OkBtn1").focus();
      } 
    },
    error: function (request, status, error){
      $(".buttonload").hide(); 
      $("#btnSaveFormData").show();   
      $("#btnApprove").prop("disabled", false);
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
    },
  });
}

$("#NoBtn").click(function(){
  $("#alert").modal('hide');
});

$("#OkBtn").click(function(){
  $("#alert").modal('hide');
  $("#YesBtn").show();
  $("#NoBtn").show();
  $("#OkBtn").hide();
  $(".text-danger").hide();
  window.location.href = '{{route("master",[$FormId,"index"]) }}';
});

$("#OkBtn1").click(function(){
  $("#alert").modal('hide');
  $("#YesBtn").show();
  $("#NoBtn").show();
  $("#OkBtn").hide();
  $("#OkBtn1").hide();
  $("#"+$(this).data('focusname')).focus();
  $(".text-danger").hide();
});

function showError(pId,pVal){
  $("#"+pId+"").text(pVal);
  $("#"+pId+"").show();
}
function getFocus(){
  var FocusId=$("#FocusId").val();
  $("#"+FocusId).focus();
  $("#closePopup").click();
}

function highlighFocusBtn(pclass){
  $(".activeYes").hide();
  $(".activeNo").hide();  
  $("."+pclass+"").show();
}

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}

$(function (){
	var today             = new Date(); 
  var dodeactived_date  = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
  $('#DODEACTIVATED').attr('min',dodeactived_date);

	$('input[type=checkbox][name=DEACTIVATED]').change(function(){
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
</script>
@endpush