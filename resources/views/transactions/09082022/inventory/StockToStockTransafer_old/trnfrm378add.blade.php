@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Stock To Stock Transafer</a></div>

    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
      <button class="btn topnavbt" id="btnSave" ><i class="fa fa-floppy-o"></i> Save</button>
      <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
      <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
      <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>

<form id="frm_trn_add" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >  
  <div class="container-fluid filter">
	  <div class="inner-form">

      <div class="row">
        <div class="col-lg-2 pl"><p>Doc No*</p></div>
        <div class="col-lg-2 pl">
          @if(isset($objSON->SYSTEM_GRSR) && $objSON->SYSTEM_GRSR == "1")
          <input type="text" name="ST_ST_DOCNO" id="ST_ST_DOCNO" value="{{ isset($objDataNo)?$objDataNo:'' }}" class="form-control mandatory" readonly  autocomplete="off"  style="text-transform:uppercase"  >
         
          @elseif(isset($objSON->MANUAL_SR) && $objSON->MANUAL_SR == "1")
          <input type="text" name="ST_ST_DOCNO" id="ST_ST_DOCNO" value="{{ old('ST_ST_DOCNO') }}" class="form-control mandatory" maxlength="{{isset($objSON->MANUAL_MAXLENGTH)?$objSON->MANUAL_MAXLENGTH:''}}" autocomplete="off" style="text-transform:uppercase"  >
          @else
            <input type="text" name="ST_ST_DOCNO" id="ST_ST_DOCNO"  class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
          @endif
          <span class="text-danger" id="ERROR_ST_ST_DOCNO"></span>
        </div>
              
        <div class="col-lg-2 pl"><p>Date*</p></div>
        <div class="col-lg-2 pl">
          <input type="date" name="ST_ST_DOCDT" id="ST_ST_DOCDT" value="{{ old('ST_ST_DOCDT') }}" class="form-control mandatory"  placeholder="dd/mm/yyyy" >
          <span class="text-danger" id="ERROR_ST_ST_DOCDT"></span>
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
					  <div class="table-responsive table-wrapper-scroll-y" style="height:400px;margin-top:10px;" >
						  <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
							  <thead id="thead1"  style="position: sticky;top: 0">
								  
                <tr>                                   
                  <th colspan="8">Out Stock</th>
                  <th colspan="8">In Stock</th>
                  <th colspan="3"></th>
                </tr>
                
                <tr>
                    <th hidden><input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="1"></th>

                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>UOM</th>
                    <th>Store</th>
                    <th>Store Name</th> 
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Value</th>

                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>UOM</th>
                    <th>Store</th>
                    <th>Store Name</th> 
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Value</th>

                    <th>Reason of Adjustment</th>
                    <th>Remarks</th>
                    <th>Action</th>
								  </tr>
							  </thead>
							  <tbody>
								  <tr  class="participantRow">

                    <td><input  type="text" name="popupITEMID_0" id="popupITEMID_0" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                    <td hidden><input type="hidden" name="ITEMID_REF_0" id="ITEMID_REF_0" class="form-control" autocomplete="off" /></td>
                  
                    <td><input type="text" name="ItemName_0" id="ItemName_0" class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>
                    <td hidden><input type="text" name="Itemspec_0" id="Itemspec_0" class="form-control"  autocomplete="off" readonly style="width:200px;"  /></td>  
                    
                    <td><input type="text" name="popupMUOM_0" id="popupMUOM_0" class="form-control"  autocomplete="off"  readonly style="width:100px;"/></td>
                    <td hidden><input type="hidden" name="MAIN_UOMID_REF_0" id="MAIN_UOMID_REF_0" class="form-control"  autocomplete="off" /></td>
                    
                    <td hidden><input type="text" name="popupALTUOM_0" id="popupALTUOM_0" class="form-control"  autocomplete="off"  readonly style="width:100px;"/></td>
                    <td hidden><input type="hidden" name="ALT_UOMID_REF_0" id="ALT_UOMID_REF_0" class="form-control"  autocomplete="off" /></td>
                    
                    <td align="center"><a class="btn checkstore"  id="0" onclick="clickStoreDetails(this.id,'OUT')" ><i class="fa fa-clone"></i></a></td>
                    <td><input type="text" name="STORE_NAME_0" id="STORE_NAME_0"  class="form-control w-100" autocomplete="off" readonly style="width:200px;" ></td>
                    <td hidden ><input type="hidden" name="TotalHiddenQty_0" id="TotalHiddenQty_0" ></td>
                    <td hidden ><input type="hidden" name="HiddenRowId_0" id="HiddenRowId_0" ></td>
                    
                    <td><input type="text"   name="QTY_0" id="QTY_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly style="width:130px;text-align:right;"  /></td>
                    <td><input type="text"  type="text" name="RATE_0" id="RATE_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly style="width:130px;text-align:right;" /></td>
                    <td><input type="text"  name="VALUE_0" id="VALUE_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)"   autocomplete="off" readonly  style="width:130px;text-align:right;" /></td>
                    



                    <td><input  type="text" name="popupITEMID_IN_0" id="popupITEMID_IN_0" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                    <td hidden><input type="hidden" name="ITEMID_REF_IN_0" id="ITEMID_REF_IN_0" class="form-control" autocomplete="off" /></td>
                  
                    <td><input type="text" name="ItemName_IN_0" id="ItemName_IN_0" class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>
                    <td hidden><input type="text" name="Itemspec_IN_0" id="Itemspec_IN_0" class="form-control"  autocomplete="off" readonly style="width:200px;"  /></td>  
                    
                    <td><input type="text" name="popupMUOM_IN_0" id="popupMUOM_IN_0" class="form-control"  autocomplete="off"  readonly style="width:100px;"/></td>
                    <td hidden><input type="hidden" name="MAIN_UOMID_REF_IN_0" id="MAIN_UOMID_REF_IN_0" class="form-control"  autocomplete="off" /></td>
                    
                    <td hidden><input type="text" name="popupALTUOM_IN_0" id="popupALTUOM_IN_0" class="form-control"  autocomplete="off"  readonly style="width:100px;"/></td>
                    <td hidden><input type="hidden" name="ALT_UOMID_REF_IN_0" id="ALT_UOMID_REF_IN_0" class="form-control"  autocomplete="off" /></td>
                    
                    <td align="center"><a class="btn checkstore"  id="0" onclick="clickStoreDetails(this.id,'IN')" ><i class="fa fa-clone"></i></a></td>
                    <td><input type="text" name="STORE_NAME_IN_0" id="STORE_NAME_IN_0"  class="form-control w-100" autocomplete="off" readonly style="width:200px;" ></td>
                    <td hidden ><input type="hidden" name="TotalHiddenQty_IN_0" id="TotalHiddenQty_IN_0" ></td>
                    <td hidden ><input type="hidden" name="HiddenRowId_IN_0" id="HiddenRowId_IN_0" ></td>
                    
                    <td><input type="text"   name="QTY_IN_0" id="QTY_IN_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly style="width:130px;text-align:right;"  /></td>
                    <td><input type="text"  type="text" name="RATE_IN_0" id="RATE_IN_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:130px;text-align:right;" /></td>
                    <td><input type="text"  name="VALUE_IN_0" id="VALUE_IN_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)"   autocomplete="off" readonly  style="width:130px;text-align:right;" /></td>
                    


                    <td><input type="text" name="REASON_0" id="REASON_0" class="form-control"   autocomplete="off"   /></td>
                    <td><input style="width:200px;" type="text" name="REMARKS_0" id="REMARKS_0" class="form-control" autocomplete="off" ></td>
                    
                    <td align="center" >
                      <button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                      <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                    </td>

								  </tr>
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
                  <input type="hidden" name="STOCK_TYPE" id="STOCK_TYPE"/>
                  
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
                <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction('{{$FormId}}')"></td>
                <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemNameFunction('{{$FormId}}')"></td>
                <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="ItemUOMFunction('{{$FormId}}')"></td>
                <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control" autocomplete="off" onkeyup="ItemQTYFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="ItemGroupFunction('{{$FormId}}')"></td>
                <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="ItemCategoryFunction('{{$FormId}}')"></td>
                <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction('{{$FormId}}')"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="ItemAPNFunction('{{$FormId}}')"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="ItemCPNFunction('{{$FormId}}')"></td>
                <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="ItemOEMPNFunction('{{$FormId}}')"></td>
                <td style="width:8%;"><input type="text" id="ItemStatussearch" class="form-control" autocomplete="off" onkeyup="ItemStatusFunction()"></td>
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
  window.location.href = "{{route('transaction',[$FormId,'add'])}}";
}

$("#btnSave" ).click(function(){
  var formReqData = $("#frm_trn_add");
  if(formReqData.valid()){
    validateForm();
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

/*================================== Save FUNCTION =================================*/
window.fnSaveData = function (){

  event.preventDefault();

  var trnFormReq  = $("#frm_trn_add");
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
    url:'{{ route("transaction",[$FormId,"save"])}}',
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

/*================================== VALIDATE FUNCTION =================================*/
function validateForm(){
  
  $("#FocusId").val('');
  var ST_ST_DOCNO   = $.trim($("#ST_ST_DOCNO").val());
  var ST_ST_DOCDT   = $.trim($("#ST_ST_DOCDT").val());
  
  if(ST_ST_DOCNO ===""){
      $("#FocusId").val('ST_ST_DOCNO');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Doc No is required.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
  }
  else if(ST_ST_DOCDT ===""){
      $("#FocusId").val('ST_ST_DOCDT');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Date.');
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


      if(jQuery.inArray("true", allblank1) !== -1 && jQuery.inArray("true", allblank2) !== -1 && jQuery.inArray("true", allblank3) !== -1 && jQuery.inArray("true", allblank5) !== -1){

        if($.trim($(this).find("[id*=ITEMID_REF_IN]").val())!=""){
          allblank1.push('true');

          if($.trim($(this).find("[id*=MAIN_UOMID_REF_IN]").val())!=""){
            allblank2.push('true');

            if($.trim($(this).find('[id*="QTY_IN"]').val()) != "" && $.trim($(this).find('[id*="QTY_IN"]').val()) > 0.000 ){
              allblank3.push('true');
            }
            else{
              allblank3.push('false');
              focustext = $(this).find("[id*=QTY_IN]").attr('id');
            }  

            if($.trim($(this).find('[id*="REASON"]').val()) != "" ){
              allblank4.push('true');
            }
            else{
              allblank4.push('false');
              focustext = $(this).find("[id*=REASON]").attr('id');
            }  

            if($.trim($(this).find('[id*="QTY_IN"]').val()) != "" && $.trim($(this).find('[id*="QTY_IN"]').val()) == $.trim($(this).find('[id*="TotalHiddenQty_IN"]').val()) ){
              allblank5.push('true');
            }
            else{
              allblank5.push('false');
              focustext = $(this).find("[id*=QTY_IN]").attr('id');
            }  

          }
          else{
              allblank2.push('false');
              focustext = $(this).find("[id*=popupMUOM_IN]").attr('id');
          }      
        }
        else{
          allblank1.push('false');
          focustext = $(this).find("[id*=popupITEMID_IN]").attr('id');
        }

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
    else if(jQuery.inArray("false", allblank4) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please enter reason of adjustment in material tab.');
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

/*================================== CHECK DUPLICATE FUNCTION =================================*/
function checkDuplicateCode(){

  var trnFormReq  = $("#frm_trn_add");
  var formData    = trnFormReq.serialize();

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
      url:'{{route("transaction",[$FormId,"codeduplicate"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
          if(data.exists) {
              $(".text-danger").hide();
              showError('ERROR_ST_ST_DOCNO',data.msg);
              $("#ST_ST_DOCNO").focus();
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
    
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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
    var GROUP = filter; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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
    var CTGRY = filter; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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
  input = document.getElementById("ItemAPNsearch");
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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
  input = document.getElementById("ItemCPNsearch");
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,''); 
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,'');
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
  input = document.getElementById("ItemOEMPNsearch");
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,'');
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
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,'');
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

function loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,STOCK_TYPE){

    var STOCK_TYPE  = $("#STOCK_TYPE").val();
	
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
			bindItemEvents(STOCK_TYPE); 
			},
			error:function(data){
			console.log("Error: Something went wrong.");
			$("#tbody_ItemID").html('');                        
			},
		});

}

$('#Material').on('click','[id*="popupITEMID"]',function(event){

  var CODE = ''; 
  var NAME = ''; 
  var MUOM = ''; 
  var GROUP = ''; 
  var CTGRY = ''; 
  var BUNIT = ''; 
  var APART = ''; 
  var CPART = ''; 
  var OPART = ''; 
  var STOCK_TYPE = $(this).attr('id').split('_')[1] =="IN"?'_IN':'';
  $("#STOCK_TYPE").val(STOCK_TYPE);

  loadItem('',CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART,STOCK_TYPE);
          
  $("#ITEMIDpopup").show();

  var id    = $(this).attr('id');
  var id2   = $(this).parent().parent().find('[id*="ITEMID_REF'+STOCK_TYPE+'"]').attr('id');
  var id3   = $(this).parent().parent().find('[id*="ItemName'+STOCK_TYPE+'"]').attr('id');
  var id4   = $(this).parent().parent().find('[id*="Itemspec'+STOCK_TYPE+'"]').attr('id');
  var id5   = $(this).parent().parent().find('[id*="popupMUOM'+STOCK_TYPE+'"]').attr('id');
  var id6   = $(this).parent().parent().find('[id*="MAIN_UOMID_REF'+STOCK_TYPE+'"]').attr('id');
  var id7   = $(this).parent().parent().find('[id*="RATE'+STOCK_TYPE+'"]').attr('id');
  var id12  = $(this).parent().parent().find('[id*="TotalHiddenQty'+STOCK_TYPE+'"]').attr('id');
  var id13  = $(this).parent().parent().find('[id*="HiddenRowId'+STOCK_TYPE+'"]').attr('id');
  var id8   = $(this).parent().parent().find('[id*="popupALTUOM'+STOCK_TYPE+'"]').attr('id');
  var id9   = $(this).parent().parent().find('[id*="ALT_UOMID_REF'+STOCK_TYPE+'"]').attr('id');
  

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

function bindItemEvents(STOCK_TYPE){

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

        var itemid      = $(this).find('[id*="ITEMID_REF'+STOCK_TYPE+'"]').val();
        var exist_val   = itemid;

        if(txtval){
          if(desc6 == exist_val){
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
        $clone.find('[id*="popupITEMID'+STOCK_TYPE+'"]').val(texdesc);
        $clone.find('[id*="ITEMID_REF'+STOCK_TYPE+'"]').val(txtval);
        $clone.find('[id*="ItemName'+STOCK_TYPE+'"]').val(txtname);
        $clone.find('[id*="Itemspec'+STOCK_TYPE+'"]').val(txtspec);
        $clone.find('[id*="popupMUOM'+STOCK_TYPE+'"]').val(txtmuom);
        $clone.find('[id*="MAIN_UOMID_REF'+STOCK_TYPE+'"]').val(txtmuomid);
        $clone.find('[id*="RATE'+STOCK_TYPE+'"]').val(txtmuomqty);

        $clone.find('[id*="popupALTUOM'+STOCK_TYPE+'"]').val(txtauom);
        $clone.find('[id*="ALT_UOMID_REF'+STOCK_TYPE+'"]').val(txtauomid);
       
        
        $clone.find('[id*="TotalHiddenQty'+STOCK_TYPE+'"]').val('');
        $clone.find('[id*="HiddenRowId'+STOCK_TYPE+'"]').val('');

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
          var itemid = $(this).find('[id*="ITEMID_REF'+STOCK_TYPE+'"]').val();
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
  $clone.find('input:hidden').val('');

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

/*------------------Change of Rate-------------------------------*/
$("#Material").on('change', '[id*="RATE_"]', function(){
  var rate = $(this).val();
  var qty = $(this).parent().parent().find('[id*="QTY_"]').val();

  var amt = parseFloat(rate*qty).toFixed(2);
  $(this).parent().parent().find('[id*="VALUE_"]').val(amt);
});

$("#Material").on('change', '[id*="RATE_IN_"]', function(){
  var rate = $(this).val();
  var qty = $(this).parent().parent().find('[id*="QTY_IN_"]').val();

  var amt = parseFloat(rate*qty).toFixed(2);
  $(this).parent().parent().find('[id*="VALUE_IN_"]').val(amt);
});

/*------------------Change of Rate-------------------------------*/

function clickStoreDetails(ROW_ID,STOCK_TYPE){

  $("#FocusId").val('');

  var STOCK_TYPE_ID   =   STOCK_TYPE =="IN"?'_IN':'';
  var ITEMID_REF      =   $("#ITEMID_REF"+STOCK_TYPE_ID+"_"+ROW_ID).val();

  if(ITEMID_REF ===""){
    $("#FocusId").val("popupITEMID"+STOCK_TYPE_ID+"_"+ROW_ID);
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select item code.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
    return false;
  }
  else{
    getStoreDetails(ROW_ID,STOCK_TYPE,STOCK_TYPE_ID);
    $("#StoreModal").show();
    event.preventDefault();
  }
}

function getStoreDetails(ROW_ID,STOCK_TYPE,STOCK_TYPE_ID){

  var ITEMID_REF      = $("#ITEMID_REF"+STOCK_TYPE_ID+"_"+ROW_ID).val();
  var ITEMROWID       = $("#HiddenRowId"+STOCK_TYPE_ID+"_"+ROW_ID).val();
  var MAIN_UOMID_DES  = $("#popupMUOM"+STOCK_TYPE_ID+"_"+ROW_ID).val();
  var MAIN_UOMID_REF  = $("#MAIN_UOMID_REF"+STOCK_TYPE_ID+"_"+ROW_ID).val();
  var ST_ADJUST_TYPE  = STOCK_TYPE;
  
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
        ST_ADJUST_TYPE:ST_ADJUST_TYPE,
        ITEMROWID:ITEMROWID,
        ACTION_TYPE:'ADD'
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

  var NewIdArr    = [];
  var ROW_ID      = [];
  var Req         = [];
  var STOCK_TYPE  = [];
  var STORE_NAME  = [];

  $('#StoreTable').find('.participantRow33').each(function(){

    if($.trim($(this).find("[id*=UserQty]").val())!=""){  
      var UserQty       = parseFloat($.trim($(this).find("[id*=UserQty]").val()));
      var BatchId       = $.trim($(this).find("[id*=BATCHID]").val());
      var ROWID         = $.trim($(this).find("[id*=ROWID]").val());
      var TOTAL_STOCK   = parseFloat($.trim($(this).find("[id*=TOTAL_STOCK]").val()));
      var BATCHNOA      = $.trim($(this).find("[id*=BATCHNOA]").val());
      var STORE         = $.trim($(this).find("[id*=STORE_NAME]").val());

      if(jQuery.inArray(STORE, STORE_NAME) == -1){
        STORE_NAME.push(STORE);
      }

      STOCK_TYPE.push($(this).find("[id*=STOCK_TYPE]").val());
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

  var ST_ADJUST_TYPE  = STOCK_TYPE[0];
  var STOCK_TYPE_ID   = ST_ADJUST_TYPE =='IN'?'_IN':'';                     

  var ROW_ID    = ROW_ID[0];
  var QTY       = parseFloat($("#QTY"+STOCK_TYPE_ID+"_"+ROW_ID).val());
  var RATE      = parseFloat($("#RATE"+STOCK_TYPE_ID+"_"+ROW_ID).val());
  var VALUE     = (QTY*RATE);

  $("#HiddenRowId"+STOCK_TYPE_ID+"_"+ROW_ID).val(NewIdArr);
  $("#VALUE"+STOCK_TYPE_ID+"_"+ROW_ID).val(parseFloat(VALUE).toFixed(3));
  $("#STORE_NAME"+STOCK_TYPE_ID+"_"+ROW_ID).val(STORE_NAME);
  $("#StoreModal").hide();

});


function checkStoreQty(ROW_ID,itemid,userQty,key,stock){

    var STOCK_TYPE    = [];

    $('#StoreTable').find('.participantRow33').each(function(){
      var STOCK_TYPE_DATA = $(this).find("[id*=STOCK_TYPE]").val();
      STOCK_TYPE.push(STOCK_TYPE_DATA);
    
    });

    var ST_ADJUST_TYPE  = STOCK_TYPE[0];
    var STOCK_TYPE_ID   = ST_ADJUST_TYPE =='IN'?'_IN':'';

    if( ST_ADJUST_TYPE =='OUT' && parseFloat(userQty) > parseFloat(stock) ){
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
   
      $("#TotalHiddenQty"+STOCK_TYPE_ID+"_"+ROW_ID).val(TotalQty);
      $("#HiddenRowId"+STOCK_TYPE_ID+"_"+ROW_ID).val(NewIdArr);
      $("#QTY"+STOCK_TYPE_ID+"_"+ROW_ID).val(TotalQty);  

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
  var lastdt = <?php echo json_encode($objlastdt[0]->ST_ST_DOCDT); ?>;
  var today = new Date(); 
  var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;

  $('#ST_ST_DOCDT').attr('min',lastdt);
  $('#ST_ST_DOCDT').attr('max',sodate);
  $("[id*='RATE']").ForceNumericOnly();

  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  $('#ST_ST_DOCDT').val(today);
  

});
</script>
@endpush
