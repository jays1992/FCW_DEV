
@extends('layouts.app')
@section('content')
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[237,'index'])}}" class="btn singlebt">Custom Duty & SWS Rate</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSave" ><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" {{($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div>
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->
    <div class="container-fluid purchase-order-view">
        <form id="frm_trn_custom_duty"  method="POST">   
            @csrf
            {{isset($objCUSTOM->VCDID[0]) ? method_field('PUT') : '' }}
            <div class="container-fluid filter">

            <div class="container-fluid filter">
                    <div class="inner-form">
                        <div class="row">
                            <div class="col-lg-2 pl"><p>Doc No</p></div>
                            <div class="col-lg-2 pl">
         
                           
                  
                                <input type="text" name="DOCNO" onkeypress="return AlphaNumaric(event,this)" id="DOCNO" readonly value="{{ $objCUSTOM->VCD_DOCNO }}" class="form-control mandatory"  autocomplete="off" style="text-transform:uppercase" autofocus >
                                                     
                            </div>
                            <div class="col-lg-2 pl"><p>Date</p></div>
                            <div class="col-lg-2 pl">
                                <input type="date" name="CD_DT" id="CD_DT" value="{{ $objCUSTOM->VCD_DOCDT }}" class="form-control mandatory" autocomplete="off"  placeholder="dd/mm/yyyy" >
                               
                            </div>    
                      
                            
                        </div>     

                        <div class="row">
                        <div class="col-lg-2 pl"><p>Validity From</p></div>
                            <div class="col-lg-2 pl">
                                <input type="date" name="VALIDITY_FROM" id="VALIDITY_FROM" value="{{ $objCUSTOM->FROM_DT }}" class="form-control mandatory" autocomplete="off"  placeholder="dd/mm/yyyy" >
                               
                            </div>    
                            <div class="col-lg-2 pl"><p>Validity To</p></div>
                            <div class="col-lg-2 pl">
                                <input type="date" name="VALIDITY_TO" id="VALIDITY_TO" value="{{ $objCUSTOM->TO_DT }}" class="form-control mandatory" autocomplete="off"  placeholder="dd/mm/yyyy" >
                               
                            </div>    
                      
                            
                        </div>     




              <div class="row" >
                <div class="col-lg-2 pl" ><p>De-Activated</p></div>
                <div class="col-lg-2 pl pl">
                <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objCUSTOM->DEACTIVATED == 1 ? "checked" : ""}}
                 value='{{$objCUSTOM->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
                </div>
                
                <div class="col-lg-2 pl" ><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objCUSTOM->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objCUSTOM->DODEACTIVATED) && $objCUSTOM->DODEACTIVATED !="" && $objCUSTOM->DODEACTIVATED !="1900-01-01" ? $objCUSTOM->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
                </div>
             </div>

                    </div>          
                        </div>                 


                    <div class="container-fluid purchase-order-view">

                        <div class="row">
                    
                            <div class="tab-content">
                            

                                <div id="MaterialCustom" class="tab-pane fade in active">
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:380px;margin-top:10px;" >
                                        <table id="example3" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                                            <thead id="thead2"  style="position: sticky;top: 0">
                                                <tr>
                                                    <th>Vendor Code<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2"></th>
                                                    <th  hidden>VENDORID_REF</th>
                                                    <th>Vendor Name</th>
                                                    <th>Item Code</th>
                                                    <th  hidden>MainItemId1_Ref</th>
                                                    <th>Item Name</th>
                                                    <th>Normal BCD %</th>
                                                    <th>Cess on Normal BCD %</th>                                              
                                                    <th>FTA BCD %</th>
                                                    <th>CEPA BCD %</th>
                                                    <th>SWS Rate %</th>
                                                    <th>TAX %</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @if(!empty($objCUSTOMMAT))
                                           @foreach($objCUSTOMMAT as $key => $row)    
                                            <tr  class="participantRow1">
                                                    <td><input type="text" name={{"VENDOR_CODE_".$key}} id ={{"VENDOR_CODE_".$key}}  value="{{ $row->VCODE }}" onclick="get_vendor($(this).attr('id'))" class="form-control"  autocomplete="off"  readonly/></td>                                                
                                                    <td   hidden><input type="text" name={{"VENDORID_REF_".$key}} id ={{"VENDORID_REF_".$key}}  value="{{ $row->VID_REF }}"  class="form-control" autocomplete="off" />
                                                    </td>
                                                    <td><input type="text" style="width: 243px;" name={{"VENDOR_NAME_".$key}} id ={{"VENDOR_NAME_".$key}}  value="{{ $row->VENDOR_NAME }}"   class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td><input type="text" name={{"MainItemCode1_".$key}} id ={{"MainItemCode1_".$key}}  value="{{ $row->ICODE }}"  onclick="get_item($(this).attr('id'))" class="form-control"  autocomplete="off"  readonly/></td>                                                
                                                    <td  hidden><input type="text" name={{"MainItemId1_Ref_".$key}} id ={{"MainItemId1_Ref_".$key}}  value="{{ $row->ITEMID_REF }}"   class="form-control" autocomplete="off" /></td>
                                                    <td><input type="text" name={{"MainItemName1_".$key}} id ={{"MainItemName1_".$key}}  value="{{ $row->NAME }}"   class="form-control"  autocomplete="off"  readonly/></td>
                                                                                     
                                                   
                                                    <td><input type="text" name={{"NORMAL_BCD_".$key}} id ={{"NORMAL_BCD_".$key}}  value="{{ $row->BCD!='.0000'? $row->BCD:'' }}"  maxlength="8" class="form-control"  autocomplete="off"  /></td>
                                                    <td><input type="text" name={{"CESS_NORMAL_BCD_".$key}} id ={{"CESS_NORMAL_BCD_".$key}}  value="{{ $row->CESS_BCD!='.0000'? $row->CESS_BCD:'' }}"  maxlength="8" class="form-control"  autocomplete="off"  /></td>
                                                    <td><input type="text" name={{"FTA_BCD_".$key}} id ={{"FTA_BCD_".$key}}  value="{{ $row->FTA_BCD!='.0000'? $row->FTA_BCD:'' }}"  class="form-control" maxlength="8"  autocomplete="off"  /></td>
                                                    <td><input type="text" name={{"CEPA_BCD_".$key}} id ={{"CEPA_BCD_".$key}}  value="{{ $row->CEPA_BCD!='.0000'? $row->CEPA_BCD:'' }}"  class="form-control" maxlength="8"  autocomplete="off"  /></td>
                                                    <td><input type="text" name={{"SW_RATE_".$key}} id ={{"SW_RATE_".$key}}  value=" {{ $row->SWS!='.0000'? $row->SWS:'' }}"  class="form-control"  maxlength="8" autocomplete="off"  /></td>
                                                    <td><input type="text" name={{"TAX_".$key}} id ={{"TAX_".$key}}  value="{{ $row->TAX!='.0000'? $row->TAX:'' }}"  class="form-control" maxlength="8"  autocomplete="off"  /></td>
                                                    <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button class="btn remove smaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                                                
                                                </tr>
                                                @endforeach          

                                              @else 
                                              <tr  class="participantRow1">
                                                    <td><input type="text" name="VENDOR_CODE_0" id="VENDOR_CODE_0" onclick="get_vendor($(this).attr('id'))" class="form-control"  autocomplete="off"  readonly/></td>                                                
                                                    <td hidden><input type="text" name="VENDORID_REF_0" id="VENDORID_REF_0" class="form-control" autocomplete="off" />
                                                    </td>
                                                    <td><input type="text" style="width: 243px;" name="VENDOR_NAME_0" id="VENDOR_NAME_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td><input type="text" name="MainItemCode1_0" id="MainItemCode1_0" onclick="get_item($(this).attr('id'))" class="form-control"  autocomplete="off"  readonly/></td>                                                
                                                    <td hidden><input type="hidden" name="MainItemId1_Ref_0" id="MainItemId1_Ref_0" class="form-control" autocomplete="off" /></td>
                                                    <td><input type="text" name="MainItemName1_0" id="MainItemName1_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                                                     
                                                   
                                                    <td><input type="text" name="NORMAL_BCD_0" id="NORMAL_BCD_0" maxlength="8" class="form-control"  autocomplete="off"  /></td>
                                                    <td><input type="text" name="CESS_NORMAL_BCD_0" id="CESS_NORMAL_BCD_0" maxlength="8" class="form-control"  autocomplete="off"  /></td>
                                                    <td><input type="text" name="FTA_BCD_0" id="FTA_BCD_0" class="form-control" maxlength="8"  autocomplete="off"  /></td>
                                                    <td><input type="text" name="CEPA_BCD_0" id="CEPA_BCD_0" class="form-control" maxlength="8"  autocomplete="off"  /></td>
                                                    <td><input type="text" name="SW_RATE_0" id="SW_RATE_0" class="form-control"  maxlength="8" autocomplete="off"  /></td>
                                                    <td><input type="text" name="TAX_0" id="TAX_0" class="form-control" maxlength="8"  autocomplete="off"  /></td>
                                                    <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button class="btn remove smaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                                                
                                                </tr>


                                              @endif 
                                            </tbody>
                                    </table>
                                    </div>	
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
<div id="mainitempopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md"  style="width:90%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='item_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Main Item List</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="MItemTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>
      <tr id="none-select" class="searchalldata" hidden>              
        <td>
          <input type="text" name="mfieldid" id="hdn_MItemID"/>
          <input type="text" name="mfieldid2" id="hdn_MItemID2"/>
          <input type="text" name="mfieldid3" id="hdn_MItemID3"/>
          <input type="text" name="mfieldid4" id="hdn_MItemID4"/>
          <input type="text" name="mfieldid5" id="hdn_MItemID5"/>
        </td>
      </tr>
      <tr>
        <th style="width:5%;text-align:center;" id="all-check_prodcode_item">Select</th>
        <th style="width:10%;">Product Code</th>
        <th style="width:15%;">Product Description</th>
        <th style="width:10%;">UOM</th>
        <th style="width:10%;">Business Unit</th>
        <th style="width:10%;">ALPS Part No.</th>
        <th style="width:10%;">Customer Part No.</th>
        <th style="width:10%;">OEM Part No.</th>
        <th style="width:10%;">Drawing No</th>
        <th style="width:10%;">Part No</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td style="width:5%;text-align:center;"><span class="check_th">&#10004;</span></td>
      <td style="width:10%;"><input type="text" id="MItemCodeSearch" class="form-control" onkeyup="MItemCodeFunction('{{$FormId}}')" ></td>
      <td style="width:15%;"><input type="text" id="MItemNameSearch" class="form-control" onkeyup="MItemNameFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="MItemUOMSearch"  class="form-control" onkeyup="MItemUOMFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="MItemBUsearch" class="form-control" onkeyup="MItemBUFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="MItemAPNsearch" class="form-control" onkeyup="MItemAPNFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="MItemCPNsearch" class="form-control"  onkeyup="MItemCPNFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="MItemOEMPNsearch" class="form-control" onkeyup="MItemOEMPNFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="MItemDrawingNoSearch" class="form-control" onkeyup="MItemDrawingNoFunction('{{$FormId}}')" ></td>
      <td style="width:10%;"><input type="text" id="MItemPartNoSearch" class="form-control"  onkeyup="MItemPartNoFunction('{{$FormId}}')"></td>
    </tr>
    </tbody>
    </table>
      <table id="MItemTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
        </thead>
        <tbody id="tbody_main_item">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Item popup ends-->
<!--vendor popup new starts-->
<div id="vendoridpopup" class="modal" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='vendor_close_popup'>&times;</button>
      </div>
      <div class="modal-body">
        <div class="tablename">
          <p>Vendor Details</p>
        </div>
        <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="VendorCodeTable" class="display nowrap table  table-striped table-bordered">
            <thead>
              <tr id="none-select" class="searchalldata" hidden>              
                <td>
                  <input type="text" name="vendorfieldid" id="hdn_VID"/>
                  <input type="text" name="vendorfieldid2" id="hdn_VID2"/>
                  <input type="text" name="vendorfieldid3" id="hdn_VID3"/>
                  <input type="text" name="vendorfieldid4" id="hdn_VID4"/>
                  <input type="text" name="vendorfieldid5" id="hdn_VID5"/>
                </td>
              </tr>
              <tr>
                <th class="ROW1">Select</th>
                <th class="ROW2">Code</th>
                <th class="ROW3">Description</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th class="ROW1"><span class="check_th">&#10004;</span></th>
                <td class="ROW2"><input type="text" id="vendorcodesearch" class="form-control" onkeyup="VendorCodeFunction()"></td>
                <td class="ROW3"><input type="text" id="vendornamesearch" class="form-control" onkeyup="VendorNameFunction()"></td>
              </tr>
            </tbody>
          </table>
          <table id="VendorCodeTable2" class="display nowrap table  table-striped table-bordered">
            <thead id="thead2">
            </thead>
            <tbody id="tbody_vendor">
            </tbody>
          </table>
        </div>
        <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

{{-- vendor new end--}}

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
   
    font-weight: 700;
}

#ItemIDTable2 {
  border-collapse: collapse;
  width: 1182px;
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

//------------------------
 

  $( "#btnSave" ).click(function() {
  var formCustomDuty = $("#frm_trn_custom_duty");
  if(formCustomDuty.valid()){

    $("#FocusId").val('');

var DOCNO           =   $.trim($("#DOCNO").val());
var CD_DT          =   $.trim($("#CD_DT").val());
var VALIDITY_FROM          =   $.trim($("#VALIDITY_FROM").val());
var VFRDT   =   $.trim($("#VALIDITY_FROM").val());
var VTODT   =   $.trim($("#VALIDITY_TO").val());
var PRODUCT_CODE          =   $.trim($("#PRODUCT_CODE").val());
var PRODUCEQTY       =   $.trim($("#PRODUCEQTY").val());
var DODEACTIVATED          =   $.trim($("#DODEACTIVATED").val());

if(DOCNO ===""){
    $("#FocusId").val($("#DOCNO"));
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter value in DOCNO.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
}
else if(CD_DT ===""){
    $("#FocusId").val($("#CD_DT"));
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
}
else if(VFRDT ===""){
    $("#FocusId").val($("#VALIDITY_FROM"));
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Validity From.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
}

else if ($('input[name="DEACTIVATED"]:checked').length != 0 &&  DODEACTIVATED =="" ) {
    $("#FocusId").val($("#DESCRIPTIONS"));
     $("#DESCRIPTIONS").val();  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Date of De-Activation.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }

else{
   event.preventDefault();


if(new Date(VFRDT)>new Date(VTODT) && VTODT!=''){
 $("#FocusId").val($("#VFRDT"));
 $("#VFRDT").val('');            
 $("#YesBtn").hide();
 $("#NoBtn").hide();
 $("#OkBtn1").show();
 $("#AlertMessage").text('Validity From Date must be less than Validity To Date.');
 $("#alert").modal('show');
 $("#OkBtn1").focus();
 return false;
}

   var allblank = [];
   var allblank2 = [];
   var allblank3 = [];

       // $('#udfforsebody').find('.form-control').each(function () {
       $('#example3').find('.participantRow1').each(function(){
           if($.trim($(this).find("[id*=VENDORID_REF]").val())!="")
           {
               allblank.push('true');
           
        
           }
           else
           {
               allblank.push('false');
           }

           if($.trim($(this).find("[id*=MainItemCode1]").val())!="")
           {
               allblank2.push('true');
           
        
           }
           else
           {
               allblank2.push('false');
           }
           if($.trim($(this).find("[id*=NORMAL_BCD]").val())!="")
           {
               allblank3.push('true');
           
        
           }
           else
           {
               allblank3.push('false');
           }

       });


               
       if(jQuery.inArray("false", allblank) !== -1){
               $("#alert").modal('show');
               $("#AlertMessage").text('Please select Vendor.');
               $("#YesBtn").hide(); 
               $("#NoBtn").hide();  
               $("#OkBtn1").show();
               $("#OkBtn1").focus();
               highlighFocusBtn('activeOk');
               return false;
           }
           else if(jQuery.inArray("false", allblank2) !== -1){
           $("#alert").modal('show');
           $("#AlertMessage").text('Please select Item.');
           $("#YesBtn").hide(); 
           $("#NoBtn").hide();  
           $("#OkBtn1").show();
           $("#OkBtn1").focus();
           highlighFocusBtn('activeOk');
           return false;
           }            
           else if(jQuery.inArray("false", allblank3) !== -1){
           $("#alert").modal('show');
           $("#AlertMessage").text('Please Enter Value in Normal BCD');
           $("#YesBtn").hide(); 
           $("#NoBtn").hide();  
           $("#OkBtn1").show();
           $("#OkBtn1").focus();
           highlighFocusBtn('activeOk');
           return false;
           }

           else{
               $("#alert").modal('show');
               $("#AlertMessage").text('Do you want to update the record.');
               $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
               $("#YesBtn").focus();
               $("#OkBtn").hide();
               highlighFocusBtn('activeYes');
           }
}

    }
});

$( "#btnApprove" ).click(function() {
  var formCustomDuty = $("#frm_trn_custom_duty");
  if(formCustomDuty.valid()){
 
    $("#FocusId").val('');

var DOCNO           =   $.trim($("#DOCNO").val());
var CD_DT          =   $.trim($("#CD_DT").val());
var VALIDITY_FROM          =   $.trim($("#VALIDITY_FROM").val());
var VFRDT   =   $.trim($("#VALIDITY_FROM").val());
var VTODT   =   $.trim($("#VALIDITY_TO").val());
var PRODUCT_CODE          =   $.trim($("#PRODUCT_CODE").val());
var PRODUCEQTY       =   $.trim($("#PRODUCEQTY").val());
var DODEACTIVATED          =   $.trim($("#DODEACTIVATED").val());

if(DOCNO ===""){
    $("#FocusId").val($("#DOCNO"));
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter value in DOCNO.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
}
else if(CD_DT ===""){
    $("#FocusId").val($("#CD_DT"));
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
}
else if(VFRDT ===""){
    $("#FocusId").val($("#VALIDITY_FROM"));
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Validity From.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
}

else if ($('input[name="DEACTIVATED"]:checked').length != 0 &&  DODEACTIVATED =="" ) {
    $("#FocusId").val($("#DESCRIPTIONS"));
     $("#DESCRIPTIONS").val();  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Date of De-Activation.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }

else{
   event.preventDefault();


if(new Date(VFRDT)>new Date(VTODT) && VTODT!=''){
 $("#FocusId").val($("#VFRDT"));
 $("#VFRDT").val('');            
 $("#YesBtn").hide();
 $("#NoBtn").hide();
 $("#OkBtn1").show();
 $("#AlertMessage").text('Validity From Date must be less than Validity To Date.');
 $("#alert").modal('show');
 $("#OkBtn1").focus();
 return false;
}

   var allblank = [];
   var allblank2 = [];
   var allblank3 = [];

       // $('#udfforsebody').find('.form-control').each(function () {
       $('#example3').find('.participantRow1').each(function(){
           if($.trim($(this).find("[id*=VENDORID_REF]").val())!="")
           {
               allblank.push('true');
           
        
           }
           else
           {
               allblank.push('false');
           }

           if($.trim($(this).find("[id*=MainItemCode1]").val())!="")
           {
               allblank2.push('true');
           
        
           }
           else
           {
               allblank2.push('false');
           }
           if($.trim($(this).find("[id*=NORMAL_BCD]").val())!="")
           {
               allblank3.push('true');
           
        
           }
           else
           {
               allblank3.push('false');
           }

       });


               
       if(jQuery.inArray("false", allblank) !== -1){
               $("#alert").modal('show');
               $("#AlertMessage").text('Please select Vendor.');
               $("#YesBtn").hide(); 
               $("#NoBtn").hide();  
               $("#OkBtn1").show();
               $("#OkBtn1").focus();
               highlighFocusBtn('activeOk');
               return false;
           }
           else if(jQuery.inArray("false", allblank2) !== -1){
           $("#alert").modal('show');
           $("#AlertMessage").text('Please select Item.');
           $("#YesBtn").hide(); 
           $("#NoBtn").hide();  
           $("#OkBtn1").show();
           $("#OkBtn1").focus();
           highlighFocusBtn('activeOk');
           return false;
           }            
           else if(jQuery.inArray("false", allblank3) !== -1){
           $("#alert").modal('show');
           $("#AlertMessage").text('Please Enter Value in Normal BCD');
           $("#YesBtn").hide(); 
           $("#NoBtn").hide();  
           $("#OkBtn1").show();
           $("#OkBtn1").focus();
           highlighFocusBtn('activeOk');
           return false;
           }

           else{
            $("#alert").modal('show');
                $("#AlertMessage").text('Do you want to Approve the record.');
                $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name
                $("#YesBtn").focus();
                $("#OkBtn").hide();
                highlighFocusBtn('activeYes');
           }
}

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

     var trnosoForm = $("#frm_trn_custom_duty");
    var formData = trnosoForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("mastermodify",[237,"update"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
       
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.SONO){
                showError('ERROR_SONO',data.errors.SONO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in SONO.');
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
        $("#AlertMessage").text('Error: Something went wrong.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk1');
    },
});

}

window.fnApproveData = function (){

//validate and save data
event.preventDefault();

     var trnosoForm = $("#frm_trn_custom_duty");
    var formData = trnosoForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("mastermodify",[237,"Approve"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
       
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.SONO){
                showError('ERROR_SONO',data.errors.SONO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in SONO.');
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
    $("#SONO").focus();
});

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("master",[237,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
    // $("[id*=txtlabel]").focus();
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
//SO Date Check
$('#CD_DT').change(function( event ) {
            var today = new Date();     
            var d = new Date($(this).val()); 
            today.setHours(0, 0, 0, 0) ;
            d.setHours(0, 0, 0, 0) ;
            var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
            if (d < today) {
                $(this).val(sodate);
                $("#alert").modal('show');
                $("#AlertMessage").text('Date cannot be less than Current date');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                event.preventDefault();
            }
            else
            {
                event.preventDefault();
            }

           
        });
        
//delete row

$("#MaterialCustom").on('click', '.remove', function() {

        var rowCount = $(this).closest('table').find('.participantRow1').length;
        if (rowCount > 1) {
        $(this).closest('.participantRow1').remove();     
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


    $("#MaterialCustom").on('click', '.add', function() {
 
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('.participantRow1').last();
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
        $clone.find('[id*="ITEMID_REF"]').val('');
        $tr.closest('table').append($clone);         
        var rowCount1 = $('#Row_Count2').val();
		    rowCount1 = parseInt(rowCount1)+1;
        $('#Row_Count2').val(rowCount1);
        $clone.find('.remove').removeAttr('disabled'); 

        $("[id*='NORMAL_BCD']").ForceNumericOnly();
        $("[id*='CESS_NORMAL_BCD']").ForceNumericOnly();
        $("[id*='FTA_BCD']").ForceNumericOnly();
        $("[id*='CEPA_BCD']").ForceNumericOnly();
        $("[id*='SW_RATE']").ForceNumericOnly();
        $("[id*='TAX']").ForceNumericOnly();
        event.preventDefault();
    });


  


function get_vendor(id){

    var result = id.split('_');
    var id_number=result[2];
    var popup_id='#'+id;

    var CODE = '';
    var NAME = '';
    loadVendor(CODE, NAME);

    $("#vendoridpopup").show();
    event.preventDefault();

    var id =  "VENDORID_REF_"+id_number;
    var id2 =  "VENDOR_CODE_"+id_number;
    var id3 =  "VENDOR_NAME_"+id_number;

    $('#hdn_VID').val(id);
    $('#hdn_VID2').val(id2);
    $('#hdn_VID3').val(id3);
    return false;
}


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

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
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
return false;
});



window.fnUndoYes = function (){
//reload form
window.location.reload();
}//fnUndoYes


window.fnUndoNo = function (){
$("#BOMNO").focus();
}//fnUndoNo

$(document).ready(function(e) {

  var count2 = <?php echo json_encode($objCount2); ?>;
  $('#Row_Count2').val(count2);

$("[id*='NORMAL_BCD']").ForceNumericOnly();


$('#example3').on('blur','[id*="NORMAL_BCD"]',function(){

          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.0000')
          }
          event.preventDefault();
      });

      $("[id*='CESS_NORMAL_BCD']").ForceNumericOnly();
      $('#example3').on('blur','[id*="CESS_NORMAL_BCD"]',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.0000')
          }
          event.preventDefault();
      });
      $("[id*='FTA_BCD']").ForceNumericOnly();
      $('#example3').on('blur','[id*="FTA_BCD"]',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.0000')
          }
          event.preventDefault();
      });

      $("[id*='CEPA_BCD']").ForceNumericOnly();
$('#example3').on('blur','[id*="CEPA_BCD"]',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.0000')
          }
          event.preventDefault();
      });
      $("[id*='SW_RATE']").ForceNumericOnly();
      $('#example3').on('blur','[id*="SW_RATE"]',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.0000')
          }
          event.preventDefault();
      });
      $("[id*='TAX']").ForceNumericOnly();
      $('#example3').on('blur','[id*="TAX"]',function(){
          if(intRegex.test($(this).val())){
           $(this).val($(this).val()+'.0000')
          }
          event.preventDefault();
      });
      });

function AlphaNumaric(e, t) {
      try {
      if (window.event) {
        var charCode = window.event.keyCode;
      }
      else if (e) {
        var charCode = e.which;
      }
      else { return true; }
      if ((charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122))
      return true;
      else
      return false;
      }
      catch (err) {
      alert(err.Description);
      }
}
//---------------
// START VENDOR CODE FUNCTION
let vendor_tid = "#VendorCodeTable2";
  let vendor_tid2 = "#VendorCodeTable";
  let vendor_headers = document.querySelectorAll(vendor_tid2 + " th");


  vendor_headers.forEach(function (element, i) {
    element.addEventListener("click", function () {
      w3.sortHTML(vendor_tid, ".clsvendorid", "td:nth-child(" + (i + 1) + ")");
    });
  });

  function VendorCodeFunction() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("vendorcodesearch");
    filter = input.value.toUpperCase();
    if (filter.length == 0) {
      var CODE = '';
      var NAME = '';
      loadVendor(CODE, NAME);
    } else if (filter.length >= 3) {
      var CODE = filter;
      var NAME = '';
      loadVendor(CODE, NAME);
    } else {
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
    if (filter.length == 0) {
      var CODE = '';
      var NAME = '';
      loadVendor(CODE, NAME);
    } else if (filter.length >= 3) {
      var CODE = '';
      var NAME = filter;
      loadVendor(CODE, NAME);
    } else {
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

  function loadVendor(CODE, NAME) {

    $("#tbody_vendor").html('');
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      url: '{{ route("master",[237,"getVendor"]) }}',
      type: 'POST',
      data: {
        'CODE': CODE,
        'NAME': NAME
      },
      success: function (data) {
        $("#tbody_vendor").html(data);
        bindVendorEvents();
        showSelectedCheck($("#VID_REF").val(), "SELECT_VID_REF");
      },
      error: function (data) {
        console.log("Error: Something went wrong.");
        $("#tbody_vendor").html('');
      },
    });
  }

  $('#txtvendor_popup').click(function (event) {


    var CODE = '';
    var NAME = '';
    loadVendor(CODE, NAME);

    $("#vendoridpopup").show();
    event.preventDefault();
  });

  $("#vendor_close_popup").click(function (event) {
    $("#vendoridpopup").hide();
    event.preventDefault();
  });



  function bindVendorEvents() {

    $('[id*="chkVendorId"]').change(function(){

      if( $(this).is(":checked") == true) {

        var fieldid = $(this).parent().parent().attr('id');
        //var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        //var txtdesc =   $("#txt"+fieldid+"").data("desc");
        var txtvcode =   $("#txt"+fieldid+"").data("vcode");
        var txtname =   $("#txt"+fieldid+"").data("vname");

        var txtid=  $('#hdn_VID').val();
        var txt_id2=$('#hdn_VID2').val();
        var txt_id3=$('#hdn_VID3').val();

        $('#'+txtid).val(txtval);
        $('#'+txt_id2).val(txtvcode);
        $('#'+txt_id3).val(txtname);

        $('#vendorcodesearch').val('');
        $('#vendornamesearch').val('');

        $(this).prop("checked",false);
        $("#vendoridpopup").hide();              

        $('#'+txtid).parent().parent().find('[id*="MainItemCode1_"]').val('');
        $('#'+txtid).parent().parent().find('[id*="MainItemId1_Ref_"]').val('');
        $('#'+txtid).parent().parent().find('[id*="MainItemName1_"]').val('');
        $('#'+txtid).parent().parent().find('[id*="NORMAL_BCD_"]').val('');
        $('#'+txtid).parent().parent().find('[id*="CESS_NORMAL_BCD_"]').val('');
        $('#'+txtid).parent().parent().find('[id*="FTA_BCD_"]').val('');
        $('#'+txtid).parent().parent().find('[id*="CEPA_BCD_"]').val('');
        $('#'+txtid).parent().parent().find('[id*="SW_RATE_"]').val('');
        $('#'+txtid).parent().parent().find('[id*="TAX_"]').val('');

      } 
  });

  }


  function showSelectedCheck(hidden_value, selectAll) {

    var divid = "";

    if (hidden_value != "") {

      var all_location_id = document.querySelectorAll('input[name="' + selectAll + '[]"]');

      for (var x = 0, l = all_location_id.length; x < l; x++) {

        var checkid = all_location_id[x].id;
        var checkval = all_location_id[x].value;

        if (hidden_value == checkval) {
          divid = checkid;
        }

        $("#" + checkid).prop('checked', false);

      }
    }

    if (divid != "") {
      $("#" + divid).prop('checked', true);
    }
  }

  // -------------

// -------------
function loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO){
	
  var url	=	'<?php echo asset('');?>master/'+FORMID+'/getItemDetails_main_item';

    $("#tbody_main_item").html('loading...');
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $.ajax({
      url:url,
      type:'POST',
      data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART,'DRAWNO':DRAWNO,'PARTNO':PARTNO},
      success:function(data) {
      $("#tbody_main_item").html(data); 
      bindMainItem();

      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_main_item").html('');                        
      },
    });

}

function get_item(id){   

      var result = id.split('_');
      var id_number=result[1];
      var VENDORID='#VENDOR_CODE_'+id_number;
      var VENDOR_CODE = $(VENDORID).val();
      if(VENDOR_CODE==''){

          $(VENDORID).focus();
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please select vendor first.');
          $("#alert").modal('show');
          highlighFocusBtn('activeOk1');
          return false;

      }
     

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
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    var FORMID = "{{$FormId}}";
    var DRAWNO = '';
    var PARTNO = '';
    loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
    
    $(".mainitem_tab").val(id_number);    
    $("#mainitempopup").show();


    var id =  "MainItemCode1_"+id_number;
    var id2 =  "MainItemId1_Ref_"+id_number;
    var id3 =  "MainItemName1_"+id_number;

    $('#hdn_MItemID').val(id);
    $('#hdn_MItemID2').val(id2);
    $('#hdn_MItemID3').val(id3);
      
 
}


//---------------------------------
//----------------------------------
//--new mainitem search
//--Search item popup for substitute Main items 

      let mainitem_tid = "#MItemTable2";
      let mainitem_tid2 = "#MItemTable";
      let mainitem_headers = document.querySelectorAll(mainitem_tid2 + " th");

      
      function MItemCodeFunction(FORMID) {
      
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("MItemCodeSearch");
        filter = input.value.toUpperCase();

        if(filter.length == 0)
        {
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
          var MUOM = ''; 
          var GROUP = ''; 
          var CTGRY = ''; 
          var BUNIT = ''; 
          var APART = ''; 
          var CPART = ''; 
          var OPART = ''; 
          var DRAWNO = ''; 
          var PARTNO = ''; 
          loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
        }
        else if(filter.length >= 3)
        {
          if ($('#Tax_State').length) 
          {
            var taxstate = $('#Tax_State').val();
          }
          else
          {
            var taxstate = '';
          }
          var CODE = filter; 
          var NAME = ''; 
          var MUOM = ''; 
          var GROUP = ''; 
          var CTGRY = ''; 
          var BUNIT = ''; 
          var APART = ''; 
          var CPART = ''; 
          var OPART = ''; 
          var DRAWNO = ''; 
          var PARTNO = ''; 
          loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
        }
        else
        {
          table = document.getElementById("MItemTable2");
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

    function MItemNameFunction(FORMID) {
      
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("MItemNameSearch");
      filter = input.value.toUpperCase();

      if(filter.length == 0)
      {
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
        var MUOM = ''; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART = ''; 
        var OPART = ''; 
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else if(filter.length >= 3)
      {
        if ($('#Tax_State').length) 
        {
          var taxstate = $('#Tax_State').val();
        }
        else
        {
          var taxstate = '';
        }
        var CODE = ''; 
        var NAME = filter; 
        var MUOM = ''; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART = ''; 
        var OPART = ''; 
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("MItemTable2");
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

    function MItemUOMFunction(FORMID) {
      
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("MItemUOMSearch");
      filter = input.value.toUpperCase();

      if(filter.length == 0)
      {
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
        var MUOM = ''; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART = ''; 
        var OPART = ''; 
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else if(filter.length >= 3)
      {
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
        var MUOM = filter; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART = ''; 
        var OPART = ''; 
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("MItemTable2");
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

    function MItemBUFunction(FORMID) {
      
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("MItemBUsearch");
      filter = input.value.toUpperCase();

      if(filter.length == 0)
      {
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
        var MUOM = ''; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART = ''; 
        var OPART = ''; 
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else if(filter.length >= 3)
      {
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
        var MUOM = ''; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = filter; 
        var APART = ''; 
        var CPART = ''; 
        var OPART = ''; 
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("MItemTable2");
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
    }

    function MItemAPNFunction(FORMID) {
      
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("MItemAPNsearch");
      filter = input.value.toUpperCase();

      if(filter.length == 0)
      {
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
        var MUOM = ''; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART = ''; 
        var OPART = ''; 
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else if(filter.length >= 3)
      {
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
        var MUOM = ''; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = ''; 
        var APART = filter; 
        var CPART = ''; 
        var OPART = ''; 
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("MItemTable2");
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

    function MItemCPNFunction(FORMID) {
      
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("MItemCPNsearch");
      filter = input.value.toUpperCase();

      if(filter.length == 0)
      {
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
        var MUOM = ''; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART = ''; 
        var OPART = ''; 
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else if(filter.length >= 3)
      {
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
        var MUOM = ''; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART = filter; 
        var OPART = ''; 
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("MItemTable2");
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

    function MItemOEMPNFunction(FORMID) {
      
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("MItemOEMPNsearch");
      filter = input.value.toUpperCase();

      if(filter.length == 0)
      {
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
        var MUOM = ''; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART = ''; 
        var OPART = ''; 
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else if(filter.length >= 3)
      {
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
        var MUOM = ''; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART = ''; 
        var OPART = filter; 
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("MItemTable2");
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

    function MItemDrawingNoFunction(FORMID) {
      
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("MItemDrawingNoSearch");
      filter = input.value.toUpperCase();

      if(filter.length == 0)
      {
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
        var MUOM = ''; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART = ''; 
        var OPART = ''; 
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else if(filter.length >= 3)
      {
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
        var MUOM = ''; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART = ''; 
        var OPART = ''; 
        var DRAWNO = filter; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("MItemTable2");
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

    function MItemPartNoFunction(FORMID) {
      
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("MItemPartNoSearch");
      filter = input.value.toUpperCase();

      if(filter.length == 0)
      {
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
        var MUOM = ''; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART = ''; 
        var OPART = ''; 
        var DRAWNO = ''; 
        var PARTNO = ''; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else if(filter.length >= 3)
      {
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
        var MUOM = ''; 
        var GROUP = ''; 
        var CTGRY = ''; 
        var BUNIT = ''; 
        var APART = ''; 
        var CPART = ''; 
        var OPART = ''; 
        var DRAWNO = ''; 
        var PARTNO = filter; 
        loadItem_main_item(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,FORMID,DRAWNO,PARTNO); 
      }
      else
      {
        table = document.getElementById("MItemTable2");
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


function bindMainItem(){

  $('[id*="chkIdMainItem"]').change(function(){

    //var fieldid = $(this).attr('id');
    var fieldid = $(this).parent().parent().attr('id');

    var txtval =    $("#txt"+fieldid+"").val();
    var name =   $("#txt"+fieldid+"").data("name");
    var code =   $("#txt"+fieldid+"").data("code");
    var drawingno =   $("#txt"+fieldid+"").data("drawingno");
    var uom =   $("#txt"+fieldid+"").data("uom");
    var uomno =   $("#txt"+fieldid+"").data("uomno");
    var partno =   $("#txt"+fieldid+"").data("partno");
    var hsnid_ref =   $("#txt"+fieldid+"").data("hsnid");

    var txtid= $('#hdn_MItemID').val();
    var txt_id2= $('#hdn_MItemID2').val();
    var txt_id3= $('#hdn_MItemID3').val();

    var current_vendorid=$("#"+txtid).parent().parent().find('[id*="VENDORID_REF"]').val();
    var current_texid=$("#"+txtid).parent().parent().find('[id*="TAX"]').attr('id');
   
      var CheckExist = [];
      var CheckExist_vendor = [];
      $('#example3').find('.participantRow1').each(function(){
        if($(this).find('[id*="MainItemId1_Ref_"]').val() != '') 
        {
          var itemid = $(this).find('[id*="MainItemId1_Ref_"]').val();
          var vendorid = $(this).find('[id*="VENDORID_REF"]').val();
          if(itemid!=''){
            CheckExist.push(itemid);
          }
          if(vendorid!=''){
            CheckExist_vendor.push(vendorid);
          }          
        }
      });

      if(jQuery.inArray(txtval, CheckExist) !== -1 && jQuery.inArray(current_vendorid, CheckExist_vendor) !== -1 ){

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

            txtval =    '';
            name =   '';
            code =   '';
            drawingno =   '';
            uom =  '' ;
            uomno =  '';
            partno =  '';

            txtid= '';
            txt_id2= '';
            txt_id3= '';

            $("#mainitempopup").hide();
            return false;

      }else{
          
          $('#'+txtid).val(code);
          $('#'+txt_id2).val(txtval);
          $('#'+txt_id3).val(name);

          $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

          $.ajax({
                    url:'{{route("master",['237',"get_tax_details"])}}',
                    type:'POST',
                    data:{'hsnid_ref':hsnid_ref},
                      success:function(data) {
                        //var TAX='#TAX_'+id_numbers;
                        $("#"+current_texid).val(data);
                      },
                  });


      }

      txtval =    '';
      name =   '';
      code =   '';
      drawingno =   '';
      uom =  '' ;
      uomno =  '';
      partno =  '';
      txtid= '';
      txt_id2= '';
      txt_id3= '';
      $("#MItemCodeSearch").val(''); 
      $("#MItemNameSearch").val(''); 
      $("#MItemUOMSearch").val(''); 
      $("#MItemBUsearch").val(''); 
      $("#MItemAPNsearch").val(''); 
      $("#MItemCPNsearch").val(''); 
      $("#MItemOEMPNsearch").val(''); 
      $("#MItemDrawingNoSearch").val(''); 
      $("#MItemPartNoSearch").val(''); 

      $("#mainitempopup").hide();

  });

 

}

$("#item_closePopup").on("click",function(event){ 
    $("#MItemCodeSearch").val(''); 
    $("#MItemNameSearch").val(''); 
    $("#MItemUOMSearch").val(''); 
    $("#MItemBUsearch").val(''); 
    $("#MItemAPNsearch").val(''); 
    $("#MItemCPNsearch").val(''); 
    $("#MItemOEMPNsearch").val(''); 
    $("#MItemDrawingNoSearch").val(''); 
    $("#MItemPartNoSearch").val(''); 
    
    $("#mainitempopup").hide();
  });


</script>
@endpush