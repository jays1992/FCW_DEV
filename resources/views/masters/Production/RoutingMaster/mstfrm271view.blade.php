@extends('layouts.app')
@section('content')
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                  <a href="{{route('master',[271,'index'])}}" class="btn singlebt">Routing Master</a>
                </div><!--col-2-->
                <div class="col-lg-10 topnav-pd">
                    <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                    <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                    <button class="btn topnavbt" id="btnSave" disabled="disabled"><i class="fa fa-save"></i> Save</button>
                    <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                    <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                    <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                    <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                    <button class="btn topnavbt" id="btnApprove" disabled="disabled" ><i class="fa fa-lock"></i> Approved</button>
                    <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                    <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div><!--row-->
    </div>

<form id="frm_mst" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >
    @csrf
    {{isset($objRouting->MWITEMID) ? method_field('PUT') : '' }}
    <div class="container-fluid filter">

<div class="inner-form">

  <div class="row">
    <div class="col-lg-2 pl"><p>FG / SFG Item</p></div>
    <div class="col-lg-2 pl">
      <input type="text" name="txtFGITEMPOP_popup" id="txtFGITEMPOP_popup" value="{{ $objItemNo->ICODE }}" class="form-control clsclear"  autocomplete="off"  readonly disabled />
      <input type="hidden" name="ITEMID_REF" id="hdnFGITEMPOPID" value="{{ $objItemNo->ITEMID }}" class="form-control clsclear" autocomplete="off" />
    </div>              
    <div class="col-lg-2 pl"><p>Description</p></div>
    <div class="col-lg-2 pl">
      <input type="text" name="ITEM_DESC" id="ITEM_DESC" value="{{ $objItemNo->NAME }}" class="form-control clsclear"  autocomplete="off" readonly />
    </div>
  </div>    

   {{-- deactive row --}}
   <div class="row">
    <div class="col-lg-2 pl"><p>De-Activated</p></div>
    <div class="col-lg-2 pl pr">
    <input type="checkbox"   name="HDR_DEACTIVATED"  id="deactive-checkbox_0" {{$objRouting->DEACTIVATED == 1 ? "checked" : ""}}
    value='{{$objRouting->DEACTIVATED == 1 ? 1 : 0}}'  disabled>
    </div>
    
    <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
    <div class="col-lg-2 pl">
      <input type="date" name="HDR_DODEACTIVATED" class="form-control" id="HDR_DODEACTIVATED" {{$objRouting->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objRouting->DODEACTIVATED) && $objRouting->DODEACTIVATED !="" && $objRouting->DODEACTIVATED !="1900-01-01" ? $objRouting->DODEACTIVATED:''}}" placeholder="dd/mm/yyyy" disabled />
    </div>
  </div>
{{-- deactive row end --}}


</div>

<div class="container-fluid">

  <div class="row">
    <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#Material">Material </a></li> 
    </ul>

    
    <div class="tab-content">
          <div id="Material" class="tab-pane fade in active">
              <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                  <table id="exp2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top:">
                      <tr >
                          <th width="5%" hidden>MWITEM_MATID</th>
                          <th width="10%">Production Stage<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
                          <th width="5%" hidden>PSTAGEID_REF</th>
                          <th width="25%">Stage Description</th>
                          <th width="10%">Inhouse Production</th>
                          <th width="10%">Job Worker</th>
                          <th width="10%">FG Stage</th>                
                          <th width="5%">Action</th>
                      </tr>
              </thead>
<tbody>
@if(!empty($objRoutingMAT))
@foreach($objRoutingMAT as $key => $row) 
<tr  class="participantRow">
  
  <td  hidden>
    <input type="text" style="width: 100px;" name="MWITEM_MATID_{{$key}}" value="{{$row->MWITEM_MATID}}" id="hdnMWITEM_MATID_{{$key}}" class="form-control"/> 
  </td> 
  <td style="text-align:center;" >
    <input type="text" name="PStage_popup_{{$key}}" id="txtPStage_popup_{{$key}}"   value="{{$row->PSTAGE_CODE}}" class="form-control mandatory" style="width:140px" readonly disabled />
  </td>
  <td  hidden>
    <input type="text" style="width: 100px;" name="PSTAGEID_REF_{{$key}}" value="{{$row->PSTAGEID_REF}}" id="hdnPSTAGEIDREF_{{$key}}" class="form-control"/> 
  </td> 
  <td >
    <input type="text" name="STAGE_DESC_{{$key}}" id="STAGE_DESC_{{$key}}" value="{{$row->DESCRIPTIONS}}" class="form-control" readonly style="width: 100%;" disabled /> 
  </td> 
  <td style="text-align: center;"><input type="checkbox" name="INHOUSE_PRODUCTION_{{$key}}" id="INHOUSE_PRODUCTION_{{$key}}" value="1" @if($row->INHOUSE_PRODUCTION==1) checked @endif class="filter-none"  style="float:none;" disabled ></td>
  <td style="text-align: center;"><input type="checkbox" name="JOB_WORKER_{{$key}}" id="JOB_WORKER_{{$key}}" value="1" @if($row->JOB_WORKER==1) checked @endif   class="filter-none" style="float:none;" style="float:none;" disabled ></td>
  <td style="text-align: center;"><input type="checkbox" name="FG_STAGE_{{$key}}" id="FG_STAGE_{{$key}}"   value="1" @if($row->FG_STAGE==1) checked @endif   class="filter-none"  style="float:none;" disabled  ></td>  
 
  <td align="center" ><button class="btn add material" title="add" data-toggle="tooltip" type="button" disabled  ><i class="fa fa-plus"></i></button>
  <button class="btn remove dmaterial"   title="Delete" data-toggle="tooltip" type="button" disabled ><i class="fa fa-trash" ></i></button></td>

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

</div>
<!-- </div> -->
</form>
@endsection
@section('alert')


@endsection


@push('bottom-css')
<style>


</style>
@endpush
@push('bottom-scripts')
<script>

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
});

 


</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

  var count1 = <?php echo json_encode($objCount1); ?>;
  $('#Row_Count1').val(count1);

});


 
</script>


@endpush