@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
      <div class="col-lg-2">
      <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Vendor - Item Info</a>
      </div>

      <div class="col-lg-10 topnav-pd">
              <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
              <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
              <button class="btn topnavbt" id="btnSaveFormData" ><i class="fa fa-floppy-o"></i> Save</button>
              <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
              <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
              <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
              <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
              <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
              <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
              <a href="{{route('home')}}" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
      </div>
  </div>
</div>

<form id="frm_trn_add" method="POST"  >
  <div class="container-fluid purchase-order-view">    
    @csrf
    <div class="container-fluid filter">
      <div class="inner-form">
                    
        <div class="row">
          <div class="col-lg-2 pl"><p>Document No*</p></div>
          <div class="col-lg-2 pl">
            @if(isset($objSON->SYSTEM_GRSR) && $objSON->SYSTEM_GRSR == "1")
                <input type="text" name="VIINFONO" id="VIINFONO" value="{{ isset($objAutoGenNo)?$objAutoGenNo:'' }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
            @elseif(isset($objSON->MANUAL_SR) && $objSON->MANUAL_SR == "1")
                <input type="text" name="VIINFONO" id="VIINFONO" value="{{ old('VIINFONO') }}" class="form-control mandatory" maxlength="{{isset($objSON->MANUAL_MAXLENGTH)?$objSON->MANUAL_MAXLENGTH:''}}" autocomplete="off" style="text-transform:uppercase"  >
            @else
                <input type="hidden" name="VIINFONO" id="VIINFONO"  class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
            @endif
            <span class="text-danger" id="ERROR_VIINFONO"></span>
          </div>
            
          <div class="col-lg-2 pl"><p>Document Date*</p></div>
          <div class="col-lg-2 pl">
            <input type="date" name="VIINFODT" id="VIINFODT"  class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
          </div>
            
          <div class="col-lg-2 pl"><p>Vendor*</p></div>
          <div class="col-lg-2 pl">
              <input type="text" name="txtvendor_popup" id="txtvendor_popup" class="form-control mandatory"  autocomplete="off" readonly/>
              <input type="hidden" name="VID_REF" id="VID_REF" class="form-control" autocomplete="off" />
              <input type="hidden" name="HID_ITEMGID_REF" id="HID_ITEMGID_REF" >
          </div>
        </div>                  
      </div>


      <div class="container-fluid purchase-order-view">

        <div class="row">
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
          </ul>

          <div class="tab-content">

            <div id="Material" class="tab-pane fade in active">
              <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                    <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                        <thead id="thead1"  style="position: sticky;top: 0">
                                
                            <tr>
                                <th>Item Group <input type="hidden" name="Row_Count1" id="Row_Count1" value="1"  > </th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>UOM</th>
                                <th>EOQ</th>
                                <th>Lead Days</th>
                                <th>Remarks</th>
                                <th>Action</th>
                               
                            </tr>
                    
            </thead>
            <tbody>
                <tr  class="participantRow">
                   
                    <td hidden><input type="text" name="MRSNO_0" id="MRSNO_0" > </td>
                    <td style="text-align:center;" >
                    
                    <input type="text" name="txtIG_popup_0" id="txtRFQ_popup_0" class="form-control CLS_RFQ"  autocomplete="off"  readonly style="width:100px;" /></td>
                    <td  hidden><input type="text" name="RFQID_0" id="RFQID_0" class="form-control" autocomplete="off" /></td>
                    
                    <td><input type="text" name="popupITEMID_0" id="popupITEMID_0" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                    <td hidden><input type="text" name="ITEMID_REF_0" id="ITEMID_REF_0" class="form-control" autocomplete="off" /></td>
                    <td><input type="text" name="ItemName_0" id="ItemName_0" class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>
                    <td><input type="text" name="ItemUom_0" id="ItemUom_0" class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>
                    <td hidden><input type="text" name="ItemuomText_0" id="ItemuomText_0" class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>
                    
                    
                    <td hidden><input type="text" name="UOMID_REF_0" id="Itemspec_0" class="form-control" readonly autocomplete="off"  /></td>
                    <td @if(strpos($objCOMPANY->NAME,"ALPS") == false)  hidden @endif  ><input type="text" name="Alpspartno_0" id="Alpspartno_0" class="form-control"  autocomplete="off"  readonly  /></td>
                    <td @if(strpos($objCOMPANY->NAME,"ALPS") == false)  hidden @endif  ><input type="text" name="Custpartno_0" id="Custpartno_0" class="form-control"  autocomplete="off"  readonly  /></td>
                    <td @if(strpos($objCOMPANY->NAME,"ALPS") == false)  hidden @endif  ><input type="text" name="OEMpartno_0" id="OEMpartno_0" class="form-control"  autocomplete="off"  readonly /></td>
                    
                    
                    <td><input type="text" name="EOQ_0" id="EOQ_0" class="form-control" onkeypress="return onlyNumberKey(event)"  autocomplete="off" style="width:100px;"/></td>
                    
                    <td><input type="text" name="LEADDAYS_0" id="LEADDAYS_0" class="form-control"  autocomplete="off"  onkeypress="return onlyNumberKey(event)" maxlength="11"></td>

                    <td><input type="text" name="REMARKS_0" id="REMARKS_0" class="form-control"  autocomplete="off"  style="width:100px;"/></td>
                    
                    <td align="center" ><button class="btn add material" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                    </tr>
                      <tr></tr>
                  </tbody>
              </table>
              </div>	
          </div>
      </div>
  </div>
</div>
</div>
</div>
</form>

 <!-- *******************************************{ alert  form  }****************************************** * -->
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
            <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData">
            <div id="alert-active" class="activeYes"></div>Yes
            </button>
            <button class="btn alertbt" name='NoBtn' id="NoBtn"   data-funcname="fnUndoNo" >
            <div id="alert-active" class="activeNo"></div>No
            </button>
            <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">
            <div id="alert-active" class="activeOk"></div>OK</button>
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" onclick="getFocus()" style="display:none;margin-left: 90px;">
            <div id="alert-active" class="activeOk1"></div>OK</button>
            <input type="hidden" id="FocusId" >
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- Alert -->
<!-- Vendor Dropdown -->


 <!-- *******************************************{ Vendor Dropdown  }****************************************** * -->
<div id="vendoridpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='vendor_close_popup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Vendor Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="VendorCodeTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Description</th>
    </tr>
    </thead>
    <tbody>
    

    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="vendorcodesearch" class="form-control" autocomplete="off" onkeyup="VendorCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="vendornamesearch" class="form-control" autocomplete="off" onkeyup="VendorNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="VendorCodeTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2"> 
        </thead>
        <tbody id="tbody_vendor" >
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Vendor Dropdown-->


 <!-- *******************************************{ Item Group Popup   }****************************************** * -->
<div id="RFQpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='RFQ_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Group</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="RFQTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata"  >            
            <td> <input type="hidden" name="fieldid" id="hdn_rfqid"/>
            <input type="hidden" name="fieldid2" id="hdn_rfqid2"/>
            <input type="hidden" name="fieldid3" id="hdn_rfqid3"/>
            </td>
          </tr>
                <tr>
                  <th class="ROW1">Select</th> 
                  <th class="ROW2">Item Code</th>
                  <th class="ROW3">Description</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th class="ROW1"><span class="check_th">&#10004;</span></th>
                    <td class="ROW2"><input type="text" id="RFQcodesearch" class="form-control" autocomplete="off" onkeyup="RFQCodeFunction()"></td>
                    <td class="ROW3"><input type="text" id="RFQnamesearch" class="form-control" autocomplete="off" onkeyup="RFQNameFunction()"></td>

                  </tr>
                </tbody>
                </table>
      <table id="RFQTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_RFQ">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

 <!-- *******************************************{ Item Code Popup   }****************************************** * -->

 <div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%;" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
    <thead>
      <tr id="none-select" class="searchalldata" >
            
            <td> <input type="hidden" name="fieldid" id="hdn_ItemID"/>
            <input type="hidden" name="fieldid2" id="hdn_ItemID2"/>
            <input type="hidden" name="fieldid3" id="hdn_ItemID3"/>
            <input type="hidden" name="fieldid4" id="hdn_ItemID4"/>
            <input type="hidden" name="fieldid5" id="hdn_ItemID5"/>
            <input type="hidden" name="fieldid6" id="hdn_ItemID6"/>
            <input type="hidden" name="fieldid7" id="hdn_ItemID7"/>
            <input type="hidden" name="fieldid8" id="hdn_ItemID8"/>
            <input type="hidden" name="fieldid9" id="hdn_ItemID9"/>
            <input type="hidden" name="fieldid10" id="hdn_ItemID10"/>
            <input type="hidden" name="fieldid100" id="hdn_ItemID100"/>
            <input type="hidden" name="fieldid11" id="hdn_ItemID11"/>
            <input type="hidden" name="fieldid12" id="hdn_ItemID12"/>
            <input type="hidden" name="fieldid13" id="hdn_ItemID13"/>
            <input type="hidden" name="fieldid14" id="hdn_ItemID14"/>
            <input type="hidden" name="fieldid15" id="hdn_ItemID15"/>
            <input type="hidden" name="fieldid16" id="hdn_ItemID16"/>
            <input type="hidden" name="fieldid17" id="hdn_ItemID17"/>
            <input type="hidden" name="fieldid18" id="hdn_ItemID18"/>
            <input type="hidden" name="fieldid19" id="hdn_ItemID19"/>
            <input type="hidden" name="fieldid20" id="hdn_ItemID20"/>
            <input type="hidden" name="fieldid21" id="hdn_ItemID21" />
            <input type="hidden" name="fieldid22" id="hdn_ItemID22" />
            </td>
      </tr>
      <tr>
            <th style="width:8%;text-align:center;" id="all-check">Select</th>
            <th style="width:10%;">Code</th>
            <th style="width:10%;">Name</th>
            <th style="width:8%;">Main UOM</th>
            <th style="width:8%;">Main QTY</th>
            <th style="width:8%;">Item Group</th>
            <th style="width:8%;">Item Category</th>
            <th style="width:8%;">Business Unit</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}} >ALPS Part No.</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}} >Customer Part No.</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}} >OEM Part No.</th>
            <th style="width:8%;">Status</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th style="width:8%;text-align:center;">&#10004;</th>
        <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction()"></td>
        <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemNameFunction()"></td>
        <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control"  autocomplete="off" onkeyup="ItemUOMFunction()"></td>
        <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control" autocomplete="off" onkeyup="ItemQTYFunction()"></td>
        <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="ItemGroupFunction()"></td>
        <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="ItemCategoryFunction()"></td>
        <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction()"></td>
        <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="ItemAPNFunction()"></td>
        <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="ItemCPNFunction()"></td>
        <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="ItemOEMPNFunction()"></td>
        <td style="width:8%;"><input type="text" id="ItemStatussearch" class="form-control" autocomplete="off" onkeyup="ItemStatusFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_ItemID">     
          
          
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
@endsection


@push('bottom-css')
<style>
#ItemIDcodesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}
#ItemIDnamesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}

#ItemIDTable {
  border-collapse: collapse;
  width: 950px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#ItemIDTable th {
    text-align: center;
    padding: 5px;
   
    font-size: 11px;
    
    color: #0f69cc;
    font-weight: 600;
}

#ItemIDTable td {
    text-align: center;
    padding: 5px;
    font-size: 11px;
   
    font-weight: 600;
}

#ItemIDTable2 {
  border-collapse: collapse;
  width: 1050px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#ItemIDTable2 th{
    text-align: left;
    padding: 5px;
    
    font-size: 11px;
    
    color: #0f69cc;
    font-weight: 600;
}

#ItemIDTable2 td {
  text-align: left;
    padding: 5px;
    font-size: 11px;
   
    font-weight: 600;
    width: 16%;
}
#CTIDDetTable2 {
  border-collapse: collapse;
  width: 1050px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#CTIDDetTable2 th{
    text-align: left;
    padding: 5px;
   
    font-size: 11px;
   
    color: #0f69cc;
    font-weight: 600;
}

#CTIDDetTable2 td {
  text-align: left;
    padding: 5px;
    font-size: 11px;
   
    font-weight: 600;
    width: 20%;
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


let tid = "#VendorCodeTable2";
let tid2 = "#VendorCodeTable";
let headers = document.querySelectorAll(tid2 + " th");

      
headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(tid, ".clsvendorid", "td:nth-child(" + (i + 1) + ")");
  });
});

function VendorCodeFunction() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("vendorcodesearch");
    filter = input.value.toUpperCase();
    if(filter.length == 0)
    {
      var CODE = ''; 
      var NAME = ''; 
      loadVendor(CODE,NAME); 
    }
    else if(filter.length >= 3)
    {
      var CODE = filter; 
      var NAME = ''; 
      loadVendor(CODE,NAME); 
    }
    else
    {
      table = document.getElementById("VendorCodeTable2");
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
}

function VendorNameFunction() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("vendornamesearch");
    filter = input.value.toUpperCase();
    if(filter.length == 0)
    {
      var CODE = ''; 
      var NAME = ''; 
      loadVendor(CODE,NAME);
    }
    else if(filter.length >= 3)
    {
      var CODE = ''; 
      var NAME = filter; 
      loadVendor(CODE,NAME);  
    }
    else
    {
      table = document.getElementById("VendorCodeTable2");
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
}

function loadVendor(CODE,NAME){
   
  $("#tbody_vendor").html('');
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("master",[$FormId,"getVendor"])}}',
    type:'POST',
    data:{'CODE':CODE,'NAME':NAME},
    success:function(data) {
      $("#tbody_vendor").html(data); 
      bindVendorEvents();
      showSelectedCheck($("#VID_REF").val(),"SELECT_VID_REF"); 
    },
    error:function(data){
    console.log("Error: Something went wrong.");
    $("#tbody_vendor").html('');                        
    },
  });
}

$('#txtvendor_popup').click(function(event){
  

  var CODE = ''; 
  var NAME = ''; 
  loadVendor(CODE,NAME);  

  $("#vendoridpopup").show();
  event.preventDefault();
});

$("#vendor_close_popup").click(function(event){
  $("#vendoridpopup").hide();
  event.preventDefault();
});




function bindVendorEvents(){

        $('.clsvendorid').click(function(){
    
            var id = $(this).attr('id');
            var txtval =    $("#txt"+id+"").val();
            var texdesc =   $("#txt"+id+"").data("desc");
          
            $("#txtvendor_popup").val(texdesc);
            $("#txtvendor_popup").blur();
            $("#VID_REF").val(txtval);

            $("#vendoridpopup").hide();
            $("#vendorcodesearch").val(''); 
            $("#vendornamesearch").val('');

         
            
              event.preventDefault();
        });
  }
  
//Vendor Ends
//------------------------

//------------------------
  //getVdInfo
      let sqtid = "#RFQTable2";
      let sqtid2 = "#RFQTable";
      let salesquotationheaders = document.querySelectorAll(sqtid2 + " th");

      // Sort the table element when clicking on the table headers
      salesquotationheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sqtid, ".clssqid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function RFQCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("RFQcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("RFQTable2");
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

  function RFQNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("RFQnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("RFQTable2");
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

  $('#Material').on('click','[id*="txtRFQ_popup"]',function(event){

      $('#HID_ITEMGID_REF').val('');


          var id = $(this).attr('id');
          var id2 = $(this).parent().parent().find('[id*="RFQID"]').attr('id');      
          $('#hdn_rfqid').val(id);
          $('#hdn_rfqid2').val(id2);
          var fieldid = $(this).parent().parent().find('[id*="RFQID"]').attr('id');

          $("#RFQpopup").show();
          $("#tbody_RFQ").html('');
          //$("#tbody_RFQ").html('Loading...');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          })
          $.ajax({
              url:'{{route("master",[$FormId,"getVdInfo"])}}',
              type:'POST',
              data:{'id':$('#VID_REF').val(),'fieldid':fieldid},
              success:function(data) {
                $("#tbody_RFQ").html(data);
                BindRFQuotation();
                
                showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $("#tbody_RFQ").html('');
              },
          });

          $(this).parent().parent().find('[id*="popupITEMID"]').val('');
          $(this).parent().parent().find('[id*="ITEMID_REF"]').val('');
          $(this).parent().parent().find('[id*="ItemName"]').val('');
          $(this).parent().parent().find('[id*="ItemuomText"]').val('');
          $(this).parent().parent().find('[id*="ItemUom"]').val('');
          $(this).parent().parent().find('[id*="ItemUomRefId"]').val('');
          $(this).parent().parent().find('[id*="popupMUOM"]').val('');
          $(this).parent().parent().find('[id*="REMARKS"]').val('');
          $(this).parent().parent().find('[id*="VQ_QTY"]').val('');
          $(this).parent().parent().find('[id*="HID_VQ_QTY"]').val('');
        

      });

      $("#RFQ_closePopup").click(function(event){
        $("#RFQpopup").hide();
      });

      function BindRFQuotation(){
          $(".clsrfqid").click(function(){
            var fieldid = $(this).attr('id');
            var txtval =    $("#txt"+fieldid+"").val();
            var texdesc =   $("#txt"+fieldid+"").data("desc");
            var texdescdate =   $("#txt"+fieldid+"").data("descdate");
            
            var txtid= $('#hdn_rfqid').val();
            var txt_id2= $('#hdn_rfqid2').val();
            //var txt_id3= $('#hdn_rfqid3').val();


            $('#'+txtid).val(texdesc+'-'+texdescdate);
            $('#'+txt_id2).val(txtval);


            $('#HID_ITEMGID_REF').val(txtval);

            $("#RFQpopup").hide();
            
            $("#RFQcodesearch").val(''); 
            $("#RFQnamesearch").val(''); 
         
            event.preventDefault();
          });
      }

      

  //getVdInfo
//------------------------


//------------------------
//Item ID Dropdown

let itemtid = "#ItemIDTable2";
let itemtid2 = "#ItemIDTable";
let itemtidheaders = document.querySelectorAll(itemtid2 + " th");

itemtidheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(itemtid, ".clsitemid", "td:nth-child(" + (i + 1) + ")");
  });
});

function ItemCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Itemcodesearch");
  filter = input.value.toUpperCase();

  if(filter.length == 0){
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3){
    var CODE = filter; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
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
}


function ItemNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Itemnamesearch");
  filter = input.value.toUpperCase();

  if(filter.length == 0)  {
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3) {    
    var CODE = ''; 
    var NAME = filter; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
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
}



function ItemUOMFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemUOMsearch");
  filter = input.value.toUpperCase();  
  if(filter.length == 0) {  
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3){   
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = filter; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[3];
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
}

function ItemQTYFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemQTYsearch");
  filter = input.value.toUpperCase();        
  table = document.getElementById("ItemIDTable2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[4];
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


function ItemGroupFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemGroupsearch");
  filter = input.value.toUpperCase();
  if(filter.length == 0){    
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3){    
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = filter; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[5];
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
}

function ItemCategoryFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemCategorysearch");
  filter = input.value.toUpperCase();
  if(filter.length == 0){    
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3){    
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = filter; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[6];
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
}

function ItemBUFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemBUsearch");
filter = input.value.toUpperCase();
if(filter.length == 0) {   
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3) {    
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = filter; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[7];
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
}

function ItemAPNFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemBUsearch");
filter = input.value.toUpperCase();
if(filter.length == 0) {   
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3) {    
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = filter; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[8];
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
}

function ItemCPNFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemBUsearch");
filter = input.value.toUpperCase();
if(filter.length == 0) {   
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3) {    
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = filter; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[9];
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
}

function ItemOEMPNFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemBUsearch");
filter = input.value.toUpperCase();
if(filter.length == 0) {   
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3) {    
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = filter; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[10];
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
}


function ItemStatusFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemStatussearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("ItemIDTable2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[11];
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

function loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART){

    var ITEMGID_REF =$("#HID_ITEMGID_REF").val();
	
		 $("#tbody_ItemID").html('');
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
      url:'{{route("master",[$FormId,"getItemDetails"])}}',
			type:'POST',
			data:{'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART,'ITEMGID_REF':ITEMGID_REF},
			success:function(data) {
			$("#tbody_ItemID").html(data); 
			bindItemEvents(); 
      $('.js-selectall').prop('disabled', true);
			},
			error:function(data){
			console.log("Error: Something went wrong.");
			$("#tbody_ItemID").html('');                        
			},
		});

}

$('#Material').on('click','[id*="popupITEMID"]',function(event){

  var ITEMGID_REF = $(this).parent().parent().find('[id*="RFQID"]').val();
  var txtRFQ_popup = $(this).parent().parent().find('[id*="txtRFQ_popup"]').attr('id');
  if(ITEMGID_REF ===""){
    $("#FocusId").val("popupITEMID_0");        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Item Code.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else{
    $('#HID_ITEMGID_REF').val(ITEMGID_REF);
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = '';
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }

  $("#ITEMIDpopup").show();

  var id = $(this).attr('id');
  var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
  var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
  var id4 = $(this).parent().parent().find('[id*="ItemUom"]').attr('id');
  var id5 = $(this).parent().parent().find('[id*="ItemuomText"]').attr('id');          
  var id6 = $(this).parent().parent().find('[id*="Itemspec"]').attr('id');
  var id7 = $(this).parent().parent().find('[id*="ItemMuomRefid"]').attr('id');
  var id9 = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
  var id10 = $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
  var id11 = $(this).parent().parent().find('[id*="VQ_QTY"]').attr('id');
  var id12 = $(this).parent().parent().find('[id*="popupAUOM"]').attr('id');
  var id13 = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');
  var id14 = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
  var id15 = $(this).parent().parent().find('[id*="RATEPUOM"]').attr('id');
  var id16 = $(this).parent().parent().find('[id*="SO_FQTY"]').attr('id');

  $('#hdn_ItemID').val(id);
  $('#hdn_ItemID2').val(id2);
  $('#hdn_ItemID3').val(id3);
  $('#hdn_ItemID4').val(id4);
  $('#hdn_ItemID5').val(id5);
  $('#hdn_ItemID9').val(id9);
  $('#hdn_ItemID10').val(id10);
  $('#hdn_ItemID100').val(id100);
  $('#hdn_ItemID11').val(id11);
  $('#hdn_ItemID12').val(id12);
  $('#hdn_ItemID13').val(id13);
  $('#hdn_ItemID14').val(id14);
  $('#hdn_ItemID15').val(id15);
  $('#hdn_ItemID16').val(id16);
  $('#hdn_ItemID17').val(RFQ_ID);

      var r_count = 0;
      var SalesEnq = [];
      $('#Material').find('.participantRow').each(function(){
        if($(this).find('[id*="ITEMID_REF"]').val() != '')
        {
          SalesEnq.push($(this).find('[id*="RFQID"]').val());
          r_count = parseInt(r_count)+1;
          $('#hdn_ItemID22').val(r_count);
        }
      });

      $('#hdn_ItemID18').val(SalesEnq.join(', '));

      var ItemID = [];
      $('#Material').find('.participantRow').each(function(){
        if($(this).find('[id*="ITEMID_REF"]').val() != '')
        {
          ItemID.push($(this).find('[id*="ITEMID_REF"]').val());
        }
      });

      $('#hdn_ItemID19').val(ItemID.join(', '));

      event.preventDefault();          
});

    $("#ITEMID_closePopup").click(function(event){
      $("#ITEMIDpopup").hide();
      $('.js-selectall').prop("checked", false);
    });

    function bindItemEvents(){

$('#ItemIDTable2').off(); 
$('.js-selectall1').prop('checked', false); 

$('[id*="chkId"]').change(function(){

  var fieldid = $(this).parent().parent().attr('id');
  var txtval =   $("#txt"+fieldid+"").val();
  var texdesc =  $("#txt"+fieldid+"").data("desc");
  var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
  var txtname =  $("#txt"+fieldid2+"").val();
  var txtspec =  $("#txt"+fieldid2+"").data("desc");
  var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
  var txtmuomid =  $("#txt"+fieldid3+"").val();
  var txtauom =  $("#txt"+fieldid3+"").data("desc");
  var txtmuom =  $(this).parent().parent().children('[id*="itemuom"]').text().trim();
  var fieldid4 = $(this).parent().parent().children('[id*="uomqty"]').attr('id');
  var txtauomid =  $("#txt"+fieldid4+"").val();

  var txtauomqty =  $("#txt"+fieldid4+"").data("desc");
  var txtmuomqty =  $(this).parent().parent().children('[id*="uomqty"]').text().trim();
  var fieldid5 = $(this).parent().parent().children('[id*="irate"]').attr('id');
  var txtruom =  $("#txt"+fieldid5+"").val();
  var txtmqtyf = $("#txt"+fieldid5+"").data("desc");
  var fieldid6 = $(this).parent().parent().children('[id*="itax"]').attr('id');
  
  
  
 if($(this).is(":checked") == true) 
 {

  $('#example2').find('.participantRow').each(function()
   {
     var itemid = $(this).find('[id*="ITEMID_REF"]').val();
     if(txtval)
     {
      $("#ITEMIDpopup").hide();
          if(txtval == itemid)
          {
                $('.js-selectall1').prop('checked', false); 
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
                $('#hdn_ItemID2').val('');
                $('#hdn_ItemID3').val('');
                $('#hdn_ItemID4').val('');
                $('#hdn_ItemID5').val('');
                $('#hdn_ItemID6').val('');
                $('#hdn_ItemID7').val('');
                
                $('#hdn_ItemID11').val('');
                txtval = '';
                texdesc = '';
                txtname = '';
                txtmuom ='';
                txtmuomid ='';
                txtspec = '';
                txtmuomrefid ='';
                txtauom = '';
                txtauomid = '';
                return false;
                
          }               
     }          
  });
                if($('#hdn_ItemID').val() == "" && txtval != '')
                {
                  var txtid= $('#hdn_ItemID').val();
                  var txt_id2= $('#hdn_ItemID2').val();
                  var txt_id3= $('#hdn_ItemID3').val();
                  var txt_id4= $('#hdn_ItemID4').val();
                  var txt_id5= $('#hdn_ItemID5').val();
                  var txt_id6= $('#hdn_ItemID6').val();
                  var txt_id7= $('#hdn_ItemID7').val();
                  
                  var txt_id11= $('#hdn_ItemID11').val();

                  var $tr = $('.material').closest('table');
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

                  $clone.find('.remove').removeAttr('disabled'); 
                  $clone.find('[id*="popupITEMID"]').val(txtval);
                  $clone.find('[id*="popupITEMID"]').val(texdesc);
                  $clone.find('[id*="ITEMID_REF"]').val(txtval);
                  $clone.find('[id*="ItemName"]').val(txtname);                  
                  $clone.find('[id*="ItemuomText"]').val(txtmuom);
                  $clone.find('[id*="ItemUom"]').val(txtmuomrefid);
                  $clone.find('[id*="Itemspec"]').val(txtspec);
                  $clone.find('[id*="ItemMuomRefid"]').val(txtmuomid); 

                        
                  $clone.find('[id*="popupMUOM"]').val(txtmuom);
                  $clone.find('[id*="SE_QTY"]').val(txtmuomqty);
                  
                  
                  $tr.closest('table').append($clone);   
                  var rowCount = $('#Row_Count1').val();
                    rowCount = parseInt(rowCount)+1;
                    $('#Row_Count1').val(rowCount);
                   
                  $('.js-selectall1').prop('checked', false); 
                  $("#ITEMIDpopup").hide();
                  event.preventDefault();
                }
                else
                {
                var txtid= $('#hdn_ItemID').val();
                var txt_id2= $('#hdn_ItemID2').val();
                var txt_id3= $('#hdn_ItemID3').val();
                var txt_id4= $('#hdn_ItemID4').val();
                var txt_id5= $('#hdn_ItemID5').val();
                var txt_id6= $('#hdn_ItemID6').val();
                var txt_id7= $('#hdn_ItemID7').val();
                var txt_id8= $('#hdn_ItemID8').val();
                var txt_id9= $('#hdn_ItemID9').val();
                
                var txt_id11= $('#hdn_ItemID11').val();
                $('#'+txtid).val(txtval);
                $('#'+txtid).val(texdesc);
                $('#'+txt_id2).val(txtval);
                $('#'+txt_id3).val(txtname);                
                $('#'+txt_id4).val(txtmuom);
                $('#'+txt_id5).val(txtmuomid);
                $('#'+txt_id6).val(txtspec);
               
                
                $('#'+txt_id8).val(txtmuomqty);
                
                $('#hdn_ItemID').val('');
                $('#hdn_ItemID2').val('');
                $('#hdn_ItemID3').val('');
                $('#hdn_ItemID4').val('');
                $('#hdn_ItemID5').val('');
                $('#hdn_ItemID6').val('');
                $('#hdn_ItemID7').val('');
               
                $('#hdn_ItemID11').val('');
                
                }

                $('.js-selectall1').prop('checked', false); 
                $("#ITEMIDpopup").hide();
                event.preventDefault();
 }
 else if($(this).is(":checked") == false) 
 {
   var id = txtval;
   var r_count = $('#Row_Count1').val();
   $('#example2').find('.participantRow').each(function()
   {
     var itemid = $(this).find('[id*="ITEMID_REF"]').val();
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
          $('.js-selectall1').prop('checked', false); 
          $("#ITEMIDpopup").hide();
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
  $("#Itemcodesearch").val(''); 
  $("#Itemnamesearch").val(''); 
  $("#ItemUOMsearch").val(''); 
  $("#ItemGroupsearch").val(''); 
  $("#ItemCategorysearch").val(''); 
  $("#ItemStatussearch").val(''); 
  $('.remove').removeAttr('disabled'); 
  ItemCodeFunction();
  event.preventDefault();
});
}

    
  //Item ID Dropdown Ends
//------------------------


$("#Material").on('click','.add', function() {  

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
	  var name = el.attr('name') || null;
	if(name){
	  var nameLength = name.split('_').pop();
	  var i = name.substr(name.length-nameLength.length);
	  var prefix1 = name.substr(0, (name.length-nameLength.length));
	  el.attr('name', prefix1+(+i+1));
	}
});

        $clone.find('input:text').val('');
        $clone.find('[id*="RFQID"]').val('');
        $clone.find('[id*="RFQID_REF"]').val('');
        $clone.find('[id*="PINO"]').val('');
        $clone.find('[id*="ITEMID_REF"]').val('');
        $tr.closest('table').append($clone);         
        var rowCount1 = $('#Row_Count1').val();
		    rowCount1 = parseInt(rowCount1)+1;
        
        $('#Row_Count1').val(rowCount1);
        $clone.find('.remove').removeAttr('disabled'); 
        
        event.preventDefault();
    });

    $("#Material").on('click', '.remove', function() {
        var rowCount = $(this).closest('table').find('.participantRow').length;
        if (rowCount > 1) {
            $(this).closest('.participantRow').remove();  
            var rowCount1 = $('#Row_Count1').val();
            rowCount1 = parseInt(rowCount1)-1;
            $('#Row_Count1').val(rowCount1);
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


    

   

$(document).ready(function(e) {

   var today = new Date(); 
   var currentdate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
   $('#VIINFODT').attr('min',currentdate);
   $('#VIINFODT').val(currentdate);

    var Material = $("#Material").html(); 
    $('#hdnmaterial').val(Material);    

    var d = new Date(); 
    var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    d.setDate(d.getDate() + 29);
    var todate = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;

    $('#VIINFODT').val(today);
    $('#OVFDT').val(today);
    $('#OVTDT').val(todate);
    $('#VENDOR_QDT').val(today);    
  
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
      window.location.href = "{{route('master',[$FormId,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){

     

   }//fnUndoNo

});

var formTrans = $("#frm_trn_add");

formTrans.validate();

$( "#btnSaveFormData" ).click(function() {

  if(formTrans.valid()){
    validateForm();
  }
});



function validateForm(){  
 
    $("#FocusId").val('');
    var VIINFONO           =   $.trim($("#VIINFONO").val());
    var VIINFODT           =   $.trim($("#VIINFODT").val());    
    var VID_REF       =   $.trim($("#VID_REF").val());

    if(VIINFONO ===""){
        $("#FocusId").val("VIINFONO");        
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please enter value in document no.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }
    else if(VIINFODT ===""){
        $("#FocusId").val("VIINFODT");        
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select document date.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    } 
    
    
    else if(VID_REF ===""){
        $("#FocusId").val("txtvendor_popup");
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select vendor.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }    
     
     
    else{
      event.preventDefault();
          var allblank1 = [];
          var allblank2 = [];
          var allblank3 = [];
          var allblank4 = [];
          var allblank5 = [];
          var allblank6 = [];
          var allblank7 = [];
          var allblank8 = [];
          var allblank9 = [];
          var allblank10 = [];
          var allblank11 = [];
          var allblank12 = [];

          var focustext1= "";
          var focustext2= "";
          var focustext3= "";
          var focustext4= "";
          var focustext5= "";
          var focustext6= "";
          var focustext7= "";
          var focustext8= "";
          var focustext9= "";
          var focustext10= "";
          var focustext11= "";
          var focustext12= "";

            
    $('#example2').find('.participantRow').each(function(){
 
      if($.trim($(this).find("[id*=RFQID]").val()) ==""){
        allblank1.push('false');
        focustext1 = $(this).find("[id*=txtRFQ_popup]").attr('id');
      }
      else if($.trim($(this).find("[id*=ITEMID_REF]").val()) ==""){
        allblank2.push('false');
        focustext2 = $(this).find("[id*=popupITEMID]").attr('id');
      }

      else if($.trim($(this).find("[id*=EOQ]").val()) ==""){
        allblank3.push('false');
        focustext3 = $(this).find("[id*=EOQ]").attr('id');
      }

      else if($.trim($(this).find("[id*=LEADDAYS]").val()) ==""){
        allblank4.push('false');
        focustext4 = $(this).find("[id*=LEADDAYS]").attr('id');
      }

      

 
  });

     
  if(jQuery.inArray("false", allblank1) !== -1){
    $("#MAT_TAB").click();
    $("#FocusId").val(focustext1);
    $("#alert").modal('show');
    $("#AlertMessage").text('Please select item group.');
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
    $("#AlertMessage").text('Please select item code.');
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
    $("#AlertMessage").text('Please enter EOQ.');
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
    $("#AlertMessage").text('Please enter Lead Days.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    }

    

    
    else{
      checkDuplicateCode();
    } 

    }

}



function checkDuplicateCode(){

var trnFormReq = $("#frm_trn_add");
var formData = trnFormReq.serialize();

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$.ajax({
    url:'{{route("master",[$FormId,"codeduplicate"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
        if(data.exists) {
            $(".text-danger").hide();
            showError('ERROR_VIINFONO',data.msg);
            $("#VIINFONO").focus();
        }
        else{
          $("#alert").modal('show');
          $("#AlertMessage").text('Do you want to save to record.');
          $("#YesBtn").data("funcname","fnSaveData");
          $("#YesBtn").focus();
          $("#OkBtn").hide();
          highlighFocusBtn('activeYes');
        }                                
    },
    error:function(data){
      console.log("Error: Something went wrong.");
    },
});
}






$("#YesBtn").click(function(){

var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

window.fnSaveData = function (){

event.preventDefault();

    var trnsoForm = $("#frm_trn_add");
    var formData = trnsoForm.serialize();

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("master",[$FormId,"save"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
       
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.VIINFONO){
                showError('ERROR_VIINFONO',data.errors.VIINFONO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in VD No.');
                        $("#alert").modal('show');
                        $("#OkBtn1").focus();
            }
           if(data.country=='norecord') {

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
        $("#AlertMessage").text('Error: Something went wrongsssssssssss.');
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

function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}

function onlyNumberKey(evt) {
          
          var ASCIICode = (evt.which) ? evt.which : evt.keyCode
          if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
              return false;
          return true;
      }




  function doCalculation(){
    $(".blurRate").blur();
  }

  function showAlert(msg){
    $("#alert").modal('show');
    $("#AlertMessage").text(msg);
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    return false;
  }


function showSelectedCheck(hidden_value,selectAll){

  var divid ="";

  if(hidden_value !=""){

      var all_location_id = document.querySelectorAll('input[name="'+selectAll+'[]"]');
      
      for(var x = 0, l = all_location_id.length; x < l;  x++){
      
          var checkid=all_location_id[x].id;
          var checkval=all_location_id[x].value;
      
          if(hidden_value == checkval){
          divid = checkid;
          }

          $("#"+checkid).prop('checked', false);
          
      }
  }

  if(divid !=""){
      $("#"+divid).prop('checked', true);
  }
}
</script>


@endpush