
@extends('layouts.app')
@section('content')
    
<div class="container-fluid topnav">
  <div class="row">
      <div class="col-lg-2">
      <a href="{{route('master',[390,'index'])}}" class="btn singlebt">Leave Opening Balance</a>
      </div><!--col-2-->

      <div class="col-lg-10 topnav-pd" id="btndiv">
              <button class="btn topnavbt" id="btnAdd"  {{isset($objRights->ADD) && $objRights->ADD != 1 ? 'disabled' : ''}} ><i class="fa fa-plus"></i> Add</button>
              <button class="btn topnavbt" id="btnEdit" {{isset($objRights->EDIT) && $objRights->EDIT != 1 ? 'disabled' : ''}} ><i class="fa fa-edit"></i> Edit</button>
              <button class="btn topnavbt" id="btnSave" disabled="disabled"><i class="fa fa-save"></i> Save</button>
              <button class="btn topnavbt" id="btnView" {{isset($objRights->VIEW) && $objRights->VIEW != 1 ? 'disabled' : ''}} ><i class="fa fa-eye"></i> View</button>
              <button class="btn topnavbt" id="btnPrint" ><i class="fa fa-print"></i> Print</button>
              <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
              <button class="btn topnavbt" id="btnCancel" {{isset($objRights->CANCEL) && $objRights->CANCEL != 1 ? 'disabled' : ''}} ><i class="fa fa-times"></i> Cancel</button>
              <button class="btn topnavbt" id="btnApprove" {{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}} ><i class="fa fa-lock"></i> Approved</button>
              <button class="btn topnavbt"  id="btnAttach" {{isset($objRights->ATTECHMENT) && $objRights->ATTECHMENT != 1 ? 'disabled' : ''}} ><i class="fa fa-link"></i> Attachment</button>
              <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
      </div><!--col-10-->
  </div><!--row-->
</div><!--topnav-->	
<!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->

<div class="container-fluid purchase-order-view">
  <form id="frm_mst_edit" method="POST"  > 
    @CSRF
    {{isset($objResponse->ETYPEID) ? method_field('PUT') : '' }}
        <div class="container-fluid filter">
                <div class="inner-form">
                    <div class="row">
                        <div class="col-lg-2 pl"><p>LOB No*</p></div>
                        <div class="col-lg-2 pl">
                          @if(isset($objSON->SYSTEM_GRSR) && $objSON->SYSTEM_GRSR == "1")
                          <input type="text" name="LOB_NO" id="LOB_NO" value="{{ isset($objAutoGenNo)?$objAutoGenNo:'' }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
                      @elseif(isset($objSON->MANUAL_SR) && $objSON->MANUAL_SR == "1")
                          <input type="text" name="LOB_NO" id="LOB_NO" value="{{ old('LOB_NO') }}" class="form-control mandatory" maxlength="{{isset($objSON->MANUAL_MAXLENGTH)?$objSON->MANUAL_MAXLENGTH:''}}" autocomplete="off" style="text-transform:uppercase"  >
                      @else
                          <input type="text" name="LOB_NO" id="LOB_NO" value="{{ isset($HDR->LOB_NO)?$HDR->LOB_NO:''}}"  class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
                          @endif
                        </div>
                        <div class="col-lg-2 pl"><p>LOB Date*</p></div>
                        <div class="col-lg-2 pl">
                            <input {{$InputStatus}} type="date" name="LOB_DT" id="LOB_DT" value="{{ isset($HDR->LOB_DT)?$HDR->LOB_DT:''}}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
                            <td hidden><input {{$InputStatus}} type="hidden" name="LEAVE_OPBLID" id="LEAVE_OPBLID" value="{{$HDR->LEAVE_OPBLID}}" class="form-control" autocomplete="off" /></td>
                          </div>                              
                        <div class="col-lg-2 pl"><p>Financial Year*</p></div>
                        <div class="col-lg-2 pl">
                          <select {{$InputStatus}} name="FYID_REF" id="FYID_REF" class="form-control mandatory" tabindex="4">
                            @if(isset($objYearList) && !empty($objYearList))
                            @foreach($objYearList as $YearList)
                            <option {{isset($HDR->YRCODE) && $HDR->YRCODE ==$YearList->YRID?'selected="selected"':''}} value="{{$YearList->YRCODE}}">{{$YearList->YRCODE}}</option>
                            @endforeach
                            @endif
                          </select>
                          <span class="text-danger" id="ERROR_FYID_REF"></span>                             
                        </div>
                    </div>           
                   
                    {{-- <div class="row">
                        <div class="col-lg-2 pl"><p>Description </p></div>
                        <div class="col-lg-4 pl">
                            <input {{$InputStatus}} type="text" name="REMARKS" id="REMARKS" class="form-control" maxlength="200"  autocomplete="off"   />
                        </div>
                    </div> --}}
                </div>

                <div class="container-fluid purchase-order-view">

                    <div class="row">
                        <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#Material"  id="MAT_TAB" >Material</a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="Material" class="tab-pane fade in active">                                
                                <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                                    <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                                        <thead id="thead1"  style="position: sticky;top: 0">
                                            <tr>
                                                <th>Employee Code<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
                                                <th>Name</th>
                                                <th>Designation</th>
                                                <th>Department</th>
                                                <th>Leave Type Code</th>
                                                <th>Description</th>
                                                <th>Opening Balance</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        //dd($objMAT);

                                        ?>
                                          @if(isset($objMAT) && !empty($objMAT))
                                            @foreach($objMAT as $key => $row)
                                              
                                            <tr  class="participantRow">
                                                <td><input {{$InputStatus}} type="text" name="popupEMPLYID_{{$key}}" id="popupEMPLYID_{{$key}}" value="{{$row->EMPCODE}}" class="form-control"  autocomplete="off"  readonly/></td>
                                                <td hidden><input {{$InputStatus}} type="hidden" name="EMPLY_REF_{{$key}}" id="EMPLY_REF_{{$key}}" value="{{$row->EMPID_REF}}" class="form-control" autocomplete="off" /></td>
                                                <td hidden><input {{$InputStatus}} type="hidden" name="LEAVE_OPBLID_REF_{{$key}}" id="LEAVE_OPBLID_REF_{{$key}}" value="{{$row->LEAVE_OPBLID_REF}}" class="form-control" autocomplete="off" /></td>
                                                <td hidden><input {{$InputStatus}} type="hidden" name="EMPID_REF_{{$key}}" id="EMPID_REF_{{$key}}" value="{{$row->EMPID_REF}}" class="form-control" autocomplete="off" /></td>
                                                <td hidden><input {{$InputStatus}} type="hidden" name="LTID_REF_{{$key}}" id="LTID_REF_{{$key}}" value="{{$row->LTID_REF}}" class="form-control" autocomplete="off" /></td>
                                                <td><input {{$InputStatus}} type="text" name="EMPLYSPECI_{{$key}}" id="EMPLYSPECI_{{$key}}" value="{{$row->FNAME}}" class="form-control"  autocomplete="off"  readonly/></td>
                                                <td><input {{$InputStatus}} type="text" name="EMPLYDES_{{$key}}" id="EMPLYDES_{{$key}}"  value="{{$row->DESGCODE}}" class="form-control"  autocomplete="off"  readonly/></td>
                                                <td hidden><input {{$InputStatus}} type="hidden" name="UOMID_REF_{{$key}}" id="UOMID_REF_{{$key}}" value="{{$row->EMPCODE}}" class="form-control"  autocomplete="off" /></td>
                                                
                                                <td><input {{$InputStatus}} type="text" name="EmplyName_{{$key}}" id="EmplyName_{{$key}}" value="{{$row->DCODE}}" class="form-control" maxlength="200" autocomplete="off" readonly /></td>
                                                <td hidden><input {{$InputStatus}} type="hidden" name="DEPID_REF_{{$key}}" id="DEPID_REF_{{$key}}" value="{{$row->EMPCODE}}" class="form-control"  autocomplete="off" /></td>
                                                
                                                <td><input {{$InputStatus}} name="leavetypecode_popup_{{$key}}" id="leavetypecode_popup_{{$key}}" value="{{$row->LEAVETYPE_CODE}}" class="form-control" autocomplete="off"  readonly/></td>
                                                <td hidden><input {{$InputStatus}} type="text" name="hdnleavetypecode_popup_{{$key}}" id="hdnleavetypecode_popup_{{$key}}" value="{{$row->LTID}}" class="form-control" /> </td>

                                                <td><input {{$InputStatus}} class="form-control w-100" type="text" name="leavetypedesciption_{{$key}}" id="txtleavetypedesciption_{{$key}}" value="{{$row->LEAVETYPE_DESC}}"  maxlength="50" readonly></td>
                                                                                                    
                                                <td hidden><input {{$InputStatus}} type="hidden" name="RATEPUOM_{{$key}}" id="RATEPUOM_{{$key}}" value="{{$row->EMPCODE}}" class="form-control" maxlength="13"  autocomplete="off" /></td>
                                                <td><input {{$InputStatus}} type="text" name="OPNGBALANCE_{{$key}}" id="OPNGBALANCE_{{$key}}" value="{{$row->LEAVE_OPBL}}" class="form-control" maxlength="13"  autocomplete="off" /></td>

                                                <td align="center" ><button {{$InputStatus}} class="btn add material" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                                                  <button {{$InputStatus}} class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                                            
                                            </tr>
                                            <tr></tr>
                                            @endforeach 
                                            @endif
                                        </tbody>
                                </table>
                                </div>	
                            </div>                                
                            
                        </div>
                    </div>
                </div>
            </div>
    
</div><!--purchase-order-view-->

<!-- </div> -->
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
        <button onclick="getFocus()" class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk1"></div>OK</button>
        <input type="hidden" id="FocusId" >
    </div>
    <div class="cl"></div>
  </div>
</div>
</div>
</div>

<!-- Alert -->

<!-- leavetypecode Dropdown -->
<div id="leavetypecodepopup" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md">
<div class="modal-content">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" id='leavetypecode_closePopup' >&times;</button>
  </div>
<div class="modal-body">
<div class="tablename"><p>Leave Type Code</p></div>
<div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
  <table id="leavetypecodetable2" class="display nowrap table  table-striped table-bordered" width="100%">
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
      <td class="ROW2"  style="width: 40%">
          <input type="text" autocomplete="off"  class="form-control"  id="leavetype2codesearch" onkeyup="leavetype2Function()" />
      </td>
      <td class="ROW3"  style="width: 40%">
          <input type="text" autocomplete="off"  class="form-control"  id="leavetype2namesearch" onkeyup="leavetype2NameFunction()" />
      </td>
    </tr>
    </tbody>
    </table>  
<table id="leavetypecodetable" class="display nowrap table  table-striped table-bordered" width="100%">
  <thead id="thead2">
    <tr id="none-select" class="searchalldata">
        
      <td hidden> 
          <input type="hidden" name="fieldid" id="hdn_leavetypecodefieldid"/>
          <input type="hidden" name="fieldid2" id="hdn_leavetypecodefieldid2"/>
          <input type="hidden" name="fieldid3" id="hdn_leavetypecodefieldid3"/>
    </td>
    </tr>
    <!-- <tr>
      <th>Code</th>
      <th>Name</th>
    </tr> -->
    <tr hidden>
      <td><input type="text" id="leavetypecode_search" > </td>
      <td><input type="text" ></td>
    </tr>
  </thead>
  <tbody>
  @foreach ($ObjMstleavetype  as $index=>$Rows)
  <tr >
    <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_ATTID_REF[]"  id="leavetypecode_{{ $Rows->LTID }}" class="clsleavetypecode" value="{{ $Rows->LTID }}" /></td>
    <td class="ROW2" style="width: 39%">{{ $Rows->LEAVETYPE_CODE }}
      <input type="hidden" id="txtleavetypecode_{{ $Rows->LTID }}" data-desc="{{ $Rows->LEAVETYPE_CODE }}" value="{{ $Rows->LTID }}" data-leavetypedesc="{{ $Rows->LEAVETYPE_DESC }}" value="{{ $Rows->LEAVETYPE_DESC }}"  />
    </td>
    <td class="ROW3" style="width: 39%">{{ $Rows->LEAVETYPE_DESC }}</td>
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
<!-- leavetypecode Dropdown-->

<!-- Employee Code-->

<div id="EMPLYIDpopup" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md" style="width:90%;">
<div class="modal-content">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" id='EMPLYID_closePopup' >&times;</button>
  </div>
<div class="modal-body">
<div class="tablename"><p>Employee Details</p></div>
<div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
<table id="EmplyIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
<thead>
  <tr id="none-select" class="searchalldata" hidden>
        
        <td> <input type="hidden" name="fieldid" id="hdn_EmplyID"/>
        <input type="hidden" name="fieldid2" id="hdn_EmplyID2"/>
        <input type="hidden" name="fieldid3" id="hdn_EmplyID3"/>
        <input type="hidden" name="fieldid4" id="hdn_EmplyID4"/>
        <input type="hidden" name="fieldid5" id="hdn_EmplyID5"/>
        </td>
  </tr>
  <!-- <tr class="topnav"><button class="btn topnavbt" id="btnOk"><i class="fa fa-check"></i> OK</button></tr> -->
  <tr>
        <th style="width:8%;text-align:center;" id="all-check">Select</th>
        <th style="width:10%;">Employee Code</th>
        <th style="width:10%;">Name</th>
        <th style="width:8%;">Designation</th>
        <th style="width:8%;">Department</th>
        <th style="width:8%;">Status</th>
  </tr>
</thead>
<tbody>
  <tr>
    <td style="width:8%;text-align:center;"><span class="check_th">&#10004;</span></td>
    <td style="width:10%;"><input type="text" id="emplycodesearch" class="form-control" onkeyup="emplycodesearch()"></td>
    <td style="width:10%;"><input type="text" id="emplynamesearch" class="form-control" onkeyup="emplynamesearch()"></td>
    <td style="width:8%;"><input type="text" id="emplydessearch" class="form-control" onkeyup="emplydessearch()"></td>
    <td style="width:8%;"><input type="text" id="emplydepsearch" class="form-control" onkeyup="emplydepsearch()"></td>
    <td style="width:8%;"><input type="text" id="emplyStatussearch" class="form-control" onkeyup="emplyStatussearch()"></td>
  </tr>                   
</tbody>
</table>
  <table id="EmplyIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
    <thead id="thead2">

    </thead>
    <tbody id="tbody_EmplyID">     
      
      
    </tbody>
  </table>
</div>
<div class="cl"></div>
  </div>
</div>
</div>
</div>
<!-- Employee Code-->

@endsection


@push('bottom-css')
<style>
#EmplyIDcodesearch {
background-image: url('/css/searchicon.png');
background-position: 10px 10px;
background-repeat: no-repeat;
font-size: 11px;
padding: 5px 5px 5px 5px;
border: 1px solid #ddd;
margin-bottom: 5px;
}
#EmplyIDnamesearch {
background-image: url('/css/searchicon.png');
background-position: 10px 10px;
background-repeat: no-repeat;
font-size: 11px;
padding: 5px 5px 5px 5px;
border: 1px solid #ddd;
margin-bottom: 5px;
}

#EmplyIDTable {
border-collapse: collapse;
width: 950px;
border: 1px solid #ddd;
font-size: 11px;
}

#EmplyIDTable th {
text-align: center;
padding: 5px;

font-size: 11px;

color: #0f69cc;
font-weight: 600;
}

#EmplyIDTable td {
text-align: center;
padding: 5px;
font-size: 11px;

font-weight: 600;
}

#EmplyIDTable2 {
border-collapse: collapse;
width: 1050px;
border: 1px solid #ddd;
font-size: 11px;
}

#EmplyIDTable2 th{
text-align: left;
padding: 5px;

font-size: 11px;

color: #0f69cc;
font-weight: 600;
}

#EmplyIDTable2 td {
text-align: left;
padding: 5px;
font-size: 11px;

font-weight: 600;
width: 16%;
}

</style>
@endpush
@push('bottom-scripts')
<script>

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

function leavetype2Function() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("leavetype2codesearch");
    filter = input.value.toUpperCase();
    table = document.getElementById("leavetypecodetable");
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

function leavetype2NameFunction() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("leavetype2namesearch");
    filter = input.value.toUpperCase();
    table = document.getElementById("leavetypecodetable");
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


function emplycodesearch() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("emplycodesearch");
    filter = input.value.toUpperCase();
    table = document.getElementById("EmplyIDTable2");
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

  function emplynamesearch() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("emplynamesearch");
    filter = input.value.toUpperCase();
    table = document.getElementById("EmplyIDTable2");
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

  function emplydessearch() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("emplydessearch");
    filter = input.value.toUpperCase();
    table = document.getElementById("EmplyIDTable2");
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

  function emplydepsearch() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("emplydepsearch");
    filter = input.value.toUpperCase();
    table = document.getElementById("EmplyIDTable2");
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

  function emplyStatussearch() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("emplyStatussearch");
    filter = input.value.toUpperCase();
    table = document.getElementById("EmplyIDTable2");
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


function loadEmply(taxstate,CODE,NAME,DES,DEP,FORMID){

var url	=	'<?php echo asset('');?>master/'+FORMID+'/getEmplyDetails';

$("#tbody_EmplyID").html('');
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
$.ajax({
  url:url,
  type:'POST',
  data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'DES':DES,'DEP':DEP},
  success:function(data) {
  $("#tbody_EmplyID").html(data); 
  bindEmplyEvents(); 
  },
  error:function(data){
  console.log("Error: Something went wrong.");
  $("#tbody_EmplyID").html('');                        
  },
});

}

$('#Material').on('click','[id*="popupEMPLYID"]',function(event){
if ($('#Tax_State').length) 
{
  var taxstate = $('#Tax_State').val();
}
else
{
  var taxstate = '';
}

    var CODE = ''; 
    var NAME = ''; 
    var DES = ''; 
    var DEP = ''; 
    var FORMID = "{{$FormId}}";
    loadEmply(taxstate,CODE,NAME,DES,DEP,FORMID); 

    $("#EMPLYIDpopup").show();
    var id = $(this).attr('id');
    var id2 = $(this).parent().parent().find('[id*="EMPLY_REF"]').attr('id');
    var id3 = $(this).parent().parent().find('[id*="EmplyName"]').attr('id');
    var id4 = $(this).parent().parent().find('[id*="EMPLYDES"]').attr('id');
    var id5 = $(this).parent().parent().find('[id*="EMPLYSPECI"]').attr('id');

    $('#hdn_EmplyID').val(id);
    $('#hdn_EmplyID2').val(id2);
    $('#hdn_EmplyID3').val(id3);
    $('#hdn_EmplyID4').val(id4);
    $('#hdn_EmplyID5').val(id5);
    event.preventDefault();
  });

  $("#EMPLYID_closePopup").click(function(event){
    $("#EMPLYIDpopup").hide();
    $('.js-selectall').prop("checked", false);
  });
  

function bindEmplyEvents(){

  //$('#EmplyIDTable2').off(); 
  $('[id*="chkId"]').change(function(){
    var fieldid = $(this).parent().parent().attr('id');
    var txtval =   $("#txt"+fieldid+"").val();
    var texdesc =  $("#txt"+fieldid+"").data("desc");
    var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
    var txtname =  $("#txt"+fieldid2+"").val();
    var txtspec =  $("#txt"+fieldid2+"").data("desc");

    var fieldid22 = $(this).parent().parent().children('[id*="deptmnt"]').attr('id');
    var txtdeptmntid =  $("#txt"+fieldid22+"").val();

    var txtname =  $("#txt"+fieldid22+"").data("desc");
    var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
    var txtmuomid =  $("#txt"+fieldid3+"").val();
    var txtauom =  $("#txt"+fieldid3+"").data("desc");
    var txtmuom =  $(this).parent().parent().children('[id*="itemuom"]').text();
    var fieldid5 = $(this).parent().parent().children('[id*="irate"]').attr('id');
    var txtruom =  $("#txt"+fieldid5+"").val();
    
    if(intRegex.test(txtruom)){
      txtruom = (txtruom +'.00000');
    }
    txtruom = parseFloat(txtruom).toFixed(5);
    var SalesEnq2 = [];
    $('#example2').find('.participantRow').each(function(){
      if($(this).find('[id*="EMPLY_REF"]').val() != '')
      {
        var seitem = $(this).find('[id*="EMPLY_REF"]').val();
        SalesEnq2.push(seitem);
      }
    });
    
        if($(this).is(":checked") == true) 
        {
          if(jQuery.inArray(txtval, SalesEnq2) !== -1)
          {
                $("#EMPLYIDpopup").hide();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Emply already exists.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                $('#example2').find('.participantRow').each(function()
                  {
                    if($(this).find('[id*="EMPLY_REF"]').val() == '')
                    {
                        var rowCount = $('#Row_Count1').val();
                        if (rowCount > 1) {
                          $(this).closest('.participantRow').remove(); 
                          rowCount = parseInt(rowCount)-1;
                        $('#Row_Count1').val(rowCount);
                        }
                          event.preventDefault(); 
                    }
                  });
                $('#hdn_EmplyID').val('');
                $('#hdn_EmplyID2').val('');
                $('#hdn_EmplyID3').val('');
                $('#hdn_EmplyID4').val('');
                $('#hdn_EmplyID5').val('');
                txtval = '';
                texdesc = '';
                txtname = '';
                txtmuom = '';
                txtmuomid = '';
                txtdeptmntid = '';
                txtruom = '';
                txtspec='';
                return false;
          }   
                  if($('#hdn_EmplyID').val() == "" && txtval != '')
                  {
                    var txtid= $('#hdn_EmplyID').val();
                    var txt_id2= $('#hdn_EmplyID2').val();
                    var txt_id3= $('#hdn_EmplyID3').val();
                    var txt_id4= $('#hdn_EmplyID4').val();
                    var txt_id5= $('#hdn_EmplyID5').val();
                    

                    var $tr = $('.material').closest('table');
                    var allTrs = $tr.find('.participantRow').last();
                    var lastTr = allTrs[allTrs.length-1];
                    var $clone = $(lastTr).clone();
                    $clone.find('td').each(function(){
                        var el = $(this).find(':first-child');
                        var id = el.attr('id') || null;
                        if(id) {
                            var i = id.substr(id.length-1);
                            var prefix = id.substr(0, (id.length-1));
                            el.attr('id', prefix+(+i+1));
                        }
                        var name = el.attr('name') || null;
                        if(name) {
                            var i = name.substr(name.length-1);
                            var prefix1 = name.substr(0, (name.length-1));
                            el.attr('name', prefix1+(+i+1));
                        }
                    });
                    $clone.find('.remove').removeAttr('disabled'); 
                    $clone.find('[id*="popupEMPLYID"]').val(texdesc);
                    $clone.find('[id*="EMPLY_REF"]').val(txtval);
                    $clone.find('[id*="EmplyName"]').val(txtname);
                    $clone.find('[id*="EMPLYDES_0"]').val(txtmuom);
                    $clone.find('[id*="UOMID_REF"]').val(txtmuomid);
                    $clone.find('[id*="DEPID_REF"]').val(txtdeptmntid);
                    $clone.find('[id*="EMPLYSPECI"]').val(txtspec);
                    
                    $tr.closest('table').append($clone);   
                    var rowCount = $('#Row_Count1').val();
                      rowCount = parseInt(rowCount)+1;
                      $('#Row_Count1').val(rowCount);
                    event.preventDefault();
                  }
                  else
                  {
                  var txtid= $('#hdn_EmplyID').val();
                  var txt_id2= $('#hdn_EmplyID2').val();
                  var txt_id3= $('#hdn_EmplyID3').val();
                  var txt_id4= $('#hdn_EmplyID4').val();
                  var txt_id5= $('#hdn_EmplyID5').val();
                 
                  $('#'+txtid).val(texdesc);
                  $('#'+txt_id2).val(txtval);
                  $('#'+txt_id3).val(txtname);
                  $('#'+txt_id4).val(txtmuom);
                  $('#'+txt_id5).val(txtspec);
                  $('#'+txtid).parent().parent().find('[id*="UOMID_REF"]').val(txtmuomid);
                  $('#'+txtid).parent().parent().find('[id*="DEPID_REF"]').val(txtdeptmntid);

                  // $("#EMPLYIDpopup").hide();
                  $('#hdn_EmplyID').val('');
                  $('#hdn_EmplyID2').val('');
                  $('#hdn_EmplyID3').val('');
                  $('#hdn_EmplyID4').val('');
                  $('#hdn_EmplyID5').val('');
                 
                  event.preventDefault();
                  }
      $("#EMPLYIDpopup").hide();
      event.preventDefault();
   }
   else if($(this).is(":checked") == false) 
   {
     var id = txtval;
     var r_count = $('#Row_Count1').val();
     $('#example2').find('.participantRow').each(function()
     {
       var itemid = $(this).find('[id*="EMPLY_REF"]').val();
       if(id == itemid)
       {
          var rowCount = $('#Row_Count1').val();
          if (rowCount > 1) {
            $(this).closest('.participantRow').remove(); 
            rowCount = parseInt(rowCount)-1;
          $('#Row_Count1').val(rowCount);
          }
          else 
          {
            $(document).find('.dmaterial').prop('disabled', true);  
            $("#EMPLYIDpopup").hide();
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
       }
    });
   }
    $("#emplycodesearch").val(''); 
    $("#emplynamesearch").val(''); 
    $("#emplydessearch").val(''); 
    $("#emplydepsearch").val(''); 
    $("#emplyStatussearch").val(''); 
    $('.remove').removeAttr('disabled'); 
    event.preventDefault();
  });
}

  

//Emply ID Dropdown Ends
//------------------------

$(document).ready(function(e) {

var Material = $("#Material").html(); 
$('#hdnmaterial').val(Material);
$("#Row_Count1").val(1);
$("#Row_Count3").val(count3);
$('#example4').find('.participantRow4').each(function(){
  var txt_id4 = $(this).find('[id*="udfinputid"]').attr('id');
  var udfid = $(this).find('[id*="UDFID_REF"]').val();
  $.each( soudf, function( soukey, souvalue ) {
    if(souvalue.UDFID == udfid)
    {
      var txtvaltype2 =   souvalue.VALUETYPE;
      var strdyn2 = txt_id4.split('_');
      var lastele2 =   strdyn2[strdyn2.length-1];
      var dynamicid2 = "udfvalue_"+lastele2;
      
      var chkvaltype2 =  txtvaltype2.toLowerCase();
      var strinp2 = '';

      if(chkvaltype2=='date'){
      strinp2 = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';       
      }
      else if(chkvaltype2=='time'){
      strinp2= '<input type="time" placeholder="h:i" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';
      }
      else if(chkvaltype2=='numeric'){
      strinp2 = '<input type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"   > ';
      }
      else if(chkvaltype2=='text'){
      strinp2 = '<input type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';          
      }
      else if(chkvaltype2=='boolean'){            
          strinp2 = '<input type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" > ';
      }
      else if(chkvaltype2=='combobox'){
      var txtoptscombo2 =   souvalue.DESCRIPTIONS;
      var strarray2 = txtoptscombo2.split(',');
      var opts2 = '';
      for (var i = 0; i < strarray2.length; i++) {
          opts2 = opts2 + '<option value="'+strarray2[i]+'">'+strarray2[i]+'</option> ';
      }
      strinp2 = '<select name="'+dynamicid2+ '" id="'+dynamicid2+'" class="form-control" required>'+opts2+'</select>' ;          
      }
      $('#'+txt_id4).html('');  
      $('#'+txt_id4).html(strinp2);
    }
  });
});


var d = new Date(); 
var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
d.setDate(d.getDate() + 29);
var todate = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
$('#LOBDT').val(today);
$('#OVFDT').val(today);
// $('#CUSTOMERPODT').val(today);
$('#OVTDT').val(todate);
$("[id*='RATEPUOM']").ForceNumericOnly();

$('#CUSTOMERPONO').change(function(){
  if($(this).val() != '')
  {
    var d = new Date(); 
    var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    $('#CUSTOMERPODT').val(today);
    //$('#CUSTOMERPODT').prop('disabled','false');
  }
  else
  {
    $('#CUSTOMERPODT').val('');
    //$('#CUSTOMERPODT').prop('disabled','true');
  }
});

$('#Material').on('focusout',"[id*='RATEPUOM']",function()
{
  if(intRegex.test($(this).val())){
    $(this).val($(this).val()+'.00000')
  }
  event.preventDefault();
});   

$('#btnAdd').on('click', function() {
    var viewURL = '{{route("master",[390,"add"])}}';
              window.location.href=viewURL;
});
$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
});
//to check the label duplicacy
 $('#LOBDT').focusout(function(){
  var LOBDT   =   $.trim($(this).val());
  if(LOBDT ===""){
            $("#FocusId").val('LOBDT');
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please enter value in LOB Date.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
        } 
});
//OSO Date Check
var today = new Date(); 
var osodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
$('#OSODT').attr('min',lastosodt);
$('#OSODT').attr('max',osodate);
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
          event.preventDefault();
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
        if(id) {
            var i = id.substr(id.length-1);
            var prefix = id.substr(0, (id.length-1));
            el.attr('id', prefix+(+i+1));
        }
        var name = el.attr('name') || null;
        if(name) {
            var i = name.substr(name.length-1);
            var prefix1 = name.substr(0, (name.length-1));
            el.attr('name', prefix1+(+i+1));
        }
    });
    $clone.find('input:text').val('');
    $clone.find('[id*="EMPLY_REF"]').val('');
    $tr.closest('table').append($clone);         
    var rowCount1 = $('#Row_Count1').val();
    rowCount1 = parseInt(rowCount1)+1;
    $('#Row_Count1').val(rowCount1);
    $clone.find('.remove').removeAttr('disabled'); 
    $("[id*='RATEPUOM']").ForceNumericOnly();
    event.preventDefault();
});

$("#btnUndo").on("click", function() {
    $("#AlertMessage").text("Do you want to erase entered information in this record?");
    $("#alert").modal('show');
    $("#YesBtn").data("funcname","fnUndoYes");
    $("#YesBtn").show();
    $("#NoBtn").data("funcname","fnUndoNo");
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#NoBtn").focus();
});



window.fnUndoYes = function (){
  //reload form
  window.location.href = "{{route('master',[390,'add'])}}";
}//fnUndoYes


window.fnUndoNo = function (){
  $("#SONO").focus();
}//fnUndoNo

$('#Material').on('focusout','[id*="RATEPUOM"]',function(){
var ratevalue = parseFloat($(this).val());
  if(ratevalue <= '0' )
  {
    $(this).val('');
    $("#FocusId").val($(this));
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter rate greater than zero.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;

  }
});


$("#OSOFC").change(function() {
  if ($(this).is(":checked") == true){
      $(this).parent().parent().find('#txtCRID_popup').removeAttr('disabled');
      $(this).parent().parent().find('#txtCRID_popup').prop('readonly','true');
      $(this).parent().parent().find('#CONVFACT').removeAttr('disabled');
      $(this).parent().parent().find('#CONVFACT').prop('readonly','true');
      event.preventDefault();
  }
  else
  {
      $(this).parent().parent().find('#txtCRID_popup').prop('disabled','true');
      $(this).parent().parent().find('#txtCRID_popup').removeAttr('readonly');
      $(this).parent().parent().find('#CONVFACT').prop('disabled','true');
      $(this).parent().parent().find('#CONVFACT').removeAttr('readonly');
      $(this).parent().parent().find('#txtCRID_popup').val('');
      $(this).parent().parent().find('#CRID_REF').val('');
      $(this).parent().parent().find('#CONVFACT').val('');
      event.preventDefault();
  }
});


});
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

$("#btnSave").on("submit", function( event ) {
if ($("#frm_mst_edit").valid()) {
    // Do something
    alert( "Handler for .submit() called." );
    event.preventDefault();
}
});


$('#frm_trn_oso1').bootstrapValidator({
   
    fields: {
        txtlabel: {
            validators: {
                notEmpty: {
                    message: 'The OSO NO is required'
                }
            }
        },            
    },
    submitHandler: function(validator, form, submitButton) {
        alert( "Handler for .submit() called." );
         event.preventDefault();
         $("#frm_mst_edit").submit();
    }
});
});
function validateForm(){

$("#FocusId").val('');
var FYID_REF           =   $.trim($("#FYID_REF").val());
var OSODT           =   $.trim($("#OSODT").val());

if(FYID_REF ===""){
 $("#FocusId").val('FYID_REF');
 $("#ProceedBtn").focus();
 $("#YesBtn").hide();
 $("#NoBtn").hide();
 $("#OkBtn1").show();
 $("#AlertMessage").text('Please enter value in Financial Year.');
 $("#alert").modal('show');
 $("#OkBtn1").focus();
 return false;
}

else if(popupEMPLYID_0 ===""){
 $("#FocusId").val('popupEMPLYID_0');
 $("#GLID_REF").val('');  
 $("#ProceedBtn").focus();
 $("#YesBtn").hide();
 $("#NoBtn").hide();
 $("#OkBtn1").show();
 $("#AlertMessage").text('Please select Employee Code.');
 $("#alert").modal('show');
 $("#OkBtn1").focus();
 return false;
} 
 
else{
event.preventDefault();
var allblank = [];
var allblank2 = [];
var allblank3 = [];
var allblank4 = [];
var allblank5 = [];

var focustext1= "";
var focustext2= "";
var focustext3= "";
var focustext4= "";
var focustext5= "";

    // $('#udfforsebody').find('.form-control').each(function () {
    $('#example2').find('.participantRow').each(function(){
        if($.trim($(this).find("[id*=EMPLY_REF]").val())!="")
        {
            allblank.push('true');
                if($.trim($(this).find("[id*=EMPLYDES]").val())!=""){
                    allblank2.push('true');
                }
                else{
                    allblank2.push('false');
                    focustext2 = $(this).find("[id*=EMPLYDES]").attr('id');
                }
                if($.trim($(this).find("[id*=leavetypecode_popup]").val())!="" && $.trim($(this).find("[id*=leavetypecode_popup]").val()) > '0'){
                    allblank3.push('true');
                }
                else{
                    allblank3.push('false');
                    focustext3 = $(this).find("[id*=leavetypecode_popup]").attr('id');
                } 

                if($.trim($(this).find("[id*=OPNGBALANCE]").val())!="" && $.trim($(this).find("[id*=OPNGBALANCE]").val()) > '0'){
                    allblank4.push('true');
                }
                else{
                    allblank4.push('false');
                    focustext4 = $(this).find("[id*=OPNGBALANCE]").attr('id');
                } 
        }
        else
        {
            allblank.push('false');
            focustext1 = $(this).find("[id*=popupEMPLYID]").attr('id');
        }
    });
   
      
    if(jQuery.inArray("false", allblank) !== -1){
      $("#MAT_TAB").click();
      $("#FocusId").val(focustext1);
            $("#alert").modal('show');
            $("#AlertMessage").text('Please select Employee Code in Material Tab.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
        }
        else if(jQuery.inArray("false", allblank2) !== -1){
        $("#MAT_TAB").click();
        $("#FocusId").val(focustext2);
        $("#alert").modal('show');
        $("#AlertMessage").text('Designation is missing in Material Tab.');
        $("#YesBtn").hide(); 
        $("#NoBtn").hide();  
        $("#OkBtn1").show();
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk');
        }            
        else if(jQuery.inArray("false", allblank3) !== -1){
          $("#MAT_TAB").click();
          $("#FocusId").val(focustext3);
        $("#alert").modal('show');
        $("#AlertMessage").text('Please enter Leave Type Code	 in Material Tab.');
        $("#YesBtn").hide(); 
        $("#NoBtn").hide();  
        $("#OkBtn1").show();
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk');
        }

        else if(jQuery.inArray("false", allblank4) !== -1){
          $("#MAT_TAB").click();
          $("#FocusId").val(focustext4);
        $("#alert").modal('show');
        $("#AlertMessage").text('Please enter Opening Balance in Material Tab.');
        $("#YesBtn").hide(); 
        $("#NoBtn").hide();  
        $("#OkBtn1").show();
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk');
        }
       
        else{
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
            $("#YesBtn").focus();
            $("#OkBtn").hide();
            highlighFocusBtn('activeYes');
        }
}
}

$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
window[customFnName]();

}); //yes button


$("#btnSave" ).click(function() {
var formReqData = $("#frm_mst_edit");
if(formReqData.valid()){
  validateForm();
}
});

window.fnSaveData = function (){

//validate and update data
 var trnosoForm = $("#frm_mst_edit");
var formData = trnosoForm.serialize();
$.ajaxSetup({
headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});
$.ajax({
url:'{{ route("master",[390,"update"])}}',
type:'POST',
data:formData,
success:function(data) {
   
    if(data.errors) {
        $(".text-danger").hide();
        if(data.errors.LOBDT){
            showError('ERROR_LOBDT',data.errors.LOBDT);
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('Please enter correct value in LOBDT.');
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
        }
       }
    if(data.success) {                   
        console.log("succes MSG="+data.msg);            
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(data.msg);
        $(".text-danger").hide();
        $("#alert").modal('show');
        $("#OkBtn").focus();
    }
    else if(data.cancel) {                   
        console.log("cancel MSG="+data.msg);
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(data.msg);
        $(".text-danger").hide();
        $("#alert").modal('show');
        $("#OkBtn1").focus();
    }
    else 
    {                   
        console.log("succes MSG="+data.msg);
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(data.msg);
        $(".text-danger").hide();
        $("#alert").modal('show');
        $("#OkBtn1").focus();
    }        
},
error:function(data){
    console.log("Error: Something went wrong.");
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Error: Something went wrong.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');
},
});

}

//no button
$("#NoBtn").click(function(){
$("#alert").modal('hide');
});

//ok button
$("#OkBtn").click(function(){
$("#alert").modal('hide');
$("#YesBtn").show();
$("#NoBtn").show();
$("#OkBtn").hide();
$(".text-danger").hide();
window.location.href = '{{route("master",[390,"index"]) }}';
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

//
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


//Leave Type Code
    $('#example2').on ("focus",'[id*="leavetypecode_popup"]',function(event){

    $("#leavetypecodepopup").show();
      var id = $(this).attr('id');
      var id2 = $(this).parent().parent().find('[id*="hdnleavetypecode_popup"]').attr('id');
      var id3 = $(this).parent().parent().find('[id*="txtleavetypedesciption"]').attr('id');

        $('#hdn_leavetypecodefieldid').val(id);
        $('#hdn_leavetypecodefieldid2').val(id2);        
        $('#hdn_leavetypecodefieldid3').val(id3);        
        });

        $("#leavetypecode_closePopup").on("click",function(event){
              $("#leavetypecodepopup").hide();
        });

$('#leavetypecodetable').on("click",".clsleavetypecode",function(){
    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");
    var txtleavetypedesc =   $("#txt"+fieldid+"").data("leavetypedesc");

    var txtid= $('#hdn_leavetypecodefieldid').val();
    var txt_id2= $('#hdn_leavetypecodefieldid2').val();
    var txt_id3= $('#hdn_leavetypecodefieldid3').val();        
    
    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $('#'+txt_id3).val(txtleavetypedesc);  
    $('#'+txtid).blur();  

    //clear 
    $('#'+txtid).parent().parent().find('[id*="txtleavetypevalue_popup"]').val('');
    $('#'+txtid).parent().parent().find('[id*="hdnleavetypevalue_popup"]').val('');

    $("#leavetypecodepopup").hide();
    $("#leavetype2codesearch").val(''); 
    $("#leavetype2namesearch").val('');
    leavetype2Function(); 
    $(this).prop("checked",false);
    
    event.preventDefault();
});
//Leave Type Code


</script>


@endpush