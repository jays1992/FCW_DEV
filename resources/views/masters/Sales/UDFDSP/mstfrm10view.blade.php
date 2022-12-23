
@extends('layouts.app')
@section('content')

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[10,'index'])}}" class="btn singlebt">UDF for Daily Sales Plan</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>

                <!-- <div class="col-lg-2">
                    <form>
                        <div class="form-group">
                            <input type="text" name="" class="form-control" placeholder="Search">
                        </div>
                    </form>
                </div> -->
                <!--col-2-->
            </div><!--row-->
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->
    <div class="container-fluid purchase-order-view">
    <form id="frm_mst_se"  method="POST"   >    
    <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:480px;" >
    
    @CSRF
          {{isset($objUdfResponse->UDF_DSPID) ? method_field('PUT') : '' }}
            <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0">
                    <tr >
                        <th width="27%">Label</th>
                        <th width="16%">Value Type</th>
                        <th width="51%">Description</th>
                        <th width="16%">Is Mandatory</th>
                        <th width="16%">De-Activated</th>
                        <th width="16%">Date of De-Activated</th>
                        <th width="3%">Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($objUdfResponse as $key => $row)
                    <tr  class="participantRow">
                        <td hidden> <label>{{ $row->UDF_DSPID }}</label>
                        </td>
                        <td  style="text-align:center;" ><label style="text-transform: uppercase;">{{ $row->LABEL }}</label>
                        </td>                        
                        <td  style="text-align:center;" ><label>{{ $row->VALUETYPE }}</label>   
                        </td>
                        <td  style="text-align:center;" ><label>{{ $row->DESCRIPTIONS }}</label></td>
                        <td style="text-align:center;" ><input type="checkbox" name="Mandatory" id="chkmdtry"  {{$row->ISMANDATORY == 1 ? 'checked' : ''}} disabled ></td>
                        <td style="text-align:center;" ><input type="checkbox" name="DEACTIVATED"  id="deactive-checkbox"  {{$row->DEACTIVATED == 1 ? 'checked' : ''}} disabled></td>
                        <td style="text-align:center;" ><label>{{ ($row->DODEACTIVATED)=='1900-01-01'?'':date('d-m-Y',strtotime($row->DODEACTIVATED)) }}</label></td>
                        <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" disabled>
                        <i class="fa fa-plus"></i></button>
                        <!-- <button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled>
                        <i class="fa fa-trash" ></i></button> -->
                        <button class="btn remove" title="Delete" data-toggle="tooltip" disabled>
                        <i class="fa fa-trash" ></i></button>
                        </td>
                    </tr>
                @endforeach
                    <tr>
                    </tr>       
                </tbody>
            </table>
              
        </div>
        </form>
    </div><!--purchase-order-view-->

<!-- </div> -->

@endsection
@section('alert')
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog"   >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='closePopup' >&times;</button>
        <h4 class="modal-title">System Alert Message</h4>
      </div>
      <div class="modal-body">
	  <h5 id="AlertMessage" ></h5>
        <div class="btdiv">    
            <button class="btn alertbt" name='YesBtn' id="YesBtn"> <div id="alert-active"></div> Yes</button>
            <button class="btn alertbt" name='NoBtn' id="NoBtn" >No</button>
            <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">OK</button>
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- Alert -->
@endsection

@push('bottom-css')
<style>
#custom_dropdown, #udfforsemst_filter {
    display: inline-table;
    margin-left: 15px;
}
.dataTables_wrapper .row:nth-child(1) .col-sm-6:nth-child(2){text-align:right;}
#filtercolumn{color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    }



</style>
@endpush
@push('bottom-scripts')
<script>
 
//  var formUDFFORSEMst = $("#frm_mst_se");
//     formUDFFORSEMst.validate();
     
$(document).ready(function(e) {
    $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[10,"add"])}}';
                  window.location.href=viewURL;
    });
    $('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });
//delete row
    $(".remove").click(function() {
    var rowCount = $(this).closest('table').find('tbody').length;
    if (rowCount > 1) {
    $(this).closest('tbody').remove(); 
    } 
    rowCount --; 
    if (rowCount <= 1) { 
    $(document).find('.remove').prop('disabled', true);  
    }
    });

//add row
    $(".add").click(function() { 
    var table = $(this).closest('table');
    var lastRow = table.find('tbody').last();
    var newRow = lastRow.clone(true, true); 
    newRow.find('input, textarea, select').val('');
    newRow.find('.growTextarea').css('height','auto');
    newRow.insertAfter(lastRow);
    table.find('.remove').removeAttr("disabled");
    });


// growTextarea function: use for testing that the the javascript
// is also copied when row is cloned.  to confirm, 
// type several lines into Location, add a row, & repeat

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
//
function showError(pId,pVal){
    $("#"+pId+"").text(pVal);
    $("#"+pId+"").show();
}


</script>


@endpush