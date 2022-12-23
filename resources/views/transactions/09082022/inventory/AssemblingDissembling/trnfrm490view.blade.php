@extends('layouts.app')
@section('content')

<div class="container-fluid topnav">
    <div class="row">
        <div class="col-lg-2">
        <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Assembling/Dissembling</a>
        </div>

        <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-floppy-o" ></i> Save</button>
                <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
        </div>
    </div>
</div>

<form id="frm_trn_edit"  method="POST">   
  @csrf
  <div class="container-fluid filter">
	  <div class="inner-form">

      <div class="row">
        <div class="col-lg-1 pl"><p>Doc No</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="DOC_NO" id="DOC_NO" value="{{isset($objResponse->ADSMNO)?$objResponse->ADSMNO:''}}" class="form-control mandatory" readonly  autocomplete="off"  style="text-transform:uppercase"  >
          <input type="hidden" name="ADSMID" id="ADSMID" value="{{isset($objResponse->ADSMID)?$objResponse->ADSMID:''}}" >
        </div>
              
        <div class="col-lg-1 pl"><p>Doc Date</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="date" name="DOC_DATE" id="DOC_DATE" value="{{isset($objResponse->ADSMDT)?$objResponse->ADSMDT:''}}" class="form-control mandatory"  placeholder="dd/mm/yyyy" >
        </div>

        <div class="col-lg-1 pl"><p>Type</p></div>
        <div class="col-lg-2 pl">
          <select {{$ActionStatus}} name="TYPE" id="TYPE" class="form-control" >
            <option {{isset($objResponse) && $objResponse->TYPE=='ASSEMBLING'?'selected="selected"':''}} value="ASSEMBLING">ASSEMBLING</option><!-- OUT -->
            <option {{isset($objResponse) && $objResponse->TYPE=='DISSEMBLING'?'selected="selected"':''}} value="DISSEMBLING">DISSEMBLING</option><!-- IN -->
          </select>
        </div>

        <div class="col-lg-1 pl"><p>Item</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text"    name="MAIN_ITEMID_NAME" id="MAIN_ITEMID_NAME" value="{{isset($objResponse->ICODE)?$objResponse->ICODE:''}}" class="form-control" readonly >
          <input type="hidden"  name="MAIN_ITEMID_REF"  id="MAIN_ITEMID_REF" value="{{isset($objResponse->ITEMID_REF)?$objResponse->ITEMID_REF:''}}" class="form-control" >
          <input type="hidden"  id="HIDDEN_ITEM_FIELD"  class="form-control" >
        </div>
      </div>
      
      <div class="row">
        <div class="col-lg-1 pl"><p>Main UOM</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="MAIN_UOM" id="MAIN_UOM" value="{{isset($objResponse->UOMCODE)?$objResponse->UOMCODE:''}}{{isset($objResponse->DESCRIPTIONS)?'-'.$objResponse->DESCRIPTIONS:''}}" class="form-control" readonly >
          <input type="hidden" name="MAIN_UOM_REF" id="MAIN_UOM_REF" value="{{isset($objResponse->UOMID_REF)?$objResponse->UOMID_REF:''}}" class="form-control" readonly >
        </div>

        <div class="col-lg-1 pl"><p>Qty</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="MAIN_QTY" id="MAIN_QTY" value="{{isset($objResponse->QTY)?$objResponse->QTY:''}}" class="form-control" onkeypress="return isNumberKey(event,this)" >
        </div>

        <div class="col-lg-1 pl"><p>Store</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="STID_REF_NAME" id="STID_REF_NAME" value="{{isset($objResponse->STCODE)?$objResponse->STCODE:''}}{{isset($objResponse->STNAME)?'-'.$objResponse->STNAME:''}}" onclick="modal('show')" class="form-control mandatory"  autocomplete="off" readonly/>
          <input type="hidden" name="STID_REF" id="STID_REF" value="{{isset($objResponse->STID_REF)?$objResponse->STID_REF:''}}" class="form-control" autocomplete="off" />
        </div>

        <div class="col-lg-1 pl"><p>Remarks</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="REMARKS" id="REMARKS" value="{{isset($objResponse->REMARKS)?$objResponse->REMARKS:''}}" class="form-control" >
        </div>
      </div>

      <div class="row">
        <div class="col-lg-1 pl"><p>Amount</p></div>
        <div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="AMOUNT" id="AMOUNT" value="{{isset($objResponse->AMOUNT)?$objResponse->AMOUNT:''}}" class="form-control" readonly >
        </div>
      </div>
    </div>

	  <div class="container-fluid">

		  <div class="row">
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
        </ul>
			
			  <div class="tab-content">

				  <div id="Material" class="tab-pane fade in active">
					  <div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;" >
						  <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
							  <thead id="thead1"  style="position: sticky;top: 0">
                  <tr>
                    <th>Item Code <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="{{isset($objMAT)?count($objMAT):1}}" ></th>
                    <th>Item Name</th>
                    <th>UOM</th>
                    <th>Store</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Value</th>
                    <th>Action</th>
								  </tr>
							  </thead>
							  <tbody>
                  @if(isset($objMAT) && !empty($objMAT))
                  @foreach($objMAT as $key => $row)
							    <tr  class="participantRow">

                    <td><input {{$ActionStatus}}  type="text" name="popupITEMID_{{$key}}" id="popupITEMID_{{$key}}" value="{{ $row->ICODE }}" class="form-control"  autocomplete="off"  readonly/></td>
                    <td hidden><input type="hidden" name="ITEMID_REF_{{$key}}" id="ITEMID_REF_{{$key}}" value="{{ $row->ITEMID_REF }}" class="form-control" autocomplete="off" /></td>
                  
                    <td><input {{$ActionStatus}} type="text" name="ItemName_{{$key}}" id="ItemName_{{$key}}" value="{{ $row->ITEM_NAME }}" class="form-control"  autocomplete="off"  readonly /></td>
                    <td hidden><input type="text" name="Itemspec_{{$key}}" id="Itemspec_{{$key}}" value="{{ $row->ITEM_SPECI }}" class="form-control"  autocomplete="off" readonly /></td>  
                    
                    <td><input {{$ActionStatus}} type="text" name="popupMUOM_{{$key}}" id="popupMUOM_{{$key}}" class="form-control" value="{{ $row->UOMCODE }}-{{ $row->DESCRIPTIONS }}"  autocomplete="off"  readonly /></td>
                    <td hidden><input type="hidden" name="MAIN_UOMID_REF_{{$key}}" id="MAIN_UOMID_REF_{{$key}}" value="{{ $row->UOMID_REF }}" class="form-control"  autocomplete="off" /></td>
                    
                    <td hidden><input type="text" name="popupALTUOM_{{$key}}" id="popupALTUOM_{{$key}}" class="form-control"  autocomplete="off"  readonly /></td>
                    <td hidden><input type="hidden" name="ALT_UOMID_REF_{{$key}}" id="ALT_UOMID_REF_{{$key}}" class="form-control"  autocomplete="off" /></td>
                    
                    <td align="center"><a {{$ActionStatus}} class="btn checkstore"  id="{{$key}}" ><i class="fa fa-clone"></i></a></td>
                    <td hidden ><input type="hidden" name="TotalHiddenQty_{{$key}}" id="TotalHiddenQty_{{$key}}" value="{{ $row->ITEM_QTY }}" ></td>
                    <td hidden ><input type="hidden" name="HiddenRowId_{{$key}}" id="HiddenRowId_{{$key}}" value="{{ $row->BATCH_QTY }}" ></td>
                    
                    <td><input {{$ActionStatus}} type="text"  name="QTY_{{$key}}" id="QTY_{{$key}}" value="{{ $row->ITEM_QTY }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly /></td>
                    <td><input {{$ActionStatus}} type="text"  type="text" name="RATE_{{$key}}" id="RATE_{{$key}}" value="{{ $row->RATEPUOM }}" class="form-control three-digits" onkeyup="getValue()" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off"  {{isset($objResponse) && $objResponse->TYPE=='DISSEMBLING'?'readonly':''}} /></td>
                    <td><input {{$ActionStatus}} type="text"  name="VALUE_{{$key}}" id="VALUE_{{$key}}" value="{{ $row->VALUE }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)"   autocomplete="off" readonly /></td>
                    
                    <td align="center" >
                      <button {{$ActionStatus}} class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                      <button {{$ActionStatus}} class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button" ><i class="fa fa-trash" ></i></button>
                    </td>
                  
                  </tr>
                  
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
</form>

@endsection
@section('alert')
<div id="StoreModal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:80%;z-index:1">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='StoreModalClose' >&times;</button>
      </div>
      <div class="modal-body">
	      <div class="tablename"><p>Store Details</p></div>
        <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="StoreTable" class="display nowrap table  table-striped table-bordered" style="width: 100%;" ></table>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

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
          <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData"><div id="alert-active" class="activeYes"></div>Yes</button>
          <button class="btn alertbt" name='NoBtn' id="NoBtn"   data-funcname="fnUndoNo" ><div id="alert-active" class="activeNo"></div>No</button>
          <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk"></div>OK</button>
          <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;" onclick="getFocus()"><div id="alert-active" class="activeOk1"></div>OK</button>
          <input type="hidden" id="FocusId" >
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="stidpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='st_closePopup' onclick="modal('hide')"  >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Store</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="STCodeTable" class="display nowrap table  table-striped table-bordered" >
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
        <td class="ROW2"><input type="text" id="stcodesearch" class="form-control" autocomplete="off" onkeyup="searchData(this.id,1)"></td>
        <td class="ROW3"><input type="text" id="stnamesearch" class="form-control" autocomplete="off" onkeyup="searchData(this.id,2)"></td>
      </tr>

    </tbody>
    </table>
      <table id="STCodeTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
        </thead>
        <tbody>
        @foreach ($objStoreList as $key=>$val)
        <tr >
          <td class="ROW1"> <input type="checkbox" name="SELECT_STID_REF[]" id="stidcode_{{ $key }}" class="clsstid" value="{{ $val-> STID }}" ></td>   
          <td class="ROW2">{{ $val-> STCODE }} <input type="hidden" id="txtstidcode_{{ $key }}" data-desc="{{ $val-> STCODE }} - {{ $val-> NAME }}"  value="{{ $val-> STID }}"/></td>
          <td class="ROW3">{{ $val-> NAME }}</td>
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

<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%">
    <div class="modal-content" >
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button></div>
      <div class="modal-body">
	      <div class="tablename"><p>Item Details</p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead>
              <tr id="none-select" class="searchalldata" hidden>
                <td> 
                  <input type="hidden" name="fieldid" id="hdn_ItemID"/>
                  <input type="hidden" name="fieldid2" id="hdn_ItemID2"/>
                  <input type="hidden" name="fieldid3" id="hdn_ItemID3"/>
                  <input type="hidden" name="fieldid4" id="hdn_ItemID4"/>
                  <input type="hidden" name="fieldid5" id="hdn_ItemID5"/>
                  <input type="hidden" name="fieldid6" id="hdn_ItemID6"/>
                  <input type="hidden" name="fieldid7" id="hdn_ItemID7"/>
                  <input type="hidden" name="fieldid8" id="hdn_ItemID8"/>
                  <input type="hidden" name="fieldid9" id="hdn_ItemID9"/>
                  <input type="hidden" name="fieldid10" id="hdn_ItemID10"/>
                  <input type="hidden" name="fieldid11" id="hdn_ItemID11"/>
                  <input type="hidden" name="fieldid12" id="hdn_ItemID12"/>
                  <input type="hidden" name="fieldid13" id="hdn_ItemID13"/>
                  <input type="hidden" name="fieldid14" id="hdn_ItemID14"/>
                  <input type="hidden" name="fieldid15" id="hdn_ItemID15"/>
                  <input type="hidden" name="fieldid16" id="hdn_ItemID16"/>
                  <input type="hidden" name="fieldid17" id="hdn_ItemID17"/>
                </td>
              </tr>

              <tr>
                <th style="width:8%;" id="all-check">Select</th>
                <th style="width:10%;">Item Code</th>
                <th style="width:10%;">Name</th>
                <th style="width:8%;">Main UOM</th>
                <th style="width:8%;">Rate</th>
                <th style="width:8%;">Item Group</th>
                <th style="width:8%;">Item Category</th>
                <th style="width:8%;">Business Unit</th>
                <th style="width:8%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                <th style="width:8%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                <th style="width:8%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
                <th style="width:8%;">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th style="width:8%;text-align:center;">&#10004;</th>
                <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemSearch(this.id,1)"></td>
                <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemSearch(this.id,2)"></td>
                <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="ItemSearch(this.id,3)"></td>
                <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control" autocomplete="off" readonly></td>
                <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="ItemSearch(this.id,5)"></td>
                <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="ItemSearch(this.id,6)"></td>
                <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemSearch(this.id,7)"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="ItemSearch(this.id,8)"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="ItemSearch(this.id,9)"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="ItemSearch(this.id,10)"></td>
                <td style="width:8%;"><input type="text" id="ItemStatussearch" class="form-control" autocomplete="off" readonly ></td>
              </tr>
            </tbody>
          </table>

          <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead id="thead2"></thead>
            <tbody id="tbody_ItemID"></tbody>
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
.text-danger{
  color:red !important;
}
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
  width: 100%;
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
  width: 100%;
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
#StoreTable {
  border-collapse: collapse;
  width: 950px;
  border: 1px solid #ddd;
  font-size: 11px;
}
#StoreTable th {
  text-align: center;
  padding: 5px;
  font-size: 11px;
  color: #0f69cc;
  font-weight: 600;
}
#StoreTable td {
  text-align: center;
  padding: 5px;
  font-size: 11px;
  font-weight: 600;
}
.qtytext{
  display: block;
  width: 100%;
  height: 24px;
  padding: 6px 6px;
  font-size: 14px;
  line-height: 1.42857143;
  color: #555;
  background-color: #fff;
  background-image: none;
  border: 1px solid #ccc;
}
</style>
@endpush
@push('bottom-scripts')
<script>
/*================================== BUTTON FUNCTION ================================*/
$('#btnAdd').on('click', function() {
  var viewURL = '{{route("transaction",[$FormId,"add"])}}';
  window.location.href=viewURL;
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
});

window.fnUndoYes = function (){
  window.location.reload();
}

$("#btnSave").click(function() {
  var formReqData = $("#frm_trn_edit");
  if(formReqData.valid()){
    validateForm('fnSaveData','update');
  }
});

$("#btnApprove").click(function(){
  var formReqData = $("#frm_trn_edit");
  if(formReqData.valid()){
    validateForm('fnApproveData','approve');
  }
});

$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName = $("#YesBtn").data("funcname");
  window[customFnName]();
});

$("#NoBtn").click(function(){
  $("#alert").modal('hide');
  $("#LABEL").focus();
});

$("#OkBtn").click(function(){
  $("#alert").modal('hide');
  $("#YesBtn").show();
  $("#NoBtn").show();
  $("#OkBtn").hide();
  $(".text-danger").hide();
  window.location.href = '{{route("transaction",[$FormId,"index"]) }}';
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
  $("#"+$("#FocusId").val()).focus();
  $("#closePopup").click();
}

function highlighFocusBtn(pclass){
  $(".activeYes").hide();
  $(".activeNo").hide();
  $("."+pclass+"").show();
}

/*================================== Update FUNCTION =================================*/
window.fnSaveData = function (){

  event.preventDefault();

  var trnFormReq  = $("#frm_trn_edit");
  var formData    = trnFormReq.serialize();

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $("#btnSave").hide(); 
  $(".buttonload").show(); 
  $("#btnApprove").prop("disabled", true);
  $.ajax({
    url:'{{ route("transaction",[$FormId,"update"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $(".buttonload").hide(); 
      $("#btnSave").show();   
      $("#btnApprove").prop("disabled", false);

      if(data.errors) {
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(data.msg);
        $("#alert").modal('show');
        $("#OkBtn").focus();
        $(".text-danger").show();
      }
      else if(data.success) {                   
        console.log("succes MSG="+data.msg);
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(data.msg);
        $("#alert").modal('show');
        $("#OkBtn").focus();
        $(".text-danger").hide();
      }
      
    },
    error:function(data){
        $(".buttonload").hide(); 
        $("#btnSave").show();   
        $("#btnApprove").prop("disabled", false);
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

/*================================== Approve FUNCTION =================================*/
window.fnApproveData = function (){

  event.preventDefault();

  var trnFormReq  = $("#frm_trn_edit");
  var formData    = trnFormReq.serialize();

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $("#btnApprove").hide(); 
  $(".buttonload_approve").show();  
  $("#btnSave").prop("disabled", true);
  $.ajax({
    url:'{{ route("transaction",[$FormId,"Approve"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $("#btnApprove").show();  
      $(".buttonload_approve").hide();  
      $("#btnSave").prop("disabled", false);

      if(data.errors) {
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(data.msg);
        $("#alert").modal('show');
        $("#OkBtn").focus();
        $(".text-danger").show();
      }
      else if(data.success) {                   
        console.log("succes MSG="+data.msg);
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(data.msg);
        $("#alert").modal('show');
        $("#OkBtn").focus();
        $(".text-danger").hide();
      }
      
    },
    error:function(data){
        $("#btnApprove").show();  
        $(".buttonload_approve").hide();  
        $("#btnSave").prop("disabled", false);
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

/*================================== VALIDATE FUNCTION =================================*/

function validateForm(actionType,actionMsg){
 
  $("#FocusId").val('');
  var DOC_NO          = $.trim($("#DOC_NO").val());
  var DOC_DATE        = $.trim($("#DOC_DATE").val());
  var TYPE            = $.trim($("#TYPE").val());
  var MAIN_ITEMID_REF = $.trim($("#MAIN_ITEMID_REF").val());
  var MAIN_QTY        = $.trim($("#MAIN_QTY").val());
  var STID_REF        = $.trim($("#STID_REF").val());
  
  if(DOC_NO ===""){
    $("#FocusId").val('DOC_NO');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select doc no.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(DOC_DATE ===""){
    $("#FocusId").val('DOC_DATE');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select doc date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(TYPE ===""){
    $("#FocusId").val('TYPE');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select type.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if(MAIN_ITEMID_REF ===""){
    $("#FocusId").val('MAIN_ITEMID_NAME');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select item.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }  
  else if(MAIN_QTY ===""){
    $("#FocusId").val('MAIN_QTY');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter qty.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else if(STID_REF ===""){
    $("#FocusId").val('STID_REF_NAME');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select store.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }  
  else{
    event.preventDefault();
    var allblank1   = [];
    var allblank2   = [];
    var allblank3   = [];
    var allblank4   = [];
    var allblank5   = [];
    var focustext   = "";
      
    $('#example2').find('.participantRow').each(function(){

      if($.trim($(this).find("[id*=ITEMID_REF]").val())!=""){
        allblank1.push('true');

        if($.trim($(this).find("[id*=MAIN_UOMID_REF]").val())!=""){
          allblank2.push('true');

          if($.trim($(this).find('[id*="QTY"]').val()) != "" && $.trim($(this).find('[id*="QTY"]').val()) > 0.000 ){
            allblank3.push('true');
          }
          else{
            allblank3.push('false');
            focustext = $(this).find("[id*=QTY]").attr('id');
          }  

          if($.trim($(this).find('[id*="QTY"]').val()) != "" && $.trim($(this).find('[id*="QTY"]').val()) == $.trim($(this).find('[id*="TotalHiddenQty"]').val()) ){
            allblank5.push('true');
          }
          else{
            allblank5.push('false');
            focustext = $(this).find("[id*=QTY]").attr('id');
          }  

        }
        else{
            allblank2.push('false');
            focustext = $(this).find("[id*=popupMUOM]").attr('id');
        }      
      }
      else{
        allblank1.push('false');
        focustext = $(this).find("[id*=popupITEMID]").attr('id');
      }

    });

    if(jQuery.inArray("false", allblank1) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Item In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank2) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Main UOM is missing in in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank3) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Qty cannot be zero or blank in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank5) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Qty cannot be equal of selected store qty in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else{
      $("#alert").modal('show');
      $("#AlertMessage").text('Do you want to '+actionMsg+' to record.');
      $("#YesBtn").data("funcname",actionType);
      $("#YesBtn").focus();
      $("#OkBtn").hide();
      highlighFocusBtn('activeYes');
    }
  }
}

/*================================== POPUP SHORTING FUNCTION =================================*/
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

  
/*================================== ITEM DETAILS =================================*/

let itemtid         = "#ItemIDTable2";
let itemtid2        = "#ItemIDTable";
let itemtidheaders  = document.querySelectorAll(itemtid2 + " th");

itemtidheaders.forEach(function(element, i){
  element.addEventListener("click", function(){
    w3.sortHTML(itemtid, ".clsitemid", "td:nth-child(" + (i + 1) + ")");
  });
});

function ItemSearch(textid,no) {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById(textid);
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
    table = document.getElementById("ItemIDTable2");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[no];
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

function loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART){
  $("#tbody_ItemID").html('');
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getItemDetails"])}}',
    type:'POST',
    data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART},
    success:function(data) {
    $("#tbody_ItemID").html(data); 

    if($("#HIDDEN_ITEM_FIELD").val() =='MAIN_ITEMID'){
      bindMainItemEvents();
    }
    else{
      bindItemEvents(); 
    }

    },
    error:function(data){
    console.log("Error: Something went wrong.");
    $("#tbody_ItemID").html('');                        
    },
  });
}

$('#MAIN_ITEMID_NAME').on('click',function(event){
  $("#HIDDEN_ITEM_FIELD").val('MAIN_ITEMID');
  var CODE  = ''; 
  var NAME  = ''; 
  var MUOM  = ''; 
  var GROUP = ''; 
  var CTGRY = ''; 
  var BUNIT = ''; 
  var APART = ''; 
  var CPART = ''; 
  var OPART = ''; 
  loadItem('',CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART);      
  $("#ITEMIDpopup").show();
  event.preventDefault();
});

function bindMainItemEvents(){

  $('[id*="chkId"]').change(function(){
   
    var fieldid     = $(this).parent().parent().attr('id');
    var txtval      = $("#txt"+fieldid+"").val();
    var texdesc     = $("#txt"+fieldid+"").data("desc");
    var fieldid2    = $(this).parent().parent().children('[id*="itemname"]').attr('id');
    var txtname     = $("#txt"+fieldid2+"").val();
    var txtspec     = $("#txt"+fieldid2+"").data("desc");
    var fieldid3    = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
    var txtmuomid   = $("#txt"+fieldid3+"").val();
    var txtauom     = $("#txt"+fieldid3+"").data("desc");
    var txtmuom     = $(this).parent().parent().children('[id*="itemuom"]').text();
    var fieldid4    = $(this).parent().parent().children('[id*="uomqty"]').attr('id');
    var txtauomid   = $("#txt"+fieldid4+"").val();
    var txtauomqty  = $("#txt"+fieldid4+"").data("desc");
    var txtmuomqty  = $(this).parent().parent().children('[id*="uomqty"]').text();
    var fieldid5    = $(this).parent().parent().children('[id*="irate"]').attr('id');
    var txtruom     = $("#txt"+fieldid5+"").val();
    var txtmqtyf    = $("#txt"+fieldid5+"").data("desc");
    var fieldid6    = $(this).parent().parent().children('[id*="itax"]').attr('id');
    var desc6       = $("#txt"+fieldid+"").data("desc6");
    var mainitem    = $("#MAIN_ITEMID_REF").val();
 
    if($(this).is(":checked") == true) {

      var checkExist  = false;

      $('#example2').find('.participantRow').each(function(){
        var itemid    = $(this).find('[id*="ITEMID_REF"]').val();
        var exist_val = itemid;

        if(txtval){
          if(mainitem !='' && mainitem ==desc6){
            checkExist  = true;
          } 
          else if(desc6 == exist_val){
            checkExist  = true;
          }            
        } 

      });

      if(checkExist ==true){
        $("#MAIN_ITEMID_NAME").val('');
        $("#MAIN_ITEMID_REF").val('');
        $("#MAIN_UOM").val(''); 
        $("#MAIN_UOM_REF").val(''); 
        $("#AMOUNT").val(''); 
        $("#ITEMIDpopup").hide();

        $('.js-selectall1').prop('checked', false);
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Item already exists.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk1'); 
        return false; 
      }
      else{
        $("#MAIN_ITEMID_NAME").val(texdesc);
        $("#MAIN_ITEMID_REF").val(txtval);
        $("#MAIN_UOM").val(txtmuom);
        $("#MAIN_UOM_REF").val(txtmuomid); 

        if($("#TYPE").val() =="DISSEMBLING"){
          $("#AMOUNT").val(txtmuomqty);   
        } 
        else{
          $("#AMOUNT").val(0);
        }
      }

      $("#ITEMIDpopup").hide();
      event.preventDefault();
    }
  
    $("#Itemcodesearch").val(''); 
    $("#Itemnamesearch").val(''); 
    $("#ItemUOMsearch").val(''); 
    $("#ItemQTYsearch").val(''); 
    $("#ItemGroupsearch").val(''); 
    $("#ItemCategorysearch").val(''); 
    $("#ItemBUsearch").val(''); 
    $("#ItemAPNsearch").val(''); 
    $("#ItemCPNsearch").val(''); 
    $("#ItemOEMPNsearch").val(''); 
    $("#ItemStatussearch").val(''); 
    $('.remove').removeAttr('disabled'); 
    event.preventDefault();
  });
}

$('#Material').on('click','[id*="popupITEMID"]',function(event){
  $("#HIDDEN_ITEM_FIELD").val('');
  var CODE = ''; 
  var NAME = ''; 
  var MUOM = ''; 
  var GROUP = ''; 
  var CTGRY = ''; 
  var BUNIT = ''; 
  var APART = ''; 
  var CPART = ''; 
  var OPART = ''; 

  loadItem('',CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART);
          
  $("#ITEMIDpopup").show();

  var id    = $(this).attr('id');
  var id2   = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
  var id3   = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
  var id4   = $(this).parent().parent().find('[id*="Itemspec"]').attr('id');
  var id5   = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
  var id6   = $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
  var id7   = $(this).parent().parent().find('[id*="RATE"]').attr('id');
  var id12  = $(this).parent().parent().find('[id*="TotalHiddenQty"]').attr('id');
  var id13  = $(this).parent().parent().find('[id*="HiddenRowId"]').attr('id');
  var id8   = $(this).parent().parent().find('[id*="popupALTUOM"]').attr('id');
  var id9   = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');
  

  $('#hdn_ItemID').val(id);
  $('#hdn_ItemID2').val(id2);
  $('#hdn_ItemID3').val(id3);
  $('#hdn_ItemID4').val(id4);
  $('#hdn_ItemID5').val(id5);
  $('#hdn_ItemID6').val(id6);
  $('#hdn_ItemID7').val(id7);
  $('#hdn_ItemID12').val(id12);
  $('#hdn_ItemID13').val(id13);
  $('#hdn_ItemID8').val(id8);
  $('#hdn_ItemID9').val(id9);
  event.preventDefault();
});

$("#ITEMID_closePopup").click(function(event){
  $("#ITEMIDpopup").hide();
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
    var txtmuom =  $(this).parent().parent().children('[id*="itemuom"]').text();
    var fieldid4 = $(this).parent().parent().children('[id*="uomqty"]').attr('id');
    var txtauomid =  $("#txt"+fieldid4+"").val();
    var txtauomqty =  $("#txt"+fieldid4+"").data("desc");
    var txtmuomqty =  $(this).parent().parent().children('[id*="uomqty"]').text();
    var fieldid5 = $(this).parent().parent().children('[id*="irate"]').attr('id');
    var txtruom =  $("#txt"+fieldid5+"").val();
    var txtmqtyf = $("#txt"+fieldid5+"").data("desc");
    var fieldid6 = $(this).parent().parent().children('[id*="itax"]').attr('id');
    var mainitem    = $("#MAIN_ITEMID_REF").val();

    txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);

    var desc6         =  $("#txt"+fieldid+"").data("desc6");
    var AultUomQty    =  $("#txt"+fieldid+"").data("desc7");
    var PoPendingQty  =  $("#txt"+fieldid+"").data("desc8");
    
    if(intRegex.test(txtauomqty)){
        txtauomqty = (txtauomqty +'.000');
    }

    if(intRegex.test(txtmuomqty)){
      txtmuomqty = (txtmuomqty +'.000');
    }

        
        
    if($(this).is(":checked") == true) {

      $('#example2').find('.participantRow').each(function(){

        var itemid      = $(this).find('[id*="ITEMID_REF"]').val();
        var exist_val   = itemid;

        if(txtval){

          if(mainitem !='' && mainitem ==desc6){

              $("#ITEMIDpopup").hide();
              $('.js-selectall1').prop('checked', false);
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
              $('#hdn_ItemID12').val('');
              $('#hdn_ItemID13').val('');
              $('#hdn_ItemID8').val('');
              $('#hdn_ItemID9').val('');
              txtval = '';
              texdesc = '';
              txtname = '';
              txtspec = '';
              txtmuom = '';
              txtauom = '';
              txtmuomid = '';
              txtauomid = '';
              txtauomqty='';
              txtmuomqty='';
              txtruom = '';
              return false;
          }
          else if(desc6 == exist_val){

              $("#ITEMIDpopup").hide();
              $('.js-selectall1').prop('checked', false);
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
              $('#hdn_ItemID12').val('');
              $('#hdn_ItemID13').val('');
              $('#hdn_ItemID8').val('');
              $('#hdn_ItemID9').val('');
              txtval = '';
              texdesc = '';
              txtname = '';
              txtspec = '';
              txtmuom = '';
              txtauom = '';
              txtmuomid = '';
              txtauomid = '';
              txtauomqty='';
              txtmuomqty='';
              txtruom = '';
              return false;
            
          }                     
        } 
                 
      });

      if($('#hdn_ItemID').val() == "" && txtval != ''){

        var txtid= $('#hdn_ItemID').val();
        var txt_id2= $('#hdn_ItemID2').val();
        var txt_id3= $('#hdn_ItemID3').val();
        var txt_id4= $('#hdn_ItemID4').val();
        var txt_id5= $('#hdn_ItemID5').val();
        var txt_id6= $('#hdn_ItemID6').val();
        var txt_id7= $('#hdn_ItemID7').val();
       

        var txt_id8= $('#hdn_ItemID8').val();
        var txt_id9= $('#hdn_ItemID9').val();
        

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
        $clone.find('[id*="popupITEMID"]').val(texdesc);
        $clone.find('[id*="ITEMID_REF"]').val(txtval);
        $clone.find('[id*="ItemName"]').val(txtname);
        $clone.find('[id*="Itemspec"]').val(txtspec);
        $clone.find('[id*="popupMUOM"]').val(txtmuom);
        $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
        $clone.find('[id*="RATE"]').val(txtmuomqty);

        $clone.find('[id*="popupALTUOM"]').val(txtauom);
        $clone.find('[id*="ALT_UOMID_REF"]').val(txtauomid);
       
        
        $clone.find('[id*="TotalHiddenQty"]').val('');
        $clone.find('[id*="HiddenRowId"]').val('');

        $clone.find('[id*="REMARKS"]').val('');
        
        $tr.closest('table').append($clone);   
        var rowCount = $('#Row_Count1').val();
          rowCount = parseInt(rowCount)+1;
          $('#Row_Count1').val(rowCount);
          
          $("#ITEMIDpopup").hide();
          $('.js-selectall1').prop('checked', false);
        event.preventDefault();
      }
      else{

        $('#'+$('#hdn_ItemID12').val()).val('');
        $('#'+$('#hdn_ItemID13').val()).val('');

        var txtid= $('#hdn_ItemID').val();
        var txt_id2= $('#hdn_ItemID2').val();
        var txt_id3= $('#hdn_ItemID3').val();
        var txt_id4= $('#hdn_ItemID4').val();
        var txt_id5= $('#hdn_ItemID5').val();
        var txt_id6= $('#hdn_ItemID6').val();
        var txt_id7= $('#hdn_ItemID7').val();
        var txt_id8= $('#hdn_ItemID8').val();
        var txt_id9= $('#hdn_ItemID9').val();
       

        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        $('#'+txt_id3).val(txtname);
        $('#'+txt_id4).val(txtspec);
        $('#'+txt_id5).val(txtmuom);
        $('#'+txt_id6).val(txtmuomid);
        $('#'+txt_id7).val(txtmuomqty);
  
        $('#'+txt_id8).val(txtauom);
        $('#'+txt_id9).val(txtauomid);
     


        $('#hdn_ItemID').val('');
        $('#hdn_ItemID2').val('');
        $('#hdn_ItemID3').val('');
        $('#hdn_ItemID4').val('');
        $('#hdn_ItemID5').val('');
        $('#hdn_ItemID6').val('');
        $('#hdn_ItemID7').val('');
        
        
        $('#hdn_ItemID12').val('');
        $('#hdn_ItemID13').val('');

        $('#hdn_ItemID8').val('');
        $('#hdn_ItemID9').val('');
       

      }
              
      $("#ITEMIDpopup").hide();
      $('.js-selectall1').prop('checked', false);
      event.preventDefault();
    }
    else if($(this).is(":checked") == false){
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
              $("#ITEMIDpopup").hide();
              $('.js-selectall1').prop('checked', false);
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
   
    event.preventDefault();

  });

}

/*================================== ADD/REMOVE FUNCTION ==================================*/

$("#Material").on('click', '.add', function() {
  var $tr     = $(this).closest('table');
  var allTrs  = $tr.find('.participantRow').last();
  var lastTr  = allTrs[allTrs.length-1];
  var $clone  = $(lastTr).clone();

  $clone.find('td').each(function(){
    var el  = $(this).find(':first-child');
    var id  = el.attr('id') || null;

    if(id) {
      var i = id.substr(id.length-1);
      var prefix = id.substr(0, (id.length-1));
      el.attr('id', prefix+(+i+1));
    }

    var name  = el.attr('name') || null;
    if(name) {
      var i = name.substr(name.length-1);
      var prefix1 = name.substr(0, (name.length-1));
      el.attr('name', prefix1+(+i+1));
    }

  });

  $clone.find('input:text').val('');
  $clone.find('input:hidden').val('');
  
  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count1').val();
  rowCount1     = parseInt(rowCount1)+1;
  $('#Row_Count1').val(rowCount1);
  $clone.find('.remove').removeAttr('disabled'); 
  event.preventDefault();
});

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

/*------------------Change of Rate-------------------------------*/
$("#Material").on('change', '[id*="RATE_"]', function(){
  var rate = $(this).val();
  var qty = $(this).parent().parent().find('[id*="QTY_"]').val();

  var amt = parseFloat(rate*qty).toFixed(2);
  $(this).parent().parent().find('[id*="VALUE_"]').val(amt);
});
/*------------------Change of Rate-------------------------------*/
/*================================== STORE DETAILS ================================*/

$("#example2").on('click', '[class*="checkstore"]', function() {
  $("#FocusId").val('');
  
  var ROW_ID          = $(this).attr('id');
  var ITEMID_REF      = $("#ITEMID_REF_"+ROW_ID).val();
  var TYPE            = $("#TYPE").val();
  
  if(TYPE ===""){
    $("#FocusId").val('TYPE');
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select type in header.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
    return false;
  }
  else if(ITEMID_REF ===""){
    $("#FocusId").val("popupITEMID_"+ROW_ID);
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select item code in material.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
    return false;
  }
  else{
      getStoreDetails(ROW_ID);
      $("#StoreModal").show();
      event.preventDefault();
  }

});

function getStoreDetails(ROW_ID){

var ITEMID_REF      = $("#ITEMID_REF_"+ROW_ID).val();
var ITEMROWID       = $("#HiddenRowId_"+ROW_ID).val();
var MAIN_UOMID_DES  = $("#popupMUOM_"+ROW_ID).val();
var MAIN_UOMID_REF  = $("#MAIN_UOMID_REF_"+ROW_ID).val();
var TYPE            = $("#TYPE").val();

$("#StoreTable").html('');
$.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$.ajax({
    url:'{{route("transaction",[$FormId,"getStoreDetails"])}}',
    type:'POST',
    data:{
      ROW_ID:ROW_ID,
      ITEMID_REF:ITEMID_REF,
      MAIN_UOMID_DES:MAIN_UOMID_DES,
      MAIN_UOMID_REF:MAIN_UOMID_REF,
      TYPE:TYPE,
      ITEMROWID:ITEMROWID,
      ACTION_TYPE:'VIEW'
      },
    success:function(data) {
      $("#StoreTable").html(data);                
    },
    error:function(data){
      console.log("Error: Something went wrong.");
      $("#StoreTable").html('');                        
    },
}); 
}

$("#StoreModalClose").click(function(event){

var NewIdArr  = [];
var ROW_ID    = [];
var Req       = [];

$('#StoreTable').find('.participantRow33').each(function(){

  if($.trim($(this).find("[id*=UserQty]").val())!=""){  
    var UserQty      = parseFloat($.trim($(this).find("[id*=UserQty]").val()));
    var BatchId      = $.trim($(this).find("[id*=BATCHID]").val());
    var ROWID        = $.trim($(this).find("[id*=ROWID]").val());
    var TOTAL_STOCK  = parseFloat($.trim($(this).find("[id*=TOTAL_STOCK]").val()));
    var BATCHNOA     = $.trim($(this).find("[id*=BATCHNOA]").val());
    
    ROW_ID.push(ROWID);
    NewIdArr.push(BatchId+"_"+UserQty+"_"+TOTAL_STOCK);

    if(UserQty > 0 && BATCHNOA =="1"){
      Req.push('false');
    }
    else{
      Req.push('true');
    }

  } 

});                       

var ROW_ID    = ROW_ID[0];
var QTY       = parseFloat($("#QTY_"+ROW_ID).val());
var RATE      = parseFloat($("#RATE_"+ROW_ID).val());
var VALUE     = (QTY*RATE);

$("#HiddenRowId_"+ROW_ID).val(NewIdArr);
$("#VALUE_"+ROW_ID).val(parseFloat(VALUE).toFixed(3));
$("#StoreModal").hide();
getValue();

});


function checkStoreQty(ROW_ID,itemid,userQty,key,stock){

  var TYPE  = $("#TYPE").val();

  if( TYPE =='ASSEMBLING' && parseFloat(userQty) > parseFloat(stock) ){
    $("#UserQty_"+key).val('');  
    $("#AltUserQty_"+key).val('');  
    $("#alert").modal('show');
    $("#AlertMessage").text('Issue quantity should not greater then Stock inhand.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
  }
  else{

    var NewQtyArr = [];
    var NewIdArr  = [];

    $('#StoreTable').find('.participantRow33').each(function(){

        if($.trim($(this).find("[id*=UserQty]").val())!=""){  
          var UserQty      = parseFloat($.trim($(this).find("[id*=UserQty]").val()));
          var BatchId      = $.trim($(this).find("[id*=BATCHID]").val());

          NewQtyArr.push(UserQty);
          NewIdArr.push(BatchId+"_"+UserQty);
        }                
    });

    var TotalQty= getArraySum(NewQtyArr); 
 
    $("#TotalHiddenQty_"+ROW_ID).val(TotalQty);
    $("#HiddenRowId_"+ROW_ID).val(NewIdArr);
    $("#QTY_"+ROW_ID).val(TotalQty);  

  }
    
}

function getArraySum(a){
    var total=0;
    for(var i in a) { 
        total += a[i];
    }
    return total;
}

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}

/*================================== ONLOAD FUNCTION ==================================*/

$(document).ready(function(e) {
  var Material = $("#Material").html(); 
  $('#hdnmaterial').val(Material);
  var lastdt = <?php echo json_encode(isset($objResponse->ADSMDT)?$objResponse->ADSMDT:''); ?>;
  var today = new Date(); 
  var sodate = <?php echo json_encode(isset($objResponse->ADSMDT)?$objResponse->ADSMDT:''); ?>;
  $('#DOC_DATE').attr('min',lastdt);
  $('#DOC_DATE').attr('max',sodate);


  $('#TYPE').on('change',function(e){
    resetTab();
    if($(this).val() == 'DISSEMBLING')
    {
      $('#Material').find('[id*="RATE_"]').removeAttr('readonly');
    }
    else
    {
      $('#Material').find('[id*="RATE_"]').prop('readonly','true');
    }
  });

});

//=====================================================================================
let sttid     = "#STCodeTable2";
let sttid2    = "#STCodeTable";
let stheaders = document.querySelectorAll(sttid2 + " th");

stheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(sttid, ".clsstid", "td:nth-child(" + (i + 1) + ")");
  });
});

function searchData(textid,no){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById(textid);
  filter = input.value.toUpperCase();
  table = document.getElementById("STCodeTable2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[no];
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

function modal(type){
  if(type == "show"){
    $("#stidpopup").show();
  }
  else{
    $("#stidpopup").hide();
  }
}

$(".clsstid").click(function(){
  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");
  
  $('#STID_REF_NAME').val(texdesc);
  $('#STID_REF').val(txtval);
  $("#stidpopup").hide();
  
  $("#stcodesearch").val(''); 
  $("#stnamesearch").val(''); 
  event.preventDefault();
});


function resetTab(){
  $('#Material').find('.participantRow').each(function(){
    var rowcount = $(this).closest('table').find('.participantRow').length;
    $(this).find('input:text').val('');
    $(this).find('input:hidden').val('');
    $(this).find('input:checkbox').prop('checked', false);

    if(rowcount > 1){
      $(this).closest('.participantRow').remove();
      rowcount = parseInt(rowcount) - 1;
      $('#Row_Count1').val(rowcount);
    }
  });

  $("#MAIN_ITEMID_NAME").val('');
  $("#MAIN_ITEMID_REF").val('');
  $("#MAIN_UOM").val('');
  $("#MAIN_UOM_REF").val('');
  $("#AMOUNT").val(0);
}

function getValue(){
  var total = 0;
  $('#Material').find('.participantRow').each(function(){
    var value = $(this).find('[id*="VALUE_"]').val();
    value = value !=''?parseFloat(value):0;
    total = total+value;
  });

  if($("#TYPE").val() =='ASSEMBLING'){
    $("#AMOUNT").val(parseFloat(total).toFixed(5));
  }

}
</script>
@endpush