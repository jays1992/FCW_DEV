@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
        <div class="row">
            <div class="col-lg-2">
            <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Goods Receipt Note <br/>against RGP</a>
            </div>

            <div class="col-lg-10 topnav-pd">
                    <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                    <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                    <button class="btn topnavbt" id="btnSaveSE" ><i class="fa fa-floppy-o"></i> Save</button>
                    <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
                    <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                    <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                    <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                    <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                    <button style="display:none" class="btn topnavbt buttonload_approve" > <i class="fa fa-refresh fa-spin"></i> {{Session::get('approve')}}</button>
                    <button class="btn topnavbt" id="btnApprove" {{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}} ><i class="fa fa-thumbs-o-up"></i> Approved</button>
                    <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                    <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
            </div>
        </div>
</div>

<form id="frm_trn_edit"  method="POST">   
    @csrf
    {{isset($objResponse->GRNID[0]) ? method_field('PUT') : '' }}
    <div class="container-fluid filter">
	<div class="inner-form">

		<div class="row">

			<div class="col-lg-1 pl"><p>RGP No</p></div>
			<div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="GRN_NO" id="GRN_NO" value="{{isset($objResponse->GRN_NO) && $objResponse->GRN_NO !=''?$objResponse->GRN_NO:''}}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
      </div>
			
			<div class="col-lg-1 pl"><p>RGP Date</p></div>
			<div class="col-lg-2 pl">
			    <input {{$ActionStatus}} type="date" name="GRN_DT" id="GRN_DT" onchange="checkPeriodClosing('{{$FormId}}',this.value,1)" value="{{isset($objResponse->GRN_DT) && $objResponse->GRN_DT !=''?$objResponse->GRN_DT:''}}" class="form-control mandatory" >
      </div>

      <div class="col-lg-1 pl"><p>Type</p></div>
			<div class="col-lg-2 pl">
        <select {{$ActionStatus}} name="TYPE" id="TYPE" class="form-control"  autocomplete="off" onchange="getType(this.value)" >
          <option {{isset($objResponse->TYPE) && $objResponse->TYPE ==='Vendor'?'selected="selected"':''}} value="Vendor">Vendor</option>
          <option {{isset($objResponse->TYPE) && $objResponse->TYPE ==='Customer'?'selected="selected"':''}} value="Customer">Customer</option>
          <option {{isset($objResponse->TYPE) && $objResponse->TYPE ==='Employee'?'selected="selected"':''}} value="Employee">Employee</option>
        </select>
			</div>

      <div class="col-lg-1 pl"><p id='fieldtype' >{{$objResponse->TYPE}}</p></div>
			<div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="VID_REF_popup" id="txtdep_popup" value="{{isset($objVendorName->VCODE) && $objVendorName->VCODE !=''?$objVendorName->VCODE.' - '.$objVendorName->NAME:''}}" class="form-control mandatory"  autocomplete="off" readonly/>
          <input type="hidden" name="VID_REF" id="VID_REF" value="{{isset($objResponse->VID_REF) && $objResponse->VID_REF !=''?$objResponse->VID_REF:''}}" class="form-control" autocomplete="off" />
          <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />
      </div>

		</div>

    <div class="row">
      
      <div class="col-lg-1 pl"><p>Remarks</p></div>
			<div class="col-lg-5 pl">
				<input {{$ActionStatus}} type="text" name="REMARKS" id="REMARKS" value="{{isset($objResponse->REMARKS) && $objResponse->REMARKS !=''?$objResponse->REMARKS:''}}" class="form-control" autocomplete="off"  maxlength="200"  >
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
					<div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;" >
						<table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
							<thead id="thead1"  style="position: sticky;top: 0">
                <tr>
                  <th>RGP No</th>
									<th>Item Code <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
									<th>Item Description</th>
                  <th>Bill / challan Qty</th>
									<th>Main UoM (MU)</th>
                  <th>Nature</th>
                  <th>Received Qty (MU)</th>
                  <th>Short Qty</th>
                  <th>Store</th>
                  <th>Remarks</th>
									<th>Action</th>
								  </tr>
							</thead>
							<tbody>
              @if(!empty($objMAT))
              @foreach($objMAT as $key => $row)
							<tr  class="participantRow">
                  <td><input {{$ActionStatus}} type="text" name="txtRGP_popup_{{$key}}" id="txtRGP_popup_{{$key}}" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                  <td hidden><input type="hidden" name="RGP_NO_{{$key}}" id="RGP_NO_{{$key}}" value="{{ $row->RGPID_REF }}"  class="form-control" autocomplete="off" /></td>

                  <td><input {{$ActionStatus}} type="text" name={{"popupITEMID_".$key}} id={{"popupITEMID_".$key}} class="form-control" value="{{ $row->ICODE }}"  autocomplete="off"  readonly/></td>
                  <td hidden><input type="hidden" name={{"ITEMID_REF_".$key}} id={{"ITEMID_REF_".$key}} class="form-control"  value="{{ $row->ITEMID_REF }}" autocomplete="off" /></td>
                  <td><input {{$ActionStatus}} type="text" name={{"ItemName_".$key}} id={{"ItemName_".$key}} class="form-control" value="{{ $row->ITEM_NAME }}"  autocomplete="off"   readonly/></td>
                  
                  <td><input {{$ActionStatus}} type="text" name={{"SE_QTY_".$key}} id={{"SE_QTY_".$key}} class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13" value="{{ $row->BILL_CH_QTY}}" autocomplete="off"  /></td>
                  
                  <td><input {{$ActionStatus}} type="text" name={{"popupMUOM_".$key}} id={{"popupMUOM_".$key}} class="form-control"  value="{{ $row->MAIN_UOM_CODE }}"  autocomplete="off"  readonly/></td>
                  <td hidden><input type="hidden" name={{"MAIN_UOMID_REF_".$key}} id={{"MAIN_UOMID_REF_".$key}}  value="{{ $row->MAIN_UOMID_REF }}" class="form-control"   autocomplete="off" /></td>
                  
                  <td>
                    <select {{$ActionStatus}} name="NATURE_{{$key}}" id="NATURE_{{$key}}" class="form-control"  autocomplete="off" >
                      <option {{isset($row->NATURE) && $row->NATURE ==='Fresh'?'selected="selected"':''}} value="Fresh">Fresh</option>
                      <option {{isset($row->NATURE) && $row->NATURE ==='Defective'?'selected="selected"':''}} value="Defective">Defective</option>
                    </select>
                  </td>

                  <td><input {{$ActionStatus}} type="text" name="RECEIVED_QTY_MU_{{$key}}" id="RECEIVED_QTY_MU_{{$key}}" value="{{ $row->RECEIVED_QTY_MU }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off"   /></td>
                  <td><input {{$ActionStatus}} type="text" name="SHORT_QTY_{{$key}}" id="SHORT_QTY_{{$key}}" value="{{ $row->SHORT_QTY }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off"  readonly  /></td>
                          


                  <td align="center"><a {{$ActionStatus}} class="btn checkstore"  id="{{$key}}" ><i class="fa fa-clone"></i></a></td>
                  <td hidden ><input type="hidden" name="TotalHiddenQty_{{$key}}" id="TotalHiddenQty_{{$key}}" value="{{ $row->RECEIVED_QTY_MU}}" ></td>
                  <td hidden ><input type="hidden" name="HiddenRowId_{{$key}}" id="HiddenRowId_{{$key}}" value="{{ $row->BATCHQTY_REF}}" ></td>
                  
              
                  <td hidden><input type="hidden" name={{"SO_FQTY_".$key}} id={{"SO_FQTY_".$key}} class="form-control three-digits" maxlength="13"  autocomplete="off"  readonly/></td>
                  <td hidden><input type="text" name={{"Itemspec_".$key}} id={{"Itemspec_".$key}} class="form-control"  autocomplete="off"    /></td>
                  <!--
                  <td><input  class="form-control w-100" type="date" name={{"EDD_".$key}} id={{"EDD_".$key}}  ></td>
                  -->
                  <td><input {{$ActionStatus}} type="text" name="REMARKS_{{$key}}" id="REMARKS_{{$key}}" value="{{ $row->REMARKS }}" class="form-control w-100" autocomplete="off" ></td>
                  <td align="center" >
                    <button {{$ActionStatus}} class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                    <button {{$ActionStatus}} class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button" ><i class="fa fa-trash" ></i></button>
                  </td>
								  </tr>
								<tr></tr>
                @endforeach 
                @endif
							</tbody>
					  </table>
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
              @foreach($objUDF as $uindex=>$uRow)
                <tr  class="participantRow3">
                    <td><input {{$ActionStatus}} type="text" name={{"popupSEID_".$uindex}} id={{"popupSEID_".$uindex}} class="form-control" value="{{$uRow->UDF}}" autocomplete="off"  readonly/></td>
                    <td hidden><input type="hidden" name={{"UDF_".$uindex}} id={{"UDF_".$uindex}} class="form-control" value="{{$uRow->UDF}}" autocomplete="off"   /></td>
                    <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->UDF}}" class="form-control"   autocomplete="off" /></td>
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
          <table id="StoreTable" class="display nowrap table  table-striped table-bordered" style="width: 100%;" >
          </table>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

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
        </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- Vendor Dropdown -->
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
        <td class="ROW2"><input type="text" id="vendorcodesearch" autocomplete="off" class="form-control" onkeyup="VendorCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="vendornamesearch" autocomplete="off" class="form-control" onkeyup="VendorNameFunction()"></td>
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

<div id="RGPpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='RGP_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>RGP NO</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="RGPTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_sqid"/>
            <input type="hidden" name="fieldid2" id="hdn_sqid2"/>
            <input type="hidden" name="fieldid2" id="hdn_sqid3"/>
            </td>
          </tr>

    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">RGP No</th>
      <th class="ROW3">Date</th>
    </tr>
    </thead>
    <tbody>
    
    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="RGPcodesearch" class="form-control" autocomplete="off" onkeyup="RGPCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="RGPnamesearch" class="form-control" autocomplete="off" onkeyup="RGPNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="RGPTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_RGP">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="EMPLOYEE_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='EMPLOYEE_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Employee</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="EmployeeTable" class="display nowrap table  table-striped table-bordered" >
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
        <td class="ROW2"><input type="text" id="Employeecodesearch" class="form-control" autocomplete="off" onkeyup="EmployeeCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="Employeenamesearch" class="form-control" autocomplete="off" onkeyup="EmployeeNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="EmployeeTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
          
        </thead>
        <tbody >     
        @foreach ($Employee as $key=>$val)
        <tr >
          <td class="ROW1"> <input type="checkbox" name="SELECT_EMPLOYEE[]" id="spidcode_{{ $key }}" class="clssemployee" value="{{ $val-> EMPID }}" ></td>  
          <td class="ROW2">{{ $val-> EMPCODE }} <input type="hidden" id="txtspidcode_{{ $key }}" data-desc="{{ $val-> EMPCODE }} - {{ $val-> FNAME }}  {{ $val-> LNAME }}"  value="{{ $val-> EMPID }}"/></td>
          <td class="ROW3">{{ $val-> FNAME }} {{ $val-> MNAME }} {{ $val-> LNAME }}</td>
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

<div id="customer_popus" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='customer_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Customer</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="GlCodeTable" class="display nowrap table  table-striped table-bordered">
    <thead>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Description</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="ROW1"><span class="check_th">&#10004;</span></td>
        <td class="ROW2"><input type="text" id="customercodesearch" class="form-control" onkeyup="CustomerCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="customernamesearch" class="form-control" onkeyup="CustomerNameFunction()"></td>
    </tr>
    </tbody>
    </table>
      <table id="GlCodeTable2" class="display nowrap table  table-striped table-bordered">
        <thead id="thead2">
        </thead>
        <tbody id="tbody_subglacct">
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%" >
    <thead>
      <tr id="none-select" class="searchalldata" hidden>
            
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
            <th style="width:10%;" >Item Code</th>
            <th style="width:10%;" >Name</th>
            <th style="width:8%;" >Main UOM</th>
            <th style="width:8%;" >Main QTY</th>
            <th style="width:8%;" >Item Group</th>
            <th style="width:8%;" >Item Category</th>
            <th style="width:8%;">Business Unit</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
            <th style="width:8%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
            <th style="width:8%;">Status</th>
      </tr>
    </thead>
    <tbody>
    <tr>
    <td style="width:8%;text-align:center;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
    <td style="width:10%;">
    <input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction()">
    </td>
    <td  style="width:10%;">
    <input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemNameFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="ItemUOMFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemQTYsearch" class="form-control" autocomplete="off" onkeyup="ItemQTYFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="ItemGroupFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="ItemCategoryFunction()">
    </td>

    <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction()"></td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="ItemAPNFunction()"></td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="ItemCPNFunction()"></td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="ItemOEMPNFunction()"></td>

    <td style="width:8%;">
    <input type="text" id="ItemStatussearch" class="form-control" autocomplete="off" onkeyup="ItemStatusFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_ItemID">     
          
          
        </tbody>
      </table>

      <div class="loader" id="item_loader" style="display:none;"></div>
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


  //Vendor  Starts
//------------------------

// START VENDOR CODE FUNCTION
let vdtid = "#VendorCodeTable2";
let vdtid2 = "#VendorCodeTable";
let vdheaders = document.querySelectorAll(vdtid2 + " th");

      
vdheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(vdtid, ".clsvendorid", "td:nth-child(" + (i + 1) + ")");
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
      bindVendEvents();
    },
    error:function(data){
    console.log("Error: Something went wrong.");
    $("#tbody_vendor").html('');                        
    },
  });
}

$("#txtdep_popup").click(function(){
  var TYPE  = $("#TYPE").val();
    
  if(TYPE ==='Vendor'){
    var CODE = ''; 
    var NAME = ''; 
    loadVendor(CODE,NAME);  
    $("#vendoridpopup").show();
  }
  else if(TYPE ==='Customer'){
    var CODE = ''; 
    var NAME = ''; 
    loadCustomer(CODE,NAME);
    $("#customer_popus").show();
  }
  else if(TYPE ==='Employee'){
    showSelectedCheck($("#EMPLOYEE").val(),"SELECT_EMPLOYEE");
    $("#EMPLOYEE_popup").show();
  }
  event.preventDefault();
});

$("#vendor_close_popup").on("click",function(){ 
  $("#vendoridpopup").hide();
  event.preventDefault();
});

function bindVendEvents(){
        $('.clsvendorid').click(function(){
    
            var id = $(this).attr('id');
            var txtval =    $("#txt"+id+"").val();
            var texdesc =   $("#txt"+id+"").data("desc");

            var oldVID_REF    =   $("#VID_REF").val();
            var MaterialClone =  $('#hdnmaterial').val();
    
    $('#txtdep_popup').val(texdesc);
    $('#VID_REF').val(txtval);

    if (txtval != oldVID_REF){ 
      $('#Material').html(MaterialClone);
      var count11 = <?php echo json_encode($objCount1); ?>;
      $('#Row_Count1').val(count11);
      $('#example2').find('.participantRow').each(function(){
        $(this).find('input:text').val('');
        var rowcount = $('#Row_Count1').val();
        if(rowcount > 1)
        {
          $(this).closest('.participantRow').remove();
          rowcount = parseInt(rowcount) - 1;
          $('#Row_Count1').val(rowcount);
        }
      });
    }

            $("#vendoridpopup").hide();
            $("#vendor_codesearch").val(''); 
            $("#vendor_namesearch").val(''); 
            
              event.preventDefault();
        });
  }
//Vendor  Ends
//------------------------

      
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

var RGP_NO = $(this).parent().parent().find('[id*="RGP_NO"]').val();

$("#item_loader").show();
$("#tbody_ItemID").html('');
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{route("transaction",[$FormId,"getItemDetails"])}}',
      type:'POST',
      data:{'status':'A',RGP_NO:RGP_NO},
      success:function(data) {
        $("#item_loader").hide();
        $("#tbody_ItemID").html(data);    
        bindItemEvents();   
        $('.js-selectall').prop('disabled', true);                     
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#item_loader").hide();
        $("#tbody_ItemID").html('');                        
      },
  }); 
  

  $("#ITEMIDpopup").show();
  var id = $(this).attr('id');
  var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
  var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
  var id4 = $(this).parent().parent().find('[id*="Itemspec"]').attr('id');
  var id5 = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
  var id6 = $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
  var id7 = $(this).parent().parent().find('[id*="SE_QTY"]').attr('id');
  var id11 = $(this).parent().parent().find('[id*="SO_FQTY"]').attr('id');

  var id12 = $(this).parent().parent().find('[id*="TotalHiddenQty"]').attr('id');
  var id13 = $(this).parent().parent().find('[id*="HiddenRowId"]').attr('id');

  $('#hdn_ItemID').val(id);
  $('#hdn_ItemID2').val(id2);
  $('#hdn_ItemID3').val(id3);
  $('#hdn_ItemID4').val(id4);
  $('#hdn_ItemID5').val(id5);
  $('#hdn_ItemID6').val(id6);
  $('#hdn_ItemID7').val(id7);
  $('#hdn_ItemID11').val(id11);

  $('#hdn_ItemID12').val(id12);
  $('#hdn_ItemID13').val(id13);
 
  event.preventDefault();
});

$("#ITEMID_closePopup").click(function(event){
  $("#ITEMIDpopup").hide();
});

function bindItemEvents(){

$('#ItemIDTable2').off(); 

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

  txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);

  var desc6 =  $("#txt"+fieldid+"").data("desc6");

  
  if(intRegex.test(txtauomqty)){
      txtauomqty = (txtauomqty +'.000');
  }

  if(intRegex.test(txtmuomqty)){
    txtmuomqty = (txtmuomqty +'.000');
  }

  
  
 if($(this).is(":checked") == true) {

  $('#example2').find('.participantRow').each(function(){
    var RGPNO = $(this).find('[id*="RGP_NO"]').val();
    var itemid = $(this).find('[id*="ITEMID_REF"]').val();
     
     var exist_val=RGPNO+'-'+itemid;

     if(txtval){
          if(desc6 == exist_val){
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

                $('#hdn_ItemID12').val('');
                $('#hdn_ItemID13').val('');
               
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
    $clone.find('[id*="popupITEMID"]').val(texdesc);
    $clone.find('[id*="ITEMID_REF"]').val(txtval);
    $clone.find('[id*="ItemName"]').val(txtname);
    $clone.find('[id*="Itemspec"]').val(txtspec);
    $clone.find('[id*="popupMUOM"]').val(txtmuom);
    $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
    $clone.find('[id*="SE_QTY"]').val(txtmuomqty);
    //$clone.find('[id*="SE_QTY"]').val('');
    
    $clone.find('[id*="TotalHiddenQty"]').val('');
    $clone.find('[id*="HiddenRowId"]').val('');

    $clone.find('[id*="REMARKS"]').val('');
    
    $tr.closest('table').append($clone);   
    var rowCount = $('#Row_Count1').val();
      rowCount = parseInt(rowCount)+1;
      $('#Row_Count1').val(rowCount);
      
      $("#ITEMIDpopup").hide();
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

    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $('#'+txt_id3).val(txtname);
    $('#'+txt_id4).val(txtspec);
    $('#'+txt_id5).val(txtmuom);
    $('#'+txt_id6).val(txtmuomid);
    $('#'+txt_id7).val(txtmuomqty);
    //$('#'+txt_id7).val('');

    $('#hdn_ItemID').val('');
    $('#hdn_ItemID2').val('');
    $('#hdn_ItemID3').val('');
    $('#hdn_ItemID4').val('');
    $('#hdn_ItemID5').val('');
    $('#hdn_ItemID6').val('');
    $('#hdn_ItemID7').val('');
    $('#hdn_ItemID11').val('');
    
    $('#hdn_ItemID12').val('');
    $('#hdn_ItemID13').val('');

  }

                
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
 
  event.preventDefault();
});
}




$(document).ready(function(e) {
var Material = $("#Material").html(); 
$('#hdnmaterial').val(Material);
var lastdt = <?php echo json_encode(isset($objResponse->GRN_DT)?$objResponse->GRN_DT:''); ?>;
var today = new Date(); 
var sodate = <?php echo json_encode(isset($objResponse->GRN_DT)?$objResponse->GRN_DT:''); ?>;
$('#GRN_DT').attr('min',lastdt);
$('#GRN_DT').attr('max',sodate);
//$('[id*="EDD"]').attr('min',sodate);



var seudf = <?php echo json_encode($objUdfData); ?>;
var count2 = <?php echo json_encode($objCountUDF); ?>;

$('#example3').find('.participantRow3').each(function(){
      var txt_id4 = $(this).find('[id*="udfinputid"]').attr('id');
      var udfid = $(this).find('[id*="UDF"]').val();

      $.each( seudf, function( seukey, seuvalue ) {
        if(seuvalue.GRNRGPID == udfid)
        {

          var txtvaltype2 =   seuvalue.VALUETYPE;
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

var count1 = <?php echo json_encode($objCount1); ?>;
var count2 = <?php echo json_encode($objCount2); ?>;
$('#Row_Count1').val(count1);
$('#Row_Count2').val(count2);
var objSE = <?php echo json_encode($objMAT); ?>;
var item = <?php echo json_encode($objItems); ?>;
var uom = <?php echo json_encode($objUOM); ?>;
var uomconv = <?php echo json_encode($objItemUOMConv); ?>;

$.each(objSE, function(sekey,sevalue) {

    $('#txtRGP_popup_'+sekey).val(sevalue.RGP_NO);
    /*
    $.each(item, function(itkey,itvalue) {

        if(sevalue.ITEMID_REF == itvalue.ITEMID){
            $('#popupITEMID_'+sekey).val(itvalue.ICODE);
            $('#ItemName_'+sekey).val(itvalue.NAME);
            $.each(uom, function(uomkey,uomvalue) {
              if(itvalue.MAIN_UOMID_REF == uomvalue.UOMID)
              {
                $('#popupMUOM_'+sekey).val(uomvalue.UOMCODE+'-'+uomvalue.DESCRIPTIONS);
                $('#MAIN_UOMID_REF_'+sekey).val(uomvalue.UOMID);
              }
            });

            $.each(uom, function(uomkey,uomvalue) {
              if(sevalue.ALTUOMID_REF == uomvalue.UOMID)
              {
                $('#popupAUOM_'+sekey).val(uomvalue.UOMCODE+'-'+uomvalue.DESCRIPTIONS);
              }
              if(sevalue.PACKUOMID_REF == uomvalue.UOMID)
              {
                $('#PACKUOM_'+sekey).val(uomvalue.UOMCODE+'-'+uomvalue.DESCRIPTIONS);
              }
            });
        }


    });*/

    


});


var soudf = <?php echo json_encode($objUDF); ?>;
var udfforse = <?php echo json_encode($objUdfData2); ?>;
$.each( soudf, function( soukey, souvalue ) {

    $.each( udfforse, function( usokey, usovalue ) { 
        if(souvalue.UDF == usovalue.GRNRGPID)
        {
            $('#popupSEID_'+soukey).val(usovalue.LABEL);
        }
    
        if(souvalue.UDF == usovalue.GRNRGPID){        
                var txtvaltype2 =   usovalue.VALUETYPE;
                var txt_id41 = $('#udfinputid_'+soukey).attr('id');
                var strdyn2 = txt_id41.split('_');
                var lastele2 =   strdyn2[strdyn2.length-1];
                var dynamicid2 = "udfvalue_"+lastele2;
                
                var chkvaltype2 =  txtvaltype2.toLowerCase();
                var strinp2 = '';

                if(chkvaltype2=='date'){

                strinp2 = '<input {{$ActionStatus}} type="date" placeholder="dd/mm/yyyy" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';       

                }
                else if(chkvaltype2=='time'){
                strinp2= '<input {{$ActionStatus}} type="time" placeholder="h:i" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';

                }
                else if(chkvaltype2=='numeric'){
                strinp2 = '<input {{$ActionStatus}} type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"   > ';

                }
                else if(chkvaltype2=='text'){

                strinp2 = '<input  {{$ActionStatus}} type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';
                
                }
                else if(chkvaltype2=='boolean'){
                    if(souvalue.SOUVALUE == "1")
                    {
                    strinp2 = '<input {{$ActionStatus}} type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" checked> ';
                    }
                    else{
                    strinp2 = '<input {{$ActionStatus}} type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" > ';
                    }
                }
                else if(chkvaltype2=='combobox'){

                var txtoptscombo2 =   usovalue.DESCRIPTIONS;
                var strarray2 = txtoptscombo2.split(',');
                var opts2 = '';

                for (var i = 0; i < strarray2.length; i++) {
                    opts2 = opts2 + '<option value="'+strarray2[i]+'">'+strarray2[i]+'</option> ';
                }

                strinp2 = '<select {{$ActionStatus}} name="'+dynamicid2+ '" id="'+dynamicid2+'" class="form-control" required>'+opts2+'</select>' ;
                
                }
                
                
                $('#'+txt_id41).html('');  
                $('#'+txt_id41).html(strinp2);   
                $('#'+dynamicid2).val(souvalue.COMMENT);
                $('#UDFismandatory_'+soukey).val(usovalue.ISMANDATORY);
            
        }
    });
  
});




$('#btnAdd').on('click', function() {
  var viewURL = '{{route("transaction",[$FormId,"add"])}}';
              window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
});

$('#GRN_DT').change(function() {
    var mindate  = $(this).val();
    //$('[id*="EDD"]').attr('min',mindate);
});   

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
  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;

  // var h         = new Date($("#EDA").val()); 
  // var headDate  = h.getFullYear() + "-" + ("0" + (h.getMonth() + 1)).slice(-2) + "-" + ('0' + h.getDate()).slice(-2) ;
  
  // $clone.find('[id*="EDD"]').val(headDate);

  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count1').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count1').val(rowCount1);
  $clone.find('.remove').removeAttr('disabled'); 
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
    window.location.reload();
}

window.fnUndoNo = function (){
   
}

function growTextarea (i,elem) {
    var elem = $(elem);
    var resizeTextarea = function( elem ) {
        var scrollLeft = window.pageXOffset || (document.documentElement || document.body.parentNode || document.body).scrollLeft;
        var scrollTop  = window.pageYOffset || (document.documentElement || document.body.parentNode || document.body).scrollTop;  
        elem.css('height', 'auto').css('height', elem.prop('scrollHeight') );
        window.scrollTo(scrollLeft, scrollTop);
    };

    elem.on('input', function() {
        resizeTextarea( $(this) );
    });
    resizeTextarea( $(elem) );
    }
    $('.growTextarea').each(growTextarea);
});
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {
    var d = new Date(); 
    var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;

    // $("#EDA").on("change", function( event ) {
    //   $('[id*="EDD"]').val($(this).val());
    // });
   
    $('#frm_trn_edit1').bootstrapValidator({
       
        fields: {
            txtlabel: {
                validators: {
                    notEmpty: {
                        message: 'The MRS No is required'
                    }
                }
            },            
        },
        submitHandler: function(validator, form, submitButton) {
            alert( "Handler for .submit() called." );
             event.preventDefault();
             $("#frm_trn_edit").submit();
        }
    });
});


$( "#btnSaveSE" ).click(function() {
    var formReqData = $("#frm_trn_edit");
    if(formReqData.valid()){
      validateForm('fnSaveData');
    }
});

$( "#btnApprove" ).click(function() {
    var formReqData = $("#frm_trn_edit");
    if(formReqData.valid()){
      validateForm('fnApproveData');
    }
});


$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

});

window.fnSaveData = function (){
event.preventDefault();
      var trnFormReq = $("#frm_trn_edit");
      var formData = trnFormReq.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $("#btnSaveSE").hide(); 
  $(".buttonload").show(); 
  $("#btnApprove").prop("disabled", true);
  $.ajax({
      url:'{{ route("transactionmodify",[$FormId,"update"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
        $(".buttonload").hide(); 
        $("#btnSaveSE").show();   
        $("#btnApprove").prop("disabled", false);
          if(data.errors) {
              $(".text-danger").hide();
              if(data.errors.LABEL){
                  showError('ERROR_LABEL',data.errors.LABEL);
                          $("#YesBtn").hide();
                          $("#NoBtn").hide();
                          $("#OkBtn1").show();
                          $("#AlertMessage").text('Please enter correct value in Label.');
                          $("#alert").modal('show');
                          $("#OkBtn1").focus();
              }
            if(data.country=='norecord') {
                ("#YesBtn").hide();
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
          $(".buttonload").hide(); 
          $("#btnSaveSE").show();   
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

window.fnApproveData = function (){
event.preventDefault();
      var trnFormReq = $("#frm_trn_edit");
      var formData = trnFormReq.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $("#btnApprove").hide(); 
  $(".buttonload_approve").show();  
  $("#btnSaveSE").prop("disabled", true);
  $.ajax({
      url:'{{ route("transactionmodify",[$FormId,"Approve"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
        $("#btnApprove").show();  
        $(".buttonload_approve").hide();  
        $("#btnSaveSE").prop("disabled", false);
          if(data.errors) {
              $(".text-danger").hide();
              if(data.errors.LABEL){
                  showError('ERROR_LABEL',data.errors.LABEL);
                          $("#YesBtn").hide();
                          $("#NoBtn").hide();
                          $("#OkBtn1").show();
                          $("#AlertMessage").text('Please enter correct value in Label.');
                          $("#alert").modal('show');
                          $("#OkBtn1").focus();
              }
            if(data.country=='norecord') {
                ("#YesBtn").hide();
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
          $("#btnApprove").show();  
          $(".buttonload_approve").hide();  
          $("#btnSaveSE").prop("disabled", false);
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
    var FocusId=$("#FocusId").val();
    $("#"+FocusId).focus();
    $("#closePopup").click();
}

function highlighFocusBtn(pclass){
    $(".activeYes").hide();
    $(".activeNo").hide();
    
    $("."+pclass+"").show();
}

function validateForm(actionType){
 
  $("#FocusId").val('');
 var GRN_NO         =   $.trim($("#GRN_NO").val());
 var GRN_DT         =   $.trim($("#GRN_DT").val());
 //var STID_REF       =   $.trim($("#STID_REF").val());
 var VID_REF        =   $.trim($("#VID_REF").val());
 //var PRIORITYID_REF =   $.trim($("#PRIORITYID_REF").val());
 //var PRIORITYID_REF =   $.trim($("#PRIORITYID_REF").val());
 //var EDA            =   $.trim($("#EDA").val());
 var TYPE           =   $.trim($("#TYPE").val());

 if(GRN_NO ===""){
     $("#FocusId").val($("#GRN_NO"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('RGP No is required.');
     $("#alert").modal('show')
     $("#OkBtn1").focus();
     return false;
 }
 else if(GRN_DT ===""){
     $("#FocusId").val($("#GRN_DT"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select RGP Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }/*  
 else if(STID_REF ===""){
     $("#FocusId").val($("#STID_REF_popup"));
     $("#STID_REF").val(''); 
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select store.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }*/ 
 else if(TYPE ===""){
    $("#FocusId").val('TYPE');
    $("#TYPE").val('');  
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select type.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
 } 
 else if(VID_REF ===""){
     $("#FocusId").val('VID_REF_popup');
     $("#VID_REF").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select '+TYPE);
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
//  else if(PRIORITYID_REF ===""){
//      $("#FocusId").val($("#PRIORITYID_REF_popup"));
//      $("#PRIORITYID_REF").val('');  
//      $("#ProceedBtn").focus();
//      $("#YesBtn").hide();
//      $("#NoBtn").hide();
//      $("#OkBtn1").show();
//      $("#AlertMessage").text('Please select priority.');
//      $("#alert").modal('show');
//      $("#OkBtn1").focus();
//      return false;
//  } 
//  else if(EDA ==""){
//     $("#FocusId").val($("#EDA")); 
//      $("#ProceedBtn").focus();
//      $("#YesBtn").hide();
//      $("#NoBtn").hide();
//      $("#OkBtn1").show();
//      $("#AlertMessage").text('Please select EDA.');
//      $("#alert").modal('show');
//      $("#OkBtn1").focus();
//      return false;
//  } 
 else{
    event.preventDefault();
    var allblank = [];
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
    var allblank13 = [];
        
    $('#example2').find('.participantRow').each(function(){

      if($.trim($(this).find("[id*=RGP_NO]").val())!=""){
        allblank1.push('true');
      }
      else{
        allblank1.push('false');
      }

      if($.trim($(this).find("[id*=ITEMID_REF]").val())!="")
      {
          allblank.push('true');
              if($.trim($(this).find("[id*=MAIN_UOMID_REF]").val())!=""){
                  allblank2.push('true');

                  if($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) != "" && parseFloat($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val())) <= parseFloat($.trim($(this).find('[id*="SE_QTY"]').val())) ){
                      allblank13.push('true');
                    }
                    else{
                      allblank13.push('false');
                    }  


                    if($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) != "" && $.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) > 0.000 )
                    {
                      allblank3.push('true');
                    }
                    else
                    {
                      allblank3.push('false');
                    }  


                    if($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) != "" && $.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) == $.trim($(this).find('[id*="TotalHiddenQty"]').val()) ){
                      allblank7.push('true');
                    }
                    else{
                      allblank7.push('false');
                    }  


              }
              else{
                  allblank2.push('false');
              }
              
              // if($.trim($(this).find("[id*=EDD]").val())!=""){
              //   allblank8.push('true');
              // }
              // else
              // {
              //   allblank8.push('false');
              // }
              
              // if(LessDateValidateWithToday( $.trim($(this).find("[id*=EDD]").val()) )==true ){
              //     allblank9.push('true');
              // }
              // else{
              //   allblank9.push('false');
              // }


      }
      else
      {
          allblank.push('false');
      }
  });

  $('#example3').find('.participantRow3').each(function(){
        if($.trim($(this).find("[id*=UDF]").val())!="")
          {
              allblank4.push('true');
                  if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
                        if($.trim($(this).find('[id*="udfvalue"]').val()) != "")
                        {
                          allblank5.push('true');
                        }
                        else
                        {
                          allblank5.push('false');
                        }
                  }  
          }                
  });

  if(jQuery.inArray("false", allblank1) !== -1){
    $("#alert").modal('show');
    $("#AlertMessage").text('Please select RGP No in material tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank) !== -1){
    $("#alert").modal('show');
    $("#AlertMessage").text('Please select item in material tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank2) !== -1){
    $("#alert").modal('show');
    $("#AlertMessage").text('Main UOM is missing in in material tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank3) !== -1){
    $("#alert").modal('show');
    $("#AlertMessage").text('Quantity cannot be zero or blank in material tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank7) !== -1){
    $("#alert").modal('show');
    $("#AlertMessage").text('Item quantity cannot be equal of selected store quantity in material tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank13) !== -1){
    $("#alert").modal('show');
    $("#AlertMessage").text('Received quantity should not greater then Bill quantity in material tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    }
    // else if(jQuery.inArray("false", allblank8) !== -1){
    // $("#alert").modal('show');
    // $("#AlertMessage").text('EDA date  cannot be blank in material tab.');
    // $("#YesBtn").hide(); 
    // $("#NoBtn").hide();  
    // $("#OkBtn1").show();
    // $("#OkBtn1").focus();
    // highlighFocusBtn('activeOk');
    // }
    // else if(jQuery.inArray("false", allblank9) !== -1){
    // $("#alert").modal('show');
    // $("#AlertMessage").text('EDA date should not less then current date in material tab.');
    // $("#YesBtn").hide(); 
    // $("#NoBtn").hide();  
    // $("#OkBtn1").show();
    // $("#OkBtn1").focus();
    // highlighFocusBtn('activeOk');
    // }
    else if(jQuery.inArray("false", allblank5) !== -1){
    $("#alert").modal('show');
    $("#AlertMessage").text('Please enter  Value / Comment in UDF Tab.');
    $("#YesBtn").hide(); 
    $("#NoBtn").hide();  
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    }
    else if(checkPeriodClosing('{{$FormId}}',$("#GRN_DT").val(),0) ==0){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text(period_closing_msg);
      $("#alert").modal('show');
      $("#OkBtn1").focus();
    }
    else{
          $("#alert").modal('show');
          $("#AlertMessage").text('Do you want to save to record.');
          $("#YesBtn").data("funcname",actionType);
          $("#YesBtn").focus();
          $("#OkBtn").hide();
          highlighFocusBtn('activeYes');
    }
  }
}

function LessDateValidateWithToday(value){

if(value !=""){
    var today = new Date(); 
    var d = new Date(value);
    today.setHours(0, 0, 0, 0) ;
    d.setHours(0, 0, 0, 0) ;

    if(d < today){
        return false;
    }
    else {
      return true;
    }
}
else{
  return true;
}
}

$("#example2").on('click', '[class*="checkstore"]', function() {
  var ROW_ID      =   $(this).attr('id');
  var ITEMID_REF  =   $("#ITEMID_REF_"+ROW_ID).val();
  var RGP_NO      =   $("#RGP_NO_"+ROW_ID).val();

  if(ITEMID_REF ===""){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select item code.');
      $("#alert").modal('show')
      $("#OkBtn1").focus();
      return false;
  }
  else{
      getStoreDetails(ITEMID_REF,ROW_ID,RGP_NO);
      $("#StoreModal").show();
      event.preventDefault();
  }

});

function getStoreDetails(ITEMID_REF,ROW_ID,RGP_NO){

var ITEMROWID = $("#HiddenRowId_"+ROW_ID).val();
var UOMID_REF = $("#MAIN_UOMID_REF_"+ROW_ID).val();

$("#StoreTable").html('');
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$.ajax({
    url:'{{route("transaction",[$FormId,"getStoreDetails"])}}',
    type:'POST',
    data:{ITEMID_REF:ITEMID_REF,RGP_NO:RGP_NO,ROW_ID:ROW_ID,ITEMROWID:ITEMROWID,ACTION_TYPE:'EDIT',UOMID_REF:UOMID_REF},
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
  $("#StoreModal").hide();
});

function checkStoreQty(ROW_ID,stockQty,userQty,key){

// if(userQty > stockQty){
//   $("#UserQty_"+key).val('');
//   $("#YesBtn").hide();
//   $("#NoBtn").hide();
//   $("#OkBtn1").show();
//   $("#AlertMessage").text('Issue Qty should greater then Stock-in-hand');
//   $("#alert").modal('show')
//   $("#OkBtn1").focus();
//   return false;
// } 
// else{

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
    var BillQty = parseFloat($.trim($("#SE_QTY_"+ROW_ID).val()));
    var ShortQty = (BillQty-TotalQty);

    $("#TotalHiddenQty_"+ROW_ID).val(TotalQty);
    $("#HiddenRowId_"+ROW_ID).val(NewIdArr);
    $("#RECEIVED_QTY_MU_"+ROW_ID).val(TotalQty);  
    $("#SHORT_QTY_"+ROW_ID).val(ShortQty);
    
//}
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

//SI NO details
let sqtid = "#RGPTable2";
let sqtid2 = "#RGPTable";
let salesquotationheaders = document.querySelectorAll(sqtid2 + " th");

// Sort the table element when clicking on the table headers
salesquotationheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(sqtid, ".clssqid", "td:nth-child(" + (i + 1) + ")");
  });
});

function RGPCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("RGPcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("RGPTable2");
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

function RGPNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("RGPnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("RGPTable2");
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

$('#Material').on('click','[id*="txtRGP_popup"]',function(event){

  var id = $(this).attr('id');
  var id2 = $(this).parent().parent().find('[id*="RGP_NO"]').attr('id');

  $('#hdn_sqid').val(id);
  $('#hdn_sqid2').val(id2);

  var VID_REF    = $.trim($('#VID_REF').val());
  var TYPE    = $.trim($("#TYPE").val());
  var fieldid = $(this).parent().parent().find('[id*="RGP_NO"]').attr('id');

  if(VID_REF ===""){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select vendor.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  }
  else{
    $("#RGPpopup").show();
    $("#tbody_RGP").html('');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    $.ajax({
        url:'{{route("transaction",[$FormId,"getCodeNo"])}}',
        type:'POST',
        data:{'id':VID_REF,TYPE:TYPE,'fieldid':fieldid},
        success:function(data) {
          $("#tbody_RGP").html(data);
          BindRGP();
          showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_RGP").html('');
        },
    });

    
    $(this).parent().parent().find('[id*="popupITEMID"]').val('');
    $(this).parent().parent().find('[id*="ITEMID_REF"]').val('');
    $(this).parent().parent().find('[id*="ItemName"]').val('');
    $(this).parent().parent().find('[id*="popupMUOM"]').val('');
    $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').val('');
    $(this).parent().parent().find('[id*="TotalHiddenQty"]').val('');
    $(this).parent().parent().find('[id*="HiddenRowId"]').val('');
    $(this).parent().parent().find('[id*="SE_QTY"]').val('');
    $(this).parent().parent().find('[id*="SO_FQTY"]').val('');
    $(this).parent().parent().find('[id*="Itemspec"]').val('');
    $(this).parent().parent().find('[id*="REMARKS_"]').val('');

  }

});

$("#RGP_closePopup").click(function(event){
  $("#RGPpopup").hide();
});

function BindRGP(){
  $(".clssqid").click(function(){
    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");
    var texdescdate =   $("#txt"+fieldid+"").data("descdate");
    
    var txtid= $('#hdn_sqid').val();
    var txt_id2= $('#hdn_sqid2').val();
   
    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $("#RGPpopup").hide();
    
    $("#RGPcodesearch").val(''); 
    $("#RGPnamesearch").val(''); 
   
    event.preventDefault();
  });
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

function getType(type){
  $("#fieldtype").text(type);
  $("#txtdep_popup").val('');
  $("#VID_REF").val('');
  resetTab();
}

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
}

//============================ START EMPLOYEE FUNCTION ============================
let sptid = "#EmployeeTable2";
let sptid2 = "#EmployeeTable";
let requestuserheaders = document.querySelectorAll(sptid2 + " th");


requestuserheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(sptid, ".clssemployee", "td:nth-child(" + (i + 1) + ")");
  });
});

function EmployeeCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Employeecodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("EmployeeTable2");
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

function EmployeeNameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("Employeenamesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("EmployeeTable2");
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

$("#EMPLOYEE_closePopup").click(function(event){
  $("#EMPLOYEE_popup").hide();
});

$(".clssemployee").click(function(){
  var fieldid = $(this).attr('id');
  var txtval  = $("#txt"+fieldid+"").val();
  var texdesc = $("#txt"+fieldid+"").data("desc");
  
  $('#txtdep_popup').val(texdesc);
  $('#VID_REF').val(txtval);
  $("#EMPLOYEE_popup").hide();

  $("#Employeecodesearch").val(''); 
  $("#Employeenamesearch").val(''); 
  event.preventDefault();
});

//============================ END EMPLOYEE FUNCTION ============================

//============================ CUSTOMER ============================

let cltid     = "#GlCodeTable2";
let cltid2    = "#GlCodeTable";
let clheaders = document.querySelectorAll(cltid2 + " th");

clheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(cltid, ".clsglid", "td:nth-child(" + (i + 1) + ")");
  });
});

function CustomerCodeFunction() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("customercodesearch");
    filter = input.value.toUpperCase();
    
  if(filter.length == 0)
    {
      var CODE = ''; 
      var NAME = ''; 
      loadCustomer(CODE,NAME); 
    }
    else if(filter.length >= 3)
    {
      var CODE = filter; 
      var NAME = ''; 
      loadCustomer(CODE,NAME); 
    }
    else
    {
      table = document.getElementById("GlCodeTable2");
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

function CustomerNameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("customernamesearch");
      filter = input.value.toUpperCase();
      if(filter.length == 0)
      {
        var CODE = ''; 
        var NAME = ''; 
        loadCustomer(CODE,NAME);
      }
      else if(filter.length >= 3)
      {
        var CODE = ''; 
        var NAME = filter; 
        loadCustomer(CODE,NAME);  
      }
      else
      {
        table = document.getElementById("GlCodeTable2");
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
  
  function loadCustomer(CODE,NAME){

      $("#tbody_subglacct").html('');
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
        url:'{{route("transaction",[$FormId,"getsubledger"])}}',
        type:'POST',
        data:{'CODE':CODE,'NAME':NAME},
        success:function(data) {
        $("#tbody_subglacct").html(data); 
        bindSubLedgerEvents(); 
        showSelectedCheck($("#SLID_REF").val(),"SELECT_SLID_REF");

        },
        error:function(data){
        console.log("Error: Something went wrong.");
        $("#tbody_subglacct").html('');                        
        },
      });
  }

$("#customer_closePopup").on("click",function(event){ 
    $("#customer_popus").hide();
    $("#customercodesearch").val(''); 
    $("#customernamesearch").val(''); 
   
    event.preventDefault();
});

function bindSubLedgerEvents(){
  $('.clssubgl').click(function(){

    var id      = $(this).attr('id');
    var txtval  = $("#txt"+id+"").val();
    var texdesc = $("#txt"+id+"").data("desc");
  
    $('#txtdep_popup').val(texdesc);
    $('#VID_REF').val(txtval);

    $("#customer_popus").hide();
    $("#customercodesearch").val(''); 
    $("#customernamesearch").val(''); 
    event.preventDefault();
  });
}

//============================ END CUSTOMER FUNCTION ============================
</script>
@endpush