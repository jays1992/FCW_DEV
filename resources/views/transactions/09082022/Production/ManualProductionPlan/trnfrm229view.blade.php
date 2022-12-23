
@extends('layouts.app')
@section('content')
<!-- <form id="frm_trn_se" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >     -->

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                  <a href="{{route('transaction',[229,'index'])}}" class="btn singlebt">Manual Production Plan - <br>Machine / Shift / Day wise</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE"  disabled="disabled"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove"  disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div><!--row-->
    </div>

<form id="frm_trn_se" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >
    @csrf
    {{isset($objSE->MPPID) ? method_field('PUT') : '' }}
    <div class="container-fluid filter">

<div class="inner-form">

  <div class="row">
    <div class="col-lg-1 pl"><p>Doc No</p></div>
    <div class="col-lg-1 pl">
      <label> {{$objSE->MPP_DOC_NO}} </label>
      <input type="hidden" name="MPPID" id="MPPID" value="{{ $objSE->MPPID }}" />
      <input type="hidden" name="MPPDOCNO" id="MPPDOCNO" value="{{ $objSE->MPP_DOC_NO }}"   />
    </div>
    
    <div class="col-lg-1 pl col-md-offset-1"><p>Date</p></div>
    <div class="col-lg-2 pl">
          <input type="hidden" name="MPP_DOC_DT" id="MPP_DOC_DT" value="{{ $objSE->MPP_DOC_DT }}" class="form-control mandatory "  >
          <input type="date" name="MPPDT" id="MPPDT" value="{{ $objSE->MPP_DOC_DT }}" class="form-control mandatory MPPDT"  placeholder="dd/mm/yyyy" disabled>
         
    </div>

    <div class="col-lg-1 pl"><p> Period	</p></div> 
			<div class="col-lg-2 pl">
        <input type="hidden" class="form-control" name="PERIOD_MTID_REF" id="PERIOD_MTID_REF" value="{{ $objSE->PERIOD_MTID_REF }}" >
        <select class="form-control" name="MONTH_DT" id="MONTH_DT" disabled>
          <option value="" selected>-- Please Select --</option>
          @foreach ($objMonths as $mrow)
            <option value="{{ $mrow->MTID }}" @if($mrow->MTID==$objSE->PERIOD_MTID_REF) selected @endif>{{ $mrow->MTDESCRIPTION }}</option>
          @endforeach
        </select>
        <input type="hidden" name="act_month_day" id="act_month_day" value="{{ $objSE->MONTH_DAYS }}" class="form-control" >
			</div>
			
  </div>

</div>

<div class="container-fluid">

  <div class="row">
    <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#Material">Material </a></li> 
    </ul>

    
    <div class="tab-content">
                              <div id="Material" class="tab-pane fade in active">
                                  <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                                      <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                                        <thead id="thead1"  style="position: sticky;top:">
                                          <tr hidden>
                                            <th colspan="5"></th> 
                                              <th colspan="95" id="monthlabel"></th>                                                       
                                          </tr>  
                                          <tr id="headingtr2">
                                            <th colspan="5"></th>                                                       
                                            <th colspan="3"  id="thday_1"> 1 - <span class="monthname"></span></th>                                                       
                                            <th colspan="3"  id="thday_2"> 2 - <span class="monthname"></span></th>                                                       
                                            <th colspan="3" id="thday_3"> 3  - <span class="monthname"></span></th>                                                       
                                            <th colspan="3" id="thday_4"> 4  - <span class="monthname"></span></th>                                                      
                                            <th colspan="3" id="thday_5"> 5  - <span class="monthname"></span></th>                                                        
                                            <th colspan="3" id="thday_6"> 6  - <span class="monthname"></span></th>                                                        
                                            <th colspan="3" id="thday_7"> 7  - <span class="monthname"></span></th>                                                       
                                            <th colspan="3" id="thday_8"> 8  - <span class="monthname"></span></th>                                                        
                                            <th colspan="3" id="thday_9"> 9  - <span class="monthname"></span></th>                                                        
                                            <th colspan="3" id="thday_10"> 10  - <span class="monthname"></span></th>    

                                            <th colspan="3" id="thday_11"> 11  - <span class="monthname"></span></th>                                                        
                                            <th colspan="3" id="thday_12"> 12  - <span class="monthname"></span></th>     
                                            <th colspan="3" id="thday_13"> 13  - <span class="monthname"></span></th>                                                        
                                            <th colspan="3" id="thday_14"> 14  - <span class="monthname"></span></th>                                                         
                                            <th colspan="3" id="thday_15"> 15  - <span class="monthname"></span></th>                                                         
                                            <th colspan="3" id="thday_16"> 16  - <span class="monthname"></span></th>                                                       
                                            <th colspan="3" id="thday_17"> 17  - <span class="monthname"></span></th>                                                       
                                            <th colspan="3" id="thday_18"> 18  - <span class="monthname"></span></th>                                                         
                                            <th colspan="3" id="thday_19"> 19  - <span class="monthname"></span></th>                                                        
                                            <th colspan="3" id="thday_20"> 20  - <span class="monthname"></span></th>    

                                            <th colspan="3" id="thday_21"> 21  - <span class="monthname"></span></th>                                                       
                                            <th colspan="3" id="thday_22"> 22  - <span class="monthname"></span></th>                                                      
                                            <th colspan="3" id="thday_23"> 23  - <span class="monthname"></span></th>                                                       
                                            <th colspan="3" id="thday_24"> 24  - <span class="monthname"></span></th>                                                       
                                            <th colspan="3" id="thday_25"> 25  - <span class="monthname"></span></th>                                                      
                                            <th colspan="3" id="thday_26"> 26  - <span class="monthname"></span></th>                                                      
                                            <th colspan="3" id="thday_27"> 27  - <span class="monthname"></span></th>                                                       
                                            <th colspan="3" id="thday_28"> 28  - <span class="monthname"></span></th>                                                       
                                            <th colspan="3" id="thday_29"> 29  - <span class="monthname"></span></th>                                                      
                                            <th colspan="3" id="thday_30"> 30  - <span class="monthname"></span></th> 

                                            <th colspan="3" id="thday_31"> 31  - <span class="monthname"></span></th>                                                      
                                            <th  ></th>                                                       
                                        </tr>  
                                          <tr id="headingtr3" >
                                              <th width="10%">Item Category	<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
                                              <th width="10%">Item Code</th>
                                              <th  width="15%">Item Name</th>
                                              <th>Main UOM</th>
                                              <th style="width:100px !important;">Production Plan Qty</th>
                                              
                                              <th id="head_mc_1">M/C</th>
                                              <th id="head_shift_1">Shift</th>
                                              <th id="head_qty_1">Qty</th>


                                              <th id="head_mc_2">M/C</th>
                                              <th id="head_shift_2">Shift</th>
                                              <th id="head_qty_2">Qty</th>

                                              
                                              <th id="head_mc_3">M/C</th>
                                              <th id="head_shift_3">Shift</th>
                                              <th id="head_qty_3">Qty</th>

                                              <th id="head_mc_4">M/C</th>
                                              <th id="head_shift_4">Shift</th>
                                              <th id="head_qty_4">Qty</th>
                                              
                                              <th id="head_mc_5">M/C</th>
                                              <th id="head_shift_5">Shift</th>
                                              <th id="head_qty_5">Qty</th>
                                              
                                              <th id="head_mc_6">M/C</th>
                                              <th id="head_shift_6">Shift</th>
                                              <th id="head_qty_6">Qty</th>

                                              <th id="head_mc_7">M/C</th>
                                              <th id="head_shift_7">Shift</th>
                                              <th id="head_qty_7">Qty</th>

                                              <th id="head_mc_8">M/C</th>
                                              <th id="head_shift_8">Shift</th>
                                              <th id="head_qty_8">Qty</th>

                                              <th id="head_mc_9">M/C</th>
                                              <th id="head_shift_9">Shift</th>
                                              <th id="head_qty_9">Qty</th>

                                              <th id="head_mc_10">M/C</th>
                                              <th id="head_shift_10">Shift</th>
                                              <th id="head_qty_10">Qty</th>

                                              <th id="head_mc_11">M/C</th>
                                              <th id="head_shift_11">Shift</th>
                                              <th id="head_qty_11">Qty</th>

                                              <th id="head_mc_12">M/C</th>
                                              <th id="head_shift_12">Shift</th>
                                              <th id="head_qty_12">Qty</th>

                                              <th id="head_mc_13">M/C</th>
                                              <th id="head_shift_13">Shift</th>
                                              <th id="head_qty_13">Qty</th>

                                              <th id="head_mc_14">M/C</th>
                                              <th id="head_shift_14">Shift</th>
                                              <th id="head_qty_14">Qty</th>

                                              <th id="head_mc_15">M/C</th>
                                              <th id="head_shift_15">Shift</th>
                                              <th id="head_qty_15">Qty</th>

                                              <th id="head_mc_16">M/C</th>
                                              <th id="head_shift_16">Shift</th>
                                              <th id="head_qty_16">Qty</th>

                                              <th id="head_mc_17">M/C</th>
                                              <th id="head_shift_17">Shift</th>
                                              <th id="head_qty_17">Qty</th>

                                              <th id="head_mc_18">M/C</th>
                                              <th id="head_shift_18">Shift</th>
                                              <th id="head_qty_18">Qty</th>

                                              <th id="head_mc_19">M/C</th>
                                              <th id="head_shift_19">Shift</th>
                                              <th id="head_qty_19">Qty</th>

                                              <th id="head_mc_20">M/C</th>
                                              <th id="head_shift_20">Shift</th>
                                              <th id="head_qty_20">Qty</th>

                                              <th id="head_mc_21">M/C</th>
                                              <th id="head_shift_21">Shift</th>
                                              <th id="head_qty_21">Qty</th>

                                              <th id="head_mc_22">M/C</th>
                                              <th id="head_shift_22">Shift</th>
                                              <th id="head_qty_22">Qty</th>

                                              <th id="head_mc_23">M/C</th>
                                              <th id="head_shift_23">Shift</th>
                                              <th id="head_qty_23">Qty</th>

                                              <th id="head_mc_24">M/C</th>
                                              <th id="head_shift_24">Shift</th>
                                              <th id="head_qty_24">Qty</th>

                                              <th id="head_mc_25">M/C</th>
                                              <th id="head_shift_25">Shift</th>
                                              <th id="head_qty_25">Qty</th>

                                              <th id="head_mc_26">M/C</th>
                                              <th id="head_shift_26">Shift</th>
                                              <th id="head_qty_26">Qty</th>

                                              <th id="head_mc_27">M/C</th>
                                              <th id="head_shift_27">Shift</th>
                                              <th id="head_qty_27">Qty</th>

                                              <th id="head_mc_28">M/C</th>
                                              <th id="head_shift_28">Shift</th>
                                              <th id="head_qty_28">Qty</th>

                                              <th id="head_mc_29">M/C</th>
                                              <th id="head_shift_29">Shift</th>
                                              <th id="head_qty_29">Qty</th>

                                              <th id="head_mc_30">M/C</th>
                                              <th id="head_shift_30">Shift</th>
                                              <th id="head_qty_30">Qty</th>

                                              <th id="head_mc_31">M/C</th>
                                              <th id="head_shift_31">Shift</th>
                                              <th id="head_qty_31">Qty</th>

                                              

                                              <th  width="6%">Action</th>
                                          </tr>
                                  </thead>
<tbody>
@if(!empty($objSEMAT))
@foreach($objSEMAT as $key => $row) 
<tr  class="participantRow">
  <td style="text-align:center;">
    <input type="text" name="BUID_REF_{{$key}}" id="BUID_REF_{{$key}}" value="{{ $row->ICCODE }}" disabled  onClick="get_section($(this).attr('id'))" class="form-control mandatory" style="width:140px" readonly tabindex="1" />
  </td>
  <td hidden> <input type="text" name="REF_BUID_{{$key}}" id="REF_BUID_{{$key}}"  value="{{ $row->ICATID_REF }}"  /> <input type="text" name="rowscount[]"  /></td> 

  <td><input type="text" name="popupITEMID_{{$key}}" id="popupITEMID_{{$key}}"   disabled     value="{{$row->ICODE}}"       class="form-control"  autocomplete="off" style="width:150px;"  readonly/></td>
  <td hidden><input type="hidden" name="ITEMID_REF_{{$key}}" id="ITEMID_REF_{{$key}}" value="{{$row->ITEMIDID_REF}}"  class="form-control" autocomplete="off" /></td>
  <td><input type="text" name="ItemName_{{$key}}" id="ItemName_{{$key}}"              value="{{$row->NAME}}"        class="form-control"  autocomplete="off"  readonly style="width:180px;" /></td>
  <td hidden><input type="hidden" name="itemuom_{{$key}}" id="itemuom_{{$key}}"       value="{{$row->UOMID_REF}}"   class="form-control"  autocomplete="off"  readonly/></td>
  <td><input type="text" name="itemmain_uom_{{$key}}" id="itemmain_uom_{{$key}}"      value="{{ $row->UOMCODE }} - {{ $row->DESCRIPTIONS }}"    class="form-control"  autocomplete="off" style="width:120px;" readonly/></td>
  <td><input type="text" name="ppqty_{{$key}}" id="ppqty_{{$key}}"            disabled        value="{{$row->PD_PLAN_QTY}}"  class="form-control three-digits" style="width:130px;" maxlength="13" autocomplete="off"  /></td>

  
  <td id="tdmachine_1"> 
    <input type="text" name="machine1_{{$key}}" id="machine1_txtMachine_popup_{{$key}}"   disabled   value="{{ $row->MACH_1_DESC }}"  disabled   readonly data-dayno="1"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_1" hidden><input type="text" name="machine1_MACH_REFID_{{$key}}"   disabled   value="{{ $row->MACH_1_ID }}"     id="machine1_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_1" >
      <input type="text" name="shift1_{{$key}}" id="shift1_txtLISTPOP_popup_{{$key}}"   disabled   value="{{ $row->SHFT_1_DESC }}"  disabled    readonly data-dayno="1" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_1" hidden> <input type="text" name="shift1_SHIFT_REFID_{{$key}}"   disabled   value="{{ $row->SHFT_1_ID }}"   id="shift1_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>
  <td id="tdday_qty_1"> <input type="text" name="dayqty1_{{$key}}"  disabled    id="dayqty1_{{$key}}"   disabled   data-dayno="1"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_1 }}" style="width:130px;" autocomplete="off"  /> </td>
  
  
  <td id="tdmachine_2"> 
    <input type="text" name="machine2_{{$key}}" id="machine2_txtMachine_popup_{{$key}}"   disabled  value="{{ $row->MACH_2_DESC }}"  disabled   readonly data-dayno="2"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_2" hidden><input type="text" name="machine2_MACH_REFID_{{$key}}"   disabled  value="{{ $row->MACH_2_ID }}" id="machine2_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_2" >
      <input type="text" name="shift2_{{$key}}" id="shift2_txtLISTPOP_popup_{{$key}}"   disabled  value="{{ $row->SHFT_2_DESC }}"  disabled   readonly data-dayno="2" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_2" hidden> <input type="text" name="shift2_SHIFT_REFID_{{$key}}"   disabled  value="{{ $row->SHFT_2_ID }}"   id="shift2_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>
  <td id="tdday_qty_2"><input type="text" name="dayqty2_{{$key}}"  disabled    id="dayqty2_{{$key}}"   disabled  data-dayno="2"   class="form-control three-digits" maxlength="13"  value="{{ $row->QTY_2 }}"  style="width:130px;" autocomplete="off"  /></td>
  
  
  <td id="tdmachine_3"> 
    <input type="text" name="machine3_{{$key}}" id="machine3_txtMachine_popup_{{$key}}"    disabled   value="{{ $row->MACH_3_DESC }}"  disabled    readonly data-dayno="3"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_3" hidden><input type="text" name="machine3_MACH_REFID_{{$key}}"   value="{{ $row->MACH_3_ID }}"   id="machine3_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_3" >
      <input type="text" name="shift3_{{$key}}" id="shift3_txtLISTPOP_popup_{{$key}}"   disabled  value="{{ $row->SHFT_3_DESC }}"  disabled   readonly data-dayno="3" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_3" hidden> <input type="text" name="shift3_SHIFT_REFID_{{$key}}" value="{{ $row->SHFT_3_ID }}" id="shift3_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>    
  <td id="tdday_qty_3"><input type="text" name="dayqty3_{{$key}}"  disabled    id="dayqty3_{{$key}}"   disabled  data-dayno="3"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_3 }}"  style="width:130px;" autocomplete="off"  /></td>
  
  
  <td id="tdmachine_4"> 
    <input type="text" name="machine4_{{$key}}" id="machine4_txtMachine_popup_{{$key}}"  disabled   value="{{ $row->MACH_4_DESC }}"  disabled   readonly data-dayno="4"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_4" hidden><input type="text" name="machine4_MACH_REFID_{{$key}}" value="{{ $row->MACH_4_ID }}"   id="machine4_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_4" >
      <input type="text" name="shift4_{{$key}}" id="shift4_txtLISTPOP_popup_{{$key}}"   disabled  value="{{ $row->SHFT_4_DESC }}"  disabled   readonly data-dayno="4" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_4" hidden> <input type="text" name="shift4_SHIFT_REFID_{{$key}}"  value="{{ $row->SHFT_4_ID }}"  id="shift4_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>    
  <td id="tdday_qty_4"><input type="text" name="dayqty4_{{$key}}"  disabled    id="dayqty4_{{$key}}"   disabled  data-dayno="4"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_4 }}" style="width:130px;" autocomplete="off"  /></td>
  
  
  <td id="tdmachine_5"> 
    <input type="text" name="machine5_{{$key}}" id="machine5_txtMachine_popup_{{$key}}"   disabled  value="{{ $row->MACH_5_DESC }}"  disabled    readonly data-dayno="5"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_5" hidden><input type="text" name="machine5_MACH_REFID_{{$key}}"  value="{{ $row->MACH_5_ID }}"  id="machine5_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_5" >
      <input type="text" name="shift5_{{$key}}" id="shift5_txtLISTPOP_popup_{{$key}}"   disabled  value="{{ $row->SHFT_5_DESC }}"  disabled  readonly data-dayno="5" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_5" hidden> <input type="text" name="shift5_SHIFT_REFID_{{$key}}"  value="{{ $row->SHFT_5_ID }}"  id="shift5_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>    
  <td id="tdday_qty_5"><input type="text" name="dayqty5_{{$key}}"  disabled    id="dayqty5_{{$key}}"   disabled  data-dayno="5"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_5 }}" style="width:130px;" autocomplete="off"  /></td>
  
 
  <td id="tdmachine_6"> 
    <input type="text" name="machine6_{{$key}}" id="machine6_txtMachine_popup_{{$key}}"   disabled  value="{{ $row->MACH_6_DESC }}"  disabled   readonly data-dayno="6"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_6" hidden><input type="text" name="machine6_MACH_REFID_{{$key}}"  value="{{ $row->MACH_6_ID }}" id="machine6_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_6" >
      <input type="text" name="shift6_{{$key}}" id="shift6_txtLISTPOP_popup_{{$key}}"    disabled  value="{{ $row->SHFT_6_DESC }}"  disabled  readonly data-dayno="6" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_6" hidden> <input type="text" name="shift6_SHIFT_REFID_{{$key}}"  value="{{ $row->SHFT_6_ID }}"  id="shift6_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_6"><input type="text" name="dayqty6_{{$key}}"  disabled    id="dayqty6_{{$key}}"   disabled  data-dayno="6"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_6 }}"  style="width:130px;" autocomplete="off"  /></td>
  
  
  <td id="tdmachine_7"> 
    <input type="text" name="machine7_{{$key}}" id="machine7_txtMachine_popup_{{$key}}"    disabled  value="{{ $row->MACH_7_DESC }}"  disabled  readonly data-dayno="7"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_7" hidden><input type="text" name="machine7_MACH_REFID_{{$key}}"  value="{{ $row->MACH_7_ID }}" id="machine7_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_7" >
      <input type="text" name="shift7_{{$key}}" id="shift7_txtLISTPOP_popup_{{$key}}"     disabled  value="{{ $row->SHFT_7_DESC }}"  disabled  readonly data-dayno="7" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_7" hidden> <input type="text" name="shift7_SHIFT_REFID_{{$key}}"     disabled  value="{{ $row->SHFT_7_ID }}"   id="shift7_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_7"><input type="text" name="dayqty7_{{$key}}"  disabled    id="dayqty7_{{$key}}"  data-dayno="7"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_7 }}"  style="width:130px;" autocomplete="off"  /></td>
  
  
  <td id="tdmachine_8"> 
    <input type="text" name="machine8_{{$key}}" id="machine8_txtMachine_popup_{{$key}}"   disabled   value="{{ $row->MACH_8_DESC }}"  disabled   readonly data-dayno="8"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_8" hidden><input type="text" name="machine8_MACH_REFID_{{$key}}"   disabled   value="{{ $row->MACH_8_ID }}"  id="machine8_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_8" >
      <input type="text" name="shift8_{{$key}}" id="shift8_txtLISTPOP_popup_{{$key}}"    disabled   value="{{ $row->SHFT_8_DESC }}"  disabled  readonly data-dayno="8" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_8" hidden> <input type="text" name="shift8_SHIFT_REFID_{{$key}}"  value="{{ $row->SHFT_8_ID }}"   id="shift8_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_8"><input type="text" name="dayqty8_{{$key}}"  disabled    id="dayqty8_{{$key}}"   disabled  data-dayno="8"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_8 }}"  style="width:130px;" autocomplete="off"  /></td>
  
  
  <td id="tdmachine_9"> 
    <input type="text" name="machine9_{{$key}}" id="machine9_txtMachine_popup_{{$key}}"   disabled   value="{{ $row->MACH_9_DESC }}"  disabled  readonly data-dayno="9"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_9" hidden><input type="text" name="machine9_MACH_REFID_{{$key}}"  value="{{ $row->MACH_9_ID }}"    id="machine9_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_9" >
      <input type="text" name="shift9_{{$key}}" id="shift9_txtLISTPOP_popup_{{$key}}"    disabled   value="{{ $row->SHFT_9_DESC }}"  disabled    readonly data-dayno="9" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_9" hidden> <input type="text" name="shift9_SHIFT_REFID_{{$key}}" value="{{ $row->SHFT_9_ID }}"   id="shift9_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_9"><input type="text" name="dayqty9_{{$key}}"  disabled    id="dayqty9_{{$key}}"   disabled  data-dayno="9"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_9 }}"  style="width:130px;" autocomplete="off"  /></td>
  
  
  <td id="tdmachine_10"> 
    <input type="text" name="machine10_{{$key}}" id="machine10_txtMachine_popup_{{$key}}"   disabled   value="{{ $row->MACH_10_DESC }}"  disabled   readonly data-dayno="10"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_10" hidden><input type="text" name="machine10_MACH_REFID_{{$key}}"   value="{{ $row->MACH_10_ID }}" id="machine10_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_10" >
      <input type="text" name="shift10_{{$key}}" id="shift10_txtLISTPOP_popup_{{$key}}"    disabled   value="{{ $row->SHFT_10_DESC }}"  disabled    readonly data-dayno="10" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_10" hidden> <input type="text" name="shift10_SHIFT_REFID_{{$key}}"  value="{{ $row->SHFT_10_ID }}"    id="shift10_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_10"><input type="text" name="dayqty10_{{$key}}"  disabled    id="dayqty10_{{$key}}"   disabled   data-dayno="10"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_10 }}"  style="width:130px;" autocomplete="off"  /></td>
  

  
  <td id="tdmachine_11"> 
    <input type="text" name="machine11_{{$key}}" id="machine11_txtMachine_popup_{{$key}}"   disabled   value="{{ $row->MACH_11_DESC }}"  disabled     readonly data-dayno="11"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_11" hidden><input type="text" name="machine11_MACH_REFID_{{$key}}"   value="{{ $row->MACH_11_ID }}"  id="machine11_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_11" >
      <input type="text" name="shift11_{{$key}}" id="shift11_txtLISTPOP_popup_{{$key}}"   disabled   value="{{ $row->SHFT_11_DESC }}"  disabled     readonly data-dayno="11" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_11" hidden> <input type="text" name="shift11_SHIFT_REFID_{{$key}}"  value="{{ $row->SHFT_11_ID }}"   id="shift11_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_11"><input type="text" name="dayqty11_{{$key}}"  disabled    id="dayqty11_{{$key}}"   disabled  data-dayno="11"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_11 }}" style="width:130px;" autocomplete="off"  /></td>


  <td id="tdmachine_12"> 
    <input type="text" name="machine12_{{$key}}" id="machine12_txtMachine_popup_{{$key}}"   disabled   value="{{ $row->MACH_12_DESC }}"  disabled     readonly data-dayno="12"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_12" hidden><input type="text" name="machine12_MACH_REFID_{{$key}}"   value="{{ $row->MACH_12_ID }}"  id="machine12_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_12" >
      <input type="text" name="shift12_{{$key}}" id="shift12_txtLISTPOP_popup_{{$key}}"   disabled    value="{{ $row->SHFT_12_DESC }}"  disabled   readonly data-dayno="12" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_12" hidden> <input type="text" name="shift12_SHIFT_REFID_{{$key}}"  value="{{ $row->SHFT_12_ID }}"  id="shift12_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_12"><input type="text"  name="dayqty12_{{$key}}"   disabled    id="dayqty12_{{$key}}"   disabled   data-dayno="12"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_12 }}"  style="width:130px;" autocomplete="off"  /></td>
  
  <td id="tdmachine_13"> 
    <input type="text" name="machine13_{{$key}}" id="machine13_txtMachine_popup_{{$key}}"   disabled  value="{{ $row->MACH_13_DESC }}"  disabled   readonly data-dayno="13"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_13" hidden><input type="text" name="machine13_MACH_REFID_{{$key}}"  value="{{ $row->MACH_13_ID }}"   id="machine13_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_13" >
      <input type="text" name="shift13_{{$key}}" id="shift13_txtLISTPOP_popup_{{$key}}"   disabled  value="{{ $row->SHFT_13_DESC }}"  disabled   readonly data-dayno="13" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_13" hidden> <input type="text" name="shift13_SHIFT_REFID_{{$key}}"  value="{{ $row->SHFT_13_ID }}" id="shift13_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_13"><input type="text"  name="dayqty13_{{$key}}"   disabled    id="dayqty13_{{$key}}"   disabled   data-dayno="13"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_13 }}" style="width:130px;" autocomplete="off"  /></td>

  
  <td id="tdmachine_14"> 
    <input type="text" name="machine14_{{$key}}" id="machine14_txtMachine_popup_{{$key}}"   disabled   value="{{ $row->MACH_14_DESC }}"  disabled   readonly data-dayno="14"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_14" hidden><input type="text" name="machine14_MACH_REFID_{{$key}}"  value="{{ $row->MACH_14_ID }}"  id="machine14_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_14" >
      <input type="text" name="shift14_{{$key}}" id="shift14_txtLISTPOP_popup_{{$key}}"   disabled   value="{{ $row->SHFT_14_DESC }}"  disabled   readonly data-dayno="14" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_14" hidden> <input type="text" name="shift14_SHIFT_REFID_{{$key}}"  value="{{ $row->SHFT_14_ID }}" id="shift14_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_14"><input type="text"  name="dayqty14_{{$key}}"   disabled    id="dayqty14_{{$key}}"   disabled   data-dayno="14"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_14 }}" style="width:130px;" autocomplete="off"  /></td>
  


  <td id="tdmachine_15"> 
    <input type="text" name="machine15_{{$key}}" id="machine15_txtMachine_popup_{{$key}}" value="{{ $row->MACH_15_DESC }}"  disabled   readonly data-dayno="15"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_15" hidden><input type="text" name="machine15_MACH_REFID_{{$key}}"  value="{{ $row->MACH_15_ID }}"  id="machine15_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_15" >
      <input type="text" name="shift15_{{$key}}" id="shift15_txtLISTPOP_popup_{{$key}}"  value="{{ $row->SHFT_15_DESC }}"  disabled  readonly data-dayno="15" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_15" hidden> <input type="text" name="shift15_SHIFT_REFID_{{$key}}"  value="{{ $row->SHFT_15_ID }}"   id="shift15_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_15"><input type="text"  name="dayqty15_{{$key}}"   disabled     disabled    id="dayqty15_{{$key}}"  data-dayno="15"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_15 }}" style="width:130px;" autocomplete="off"  /></td>
  

  <td id="tdmachine_16"> 
    <input type="text" name="machine16_{{$key}}" id="machine16_txtMachine_popup_{{$key}}"  value="{{ $row->MACH_16_DESC }}"  disabled  readonly data-dayno="16"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_16" hidden><input type="text" name="machine16_MACH_REFID_{{$key}}"  value="{{ $row->MACH_16_ID }}"  id="machine16_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_16" >
      <input type="text" name="shift16_{{$key}}" id="shift16_txtLISTPOP_popup_{{$key}}"  value="{{ $row->SHFT_16_DESC }}"  disabled  readonly data-dayno="16" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_16" hidden> <input type="text" name="shift16_SHIFT_REFID_{{$key}}"  value="{{ $row->SHFT_16_ID }}"   id="shift16_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_16"><input type="text"  name="dayqty16_{{$key}}"   disabled     disabled    id="dayqty16_{{$key}}"  data-dayno="16"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_16 }}" style="width:130px;" autocomplete="off"  /></td>
  
  

  <td id="tdmachine_17"> 
    <input type="text" name="machine17_{{$key}}" id="machine17_txtMachine_popup_{{$key}}"  value="{{ $row->MACH_17_DESC }}"  disabled   readonly data-dayno="17"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_17" hidden><input type="text" name="machine17_MACH_REFID_{{$key}}"  value="{{ $row->MACH_17_ID }}"  id="machine17_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_17" >
      <input type="text" name="shift17_{{$key}}" id="shift17_txtLISTPOP_popup_{{$key}}"  value="{{ $row->SHFT_17_DESC }}"  disabled    readonly data-dayno="17" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_17" hidden> <input type="text" name="shift17_SHIFT_REFID_{{$key}}"   value="{{ $row->SHFT_17_ID }}"  id="shift17_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_17"><input type="text"  name="dayqty17_{{$key}}"   disabled    id="dayqty17_{{$key}}"  data-dayno="17"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_17 }}" style="width:130px;" autocomplete="off"  /></td>
  



  <td id="tdmachine_18"> 
    <input type="text" name="machine18_{{$key}}" id="machine18_txtMachine_popup_{{$key}}"   value="{{ $row->MACH_18_DESC }}"  disabled    readonly data-dayno="18"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_18" hidden><input type="text" name="machine18_MACH_REFID_{{$key}}"  value="{{ $row->MACH_18_ID }}"   id="machine18_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_18" >
      <input type="text" name="shift18_{{$key}}" id="shift18_txtLISTPOP_popup_{{$key}}" value="{{ $row->SHFT_18_DESC }}"  disabled   readonly data-dayno="18" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_18" hidden> <input type="text" name="shift18_SHIFT_REFID_{{$key}}"   value="{{ $row->SHFT_18_ID }}"  id="shift18_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_18"><input type="text"  name="dayqty18_{{$key}}"   disabled    id="dayqty18_{{$key}}"  data-dayno="18"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_18 }}" style="width:130px;" autocomplete="off"  /></td>
  
  

  <td id="tdmachine_19"> 
    <input type="text" name="machine19_{{$key}}" id="machine19_txtMachine_popup_{{$key}}" value="{{ $row->MACH_19_DESC }}"  disabled    readonly data-dayno="19"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_19" hidden><input type="text" name="machine19_MACH_REFID_{{$key}}" value="{{ $row->MACH_19_ID }}" id="machine19_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_19" >
      <input type="text" name="shift19_{{$key}}" id="shift19_txtLISTPOP_popup_{{$key}}" value="{{ $row->SHFT_19_DESC }}"  disabled   readonly data-dayno="19" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_19" hidden> <input type="text" name="shift19_SHIFT_REFID_{{$key}}"   value="{{ $row->SHFT_19_ID }}"  id="shift19_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_19"><input type="text"  name="dayqty19_{{$key}}"   disabled    id="dayqty19_{{$key}}"  data-dayno="19"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_19 }}" style="width:130px;" autocomplete="off"  /></td>
  
  
  <td id="tdmachine_20"> 
    <input type="text" name="machine20_{{$key}}" id="machine20_txtMachine_popup_{{$key}}"  value="{{ $row->MACH_20_DESC }}"  disabled   readonly data-dayno="20"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_20" hidden><input type="text" name="machine20_MACH_REFID_{{$key}}"  value="{{ $row->MACH_20_ID }}"  id="machine20_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_20" >
      <input type="text" name="shift20_{{$key}}" id="shift20_txtLISTPOP_popup_{{$key}}" value="{{ $row->SHFT_20_DESC }}"  disabled    readonly data-dayno="20" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_20" hidden> <input type="text" name="shift20_SHIFT_REFID_{{$key}}"   value="{{ $row->SHFT_20_ID }}" id="shift20_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_20"><input type="text"  name="dayqty20_{{$key}}"   disabled    id="dayqty20_{{$key}}"  data-dayno="20"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_20 }}" style="width:130px;" autocomplete="off"  /></td>
  


  <td id="tdmachine_21"> 
    <input type="text" name="machine21_{{$key}}" id="machine21_txtMachine_popup_{{$key}}"  value="{{ $row->MACH_21_DESC }}"  disabled   readonly data-dayno="21"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_21" hidden><input type="text" name="machine21_MACH_REFID_{{$key}}"  value="{{ $row->MACH_21_ID }}"  id="machine21_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_21" >
      <input type="text" name="shift21_{{$key}}" id="shift21_txtLISTPOP_popup_{{$key}}" value="{{ $row->SHFT_21_DESC }}"  disabled   readonly data-dayno="21" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_21" hidden> <input type="text" name="shift21_SHIFT_REFID_{{$key}}" value="{{ $row->SHFT_21_ID }}"  id="shift21_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_21"><input type="text"  name="dayqty21_{{$key}}"   disabled    id="dayqty21_{{$key}}"  data-dayno="21"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_21 }}" style="width:130px;" autocomplete="off"  /></td>
  


  <td id="tdmachine_22"> 
    <input type="text" name="machine22_{{$key}}" id="machine22_txtMachine_popup_{{$key}}" value="{{ $row->MACH_22_DESC }}"  disabled    readonly data-dayno="22"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_22" hidden><input type="text" name="machine22_MACH_REFID_{{$key}}"  value="{{ $row->MACH_22_ID }}"   id="machine22_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_22" >
      <input type="text" name="shift22_{{$key}}" id="shift22_txtLISTPOP_popup_{{$key}}" value="{{ $row->SHFT_22_DESC }}"  disabled   readonly data-dayno="22" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_22" hidden> <input type="text" name="shift22_SHIFT_REFID_{{$key}}" value="{{ $row->SHFT_22_ID }}"  id="shift22_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_22"><input type="text"  name="dayqty22_{{$key}}"   disabled    id="dayqty22_{{$key}}"  data-dayno="22"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_22 }}" style="width:130px;" autocomplete="off"  /></td>
  
    
  <td id="tdmachine_23"> 
    <input type="text" name="machine23_{{$key}}" id="machine23_txtMachine_popup_{{$key}}" value="{{ $row->MACH_23_DESC }}"  disabled  readonly data-dayno="23"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_23" hidden><input type="text" name="machine23_MACH_REFID_{{$key}}"  value="{{ $row->MACH_23_ID }}" id="machine23_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_23" >
      <input type="text" name="shift23_{{$key}}" id="shift23_txtLISTPOP_popup_{{$key}}"  value="{{ $row->SHFT_23_DESC }}"  disabled  readonly data-dayno="23" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_23" hidden> <input type="text" name="shift23_SHIFT_REFID_{{$key}}" value="{{ $row->SHFT_23_ID }}"   id="shift23_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_23"><input type="text"  name="dayqty23_{{$key}}"   disabled    id="dayqty23_{{$key}}"  data-dayno="23"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_23 }}" style="width:130px;" autocomplete="off"  /></td>
  
    

  <td id="tdmachine_24"> 
    <input type="text" name="machine24_{{$key}}" id="machine24_txtMachine_popup_{{$key}}" value="{{ $row->MACH_24_DESC }}"  disabled    readonly data-dayno="24"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_24" hidden><input type="text" name="machine24_MACH_REFID_{{$key}}"  value="{{ $row->MACH_24_ID }}"  id="machine24_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_24" >
      <input type="text" name="shift24_{{$key}}" id="shift24_txtLISTPOP_popup_{{$key}}"  value="{{ $row->SHFT_24_DESC }}"  disabled   readonly data-dayno="24" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_24" hidden> <input type="text" name="shift24_SHIFT_REFID_{{$key}}" value="{{ $row->SHFT_24_ID }}"  id="shift24_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_24"><input type="text"  name="dayqty24_{{$key}}"   disabled    id="dayqty24_{{$key}}"  data-dayno="24"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_24 }}" style="width:130px;" autocomplete="off"  /></td>
  

    
  <td id="tdmachine_25"> 
    <input type="text" name="machine25_{{$key}}" id="machine25_txtMachine_popup_{{$key}}"  value="{{ $row->MACH_25_DESC }}"  disabled     readonly data-dayno="25"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_25" hidden><input type="text" name="machine25_MACH_REFID_{{$key}}"  value="{{ $row->MACH_25_ID }}"  id="machine25_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_25" >
      <input type="text" name="shift25_{{$key}}" id="shift25_txtLISTPOP_popup_{{$key}}"  value="{{ $row->SHFT_25_DESC }}"  disabled   readonly data-dayno="25" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_25" hidden> <input type="text" name="shift25_SHIFT_REFID_{{$key}}"  value="{{ $row->SHFT_25_ID }}"  id="shift25_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_25"><input type="text"  name="dayqty25_{{$key}}"   disabled    id="dayqty25_{{$key}}"  data-dayno="25"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_25 }}" style="width:130px;" autocomplete="off"  /></td>
  
    
  <td id="tdmachine_26"> 
    <input type="text" name="machine26_{{$key}}" id="machine26_txtMachine_popup_{{$key}}"  value="{{ $row->MACH_26_DESC }}"  disabled   readonly data-dayno="26"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_26" hidden><input type="text" name="machine26_MACH_REFID_{{$key}}"  value="{{ $row->MACH_26_ID }}" id="machine26_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_26" >
      <input type="text" name="shift26_{{$key}}" id="shift26_txtLISTPOP_popup_{{$key}}"  value="{{ $row->SHFT_26_DESC }}"  disabled   readonly data-dayno="26" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_26" hidden> <input type="text" name="shift26_SHIFT_REFID_{{$key}}"  value="{{ $row->SHFT_26_ID }}"   id="shift26_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_26"><input type="text"  name="dayqty26_{{$key}}"   disabled    id="dayqty26_{{$key}}"  data-dayno="26"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_26 }}" style="width:130px;" autocomplete="off"  /></td>
  
    
  <td id="tdmachine_27"> 
    <input type="text" name="machine27_{{$key}}" id="machine27_txtMachine_popup_{{$key}}"   value="{{ $row->MACH_27_DESC }}"  disabled   readonly data-dayno="27"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_27" hidden><input type="text" name="machine27_MACH_REFID_{{$key}}"   value="{{ $row->MACH_27_ID }}"  id="machine27_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_27" >
      <input type="text" name="shift27_{{$key}}" id="shift27_txtLISTPOP_popup_{{$key}}"   value="{{ $row->SHFT_27_DESC }}"  disabled   readonly data-dayno="27" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_27" hidden> <input type="text" name="shift27_SHIFT_REFID_{{$key}}"   value="{{ $row->SHFT_27_ID }}"   id="shift27_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_27"><input type="text"  name="dayqty27_{{$key}}"   disabled    id="dayqty27_{{$key}}"  data-dayno="27"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_27 }}" style="width:130px;" autocomplete="off"  /></td>
  
  
  <td id="tdmachine_28"> 
    <input type="text" name="machine28_{{$key}}" id="machine28_txtMachine_popup_{{$key}}"   value="{{ $row->MACH_28_DESC }}"  disabled   readonly data-dayno="28"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_28" hidden><input type="text" name="machine28_MACH_REFID_{{$key}}"  value="{{ $row->MACH_28_ID }}"  id="machine28_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_28" >
      <input type="text" name="shift28_{{$key}}" id="shift28_txtLISTPOP_popup_{{$key}}"   value="{{ $row->SHFT_28_DESC }}"  disabled    readonly data-dayno="28" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_28" hidden> <input type="text" name="shift28_SHIFT_REFID_{{$key}}"   value="{{ $row->SHFT_28_ID }}"   id="shift28_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_28"><input type="text"  name="dayqty28_{{$key}}"   disabled    id="dayqty28_{{$key}}"  data-dayno="28"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_28 }}" style="width:130px;" autocomplete="off"  /></td>
  
    
  
  <td id="tdmachine_29"> 
    <input type="text" name="machine29_{{$key}}" id="machine29_txtMachine_popup_{{$key}}"   value="{{ $row->MACH_29_DESC }}"  disabled    readonly data-dayno="29"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_29" hidden><input type="text" name="machine29_MACH_REFID_{{$key}}"  value="{{ $row->MACH_29_ID }}"  id="machine29_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_29" >
      <input type="text" name="shift29_{{$key}}" id="shift29_txtLISTPOP_popup_{{$key}}"  value="{{ $row->SHFT_29_DESC }}"  disabled    readonly data-dayno="29" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_29" hidden> <input type="text" name="shift29_SHIFT_REFID_{{$key}}"   value="{{ $row->SHFT_29_ID }}"    id="shift29_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_29"><input type="text"  name="dayqty29_{{$key}}"   disabled    id="dayqty29_{{$key}}"  data-dayno="29"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_29 }}" style="width:130px;" autocomplete="off"  /></td>
  
    
  <td id="tdmachine_30"> 
    <input type="text" name="machine30_{{$key}}" id="machine30_txtMachine_popup_{{$key}}"   value="{{ $row->MACH_30_DESC }}"  disabled    readonly data-dayno="30"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_30" hidden><input type="text" name="machine30_MACH_REFID_{{$key}}"  value="{{ $row->MACH_30_ID }}"  id="machine30_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_30" >
      <input type="text" name="shift30_{{$key}}" id="shift30_txtLISTPOP_popup_{{$key}}"  value="{{ $row->SHFT_30_DESC }}"  disabled    readonly data-dayno="30" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_30" hidden> <input type="text" name="shift30_SHIFT_REFID_{{$key}}"   value="{{ $row->SHFT_30_ID }}"   id="shift30_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_30"><input type="text"  name="dayqty30_{{$key}}"   disabled    id="dayqty30_{{$key}}"  data-dayno="30"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_30 }}" style="width:130px;" autocomplete="off"  /></td>
  
    
  <td id="tdmachine_31"> 
    <input type="text" name="machine31_{{$key}}" id="machine31_txtMachine_popup_{{$key}}"   value="{{ $row->MACH_31_DESC }}"  disabled    readonly data-dayno="31"  class="form-control"  style="width:140px;"  />
  </td>
  <td id="tdhdnmachine_31" hidden><input type="text" name="machine31_MACH_REFID_{{$key}}"  value="{{ $row->MACH_31_ID }}"  id="machine31_hdnMACHID_{{$key}}" class="form-control" /></td>
  <td id="tdshift_31" >
      <input type="text" name="shift31_{{$key}}" id="shift31_txtLISTPOP_popup_{{$key}}"  value="{{ $row->SHFT_31_DESC }}"  disabled    readonly data-dayno="31" class="form-control" style="width:140px;"  />
  </td>
  <td id="tdhdnshift_31" hidden> <input type="text" name="shift31_SHIFT_REFID_{{$key}}"    value="{{ $row->SHFT_31_ID }}"   id="shift31_hdnSHIFTID_{{$key}}" class="form-control"  /> </td>  
  <td id="tdday_qty_31"><input type="text"  name="dayqty31_{{$key}}"   disabled    id="dayqty31_{{$key}}"  data-dayno="31"   class="form-control three-digits" maxlength="13" value="{{ $row->QTY_31 }}" style="width:130px;" autocomplete="off"  /></td>
  
  
  
  <td align="center" ><button class="btn add material" title="add" data-toggle="tooltip" disabled type="button" ><i class="fa fa-plus"></i></button>
  <button class="btn remove dmaterial"  disabled title="Delete" data-toggle="tooltip" type="button" ><i class="fa fa-trash" ></i></button></td>

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
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Alert -->
<!--itemcat dropdown-->

<div id="sectionid_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='ctryidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Category Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="country_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="sectionmaster_codesearch" onkeyup="searchSectionMasteCode()"></td>
          <td><input type="text" id="sectionmaster_namesearch" onkeyup="searchSectionMasteName()"></td>
        </tr>
        </tbody>
      </table>


      <table id="sectionmaster_tab" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="country_body">
        @foreach ($objItemCategoryList as $index=>$ItemCatList)
        <tr id="businesscode_{{ $ItemCatList->ICID }}" class="sectionmaster_tab">
          <td width="50%">{{ $ItemCatList->ICCODE }}
          <input type="hidden" id="txtbusinesscode_{{ $ItemCatList->ICID }}" data-desc="{{ $ItemCatList->ICCODE }}" data-descname="{{ $ItemCatList->DESCRIPTIONS }}" value="{{ $ItemCatList->ICID }}"/>
          </td>
          <td>{{ $ItemCatList->DESCRIPTIONS }}</td>
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





<!-- Item Code Dropdown -->
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
            
            <td> <input type="text" name="fieldid" id="hdn_ItemID"/>
            <input type="text" name="fieldid2" id="hdn_ItemID2"/>
            <input type="text" name="fieldid3" id="hdn_ItemID3"/>
            <input type="text" name="fieldid4" id="hdn_ItemID4"/>
            <input type="text" name="fieldid5" id="hdn_ItemID5"/>
            <input type="text" name="fieldid6" id="hdn_ItemID6"/>
            <input type="text" name="fieldid7" id="hdn_ItemID7"/>

     
            </td>
      </tr>
      <!-- <tr class="topnav"><button class="btn topnavbt" id="btnOk"><i class="fa fa-check"></i> OK</button></tr> -->
      <tr>
            <th style="width:10%;" id="all-check" >Select</th>
            <th style="width:15%;">Item Code</th>
            <th style="width:15%;">Name</th>

            <th hidden>Part No</th>

            <th style="width:10%;">Main UOM</th>

            <th hidden >Main QTY</th>
            <th hidden >Item Group</th>

            <th style="width:10%;">Item Category</th>
            <th style="width:10%;">Business Unit</th>
            <th style="width:10%;">ALPS Part No.</th>
            <th style="width:10%;">Customer Part No.</th>
            <th style="width:10%;">OEM Part No.</th>

            <th hidden>Status</th>
      </tr>
    </thead>
    <tbody>
    <tr>
    <td style="width:10%;text-align:center;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
    <td style="width:15%;">
    <input type="text" id="Itemcodesearch" class="form-control" onkeyup="ItemCodeFunction()">
    </td>
    <td style="width:15%;">
    <input type="text" id="Itemnamesearch" class="form-control" onkeyup="ItemNameFunction()">
    </td>
    <td hidden>
    <input type="text" id="Itempartsearch" class="form-control" onkeyup="ItemPartnoFunction()">
    </td>
    <td style="width:10%;">
    <input type="text" id="ItemUOMsearch" class="form-control" onkeyup="ItemUOMFunction()">
    </td>
    <td hidden>
    <input type="text" id="ItemQTYsearch" class="form-control" onkeyup="ItemQTYFunction()">
    </td>
    <td hidden>
    <input  type="text" id="ItemGroupsearch" class="form-control" onkeyup="ItemGroupFunction()">
    </td>
    <td style="width:10%;">
    <input  type="text" id="ItemCategorysearch" class="form-control" onkeyup="ItemCategoryFunction()">
    </td>

    <td style="width:10%;"><input type="text" id="ItemBUsearch" class="form-control" onkeyup="ItemBUFunction()"></td>
    <td style="width:10%;"><input type="text" id="ItemAPNsearch" class="form-control" onkeyup="ItemAPNFunction()"></td>
    <td style="width:10%;"><input type="text" id="ItemCPNsearch" class="form-control" onkeyup="ItemCPNFunction()"></td>
    <td style="width:10%;"><input type="text" id="ItemOEMPNsearch" class="form-control" onkeyup="ItemOEMPNFunction()"></td>

    <td hidden>
    <input type="text" id="ItemStatussearch" class="form-control" onkeyup="ItemStatusFunction()">
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
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Item Code Dropdown-->

<!-- FORMUP-->
<div id="FORMpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='FORM_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Machines</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="FORMTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
          <tr id="none-select" class="searchalldata"  >            
            <td > <input type="text" name="fieldid" id="hdn_FORMid"/>
              <input type="text" name="fieldid2" id="hdn_FORMid2"/>
              <input type="text" name="fieldid3" id="hdn_FORMid3"/>
            </td>
          </tr>
          <tr>
                  <th>Code</th>
                  <th>Description</th>
          </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="FORMcodesearch" onkeyup="FORMCodeFunction()">
    </td>
    <td>
    <input type="text" id="FORMnamesearch" onkeyup="FORMNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="FORMTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">

        </thead>
        <tbody id="tbody_FORM">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- FORMUP END-->
<!-- POPUP-->
<div id="LISTPOP1popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='LISTPOP1_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Shifts</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="LISTPOP1Table" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
          <tr id="none-select" class="searchalldata"  >            
            <td >
              <input type="text" name="fieldid" id="hdn_LISTPOP1id"/>
              <input type="text" name="fieldid2" id="hdn_LISTPOP1id2"/>
              <input type="text" name="fieldid3" id="hdn_LISTPOP1id3"/>
              <input type="text" name="fieldid4" id="hdn_LISTPOP1id4"/>
            </td>
          </tr>
          <tr>
                  <th>Code</th>
                  <th>Description</th>
          </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="LISTPOP1codesearch" onkeyup="LISTPOP1CodeFunction()">
    </td>
    <td>
    <input type="text" id="LISTPOP1namesearch" onkeyup="LISTPOP1NameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="LISTPOP1Table2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">

        </thead>
        <tbody id="tbody_LISTPOP1">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- POPUP END-->

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

    font-weight: 600;
}

#ItemIDTable2 td {
  text-align: left;
    padding: 5px;
    font-size: 11px;
      font-weight: 600;
    width: 16%;
}

</style>
@endpush
@push('bottom-scripts')
<script>

 //Item ID Dropdown
 let itemtid = "#ItemIDTable2";
      let itemtid2 = "#ItemIDTable";
      let itemtidheaders = document.querySelectorAll(itemtid2 + " th");

      // Sort the table element when clicking on the table headers
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


      function ItemPartnoFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Itempartsearch");
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

  $('#Material').on('focus','[id*="popupITEMID"]',function(event){
    var BU_NO = $(this).parent().parent().find('[id*="REF_BUID"]').val();
    if(BU_NO ===""){
          showAlert('Please select Item Category.');
        }else{


                
                $("#tbody_ItemID").html('Loading...');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[229,"getItemDetails"])}}',
                      type:'POST',
                      data:{'status':'A',BU_NO:BU_NO},
        
                      success:function(data) {
                        $("#tbody_ItemID").html(data);    
                        bindItemEvents();   
                        $('.js-selectall').prop('disabled', true);                     
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
       // var id4 = $(this).parent().parent().find('[id*="ItemPartno"]').attr('id');
       // var id5 = $(this).parent().parent().find('[id*="Itemspec"]').attr('id');
        var id6 = $(this).parent().parent().find('[id*="itemuom"]').attr('id');
        var id7 = $(this).parent().parent().find('[id*="REF_BUID"]').attr('id');



  }

        $('#hdn_ItemID').val(id);
        $('#hdn_ItemID2').val(id2);
        $('#hdn_ItemID3').val(id3);
       // $('#hdn_ItemID4').val(id4);
       // $('#hdn_ItemID5').val(id5);
        $('#hdn_ItemID6').val(id6);
        $('#hdn_ItemID7').val(id7);
     
       
        event.preventDefault();
      });

      $("#ITEMID_closePopup").click(function(event){
        $("#ITEMIDpopup").hide();
      });

    function bindItemEvents(){

      $('#ItemIDTable2').off(); 

      $('[id*="chkId"]').change(function(){
  
      // $(".clsitemid").dblclick(function(){
        var fieldid = $(this).parent().parent().attr('id');
        var txtval =   $("#txt"+fieldid+"").val();
        var texdesc =  $("#txt"+fieldid+"").data("desc");
        var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
        var txtname =  $("#txt"+fieldid2+"").val();
        var txtspec =  $("#txt"+fieldid2+"").data("desc");

   
        var fieldid7 = $(this).parent().parent().children('[id*="itempartno"]').attr('id');
        var txtpartno =  $("#txt"+fieldid7+"").val();

        var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
        var txtmuomid =  $("#txt"+fieldid3+"").val();
        var txtmainuom =  $("#txt"+fieldid3+"").data("desc");

       
        // var txtAddPur1 = 0;
        var txtItemRate =  $("#txt"+fieldid+"").data("itemrate");

        var  buref =  $("#"+$('#hdn_ItemID7').val()).val();
       

       if($(this).is(":checked") == true) 
       {

        var ArrData = [];
        $('#example2').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            var tmpitem = $(this).find('[id*="REF_BUID_"]').val()+'-'+$(this).find('[id*="ITEMID_REF"]').val();
            ArrData.push(tmpitem);
          }
        });
        
        var recdata = buref+'-'+txtval;
        if(jQuery.inArray(recdata, ArrData) !== -1){
          $("#ITEMIDpopup").hide();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Item already exists. Please check.');
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

          txtval = '';
          texdesc = '';
          txtname = '';
          txtpartno = '';
          txtspec = '';   
                    
          txtmuomid = '';
          return false;
          
        }

        // $('#example2').find('.participantRow').each(function()
        //  {
        //    var itemid = $(this).find('[id*="ITEMID_REF"]').val();
        //    if(txtval)
        //    {
        //         if(txtval == itemid)
        //         {
        //             $("#ITEMIDpopup").hide();
        //               $("#YesBtn").hide();
        //               $("#NoBtn").hide();
        //               $("#OkBtn").hide();
        //               $("#OkBtn1").show();
        //               $("#AlertMessage").text('Item already exists.');
        //               $("#alert").modal('show');
        //               $("#OkBtn1").focus();
        //               highlighFocusBtn('activeOk1');
        //               $('#hdn_ItemID').val('');
        //               $('#hdn_ItemID2').val('');
        //               $('#hdn_ItemID3').val('');
        //               $('#hdn_ItemID4').val('');
        //               $('#hdn_ItemID5').val('');
        //               $('#hdn_ItemID6').val('');
        //               $('#hdn_ItemID7').val('');

        //               txtval = '';
        //               texdesc = '';
        //               txtname = '';
        //               txtpartno = '';
        //               txtspec = '';   
                               
        //               txtmuomid = '';
        //               return false;
                      
        //         }   
               
        //    }          
        // });
                      if($('#hdn_ItemID').val() == "" && txtval != '')
                      {
                        var txtid= $('#hdn_ItemID').val();
                        var txt_id2= $('#hdn_ItemID2').val();
                        var txt_id3= $('#hdn_ItemID3').val();
                        var txt_id4= $('#hdn_ItemID4').val();
                        var txt_id5= $('#hdn_ItemID5').val();
                        var txt_id6= $('#hdn_ItemID6').val();
                        var txt_id7= $('#hdn_ItemID7').val();
               
   
                        var $tr = $('.material').closest('table');
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
                        $clone.find('.remove').removeAttr('disabled'); 
                        $clone.find('[id*="popupITEMID"]').val(texdesc);
                        $clone.find('[id*="ITEMID_REF"]').val(txtval);
                        $clone.find('[id*="ItemName"]').val(txtname);

                      // $clone.find('[id*="ItemPartno"]').val(txtsappartno);
                      //  $clone.find('[id*="Itemspec"]').val(txtspec);

                        $clone.find('[id*="itemuom"]').val(txtmuomid);
                        $clone.find('[id*="itemmain_uom"]').val(txtmainuom);
                        // $clone.find('[id*="CID_REF"]').val(txtsapcustname);
                        // $clone.find('[id*="CUSTOMERID_REF"]').val(txtsapcustcode);

                        
                        
                        $tr.closest('table').append($clone);   
                        var rowCount = $('#Row_Count1').val();
                          rowCount = parseInt(rowCount)+1;
                          $('#Row_Count1').val(rowCount);
                         

                        event.preventDefault();
                      }
                      else
                      {
                      var txtid= $('#hdn_ItemID').val();
                      var txt_id2= $('#hdn_ItemID2').val();
                      var txt_id3= $('#hdn_ItemID3').val();
                      //var txt_id4= $('#hdn_ItemID4').val();
                     // var txt_id5= $('#hdn_ItemID5').val();
                      var txt_id6= $('#hdn_ItemID6').val();
                      var txt_id7= $('#hdn_ItemID7').val();

       
                      $('#'+txtid).val(texdesc);
                      $('#'+txt_id2).val(txtval);
                      $('#'+txt_id3).val(txtname);
                      //$('#'+txt_id4).val(txtsappartno);
                     // $('#'+txt_id5).val(txtspec);
                      $('#'+txt_id6).val(txtmuomid);

                      $('#'+txtid).parent().parent().find('[id*="itemmain_uom"]').val(txtmainuom);
                     
                       
                      $('#hdn_ItemID').val('');
                      $('#hdn_ItemID2').val('');
                      $('#hdn_ItemID3').val('');
                      $('#hdn_ItemID4').val('');
                      $('#hdn_ItemID5').val('');
                      $('#hdn_ItemID6').val('');
                      $('#hdn_ItemID7').val('');

                      
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
        $("#Itempartnosearch").val(''); 
        $("#ItemUOMsearch").val(''); 
        $("#ItemGroupsearch").val(''); 
        $("#ItemCategorysearch").val(''); 
        $("#ItemStatussearch").val(''); 
        $('.remove').removeAttr('disabled'); 
        ItemCodeFunction();
        event.preventDefault();
      });
    }

      

  //Item ID Dropdown Ends
//------------------------
//------------------------
  //FORM Dropdown
  let frmid = "#FORMTable2";
      let frmid2 = "#FORMTable";
      let frmheaders = document.querySelectorAll(frmid2 + " th");

      // Sort the table element when clicking on the table headers
      frmheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(frmid, ".clssqid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function FORMCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("FORMcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("FORMTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[0];
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

  function FORMNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("FORMnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("FORMTable2");
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

  $('#example2').on('focus','[id*="txtMachine_popup"]',function(event){

    var BU_NO = $(this).parent().parent().find('[id*="REF_BUID"]').val();
    if(BU_NO ===""){
          showAlert('Please select Item Category.');
          return false;
    }

    var ITEMID_REF = $(this).parent().parent().find('[id*="ITEMID_REF"]').val();
    if(ITEMID_REF ===""){
          showAlert('Please select Item Code.');
          return false;
    }
        //--

          var id = $(this).attr('id');
          var id2 = id.replace("txtMachine_popup","hdnMACHID");
          //var id2 = $("#tdhdnmachine_"+dno).find('[id*="hdnMACHID"]').attr('id');      
         // var id3 = $(this).parent().find('[id*="FORMNAME"]').attr('id');      

          $('#hdn_FORMid').val(id);
          $('#hdn_FORMid2').val(id2);
         // $('#hdn_FORMid3').val(id3);
        
          $("#FORMpopup").show();
          //$("#tbody_FORM").html('');
          $("#tbody_FORM").html('Loading...');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          })
          $.ajax({
              url:'{{route("transaction",[229,"getmachines"])}}',
              type:'POST',
              success:function(data) {
                $("#tbody_FORM").html(data);
                BindFORMEvents();
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $("#tbody_FORM").html('');
              },
          });

      });

      $("#FORM_closePopup").click(function(event){
        $("#FORMpopup").hide();
      });

      function BindFORMEvents()
      {
          $(".clsFORMid").dblclick(function(){
              var fieldid = $(this).attr('id');
              var txtval =    $("#txt"+fieldid+"").val();
              var texdesc =   $("#txt"+fieldid+"").data("desc");
              var texdescdate =   $("#txt"+fieldid+"").data("descdate");
              
              var txtid= $('#hdn_FORMid').val();
              var txt_id2= $('#hdn_FORMid2').val();
              //var txt_id3= $('#hdn_FORMid3').val();

              $('#'+txtid).val(texdesc);
              $('#'+txt_id2).val(txtval);
              //$('#'+txt_id3).val(texdescdate);
             
              //clear  CODE 
             // $('#'+txtid).parent().parent().find('[id*="txtLISTPOP1_popup"]').val('');
             // $('#'+txtid).parent().parent().find('[id*="hdnLISTPOP1ID"]').val('');
             // $('#'+txtid).parent().parent().find('[id*="DESC2"]').val('');


              $("#FORMpopup").hide();
              
              $("#FORMcodesearch").val(''); 
              $("#FORMnamesearch").val(''); 
              FORMCodeFunction();
              event.preventDefault();
          });
      }
//------------------------


      


  
$('#btnAdd').on('click', function() {
  var viewURL = '{{route("transaction",[229,"add"])}}';
              window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
});

$('#ENQDT').change(function() {
    var mindate  = $(this).val();
    $('[id*="EDD"]').attr('min',mindate);
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

  //calculate and check sum
    var totqty = 0;
    $(this).parent().parent().find('[id*="dayqty"]').each(function(){
        
        var dqty = $.trim($(this).val());
        if(isNaN(dqty) || dqty=="" )
        {
          dqty = 0;
        }
        totqty = parseFloat( parseFloat(totqty) + parseFloat(dqty) ).toFixed(3);

    }); 
    
    var prqty  = $.trim( $(this).parent().parent().find('[id*="ppqty"]').val() );
    if(isNaN(prqty) || prqty=="" )
    {
      prqty = 0;
    }
    if(parseFloat(totqty)!=parseFloat(prqty) ){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please check. Production Plan Qty and Total of Qty must be same.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
      return false;
    }
    //console.log('prqty='+prqty + " totqty="+totqty );
  
  //----calculate and check sum

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
  
  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count1').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count1').val(rowCount1);
  $clone.find('.remove').removeAttr('disabled'); 

  applyForceNum();
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
    //reload form
    window.location.reload();
}//fnUndoYes

window.fnUndoNo = function (){
    $("#ENQNO").focus();
}//fnUndoNo

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

</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  
  var last_DT = <?php echo json_encode($objlast_DT[0]->MPP_DOC_DT); ?>;
  
  $('#MPPDT').attr("min",last_DT);
  $('#MPPDT').attr("max",today);

  var lblname =  $.trim( $("#MONTH_DT option:selected").text() ) ;
  $(".monthname").html(lblname); 

  //---- show day
      var nodays = $("#act_month_day").val();
      $("#act_month_day").val(nodays);

      var i;
      for (i=1 ; i<=31; i++) {

          $("#thday_"+i).hide();

          $("#head_mc_"+i).hide();
          $("#head_shift_"+i).hide();
          $("#head_qty_"+i).hide();

          $("#tdmachine_"+i).hide();
          $("#tdshift_"+i).hide();
          $("#tdday_qty_"+i).hide();
      }

      var k;
      for (k=1; k<=nodays;k++) {

          $("#thday_"+k).show();

          $("#head_mc_"+k).show();
          $("#head_shift_"+k).show();
          $("#head_qty_"+k).show();

          $("#tdmachine_"+k).show();
          $("#tdshift_"+k).show();
          $("#tdday_qty_"+k).show();
      }
  //---- show day end



  applyForceNum();

  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  
  var last_DT = <?php echo json_encode($objlast_DT); ?>;
  
  if(Date(last_DT)>=Date(today)){
    mindate = today;
  }else{
    mindate = last_DT;
  }
  
  if(Date(last_DT)>=Date(today)){
    mxdate = last_DT;
  }else{
    mxdate = today;
  }
  $('#AFSDT').attr("min",mindate);
  $('#AFSDT').attr("max",mxdate);



var count1 = <?php echo json_encode($objCount1); ?>;


$('#Row_Count1').val(count1);

var objSE = <?php echo json_encode($objSEMAT); ?>;


//var item = <?php echo json_encode($objItems); ?>;




    $('#frm_trn_se1').bootstrapValidator({
       
        fields: {
            txtlabel: {
                validators: {
                    notEmpty: {
                        message: 'The Enquiry Number is required'
                    }
                }
            },            
        },
        submitHandler: function(validator, form, submitButton) {
            alert( "Handler for .submit() called." );
             event.preventDefault();
             $("#frm_trn_se").submit();
        }
    });
});

function validateForm(){
       $("#FocusId").val('');
       var MPPDOCNO     =   $.trim($("#MPPDOCNO").val());
        var MPPDT        =   $.trim($("#MPPDT").val());
        var PERIOD    =   $.trim($("#MONTH_DT option:selected").val());
        var FYID_REF     =   $.trim($("#FYID_REF").val());
        var monthdays =    $.trim($("#act_month_day").val()); 


        if(MPPDOCNO ===""){
            $("#FocusId").val($("#MPPDOCNO"));
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please enter value in Doc Number.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else if(MPPDT ===""){
            $("#FocusId").val($("#MPPDT"));
            $("#MPPDT").val();  
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please select Date.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }  
        else if(PERIOD ===""){
            $("#FocusId").val($("#MONTH_DT"));
            $("#MPPDT").val();  
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please select Period.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }  

        else{

          event.preventDefault();
                  var allblank = [];
                  var allblank2 = [];
                  var allblank3 = [];
                  var allblank4 = [];
                  var allblank5 = [];


                  $('#example2').find('.participantRow').each(function(){
                      // $(this).find('td').each(function(){
                      //   var dayno = $(this).find("[id*=dayqty]").data("dayno") || null;
                      //   if(parseInt(dayno)<=parseInt(monthdays) ){
                      //    // alert("dayno="+ $(this).find("[id*=dayqty]").data("dayno") );
                      //   }
                      // });    
                      //-------
                      var totqty = 0;
                      $(this).find('[id*="dayqty"]').each(function(){
                          
                          var dqty = $.trim($(this).val());
                          if(isNaN(dqty) || dqty=="" )
                          {
                            dqty = 0;
                          }
                          totqty = parseFloat( parseFloat(totqty) + parseFloat(dqty) ).toFixed(3);

                      }); 

                      var prqty  = $.trim( $(this).find('[id*="ppqty"]').val() );
                      if(isNaN(prqty) || prqty=="" )
                      {
                        prqty = 0;
                      }
                      console.log('totqty'+totqty + 'prqty=='+prqty);

                      if(parseFloat(totqty)==parseFloat(prqty) ){
                          allblank5.push('true');
                      }else{
                        allblank5.push('false');
                      }
                      //---------          
                }); 
            
            
            
                $('#example2').find('.participantRow').each(function(){
                  if($.trim($(this).find("[id*=BUID_REF]").val())!=""){
                  allblank.push('true');
                  }
                  else{
                    allblank.push('false');
                  } 

                  if($.trim($(this).find("[id*=popupITEMID]").val())!=""){
                      allblank2.push('true');
                  }
                  else{
                      allblank2.push('false');
                  } 

                  var prqty  = $.trim( $(this).find('[id*="ppqty"]').val() );
                  if(isNaN(prqty) || prqty=="" )
                  {
                    prqty = "";
                  }

                  if(prqty!=""){
                      allblank3.push('true');
                  }
                  else{
                      allblank3.push('false');
                  } 

                  
                //-----------   
                }); 

        }


        if(jQuery.inArray("false", allblank) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Item Category in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        else if(jQuery.inArray("false", allblank2) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Item in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }  
        else if(jQuery.inArray("false", allblank3) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please enter value for Production Plan Qty in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }  
        else if(jQuery.inArray("false", allblank5) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please check. Production Plan Qty and Total of Qty must be same.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }   
          else{
                $("#alert").modal('show');
                $("#AlertMessage").text('Do you want to save to record.');
                $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                $("#YesBtn").focus();
                $("#OkBtn").hide();
                highlighFocusBtn('activeYes');
          }

}

$("#btnSaveSE" ).click(function() {
    var formReqData = $("#frm_trn_se");
    if(formReqData.valid()){
      validateForm();
    }
});



$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

window.fnSaveData = function (){
event.preventDefault();
      var trnseForm = $("#frm_trn_se");
      var formData = trnseForm.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{ route("transactionmodify",[229,"update"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
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
      var trnseForm = $("#frm_trn_se");
      var formData = trnseForm.serialize();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  $.ajax({
      url:'{{ route("transactionmodify",[229,"Approve"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
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

//no button
$("#NoBtn").click(function(){
    $("#alert").modal('hide');
    $("#LABEL").focus();
});

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("transaction",[229,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
    // $("[id*=txtlabel]").focus();
    $(".text-danger").hide();
});

//
function showError(pId,pVal){
    $("#"+pId+"").text(pVal);
    $("#"+pId+"").show();
}
function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}
function getFocus(){
    var FocusId=$("#FocusId").val();
    if(focusid!="" && focus!=undefined){
      $("#"+focusid).focus();
    }
    $("#closePopup").click();
}
function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }
    // Business Unit popup function
function get_section(id){
   
   var result = id.split('_');
   var id_number=result[2];
   var popup_id='#'+id;
   $(".sectionmaster_tab").val(popup_id);    

 $("#sectionid_popup").show();
 $("#SECTIONID_POPUP").keyup(function(event){
 if(event.keyCode==13){
   $("#sectionid_popup").show();
 }
});

$("#ctryidref_close").on("click",function(event){ 
 $("#sectionid_popup").hide();
});

$('.sectionmaster_tab').dblclick(function(){

   var value= $(".sectionmaster_tab").val()
   var result = value.split('_');
   var id_numbers=result[2];
   var sectionid_ref="#BUID_REF_"+id_numbers; 
   var buid="#REF_BUID_"+id_numbers; 


   var id          =   $(this).attr('id');

 var txtval      =   $("#txt"+id+"").val();
 var texdesc     =   $("#txt"+id+"").data("desc");
 var texdescname =   $("#txt"+id+"").data("descname");
 



 //$("#SALES_AC_DESC").val(texdescname);
  //clear row
 $(buid).parent().parent().find('input:text').val('');  //clear text
 $(buid).parent().parent().find('input:hidden').val(''); //clear hidden

 $(buid).val(txtval);
 $(sectionid_ref).val(texdesc);
 $("#sectionid_popup").hide();

});
}

function searchSectionMasteCode() {
 var input, filter, table, tr, td, i, txtValue;
 input = document.getElementById("sectionmaster_codesearch");
 filter = input.value.toUpperCase();
 table = document.getElementById("sectionmaster_tab");
 tr = table.getElementsByTagName("tr");
 for (i = 0; i < tr.length; i++) {
   td = tr[i].getElementsByTagName("td")[0];
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

function searchSectionMasteName() {
     var input, filter, table, tr, td, i, txtValue;
     input = document.getElementById("sectionmaster_namesearch");
     filter = input.value.toUpperCase();
     table = document.getElementById("sectionmaster_tab");
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


// Customer popup function
function get_customer(id){
   
   var result = id.split('_');
   var id_number=result[2];
   var popup_id='#'+id;
   $(".customermaster_tab").val(popup_id);    

 $("#customerid_popup").show();
 $("#CUSTOMERID_POPUP").keyup(function(event){
 if(event.keyCode==13){
   $("#customerid_popup").show();
 }
});

$("#ctryidref1_close").on("click",function(event){ 
 $("#customerid_popup").hide();
});

$('.customermaster_tab').dblclick(function(){

   var value= $(".customermaster_tab").val()
   var result = value.split('_');
   var id_numbers=result[2];
   var sectionid_ref="#CID_REF_"+id_numbers; 
   var customer_id="#CUSTOMERID_REF_"+id_numbers;




   var id          =   $(this).attr('id');

 var txtval      =   $("#txt"+id+"").val();
 var texdesc     =   $("#txt"+id+"").data("desc");
 var texdescname =   $("#txt"+id+"").data("descname");

 //$("#SALES_AC_DESC").val(texdescname);
 $(customer_id).val(txtval);
 $(sectionid_ref).val(texdesc);
 $("#customerid_popup").hide();

});
}

function searchCustomerCode() {
 var input, filter, table, tr, td, i, txtValue;
 input = document.getElementById("customermaster_codesearch");
 filter = input.value.toUpperCase();
 table = document.getElementById("customermaster_tab");
 tr = table.getElementsByTagName("tr");
 for (i = 0; i < tr.length; i++) {
   td = tr[i].getElementsByTagName("td")[0];
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

function searchCustomerName() {
     var input, filter, table, tr, td, i, txtValue;
     input = document.getElementById("customermaster_namesearch");
     filter = input.value.toUpperCase();
     table = document.getElementById("customermaster_tab");
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

// DEPT popup function

$("#DEPTID_NAME").on("click",function(event){ 

$("#dept_popup").show();
});

$("#DEPTID_NAME").keyup(function(event){
if(event.keyCode==13){
  $("#DEPTID_NAME").show();
}
});

$("#dept_close").on("click",function(event){ 
$("#dept_popup").hide();
});

$('.cls_dept').dblclick(function(){
var id          =   $(this).attr('id');
var txtval      =   $("#txt"+id+"").val();
var texdesc     =   $("#txt"+id+"").data("desc");
var texdescname =   $("#txt"+id+"").data("descname");

$("#DEPTID_NAME").val(texdescname);
$("#DEPID_REF").val(txtval);



$("#DEPTID_NAME").blur(); 
$("#STID_REF_POPUP").focus(); 

$("#dept_popup").hide();

event.preventDefault();
});


function searchdeptCode() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("dept_codesearch");
filter = input.value.toUpperCase();
table = document.getElementById("dept_tab");
tr = table.getElementsByTagName("tr");
for (i = 0; i < tr.length; i++) {
  td = tr[i].getElementsByTagName("td")[0];
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


function searchdeptName() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("dept_namesearch");
    filter = input.value.toUpperCase();
    table = document.getElementById("dept_tab");
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


// FINANCE popup function

$("#FYID_NAME").on("click",function(event){ 

$("#fy_popup").show();
});

$("#FYID_NAME").keyup(function(event){
if(event.keyCode==13){
$("#FYID_NAME").show();
}
});

$("#fy_close").on("click",function(event){ 
$("#fy_popup").hide();
});

$('.cls_fyear').dblclick(function(){
var fieldid          =   $(this).attr('id');
var txtval      =   $("#txt"+fieldid+"").val();
var texdesc     =   $("#txt"+fieldid+"").data("desc");
var texdescname =   $("#txt"+fieldid+"").data("descname");


$("#FYID_NAME").val(texdescname);
$("#FYID_REF").val(txtval);



$("#FYID_NAME").blur(); 

$("#fy_popup").hide();

event.preventDefault();
});


let fyear = "#fy_table2";
    let fyearid2 = "#fy_table1";
    let fyearheaders = document.querySelectorAll(fyearid2 + " th");

    // Sort the table element when clicking on the table headers
    fyearheaders.forEach(function(element, i) {
      element.addEventListener("click", function() {
        w3.sortHTML(fyear, ".cls_fyear", "td:nth-child(" + (i + 1) + ")");
      });
    });

function searchfyCode() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("fy_codesearch");
filter = input.value.toUpperCase();
table = document.getElementById("fy_table2");
tr = table.getElementsByTagName("tr");
for (i = 0; i < tr.length; i++) {
td = tr[i].getElementsByTagName("td")[0];
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


function searchfyName() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("fy_namesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("fy_table2");
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




$( "#btnApprove" ).click(function() {
 
  $("#FocusId").val('');
        var AFSNO          =   $.trim($("#AFSNO").val());
        var AFSDT          =   $.trim($("#AFSDT").val());
        var DEPID_REF          =   $.trim($("#DEPID_REF").val());
        var FYID_REF          =   $.trim($("#FYID_REF").val());


        if(AFSNO ===""){
            $("#FocusId").val($("#AFSNO"));
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please enter value in AFP Number.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else if(AFSDT ===""){
            $("#FocusId").val($("#AFSDT"));
            $("#AFSDT").val();  
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please select AFP Date.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }  

        else if(DEPID_REF ===""){
          $("#FocusId").val($("#DEPID_REF"));
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please select Department.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else if(FYID_REF ===""){
          $("#FocusId").val($("#FYID_REF"));
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show(); 
            $("#AlertMessage").text('Please select Financial Year.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else{

            event.preventDefault();
            var allblank = [];
            var allblank2 = [];
            var allblank3 = [];
            
            
                $('#example2').find('.participantRow').each(function(){
                  if($.trim($(this).find("[id*=BUID_REF]").val())!=""){
                  allblank.push('true');
                  }
                  else{
                    allblank.push('false');
                  } 

                  if($.trim($(this).find("[id*=popupITEMID]").val())!=""){
                      allblank2.push('true');
                  }
                  else{
                      allblank2.push('false');
                  } 

                //-----------   
                }); 
        }

        if(jQuery.inArray("false", allblank) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Business Unit in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        else if(jQuery.inArray("false", allblank2) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Item in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          } 
   
          else{
                $("#alert").modal('show');
                $("#AlertMessage").text('Do you want to save to record.');
                $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name
                $("#YesBtn").focus();
                $("#OkBtn").hide();
                highlighFocusBtn('activeYes');
          }
       
});


// begin
$('#Material').on('blur',"[id*='dayqty']",function()
{
    var dqty = $.trim($(this).val());
    if(isNaN(dqty) || dqty=="" )
    {
      dqty = 0;
    }  
    if(intRegex.test(dqty))
    {
      $(this).val((dqty +'.000'));
    }
   
    event.preventDefault();
});  

$('#Material').on('blur',"[id*='ppqty']",function()
{
      var ppqty = $.trim($(this).val());
      if(isNaN(ppqty) || ppqty=="" )
      {
        ppqty = 0;
      }  
      if(intRegex.test(ppqty))
      {
        $(this).val((ppqty +'.000'));
      }

    event.preventDefault();
});  

//  end


function applyForceNum(){

  $("[id*='ppqty']").ForceNumericOnly();
  $("[id*='dayqty']").ForceNumericOnly();

}


$('#MONTH_DT').on('change', function () {
     var selectVal = $.trim( $("#MONTH_DT option:selected").val() );
     var lblname =  $.trim( $("#MONTH_DT option:selected").text() ) 

     //clear all rows
     $('#example2').find('.participantRow').each(function(){
            var rowcount = $('#Row_Count').val();
            $(this).find('input:text').val('');
            $(this).find('input:hidden').val('');
            var rowid = $(this).find('[id*="ItemName_"]').attr("id");
            if(rowid!="ItemName_0"){
              $(this).remove();      //remove except first row
            }
      });

     if(selectVal !=""){

        // //clear all rows
        // $('#example2').find('.participantRow').each(function(){
        //       var rowcount = $('#Row_Count').val();
        //       $(this).find('input:text').val('');
        //       $(this).find('input:hidden').val('');
        //       var rowid = $(this).find('[id*="machine1"]').attr("id");
        //       if(rowid!="machine1_0"){
        //         $(this).remove();      //remove except first row
        //       }
        // });
      
      $("#monthlabel").html(lblname); 
      $(".monthname").html(lblname); 
      


      var d = new Date(); 
      var fullyear =  d.getFullYear();
      
      var nodays = new Date(fullyear,selectVal,0).getDate();
          $("#act_month_day").val(nodays);
  
          var i;
          for (i=1 ; i<=31; i++) {

                  $("#thday_"+i).hide();

                  $("#head_mc_"+i).hide();
                  $("#head_shift_"+i).hide();
                  $("#head_qty_"+i).hide();

                  $("#tdmachine_"+i).hide();
                  $("#tdshift_"+i).hide();
                  $("#tdday_qty_"+i).hide();
          }

      
          var k;
          for (k=1; k<=nodays;k++) {

            $("#thday_"+k).show();

            $("#head_mc_"+k).show();
            $("#head_shift_"+k).show();
            $("#head_qty_"+k).show();

            $("#tdmachine_"+k).show();
            $("#tdshift_"+k).show();
            $("#tdday_qty_"+k).show();
          }

          $("#headingtr2").show();
          $("#headingtr3").show();
          $(".participantRow").show();
       
       
      // $('#example2').find('.participantRow').each(function(){
      //     $(this).find('td').each(function(){
      //       var dayno = $(this).find("[id*=dayqty]").data("dayno") || null;
      //       if(parseInt(dayno)<=parseInt(selectVal) ){
      //         //alert("dayno="+ $(this).find("[id*=dayqty]").data("dayno") );
      //         $(this).hide();
      //       }else{
      //         $(this).show();
      //       }

      //       var dayno2 = $(this).find("[id*=machine]").data("dayno") || null;
      //       if(parseInt(dayno2)<=parseInt(selectVal) ){
      //         //alert("dayno="+ $(this).find("[id*=dayqty]").data("dayno") );
      //         $(this).hide();
      //       }else{
      //         $(this).show();
      //       }

      //       var dayno3 = $(this).find("[id*=shift]").data("dayno") || null;
      //       if(parseInt(dayno3)<=parseInt(selectVal) ){
      //         //alert("dayno="+ $(this).find("[id*=dayqty]").data("dayno") );
      //         $(this).hide();
      //       }else{
      //         $(this).show();
      //       }

      //     });
      // });

     }else{
      $("#act_month_day").val('0');
      $("#headingtr2").hide();
      $("#headingtr3").hide();
      $(".participantRow").hide();

     }
});
 
 
//------------------------
  //LISTPOP1 Dropdown
  let sqtid = "#LISTPOP1Table2";
      let sqtid2 = "#LISTPOP1Table";
      let salesquotationheaders = document.querySelectorAll(sqtid2 + " th");

      // Sort the table element when clicking on the table headers
      salesquotationheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sqtid, ".clssqid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function LISTPOP1CodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("LISTPOP1codesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("LISTPOP1Table2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[0];
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

  function LISTPOP1NameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("LISTPOP1namesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("LISTPOP1Table2");
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

  $('#example2').on('focus','[id*="txtLISTPOP_popup"]',function(event){

          var BU_NO = $(this).parent().parent().find('[id*="REF_BUID"]').val();
          if(BU_NO ===""){
                showAlert('Please select Item Category.');
                return false;
          }

          var ITEMID_REF = $(this).parent().parent().find('[id*="ITEMID_REF"]').val();
          if(ITEMID_REF ===""){
                showAlert('Please select Item Code.');
                return false;
          }
        
          var id = $(this).attr('id');
          var id2 =  id.replace("txtLISTPOP_popup","hdnSHIFTID");      
          

          $('#hdn_LISTPOP1id').val(id);
          $('#hdn_LISTPOP1id2').val(id2);
          //$('#hdn_LISTPOP1id3').val(id3);
          //$('#hdn_LISTPOP1id4').val(id4);
        
          $("#LISTPOP1popup").show();
          //$("#tbody_LISTPOP1").html('');
          $("#tbody_LISTPOP1").html('Loading...');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          })
          $.ajax({
              url:'{{route("transaction",[229,"getshifts"])}}',
              type:'POST',
              success:function(data) {
                $("#tbody_LISTPOP1").html(data);
                BindLISTPOP1Events();
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $("#tbody_LISTPOP1").html('');
              },
          });

      });

      $("#LISTPOP1_closePopup").click(function(event){
        $("#LISTPOP1popup").hide();
      });

      function BindLISTPOP1Events()
      {
          $(".clsLISTPOP1id").dblclick(function(){
              var fieldid = $(this).attr('id');
              var txtval =    $("#txt"+fieldid+"").val();
              var texdesc =   $("#txt"+fieldid+"").data("desc");
              var texdescdate =   $("#txt"+fieldid+"").data("descdate");
              
              var txtid= $('#hdn_LISTPOP1id').val();
              var txt_id2= $('#hdn_LISTPOP1id2').val();
              //var txt_id3= $('#hdn_LISTPOP1id3').val();
              //var txt_id4= $('#hdn_LISTPOP1id4').val();


              //-----------------------------
              // var  buref =  txt_id4; //todo: set on focus
              // var ArrData = [];
              // $('#example2').find('.participantRow').each(function(){
              //   if($(this).find('[id*="hdnFORMID_"]').val() != '')
              //   {
              //     var tmpitem = $(this).find('[id*="hdnFORMID_"]').val()+'-'+$(this).find('[id*="hdnLISTPOP1ID_"]').val();
              //     ArrData.push(tmpitem);
              //   }
              // });
              
              // var recdata = buref+'-'+txtval;
              // if(jQuery.inArray(recdata, ArrData) !== -1){
              //   $("#LISTPOP1popup").hide();
               
              //   $("#YesBtn").hide();
              //   $("#NoBtn").hide();
              //   $("#OkBtn2").hide();
              //   $("#OkBtn").show();
              //   $("#AlertMessage").text('MSP and MSP Code already exists. Please check.');
              //   $("#alert").modal('show');
              //   $("#OkBtn").focus();
              //   highlighFocusBtn('activeOk1');
              //   $('#hdn_LISTPOP1id').val('');
              //   $('#hdn_LISTPOP1id2').val('');
              //   $('#hdn_LISTPOP1id3').val('');
              

              //   fieldid = '';
              //   txtval =   '';
              //   texdesc =   '';
              //   texdescdate =   '';
                
              //   txtid= '';
              //   txt_id2= '';
              //   txt_id3= '';
              //   return false;
                
              // }

              //-----------------------------

              $('#'+txtid).val(texdesc);
              $('#'+txt_id2).val(txtval);
             // $('#'+txt_id3).val(texdescdate);

              

              

              $("#LISTPOP1popup").hide();
              
              $("#LISTPOP1codesearch").val(''); 
              $("#LISTPOP1namesearch").val(''); 
              LISTPOP1CodeFunction();
              event.preventDefault();
          });
      }
//------------------------
//LISTPOP1 Dropdown end

</script>


@endpush