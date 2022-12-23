@extends('layouts.app')
@section('content')

<div class="container-fluid topnav">
	<div class="row">
		<div class="col-lg-2"><a href="{{route('master',[30,'index'])}}" class="btn singlebt">Maximum Retail Price Master</a></div>
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

<div class="container-fluid filter">
	<form id="form_data" method="POST"  > 
  	<div class="inner-form">
      
      <div class="row">
        <div class="col-lg-2 pl"><p>MRP No</p></div>
        <div class="col-lg-2 pl">
          <label>{{ $objMstResponse->MRP_NO }}</label>
        </div>			
        <div class="col-lg-2 pl"><p>MRP Date</p></div>
        <div class="col-lg-2 pl">
          <label>{{ (!is_null($objMstResponse->MRP_DT) && $objMstResponse->MRP_DT!='1900-01-01')? 
            \Carbon\Carbon::parse($objMstResponse->MRP_DT)->format('Y-m-d') : ''   }}</label>
        </div>			
        <div class="col-lg-2 pl"><p>With Effective Date</p></div>
        <div class="col-lg-2 pl">
          <label>{{ (!is_null($objMstResponse->EFFECTIVE_DT) && $objMstResponse->EFFECTIVE_DT!='1900-01-01')? 
            \Carbon\Carbon::parse($objMstResponse->EFFECTIVE_DT)->format('Y-m-d') : ''   }}</label>
        </div>
      </div>
      
      <div class="row">			
        <div class="col-lg-2 pl"><p>MRP Title</p></div>
        <div class="col-lg-4 pl">
          <label>{{ $objMstResponse->MRP_TITLE }}</label>
        </div>
      </div>
      
      <div class="row">
        <div class="col-lg-2 pl"><p>De-Activated</p></div>
        <div class="col-lg-1 pl pr">
        <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objMstResponse->DEACTIVATED == 1 ? "checked" : ""}}
        value='{{$objMstResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="3" disabled>
        </div>
        
        <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
        <div class="col-lg-2 pl">
        <div class="col-lg-8 pl">
          <label>{{ (!is_null($objMstResponse->DODEACTIVATED) && $objMstResponse->DODEACTIVATED!='1900-01-01')? 
            \Carbon\Carbon::parse($objMstResponse->DODEACTIVATED)->format('Y-m-d') : ''   }}</label>       
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
                    <th style="width: 130px">MRP</th>
                    <th>Remarks</th>
                  </tr>
                </thead>
                <tbody>
                  @if(!empty($objList1))
                  @foreach($objList1 as $key => $row)
                  <tr  class="participantRow">
                    <td>
                      <input  class="form-control w-100" type="text" name="ITEMID_REF_{{ $key }}" id="TXT_ITEMID_REF_POPUP_{{ $key }}" value="{{ $row->ICODE }}"  maxlength="20" disabled>
                    </td>
                    <td hidden>
                      <input  class="form-control w-100" type="text" name="HDN_ITEMID_REF_{{ $key }}" id="HDN_ITEMID_REF_POPUP_{{ $key }}" value="{{ $row->ITEMID_REF }}" maxlength="100" disabled>
                    </td>
                    <td>
                      <input  class="form-control w-100" type="text" name="ITEMNAME_{{ $key }}" id="ITEMNAME_{{ $key }}" autocomplete="off" value="{{ $row->NAME }}" disabled >
                    </td>
                    <td hidden>
                      <input  class="form-control w-100" type="text" name="HDN_UOMID_REF_{{ $key }}" id="HDN_UOMID_REF_POPUP_{{ $key }}" value="{{ $row->UOMID_REF }}"  maxlength="100" disabled >
                    </td>
                    <td >
                      <input  class="form-control" style="width: 70px" type="text" name="UOM_{{ $key }}" id="UOM_{{ $key }}" autocomplete="off" value="{{ $row->UOMCODE }}" disabled >
                    </td>
                    <td>
                      <input  class="form-control w-100" type="text" name="ITEM_SPEC_{{ $key }}" id="ITEM_SPEC_{{ $key }}" maxlength="200" value="{{ $row->ITEMSPECI }}"  autocomplete="off" disabled >
                    </td>
                    <td>
                      <input  class="form-control rightalign five-digits" type="text" name="MRP_{{ $key }}" id="IDMRP_{{ $key }}" value="{{ $row->MRP }}" maxlength="13" autocomplete="off" disabled >
                    </td>
                    <td>
                      <input  class="form-control " type="text" name="REMARKS_{{ $key }}" id="REMARKS_{{ $key }}" maxlength="200" autocomplete="off" value="{{ $row->REMARKS }}" disabled >
                    </td>
                  </tr>
                  @endforeach
                  @else 
                  <tr  class="participantRow">
                    <td>
                      <input  class="form-control w-100" type="text" name="ITEMID_REF_0" id="TXT_ITEMID_REF_POPUP_0" maxlength="100" disabled>
                    </td>
                    <td hidden>
                      <input  class="form-control w-100" type="text" name="HDN_ITEMID_REF_0" id="HDN_ITEMID_REF_POPUP_0" maxlength="100" disabled>
                    </td>
                    <td>
                      <input  class="form-control w-100" type="text" name="ITEMNAME_0" id="ITEMNAME_0" autocomplete="off" disabled>
                    </td>
                    <td hidden>
                      <input  class="form-control w-100" type="text" name="HDN_UOMID_REF_0" id="HDN_UOMID_REF_POPUP_0" maxlength="100" disabled>
                    </td>
                    <td >
                      <input  class="form-control" style="width: 70px" type="text" name="UOM_0" id="UOM_0" autocomplete="off" disabled>
                    </td>
                    <td>
                      <input  class="form-control w-100" type="text" name="ITEM_SPEC_0" id="ITEM_SPEC_0" maxlength="200" autocomplete="off" disabled  >
                    </td>
                    <td>
                      <input  class="form-control rightalign five-digits" type="text" name="MRP_0" id="IDMRP_0" maxlength="13" autocomplete="off"  disabled>
                    </td>
                    <td>
                      <input  class="form-control " type="text" name="REMARKS_0" id="REMARKS_0" maxlength="200" autocomplete="off"  disabled>
                    </td>
                  </tr>
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

@endsection

@push('bottom-css')
@endpush

@push('bottom-scripts')

@endpush