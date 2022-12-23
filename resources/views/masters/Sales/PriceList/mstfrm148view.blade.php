@extends('layouts.app')
@section('content')

<div class="container-fluid topnav">
	<div class="row">
		<div class="col-lg-2"><a href="{{route('master',[148,'index'])}}" class="btn singlebt">Price List Standard Master</a></div>
		<div class="col-lg-10 topnav-pd">
      <button  id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
      <button class="btn topnavbt"  disabled><i class="fa fa-save"></i> Save</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id="btnUndo"  disabled><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" disabled><i class="fa fa-lock"></i> Approved</button>
      <button class="btn topnavbt" id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
      <a href="{{route('home')}}" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
		</div>
	</div>
</div>
@php
  //DUMP($objPopup1List);
//  DUMP($objList1);   
@endphp
<div class="container-fluid filter">
	<form id="form_data" method="POST" onsubmit="return false;" > 
   
		<div class="inner-form">
     
      <div class="row">
        <div class="col-lg-2 pl"><p>PL No</p></div>
        <div class="col-lg-2 pl">
          <label>{{ $objMstResponse->PL_NO }}</label>
        </div>			
        <div class="col-lg-2 pl"><p>PL Date</p></div>
        <div class="col-lg-2 pl">   
          <label>{{ \Carbon\Carbon::parse($objMstResponse->PL_DT)->format('d/m/Y') }}</label>       
        </div>			
        <div class="col-lg-1 pl"><p>Price List Category</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="PLCID_REF_POPUP" id="PLCID_REF_POPUP" class="form-control mandatory" value="{{ $objPLCategory->PLCCODE }} - {{ $objPLCategory->PLCDESCRIPTIONS }} " required readonly tabindex="3" />
        </div>
      </div>

      <div class="row">
        <div class="col-lg-2 pl"><p>From Period</p></div>
        <div class="col-lg-2 pl">
          <label>{{ (!is_null($objMstResponse->PERIOD_FRDT) && $objMstResponse->PERIOD_FRDT!='1900-01-01')? \Carbon\Carbon::parse($objMstResponse->PERIOD_FRDT)->format('d/m/Y') : ''   }}</label>
        </div>
        
        <div class="col-lg-2 pl"><p>To Period</p></div>
        <div class="col-lg-2 pl">
          <label>{{ (!is_null($objMstResponse->PERIOD_TODT) && $objMstResponse->PERIOD_TODT!='1900-01-01')? \Carbon\Carbon::parse($objMstResponse->PERIOD_TODT)->format('d/m/Y') : ''   }}</label>
        </div>
        
       
        <div class="col-lg-1 pl"><p>Price List Title</p></div>
        <div class="col-lg-3 pl">
          <label>{{ $objMstResponse->PL_TITLE }}</label>
        </div>        
        
      </div>


     
      <div class="row">

        <div class="col-lg-2 pl"><p>MRP Applicable</p></div>
        <div class="col-lg-2 pl pr">
          <label>{{$objMstResponse->MRP_APPLICABLE == 1 ? "Yes" : "No" }}</label>
        </div>
        

        <div class="col-lg-2 pl"><p>De-Activated</p></div>
        <div class="col-lg-2 pl pr">
        <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" value='{{$objMstResponse->DEACTIVATED == 1 ? 1 : 0}}'  tabindex="3" disabled>
        </div>
        
        <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
        <div class="col-lg-2 pl">
        <div class="col-lg-8 pl">
          <label>{{ (!is_null($objMstResponse->DODEACTIVATED) && $objMstResponse->DODEACTIVATED!='1900-01-01')? \Carbon\Carbon::parse($objMstResponse->DODEACTIVATED)->format('d/m/Y') : ''   }}</label>
        </div>
        </div>        

      </div>    

	</div>
		
		
	<div class="container-fluid">
		<div class="row">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#tab1" tabindex="6">Material</a></li>
			</ul>
			<div class="tab-content">
        <div id="tab1" class="tab-pane fade in active">
            <div class="table-responsive table-wrapper-scroll-y " style="height:260px;margin-top:10px;">					
              <table id="table3" class="display nowrap table table-striped table-bordered itemlist itemlistscroll w-200" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0">
                  <tr >
                    <th style="width: 120px">Item Code<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3" value="{{ $objList1Count }}"></th>
                    <th>Item Name</th>
                    <th style="width: 70px">UoM Code</th>
                    <th>Item Specifications (If Any)</th>
                    <th>Item Cost</th>
                    <th style="width: 130px">Less % of MRP</th>
                    <th  style="width: 130px">List Price (LP)</th>
                    <th  style="width: 130px">Customer Price</th>
                    <th  style="width: 130px">Dealer Price</th>
                    <th  style="width: 130px">MRP</th>
                    <th  style="width: 130px">MSP</th>
                    <th  style="width: 120px">GST included in LP</th>
                    <th>Remarks</th>
                  </tr>
                </thead>
                <tbody>
                  @if(!empty($objList1))
                  @foreach($objList1 as $key => $row)
                  <tr  class="participantRow">
                    <td>
                      <input  class="form-control w-100" type="text" name="ITEMID_REF_{{ $key }}" id="TXT_ITEMID_REF_POPUP_{{ $key }}" value="{{ $row->ICODE }}" maxlength="100" readonly>
                    </td>
                    <td hidden>
                      <input  class="form-control w-100" type="text" name="HDN_ITEMID_REF_{{ $key }}" id="HDN_ITEMID_REF_POPUP_{{ $key }}" value="{{ $row->ITEMID_REF }}" maxlength="100" readonly>
                    </td>
                    <td>
                      <input  class="form-control w-100" type="text" name="ITEMNAME_{{ $key }}" id="ITEMNAME_{{ $key }}"  value="{{ $row->NAME }}" autocomplete="off" readonly >
                    </td>
                    <td hidden>
                      <input  class="form-control w-100" type="text" name="HDN_UOMID_REF_{{ $key }}" id="HDN_UOMID_REF_POPUP_{{ $key }}" value="{{ $row->UOMID_REF }}" maxlength="100" readonly>
                    </td>
                    <td >
                      <input  class="form-control" style="width: 70px" type="text" name="UOM_{{ $key }}" id="UOM_{{ $key }}" value="{{ $row->UOMCODE }}"  autocomplete="off" readonly >
                    </td>
                    <td>
                     <input  class="form-control w-100" type="text" name="ITEM_SPEC_{{ $key }}" id="ITEM_SPEC_{{ $key }}" value="{{ $row->ITEMSPECI }}" maxlength="200" autocomplete="off"  readonly>
                    </td>
                    <td>
                     <input  class="form-control w-100 " type="text" name="ITEM_COST_{{ $key }}" id="ITEM_COST_{{ $key }}" value="{{ ($objMstResponse->MRP_APPLICABLE == 1) ? $row->MRP_PRICE :  $row->ITEMCOST }}"  autocomplete="off" readonly >
                    </td>
                    <td>
                      <input  class="form-control CLS_MRP_PER rightalign four-digits" maxlength="8" type="text" name="MRP_PER_{{ $key }}" id="IDMRP_PER_{{ $key }}" value="{{ $row->MRP_PER }}" autocomplete="off"  {{ $objMstResponse->MRP_APPLICABLE == 1 ? "" : "readonly" }}  readonly>
                    </td>
                    <td>
                      <input  class="form-control CLS_LISTPRICE rightalign five-digits" type="text"  name="LISTPRICE_{{ $key }}" id="IDLISTPRICE_{{ $key }}" value="{{ $row->LISTPRICE }}"  maxlength="13" autocomplete="off" {{ $objMstResponse->MRP_APPLICABLE == 1 ? "readonly" : "" }}  readonly>
                    </td>
                    <td>
                      <input  class="form-control  rightalign five-digits" type="text"  name="CUSTOMER_PRICE_{{ $key }}" id="IDCUSTOMER_PRICE_{{ $key }}" value="{{ $row->CUSTOMER_PRICE }}"  maxlength="13" autocomplete="off"  disabled >
                    </td>
                    <td>
                      <input  class="form-control  rightalign five-digits" type="text"  name="DEALER_PRICE_{{ $key }}" id="IDDEALER_PRICE_{{ $key }}" value="{{ $row->DEALER_PRICE }}"  maxlength="13" autocomplete="off" disabled  >
                    </td>
                    <td>
                      <input  class="form-control  rightalign five-digits" type="text"  name="MRP_{{ $key }}" id="IDMRP_{{ $key }}" value="{{ $row->MRP }}"  maxlength="13" autocomplete="off"   disabled>
                    </td>
                    <td>
                      <input  class="form-control  rightalign five-digits" type="text"  name="MSP_{{ $key }}" id="IDMSP_{{ $key }}" value="{{ $row->MSP }}"  maxlength="13" autocomplete="off"  disabled >
                    </td>
                    <td style="text-align:center;">
                      <input type="checkbox" class="filter-none"  name="GST_IN_LP_{{ $key }}" id="IDGST_IN_LP_{{ $key }}" value="1" autocomplete="off" {{ $row->GST_IN_LP==1? "checked":"" }} disabled>
                    </td>
                    <td>
                      <input  class="form-control " type="text" name="REMARKS_{{ $key }}" id="REMARKS_{{ $key }}" value="{{ $row->REMARKS }}"  maxlength="200" autocomplete="off"  readonly>
                    </td>
                  </tr>
                  @endforeach
                  @endif
                </tbody>
              </table>          
            </div>
        </div><!-- tab1 -->

      </div><!-- tab-content -->
		</div><!-- row -->			
	</div><!-- container-fluid -->
						
	</form>
  </div>
@endsection

@section('alert')
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog" style="position:relative;top:82px;left:273px;"  >
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
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="margin-left: 90px;display:none;">
              <div id="alert-active" class="activeOk1"></div>OK</button>
        </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('bottom-css')
@endpush

@push('bottom-scripts')
@endpush