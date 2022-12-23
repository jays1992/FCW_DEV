
@extends('layouts.app')
@section('content')

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[166,'index'])}}" class="btn singlebt">Withholding (TDS) Tax Master</a>
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
    <form id="frm_mst_edit"  method="POST">  
    @CSRF
            
        
          {{isset($objTdsResponse->HOLDINGID) ? method_field('PUT') : '' }}
            <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0">
                    <tr >
                    <th>Code<input class="form-control" type="hidden" name="Row_Count" id ="Row_Count"> </th>
                        <th width="50%">Description</th>
                        <th width="51%">Section</th>
                        <th width="16%">Assessee Type</th>
                        <th width="16%">Base Type</th>
                        <th width="16%">Applicable From</th>
                        <th width="16%">TDS Rate(%)</th>
                        <th width="16%">Tds Exemption Limit</th>
                        <th width="16%">Surcharge Rate %</th>
                        <th width="16%">Surcharge Exemption Limit</th>
                        <th width="16%">Cess Rate %</th>
                        <th width="16%">Cess Exemption Limit</th>
                        <th width="16%">Special Cess Rate(%)</th>
                        <th width="16%">Special Cess Exemption Limit</th>
                        <th width="16%">TDS GL</th>
                        <th width="16%">Surcharge GL</th>
                        <th width="16%">Cess GL</th>
                        <th width="16%">Special Cess GL</th>
                        <th width="16%">Return Type</th>
                        <th width="16%">De-Activated</th>
                        <th width="16%">Date of De-Activated</th>
                        <th width="3%">Action</th>
                    </tr>
                </thead>
                <tbody>
                @if(!empty($objTdsResponse))
                
                @php $n=1; @endphp
                @foreach($objTdsResponse as $key => $row) 
    
                    <tr  class="participantRow">
                        <td hidden>
                        <input  class="form-control" type="hidden"  readonly  name={{"HOLDINGID_".$key}} id ={{"txtID_".$key}} maxlength="100" value="{{ $row->HOLDINGID }}" autocomplete="off"   >
                        </td>
                        <td>
                        <input  class="form-control" type="text" readonly  onkeypress="return AlphaNumaric(event,this)" name={{"CODE_".$key}} id ={{"txtcode_".$key}}  maxlength="100" value="{{ $row->CODE }}"    autocomplete="off" style="text-transform:uppercase; width:91px;" >
                        </td>

                        <td>
                        <input  class="form-control" type="text" readonly  name={{"CODE_DESC_".$key}} id ={{"code_desc_".$key}} maxlength="100" value="{{ $row->CODE_DESC }}" autocomplete="off" style="width:91px;"  > </td>
                        <td>
                        <input type="text" name={{"SECTIONID_POPUP_".$key}} id ={{"SECTIONID_POPUP_".$key}} onClick="get_section($(this).attr('id'))" class="form-control" value="{{ $row->SECTION_CODE}}" style="width:91px" readonly tabindex="1" />
                       
                      
                            
                       </td>
                       <td hidden> <input type="text" readonly  name={{"SECTIONID_REF_".$key}} id ={{"sectionid_ref_".$key}} value="{{ $row->SECTIONID_REF}}" /></td>
                       
                       <td>

        
                          
                          <select class="form-control " readonly  name={{"ASSESSEEID_REF_".$key}} id ={{"assesseeid_ref_".$key}}  style="width:100px;" >
 
                          @foreach ($objNatureOfAsseesseeList as $index=>$AsseecceeList)
                             
                <option value="{{ $AsseecceeList->NOAID }}" {{$row->ASSESSEEID_REF == $AsseecceeList->NOA_NAME ? 'selected' : ""}}  >{{ $AsseecceeList->NOA_NAME }}</option>

                              @endforeach
                          </select>
                          
                      </td>
                      <td>
                          
                          <select class="form-control " readonly  name={{"BASE_TYPE_".$key}} id ={{"base_type_".$key}}  style="width:100px;" >
                              <option value="" selected >Select</option>
                              <option value="Net" @if ($row->BASE_TYPE=='Net') selected @endif>Net</option>
                              <option value="Gross" @if ($row->BASE_TYPE=='Gross') selected @endif>Gross</option>
                       
                          </select>

                               
                      </td>
                      
                      <td style="text-align:center;" ><input type="date" readonly   name={{"APPLICABLE_FRDT_".$key}} id ={{"applicable_frdt_".$key}} value="{{$row->APPLICABLE_FRDT}}" class="form-control APPLICABLE_FRDT" placeholder="dd/mm/yyyy"  ></td>
                      <td><input  class="form-control" type="text" readonly  name={{"TDS_RATE_".$key}} id ={{"tds_rate_".$key}} value=" {{$row->TDS_RATE == '.0000' ? '0.0000' : $row->TDS_RATE}}"  maxlength="8" autocomplete="off" style="width:85px;"  ></td>
                      <td><input  class="form-control " type="text" readonly  name={{"TDS_EXEMP_LIMIT_".$key}} id ={{"tds_exemp_limit_".$key}} value="{{$row->TDS_EXEMP_LIMIT == '.00' ? '0.00' : $row->TDS_EXEMP_LIMIT}}"  maxlength="16" autocomplete="off"  ></td>
                      <td><input  class="form-control " type="text" readonly  name={{"SURCHARGE_RAGE_".$key}} id ={{"surcharge_rage_".$key}} value="{{$row->SURCHARGE_RAGE == '.0000' ? '0.0000' : $row->SURCHARGE_RAGE}}"  maxlength="8" autocomplete="off" style="width:85px;"  ></td>
                      <td><input  class="form-control " type="text" readonly  name={{"SURCHARGE_EXEMP_LIMIT_".$key}} id ={{"surcharge_exemp_limit_".$key}} value="{{$row->SURCHARGE_EXEMP_LIMIT == '.00' ? '0.00' : $row->SURCHARGE_EXEMP_LIMIT}}" maxlength="16" autocomplete="off"  ></td>
                      <td><input  class="form-control " type="text" readonly  name={{"CESS_RATE_".$key}} id ={{"cess_rate_".$key}} value="{{$row->CESS_RATE == '.0000' ? '0.0000' : $row->CESS_RATE}}" maxlength="8" autocomplete="off" style="width:85px;" ></td>
                      <td><input  class="form-control " type="text" readonly  name={{"CESS_EXEMP_LIMIT_".$key}} id ={{"cess_exemp_limit_".$key}} value="{{$row->CESS_EXEMP_LIMIT == '.00' ? '0.00' : $row->CESS_EXEMP_LIMIT}}" maxlength="16" autocomplete="off"  ></td>
                      <td><input  class="form-control " type="text" readonly  name={{"SP_CESS_RATE_".$key}} id ={{"sp_cess_rate_".$key}} value="{{$row->SP_CESS_RATE == '.0000' ? '0.0000' : $row->SP_CESS_RATE}}" maxlength="8" autocomplete="off" style="width:85px;"  ></td>
                      <td><input  class="form-control " type="text" readonly  name={{"SP_CESS_EXEMP_LIMIT_".$key}} id ={{"sp_cess_exemp_limit_".$key}} value="{{$row->SP_CESS_EXEMP_LIMIT == '.00' ? '0.00' : $row->SP_CESS_EXEMP_LIMIT}}" maxlength="16" autocomplete="off"  ></td>
                      <td>
                          
                          <select class="form-control "  readonly  name={{"TDS_GLID_REF_".$key}} id ={{"tds_glid_ref_".$key}} style="width:100px;" >
                              <option value="" selected >Select</option>
                              @foreach ($objGenralLedgerList as $index=>$GenralLedgerList)
                             
                             <option value="{{ $GenralLedgerList->GLID }}" {{$row->TDS_GLID_REF == $GenralLedgerList->GLID ? 'selected' : ""}}   >{{ $GenralLedgerList->GLNAME }}</option>

                             @endforeach
                          </select>
                          
                      </td>
                      <td>
                          
                          <select class="form-control " readonly  name={{"SURCHARGE_GLID_REF_".$key}} id ={{"surcharge_glid_ref_".$key}}  style="width:100px;" >
                              <option value="" selected >Select</option>
                              @foreach ($objGenralLedgerList as $index=>$GenralLedgerList)
                             
                             <option value="{{ $GenralLedgerList->GLID }}" {{$row->SURCHARGE_GLID_REF == $GenralLedgerList->GLID ? 'selected' : ""}} >{{ $GenralLedgerList->GLNAME }}</option>

                             @endforeach
                          </select>
                          
                      </td>
                      <td>
                          
                          <select class="form-control " readonly  name={{"CESS_GLID_REF_".$key}} id ={{"cess_glid_ref_".$key}} style="width:100px;" >
                              <option value="" selected >Select</option>
                              @foreach ($objGenralLedgerList as $index=>$GenralLedgerList)
                             
                             <option value="{{ $GenralLedgerList->GLID }}"  {{$row->CESS_GLID_REF == $GenralLedgerList->GLID ? 'selected' : ""}} >{{ $GenralLedgerList->GLNAME }}</option>

                             @endforeach
                          </select>
                          
                      </td>
                      <td>
                          
                          <select class="form-control " readonly    name={{"SP_CESS_GLID_REF_".$key}} id ={{"sp_cess_glid_ref_".$key}}   style="width:100px;">
                              <option value="" selected >Select</option>
                              @foreach ($objGenralLedgerList as $index=>$GenralLedgerList)
                             
                             <option value="{{ $GenralLedgerList->GLID }}"  {{$row->SP_CESS_GLID_REF == $GenralLedgerList->GLID ? 'selected' : ""}}  >{{ $GenralLedgerList->GLNAME }}</option>

                             @endforeach
                          </select>
                          
                      </td>
                      <td>
                          
                          <select class="form-control " readonly   name={{"RETURN_TYPE_".$key}} id ={{"return_type_".$key}} >
                              <option value="" >Select</option>
                              <option value="26" {{$row->RETURN_TYPE == '26' ? 'selected' : ""}}>26</option>
                              <option value="27" {{$row->RETURN_TYPE == '27' ? 'selected' : ""}}>27</option>
          
                          </select>
                          
                      </td>
                      
                <td style="text-align:center;" ><input disabled  type="checkbox" name={{"DEACTIVATED_".$key}}  id={{"deactive-checkbox_".$key}} {{$row->DEACTIVATED == 1 ? 'checked' : ''}}  ></td>
                        <td style="text-align:center;" >
                        <input type="date" readonly  name={{"DODEACTIVATED_".$key}} class="form-control" placeholder="dd/mm/yyyy" id={{"decativateddate_".$key}} value="{{ ($row->DODEACTIVATED)=='1900-01-01'?'':$row->DODEACTIVATED }}" ></td>                    
                        <td align="center" ><button class="btn add" disabled title="add" data-toggle="tooltip">
                        <i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip"  disabled >
                        <i class="fa fa-trash" ></i></button>
                        </td>
                    </tr>
                    <tr>
                    </tr> 
                    @php $n++; @endphp
                @endforeach 
                @endif                   
                          
                </tbody>
            </table>
        
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

.table-bordered.itemlist tr th {
    padding: 5px 5px;
    font-size: 13px;
    border: 1px solid#0f69cc !important;
    color: #0f69cc;
    background: #eff7fb;
    font-weight: 400;
    text-align: center;
    position: sticky;
    top: 0;
}

</style>
@endpush
@push('bottom-scripts')
<script>
 
//  var formUDFFORSEMst = $("#frm_mst_se");
//     formUDFFORSEMst.validate();
     
$(document).ready(function(e) {
    $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[166,"add"])}}';
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