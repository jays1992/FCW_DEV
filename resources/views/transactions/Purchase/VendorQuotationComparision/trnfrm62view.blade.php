@extends('layouts.app')
@section('content')
   
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                  <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Vendor Quotation Comparision (VQC)</a>
                </div><!--col-2-->
                <div class="col-lg-10 topnav-pd">
                  <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                  <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                  <button class="btn topnavbt" id="btnSaveFormData" disabled="disabled"><i class="fa fa-save"></i> Save</button>
                  <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                  <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                  <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                  <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                  <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
                  <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                  <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div><!--row-->
    </div>

    <form id="frm_trn_add" method="POST"  >

<div class="container-fluid purchase-order-view">
    
        @csrf
        <div class="container-fluid filter">

                <div class="inner-form">
                
                    <div class="row">
                        <div class="col-lg-2 pl"><p>VQC No*</p></div>
                        <div class="col-lg-2 pl">
                          <input type="text" disabled name="VQC_NO" id="VQC_NO" value="{{ $objMstResponse->VQC_NO }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
                          <input type="hidden" name="VQCID" id="VQCID" value="{{ $objMstResponse->VQCID }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
                        </div>
                        
                        <div class="col-lg-2 pl"><p>VQC Date*</p></div>
                        <div class="col-lg-2 pl">
                            <input type="date" disabled name="VQC_DT" id="VQC_DT" value="{{ $objMstResponse->VQC_DT }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
                            <input type="hidden" id="LAST_DATE" value="{{$objlastVQC_DT[0]->VQC_DT}}">
                        </div>

                        
         
                    

  

                        

                        <!--
                        <div class="col-lg-2 pl"><p>Remarks</p></div>
                        <div class="col-lg-3 pl">
                            <input type="text" name="REMARKS" id="REMARKS" autocomplete="off" class="form-control" maxlength="200"  >
                        </div>
                        -->
                    </div>
                   
          



                    <div class="row">
              
                        
              <div class="col-lg-2 pl"><p>Vendor 1*</p></div>
              <div class="col-lg-2 pl">
                  <input type="text" disabled name="VID_REF1" id="VID_REF1_popup" class="form-control mandatory" value="{{$objMstResponse->VID_REF1 !=''  ? $objMstResponse->VENDOR_CODE1.'-'.$objMstResponse->VENDOR_NAME1 : ''}}" onclick="get_vendor('VID_REF1');"  autocomplete="off" readonly/>
                  <input type="hidden" name="VID_REF1" id="VID_REF1" class="form-control" value="{{$objMstResponse->VID_REF1}}" autocomplete="off" />
                                                           
                                                                               
              </div>


              <div class="col-lg-2 pl"><p>Vendor 2*</p></div>
              <div class="col-lg-2 pl">
              <input type="text" disabled name="VID_REF2" id="VID_REF2_popup" class="form-control mandatory" value="{{$objMstResponse->VID_REF2 !=''  ? $objMstResponse->VENDOR_CODE2.'-'.$objMstResponse->VENDOR_NAME2 : ''}}" onclick="get_vendor('VID_REF2');"    autocomplete="off" readonly/>
                <input type="hidden" name="VID_REF2" id="VID_REF2" value="{{$objMstResponse->VID_REF2}}" class="form-control" autocomplete="off" />
                                                
                                                                   
              </div>

              <div class="col-lg-2 pl"><p>Vendor 3</p></div>
              <div class="col-lg-2 pl">
              <input type="text" disabled name="VID_REF3" id="VID_REF3_popup" class="form-control mandatory" value="{{$objMstResponse->VID_REF3 !=''  ? $objMstResponse->VENDOR_CODE3.'-'.$objMstResponse->VENDOR_NAME3 : ''}}" onclick="get_vendor('VID_REF3');"  autocomplete="off" readonly/>
              <input type="hidden" name="VID_REF3" id="VID_REF3" value="{{$objMstResponse->VID_REF3}}" class="form-control" autocomplete="off" />
                                              
                                              
              </div>

          </div>
          



          <div class="row">
    
              
    <div class="col-lg-2 pl"><p>Quotation No 1*</p></div>
    <div class="col-lg-2 pl">
        <input type="text" disabled name="VQID_REF1" id="VQID_REF1_popup" value="{{$objMstResponse->VQ_NO1}}" class="form-control mandatory" onclick="get_vendor_quotation('VQID_REF1');"  autocomplete="off" readonly/>
        <input type="hidden" name="VQID_REF1" id="VQID_REF1" value="{{$objMstResponse->VQID_REF1}}" class="form-control" autocomplete="off" />
                                                          
                                                                 
    </div>


    <div class="col-lg-2 pl"><p>Quotation No 2*</p></div>
    <div class="col-lg-2 pl">
    <input type="text" disabled name="VQID_REF2" id="VQID_REF2_popup" value="{{$objMstResponse->VQ_NO2}}" class="form-control mandatory" onclick="get_vendor_quotation('VQID_REF2');"  autocomplete="off" readonly/>
        <input type="hidden" name="VQID_REF2" id="VQID_REF2" value="{{$objMstResponse->VQID_REF2}}" class="form-control" autocomplete="off" />
                                                          
                                                                 
    </div>

    <div class="col-lg-2 pl"><p>Quotation No 3</p></div>
    <div class="col-lg-2 pl">
    <input type="text" disabled name="VQID_REF3" id="VQID_REF3_popup" value="{{$objMstResponse->VQ_NO3}}" class="form-control mandatory" onclick="get_vendor_quotation('VQID_REF3');"  autocomplete="off" readonly/>
        <input type="hidden" name="VQID_REF3" id="VQID_REF3" value="{{$objMstResponse->VQID_REF3}}" class="form-control" autocomplete="off" />
                                                          
                                                                 
    </div>






                        
                    </div>
                </div>

                <div class="container-fluid purchase-order-view">

<div class="row">
<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
    <li><a data-toggle="tab" href="#TC">T & C</a></li>
    <li><a data-toggle="tab" href="#CT">Calculation Template</a></li>  
</ul>



<div class="tab-content">

    <div id="Material" class="tab-pane fade in active">
        <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:500px;margin-top:10px;" >
            <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0">
                    <tr>                                                                                                    
                        <th rowspan="2">Item Code <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="1"></th>
                        <th rowspan="2">Item Name</th>                                           
                        <th rowspan="2" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                                                    <th rowspan="2" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                                                    <th rowspan="2" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
                        <th rowspan="2" width="10%">UoM</th>    
                        <th colspan="3" width="20%">Vendor1</th>
                        <th colspan="3" width="20%">Vendor2</th>
                        <th colspan="3" width="20%">Vendor3</th>
                        <th rowspan="2" width="3%">Action</th>
                    </tr>
                        <tr>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Amount</th>
                 
                        </tr>
                        
                </thead>
                <tbody>
                <tbody id="tbody_item">
                @if(!empty($objList1))
                @foreach($objList1 as $key => $row)
                <tr  class="participantRow" id="VID_REF_{{ $row->QID_REF }}" >
             
                     <td hidden><input type="text" name="rowscount1[]"  > </td>
                     <td hidden><input type="text" name="VID_REF[]" id="VID_REF_{{ $row->QID_REF }}" value="{{$row->QID_REF}}" > </td>
                     <td hidden><input type="text" name="VQID_REF[]" id="VQID_REF_'.$index.'" value="" > </td>
                     <td hidden><input type="text" name="ITEMID_REF[]"   value="{{$row->ITEMID}}"> </td>                                                    
                     <td><input type="text" name="ITEMCODE[]"  class="form-control"value="{{$row->ICODE}}"  autocomplete="off"  readonly style="width:100px;" /></td>
                     <td><input type="text" name="ITEMNAME[]"  class="form-control" value="{{$row->NAME}}"  autocomplete="off"  readonly style="width:200px;" /></td>
                     <td {{$AlpsStatus['hidden']}}  ><input type="text" name="Alpspartno_0" id="Alpspartno_0" class="form-control"  autocomplete="off"   value="{{$row->ALPS_PART_NO}}" readonly  /></td>
                <td {{$AlpsStatus['hidden']}}  ><input type="text" name="Custpartno_0" id="Custpartno_0" class="form-control" value="{{$row->CUSTOMER_PART_NO}}"  autocomplete="off"  readonly  /></td>
                  <td {{$AlpsStatus['hidden']}}  ><input type="text"  value="{{$row->OEM_PART_NO}}" name="OEMpartno_0" id="OEMpartno_0" class="form-control"  autocomplete="off"  readonly /></td>
             
                       
                 <td><input type="text" name="UOMID[]"  class="form-control" value="{{$row->UOMCODE.'-'.$row->DESCRIPTIONS}}"  autocomplete="off"  readonly style="width:100px;"/></td>
                 <td hidden><input type="hidden" name="UOMID_REF[]"  value="{{$row->UOMID_REF}}"  class="form-control"  autocomplete="off" /></td>
               
                   <td><input type="text" name="QTY1[]"   value="{{$row->V1_QTY}}" class="form-control three-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                   <td><input type="text" name="RATE1[]"  value="{{$row->V1_RATE}}" class="form-control three-digits" maxlength="15"  autocomplete="off" readonly  /></td>
                   <td><input type="text" name="AMOUNT1[]"  value="{{$row->V1_RATE*$row->V1_QTY}}" class="form-control three-digits"  maxlength="15"  autocomplete="off" readonly /></td>

                   <td><input type="text" name="QTY2[]"  value="{{$row->V2_QTY}}" class="form-control three-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                   <td><input type="text" name="RATE2[]"  value="{{$row->V2_RATE}}" class="form-control three-digits" maxlength="15"  autocomplete="off" readonly  /></td>
                   <td><input type="text" name="AMOUNT2[]"  value="{{$row->V2_RATE*$row->V2_QTY}}" class="form-control three-digits"  maxlength="15"  autocomplete="off" readonly /></td>

                   <td><input type="text" name="QTY3[]"  value="{{$row->V3_QTY}}" class="form-control three-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                   <td><input type="text" name="RATE3[]"  value="{{$row->V3_RATE}}" class="form-control three-digits" maxlength="15"  autocomplete="off" readonly  /></td>
                   <td><input type="text" name="AMOUNT3[]"  value="{{$row->V3_RATE*$row->V3_QTY}}" class="form-control three-digits"  maxlength="15"  autocomplete="off" readonly /></td>
               
                         
                 <td align="center" ><button class="btn add material" disabled title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button class="btn remove dmaterial" disabled title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
             
                     </tr>

                    @endforeach
                    @endif
                </tbody>
        </table>
        </div>	
    </div>
    
    
    
    <div id="TC" class="tab-pane fade">
        <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:15px;height:240px;width:70%;">
            <table id="example3" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0">                      
         <tr>                                                 
         <th>Terms & Conditions Description</th>
             <th >Vendor1</th>
             <th>Vendor2</th>
             <th>Vendor3</th>
             <th rowspan="2" style="width: 11%;">Action</th>
         </tr>
             <tr>
                 <th>	</th>
                 <th>Value / Comment	</th>
                 <th>Value / Comment	</th>
                 <th>Value / Comment	</th>                                  
             </tr>
             
     </thead>

                <tbody id="tncbody">
                @if(!empty($objList2))
                @foreach($objList2 as $key => $row)

                <tr  class="participantRow3"  id="VID_REF_{{ $row->QID_REF }}">

            
                    <td hidden><input type="text" name="VID_REF_TNC[]" id="VID_REF_{{ $row->QID_REF }}" value="{{$row->QID_REF}}" > </td>
                    <td hidden><input type="text" name="rowscount2[]"  > </td>

                    <td hidden><input type="hidden" name="TNCID_REF[]" value="{{$row->TNCID_REF}}" class="form-control" autocomplete="off" /></td>
                    <td hidden><input type="hidden" name="VQID_REF_TNC[]"  class="form-control" autocomplete="off" /></td>
                    <td ><input type="text" name="TNC_DESC[]" readonly value="{{$row->TNC_NAME}}" class="form-control" autocomplete="off" /></td>
                    <td ><input type="text"  name="VALUE1[]" value="{{$row->V1_VALUE}}" class="form-control" autocomplete="off" readonly /></td>
                    <td ><input type="text"  name="VALUE2[]" value="{{$row->V2_VALUE}}" class="form-control" autocomplete="off" readonly /></td>
                    <td ><input type="text"  name="VALUE3[]" value="{{$row->V3_VALUE}}" class="form-control" autocomplete="off" readonly /></td>

                    </td>
                      <td align="center" ><button class="btn add TNC" title="add" data-toggle="tooltip"  type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DTNC" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                    </tr>
                    <tr></tr>

          
                @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>



    
    <div id="CT" class="tab-pane fade">                                            
      <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:240px; width:72%; margin-top:15px">
<table id="example5" class="display nowrap table table-striped table-bordered itemlist " width="70%" style="height:auto !important;">
              <thead id="thead1"  style="position: sticky;top: 0">                                     
         <tr>                                                                                         
             <th>Calculation Component</th> 
             <th colspan="2" width="20%">Vendor1</th>
             <th colspan="2" width="20%">Vendor2</th>
             <th colspan="2" width="20%">Vendor3</th>
             <th rowspan="2" width="10%">Action</th>
         </tr>
             <tr>                    
                 <th></th>
                 <th>Rate</th>
                 <th>Amount</th>                                   
                 <th>Rate</th>
                 <th>Amount</th>                         
                 <th>Rate</th>
                 <th>Amount</th>                                  
             </tr>                                         
     </thead>
              <tbody id="calc_body">
              @if(!empty($objList3))
                @foreach($objList3 as $key => $row)

                <tr  class="participantRow5" id="VID_REF_{{ $row->QID_REF }}">

          <td hidden><input type="text" name="VID_REF_CAL[]" id="VID_REF_{{ $row->QID_REF }}" value="{{$row->QID_REF}}" > </td>
          <td hidden><input type="text" name="rowscount3[]"  > </td>

          <td hidden><input type="hidden"  name="VQID_REF_CAL[]"  class="form-control" autocomplete="off" /></td>
          <td hidden><input type="hidden"  name="CTID_REF[]"  value="{{$row->TID_REF}}" class="form-control" autocomplete="off" /></td>
          <td><input type="text"  name="COMPONENT[]"  value="{{$row->COMPONENT}}" class="form-control four-digits"  autocomplete="off"  readonly/></td>

          <td><input type="text" name="RATECAL1[]"  value="{{$row->V1_RATE}}" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
          <td><input type="text" name="AMOUNTCAL1[]" value="{{$row->V1_VALUE}}"  class="form-control four-digits" maxlength="15" autocomplete="off"  readonly/></td>

          <td><input type="text" name="RATECAL2[]"  value="{{$row->V2_RATE}}" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
          <td><input type="text" name="AMOUNTCAL2[]" value="{{$row->V2_VALUE}}"  class="form-control four-digits" maxlength="15" autocomplete="off"  readonly/></td>

          <td><input type="text" name="RATECAL3[]"  value="{{$row->V3_RATE}}" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
          <td><input type="text" name="AMOUNTCAL3[]" value="{{$row->V3_VALUE}}"  class="form-control four-digits" maxlength="15" autocomplete="off"  readonly/></td>



          </td>
          <td hidden style="text-align:center;"><input type="checkbox" class="filter-none" name="calACTUAL_0" id="calACTUAL_0" value=""   ></td>
          <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button> <button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
          </tr>
          <tr></tr>



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

</div><!--purchase-order-view-->

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



@endsection


@push('bottom-css')
<style>/*
#custom_dropdown, #frm_trn_add_filter {
    display: inline-table;
    margin-left: 15px;
}
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 7px;
    line-height: 1.42857143;
    vertical-align: top;
    border-top: 1px solid #ddd;
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
} */
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
#CTIDDetTable2 {
  border-collapse: collapse;
  width: 1050px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#CTIDDetTable2 th{
    text-align: left;
    padding: 5px;
    
    font-size: 11px;
    
    color: #0f69cc;
    font-weight: 600;
}

#CTIDDetTable2 td {
  text-align: left;
    padding: 5px;
    font-size: 11px;
    
    font-weight: 600;
    width: 20%;
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

      $("#VQ_NO").focus();

   }//fnUndoNo



</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

//   $("#btnSaveFormData").on("submit", function( event ) {
//     if ($("#frm_trn_add").valid()) {
//         // Do something
//         alert( "Handler for .submit() called." );
//         event.preventDefault();
//     }
// });


    // $('#frm_trn_add1').bootstrapValidator({
       
    //     fields: {
    //         txtlabel: {
    //             validators: {
    //                 notEmpty: {
    //                     message: 'The SO NO is required'
    //                 }
    //             }
    //         },            
    //     },
    //     submitHandler: function(validator, form, submitButton) {
    //         alert( "Handler for .submit() called." );
    //          event.preventDefault();
    //          $("#frm_trn_add").submit();
    //     }
    // });
});

var formTrans = $("#frm_trn_add");
formTrans.validate();

$( "#btnSaveFormData" ).click(function() {
  //var formTrans = $("#frm_trn_add");
  if(formTrans.valid()){
    validateForm("fnSaveData");
  }
});


$( "#btnApprove" ).click(function() {
 
  if(formTrans.valid()){
    validateForm("fnApproveData");
  }
});




$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

window.fnSaveData = function (){

    //validate and save data
    event.preventDefault();
    var trnsoForm = $("#frm_trn_add");
    var formData = trnsoForm.serialize();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{ route("transaction",[$FormId,"update"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
          
            if(data.errors) {
                $(".text-danger").hide();

                if(data.errors.VQ_NO){
                    showError('ERROR_VQ_NO',data.errors.VQ_NO);
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn1").show();
                            $("#AlertMessage").text('Please enter correct value in VQ NO.');
                            $("#alert").modal('show');
                            $("#OkBtn1").focus();
                }
              if(data.country=='norecord') {

                $("#YesBtn").hide();
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

    //validate and save data
    event.preventDefault();
    var trnsoForm = $("#frm_trn_add");
    var formData = trnsoForm.serialize();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{ route("transaction",[$FormId,"Approve"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
          
            if(data.errors) {
                $(".text-danger").hide();

                if(data.errors.VQ_NO){
                    showError('ERROR_VQ_NO',data.errors.VQ_NO);
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn1").show();
                            $("#AlertMessage").text('Please enter correct value in VQ NO.');
                            $("#alert").modal('show');
                            $("#OkBtn1").focus();
                }
              if(data.country=='norecord') {

                $("#YesBtn").hide();
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
});

//ok button
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
    $("#VQ_NO").focus();
    $(".text-danger").hide();
});

//
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

function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
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

</script>


@endpush