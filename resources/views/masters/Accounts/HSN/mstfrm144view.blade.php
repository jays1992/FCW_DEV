@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
	<div class="row">
		<div class="col-lg-2"><a href="{{route('master',[144,'index'])}}" class="btn singlebt">HSN Master</a></div>
		<div class="col-lg-10 topnav-pd">
		  <button  id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
      <button id="btnSaveItem"     class="btn topnavbt" tabindex="7" disabled="disabled"><i class="fa fa-save"></i> Save</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo" disabled="disabled"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
      <button class="btn topnavbt" id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
      <a href="{{route('home')}}" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
		</div>
	</div>
</div>
<div class="container-fluid filter">
	<form id="form_data" method="POST"  > 
    @CSRF  
		<div class="inner-form">
    
			<div class="row">
				<div class="col-lg-2 pl"><p>HSN Code</p></div>
				<div class="col-lg-2 pl">
          <label>{{ $objMstResponse->HSNCODE }}</label>
				</div>
			</div>
			
			<div class="row">
				<div class="col-lg-2 pl"><p>HSN Description</p></div>
				<div class="col-lg-4 pl">
          <label>{{ $objMstResponse->HSNDESCRIPTION }}</label>
        </div>
			</div>
				
      <div class="row">
        <div class="col-lg-2 pl"><p>De-Activated</p></div>
        <div class="col-lg-1 pl pr">
        <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objMstResponse->DEACTIVATED == 1 ? "checked" : ""}}  value='{{$objMstResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="3" disabled>
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
				<li class="active"><a data-toggle="tab" href="#tab1">Normal</a></li>
				<li><a data-toggle="tab" href="#tab2">Exceptional</a></li>
			</ul>
			<div class="tab-content">
        <div id="tab1" class="tab-pane fade in active">
            <div class="table-responsive table-wrapper-scroll-y " style="height:260px;margin-top:10px;">					
              <table id="table3" class="display nowrap table table-striped table-bordered itemlist itemlistscroll w-200" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0">
                  <tr >
                    <th>Tax Type Code<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3" value="{{ $objList1Count }}"></th>
                    <th>Tax Type Description</th>
                    <th>Rate</th>
                    <th>Deactivated</th>
                    <th>Date Of Deactivated</th>
                  </tr>
                </thead>
                <tbody>
                  @if(!empty($objList1))
                  @foreach($objList1 as $key => $row)
                  <tr  class="participantRow">
                    <td>
                      <input  class="form-control w-100" type="text" name="NOR_TAXID_REF_{{ $key }}" id="TXTNOR_TAXID_REF_POPUP_{{ $key }}" value="{{ $row->TTCODE }}" maxlength="100" disabled>
                    </td>
                    <td hidden>
                      <input  class="form-control w-100" type="text" name="HDNNOR_TAXID_REF_{{ $key }}" id="HDNNOR_TAXID_REF_POPUP_{{ $key }}"  value="{{ $row->TAXID_REF }}" maxlength="100" disabled>
                    </td>
                    <td>
                      <input  class="form-control w-100" type="text" name="NOR_TTDESCRIPTION_{{ $key }}" id="NOR_TTDESCRIPTION_{{ $key }}" value="{{ $row->TTDESCRIPTION }}" maxlength="20" autocomplete="off" disabled >
                    </td>
                    <td>
                      <input  class="form-control rightalign two-digits" type="text" name="NOR_RATE_{{ $key }}" id="NOR_RATE_{{ $key }}"  value="{{ $row->NRATE }}" maxlength="6" autocomplete="off" disabled >
                    </td>
                    <td style="text-align:center;" >
                      <input type="checkbox" name="NOR_DEACTIVATED_{{ $key }}" id="NOR_DEACTIVATED_{{ $key }}" {{ ($row->DEACTIVATED == 1 || $row->DEACTIVATED=='on')?'checked':'' }} class="filter-none"  value="{{ ($row->DEACTIVATED == 1 || $row->DEACTIVATED=='on')? 1 : 0 }}" disabled>
                    </td>
                    <td style="text-align:center;" >
                      <input type="date"  name="NOR_DODEACTIVATED_{{ $key }}" id="NOR_DODEACTIVATED_{{ $key }}" class="form-control" value="{{ (!is_null($row->DODEACTIVATED) && $row->DODEACTIVATED!='1900-01-01')? $row->DODEACTIVATED : '' }}"   autocomplete="off" disabled>
                    </td>
                  </tr>
                  @endforeach
                  @else 
                    <tr  class="participantRow">
                      <td>
                        <input  class="form-control w-100" type="text" name="NOR_TAXID_REF_0" id="TXTNOR_TAXID_REF_POPUP_0" maxlength="100" disabled>
                      </td>
                      <td hidden>
                        <input  class="form-control w-100" type="text" name="HDNNOR_TAXID_REF_0" id="HDNNOR_TAXID_REF_POPUP_0" maxlength="100" disabled >
                      </td>
                      <td>
                        <input  class="form-control w-100" type="text" name="NOR_TTDESCRIPTION_0" id="NOR_TTDESCRIPTION_0" maxlength="20" autocomplete="off" disabled >
                      </td>
                      <td>
                        <input  class="form-control rightalign two-digits" type="text" name="NOR_RATE_0" id="NOR_RATE_0" maxlength="6" autocomplete="off"  disabled>
                      </td>
                      <td style="text-align:center;" >
                        <input type="checkbox" name="NOR_DEACTIVATED_0" id="NOR_DEACTIVATED_0" class="filter-none"  value="0" disabled>
                      </td>
                      <td style="text-align:center;" >
                        <input type="date" name="NOR_DODEACTIVATED_0" id="NOR_DODEACTIVATED_0" class="form-control"  autocomplete="off" disabled>
                      </td>
                    </tr>
                  @endif
                </tbody>
              </table>          
            </div>
        </div><!-- tab1 -->

        <div id="tab2" class="tab-pane fade">
          <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:250px;margin-top:10px;" >					 
            <table id="table4" class="display nowrap table table-striped table-bordered itemlist itemlistscroll w-200" style="height:auto !important;">
              <thead id="thead1"  style="position: sticky;top: 0">
                <tr >
                  <th>Tax Type Code<input class="form-control" type="hidden" name="Row_Count4" id ="Row_Count4" value="{{ $objList2Count }}"></th>
                  <th>Tax Type Description</th>
                  <th>Rate</th>
                  <th>Deactivated</th>
                  <th>Date Of Deactivated</th>
                </tr>
              </thead>
              <tbody>
              @if(!empty($objList2))
              @foreach($objList2 as $key => $row)
                <tr  class="participantRow">
                  <td>
                    <input  class="form-control w-100" type="text" name="EXC_TAXID_REF_{{ $key }}" id="TXTEXC_TAXID_REF_POPUP_{{ $key }}" value="{{ $row->TTCODE }}" maxlength="100" disabled>
                  </td>
                  <td hidden >
                    <input  class="form-control w-100" type="text" name="HDNEXC_TAXID_REF_{{ $key }}" id="HDNEXC_TAXID_REF_POPUP_{{ $key }}" value="{{ $row->TAXID_REF }}"  maxlength="100" disabled>
                  </td>
                  <td>
                    <input  class="form-control w-100" type="text" name="EXC_TTDESCRIPTION_{{ $key }}" id="EXC_TTDESCRIPTION_{{ $key }}" value="{{ $row->TTDESCRIPTION }}"  autocomplete="off" disabled >
                  </td>
                  <td>
                    <input  class="form-control two-digits rightalign" type="text" name="EXC_RATE_{{ $key }}" id="EXC_RATE_{{ $key }}" value="{{ $row->ERATE }}" maxlength="6" autocomplete="off" disabled >
                  </td>
                  <td style="text-align:center;" >
                    <input type="checkbox" name="EXC_DEACTIVATED_{{ $key }}" id="EXC_DEACTIVATED_{{ $key }}" {{ ($row->DEACTIVATED == 1 || $row->DEACTIVATED=='on')?'checked':'' }}  class="filter-none"  value="{{ ($row->DEACTIVATED == 1 || $row->DEACTIVATED=='on')? 1 : 0 }}" disabled>
                  </td>
                  <td style="text-align:center;" >
                    <input type="date" name="EXC_DODEACTIVATED_{{ $key }}" id="EXC_DODEACTIVATED_{{ $key }}" class="form-control"  autocomplete="off" value="{{ (!is_null($row->DODEACTIVATED) && $row->DODEACTIVATED!='1900-01-01')? $row->DODEACTIVATED : '' }}" disabled>
                  </td>
                </tr>
              @endforeach
              @else 
                <tr  class="participantRow">
                  <td>
                    <input  class="form-control w-100" type="text" name="EXC_TAXID_REF_0" id="TXTEXC_TAXID_REF_POPUP_0"  disabled>
                  </td>
                  <td hidden>
                    <input  class="form-control w-100" type="text" name="HDNEXC_TAXID_REF_0" id="HDNEXC_TAXID_REF_POPUP_0" disabled>
                  </td>
                  <td>
                    <input  class="form-control w-100" type="text" name="EXC_TTDESCRIPTION_0" id="EXC_TTDESCRIPTION_0" maxlength="20" autocomplete="off" disabled >
                  </td>
                  <td>
                    <input  class="form-control two-digits rightalign" type="text" name="EXC_RATE_0" id="EXC_RATE_0" maxlength="6" autocomplete="off"  disabled >
                  </td>
                  <td style="text-align:center;" >
                    <input type="checkbox" name="EXC_DEACTIVATED_0" id="EXC_DEACTIVATED_0" class="filter-none"  value="0" disabled>
                  </td>
                  <td style="text-align:center;" >
                    <input type="date" name="EXC_DODEACTIVATED_0" id="EXC_DODEACTIVATED_0" class="form-control"  autocomplete="off" disabled>
                  </td>                 
                </tr>
              @endif  
              </tbody>
            </table>   
          </div>
        </div><!-- tab2-->
      </div><!-- tab-content -->
		</div><!-- row -->			
	</div><!-- container-fluid -->						
	</form>
  </div>
@endsection