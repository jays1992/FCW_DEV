@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
        <div class="row">
            <div class="col-lg-2">
            <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Goods Receipt Note <br/>against GE</a>
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

			<div class="col-lg-2 pl"><p>GRN No*</p></div>
			<div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="GRN_NO" id="GRN_NO" value="{{isset($objResponse->GRN_NO) && $objResponse->GRN_NO !=''?$objResponse->GRN_NO:''}}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
      </div>
			
			<div class="col-lg-2 pl"><p>GRN Date*</p></div>
			<div class="col-lg-2 pl">
			    <input {{$ActionStatus}} type="date" name="GRN_DT" id="GRN_DT" value="{{isset($objResponse->GRN_DT) && $objResponse->GRN_DT !=''?$objResponse->GRN_DT:''}}" onchange="checkPeriodClosing('{{$FormId}}',this.value,1)" class="form-control mandatory" >
      </div>

      <div class="col-lg-4 pl">

        <div class="col-lg-2 pl"><p>PO</p></div>
        <div class="col-lg-1 pl">
          <input {{$ActionStatus}} type="radio" name="GRNTYPE" id="PO" value="PO" onchange="getGgType('PO')" {{isset($objResponse->GE_TYPE) && $objResponse->GE_TYPE =='PO'?'checked':''}} />
        </div>

        <div class="col-lg-2 pl"><p>IPO</p></div>
        <div class="col-lg-1 pl">
          <input {{$ActionStatus}} type="radio" name="GRNTYPE" id="IPO" value="IPO" onchange="getGgType('IPO')" {{isset($objResponse->GE_TYPE) && $objResponse->GE_TYPE =='IPO'?'checked':''}} />
        </div>

        <div class="col-lg-2 pl"><p>BPO</p></div>
        <div class="col-lg-1 pl">
          <input {{$ActionStatus}} type="radio" name="GRNTYPE" id="BPO" value="BPO" onchange="getGgType('BPO')" {{isset($objResponse->GE_TYPE) && $objResponse->GE_TYPE =='BPO'?'checked':''}} />
        </div>

        <input type="hidden" name="GE_TYPE" id="GE_TYPE" value="{{isset($objResponse->GE_TYPE)?$objResponse->GE_TYPE:''}}" >   

      </div>





		</div>

    <div class="row">
      <div class="col-lg-2 pl"><p>Vendor*</p></div>
			<div class="col-lg-2 pl">
          <input {{$ActionStatus}} type="text" name="VID_REF_popup" id="txtdep_popup" value="{{isset($objVendorName->VCODE) && $objVendorName->VCODE !=''?$objVendorName->VCODE.' - '.$objVendorName->NAME:''}}" class="form-control mandatory"  autocomplete="off" readonly/>
          <input type="hidden" name="VID_REF" id="VID_REF" value="{{isset($objResponse->VID_REF) && $objResponse->VID_REF !=''?$objResponse->VID_REF:''}}" class="form-control" autocomplete="off" />
          <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" /> 
      </div>

      <div class="col-lg-2 pl"><p>Remarks</p></div>
			<div class="col-lg-4 pl">
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
        <div class="row"><div class="col-lg-4" style="padding-left: 15px;">Note:- 1 row mandatory in Material Tab </div></div>
					<div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;" >
						<table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
							<thead id="thead1"  style="position: sticky;top: 0">
              <tr>
                  <th>GE No</th>
                  <th>PO/IPO/BPO No</th>
									<th>Item Code <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
                  <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                  <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                  <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
									<th>Item Description</th>
                  <th>Pending Qty</th>
                  <th>Bill Qty</th>
									<th>Main UoM (MU)</th>
                  <th>Store</th>
                  <th>Bin</th>
                  <th>Received Qty (MU)</th>
                  <th>Alt UOM (AU)</th>
                  <th>Received Qty (AU)</th>
                  <th>Short Qty</th>
                  <th>Rate</th>
                  <th>Amount</th>               
                  <th>Store Name</th>               
                  <th>Remarks</th>
									<th>Action</th>
								  </tr>
							</thead>
							<tbody id="MaterialRow" >
              @if(!empty($objMAT))
              @foreach($objMAT as $key => $row)
							<tr  class="participantRow">

                  <td><input {{$ActionStatus}} style="width:100px;" type="text" name="txtRGP_popup_{{$key}}" id="txtRGP_popup_{{$key}}" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                  <td hidden><input type="hidden" name="RGP_NO_{{$key}}" id="RGP_NO_{{$key}}" value="{{ $row->GEID_REF }}"  class="form-control" autocomplete="off" /></td>

                  <td><input {{$ActionStatus}} style="width:100px;" type="text" name="txtPO_popup_{{$key}}" id="txtPO_popup_{{$key}}" value="{{ $row->PO_NO }}" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                  
                  @if($objResponse->GE_TYPE =='IPO')
                  <td hidden><input type="hidden" name="POID_REF_{{$key}}" id="POID_REF_{{$key}}" value="{{ $row->IPOID_REF }}" class="form-control" autocomplete="off" /></td>
                  @elseif($objResponse->GE_TYPE =='BPO')
                  <td hidden><input type="hidden" name="POID_REF_{{$key}}" id="POID_REF_{{$key}}" value="{{ $row->BPOID_REF }}" class="form-control" autocomplete="off" /></td>
                  @else
                  <td hidden><input type="hidden" name="POID_REF_{{$key}}" id="POID_REF_{{$key}}" value="{{ $row->POID_REF }}" class="form-control" autocomplete="off" /></td>
                  @endif

                  <td hidden><input type="hidden" name="MRSID_REF_{{$key}}" id="MRSID_REF_{{$key}}" value="{{ $row->MRSID_REF }}"  class="form-control" autocomplete="off" /></td>
                  <td hidden><input type="hidden" name="PIID_REF_{{$key}}" id="PIID_REF_{{$key}}"   value="{{ $row->PIID_REF }}" class="form-control" autocomplete="off" /></td>
                  <td hidden><input type="hidden" name="RFQID_REF_{{$key}}" id="RFQID_REF_{{$key}}" value="{{ $row->RFQID_REF }}"  class="form-control" autocomplete="off" /></td>
                  <td hidden><input type="hidden" name="VQID_REF_{{$key}}" id="VQID_REF_{{$key}}"   value="{{ $row->VQID_REF }}"  class="form-control" autocomplete="off" /></td>
                  <td hidden><input type="hidden" name="IPOID_REF_{{$key}}" id="IPOID_REF_{{$key}}" value="{{ $row->IPOID_REF }}" class="form-control" autocomplete="off" /></td>
                  <td hidden><input type="hidden" name="BPOID_REF_{{$key}}" id="BPOID_REF_{{$key}}" value="{{ $row->BPOID_REF }}" class="form-control" autocomplete="off" /></td>

                  <td><input {{$ActionStatus}}  style="width:100px;" type="text" name={{"popupITEMID_".$key}} id={{"popupITEMID_".$key}} class="form-control" value="{{ $row->ICODE }}"  autocomplete="off"  readonly/></td>
                  <td {{$AlpsStatus['hidden']}}><input {{$ActionStatus}} type="text" name="Alpspartno_{{$key}}" id="Alpspartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->ALPS_PART_NO)?$row->ALPS_PART_NO:''}}" readonly/></td>
                  <td {{$AlpsStatus['hidden']}}><input {{$ActionStatus}} type="text" name="Custpartno_{{$key}}" id="Custpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->CUSTOMER_PART_NO)?$row->CUSTOMER_PART_NO:''}}" readonly/></td>
                  <td {{$AlpsStatus['hidden']}}><input {{$ActionStatus}} type="text" name="OEMpartno_{{$key}}"  id="OEMpartno_{{$key}}" class="form-control"  autocomplete="off"  value="{{ isset($row->OEM_PART_NO)?$row->OEM_PART_NO:'' }}" readonly/></td>

                  <td hidden><input type="hidden" name={{"ITEMID_REF_".$key}} id={{"ITEMID_REF_".$key}} class="form-control"  value="{{ $row->ITEMID_REF }}" autocomplete="off" /></td>
                  <td><input {{$ActionStatus}} style="width:200px;" type="text" name={{"ItemName_".$key}} id={{"ItemName_".$key}} class="form-control" value="{{ $row->ITEM_NAME }}"  autocomplete="off"   readonly/></td>
                  
                  <td><input {{$ActionStatus}} type="text" name="PO_PENDING_QTY_{{$key}}" id="PO_PENDING_QTY_{{$key}}" value="{{ $row->PO_PENDING_QTY }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off" readonly  /></td>
                         

                  <td><input {{$ActionStatus}} type="text" name={{"SE_QTY_".$key}} id={{"SE_QTY_".$key}} class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13" value="{{ $row->BILL_QTY}}" autocomplete="off" readonly  /></td>
                  
                  <td><input {{$ActionStatus}} type="text" name={{"popupMUOM_".$key}} id={{"popupMUOM_".$key}} class="form-control" value="{{ $row->MAIN_UOM_CODE }}"  autocomplete="off"  readonly/></td>
                  <td hidden><input type="hidden" name={{"MAIN_UOMID_REF_".$key}} id={{"MAIN_UOMID_REF_".$key}}  value="{{ $row->MAIN_UOMID_REF }}" class="form-control"   autocomplete="off" /></td>
                  
                  <td align="center"><a {{$ActionStatus}} class="btn checkstore"  id="{{$key}}" ><i class="fa fa-clone"></i></a></td>
                  <td align="center"><a {{$ActionStatus}} class="btn BinEvent" id="BIN_EVENT_{{$key}}" onclick="getBin(this.id)"><i class="fa fa-clone"></i></a></td>
                  
                  <td><input {{$ActionStatus}} type="text" readonly name="RECEIVED_QTY_MU_{{$key}}" id="RECEIVED_QTY_MU_{{$key}}" value="{{ $row->RECEIVED_QTY_MU }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" onkeyup="getRate(this.id)" maxlength="13"  autocomplete="off"   /></td>
                  
                  <td><input {{$ActionStatus}} type="text" name="popupALTUOM_{{$key}}" id="popupALTUOM_{{$key}}" class="form-control" value="{{ $row->AULT_UOM_CODE }}"  autocomplete="off"  readonly/></td>
                  <td hidden><input type="hidden" name="ALT_UOMID_REF_{{$key}}" id="ALT_UOMID_REF_{{$key}}" value="{{ $row->ALT_UOMID_REF }}" class="form-control"  autocomplete="off" /></td>
                  <td><input {{$ActionStatus}} type="text" name="RECEIVED_QTY_AU_{{$key}}" id="RECEIVED_QTY_AU_{{$key}}" value="{{ $row->RECEIVED_QTY_AU }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" readonly maxlength="13"  autocomplete="off"   /></td>
                          
                  
                  <td><input {{$ActionStatus}} type="text" name="SHORT_QTY_{{$key}}" id="SHORT_QTY_{{$key}}" value="{{ $row->SHORT_QTY }}" class="form-control three-digits" onkeypress="return isNumberDecimalKey(event,this)" maxlength="13"  autocomplete="off"  readonly  /></td>


                  <td><input {{$ActionStatus}} type="text" name="RATE_{{$key}}" id="RATE_{{$key}}" class="form-control" onkeyup="getActionEvent()" onkeypress="return isNumberDecimalKey(event,this)" onkeyup="getRate(this.id)" value="{{$row->RATE}}"   autocomplete="off" style="width:100px;" /></td>
                  <td><input {{$ActionStatus}} type="text" name="AMOUNT_{{$key}}" id="AMOUNT_{{$key}}" class="form-control"  autocomplete="off"  readonly style="width:100px;" value="{{round(($row->RECEIVED_QTY_MU*$row->RATE),2)}}"  /></td>
                          



                  <td hidden ><input type="hidden" name="TotalHiddenQty_{{$key}}" id="TotalHiddenQty_{{$key}}" value="{{ $row->RECEIVED_QTY_MU}}" ></td>
                  <td hidden ><input type="hidden" name="HiddenRowId_{{$key}}" id="HiddenRowId_{{$key}}" value="{{ $row->BATCHQTY_REF}}" ></td>
                  
              
                  <td hidden><input type="hidden" name={{"SO_FQTY_".$key}} id={{"SO_FQTY_".$key}} class="form-control three-digits" maxlength="13"  autocomplete="off"  readonly/></td>
                  <td hidden><input type="text" name={{"Itemspec_".$key}} id={{"Itemspec_".$key}} class="form-control"  autocomplete="off"    /></td>
                 
                  <td><input {{$ActionStatus}} style="width:200px;" type="text" name="STORE_NAME_{{$key}}" id="STORE_NAME_{{$key}}" readonly value="{{ $row->STORE_NAME }}" class="form-control w-100" autocomplete="off" ></td>
                  <td hidden><input style="width:200px;" type="text" name="STORE_ID_{{$key}}" id="STORE_ID_{{$key}}" readonly value="{{ $row->STID }}" class="form-control w-100" autocomplete="off" ></td>
                  <td hidden><input style="width:200px;" type="text" name="HIDDEN_BIN_{{$key}}" id="HIDDEN_BIN_{{$key}}" value="{{isset($row->HIDDEN_BIN)?$row->HIDDEN_BIN:''}}"  class="form-control" readonly autocomplete="off" ></td>
                  <td hidden><input style="width:200px;" type="text" name="CHECK_BIN_DATA_{{$key}}" id="CHECK_BIN_DATA_{{$key}}" value="{{isset($row->CHECK_BIN_DATA)?$row->CHECK_BIN_DATA:''}}"   class="form-control" readonly autocomplete="off" ></td>
                          
                  <td><input {{$ActionStatus}} style="width:200px;" type="text" name="REMARKS_{{$key}}" id="REMARKS_{{$key}}" value="{{ $row->REMARKS }}" class="form-control w-100" autocomplete="off" ></td>
                  <td align="center" >
                    <button {{$ActionStatus}} class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                    <button {{$ActionStatus}} class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button" ><i class="fa fa-trash" ></i></button>
                  </td>
								  </tr>
								<tr></tr>
                @endforeach 
                @endif
							</tbody>

              <tr>
                <td colspan="3" style="text-align:center;font-weight:bold;">TOTAL</td>    
                <td {{$AlpsStatus['hidden']}} ></td>
                <td {{$AlpsStatus['hidden']}} ></td>
                <td {{$AlpsStatus['hidden']}} ></td>
                <td></td>
                <td id="PO_PENDING_QTY_total" style="font-weight:bold;"></td>
                <td id="SE_QTY_total" style="font-weight:bold;"></td>
                <td></td>
                <td></td>
                <td></td>
                <td id="RECEIVED_QTY_MU_total" style="font-weight:bold;"></td>
                <td></td>
                <td id="RECEIVED_QTY_AU_total" style="font-weight:bold;"></td>
                <td id="SHORT_QTY_total" style="font-weight:bold;"></td>
                <td id="RATE_total" style="font-weight:bold;"></td>
                <td id="AMOUNT_total" style="font-weight:bold;"></td>               
                <td></td>               
                <td></td>
                <td></td>
							</tr>


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

<div id="BinModal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:50%;z-index:1;top:2%;">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #d7d6d2;"><span style="margin-left:12px;">Bin Details</span>
        <button type="button" class="close BinModalClose" data-dismiss="modal" >&times;</button>
      </div>
      <div class="modal-body">
	    
        <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar" style="height:250px;" >
          <table id="BinTable" class="display nowrap table  table-striped table-bordered" style="width: 100%;" ></table>
        </div>
       
		    
        <button class="btn alertbt BinModalClose" style="float:right;margin-right:27px;"><div class="activeYes"></div>Ok</button>
        <div class="cl"></div>
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

<div id="RGPpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='RGP_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>GE No</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="RGPTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden" id="hdn_sqid"/>
            <input type="hidden" id="hdn_sqid2"/>
            <input type="hidden" id="hdn_sqid3"/>
            </td>
          </tr>


    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">GE No</th>
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

<div id="POpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='PO_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>PO/IPO/BPO No</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="POTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> 
            <input type="hidden"  id="hdn_poid"/>
            <input type="hidden" id="hdn_poid2"/>
            <input type="hidden" id="hdn_poid3"/>
            </td>
          </tr>
  

    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">PO/IPO/BPO No</th>
      <th class="ROW3">Date</th>
    </tr>
    </thead>
    <tbody>

    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="POcodesearch" class="form-control" autocomplete="off" onkeyup="POCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="POnamesearch" class="form-control" autocomplete="off" onkeyup="PONameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="POTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_PO">     
        
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
            <input type="hidden" name="fieldid18" id="hdn_ItemID18"/>
            <input type="hidden" name="fieldid19" id="hdn_ItemID19"/>
          </td>
      </tr>
      
      <tr>
            <th style="width:8%;" id="all-check" >Select</th>
            <th style="width:10%;">Item Code</th>
            <th style="width:10%;">Name</th>
            <th style="width:8%;">Main UOM</th>
            <th style="width:8%;">Main QTY</th>
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
    <td style="width:8%;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
    <td style="width:10%;">
    <input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction(event)">
    </td>
    <td style="width:10%;">
    <input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemNameFunction(event)">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="ItemUOMFunction(event)">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemQTYsearch" class="form-control" autocomplete="off" onkeyup="ItemQTYFunction(event)" readonly>
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="ItemGroupFunction(event)">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="ItemCategoryFunction(event)">
    </td>

    <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction(event)" readonly></td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="ItemAPNFunction(event)"></td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="ItemCPNFunction(event)"></td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="ItemOEMPNFunction(event)"></td>

    <td style="width:8%;">
    <input type="text" id="ItemStatussearch" class="form-control" autocomplete="off" onkeyup="ItemStatusFunction(event)" readonly>
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

<div id="BinModal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:40%;z-index:1;top:2%;">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #d7d6d2;"><span style="margin-left:12px;">Bin Details</span>
        <button type="button" class="close BinModalClose" data-dismiss="modal" >&times;</button>
      </div>
      <div class="modal-body">
	    
        <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar" style="height:250px;" >
          <table id="BinTable" class="display nowrap table  table-striped table-bordered" style="width: 100%;" ></table>
        </div>
       
		    
        <button class="btn alertbt BinModalClose" style="float:right;margin-right:27px;"><div class="activeYes"></div>Ok</button>
        <div class="cl"></div>
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

// START VENDOR CODE FUNCTION
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

$('#txtdep_popup').click(function(event){
  

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
    
    getMaterialHtml();
    $('#txtdep_popup').val(texdesc);
    $('#VID_REF').val(txtval);

    $("#vendoridpopup").hide();
    $("#vendorcodesearch").val(''); 
    $("#vendornamesearch").val(''); 
   
    event.preventDefault();
  });

}

      
//==================================ITEM DETAILS=============================================

let itemtid = "#ItemIDTable2";
let itemtid2 = "#ItemIDTable";
let itemtidheaders = document.querySelectorAll(itemtid2 + " th");

itemtidheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(itemtid, ".clsitemid", "td:nth-child(" + (i + 1) + ")");
  });
});

function ItemCodeFunction(e) {
  if(e.which == 13){
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
}

function ItemNameFunction(e) {
  if(e.which == 13){
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
}

function ItemUOMFunction(e) {
  if(e.which == 13){
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
}

function ItemQTYFunction(e) {
  if(e.which == 13){
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
}

function ItemGroupFunction(e) {
  if(e.which == 13){
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
}

function ItemCategoryFunction(e) {
  if(e.which == 13){
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
}

function ItemBUFunction(e) {
  if(e.which == 13){
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
}

function ItemAPNFunction(e) {
  if(e.which == 13){
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
}

function ItemCPNFunction(e) {
  if(e.which == 13){
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
}

function ItemOEMPNFunction(e) {
  if(e.which == 13){
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
}

function ItemStatusFunction(e) {
  if(e.which == 13){
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
}

$('#Material').on('click','[id*="popupITEMID"]',function(event){

var RGP_NO    = $(this).parent().parent().find('[id*="RGP_NO"]').val();
var POID_REF  = $(this).parent().parent().find('[id*="POID_REF"]').val();
var GE_TYPE   = $("#GE_TYPE").val();

$("#tbody_ItemID").html('');
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{route("transaction",[$FormId,"getItemDetails"])}}',
      type:'POST',
      data:{'status':'A',RGP_NO:RGP_NO,POID_REF:POID_REF,GE_TYPE:GE_TYPE},
      success:function(data) {
        $("#tbody_ItemID").html(data);    
        bindItemEvents();   
        $('.js-selectall').prop('disabled', false);  
        $('.js-selectall').prop('checked', false);                   
      },
      error:function(data){
        console.log("Error: Something went wrong.");
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

  var id8 = $(this).parent().parent().find('[id*="popupALTUOM"]').attr('id');
  var id9 = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');
  var id10 = $(this).parent().parent().find('[id*="RECEIVED_QTY_AU"]').attr('id');
  var id14 = $(this).parent().parent().find('[id*="PO_PENDING_QTY"]').attr('id');

  var id15 = $(this).parent().parent().find('[id*="MRSID_REF"]').attr('id');
  var id16 = $(this).parent().parent().find('[id*="PIID_REF"]').attr('id');
  var id17 = $(this).parent().parent().find('[id*="RFQID_REF"]').attr('id');
  var id18 = $(this).parent().parent().find('[id*="VQID_REF"]').attr('id');

  var id19 = $(this).parent().parent().find('[id*="RATE"]').attr('id');


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

  $('#hdn_ItemID8').val(id8);
  $('#hdn_ItemID9').val(id9);
  $('#hdn_ItemID10').val(id10);
  $('#hdn_ItemID14').val(id14);

  $('#hdn_ItemID15').val(id15);
  $('#hdn_ItemID16').val(id16);
  $('#hdn_ItemID17').val(id17);
  $('#hdn_ItemID18').val(id18);

  $('#hdn_ItemID19').val(id19);

  event.preventDefault();
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

    var fieldid = $(this).attr('id');
  var txtval =   $("#txt"+fieldid+"").val();
  var texdesc =  $("#txt"+fieldid+"").data("desc");
  var fieldid2 = $(this).find('[id*="itemname"]').attr('id');
  var txtname =  $("#txt"+fieldid2+"").val();
  var txtspec =  $("#txt"+fieldid2+"").data("desc");
  var fieldid3 = $(this).find('[id*="itemuom"]').attr('id');
  var txtmuomid =  $("#txt"+fieldid3+"").val();
  var txtauom =  $("#txt"+fieldid3+"").data("desc");

  var apartno =  $("#txt"+fieldid3+"").data("desc2");
  var cpartno =  $("#txt"+fieldid3+"").data("desc3");
  var opartno =  $("#txt"+fieldid3+"").data("desc4");

  var txtmuom =  $(this).find('[id*="itemuom"]').text().trim();
  var fieldid4 = $(this).find('[id*="uomqty"]').attr('id');
  var txtauomid =  $("#txt"+fieldid4+"").val();
  var txtauomqty =  $("#txt"+fieldid4+"").data("desc");
  var txtmuomqty =  $(this).find('[id*="uomqty"]').text().trim();
  var fieldid5 = $(this).find('[id*="irate"]').attr('id');
  var txtruom =  $("#txt"+fieldid5+"").val();
  var txtmqtyf = $("#txt"+fieldid5+"").data("desc");
  var fieldid6 = $(this).find('[id*="itax"]').attr('id');

  txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);

  var desc6         =  $("#txt"+fieldid+"").data("desc6");
  var AultUomQty    =  $("#txt"+fieldid+"").data("desc7");
  var PoPendingQty  =  $("#txt"+fieldid+"").data("desc8");

  var MRSID_REF     =  $("#txt"+fieldid+"").data("desc9");
  var PIID_REF      =  $("#txt"+fieldid+"").data("desc10");
  var RFQID_REF     =  $("#txt"+fieldid+"").data("desc11");
  var VQID_REF      =  $("#txt"+fieldid+"").data("desc12");

  var RATE          =  $("#txt"+fieldid+"").data("desc13");






  
  if(intRegex.test(txtauomqty)){
      txtauomqty = (txtauomqty +'.000');
  }

  if(intRegex.test(txtmuomqty)){
    txtmuomqty = (txtmuomqty +'.000');
  }

  
  if($(this).find('[id*="chkId"]').is(":checked") == true)  {

  $('#example2').find('.participantRow').each(function(){
    var item_geid   = $(this).find('[id*="RGP_NO"]').val();
    var item_mrsid  = $(this).find('[id*="MRSID_REF"]').val();
    var item_piid   = $(this).find('[id*="PIID_REF"]').val();
    var item_rfqid  = $(this).find('[id*="RFQID_REF"]').val();
    var item_vqid   = $(this).find('[id*="VQID_REF"]').val();
    var item_poid   = $(this).find('[id*="POID_REF"]').val();
    var itemid      = $(this).find('[id*="ITEMID_REF"]').val();
    var GE_TYPE     = $("#GE_TYPE").val();
     
    if(GE_TYPE =="PO"){
      var exist_val=item_mrsid+'-'+item_piid+'-'+item_rfqid+'-'+item_vqid+'-'+item_poid+'-'+item_geid+'-'+itemid;
    }
    else{
      var exist_val=item_poid+'-'+item_geid+'-'+itemid;
    }
   
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

                $('#hdn_ItemID8').val('');
                $('#hdn_ItemID9').val('');
                $('#hdn_ItemID10').val('');
                $('#hdn_ItemID14').val('');

                $('#hdn_ItemID15').val('');
                $('#hdn_ItemID16').val('');
                $('#hdn_ItemID17').val('');
                $('#hdn_ItemID18').val('');

                $('#hdn_ItemID19').val('');
               
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

    var txt_id8= $('#hdn_ItemID8').val();
    var txt_id9= $('#hdn_ItemID9').val();
    var txt_id10= $('#hdn_ItemID10').val();
    var txt_id14= $('#hdn_ItemID14').val();

    var txt_id15= $('#hdn_ItemID15').val();
    var txt_id16= $('#hdn_ItemID16').val();
    var txt_id17= $('#hdn_ItemID17').val();
    var txt_id18= $('#hdn_ItemID18').val();

    var txt_id19= $('#hdn_ItemID19').val();

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

    $clone.find('[id*="Alpspartno"]').val(apartno);
    $clone.find('[id*="Custpartno"]').val(cpartno);
    $clone.find('[id*="OEMpartno"]').val(opartno);

    $clone.find('[id*="ITEMID_REF"]').val(txtval);
    $clone.find('[id*="ItemName"]').val(txtname);
    $clone.find('[id*="Itemspec"]').val(txtspec);
    $clone.find('[id*="popupMUOM"]').val(txtmuom);
    $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
    $clone.find('[id*="SE_QTY"]').val(txtmuomqty);

    $clone.find('[id*="popupALTUOM"]').val(txtauom);
    $clone.find('[id*="ALT_UOMID_REF"]').val(txtauomid);
    $clone.find('[id*="RECEIVED_QTY_AU"]').val('');
    $clone.find('[id*="PO_PENDING_QTY"]').val(PoPendingQty);

    $clone.find('[id*="MRSID_REF"]').val(MRSID_REF);
    $clone.find('[id*="PIID_REF"]').val(PIID_REF);
    $clone.find('[id*="RFQID_REF"]').val(RFQID_REF);
    $clone.find('[id*="VQID_REF"]').val(VQID_REF);

    $clone.find('[id*="RATE"]').val(RATE);

    
    $clone.find('[id*="TotalHiddenQty"]').val('');
    $clone.find('[id*="HiddenRowId"]').val('');

    $clone.find('[id*="REMARKS"]').val('');

    if(getBinApplicable(txtval) !='1'){
      $clone.find('[id*="BIN_EVENT"]').hide();
    }
    else{
      $clone.find('[id*="BIN_EVENT"]').show();
    }

    
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
    var txt_id8= $('#hdn_ItemID8').val();
    var txt_id9= $('#hdn_ItemID9').val();
    var txt_id10= $('#hdn_ItemID10').val();
    var txt_id14= $('#hdn_ItemID14').val();

    var txt_id15= $('#hdn_ItemID15').val();
    var txt_id16= $('#hdn_ItemID16').val();
    var txt_id17= $('#hdn_ItemID17').val();
    var txt_id18= $('#hdn_ItemID18').val();

    var txt_id19= $('#hdn_ItemID19').val();

    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $('#'+txt_id3).val(txtname);
    $('#'+txt_id4).val(txtspec);
    $('#'+txt_id5).val(txtmuom);
    $('#'+txt_id6).val(txtmuomid);
    $('#'+txt_id7).val(txtmuomqty);

    $('#'+txt_id8).val(txtauom);
    $('#'+txt_id9).val(txtauomid);
    $('#'+txt_id10).val('');
    $('#'+txt_id14).val(PoPendingQty);


    $('#'+txt_id15).val(MRSID_REF);
    $('#'+txt_id16).val(PIID_REF);
    $('#'+txt_id17).val(RFQID_REF);
    $('#'+txt_id18).val(VQID_REF);

    $('#'+txt_id19).val(RATE);

    $('#'+txtid).parent().parent().find('[id*="Alpspartno"]').val(apartno);
    $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
    $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);

    if(getBinApplicable(txtval) !='1'){
      $('#'+txtid).parent().parent().find('[id*="BIN_EVENT"]').hide();
    }
    else{
      $('#'+txtid).parent().parent().find('[id*="BIN_EVENT"]').show();
    }


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

    $('#hdn_ItemID8').val('');
    $('#hdn_ItemID9').val('');
    $('#hdn_ItemID10').val('');
    $('#hdn_ItemID14').val('');

    $('#hdn_ItemID15').val('');
    $('#hdn_ItemID16').val('');
    $('#hdn_ItemID17').val('');
    $('#hdn_ItemID18').val('');

    $('#hdn_ItemID19').val('');

  }
                
  $("#ITEMIDpopup").hide();
  event.preventDefault();
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
  
  $('.js-selectall').prop("checked", false);   
  $("#ITEMIDpopup").hide();

  getActionEvent();
    
});




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
  var apartno =  $("#txt"+fieldid3+"").data("desc2");
  var cpartno =  $("#txt"+fieldid3+"").data("desc3");
  var opartno =  $("#txt"+fieldid3+"").data("desc4");
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

  var desc6         =  $("#txt"+fieldid+"").data("desc6");
  var AultUomQty    =  $("#txt"+fieldid+"").data("desc7");
  var PoPendingQty  =  $("#txt"+fieldid+"").data("desc8");

  var MRSID_REF     =  $("#txt"+fieldid+"").data("desc9");
  var PIID_REF      =  $("#txt"+fieldid+"").data("desc10");
  var RFQID_REF     =  $("#txt"+fieldid+"").data("desc11");
  var VQID_REF      =  $("#txt"+fieldid+"").data("desc12");

  var RATE      =  $("#txt"+fieldid+"").data("desc13");

  
  if(intRegex.test(txtauomqty)){
      txtauomqty = (txtauomqty +'.000');
  }

  if(intRegex.test(txtmuomqty)){
    txtmuomqty = (txtmuomqty +'.000');
  }

  
 if($(this).is(":checked") == true) {

  $('#example2').find('.participantRow').each(function(){
    var item_geid   = $(this).find('[id*="RGP_NO"]').val();
    var item_mrsid  = $(this).find('[id*="MRSID_REF"]').val();
    var item_piid   = $(this).find('[id*="PIID_REF"]').val();
    var item_rfqid  = $(this).find('[id*="RFQID_REF"]').val();
    var item_vqid   = $(this).find('[id*="VQID_REF"]').val();
    var item_poid   = $(this).find('[id*="POID_REF"]').val();
    var itemid      = $(this).find('[id*="ITEMID_REF"]').val();
    var GE_TYPE     = $("#GE_TYPE").val();
     
    if(GE_TYPE =="PO"){
      var exist_val=item_mrsid+'-'+item_piid+'-'+item_rfqid+'-'+item_vqid+'-'+item_poid+'-'+item_geid+'-'+itemid;
    }
    else{
      var exist_val=item_poid+'-'+item_geid+'-'+itemid;
    }
   
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

                $('#hdn_ItemID8').val('');
                $('#hdn_ItemID9').val('');
                $('#hdn_ItemID10').val('');
                $('#hdn_ItemID14').val('');

                $('#hdn_ItemID15').val('');
                $('#hdn_ItemID16').val('');
                $('#hdn_ItemID17').val('');
                $('#hdn_ItemID18').val('');

                $('#hdn_ItemID19').val('');
               
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

    var txt_id8= $('#hdn_ItemID8').val();
    var txt_id9= $('#hdn_ItemID9').val();
    var txt_id10= $('#hdn_ItemID10').val();
    var txt_id14= $('#hdn_ItemID14').val();

    var txt_id15= $('#hdn_ItemID15').val();
    var txt_id16= $('#hdn_ItemID16').val();
    var txt_id17= $('#hdn_ItemID17').val();
    var txt_id18= $('#hdn_ItemID18').val();

    var txt_id19= $('#hdn_ItemID19').val();

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
    $clone.find('[id*="Alpspartno"]').val(apartno);
    $clone.find('[id*="Custpartno"]').val(cpartno);
    $clone.find('[id*="OEMpartno"]').val(opartno);
    $clone.find('[id*="ITEMID_REF"]').val(txtval);
    $clone.find('[id*="ItemName"]').val(txtname);
    $clone.find('[id*="Itemspec"]').val(txtspec);
    $clone.find('[id*="popupMUOM"]').val(txtmuom);
    $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
    $clone.find('[id*="SE_QTY"]').val(txtmuomqty);

    $clone.find('[id*="popupALTUOM"]').val(txtauom);
    $clone.find('[id*="ALT_UOMID_REF"]').val(txtauomid);
    $clone.find('[id*="RECEIVED_QTY_AU"]').val('');
    $clone.find('[id*="PO_PENDING_QTY"]').val(PoPendingQty);

    $clone.find('[id*="MRSID_REF"]').val(MRSID_REF);
    $clone.find('[id*="PIID_REF"]').val(PIID_REF);
    $clone.find('[id*="RFQID_REF"]').val(RFQID_REF);
    $clone.find('[id*="VQID_REF"]').val(VQID_REF);

    $clone.find('[id*="RATE"]').val(RATE);

    
    $clone.find('[id*="TotalHiddenQty"]').val('');
    $clone.find('[id*="HiddenRowId"]').val('');

    $clone.find('[id*="REMARKS"]').val('');

    if(getBinApplicable(txtval) !='1'){
      $clone.find('[id*="BIN_EVENT"]').hide();
    }
    else{
      $clone.find('[id*="BIN_EVENT"]').show();
    }
    
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
    var txt_id8= $('#hdn_ItemID8').val();
    var txt_id9= $('#hdn_ItemID9').val();
    var txt_id10= $('#hdn_ItemID10').val();
    var txt_id14= $('#hdn_ItemID14').val();

    var txt_id15= $('#hdn_ItemID15').val();
    var txt_id16= $('#hdn_ItemID16').val();
    var txt_id17= $('#hdn_ItemID17').val();
    var txt_id18= $('#hdn_ItemID18').val();

    var txt_id19= $('#hdn_ItemID19').val();

  

    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $('#'+txt_id3).val(txtname);
    $('#'+txt_id4).val(txtspec);
    $('#'+txt_id5).val(txtmuom);
    $('#'+txt_id6).val(txtmuomid);
    $('#'+txt_id7).val(txtmuomqty);

    $('#'+txt_id8).val(txtauom);
    $('#'+txt_id9).val(txtauomid);
    $('#'+txt_id10).val('');
    $('#'+txt_id14).val(PoPendingQty);


    $('#'+txt_id15).val(MRSID_REF);
    $('#'+txt_id16).val(PIID_REF);
    $('#'+txt_id17).val(RFQID_REF);
    $('#'+txt_id18).val(VQID_REF);

    $('#'+txt_id19).val(RATE);
    $('#'+txtid).parent().parent().find('[id*="Alpspartno"]').val(apartno);
    $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
    $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);

    if(getBinApplicable(txtval) !='1'){
      $('#'+txtid).parent().parent().find('[id*="BIN_EVENT"]').hide();
    }
    else{
      $('#'+txtid).parent().parent().find('[id*="BIN_EVENT"]').show();
    }


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

    $('#hdn_ItemID8').val('');
    $('#hdn_ItemID9').val('');
    $('#hdn_ItemID10').val('');
    $('#hdn_ItemID14').val('');

    $('#hdn_ItemID15').val('');
    $('#hdn_ItemID16').val('');
    $('#hdn_ItemID17').val('');
    $('#hdn_ItemID18').val('');

    $('#hdn_ItemID19').val('');

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
 
  getActionEvent();
  event.preventDefault();
});
}

//==================================UDF INPUT DETAILS=============================================




$(document).ready(function(e) {
var Material = $("#Material").html(); 
$('#hdnmaterial').val(Material);
var lastdt = <?php echo json_encode(isset($objResponse->GRN_DT)?$objResponse->GRN_DT:''); ?>;
var today = new Date(); 
var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
$('#GRN_DT').attr('min',lastdt);
$('#GRN_DT').attr('max',sodate);
//$('[id*="EDD"]').attr('min',sodate);



var seudf = <?php echo json_encode($objUdfData); ?>;
var count2 = <?php echo json_encode($objCountUDF); ?>;

$('#example3').find('.participantRow3').each(function(){
      var txt_id4 = $(this).find('[id*="udfinputid"]').attr('id');
      var udfid = $(this).find('[id*="UDF"]').val();

      $.each( seudf, function( seukey, seuvalue ) {
        if(seuvalue.GRNGEID == udfid)
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

    $('#txtRGP_popup_'+sekey).val(sevalue.GE_NO);

    

    


});


var soudf = <?php echo json_encode($objUDF); ?>;
var udfforse = <?php echo json_encode($objUdfData2); ?>;
$.each( soudf, function( soukey, souvalue ) {

    $.each( udfforse, function( usokey, usovalue ) { 
        if(souvalue.UDF == usovalue.GRNGEID)
        {
            $('#popupSEID_'+soukey).val(usovalue.LABEL);
        }
    
        if(souvalue.UDF == usovalue.GRNGEID){        
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

                strinp2 = '<input {{$ActionStatus}} type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';
                
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
    getActionEvent();
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
      
      var rejected_item = check_rejected_item("{{isset($objResponse->GRNID)?$objResponse->GRNID:''}}");
      var qcserial_item = check_qcserial_item("{{isset($objResponse->GRNID)?$objResponse->GRNID:''}}");
      if(rejected_item !=''){
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please check your rejected qty missing.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
      }
      else if(qcserial_item !=''){
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please check your QC / Serial No.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
      }
      else{
        validateForm('fnApproveData');
      }

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
 
  var GRN_NO         =   $.trim($("#GRN_NO").val());
  var GRN_DT         =   $.trim($("#GRN_DT").val());
  var VID_REF        =   $.trim($("#VID_REF").val());
	 var checkCompany   =   "{{$checkCompany}}";
  if(GRN_NO ===""){
      $("#FocusId").val($("#GRN_NO"));
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('GRN No is required.');
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
      $("#AlertMessage").text('Please select GRN Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }
  else if(VID_REF ===""){
      $("#FocusId").val($("#VID_REF_popup"));
      $("#VID_REF").val('');  
      $("#ProceedBtn").focus();
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
    var allblank = [];
    var allblank1 = [];
    var allblank1_1 = [];
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
    var allblank14 = [];
        
    $('#example2').find('.participantRow').each(function(){

      if($.trim($(this).find("[id*=RGP_NO]").val())!=""){
        allblank1.push('true');
      }
      else{
        allblank1.push('false');
      }

      if($.trim($(this).find("[id*=POID_REF]").val())!=""){
        allblank1_1.push('true');
      }
      else{
        allblank1_1.push('false');
      }

      if($.trim($(this).find("[id*=ITEMID_REF]").val())!=""){
          allblank.push('true');

          if($.trim($(this).find("[id*=MAIN_UOMID_REF]").val())!=""){
            allblank2.push('true');
			if(checkCompany ==''){
				if($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) != "" && parseFloat($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val())) <= parseFloat($.trim($(this).find('[id*="PO_PENDING_QTY"]').val())) ){
				  allblank13.push('true');
				}
				else{
				  allblank13.push('false');
				}  
			}
            else{
              allblank13.push('true');
            }

            if($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) != "" && $.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) > 0.000 ){
              allblank3.push('true');
            }
            else{
              allblank3.push('false');
            }  

            if($.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) != "" && $.trim($(this).find('[id*="RECEIVED_QTY_MU"]').val()) == $.trim($(this).find('[id*="TotalHiddenQty"]').val()) ){
              allblank7.push('true');
            }
            else{
              allblank7.push('false');
            } 

            if(getBinApplicable($.trim($(this).find("[id*=ITEMID_REF]").val())) =='1' ){
              if($.trim($(this).find('[id*="HIDDEN_BIN"]').val()) != "" ){
                allblank14.push('true');
              }
              else{
                allblank14.push('false');
              } 
            }
            else{
              allblank14.push('true');
            }

          }
          else{
              allblank2.push('false');
          }      
      }
      else{
        allblank.push('false');
      }
    });

    $('#example3').find('.participantRow3').each(function(){
      if($.trim($(this).find("[id*=UDF]").val())!=""){
        allblank4.push('true');
        if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
          if($.trim($(this).find('[id*="udfvalue"]').val()) != ""){
            allblank5.push('true');
          }
          else{
            allblank5.push('false');
          }
        }  
      }                
    });

    if(jQuery.inArray("false", allblank1) !== -1){
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select GE In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank1_1) !== -1){
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select PO No In Material');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank) !== -1){
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Item In Material');
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
      $("#AlertMessage").text('Received quantity should not greater then Pending quantity in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    else if(jQuery.inArray("false", allblank14) !== -1){
      $("#alert").modal('show');
      $("#AlertMessage").text('Bin quantity should equal of store quantity in material tab.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
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

//==================================STORE DETAILS=============================================

$("#example2").on('click', '[class*="checkstore"]', function() {
  var ROW_ID      =   $(this).attr('id');
  var ITEMID_REF  = $("#ITEMID_REF_"+ROW_ID).val();
 
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

      getStoreDetails(ROW_ID);
      $("#StoreModal").show();
      event.preventDefault();
  }

});

function getStoreDetails(ROW_ID){

  var RGP_NO          = $("#RGP_NO_"+ROW_ID).val();
  var POID_REF        = $("#POID_REF_"+ROW_ID).val();
  var ITEMID_REF      = $("#ITEMID_REF_"+ROW_ID).val();
  var ITEMROWID       = $("#HiddenRowId_"+ROW_ID).val();
  var MAIN_UOMID_DES  = $("#popupMUOM_"+ROW_ID).val();
  var MAIN_UOMID_REF  = $("#MAIN_UOMID_REF_"+ROW_ID).val();
  var ALT_UOMID_DES   = $("#popupALTUOM_"+ROW_ID).val();
  var ALT_UOMID_REF  = $("#ALT_UOMID_REF_"+ROW_ID).val();
  var PO_PENDING_QTY  = $("#PO_PENDING_QTY_"+ROW_ID).val();

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
        RGP_NO:RGP_NO,
        POID_REF:POID_REF,
        ITEMID_REF:ITEMID_REF,
        MAIN_UOMID_DES:MAIN_UOMID_DES,
        MAIN_UOMID_REF:MAIN_UOMID_REF,
        ALT_UOMID_DES:ALT_UOMID_DES,
        ALT_UOMID_REF:ALT_UOMID_REF,
        ITEMROWID:ITEMROWID,
        PO_PENDING_QTY:PO_PENDING_QTY,
        ACTION_TYPE:'EDIT'
        },
      success:function(data) {
        $("#StoreTable").html(data);   
        getTotalRowValue();              
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
var STORE_NAME  = [];
var STORE_ID    = [];
var AULT_QTY    = [];

$('#StoreTable').find('.participantRow33').each(function(){

  if($.trim($(this).find("[id*=UserQty]").val())!=""){  
    var UserQty       = parseFloat($.trim($(this).find("[id*=UserQty]").val()));
    var BatchId       = $.trim($(this).find("[id*=BATCHID]").val());
    var ROWID         = $.trim($(this).find("[id*=ROWID]").val());
    var LOT_NO        = $.trim($(this).find("[id*=LOT_NO]").val());
    var VENDOR_LOTNO  = $.trim($(this).find("[id*=VENDOR_LOTNO]").val());
    var TOTAL_STOCK   = $.trim($(this).find("[id*=TOTAL_STOCK]").val());
    var BATCHNOA      = $.trim($(this).find("[id*=BATCHNOA]").val());
    var STORENAME     = $.trim($(this).find("[id*=STORE_NAME]").val());
    var STOREID       = $.trim($(this).find("[id*=STOREID]").val());
    var AULTQTY       = parseFloat($.trim($(this).find("[id*=AltUserQty]").val()));
    var EXPIRE_DATE   = $.trim($(this).find("[id*=EXPIRE_DATE]").val());
    var EXPIRY_APP    = $.trim($(this).find("[id*=EXPIRY_APPLICABLE]").val());
    
    ROW_ID.push(ROWID);
    STORE_NAME.push(STORENAME);
    STORE_ID.push(STOREID);
    AULT_QTY.push(AULTQTY);
    NewIdArr.push(BatchId+"_"+UserQty+"_"+LOT_NO+"_"+VENDOR_LOTNO+"_"+TOTAL_STOCK+"_"+EXPIRE_DATE);

    if(UserQty > 0 && EXPIRY_APP =="1" && EXPIRE_DATE ===""){
      Req.push('false');
    }
    else{
      Req.push('true');
    }

  }                
});

$("#STORE_NAME_"+ROW_ID).val(STORE_NAME);
$("#STORE_ID_"+ROW_ID).val(STORE_ID);
$("#RECEIVED_QTY_AU_"+ROW_ID).val(getArraySum(AULT_QTY));

var ROW_ID  = ROW_ID[0];
$("#HiddenRowId_"+ROW_ID).val(NewIdArr);

if(jQuery.inArray("false", Req) !== -1){
  $("#alert").modal('show');
  $("#AlertMessage").text('Please select Expiry Date.');
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}
else{
  $("#StoreModal").hide();
}

getActionEvent();
});

function checkStoreQty(ROW_ID,itemid,altumid,userQty,key,PO_PENDING_QTY){
  
  var checkCompany  = "{{$checkCompany}}";
  var perQty        = parseFloat((PO_PENDING_QTY*25)/100);
  var penQty        = parseFloat(PO_PENDING_QTY);
  var finalQty      = parseFloat(perQty+penQty).toFixed(3);

  if(userQty > PO_PENDING_QTY && checkCompany ==''){
     $("#FocusId").val($(this));
     $("#UserQty_"+key).val('');
     $("#AltUserQty_"+key).val('');
     $("#RECEIVED_QTY_MU_"+ROW_ID).val('');
     $("#RECEIVED_QTY_AU_"+ROW_ID).val('');
     $("#SHORT_QTY_"+ROW_ID).val('');
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Received Quantity cannot be greater than Pending Quantity');
     $("#alert").modal('show')
     $("#OkBtn1").focus();
     return false;
  }
  else if( parseFloat(userQty) > finalQty && checkCompany !=''){
     $("#FocusId").val($(this));
     $("#UserQty_"+key).val('');
     $("#AltUserQty_"+key).val('');
     $("#RECEIVED_QTY_MU_"+ROW_ID).val('');
     $("#RECEIVED_QTY_AU_"+ROW_ID).val('');
     $("#SHORT_QTY_"+ROW_ID).val('');
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Received Quantity cannot be greater than Pending Quantity');
     $("#alert").modal('show')
     $("#OkBtn1").focus();
     return false;
  }
  else
  {

      $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
          url:'{{route("transaction",[$FormId,"changeAltUm"])}}',
          type:'POST',
          data:{altumid:altumid,itemid:itemid,mqty:userQty},
          success:function(data) {
            $("#AltUserQty_"+key).val(data); 
            getTotalRowValue();
          },
          error:function(data){
            console.log("Error: Something went wrong.");            
          },
      }); 


      var NewQtyArr     = [];
      var NewIdArr      = [];
	    var NewQtyArrAlt  = [];

      
      $('#StoreTable').find('.participantRow33').each(function(){

          if($.trim($(this).find("[id*=UserQty]").val()) !=""){  
            var UserQty      = parseFloat($.trim($(this).find("[id*=UserQty]").val()));
            var BatchId      = $.trim($(this).find("[id*=BATCHID]").val());
			      var AltUserQty   = parseFloat($.trim($(this).find("[id*=AltUserQty]").val()));

            NewQtyArr.push(UserQty);
			      NewQtyArrAlt.push(AltUserQty);
            NewIdArr.push(BatchId+"_"+UserQty);
          }                
      });

	
      var TotalQty= getArraySum(NewQtyArr); 
	    var TotalQtyAlt= getArraySum(NewQtyArrAlt); 
	
      var PendingQty = parseFloat($.trim($("#PO_PENDING_QTY_"+ROW_ID).val()));
      var ShortQty = parseFloat(PendingQty-TotalQty).toFixed(3) > 0 ?parseFloat(PendingQty-TotalQty).toFixed(3):'0.000';

      $("#TotalHiddenQty_"+ROW_ID).val(TotalQty);
      $("#HiddenRowId_"+ROW_ID).val(NewIdArr);
      $("#SHORT_QTY_"+ROW_ID).val(ShortQty);

        // if(intRegex.test(TotalQty)){
        //   TotalQty = (TotalQty +'.000');
        // }
        
      $("#RECEIVED_QTY_MU_"+ROW_ID).val(TotalQty);
	    //$("#RECEIVED_QTY_AU_"+ROW_ID).val(TotalQtyAlt);

      var item_rate = parseFloat($.trim($("#RATE_"+ROW_ID).val()));
      var item_qty = parseFloat($.trim(TotalQty));
      var total_amount=item_rate*item_qty;
      if(intRegex.test(total_amount)){
        total_amount = (total_amount +'.00');
        }

        $("#AMOUNT_"+ROW_ID).val(total_amount);
        getTotalRowValue();
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

//==================================GE NO DETAILS=============================================
let sqtid = "#RGPTable2";
let sqtid2 = "#RGPTable";
let salesquotationheaders = document.querySelectorAll(sqtid2 + " th");

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
  var GE_TYPE    =  $.trim($('#GE_TYPE').val());
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
        data:{'id':VID_REF,'GE_TYPE':GE_TYPE,'fieldid':fieldid},
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

    $(this).parent().parent().find('[id*="txtPO_popup"]').val('');
    $(this).parent().parent().find('[id*="POID_REF"]').val('');
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

    var fieldid   = $(this).attr('id');
    var txtval    = $("#txt"+fieldid+"").val();
    var texdesc   = $("#txt"+fieldid+"").data("desc");
    
    var txtid     = $('#hdn_sqid').val();
    var txt_id2   = $('#hdn_sqid2').val();

    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);

    var CheckExist  = []; 
    $('#Material').find('.participantRow').each(function(){
      var RGP_NO  = $.trim($(this).find('[id*="RGP_NO"]').val());
      if(RGP_NO !=""){
        CheckExist.push(RGP_NO);
      }
    });

    if(CheckExist.length ==0){
      $('#'+txtid).val(texdesc);
      $('#'+txt_id2).val(txtval);
    }
    else if(arrayUnique(CheckExist) ==true){
      $('#'+txtid).val(texdesc);
      $('#'+txt_id2).val(txtval);
    }
    else{
      $('#'+txtid).val('');
      $('#'+txt_id2).val('');
      $("#alert").modal('show');
      $("#AlertMessage").text('Please Select Same GE No In All Row.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
    }
    

    $("#RGPpopup").hide();
    
    $("#RGPcodesearch").val(''); 
    $("#RGPnamesearch").val(''); 
   
    event.preventDefault();
  });
}

function arrayUnique(array){
    function onlyUnique(value, index, self) { 
         return self.indexOf(value) === index;
    }

    var unique = array.filter( onlyUnique );

    return (unique.length == 1);
} 

//==================================PO NO DETAILS=============================================

let POTable2 = "#POTable2";
let POTable = "#POTable";
let POheaders = document.querySelectorAll(POTable + " th");

POheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(POTable2, ".clsspoid", "td:nth-child(" + (i + 1) + ")");
  });
});

function POCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("POcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("POTable2");
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

function PONameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("POnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("POTable2");
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

$('#Material').on('click','[id*="txtPO_popup"]',function(event){

  var id = $(this).attr('id');
  var id2 = $(this).parent().parent().find('[id*="POID_REF"]').attr('id');

  $('#hdn_poid').val(id);
  $('#hdn_poid2').val(id2);

  var VID_REF    =  $.trim($('#VID_REF').val());
  var RGP_NO     =  $(this).parent().parent().find('[id*="RGP_NO"]').val();
  var GE_TYPE    =  $.trim($('#GE_TYPE').val());
  var fieldid = $(this).parent().parent().find('[id*="POID_REF"]').attr('id');

  if(VID_REF ===""){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Vendor');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  }
  else if(RGP_NO ===""){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select GE No In Material');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
  }
  else{
    $("#POpopup").show();
    $("#tbody_PO").html('');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    $.ajax({
        url:'{{route("transaction",[$FormId,"getPoCodeNo"])}}',
        type:'POST',
        data:{'id':RGP_NO,'GE_TYPE':GE_TYPE,'fieldid':fieldid},
        success:function(data) {
          $("#tbody_PO").html(data);
          BindPO();
          showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_PO").html('');
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

$("#PO_closePopup").click(function(event){
  $("#POpopup").hide();
});

function BindPO(){
  $(".clsspoid").click(function(){
    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");

    var txtid= $('#hdn_poid').val();
    var txt_id2= $('#hdn_poid2').val();

    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $("#POpopup").hide();
    
    $("#POcodesearch").val(''); 
    $("#POnamesearch").val(''); 
   
    event.preventDefault();
  });
}


function getGgType(type){

if($("#"+type).prop("checked") == true){
  $("#GE_TYPE").val(type);
}
else{
  $("#GE_TYPE").val('PO');
}
         
getMaterialHtml();

}

function getMaterialHtml(){
  $('#example2').find('.participantRow').each(function(){
    var rowcount = $(this).closest('table').find('.participantRow').length;
    $(this).find('input:text').val('');
    $(this).find('input:hidden').val('');
      if(rowcount > 1)
      {
        $(this).closest('.participantRow').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count1').val(rowcount);
      }
  });

  getActionEvent();
}



function getRate(fieldid){

  var arr     = fieldid.split('_');
  var textid  = arr.slice(-1)[0]

  var QTY   = parseFloat($.trim($("#RECEIVED_QTY_MU_"+textid).val()));
  var RATE  = parseFloat($.trim($("#RATE_"+textid).val()));
  var AMOUNT= (QTY*RATE).toFixed(2);
  $("#AMOUNT_"+textid).val(AMOUNT);

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

function check_rejected_item(id){

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var result = $.ajax({type: 'POST',url:'{{ route("transaction",[$FormId,"check_rejected_item"])}}',async: false,data: {id:id},done: function(response) {return response;}}).responseText;
  return result;
}


function getTotalRowValue(){

  var strSOTCK              = 0;
  var strDISPATCH_MAIN_QTY  = 0;
  var DISPATCH_ALT_QTY      = 0;

  $('#StoreTable').find('.participantRow33').each(function(){
      strSOTCK  = parseFloat($(this).find('[id*="TOTAL_STOCK"]').val()) > 0? strSOTCK+parseFloat($(this).find('[id*="TOTAL_STOCK"]').val()):strSOTCK;
      strDISPATCH_MAIN_QTY = parseFloat($(this).find('[id*="UserQty"]').val()) > 0?strDISPATCH_MAIN_QTY+parseFloat($(this).find('[id*="UserQty"]').val()):strDISPATCH_MAIN_QTY;
      DISPATCH_ALT_QTY  = parseFloat($(this).find('[id*="AltUserQty"]').val()) > 0?DISPATCH_ALT_QTY+parseFloat($(this).find('[id*="AltUserQty"]').val()):DISPATCH_ALT_QTY;
  });

  strSOTCK          = strSOTCK > 0?parseFloat(strSOTCK).toFixed(3):'';
  strDISPATCH_MAIN_QTY   = strDISPATCH_MAIN_QTY > 0?parseFloat(strDISPATCH_MAIN_QTY).toFixed(3):'';
  DISPATCH_ALT_QTY    = DISPATCH_ALT_QTY > 0?parseFloat(DISPATCH_ALT_QTY).toFixed(2):'';

  $("#strSOTCK_total").text(strSOTCK);
  $("#strDISPATCH_MAIN_QTY_total").text(strDISPATCH_MAIN_QTY);
  $("#DISPATCH_ALT_QTY_total").text(DISPATCH_ALT_QTY);

}


function getActionEvent(){
  getTotalMaterialRowValue();
}

function getTotalMaterialRowValue(){

  var PO_PENDING_QTY  = 0;
  var SE_QTY          = 0;
  var RECEIVED_QTY_MU = 0; 
  var RECEIVED_QTY_AU = 0;
  var SHORT_QTY       = 0;
  var RATE            = 0;
  var AMOUNT          = 0;

  $('#Material').find('.participantRow').each(function(){

    var item_qty      = parseFloat($(this).find('[id*="RECEIVED_QTY_MU"]').val());
    var item_rate     = parseFloat($(this).find('[id*="RATE"]').val());
    var total_amount  = item_rate*item_qty;
    if(intRegex.test(total_amount)){
      total_amount = (total_amount +'.00');
    }
    $(this).find('[id*="AMOUNT"]').val(total_amount);

    PO_PENDING_QTY  = $(this).find('[id*="PO_PENDING_QTY"]').val() > 0? PO_PENDING_QTY+parseFloat($(this).find('[id*="PO_PENDING_QTY"]').val()):PO_PENDING_QTY;
    SE_QTY          = $(this).find('[id*="SE_QTY"]').val() > 0?SE_QTY+parseFloat($(this).find('[id*="SE_QTY"]').val()):SE_QTY;
    RECEIVED_QTY_MU = $(this).find('[id*="RECEIVED_QTY_MU"]').val() > 0?RECEIVED_QTY_MU+parseFloat($(this).find('[id*="RECEIVED_QTY_MU"]').val()):RECEIVED_QTY_MU;
    RECEIVED_QTY_AU = $(this).find('[id*="RECEIVED_QTY_AU"]').val() > 0?RECEIVED_QTY_AU+parseFloat($(this).find('[id*="RECEIVED_QTY_AU"]').val()):RECEIVED_QTY_AU;
    SHORT_QTY       = $(this).find('[id*="SHORT_QTY"]').val() > 0?SHORT_QTY+parseFloat($(this).find('[id*="SHORT_QTY"]').val()):SHORT_QTY;
    RATE            = $(this).find('[id*="RATE"]').val() > 0?RATE+parseFloat($(this).find('[id*="RATE"]').val()):RATE;
    AMOUNT          = $(this).find('[id*="AMOUNT"]').val() > 0?AMOUNT+parseFloat($(this).find('[id*="AMOUNT"]').val()):AMOUNT;

  });

  PO_PENDING_QTY  = PO_PENDING_QTY > 0?parseFloat(PO_PENDING_QTY).toFixed(3):'';
  SE_QTY          = SE_QTY > 0?parseFloat(SE_QTY).toFixed(3):'';
  RECEIVED_QTY_MU = RECEIVED_QTY_MU > 0?parseFloat(RECEIVED_QTY_MU).toFixed(3):'';
  RECEIVED_QTY_AU = RECEIVED_QTY_AU > 0?parseFloat(RECEIVED_QTY_AU).toFixed(3):'';
  SHORT_QTY       = SHORT_QTY > 0?parseFloat(SHORT_QTY).toFixed(3):'';
  RATE            = RATE > 0?parseFloat(RATE).toFixed(5):'';
  AMOUNT          = AMOUNT > 0?parseFloat(AMOUNT).toFixed(2):'';

  $("#PO_PENDING_QTY_total").text(PO_PENDING_QTY);
  $("#SE_QTY_total").text(SE_QTY);
  $("#RECEIVED_QTY_MU_total").text(RECEIVED_QTY_MU);
  $("#RECEIVED_QTY_AU_total").text(RECEIVED_QTY_AU);
  $("#SHORT_QTY_total").text(SHORT_QTY);
  $("#RATE_total").text(RATE);
  $("#AMOUNT_total").text(AMOUNT);

}

$(document).ready(function(){
  getActionEvent();
});

function check_qcserial_item(id){

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

var result = $.ajax({type: 'POST',url:'{{ route("transaction",[$FormId,"check_qcserial_item"])}}',async: false,data: {id:id},done: function(response) {return response;}}).responseText;
return result;
}

// BIN DETAILS

function getBin(textid){
  $("#BinTable").html('');
  var textid      = textid.split('_').pop();
  var stid        = $("#STORE_ID_"+textid).val();
  var stqt        = $("#RECEIVED_QTY_MU_"+textid).val();
  var RACK_QTY    = $("#HIDDEN_BIN_"+textid).val();
  var ITEMID_REF  = $("#ITEMID_REF_"+textid).val();

  if(getBinApplicable(ITEMID_REF) !='1'){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Bin not applicabile for this item.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
    return false;
  }
  else if(stid ===""){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select store.');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
    return false;
  }
  else{

    var BIN_ARRAY = [];
    $('#Material').find('.participantRow').each(function(){
      if($.trim($(this).find("[id*=CHECK_BIN_DATA]").val()) !="" && $(this).find("[id*=CHECK_BIN_DATA]").attr('id') !='CHECK_BIN_DATA_'+textid){  
        BIN_ARRAY.push($.trim($(this).find("[id*=CHECK_BIN_DATA]").val()));
      }                
    });

    var BINARRAY = BIN_ARRAY.toString();

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      url:'{{route("transaction",[$FormId,"getBinDetails"])}}',
      type:'POST',
      data:{STID:stid,STQTY:stqt,MATERIAL_ROWID:textid,RACK_QTY:RACK_QTY,BINARRAY:BINARRAY,ACTION_TYPE:'EDIT'},
      success:function(data) {
        $("#BinTable").html(data);     
        $("#BinModal").show();    
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#BinTable").html('');                        
      },
    });
  }
}

$(".BinModalClose").click(function(event){
  var TOTAL_BIN   = $("#TOTAL_BIN").val() !=''?parseFloat($("#TOTAL_BIN").val()):0;
  var TOTAL_STORE = $("#TOTAL_STORE").val() !=''?parseFloat($("#TOTAL_STORE").val()):0;

  if($("#TOTAL_BIN").val() !='' && TOTAL_STORE !=TOTAL_BIN){
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Bin Qty Should be equal of Total Received Qty (MU)');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
    return false;
  }
  else{

    var HIDDEN_BIN      = [];
    var MATERIAL_ROWID  = [];
    var CHECK_BIN_DATA  = [];

    $('#BinTable').find('.participantRow44').each(function(){
      MATERIAL_ROWID.push($.trim($(this).find("[id*=MATERIAL_ROWID]").val()));
      if($.trim($(this).find("[id*=BIN_QTY]").val())!=""){
       
        var rack_id = $.trim($(this).find("[id*=RACK_ID]").val());
        var rack_no = $.trim($(this).find("[id*=RACK_NO]").val());
        var bin_no  = $.trim($(this).find("[id*=BIN_NO]").val());
        var bin_qty = parseFloat($.trim($(this).find("[id*=BIN_QTY]").val()));

        HIDDEN_BIN.push(rack_id+"###"+rack_no+"###"+bin_no+"###"+bin_qty);
        CHECK_BIN_DATA.push(bin_no);
      }                
    });

    $("#HIDDEN_BIN_"+MATERIAL_ROWID[0]).val(HIDDEN_BIN);
    $("#CHECK_BIN_DATA_"+MATERIAL_ROWID[0]).val(CHECK_BIN_DATA);
    $("#BinModal").hide();
  }
});

function sumBinQty(){
  var BIN_QTY_ARRAY   = [];
  $('#BinTable').find('.participantRow44').each(function(){
    if($.trim($(this).find("[id*=BIN_QTY]").val()) !=""){  
      BIN_QTY_ARRAY.push(parseFloat($.trim($(this).find("[id*=BIN_QTY]").val())));
    }                
  });
  $("#TOTAL_BIN").val(getArraySum(BIN_QTY_ARRAY));
}

function getBinApplicable(ITEMID_REF){

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var posts = $.ajax({
			url:'{{route("transaction",[$FormId,"getBinApplicable"])}}',
			type:'POST',
			async: false,
			dataType: 'json',
			data: {ITEMID_REF:ITEMID_REF},
			done: function(response) {return response;}
		  }).responseText;

      return posts;
}
</script>
@endpush