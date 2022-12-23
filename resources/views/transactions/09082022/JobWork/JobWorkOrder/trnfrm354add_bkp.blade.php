@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Job Work Order</a></div>

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
    </div>
  </div>
</div>


<form id="production_order_add" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >  
  <div class="container-fluid filter">
	  <div class="inner-form">

      <div class="row">
        <div class="col-lg-2 pl"><p>JWO No</p></div>
        <div class="col-lg-2 pl">
          @if(isset($objSON->SYSTEM_GRSR) && $objSON->SYSTEM_GRSR == "1")
          <input type="text" name="PRO_NO" id="PRO_NO" value="{{ $objDataNo }}" class="form-control mandatory" readonly  autocomplete="off"  style="text-transform:uppercase" autofocus >
          @endif

          @if(isset($objSON->MANUAL_SR) && $objSON->MANUAL_SR == "1")
          <input type="text" name="PRO_NO" id="PRO_NO" value="{{ old('PRO_NO') }}" class="form-control mandatory" maxlength="{{$objSON->MANUAL_MAXLENGTH}}" autocomplete="off" style="text-transform:uppercase" autofocus >
          @endif
          <span class="text-danger" id="ERROR_PRO_NO"></span>
        </div>
              
        <div class="col-lg-2 pl"><p>JWO Date</p></div>
        <div class="col-lg-2 pl">
          <input type="date" name="PRO_DT" id="PRO_DT" value="{{ old('PRO_DT') }}" class="form-control mandatory"  placeholder="dd/mm/yyyy" maxlength="10" >
          <span class="text-danger" id="ERROR_PRO_DT"></span>
        </div>

        <div class="col-lg-2 pl"><p>Vendor</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="txtvendor_popup" id="txtvendor_popup" class="form-control"  readonly >
          <input type="hidden" name="VID_REF" id="VID_REF" >
          <span class="text-danger" id="ERROR_VID_REF"></span>
        </div>

      </div>      
    </div>

	  <div class="container-fluid">

      <div class="row">
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
          <li><a data-toggle="tab" href="#udf">UDF</a></li>
        </ul>
			
			  <div class="tab-content">

				  <div id="Material" class="tab-pane fade in active">
					  <div class="table-responsive table-wrapper-scroll-y" style="margin-top:10px;height:400px;" >
						  <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
							  <thead id="thead1"  style="position: sticky;top: 0">
								  <tr>
                    <th>PRO No</th>
                    <th>Item Code </th>
                    <th>Item Name</th>
                    <th>UOM</th>
                    <th>Produced Qty</th>
                    <th>Job Work Order Qty</th>
                    <th>EDA</th>
                    <th>Rate</th>
                    <th>Amount</th>
                    <th>Action <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="1"></th>
								  </tr>
							  </thead>
							  <tbody>
								  <tr  class="participantRow">
                    <td hidden><input type="hidden" id="0" > </td>
                    <td hidden><input  type="text" name="txtSL_popup_0" id="txtSL_popup_0" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                    <td  hidden><input type="text" name="SLID_REF_0" id="SLID_REF_0" class="form-control" autocomplete="off" /></td>

                    <td><input  type="text" name="txtPRO_popup_0" id="txtPRO_popup_0" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                    <td  hidden><input type="text" name="PROID_REF_0" id="PROID_REF_0" class="form-control" autocomplete="off" /></td>
                    
                    <td  hidden><input type="text" name="SOID_REF_0" id="SOID_REF_0"class="form-control" autocomplete="off" /></td>
                    <td  hidden ><input type="text" name="SQID_REF_0" id="SQID_REF_0" class="form-control" autocomplete="off" /></td>
                    <td  hidden ><input type="text" name="SEID_REF_0" id="SEID_REF_0" class="form-control" autocomplete="off" /></td>
                  
                    <td><input  type="text" name="popupITEMID_0" id="popupITEMID_0" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                    <td   hidden><input type="text" name="ITEMID_REF_0" id="ITEMID_REF_0" class="form-control" autocomplete="off" /></td>
                  
                    <td><input type="text" name="ItemName_0" id="ItemName_0" class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>
               
                    <td><input type="text" name="popupMUOM_0" id="popupMUOM_0" class="form-control"  autocomplete="off"  readonly style="width:100px;"/></td>
                    <td   hidden><input type="text" name="MAIN_UOMID_REF_0" id="MAIN_UOMID_REF_0" class="form-control"  autocomplete="off" /></td>
              
                    <td><input type="text"   name="QTY_0" id="QTY_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  style="width:100px;"  /></td>
                    <td hidden><input type="text"   name="BL_SOQTY_0" id="BL_SOQTY_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  style="width:100px;"  /></td>
                    <td><input type="text"   name="PD_OR_QTY_0" id="PD_OR_QTY_0" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="rate_amount(this.id),get_materital_item()"  /></td>
                   
                    <td><input type="date"   name="EDA_0" id="EDA_0" class="form-control"   autocomplete="off"   /></td>
                    <td><input type="text"   name="RATE_0" id="RATE_0" class="form-control three-digits" value="0.00" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="rate_amount(this.id)"  /></td>
                    <td><input type="text"   name="AMOUNT_0" id="AMOUNT_0" class="form-control three-digits" readonly maxlength="13"  autocomplete="off" style="width:100px;" onKeyup="rate_amount(this.id)"  /></td>
                   
                    <td hidden><input type="text"   name="MAINTROWID_0" id="MAINTROWID_0" class="form-control " style="width:100px;"  /></td>
                    
                    <td align="center" >
                      <button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                      <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                    </td>

								  </tr>
							  </tbody>
					    </table>

              <div id="material_item">
                
              </div>


					  </div>	
				  </div>


          <div id="udf" class="tab-pane fade">
					<div class="table-responsive table-wrapper-scroll-y " style="margin-top:10px;height:280px;width:50%;">
						<table id="example3" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
							<thead id="thead1"  style="position: sticky;top: 0">
							  <tr >
								<th>UDF Fields<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2"></th>
								<th>Value / Comments</th>
								<th>Action</th>
							  </tr>
							</thead>
							<tbody>
              @foreach($objUdfData as $uindex=>$uRow)
                <tr  class="participantRow3">
                    <td><input type="text" name={{"popupSEID_".$uindex}} id={{"popupSEID_".$uindex}} class="form-control" value="{{$uRow->LABEL}}" autocomplete="off"  readonly/></td>
                    <td hidden><input type="hidden" name={{"UDF_".$uindex}} id={{"UDF_".$uindex}} class="form-control" value="{{$uRow->UDFJWOID}}" autocomplete="off"   /></td>
                    <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->ISMANDATORY}}" class="form-control"   autocomplete="off" /></td>
                    <td id={{"udfinputid_".$uindex}} >
                      
                    </td>
                    <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                    
                </tr>
                <tr></tr>
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
        <td class="ROW2"><input type="text" id="vendorcodesearch" class="form-control" onkeyup="VendorCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="vendornamesearch" class="form-control" onkeyup="VendorNameFunction()"></td>
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


<div id="PROpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='PRO_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>PRO No</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="PROTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden"  id="hdn_PROid"/>
            <input type="hidden" id="hdn_PROid2"/>
            <input type="hidden" id="hdn_PROid3"/>
            </td>
          </tr>

      <tr>
        <th class="ROW1">Select</th> 
        <th class="ROW2">PRO NO</th>
        <th class="ROW3">Date</th>
      </tr>
    </thead>
    <tbody>

      <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="PROcodesearch" class="form-control" onkeyup="PROCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="PROnamesearch" class="form-control" onkeyup="PRONameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="PROTable2" class="display nowrap table  table-striped table-bordered"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_PRO">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="SUBITEMpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='SUBITEM_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sub Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SUBITEMTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="text"  id="hdn_SUBITEMid1"/>
            <input type="text"  id="hdn_SUBITEMid2"/>
            <input type="text"  id="hdn_SUBITEMid3"/>
            <input type="text"  id="hdn_SUBITEMid4"/>
            <input type="text"  id="hdn_SUBITEMid5"/>
            <input type="text"  id="hdn_SUBITEMid6"/>
            <input type="text"  id="hdn_SUBITEMid7"/>
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
        <td class="ROW2"><input type="text" id="SUBITEMcodesearch" class="form-control" onkeyup="SUBITEMCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="SUBITEMnamesearch" class="form-control" onkeyup="SUBITEMNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="SUBITEMTable2" class="display nowrap table  table-striped table-bordered"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_SUBITEM">     
        
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
                  <input type="text" name="fieldid1" id="hdn_ItemID1"/>
                  <input type="text" name="fieldid2" id="hdn_ItemID2"/>
                  <input type="text" name="fieldid3" id="hdn_ItemID3"/>
                  <input type="text" name="fieldid4" id="hdn_ItemID4"/>
                  <input type="text" name="fieldid5" id="hdn_ItemID5"/>
                  <input type="text" name="fieldid6" id="hdn_ItemID6"/>
                  <input type="text" name="fieldid7" id="hdn_ItemID7"/>
                  <input type="text" name="fieldid8" id="hdn_ItemID8"/>
                  <input type="text" name="fieldid9" id="hdn_ItemID9"/>
                  <input type="text" name="fieldid10" id="hdn_ItemID10"/>
                  <input type="text" name="fieldid11" id="hdn_ItemID11"/>
                  <input type="text" name="fieldid18" id="hdn_ItemID18"/>
                  <input type="text" name="fieldid19" id="hdn_ItemID19"/>
                  <input type="text" name="fieldid20" id="hdn_ItemID20"/>
                  <input type="text" name="hdn_ItemID21" id="hdn_ItemID21" value="0"/>
                  <input type="text" name="fieldid22" id="hdn_ItemID22"/>
                  <input type="text" name="fieldid23" id="hdn_ItemID23"/>
                  <input type="text" name="fieldid24" id="hdn_ItemID24"/>
                  <input type="text" name="fieldid25" id="hdn_ItemID25"/>
                </td>
              </tr>

              <tr>
                <th style="width:8%;" id="all-check">Select</th>
                <th style="width:10%;">Item Code</th>
                <th style="width:10%;">Name</th>
                <th style="width:8%;">Main UOM</th>
                <th style="width:8%;">Qty</th>
                <th style="width:8%;">Item Group</th>
                <th style="width:8%;">Item Category</th>
                <th style="width:8%;">Business Unit</th>
                <th style="width:8%;">ALPS Part No.</th>
                <th style="width:8%;">Customer Part No.</th>
                <th style="width:8%;">OEM Part No.</th>
                <th style="width:8%;">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="width:8%;text-align:center;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
                <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" onkeyup="ItemCodeFunction()"></td>
                <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" onkeyup="ItemNameFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control" onkeyup="ItemUOMFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control" onkeyup="ItemQTYFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" onkeyup="ItemGroupFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" onkeyup="ItemCategoryFunction()"></td>
                
                <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" onkeyup="ItemBUFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemAPNsearch" class="form-control" onkeyup="ItemAPNFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemCPNsearch" class="form-control" onkeyup="ItemCPNFunction()"></td>
                <td style="width:8%;"><input type="text" id="ItemOEMPNsearch" class="form-control" onkeyup="ItemOEMPNFunction()"></td>
                
                <td style="width:8%;"><input  type="text" id="ItemStatussearch" class="form-control" onkeyup="ItemStatusFunction()"></td>
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

$("#btnSave" ).click(function() {
    var formReqData = $("#production_order_add");
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
  // if($("#"+$("#FocusId").val())!=undefined){
  //   $("#"+$("#FocusId").val()).focus();
  // }  
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

  var trnFormReq  = $("#production_order_add");
  var formData    = trnFormReq.serialize();

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{ route("transaction",[$FormId,"save"])}}',
    type:'POST',
    data:formData,
    success:function(data) {

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
  var PRO_NO      = $.trim($("#PRO_NO").val());
  var PRO_DT      = $.trim($("#PRO_DT").val());  
  
  if(PRO_NO ===""){
      $("#FocusId").val('PRO_NO');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('PRO No is required.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
  }
  else if(PRO_DT ===""){
      $("#FocusId").val('PRO_DT');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select PRO Date.');
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
    var allblank6   = [];
    var allblank7   = [];
    var allblank8   = [];
    var focustext   = "";
      
    $('#example2').find('.participantRow').each(function(){

      if($.trim($(this).find("[id*=SLID_REF]").val()) ===""){
        allblank1.push('false');
        focustext = $(this).find("[id*=txtSL_popup]").attr('id');
      }
      else if($.trim($(this).find("[id*=SOID_REF]").val()) ===""){
        allblank2.push('false');
        focustext = $(this).find("[id*=txtPRO_popup]").attr('id');
      }
      else if($.trim($(this).find("[id*=ITEMID_REF]").val()) ===""){
        allblank3.push('false');
        focustext = $(this).find("[id*=popupITEMID]").attr('id');
      }
      else if($.trim($(this).find("[id*=MAIN_UOMID_REF]").val()) ===""){
        allblank4.push('false');
        focustext = $(this).find("[id*=popupMUOM]").attr('id');
      }
      else if($.trim($(this).find("[id*=QTY]").val()) ===""){
        allblank5.push('false');
        focustext = $(this).find("[id*=QTY]").attr('id');
      }
      else if($.trim($(this).find("[id*=PD_OR_QTY]").val()) ==="" || parseFloat($.trim($(this).find("[id*=PD_OR_QTY]").val())) <=0){
        allblank6.push('false');
        focustext = $(this).find("[id*=PD_OR_QTY]").attr('id');
      }
      else if(parseFloat($.trim($(this).find("[id*=PD_OR_QTY]").val())).toFixed(3) > parseFloat($.trim($(this).find("[id*=BL_SOQTY_]").val())).toFixed(3) ){
        allblank7.push('false');
        focustext = $(this).find("[id*=PD_OR_QTY]").attr('id');
      }
      else{
        allblank1.push('true');
        allblank2.push('true');
        allblank3.push('true');
        allblank4.push('true');
        allblank5.push('true');
        allblank6.push('true');
        allblank7.push('true');
        focustext   = "";
      }

    });

    $('#example3').find('.participantRow3').each(function(){
      if($.trim($(this).find("[id*=UDF]").val())!=""){
        if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
          if($.trim($(this).find('[id*="udfvalue"]').val()) != ""){
            allblank8.push('true');
          }
          else{
            allblank8.push('false');
          }
        }  
      }                
    });
    
    if(jQuery.inArray("false", allblank1) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Customer In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank2) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select So No In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank3) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Item In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank4) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Main UOM is missing in in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank5) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Qty cannot be blank in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank6) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Production Order Qty should be greater than 0 in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank7) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Production Order qty cannot be greater then balance so qty in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank8) !== -1){
      $("#FocusId").val(focustext);
      $("#alert").modal('show');
      $("#AlertMessage").text('Please enter  Value / Comment in UDF Tab.');
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

  var trnFormReq  = $("#production_order_add");
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
              showError('ERROR_PRO_NO',data.msg);
              $("#PRO_NO").focus();
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

/*================================== VENDOR POPUP FUNCTION =================================*/

let vendor_tid = "#VendorCodeTable2";
let vendor_tid2 = "#VendorCodeTable";
let vendor_headers = document.querySelectorAll(vendor_tid2 + " th");

      
vendor_headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(vendor_tid, ".clsvendorid", "td:nth-child(" + (i + 1) + ")");
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
    url:'{{route("transaction",[$FormId,"getVendor"])}}',
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

  $(".clsvendorid").click(function(){
    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");
    
    $('#txtvendor_popup').val(texdesc);
    $('#VID_REF').val(txtval);

    $("#vendoridpopup").hide();
    $("#vendorcodesearch").val(''); 
    $("#vendornamesearch").val(''); 
    VendorCodeFunction();
    event.preventDefault();
  });

}

/*================================== PRO POPUP FUNCTION =================================*/

let PROTable2 = "#PROTable2";
let PROTable = "#PROTable";
let PROheaders = document.querySelectorAll(PROTable + " th");

PROheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(PROTable2, ".clssPROid", "td:nth-child(" + (i + 1) + ")");
  });
});

function PROCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("PROcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("PROTable2");
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

function PRONameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("PROnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("PROTable2");
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

$('#Material').on('click','[id*="txtPRO_popup"]',function(event){

  $('#hdn_PROid').val($(this).attr('id'));
  $('#hdn_PROid2').val($(this).parent().parent().find('[id*="PROID_REF"]').attr('id'));

  var fieldid = $(this).parent().parent().find('[id*="PROID_REF"]').attr('id');

  var VID_REF      =  $("#VID_REF").val();

  if(VID_REF ===""){
    $("#FocusId").val('txtvendor_popup');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select vendor.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  }
  else{

    $("#PROpopup").show();
    $("#tbody_PRO").html('loading...');

    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })

    $.ajax({
        url:'{{route("transaction",[$FormId,"getProNo"])}}',
        type:'POST',
        data:{'id':VID_REF,'fieldid':fieldid},
        success:function(data) {
          $("#tbody_PRO").html(data);
          BindSO();
          showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_PRO").html('');
        },
    });

    $(this).parent().parent().find('[id*="popupITEMID"]').val('');
    $(this).parent().parent().find('[id*="ITEMID_REF"]').val('');
    $(this).parent().parent().find('[id*="ItemName"]').val('');
    $(this).parent().parent().find('[id*="popupMUOM"]').val('');
    $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').val('');
    $(this).parent().parent().find('[id*="QTY"]').val('');
    $(this).parent().parent().find('[id*="BL_SOQTY"]').val('');
    $(this).parent().parent().find('[id*="PD_OR_QTY"]').val('');
    $("#material_item").html('');

  }

});

$("#PRO_closePopup").click(function(event){
  $("#PROpopup").hide();
});

function BindSO(){
  $(".clssPROid").click(function(){
    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");

    var txtid= $('#hdn_PROid').val();
    var txt_id2= $('#hdn_PROid2').val();

    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $("#PROpopup").hide();
    
    $("#PROcodesearch").val(''); 
    $("#PROnamesearch").val(''); 
    PROCodeFunction();
    event.preventDefault();
  });
}

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

function ItemNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Itemnamesearch");
  filter = input.value.toUpperCase();
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

function ItemUOMFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemUOMsearch");
  filter = input.value.toUpperCase();
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

function ItemCategoryFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemCategorysearch");
  filter = input.value.toUpperCase();
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

function ItemBUFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemBUsearch");
	filter = input.value.toUpperCase();
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

function ItemAPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemAPNsearch");
	filter = input.value.toUpperCase();
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

function ItemCPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemCPNsearch");
	filter = input.value.toUpperCase();
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

function ItemOEMPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemOEMPNsearch");
	filter = input.value.toUpperCase();
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

function ItemStatusFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemStatussearch");
  filter = input.value.toUpperCase();
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

$('#Material').on('click','[id*="popupITEMID"]',function(event){

  var VID_REF       = $("#VID_REF").val();
  var PROID_REF     = $(this).parent().parent().find('[id*="PROID_REF"]').val();
  var txtPRO_popup  = $(this).parent().parent().find('[id*="txtPRO_popup"]').attr('id');

  if(VID_REF ===""){
    $("#FocusId").val('VID_REF');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select vendor.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  }
  else if(PROID_REF ===""){
    $("#FocusId").val(txtPRO_popup);
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select PRO No.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  }
  else{

    $('.js-selectall').prop('disabled', true);   

    $("#tbody_ItemID").html('loading...');
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
        url:'{{route("transaction",[$FormId,"getItemDetails"])}}',
        type:'POST',
        data:{'VID_REF':VID_REF,'PROID_REF':PROID_REF,'status':'A'},
        success:function(data) {
          $("#tbody_ItemID").html(data);    
          bindItemEvents();   
          $('.js-selectall').prop('disabled', false);                     
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_ItemID").html('');                        
        },
    }); 
          
    $("#ITEMIDpopup").show();

    var id1   = $(this).attr('id');
    var id2   = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
    var id3   = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
    var id4   = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
    var id5   = $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
    var id6   = $(this).parent().parent().find('[id*="QTY"]').attr('id');
    var id7   = $(this).parent().parent().find('[id*="BL_SOQTY"]').attr('id');
    var id8   = $(this).parent().parent().find('[id*="PD_OR_QTY"]').attr('id');
    var id9   = $(this).parent().parent().find('[id*="SQID_REF"]').attr('id');
    var id10  = $(this).parent().parent().find('[id*="SEID_REF"]').attr('id');
    var id11  = $(this).parent().parent().find('[id*="SOID_REF"]').attr('id');


    $('#hdn_ItemID1').val(id1);
    $('#hdn_ItemID2').val(id2);
    $('#hdn_ItemID3').val(id3);
    $('#hdn_ItemID4').val(id4);
    $('#hdn_ItemID5').val(id5);
    $('#hdn_ItemID6').val(id6);
    $('#hdn_ItemID7').val(id7);
    $('#hdn_ItemID8').val(id8);
    $('#hdn_ItemID9').val(id9);
    $('#hdn_ItemID10').val(id10);
    $('#hdn_ItemID11').val(id11);

  }

});

$("#ITEMID_closePopup").click(function(event){
  $("#ITEMIDpopup").hide();
});

function bindItemEvents(){

  $('#ItemIDTable2').off(); 
 
  $('.js-selectall').change(function(){
	
      var isChecked = $(this).prop("checked");
      var selector = $(this).data('target');
      $(selector).prop("checked", isChecked);
          
      $('#ItemIDTable2').find('.clsitemid').each(function(){

        var fieldid             =   $(this).attr('id');
        var item_id             =   $("#txt"+fieldid+"").data("desc1");
        var item_code           =   $("#txt"+fieldid+"").data("desc2");
        var item_name           =   $("#txt"+fieldid+"").data("desc3");
        var item_main_uom_id    =   $("#txt"+fieldid+"").data("desc4");
        var item_main_uom_code  =   $("#txt"+fieldid+"").data("desc5");
        var item_qty            =   $("#txt"+fieldid+"").data("desc6");
        var item_unique_row_id  =   $("#txt"+fieldid+"").data("desc7");
        var item_sqid           =   $("#txt"+fieldid+"").data("desc8");
        var item_seid           =   $("#txt"+fieldid+"").data("desc9");
        var item_proid          =   $("#txt"+fieldid+"").data("desc10");
        var item_soid           =   $("#txt"+fieldid+"").data("desc11");
        var item_soqty          =   $("#txt"+fieldid+"").data("desc12");

        if($(this).find('[id*="chkId"]').is(":checked") == true){

          $('#example2').find('.participantRow').each(function(){

            var PROID_REF   =   $(this).find('[id*="PROID_REF"]').val();
            var SOID_REF    =   $(this).find('[id*="SOID_REF"]').val();
            var ITEMID_REF  =   $(this).find('[id*="ITEMID_REF"]').val();
            var SQID_REF    =   $(this).find('[id*="SQID_REF"]').val();
            var SEID_REF    =   $(this).find('[id*="SEID_REF"]').val();

            var exist_val   =   PROID_REF+"_"+SOID_REF+"_"+SQID_REF+"_"+SEID_REF+"_"+ITEMID_REF;

            if(item_id){
              if(item_unique_row_id == exist_val){
                $("#ITEMIDpopup").hide();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Item already exists.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');

                $('#hdn_ItemID1').val('');
                $('#hdn_ItemID2').val('');
                $('#hdn_ItemID3').val('');
                $('#hdn_ItemID4').val('');
                $('#hdn_ItemID5').val('');
                $('#hdn_ItemID6').val('');
                $('#hdn_ItemID7').val('');
                $('#hdn_ItemID8').val('');
                $('#hdn_ItemID9').val('');
                $('#hdn_ItemID10').val('');
                $('#hdn_ItemID11').val('');
                
                item_id             =   '';
                item_code           =   '';
                item_name           =   '';
                item_main_uom_id    =   '';
                item_main_uom_code  =   '';
                item_qty            =   '';
                item_unique_row_id  =   '';
                item_sqid           =   '';
                item_seid           =   '';
                item_soid           =   '';
                item_soqty          =   '';
                return false;
              }               
            } 
                    
          });

          if($('#hdn_ItemID1').val() == "" && item_id != ''){

            var $tr       =   $('.material').closest('table');
            var allTrs    =   $tr.find('.participantRow').last();
            var lastTr    =   allTrs[allTrs.length-1];
            var $clone    =   $(lastTr).clone();

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
            $clone.find('[id*="popupITEMID"]').val(item_code);
            $clone.find('[id*="ITEMID_REF"]').val(item_id);
            $clone.find('[id*="ItemName"]').val(item_name);
            $clone.find('[id*="popupMUOM"]').val(item_main_uom_code);
            $clone.find('[id*="MAIN_UOMID_REF"]').val(item_main_uom_id);
            $clone.find('[id*="QTY"]').val(item_soqty);
            $clone.find('[id*="BL_SOQTY"]').val(item_qty);
            $clone.find('[id*="PD_OR_QTY"]').val(item_qty);
          
            $clone.find('[id*="MAINTROWID"]').val(item_unique_row_id); 
            $clone.find('[id*="SQID_REF"]').val(item_sqid);
            $clone.find('[id*="SEID_REF"]').val(item_seid);
            $clone.find('[id*="SOID_REF"]').val(item_soid);

            $tr.closest('table').append($clone);   
            var rowCount = $('#Row_Count1').val();
            rowCount    = parseInt(rowCount)+1;
            $('#Row_Count1').val(rowCount);
            $("#ITEMIDpopup").hide();
            get_materital_item();
            event.preventDefault();

          }
          else{

            var txt_id1   =   $('#hdn_ItemID1').val();
            var txt_id2   =   $('#hdn_ItemID2').val();
            var txt_id3   =   $('#hdn_ItemID3').val();
            var txt_id4   =   $('#hdn_ItemID4').val();
            var txt_id5   =   $('#hdn_ItemID5').val();
            var txt_id6   =   $('#hdn_ItemID6').val();
            var txt_id7   =   $('#hdn_ItemID7').val();
            var txt_id8   =   $('#hdn_ItemID8').val();
            var txt_id9   =   $('#hdn_ItemID9').val();
            var txt_id10  =   $('#hdn_ItemID10').val();
            var txt_id11  =   $('#hdn_ItemID11').val();
          
            if($.trim(txt_id1)!=""){
              $('#'+txt_id1).val(item_code);
            }
            if($.trim(txt_id2)!=""){
              $('#'+txt_id2).val(item_id);
              $('#'+txt_id2).parent().parent().find('[id*="MAINTROWID"]').val(item_unique_row_id);
            }
            if($.trim(txt_id3)!=""){
              $('#'+txt_id3).val(item_name);
            }
            if($.trim(txt_id4)!=""){
              $('#'+txt_id4).val(item_main_uom_code);
            }
            if($.trim(txt_id5)!=""){
              $('#'+txt_id5).val(item_main_uom_id);
            }
            if($.trim(txt_id6)!=""){
              $('#'+txt_id6).val(item_soqty);
            }
            if($.trim(txt_id7)!=""){
              $('#'+txt_id7).val(item_qty);
            }
            if($.trim(txt_id8)!=""){
              $('#'+txt_id8).val(item_qty);
            }
            if($.trim(txt_id9)!=""){
              $('#'+txt_id9).val(item_sqid);
            }
            if($.trim(txt_id10)!=""){
              $('#'+txt_id10).val(item_seid);
            }
            if($.trim(txt_id11)!=""){
              $('#'+txt_id11).val(item_soid);
            }
            $('#hdn_ItemID1').val('');
            $('#hdn_ItemID2').val('');
            $('#hdn_ItemID3').val('');
            $('#hdn_ItemID4').val('');
            $('#hdn_ItemID5').val('');
            $('#hdn_ItemID6').val('');
            $('#hdn_ItemID7').val('');
            $('#hdn_ItemID8').val('');
            $('#hdn_ItemID9').val('');
            $('#hdn_ItemID10').val('');
            $('#hdn_ItemID11').val('');
            get_materital_item();
          }
                  
        
        }
        
      });
    
      $('.js-selectall').prop("checked", false);   
      $("#ITEMIDpopup").hide();
      
  });



  $('[id*="chkId"]').change(function(){

    var fieldid             =   $(this).parent().parent().attr('id');
    var item_id             =   $("#txt"+fieldid+"").data("desc1");
    var item_code           =   $("#txt"+fieldid+"").data("desc2");
    var item_name           =   $("#txt"+fieldid+"").data("desc3");
    var item_main_uom_id    =   $("#txt"+fieldid+"").data("desc4");
    var item_main_uom_code  =   $("#txt"+fieldid+"").data("desc5");
    var item_qty            =   $("#txt"+fieldid+"").data("desc6");
    var item_unique_row_id  =   $("#txt"+fieldid+"").data("desc7");
    var item_sqid           =   $("#txt"+fieldid+"").data("desc8");
    var item_seid           =   $("#txt"+fieldid+"").data("desc9");
    var item_proid          =   $("#txt"+fieldid+"").data("desc10");
    var item_soid           =   $("#txt"+fieldid+"").data("desc11");
    var item_soqty          =   $("#txt"+fieldid+"").data("desc12");

    if($(this).is(":checked") == true) {

      $('#example2').find('.participantRow').each(function(){

        var PROID_REF   =   $(this).find('[id*="PROID_REF"]').val();
        var SOID_REF    =   $(this).find('[id*="SOID_REF"]').val();
        var ITEMID_REF  =   $(this).find('[id*="ITEMID_REF"]').val();
        var SQID_REF    =   $(this).find('[id*="SQID_REF"]').val();
        var SEID_REF    =   $(this).find('[id*="SEID_REF"]').val();

        var exist_val   =   PROID_REF+"_"+SOID_REF+"_"+SQID_REF+"_"+SEID_REF+"_"+ITEMID_REF;

        if(item_id){
          if(item_unique_row_id == exist_val){
            $("#ITEMIDpopup").hide();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Item already exists.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');

            $('#hdn_ItemID1').val('');
            $('#hdn_ItemID2').val('');
            $('#hdn_ItemID3').val('');
            $('#hdn_ItemID4').val('');
            $('#hdn_ItemID5').val('');
            $('#hdn_ItemID6').val('');
            $('#hdn_ItemID7').val('');
            $('#hdn_ItemID8').val('');
            $('#hdn_ItemID9').val('');
            $('#hdn_ItemID10').val('');
            $('#hdn_ItemID11').val('');
             
            item_id             =   '';
            item_code           =   '';
            item_name           =   '';
            item_main_uom_id    =   '';
            item_main_uom_code  =   '';
            item_qty            =   '';
            item_unique_row_id  =   '';
            item_sqid           =   '';
            item_seid           =   '';
            item_soid           =   '';
            item_soqty          =   '';
            return false;
          }               
        } 
                 
      });

      if($('#hdn_ItemID1').val() == "" && item_id != ''){

        var $tr       =   $('.material').closest('table');
        var allTrs    =   $tr.find('.participantRow').last();
        var lastTr    =   allTrs[allTrs.length-1];
        var $clone    =   $(lastTr).clone();

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
        $clone.find('[id*="popupITEMID"]').val(item_code);
        $clone.find('[id*="ITEMID_REF"]').val(item_id);
        $clone.find('[id*="ItemName"]').val(item_name);
        $clone.find('[id*="popupMUOM"]').val(item_main_uom_code);
        $clone.find('[id*="MAIN_UOMID_REF"]').val(item_main_uom_id);
        $clone.find('[id*="QTY"]').val(item_soqty);
        $clone.find('[id*="BL_SOQTY"]').val(item_qty);
        $clone.find('[id*="PD_OR_QTY"]').val(item_qty);
       
        $clone.find('[id*="MAINTROWID"]').val(item_unique_row_id); 
        $clone.find('[id*="SQID_REF"]').val(item_sqid);
        $clone.find('[id*="SEID_REF"]').val(item_seid);
        $clone.find('[id*="SOID_REF"]').val(item_soid);

        $tr.closest('table').append($clone);   
        var rowCount = $('#Row_Count1').val();
        rowCount    = parseInt(rowCount)+1;
        $('#Row_Count1').val(rowCount);
        $("#ITEMIDpopup").hide();
        get_materital_item();
        event.preventDefault();

      }
      else{

        var txt_id1   =   $('#hdn_ItemID1').val();
        var txt_id2   =   $('#hdn_ItemID2').val();
        var txt_id3   =   $('#hdn_ItemID3').val();
        var txt_id4   =   $('#hdn_ItemID4').val();
        var txt_id5   =   $('#hdn_ItemID5').val();
        var txt_id6   =   $('#hdn_ItemID6').val();
        var txt_id7   =   $('#hdn_ItemID7').val();
        var txt_id8   =   $('#hdn_ItemID8').val();
        var txt_id9   =   $('#hdn_ItemID9').val();
        var txt_id10  =   $('#hdn_ItemID10').val();
        var txt_id11  =   $('#hdn_ItemID11').val();
       
        if($.trim(txt_id1)!=""){
          $('#'+txt_id1).val(item_code);
        }
        if($.trim(txt_id2)!=""){
          $('#'+txt_id2).val(item_id);
          $('#'+txt_id2).parent().parent().find('[id*="MAINTROWID"]').val(item_unique_row_id);
        }
        if($.trim(txt_id3)!=""){
          $('#'+txt_id3).val(item_name);
        }
        if($.trim(txt_id4)!=""){
          $('#'+txt_id4).val(item_main_uom_code);
        }
        if($.trim(txt_id5)!=""){
          $('#'+txt_id5).val(item_main_uom_id);
        }
        if($.trim(txt_id6)!=""){
          $('#'+txt_id6).val(item_soqty);
        }
        if($.trim(txt_id7)!=""){
          $('#'+txt_id7).val(item_qty);
        }
        if($.trim(txt_id8)!=""){
          $('#'+txt_id8).val(item_qty);
        }
        if($.trim(txt_id9)!=""){
          $('#'+txt_id9).val(item_sqid);
        }
        if($.trim(txt_id10)!=""){
          $('#'+txt_id10).val(item_seid);
        }
        if($.trim(txt_id11)!=""){
          $('#'+txt_id11).val(item_soid);
        }
        $('#hdn_ItemID1').val('');
        $('#hdn_ItemID2').val('');
        $('#hdn_ItemID3').val('');
        $('#hdn_ItemID4').val('');
        $('#hdn_ItemID5').val('');
        $('#hdn_ItemID6').val('');
        $('#hdn_ItemID7').val('');
        $('#hdn_ItemID8').val('');
        $('#hdn_ItemID9').val('');
        $('#hdn_ItemID10').val('');
        $('#hdn_ItemID11').val('');
        get_materital_item();
      }
              
      $("#ITEMIDpopup").hide();
      event.preventDefault();
    }
    else if($(this).is(":checked") == false){

      var id = item_id;
      var r_count = $('#Row_Count1').val();

      $('#example2').find('.participantRow').each(function(){
        var ITEMID_REF = $(this).find('[id*="ITEMID_REF"]').val();

        if(id == ITEMID_REF){
          var rowCount = $('#Row_Count1').val();

          if (rowCount > 1) {
            $(this).closest('.participantRow').remove(); 
            rowCount = parseInt(rowCount)-1;
            $('#Row_Count1').val(rowCount);
          }
          else {
            $(document).find('.dmaterial').prop('disabled', true);  
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

} //bindItemEvents

/*================================== ADD/REMOVE FUNCTION ==================================*/

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
  $clone.find('input:hidden').val('');
  $clone.find('[id*="RATE_"]').val('0.00');

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
		get_materital_item();
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


/*================================== SUB MATERIAL POPUP FUNCTION =================================*/

let SUBITEMTable2 = "#SUBITEMTable2";
let SUBITEMTable = "#SUBITEMTable";
let SUBITEMheaders = document.querySelectorAll(SUBITEMTable + " th");

SUBITEMheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(SUBITEMTable2, ".clssSUBITEMid", "td:nth-child(" + (i + 1) + ")");
  });
});

function SUBITEMCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("SUBITEMcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("SUBITEMTable2");
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

function SUBITEMNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("SUBITEMnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("SUBITEMTable2");
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

$('#Material').on('click','[id*="txtSUBITEM_popup"]',function(event){

  $('#hdn_SUBITEMid1').val($(this).attr('id'));
  $('#hdn_SUBITEMid2').val($(this).parent().parent().find('[id*="SUBITEM_NAME"]').attr('id'));
  $('#hdn_SUBITEMid3').val($(this).parent().parent().find('[id*="REQ_ITEMID_REF"]').attr('id'));
  $('#hdn_SUBITEMid4').val($(this).parent().parent().find('[id*="REQ_MAIN_ITEMID_REF"]').attr('id'));
  $('#hdn_SUBITEMid5').val($(this).parent().parent().find('[id*="REQ_BOM_QTY"]').attr('id'));
  $('#hdn_SUBITEMid6').val($(this).parent().parent().find('[id*="REQ_INPUT_PD_OR_QTY"]').attr('id'));
  $('#hdn_SUBITEMid7').val($(this).parent().parent().find('[id*="REQ_CHANGES_PD_OR_QTY"]').attr('id'));

  var REQ_BOMID_REF       =  $(this).parent().parent().find('[id*="REQ_BOMID_REF"]').val();
  var REQ_ITEMID_REF      =  $(this).parent().parent().find('[id*="REQ_ITEMID_REF"]').val();
  var REQ_MAIN_ITEMID_REF =  $(this).parent().parent().find('[id*="REQ_MAIN_ITEMID_REF"]').val();
  var MAIN_PD_OR_QTY      =  $(this).parent().parent().find('[id*="MAIN_PD_OR_QTY"]').val();

  var REQ_ITEMID          =  REQ_MAIN_ITEMID_REF !=""?REQ_MAIN_ITEMID_REF:REQ_ITEMID_REF;


  var fieldid = $(this).attr('id');


  $("#SUBITEMpopup").show();
  $("#tbody_SUBITEM").html('');

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  $.ajax({
      url:'{{route("transaction",[$FormId,"getSUBITEMCodeNo"])}}',
      type:'POST',
      data:{'REQ_BOMID_REF':REQ_BOMID_REF,REQ_ITEMID:REQ_ITEMID,MAIN_PD_OR_QTY:MAIN_PD_OR_QTY,'fieldid':fieldid},
      success:function(data) {
        $("#tbody_SUBITEM").html(data);
        BindSUBITEM();
        showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_SUBITEM").html('');
      },
  });

});

$("#SUBITEM_closePopup").click(function(event){
  $("#SUBITEMpopup").hide();
});

function BindSUBITEM(){
  $(".clssSUBITEMid").click(function(){
    var fieldid   =   $(this).attr('id');

    var texdesc1   =   $("#txt"+fieldid+"").data("desc1");
    var texdesc2   =   $("#txt"+fieldid+"").data("desc2");
    var texdesc3   =   $("#txt"+fieldid+"").data("desc3");
    var texdesc4   =   $("#txt"+fieldid+"").data("desc4");
    var texdesc5   =   $("#txt"+fieldid+"").data("desc5");
    var texdesc6   =   $("#txt"+fieldid+"").data("desc6");

    var txt_id1= $('#hdn_SUBITEMid1').val();
    var txt_id2= $('#hdn_SUBITEMid2').val();
    var txt_id3= $('#hdn_SUBITEMid3').val();
    var txt_id4= $('#hdn_SUBITEMid4').val();
    var txt_id5= $('#hdn_SUBITEMid5').val();
    var txt_id6= $('#hdn_SUBITEMid6').val();
    var txt_id7= $('#hdn_SUBITEMid7').val();

    $('#'+txt_id1).val(texdesc1);
    $('#'+txt_id2).val(texdesc2);
    $('#'+txt_id3).val(texdesc3);
    $('#'+txt_id4).val(texdesc4);
    $('#'+txt_id5).val(texdesc5);
    $('#'+txt_id6).val(texdesc6);
    $('#'+txt_id7).val(texdesc6);

    $("#SUBITEMpopup").hide();
    $("#SUBITEMcodesearch").val(''); 
    $("#SUBITEMnamesearch").val(''); 
    SUBITEMCodeFunction();
    event.preventDefault();
  });
}

/*================================== MATERIAL ITEM FUNCTION ==================================*/

function get_materital_item(){

  var  item_array   = [];
  $('#example2').find('.participantRow').each(function(){
    var PROID_REF    = $(this).find('[id*="PROID_REF"]').val();
    var SOID_REF    = $(this).find('[id*="SOID_REF"]').val();
    var ITEMID_REF  = $(this).find('[id*="ITEMID_REF"]').val();
    var ITEMID_CODE = $(this).find('[id*="popupITEMID"]').val();
    var PD_OR_QTY   = $(this).find('[id*="PD_OR_QTY"]').val();
    var SQID_REF    = $(this).find('[id*="SQID_REF"]').val();
    var SEID_REF    = $(this).find('[id*="SEID_REF"]').val();

    item_array.push(PROID_REF+'_'+SOID_REF+'_'+ITEMID_REF+'_'+ITEMID_CODE+'_'+PD_OR_QTY+'_'+SQID_REF+'_'+SEID_REF);
  });

  $("#material_item").html('loading..');
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
      url:'{{route("transaction",[$FormId,"get_materital_item"])}}',
      type:'POST',
      data:{
        item_array:item_array
        },
      success:function(data) {
        $("#material_item").html(data);                
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#material_item").html('');                        
      },
  }); 


}

/*================================== USER DEFINE FUNCTION ==================================*/

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}

function change_production_order(id,value){

  var field_id  =   id.split("_")[3];
  //var QTY       =   parseFloat($("#BL_SOQTY_"+field_id).val()).toFixed(3);
  var PD_OR_QTY =   parseFloat(value).toFixed(3);
  //var BL_SOQTY  =   (QTY-PD_OR_QTY);

  var qty_val = $("#"+id).parent().parent().find('[id*="BL_SOQTY_"]').val();

  if(isNaN(value) || $.trim(value)==""){
    value = 0;
  }

  var mainitem_id  = $("#"+id+"").parent().parent().find('[id*="MAINTROWID"]').val();
  //console.log(" MAINTROWID=",mainitem_id);
 
  if(parseFloat(PD_OR_QTY) > parseFloat(qty_val)){

      //$("#FocusId").val('PD_OR_QTY_'+field_id);
      $("#"+id).val('0.000');
      $('#example4').find('.participantRow4').each(function(){
        var unirowid_val = $(this).children().find('[id*="main_item_rowid"]').val();
        
        if(mainitem_id==unirowid_val){
          var new_po_qty = 0;
          var REQ_BOM_QTY = $.trim( $(this).children().find('[id*="REQ_BOM_QTY_"]').val() );
          if(isNaN(REQ_BOM_QTY) || REQ_BOM_QTY==""){
            REQ_BOM_QTY=0;
          }
          var newval = $("#"+id).val();
          new_po_qty =parseFloat( parseFloat(newval) * parseFloat(REQ_BOM_QTY) ).toFixed(3);       
          $(this).children().find('[id*="REQ_INPUT_PD_OR_QTY_"]').val(new_po_qty);
          $(this).children().find('[id*="REQ_CHANGES_PD_OR_QTY_"]').val(new_po_qty);
          $(this).children().find('[id*="MAIN_PD_OR_QTY_"]').val(newval);
          console.log("m==",mainitem_id,unirowid_val,"value=",newval);
        }
        //---------

    });

    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Production order qty should not greater then Balance SO qty.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();    

  }
  else{

    // if(isNaN(BL_SOQTY) || $.trim(BL_SOQTY)==""){
    //   BL_SOQTY = 0;
    // }
    //$("#BL_SOQTY_"+field_id).val(parseFloat(BL_SOQTY).toFixed(3));   
    //var mainitem_id  = $("#"+id+"").data("mainitem");
  // console.log("sssmainitem_id="+mainitem_id);

    $('#example4').find('.participantRow4').each(function(){

      var unirowid_val = $(this).children().find('[id*="main_item_rowid"]').val();

      console.log("m2="+mainitem_id,unirowid_val,'m2 val====',value);

      if(mainitem_id==unirowid_val){
        //console.log("-- in m2="+mainitem_id,unirowid_val,'m2 val====',value);
        var new_po_qty = 0;
        var REQ_BOM_QTY = $.trim( $(this).children().find('[id*="REQ_BOM_QTY_"]').val() );
        if(isNaN(REQ_BOM_QTY) || REQ_BOM_QTY==""){
          REQ_BOM_QTY=0;

        }else{
          new_po_qty =parseFloat( parseFloat(value) * parseFloat(REQ_BOM_QTY) ).toFixed(3);            
        }

        $(this).children().find('[id*="REQ_INPUT_PD_OR_QTY_"]').val(new_po_qty);
        $(this).children().find('[id*="REQ_CHANGES_PD_OR_QTY_"]').val(new_po_qty);
        $(this).children().find('[id*="MAIN_PD_OR_QTY_"]').val(value);
        
      }

    });
    
  }

}


function change_production_qty(id,value){
  //console.log(id,value);
}



/*================================== ONLOAD FUNCTION ==================================*/
$(document).ready(function(e) {
  var Material = $("#Material").html(); 
  $('#hdnmaterial').val(Material);
  var lastdt = <?php echo json_encode($objlastdt[0]->PRO_DT); ?>;
  var today = new Date(); 
  var prodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;

  $('#PRO_DT').attr('min',lastdt);
  $('#PRO_DT').attr('max',prodate);
  $("[id*='QTY']").ForceNumericOnly();

  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  $('#PRO_DT').val(today);

});



/*================================== UDF FUNCTION ==================================*/

var udfdata = <?php echo json_encode($objUdfData); ?>;
var count2  = <?php echo json_encode($objCountUDF); ?>;

$('#Row_Count2').val(count2);
$('#example3').find('.participantRow3').each(function(){

  var txt_id4 = $(this).find('[id*="udfinputid"]').attr('id');
  var udfid   = $(this).find('[id*="UDF"]').val();

  $.each( udfdata, function( seukey, seuvalue ) {
    if(seuvalue.UDFJWOID == udfid){

      var txtvaltype2 = seuvalue.VALUETYPE;
      var strdyn2     = txt_id4.split('_');
      var lastele2    = strdyn2[strdyn2.length-1];
      var dynamicid2  = "udfvalue_"+lastele2;
          
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
      var txtoptscombo2 =   seuvalue.DESCRIPTIONS;
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

$('#Material').on('blur',"[id*='PD_OR_QTY']",function(){
    var qty2 = $.trim($(this).val());
    if(isNaN(qty2) || qty2=="" )
    {
      qty2 = 0;
    }  
    if(intRegex.test(qty2))
    {
      $(this).val((qty2 +'.000'));
    }

    event.preventDefault();
});

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

function rate_amount(fieldid){
  var arr     = fieldid.split('_');
  var textid  = arr.slice(-1)[0]

  var QTY   = parseFloat($.trim($("#PD_OR_QTY_"+textid).val()));
  var RATE  = parseFloat($.trim($("#RATE_"+textid).val()));
  var AMOUNT= (QTY*RATE).toFixed(2);

  $("#AMOUNT_"+textid).val(AMOUNT);
}
</script>
@endpush
