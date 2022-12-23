
@extends('layouts.app')
@section('content')
<!-- <form id="frm_mst_se" onsubmit="return validateForm()"  method="POST"  >     -->
 
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[94,'index'])}}" class="btn singlebt">Terms & Condition Template</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo" disabled="disabled" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled" {{($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                        
                </div>
            </div><!--row-->
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->
    <div class="container-fluid purchase-order-view filter">   
      
    <form id="frm_mst_condition"  method="POST">  
    @CSRF
          {{isset($objCondition->TNCID) ? method_field('PUT') : '' }}
                <div class="inner-form">
                    
                    <div class="row">
                      <div class="col-lg-2 pl"><p>TnC Template Code</p></div>
                      <div class="col-lg-2 pl">
                        <div class="col-lg-12 pl">
                        <label style="text-transform: uppercase;">{{ $objCondition->TNC_CODE }}</label>
                        </div>
                      </div>
                    </div>
                    
                    <div class="row">
                    <div class="col-lg-2 pl"><p>TnC Template Description</p></div>
                        <div class="col-lg-5 pl">
                            <label>{{ $objCondition->TNC_DESC }}</label>
                            
                        </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-2 pl"><p>For Sale</p></div>
                      <div class="col-lg-1 pl">
                        <input type="checkbox" name="FOR_SALE" id="chkforsale" tabindex="3" {{$objCondition->FOR_SALE == 1 ? 'checked' : ''}} disabled>
                      </div>
                      
                      <div class="col-lg-1">OR</div>
                      
                      <div class="col-lg-2 pl"><p>For Purchase</p></div>
                      <div class="col-lg-1 pl">
                        <input type="checkbox" name="FOR_PURCHASE" id="chkforpurchase" tabindex="4" {{$objCondition->FOR_PURCHASE == 1 ? 'checked' : ''}} disabled>
                      </div>
                    </div>	
                            
                    <div class="row">
                        <div class="col-lg-2 pl"><p>De-Activated</p></div>
                        <div class="col-lg-1 pl">
                            <input type="checkbox" name="DE_ACTIVATED" id="deactive"  tabindex="5" {{$objCondition->DEACTIVATED == 1 ? 'checked' : ''}} disabled>
                        </div>
                        
                        <div class="col-lg-2 pl col-md-offset-1"><p>Date of De-Activated</p></div>
                        <div class="col-lg-2 pl">
                        <div class="col-lg-8 pl">
                        <label>{{ ($objCondition->DODEACTIVATED)=='1900-01-01'?'':$objCondition->DODEACTIVATED }}</label>
                        </div>
                        </div>
                    </div>
                    
                    
                    <div class="row">
                        <div class="table-responsive table-wrapper-scroll-y " style="height:330px;margin-top:10px;">
                        <table id="example2" class="display nowrap table table-striped table-bordered itemlist dataTable" style="height:auto !important;">
                                <thead id="thead1"   style="position: sticky;top: 0; white-space:none;">
                                <tr>
                                  <th width="27%" style="vertical-align:middle; font-family: 'Roboto',sans-serif; font-size:11px; font-weight: 600;">TNC Name <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count">  </th>
                                  <th width="16%"  style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">Value Type</th>
                                  <th width="51%" style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">Description</th>
                                  <th width="16%" style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">Is Mandatory</th>
                                  <th width="16%"  style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;"> De-Activated</th>
                                  <th width="16%"  style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">Date of De-Activated</th>
                                  <th width="10%" style="vertical-align:middle; font-size:11px; font-family: 'Roboto',sans-serif;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($objConditiontemp))
                                    @foreach($objConditiontemp as $key => $row)
                                        <tr  class="participantRow">
                                            <td hidden>
                                            <input  class="form-control" type="hidden" name={{"TNCDID_".$key}} id ={{"txtID_".$key}} maxlength="100" value="{{ $row->TNCDID }}"    >
                                            </td>
                                            <td style="width:10%;"><label  style="text-transform: uppercase;">{{ $row->TNC_NAME }}</label></td>
                                            <td><label>{{ $row->VALUE_TYPE }}</label></td>
                                            <td style="width:51%;"><label>{{ $row->DESCRIPTIONS }}</label></td>              
                                            <td style="width:16%;">
                                            <input type="checkbox" name={{"IS_MANDATORY_".$key}} id={{"chkmdtry_".$key}}  {{$row->IS_MANDATORY == 1 ? 'checked' : ''}}  disabled></td>
                                            </td>
                                            <td  style="text-align:center; width:16%;" ><input type="checkbox" name={{"DEACTIVATED_".$key}}  id={{"deactive-checkbox_".$key}} {{$row->DEACTIVATED == 1 ? 'checked' : ''}} disabled></td>
                                            <td style="width:16%;"><label>{{ ($row->DODEACTIVATED)=='1900-01-01'?'':$row->DODEACTIVATED }}</label></td> 
                                            <td style="width:10%;"><button class="btn add" title="add" data-toggle="tooltip" disabled><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                        </tr>
                                        <tr></tr>
                                    @endforeach 
                                @endif 
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </form> 
</div><!--purchase-order-view-->
<!-- </form>    -->
<!-- </div> -->

@endsection
