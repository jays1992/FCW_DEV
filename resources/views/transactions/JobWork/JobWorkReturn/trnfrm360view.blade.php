
@extends('layouts.app')
@section('content')
<!-- <form id="frm_trn_so" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >     -->

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Job Work Return (JWR)</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSO"  disabled="disabled" ><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove"  disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div>
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->
    <div class="container-fluid purchase-order-view">
  <form id="frm_trn_view"  method="POST">   
    @csrf
    <div class="container-fluid filter">
      <div class="inner-form" id="Header_Form" >

        <div class="row">
          <div class="col-lg-2 pl"><p>JWR No</p></div>
          <div class="col-lg-2 pl">
              <input disabled type="text" name="JWRNO" id="JWRNO" value="{{isset($DATA_HDR->JWRNO)?$DATA_HDR->JWRNO:NULL}}" class="form-control mandatory" maxlength="15" autocomplete="off" style="text-transform:uppercase" autofocus readonly >
          </div>
            
          <div class="col-lg-2 pl"><p>JWR Date</p></div>
          <div class="col-lg-2 pl">
            <input disabled type="date" name="JWRDT" id="JWRDT" value="{{isset($DATA_HDR->JWRDT)?$DATA_HDR->JWRDT:NULL}}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
          </div>

          <div class="col-lg-2 pl"><p>Vendor</p></div>
          <div class="col-lg-2 pl">
              <input disabled type="text" name="txtVID_popup" id="txtVID_popup"  value="{{isset($objsubglcode->VCODE)?$objsubglcode->VCODE:''}} - {{isset($objsubglcode->NAME)?$objsubglcode->NAME:''}}" class="form-control mandatory"  autocomplete="off" readonly/>
              <input type="hidden" name="VID_REF" id="VID_REF" value="{{isset($DATA_HDR->VID_REF)?$DATA_HDR->VID_REF:NULL}}" class="form-control" autocomplete="off" />
              <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />                                                                
          </div>

        </div>

        <div class="row">

          <div class="col-lg-2 pl"><p>Vendor Invoice No</p></div>
          <div class="col-lg-2 pl">
            <input disabled type="text" name="VENDOR_INVOICE_NO" id="VENDOR_INVOICE_NO" value="{{isset($DATA_HDR->Vendor_Invoice_No)?$DATA_HDR->Vendor_Invoice_No:NULL}}" autocomplete="off" class="form-control" maxlength="200"  >
          </div>

          <div class="col-lg-2 pl"><p>Mode Of Transport</p></div>
          <div class="col-lg-2 pl">
            <select disabled  name="TRANSPORT_MODE" id="TRANSPORT_MODE" class="form-control" autocomplete="off" >
              <option value="">Select</option>
              <option {{isset($DATA_HDR->TRANSPORT_MODE) && $DATA_HDR->TRANSPORT_MODE =='By Road'?'selected="selected"':''}} value="By Road">By Road</option>
              <option {{isset($DATA_HDR->TRANSPORT_MODE) && $DATA_HDR->TRANSPORT_MODE =='By Sea'?'selected="selected"':''}} value="By Sea">By Sea</option>
              <option {{isset($DATA_HDR->TRANSPORT_MODE) && $DATA_HDR->TRANSPORT_MODE =='By Air'?'selected="selected"':''}} value="By Air">By Air</option>
            </select>
          </div>

          <div class="col-lg-2 pl"><p>Vehicle No</p></div>
          <div class="col-lg-2 pl">
            <input disabled type="text" name="VCL_NO" id="VCL_NO" value="{{isset($DATA_HDR->VECHILE_NO)?$DATA_HDR->VECHILE_NO:NULL}}" autocomplete="off" class="form-control mandatory" maxlength="200"  >
          </div>

        </div>

        <div class="row">
          <div class="col-lg-2 pl"><p>Transporter Name</p></div>
          <div class="col-lg-2 pl">
            <input disabled type="text" name="txt_TRASPORTER_popup" id="txt_TRASPORTER_popup" value="{{ isset($DATA_HDR->TRANSPORTER_CODE)?$DATA_HDR->TRANSPORTER_CODE:NULL }} - {{ isset($DATA_HDR->TRANSPORTER_NAME)?$DATA_HDR->TRANSPORTER_NAME:NULL }}" class="form-control mandatory"  autocomplete="off" readonly/>
            <input type="hidden" name="TRASPORTER_NAME" id="TRASPORTER_NAME" value="{{isset($DATA_HDR->TRANSPORTERID_REF)?$DATA_HDR->TRANSPORTERID_REF:NULL}}" class="form-control" autocomplete="off" />                                                              
          </div>

          <div class="col-lg-2 pl"><p>Driver Name</p></div>
          <div class="col-lg-2 pl">
            <input disabled type="text" name="DRIVER_NAME" id="DRIVER_NAME" value="{{isset($DATA_HDR->DRIVER_NAME)?$DATA_HDR->DRIVER_NAME:NULL}}" autocomplete="off" class="form-control" maxlength="200"  >
          </div>

          <div class="col-lg-2 pl"><p>Purpose</p></div>
          <div class="col-lg-2 pl">
            <input disabled type="text" name="PURPOSE" id="PURPOSE" value="{{isset($DATA_HDR->PURPOSE)?$DATA_HDR->PURPOSE:NULL}}"  autocomplete="off" class="form-control mandatory" maxlength="200"  >
          </div>
        </div>

        <div class="row">
            <div class="col-lg-2 pl"><p>Total Value</p></div>
            <div class="col-lg-2 pl">
                <input type="text" name="TotalValue" id="TotalValue" class="form-control"  autocomplete="off" readonly  />
            </div>
        </div>
        
      </div>

                    <div class="container-fluid">

                        <div class="row">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
                                
                                <li><a data-toggle="tab" href="#udf">UDF</a></li>
                                <li><a data-toggle="tab" href="#TC">T & C</a></li>
                                <li><a data-toggle="tab" href="#CT">Calculation Template</a></li>
                               
                            </ul>
                            
                            
                            
                            <div class="tab-content">

                                <div id="Material" class="tab-pane fade in active">
                                    <div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;" >
                                        <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">
                                        <thead id="thead1"  style="position: sticky;top: 0">



<tr>
<th hidden><input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
<th>Job Work Invoice</th>
<th>Item Code</th>
<th>Item Description</th>
<th hidden>Main UOM</th>
<th>Qty (Main UOM)</th>
<th hidden>Alt UOM (AU)</th>
<th hidden>Qty (Alt UOM)</th>
<th>Main UOM</th>
<th>GRJ No</th>
<th>Store</th>
<th>Return Qty</th>
<th>Store Name</th>
<th hidden>Alt UOM (AU)</th>
<th hidden>Qty (Alt UOM)</th>
<th>Rate Per UoM</th>
<th>Amount before GST</th>
<th>GST Flag</th>
<th>IGST Rate %</th>
<th>IGST Amount</th>
<th>CGST Rate %</th>
<th>CGST Amount</th>
<th>SGST Rate %</th>
<th>SGST Amount</th>
<th>Total GST Amount</th>
<th>Total after GST</th>
<th>Action</th>
</tr>

<tbody>
@if(!empty($DATA_MAT))
@foreach($DATA_MAT as $key => $row)

<?php  
$item_unique_row_id =   $row->JWIID_REF."_".$row->GEJWOID_REF."_".$row->GRJID_REF."_".$row->JWCID_REF."_".$row->JWOID_REF."_".$row->PROID_REF."_".$row->SOID_REF."_".$row->SQID_REF."_".$row->SEID_REF."_".$row->ITEMID_REF;

if($row->IGST > 0 || $row->CGST > 0 || $row->SGST > 0){
  $GST_FLAGE_CHECKED  = "checked";
}
else{
  $GST_FLAGE_CHECKED  = "";
}

$TaxAmt     =   number_format((($row->RETURN_QTY*$row->RATE_PUOM) - 0),2, '.', '');
$IGSTAMT    =   number_format((($TaxAmt*$row->IGST)/100),2, '.', '');
$CGSTAMT    =   number_format((($TaxAmt*$row->CGST)/100),2, '.', '');
$SGSTAMT    =   number_format((($TaxAmt*$row->SGST)/100),2, '.', '');
$TOTGST     =   number_format(($IGSTAMT+$CGSTAMT+$SGSTAMT),2, '.', '');
$TOTAL      =   number_format(($TaxAmt+$IGSTAMT+$CGSTAMT+$SGSTAMT),2, '.', '');
?>

<tr  class="participantRow">

<td hidden><input type="hidden" id="{{$key}}" > </td>
<td hidden><input type="hidden" id="exist_{{$key}}" name="exist_{{$key}}" value="{{$item_unique_row_id}}" > </td>

<td hidden><input type="hidden" name="HIDNO_{{$key}}" id="HIDNO_{{$key}}" class="form-control three-digits" maxlength="13"  autocomplete="off"  /></td>
<td><input disabled type="text" name="txtJWID_popup_{{$key}}" id="txtJWID_popup_{{$key}}" value="{{ $row->JWINO }}" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
<td hidden><input type="hidden" name="JWID_REF_{{$key}}" id="JWID_REF_{{$key}}"  value="{{ $row->JWIID_REF }}" class="form-control" autocomplete="off" /></td>

<td hidden><input type="text" name="GEJID_REF_{{$key}}" id="GEJID_REF_{{$key}}" value="{{ $row->GEJID_REF }}" class="form-control" autocomplete="off" /></td>      
<td hidden><input type="text" name="GRJID_REF_{{$key}}" id="GRJID_REF_{{$key}}" value="{{ $row->GRJID_REF }}" class="form-control" autocomplete="off" /></td>      
<td hidden><input type="text" name="JWCID_REF_{{$key}}" id="JWCID_REF_{{$key}}" value="{{ $row->JWCID_REF }}" class="form-control" autocomplete="off" /></td>
<td hidden><input type="text" name="JWOID_REF_{{$key}}" id="JWOID_REF_{{$key}}" value="{{ $row->JWOID_REF }}" class="form-control" autocomplete="off" /></td>
<td hidden><input type="text" name="PROID_REF_{{$key}}" id="PROID_REF_{{$key}}" value="{{ $row->PROID_REF }}" class="form-control" autocomplete="off" /></td>
<td hidden><input type="text" name="SOID_REF_{{$key}}"  id="SOID_REF_{{$key}}"  value="{{ $row->SOID_REF }}" class="form-control" autocomplete="off" /></td>
<td hidden><input type="text" name="SQID_REF_{{$key}}"  id="SQID_REF_{{$key}}"  value="{{ $row->SQID_REF }}" class="form-control" autocomplete="off" /></td>
<td hidden><input type="text" name="SEID_REF_{{$key}}"  id="SEID_REF_{{$key}}"  value="{{ $row->SEID_REF }}" class="form-control" autocomplete="off" /></td>
  

<td><input disabled type="text" name="popupITEMID_{{$key}}" id="popupITEMID_{{$key}}" value="{{ $row->ICODE }}" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
<td hidden><input type="hidden" name="ITEMID_REF_{{$key}}" id="ITEMID_REF_{{$key}}" value="{{ $row->ITEMID_REF }}" class="form-control" autocomplete="off" /></td>
<td><input disabled type="text" name="ItemName_{{$key}}" id="ItemName_{{$key}}" value="{{ $row->ITEM_NAME }}" class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>


<td hidden><input type="hidden" name="SSO_DATE_{{$key}}" id="SSO_DATE_{{$key}}" class="form-control" ></td>
<td hidden><input type="hidden" name="Itemspec_{{$key}}" id="Itemspec_{{$key}}" class="form-control" ></td>
<td hidden><input type="hidden" name="REMARKS_{{$key}}" id="REMARKS_{{$key}}"   class="form-control" ></td>

<td hidden><input type="text" name="SQMUOM_{{$key}}" id="SQMUOM_{{$key}}" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
<td><input disabled type="text" name="SQMUOMQTY_{{$key}}" id="SQMUOMQTY_{{$key}}" value="{{ $row->RECV_QTY }}" class="form-control" maxlength="13"  autocomplete="off"  readonly/></td>
<td hidden><input type="text" name="SI_RATE_{{$key}}" id="SI_RATE_{{$key}}" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
<td hidden><input type="text" name="SQAUOM_{{$key}}" id="SQAUOM_{{$key}}" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
<td hidden><input type="text" name="SQAUOMQTY_{{$key}}" id="SQAUOMQTY_{{$key}}"  class="form-control" autocomplete="off"  readonly/></td>

<td><input disabled type="text" name="popupMUOM_{{$key}}" id="popupMUOM_{{$key}}" value="{{ $row->MAIN_UOM_CODE }}" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
<td hidden><input type="hidden" name="MAIN_UOMID_REF_{{$key}}" id="MAIN_UOMID_REF_{{$key}}" value="{{ $row->UOMID_REF }}" class="form-control"  autocomplete="off" style="width:75px;" /></td>

<td><input disabled type="text"   name="GRNNO_{{$key}}" id="GRNNO_{{$key}}" value="{{ $row->GRNNO }}" class="form-control"   autocomplete="off" style="width:150px;" readonly /></td>  

<td align="center"><a disabled class="btn checkstore" onclick="getStore(this.id)"  id="{{$key}}" ><i class="fa fa-clone"></i></a></td>
<td hidden ><input type="hidden" name="TotalHiddenQty_{{$key}}" id="TotalHiddenQty_{{$key}}" value="{{ $row->RETURN_QTY }}" ></td>
<td hidden ><input type="hidden" name="HiddenRowId_{{$key}}" id="HiddenRowId_{{$key}}" value="{{ $row->BATCH_QTY_REF }}" ></td>

      
<td><input disabled type="text" name="SO_QTY_{{$key}}" id="SO_QTY_{{$key}}" value="{{ $row->RETURN_QTY }}" class="form-control" maxlength="13" onkeypress="return isNumberDecimalKey(event,this)"  autocomplete="off" readonly  style="width:75px;" /></td>
<td><input disabled type="text"   name="STORE_NAME_{{$key}}" id="STORE_NAME_{{$key}}" value="{{ $row->STORE_NAME }}" class="form-control"   autocomplete="off" style="width:200px;" readonly /></td>  
<td hidden><input type="hidden" name="SO_FQTY_{{$key}}" id="SO_FQTY_{{$key}}" class="form-control three-digits" maxlength="13"  autocomplete="off"  readonly style="width:75px;" /></td>


<td hidden><input type="text" name="popupAUOM_{{$key}}" id="popupAUOM_{{$key}}" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
<td hidden><input type="hidden" name="ALT_UOMID_REF_{{$key}}" id="ALT_UOMID_REF_{{$key}}" class="form-control"  autocomplete="off"  readonly  style="width:75px;"  /></td>
<td hidden><input type="text" name="ALT_UOMID_QTY_{{$key}}" id="ALT_UOMID_QTY_{{$key}}" class="form-control three-digits"  autocomplete="off"   style="width:75px;" readonly /></td>
         
<td hidden><input type="hidden" name="DISCPER_{{$key}}" id="DISCPER_{{$key}}" value="<?php echo 0;?>" class="form-control four-digits" maxlength="8"  autocomplete="off" style="width: 50px;"   style="width:75px;" /></td>
<td hidden><input type="hidden" name="DISCOUNT_AMT_{{$key}}" id="DISCOUNT_AMT_{{$key}}" value="<?php echo 0;?>" class="form-control two-digits" maxlength="15"  autocomplete="off"   style="width:75px;" /></td>

<td><input disabled type="text" name="RATEPUOM_{{$key}}" id="RATEPUOM_{{$key}}" value="{{ $row->RATE_PUOM }}" class="form-control five-digits blurRate" maxlength="13"  autocomplete="off"  style="width:75px;" /></td>
<td><input disabled type="text" name="DISAFTT_AMT_{{$key}}" id="DISAFTT_AMT_{{$key}}" value="{{ $TaxAmt }}" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly style="width:75px;" /></td>

<td style="text-align:center;" ><input disabled type="checkbox" value="{{$row->GST}}" name='flagtype_{{$key}}' id="flagtype_{{$key}}" {{$GST_FLAGE_CHECKED}} ></td>

<td><input disabled type="text" name="IGST_{{$key}}" id="IGST_{{$key}}" value="{{ $row->IGST }}" class="form-control four-digits" maxlength="8"  autocomplete="off"  readonly style="width:75px;" /></td>
<td><input disabled type="text" name="IGSTAMT_{{$key}}" id="IGSTAMT_{{$key}}"  value="{{ $IGSTAMT }}" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly style="width:75px;" /></td>
<td><input disabled type="text" name="CGST_{{$key}}" id="CGST_{{$key}}" value="{{ $row->CGST }}" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly style="width:75px;" /></td>
<td><input disabled type="text" name="CGSTAMT_{{$key}}" id="CGSTAMT_{{$key}}"  value="{{ $CGSTAMT }}" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly style="width:75px;" /></td>
<td><input disabled type="text" name="SGST_{{$key}}" id="SGST_{{$key}}" value="{{ $row->SGST }}" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly style="width:75px;" /></td>
<td><input disabled type="text" name="SGSTAMT_{{$key}}" id="SGSTAMT_{{$key}}"  value="{{ $SGSTAMT }}" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly style="width:75px;" /></td>
<td><input disabled type="text" name="TGST_AMT_{{$key}}" id="TGST_AMT_{{$key}}" value="{{ $TOTGST }}" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly style="width:75px;" /></td>
<td><input disabled type="text" name="TOT_AMT_{{$key}}" id="TOT_AMT_{{$key}}" value="{{ $TOTAL }}" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly style="width:75px;" /></td>

<td align="center">
  <button disabled class="btn add material" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button> 
  <button disabled class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
</td>
</tr>
<tr></tr>
@endforeach 
@endif 
</tbody>
                                    </table>
                                    </div>	
                                </div>
                                

                                <div id="udf" class="tab-pane fade">
                                    <div class="table-responsive table-wrapper-scroll-y " style="margin-top:10px;height:280px;width:50%;">
                                        <table id="example4" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                            <tr >
                                                <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3"></th>
                                                <th>Value / Comments</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                        @if(!empty($objSOUDF))
                                            @foreach($objSOUDF as $Ukey => $Urow)
                                                <tr  class="participantRow4">
                                                    <!--
                                                    <td hidden><input type="hidden" name="SSI_UDFID_{{$Ukey}}" id="SSI_UDFID_{{$Ukey}}"  class="form-control" autocomplete="off" /></td>
                                                    -->
                                                    <td><input disabled type="text" name={{"popupUDFSOID_".$Ukey}} id={{"popupUDFSOID_".$Ukey}}  class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name={{"UDFSOID_REF_".$Ukey}}  id={{"UDFSOID_REF_".$Ukey}} class="form-control" value="{{$Urow->UDFJWRID_REF}}" autocomplete="off" /></td>
                                                    <td hidden><input type="hidden" name={{"UDFismandatory_".$Ukey}} id={{"UDFismandatory_".$Ukey}} class="form-control" autocomplete="off" /></td>
                                                    <td id={{"udfinputid_".$Ukey}}>
                                                    {{-- dynamic input --}} 
                                                    </td>
                                                    <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                </tr>
                                                <tr></tr>
                                            @endforeach 
                                            @else
                                            @foreach($objUdfSOData as $uindex=>$uRow)
                                              <tr  class="participantRow4">
                                                  <!--
                                                  <td hidden><input type="hidden" name="SSI_UDFID_{{$uindex}}" id="SSI_UDFID_{{$uindex}}"  class="form-control" autocomplete="off" /></td>
                                                  -->
                                                  <td><input disabled type="text" name={{"popupUDFSOID_".$uindex}} id={{"popupUDFSOID_".$uindex}} class="form-control" value="{{$uRow->LABEL}}" autocomplete="off"  readonly/></td>
                                                  <td hidden><input type="hidden" name={{"UDFSOID_REF_".$uindex}} id={{"UDFSOID_REF_".$uindex}} class="form-control" value="{{$uRow->UDFJWRID}}" autocomplete="off"   /></td>
                                                  <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->ISMANDATORY}}" class="form-control"   autocomplete="off" /></td>
                                                  <td id={{"udfinputid_".$uindex}} >
                                                    
                                                  </td>
                                                  <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                  
                                              </tr>
                                              <tr></tr>
                                            @endforeach  
                                        @endif 
                                        
                                    
                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                                <div id="TC" class="tab-pane fade">
            <div class="row" style="margin-top:10px;margin-left:3px;" >	
                <div class="col-lg-1 pl"><p>T&C Template</p></div>
                <div class="col-lg-2 pl">
                <input disabled type="text" name="txtTNCID_popup" id="txtTNCID_popup" class="form-control"  autocomplete="off"  readonly/>
                @if(!empty($objSOTNC))
                    <input type="hidden" name="TNCID_REF" id="TNCID_REF" class="form-control" value="{{$objSOTNC[0]->TNCID_REF}}" autocomplete="off" />
                @else
                    <input type="hidden" name="TNCID_REF" id="TNCID_REF" class="form-control"  autocomplete="off" />
                @endif
                </div>
            </div>
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:240px;width:50%;">
                <table id="example3" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                    <tr >
                        <th>Terms & Conditions Description<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2"  value="{{$objCount2}}" /></th>
                        <th>Value / Comment</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="tncbody">
                @if(!empty($objSOTNC))
                  @foreach($objSOTNC as $Tkey => $Trow)
                      <tr  class="participantRow3">
                          <td><input disabled type="text" name={{"popupTNCDID_".$Tkey}} id={{"popupTNCDID_".$Tkey}} class="form-control"  autocomplete="off"  readonly/></td>
                          <td hidden><input type="hidden" name={{"TNCDID_REF_".$Tkey}} id={{"TNCDID_REF_".$Tkey}} class="form-control" value="{{$Trow->TNCDID_REF}}" autocomplete="off" /></td>
                          <td hidden><input type="hidden" name={{"TNCismandatory_".$Tkey}} id={{"TNCismandatory_".$Tkey}} class="form-control" autocomplete="off" /></td>
                          <td id={{"tdinputid_".$Tkey}}>
                          {{-- dynamic input --}} 
                          </td>
                          <td align="center" ><button class="btn add TNC" title="add" data-toggle="tooltip"  type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DTNC" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                      </tr>
                      <tr></tr>
                  @endforeach 
                  @else
                      <tr  class="participantRow3">
                          <td><input disabled type="text" name="popupTNCDID_0" id="popupTNCDID_0" class="form-control"  autocomplete="off"  readonly/></td>
                          <td hidden><input type="hidden" name="TNCDID_REF_0" id="TNCDID_REF_0" class="form-control" autocomplete="off" /></td>
                          <td hidden><input type="hidden" name="TNCismandatory_0" id="TNCismandatory_0" class="form-control" autocomplete="off" /></td>
                          <td id="tdinputid_0">
                            {{-- dynamic input --}} 
                          </td>
                              <td align="center" ><button class="btn add TNC" title="add" data-toggle="tooltip"  type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DTNC" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                          </tr>
                      <tr></tr>
              @endif
                    </tbody>
                </table>
            </div>
        </div>



                               
                                
                                <div id="CT" class="tab-pane fade">
                                    <div class="row" style="margin-top:10px;margin-left:3px;" >	
                                        <div class="col-lg-2 pl"><p>Calculation Template</p></div>
                                        <div class="col-lg-2 pl">
                                        <input disabled type="text" name="txtCTID_popup" id="txtCTID_popup" class="form-control"  autocomplete="off"  readonly/>
                                        @if(!empty($objSOCAL))
                                         <input type="hidden" name="CTID_REF" id="CTID_REF" class="form-control" value="{{$objSOCAL[0]->CTID_REF}}" autocomplete="off" />
                                         @else
                                         <input type="hidden" name="CTID_REF" id="CTID_REF" class="form-control" autocomplete="off" />
                                        @endif
                                        </div>
                                    </div>
                                    <div class="table-responsive table-wrapper-scroll-y" style="height:240px;margin-top:10px;" >
                                        <table id="example5" class="display nowrap table table-striped table-bordered itemlist " width="100%" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                                <tr>
                                                    <th>Calculation Component<input class="form-control" type="hidden" name="Row_Count4" id ="Row_Count4"></th>
                                                    <th>Rate</th>
                                                    <th>Value</th>
                                                    <th>GST Applicable</th>
                                                    <th>IGST Rate</th>
                                                    <th>IGST Amount</th>
                                                    <th>CGST Rate</th>
                                                    <th>CGST Amount</th>
                                                    <th>SGST Rate</th>
                                                    <th>SGST Amount</th>
                                                    <th>Total GST Amount</th>
                                                    <th>As per Actual</th>
                                                    <th width="8%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_ctid">
                                            @if(!empty($objSOCAL))
                                                @foreach($objSOCAL as $Ckey => $Crow)
                                                    <tr  class="participantRow5">
                                                        <!--
                                                        <td hidden><input type="hidden" name="SSI_CALID_{{$Ckey}}" id="SSI_CALID_{{$Ckey}}"  class="form-control" autocomplete="off" /></td>
                                                        -->
                                                        <td><input disabled type="text" name={{"popupTID_".$Ckey}} id={{"popupTID_".$Ckey}}  class="form-control"  autocomplete="off"  readonly/></td>
                                                        <td hidden><input type="hidden" name={{"TID_REF_".$Ckey}}  id={{"TID_REF_".$Ckey}}  class="form-control" autocomplete="off" value="{{$Crow->TID_REF}}" /></td>
                                                        <td><input disabled type="text" name={{"RATE_".$Ckey}}  id={{"RATE_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->RATE}}"  readonly/></td>
                                                        <td hidden><input type="hidden" name={{"BASIS_".$Ckey}} id={{"BASIS_".$Ckey}} class="form-control" autocomplete="off"  /></td>
                                                        <td hidden><input type="hidden" name={{"SQNO_".$Ckey}} id={{"SQNO_".$Ckey}} class="form-control" autocomplete="off" /></td>
                                                        <td hidden><input type="hidden" name={{"FORMULA_".$Ckey}} id={{"FORMULA_".$Ckey}} class="form-control" autocomplete="off" /></td>
                                                        <td><input disabled type="text" name={{"VALUE_".$Ckey}} id={{"VALUE_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$Crow->VALUE}}" readonly/></td>
                                                        <td style="text-align:center;" ><input disabled type="checkbox" class="filter-none" name={{"calGST_".$Ckey}} id={{"calGST_".$Ckey}} {{$Crow->GST == 1 ? 'checked' : ''}}   ></td>
                                                        
                                                        <td><input disabled type="text" name={{"calIGST_".$Ckey}} id={{"calIGST_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->IGST}}" readonly/></td>
                                                        <td><input disabled type="text" name={{"AMTIGST_".$Ckey}} id={{"AMTIGST_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td><input disabled type="text" name={{"calCGST_".$Ckey}} id={{"calCGST_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->CGST}}" readonly/></td>
                                                        <td><input disabled type="text" name={{"AMTCGST_".$Ckey}} id={{"AMTCGST_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td><input disabled type="text" name={{"calSGST_".$Ckey}} id={{"calSGST_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->SGST}}" readonly/></td>
                                                        <td><input disabled type="text" name={{"AMTSGST_".$Ckey}} id={{"AMTSGST_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td><input disabled type="text" name={{"TOTGSTAMT_".$Ckey}} id={{"TOTGSTAMT_".$Ckey}} class="form-control two-digits"  maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td style="text-align:center;"><input disabled type="checkbox" class="filter-none" name={{"calACTUAL_".$Ckey}} id={{"calACTUAL_".$Ckey}}  ></td>
                                                        <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                    </tr>
                                                    <tr></tr>
                                                @endforeach 
                                                @else
                                                <tr  class="participantRow5">
                                                    <!--
                                                    <td hidden><input type="hidden" name="SSI_CALID_0" id="SSI_CALID_0"  class="form-control" autocomplete="off" /></td>
                                                    -->
                                                    <td><input disabled type="text" name="popupTID_0" id="popupTID_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name="TID_REF_0" id="TID_REF_0" class="form-control" autocomplete="off" /></td>
                                                    <td><input disabled type="text" name="RATE_0" id="RATE_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name="BASIS_0" id="BASIS_0" class="form-control" autocomplete="off" /></td>
                                                    <td hidden><input type="hidden" name="SQNO_0" id="SQNO_0" class="form-control" autocomplete="off" /></td>
                                                    <td><input disabled type="text" name="VALUE_0" id="VALUE_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td style="text-align:center;" ><input disabled type="checkbox" class="filter-none" name="calGST_0" id="calGST_0" value="" ></td>
                                                    <td><input disabled type="text" name="calIGST_0" id="calIGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                    <td><input disabled type="text" name="AMTIGST_0" id="AMTIGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td><input disabled type="text" name="calCGST_0" id="calCGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                    <td><input disabled type="text" name="AMTCGST_0" id="AMTCGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td><input disabled type="text" name="calSGST_0" id="calSGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                    <td><input disabled type="text" name="AMTSGST_0" id="AMTSGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td><input disabled type="text" name="TOTGSTAMT_0" id="TOTGSTAMT_0" class="form-control two-digits"  maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td style="text-align:center;"><input disabled type="checkbox" class="filter-none" name="calACTUAL_0" id="calACTUAL_0" value=""   ></td>
                                                    <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                </tr>
                                                <tr></tr>
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
<div id="StoreModal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:60%;z-index:1">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='StoreModalClose' >&times;</button>
      </div>
      <div class="modal-body">
	      <div class="tablename"><p>Store Details</p></div>
        <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="StoreTable" class="display nowrap table  table-striped table-bordered" style="width: 100%;font-size:14px;" >
          </table>
        </div>
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
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" onclick="getFocus()" style="display:none;margin-left: 90px;">
            <div id="alert-active" class="activeOk1"></div>OK</button>

            <input type="hidden" id="FocusId" >
        </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- TNC Header Dropdown -->
<div id="TNCIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='TNCID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>T&C</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="TNCIDTable" class="display nowrap table  table-striped table-bordered" >
    <thead>


    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Name</th>
    </tr>

    </thead>
    <tbody>

    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="TNCcodesearch" class="form-control" onkeyup="TNCCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="TNCnamesearch" class="form-control" onkeyup="TNCNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="TNCIDTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
         
        </thead>
        <tbody>
        @foreach ($objTNCHeader as $tncindex=>$tncRow)
        <tr >
        <td class="ROW1"> <input type="checkbox" name="SELECT_VID_REF[]" id="tncidcode_{{ $tncindex }}" class="clstncid" value="{{ $tncRow-> TNCID }}" ></td>
          <td class="ROW2">{{ $tncRow-> TNC_CODE }}
          <input type="hidden" id="txttncidcode_{{ $tncindex }}" data-desc="{{ $tncRow-> TNC_CODE }} - {{ $tncRow-> TNC_DESC }}"  
          value="{{ $tncRow-> TNCID }}"/></td><td class="ROW3">{{ $tncRow-> TNC_DESC }}</td>
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

<!-- TNC Details Dropdown -->
<div id="tncdetpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='tncdet_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Terms & Condition Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="TNCDetTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>TNC Name</th>
            <th>Value Type</th>
    </tr>
    <tr hidden>
            <input type="hidden" name="fieldid" id="hdn_tncdet"/>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="tncdetcodesearch" onkeyup="TNCDetCodeFunction()">
    </td>
    <td>
    <input type="text" id="tncdetnamesearch" onkeyup="TNCDetNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="TNCDetTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_tncdetails">       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- calculation template alert popup -->
<div id="CTIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='CTID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Calculation Template</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CTIDTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
 

    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Name</th>
    </tr>

    </thead>
    <tbody>


    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="CTIDcodesearch" class="form-control" onkeyup="CTIDCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="CTIDnamesearch" class="form-control" onkeyup="CTIDNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="CTIDTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
         
        </thead>
        <tbody>
        @foreach ($objCalculationHeader as $calindex=>$calRow)
        <tr id="CTIDcode_{{ $calindex }}" class="clsctid">
        <td class="ROW1"> <input type="checkbox" name="SELECT_CTID_REF[]" id="CTIDcode_{{ $calindex }}" class="clsctid" value="{{ $calRow-> CTID }}" ></td>
          <td class="ROW2">{{ $calRow-> CTCODE }}
          <input type="hidden" id="txtCTIDcode_{{ $calindex }}" data-desc="{{ $calRow-> CTCODE }} - {{ $calRow-> CTDESCRIPTION }}"  
          value="{{ $calRow-> CTID }}"/></td><td class="ROW3">{{ $calRow-> CTDESCRIPTION }}</td>
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
        <td class="ROW2"><input type="text" id="vendorcodesearch" class="form-control" onkeyup="VendorCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="vendornamesearch" class="form-control" onkeyup="VendorNameFunction()"></td>
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

<div id="JWID_REFpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='JWID_REF_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Job Work Invoice</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SalesQuotationTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_sqid"/>
            <input type="hidden" name="fieldid2" id="hdn_sqid2"/>
            <input type="hidden" name="fieldid2" id="hdn_sqid3"/>
            </td>
          </tr>
 
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Job Work Invoice No</th>
      <th class="ROW3">Job Work Invoice Date</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="SalesQuotationcodesearch" class="form-control" onkeyup="SalesQuotationCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="SalesQuotationnamesearch" class="form-control" onkeyup="SalesQuotationNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="SalesQuotationTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_SQ">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="TRASPORTER_OPEN_POPUP" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='TRASPORTER_CLOSE_POPUP' >&times;</button></div>
      <div class="modal-body">
        <div class="tablename"><p>Transporter Name</p></div>
        <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="TRASPORTER_TABLE" class="display nowrap table  table-striped table-bordered" >
            <thead>
              <tr id="none-select" class="searchalldata" hidden>
                <td> 
                  <input type="hidden" id="HIDDEN_TRASPORTER_ID"/>
                  <input type="hidden" id="HIDDEN_TRASPORTER_ID2"/>
                </td>
              </tr>
              
              <tr>
                <th class="ROW1">Select</th> 
                <th class="ROW2">Code</th>
                <th class="ROW3">Name</th>
              </tr>
            </thead>

            <tbody>
       

              <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="TRASPORTER_CODE_SEARCH" class="form-control" onkeyup="TRASPORTER_CODE_FUNCTION()"></td>
        <td class="ROW3"><input type="text" id="TRASPORTER_NAME_SEARCH" class="form-control" onkeyup="TRASPORTER_NAME_FUNCTION()"></td>
      </tr>
            </tbody>
          </table>

          <table id="TRASPORTER_TABLE2" class="display nowrap table  table-striped table-bordered" >
            <thead id="thead2"></thead>
            <tbody id="TBODY_TRASPORTER">    
              @if(!empty(isset($objTRASPORTER) && $objTRASPORTER))
                @foreach($objTRASPORTER as $key=>$val)
                  <tr >
                  <td class="ROW1"> <input type="checkbox" name="SELECT_TRASPORTER_NAME[]" id="TRASPORTER_CODE_{{$val->ID}}"  class="CLASS_TRASPORTER" value="{{$val->ID}}" ></td>
                    <td class="ROW2">{{$val->CODE}} </td>
                    <td class="ROW3">{{$val->DESC}}</td>
                    <td hidden><input type="text" id="txtTRASPORTER_CODE_{{$val->ID}}" data-desc="{{$val->CODE}} - {{$val->DESC}}" value="{{$val->ID}}"/></td>
                  </tr>
                @endforeach
              @else
              <tr><td colspan="2">Record not found.</td></tr>
              @endif 
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
            
            <td> <input type="hidden" name="fieldid" id="hdn_ItemID"/>
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
            <input type="hidden" name="fieldid20" id="hdn_ItemID20"/>
            </td>
      </tr>
     
      <tr>
            <th style="width:8%;" id="all-check"  >Select</th>
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
    <input type="text" id="Itemcodesearch" class="form-control" onkeyup="ItemCodeFunction()">
    </td>
    <td style="width:10%;">
    <input type="text" id="Itemnamesearch" class="form-control" onkeyup="ItemNameFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemUOMsearch" class="form-control" onkeyup="ItemUOMFunction()">
    </td>
    <td style="width:8%;"> 
    <input type="text" id="ItemQTYsearch" class="form-control" onkeyup="ItemQTYFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemGroupsearch" class="form-control" onkeyup="ItemGroupFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemCategorysearch" class="form-control" onkeyup="ItemCategoryFunction()">
    </td>
    <td style="width:8%;">
        <input type="text" id="ItemBUsearch" class="form-control" onkeyup="ItemBUFunction()">
    </td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}} >
      <input type="text" id="ItemAPNsearch" class="form-control" onkeyup="ItemAPNFunction()">
    </td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}} >
      <input type="text" id="ItemCPNsearch" class="form-control" onkeyup="ItemCPNFunction()">
    </td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}} >
      <input type="text" id="ItemOEMPNsearch" class="form-control" onkeyup="ItemOEMPNFunction()">
    </td>

    <td style="width:8%;">
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
//====================================== SHORTING ======================================
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



//====================================== TNC HEADER ======================================

let tnctid = "#TNCIDTable2";
let tnctid2 = "#TNCIDTable";
let tncheaders = document.querySelectorAll(tnctid2 + " th");

tncheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(tnctid, ".clstncid", "td:nth-child(" + (i + 1) + ")");
  });
});

function TNCCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("TNCcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("TNCIDTable2");
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

function TNCNameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("TNCnamesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("TNCIDTable2");
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

$('#txtTNCID_popup').click(function(event){
  showSelectedCheck($("#TNCID_REF").val(),"SELECT_TNCID_REF");
      $("#TNCIDpopup").show();
      event.preventDefault();
  });

  $("#TNCID_closePopup").click(function(event){
    $("#TNCIDpopup").hide();
  });

  $(".clstncid").click(function(){
    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");
    
    $('#txtTNCID_popup').val(texdesc);
    $('#TNCID_REF').val(txtval);
    $("#TNCIDpopup").hide();
    $("#TNCcodesearch").val(''); 
    $("#TNCnamesearch").val(''); 
    TNCCodeFunction();
  
    var customid = txtval;
    if(customid!=''){
      
      $('#tbody_tncdetails').html('<tr><td colspan="2">Please wait..</td></tr>');
    

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[$FormId,"gettncdetails2"])}}',
            type:'POST',
            data:{'id':customid},
            success:function(data) {
                $('#tncbody').html(data);
                bindTNCDetailsEvents();
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              $('#tncbody').html('');
            },
        });            
        $.ajax({
            url:'{{route("transaction",[$FormId,"gettncdetails3"])}}',
            type:'POST',
            data:{'id':customid},
            success:function(data) {
                $('#Row_Count2').val(data);
                bindTNCDetailsEvents();
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              $('#Row_Count2').val('0');
            },
        });
        $.ajax({
            url:'{{route("transaction",[$FormId,"gettncdetails"])}}',
            type:'POST',
            data:{'id':customid},
            success:function(data) {
                $('#tbody_tncdetails').html(data);
                bindTNCDetailsEvents();
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              $('#tbody_tncdetails').html('');
            },
        });        
    }
    event.preventDefault();
  });

      

//====================================== TNC DETAILS ======================================

let tncdettid = "#TNCDetTable2";
let tncdettid2 = "#TNCDetTable";
let tncdetheaders = document.querySelectorAll(tncdettid2 + " th");


tncdetheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(tncdettid, ".clstncdet", "td:nth-child(" + (i + 1) + ")");
  });
});

function TNCDetCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("tncdetcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("TNCDetTable2");
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

function TNCDetNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("tncdetnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("TNCDetTable2");
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


$("#tncdet_closePopup").on("click",function(event){ 
     $("#tncdetpopup").hide();
});

function bindTNCDetailsEvents(){
        $('.clstncdet').dblclick(function(){
    
            var id = $(this).attr('id');
            var txtid =    $("#txt"+id+"").val();
            var txtname =   $("#txt"+id+"").data("desc");
            var fieldid2 = $(this).find('[id*="tncvalue"]').attr('id');
            var txtvaluetype = $.trim($(this).find('[id*="tncvalue"]').text().trim());
            var txtismandatory =  $("#txt"+fieldid2+"").val();
            var txtdescription =  $("#txt"+fieldid2+"").data("desc");
            
            var txtcol = $('#hdn_tncdet').val();
            $("#"+txtcol).val(txtname);
            $("#"+txtcol).parent().parent().find("[id*='TNCDID_REF']").val(txtid);
            $("#"+txtcol).parent().parent().find("[id*='TNCismandatory']").val(txtismandatory);
            
            var txt_id4 = $("#"+txtcol).parent().parent().find("[id*='tdinputid']").attr('id');  //<td> id 

            var strdyn = txt_id4.split('_');
            var lastele =   strdyn[strdyn.length-1];

            var dynamicid = "tncdetvalue_"+lastele;

            var chkvaltype =  txtvaluetype.toLowerCase();
            var strinp = '';

            if(chkvaltype=='date'){

              strinp = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';       

            }else if(chkvaltype=='time'){
              strinp= '<input type="time" placeholder="h:i" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';

            }else if(chkvaltype=='numeric'){
              strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';

            }else if(chkvaltype=='text'){

              strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';
            
            }else if(chkvaltype=='boolean'){

              strinp = '<input type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" /> ';
            
            }else if(chkvaltype=='combobox'){
              if(txtdescription !== undefined)
              {
                var strarray = txtdescription.split(',');
                
                var opts = '';

                for (var i = 0; i < strarray.length; i++) {
                  opts = opts + '<option value="'+strarray[i]+'">'+strarray[i]+'</option> ';
                }

                strinp = '<select name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" required>'+opts+'</select>' ;
              }
            }

            $('#'+txt_id4).html('');  
            $('#'+txt_id4).html(strinp);   //set dynamic input

            $("#tncdetpopup").hide();
            $("#tncdetcodesearch").val(''); 
            $("#tncdetnamesearch").val(''); 
            TNCDetCodeFunction();
            event.preventDefault();
            
        });
  }

//====================================== CALCULATION TEMPLATE ======================================

let cttid = "#CTIDTable2";
  let cttid2 = "#CTIDTable";
  let ctheaders = document.querySelectorAll(cttid2 + " th");


  ctheaders.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(cttid, ".clsctid", "td:nth-child(" + (i + 1) + ")");
    });
  });

function CTIDCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("CTIDcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("CTIDTable2");
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

function CTIDNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("CTIDnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("CTIDTable2");
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

$('#txtCTID_popup').click(function(event){
  showSelectedCheck($("#CTID_REF").val(),"SELECT_CTID_REF");
    $("#CTIDpopup").show();
    event.preventDefault();
});

$("#CTID_closePopup").click(function(event){
  $("#CTIDpopup").hide();
});

$(".clsctid").click(function(){
  var fieldid = $(this).attr('id');
  var txtval =    $("#txt"+fieldid+"").val();
  var texdesc =   $("#txt"+fieldid+"").data("desc");
  
  $('#txtCTID_popup').val(texdesc);
  $('#CTID_REF').val(txtval);
  $("#CTIDpopup").hide();
  $("#CTIDcodesearch").val(''); 
  $("#CTIDnamesearch").val(''); 
  CTIDCodeFunction();

  var customid = txtval;
  if(customid!=''){
    
    $('#tbody_ctiddetails').html('<tr><td colspan="2">Please wait..</td></tr>');
    $('#tbody_ctid').html('<tr><td colspan="2">Please wait..</td></tr>');
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{route("transaction",[$FormId,"getcalculationdetails2"])}}',
          type:'POST',
          data:{'id':customid},
          success:function(data) {
              $('#tbody_ctid').html(data);
              bindCTIDDetailsEvents();
              bindGSTCalTemplate();
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $('#tbody_ctid').html('');
          },
      });
      $.ajax({
          url:'{{route("transaction",[$FormId,"getcalculationdetails3"])}}',
          type:'POST',
          data:{'id':customid},
          success:function(data) {
            $('#Row_Count4').val(data);
              bindCTIDDetailsEvents();
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $('#Row_Count4').val('0');
          },
      });
      $.ajax({
          url:'{{route("transaction",[$FormId,"getcalculationdetails"])}}',
          type:'POST',
          data:{'id':customid},
          success:function(data) {
              $('#tbody_ctiddetails').html(data);
              bindCTIDDetailsEvents();
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $('#tbody_ctiddetails').html('');
          },
      }); 
        
  }
  event.preventDefault();
});

function bindGSTCalTemplate(){ 
  $('#CT').find('.participantRow5').each(function()
    { 
        var basis = $(this).find('[id*="BASIS"]').val();
        var sqno = $(this).find('[id*="SQNO"]').val();
        var formula = $(this).find('[id*="FORMULA"]').val();
        var rate = $(this).find('[id*="RATE"]').val();
        var amountnet = $(this).find('[id*="VALUE"]').val();
        var netTaxableAmount = 0.00;
        var netGSTAmount = 0.00;
        var netTotalAmount = 0.00;
        var totamount = 0.00;
        var tamt = 0.00;
        var IGSTamt = 0.00;
        var CGSTamt = 0.00;
        var SGSTamt = 0.00;
        var TotGSTamt = 0.00;

        $('#Material').find('.participantRow').each(function()
        {                       
          var TaxableAmount = $(this).find('[id*="DISAFTT_AMT"]').val();
          if (!isNaN(TaxableAmount) && TaxableAmount.length !== 0) {
            netTaxableAmount += parseFloat(TaxableAmount);
            }                      
          
          var GSTAmount = $(this).find('[id*="TGST_AMT"]').val();
          if (!isNaN(GSTAmount) && GSTAmount.length !== 0) {
            netGSTAmount += parseFloat(GSTAmount);
            }
          
          var TotalAmount = $(this).find('[id*="TOT_AMT"]').val();
          if (!isNaN(TotalAmount) && TotalAmount.length !== 0) {
            netTotalAmount += parseFloat(TotalAmount);
            }
        })
        var IGST = $('#IGST_0').val();
        var CGST = $('#CGST_0').val();
        var SGST = $('#SGST_0').val();
        
          if(formula == '')
          {
            if(rate > 0)
            { 
              if(basis == 'Item Taxable Amount')
              {
                totamount = parseFloat((rate * netTaxableAmount)/100).toFixed(2);
              }
              if(basis == 'Item GST Amount')
              {
                totamount = parseFloat((rate * netGSTAmount)/100).toFixed(2);
              }
              if(basis == 'Amount After GST Item')
              {
                totamount = parseFloat((rate * netTotalAmount)/100).toFixed(2);
              }
            }
            else
            {
              totamount = amountnet;
            }
          }
          else
          {
            if(basis == 'Item Taxable Amount')
            {
              var basis1 = '( '+netTaxableAmount+' * '+rate+' ) / 100';
              var basis2 = netTaxableAmount;
              var rate1 = rate +' ) / 100';
              if(formula.indexOf("BASIS*RATE") != -1){
                var formula1 = formula.replace ("BASIS*RATE", basis1);
                tamt = eval(formula1);
                totamount = parseFloat((tamt * rate)/100).toFixed(2);
              }
              else if(formula.indexOf("BASIS") != -1){
                var formula1 = formula.replace ("BASIS", basis2);
                tamt = eval(formula1);
                totamount = parseFloat((tamt * rate)/100).toFixed(2);
              }
              else if(formula.indexOf("RATE") != -1){
                var formula1 = formula.replace ("RATE", rate1);
                tamt = eval(formula1);
                totamount = parseFloat(( tamt * rate)/100).toFixed(2);
              }
            }
            if(basis == 'Item GST Amount')
            {
              var basis1 = '('+netGSTAmount+'*'+rate+')/100';
              var basis2 = netGSTAmount;
              var rate1 = rate+')/100';
              if(formula.indexOf("BASIS*RATE") != -1){
                var formula1 = formula.replace ("BASIS*RATE", basis1);
                tamt = eval(formula1);
                totamount = parseFloat((tamt * rate)/100).toFixed(2);
              }
              else if(formula.indexOf("BASIS") != -1){
                var formula1 = formula.replace ("BASIS", basis2);
                tamt = eval(formula1);
                totamount = parseFloat((tamt * rate)/100).toFixed(2);
              }
              else if(formula.indexOf("RATE") != -1){
                var formula1 = formula.replace ("RATE", rate1);
                tamt = eval(formula1);
                totamount = parseFloat(( tamt * rate)/100).toFixed(2);
              }
            }
            if(basis == 'Amount After GST Item')
            {
              var basis1 = '( '+netTotalAmount+' * '+rate+' ) / 100';
              var basis2 = netTotalAmount;
              var rate1 = rate+' ) / 100';
              if(formula.indexOf("BASIS*RATE") != -1){
                var formula1 = formula.replace ("BASIS*RATE", basis1);
                tamt = eval(formula1);
                totamount = parseFloat((tamt * rate)/100).toFixed(2);
              }
              else if(formula.indexOf("BASIS") != -1){
                var formula1 = formula.replace ("BASIS", basis2);
                tamt = eval(formula1);
                totamount = parseFloat((tamt * rate)/100).toFixed(2);
              }
              else if(formula.indexOf("RATE") != -1){
                var formula1 = formula.replace ("RATE", rate1);
                tamt = eval(formula1);
                totamount = parseFloat(( tamt * rate)/100).toFixed(2);
              }
            }
            
          }
          $(this).find('[id*="VALUE_"]').val(totamount);
            IGSTamt = parseFloat((IGST * totamount)/100).toFixed(2);
            CGSTamt = parseFloat((CGST * totamount)/100).toFixed(2);
            SGSTamt = parseFloat((SGST * totamount)/100).toFixed(2);
            TotGSTamt = parseFloat(parseFloat(IGSTamt)+parseFloat(CGSTamt)+parseFloat(SGSTamt)).toFixed(2);
        if($(this).find('[id*="calGST"]').is(":checked") != false)
        {
          if (IGST != '')
          {
          $(this).find('[id*="calIGST_"]').val(IGST);
          $(this).find('[id*="AMTIGST_"]').val(IGSTamt);
          $(this).find('[id*="calIGST_"]').removeAttr('readonly');
          }
          else
          {
            $(this).find('[id*="calIGST_"]').val('0');
            $(this).find('[id*="AMTIGST_"]').val('0');
            $(this).find('[id*="calIGST_"]').prop('readonly',true);
            
          }
          if (CGST != '')
          {
          $(this).find('[id*="calCGST_"]').val(CGST);
          $(this).find('[id*="AMTCGST_"]').val(CGSTamt);
          $(this).find('[id*="calCGST_"]').removeAttr('readonly');
          }
          else
          {
            $(this).find('[id*="calCGST_"]').val('0');
            $(this).find('[id*="AMTCGST_"]').val('0');
            $(this).find('[id*="calCGST_"]').prop('readonly',true);
          }
          if (SGST != '')
          {
          $(this).find('[id*="calSGST_"]').val(SGST);
          $(this).find('[id*="AMTSGST_"]').val(SGSTamt);
          $(this).find('[id*="calSGST_"]').removeAttr('readonly');
          }
          else
          {
            $(this).find('[id*="calSGST_"]').val('0');
            $(this).find('[id*="AMTSGST_"]').val('0');
            $(this).find('[id*="calSGST_"]').prop('readonly',true);
          }
          $(this).find('[id*="TOTGSTAMT_"]').val(TotGSTamt);
        }
        else
        {
          $(this).find('[id*="calSGST_"]').val('0');
          $(this).find('[id*="AMTSGST_"]').val('0');
          $(this).find('[id*="calCGST_"]').val('0');
          $(this).find('[id*="AMTCGST_"]').val('0');
          $(this).find('[id*="calIGST_"]').val('0');
          $(this).find('[id*="AMTIGST_"]').val('0');
          $(this).find('[id*="TOTGSTAMT_"]').val('0');
          $(this).find('[id*="calIGST_"]').prop('readonly',true);
          $(this).find('[id*="calCGST_"]').prop('readonly',true);
          $(this).find('[id*="calSGST_"]').prop('readonly',true);
        }
    });
    var totalvalue = 0.00;
    var tvalue = 0.00;
    var ctvalue = 0.00;
    var ctgstvalue = 0.00;
    $('#Material').find('.participantRow').each(function()
    {
      tvalue = $(this).find('[id*="TOT_AMT"]').val();
      totalvalue = parseFloat(totalvalue) + parseFloat(tvalue);
      totalvalue = parseFloat(totalvalue).toFixed(2);
    });
    if($('#CTID_REF').val() != '')
    {
      $('#CT').find('.participantRow5').each(function()
      {
        ctvalue = $(this).find('[id*="VALUE"]').val();
        ctgstvalue = $(this).find('[id*="TOTGSTAMT"]').val();
        totalvalue = parseFloat(totalvalue) + parseFloat(ctvalue);
        totalvalue = parseFloat(totalvalue) + parseFloat(ctgstvalue);
        totalvalue = parseFloat(totalvalue).toFixed(2);
      });
    }
    $('#TotalValue').val(totalvalue);
    event.preventDefault();
}


//====================================== CALCULATION DETAILS ======================================

let ctiddettid = "#CTIDDetTable2";
let ctiddettid2 = "#CTIDDetTable";
let ctiddetheaders = document.querySelectorAll(ctiddettid2 + " th");


ctiddetheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(ctiddettid, ".clsctiddet", "td:nth-child(" + (i + 1) + ")");
  });
});

function CTIDDetCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("CTIDdetcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("CTIDDetTable2");
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

function CTIDDetNameFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("CTIDdetnamesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("CTIDDetTable2");
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

function CTIDDetRateFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("CTIDdetratesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("CTIDDetTable2");
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

function CTIDDetAmountFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("CTIDdetamountsearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("CTIDDetTable2");
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

function CTIDDetFormulaFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("CTIDdetformulasearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("CTIDDetTable2");
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

$("#ctiddet_closePopup").on("click",function(event){ 
     $("#ctiddetpopup").hide();
});

function bindCTIDDetailsEvents(){
$('.clsctiddet').dblclick(function(){    
    var id = $(this).attr('id');
    var txtid =    $("#txt"+id+"").val();
    var txtname =   $("#txt"+id+"").data("desc");
    var fieldid2 = $(this).find('[id*="ctidbasis"]').attr('id');
    var txtbasis = $.trim($(this).find('[id*="ctidbasis"]').text().trim());
    var txtactual =  $("#txt"+fieldid2+"").val();
    var txtgst =  $("#txt"+fieldid2+"").data("desc");
    var fieldid3 = $(this).find('[id*="ctidformula_"]').attr('id');
    var txtrate = $.trim($(this).find('[id*="ctidformula_"]').text().trim());
    var txtsqno =  $("#txt"+fieldid3+"").val();
    var txtformula =  $("#txt"+fieldid3+"").data("desc");
    var txtamount = $.trim($(this).find('[id*="ctidamount_"]').text().trim());
    var txtcol = $('#hdn_ctiddet').val();
    if(intRegex.test(txtrate)){
      txtrate = (txtrate +'.00');
    }
    $("#"+txtcol).val(txtname);
    $("#"+txtcol).parent().parent().find("[id*='TID_REF']").val(txtid);
    $("#"+txtcol).parent().parent().find("[id*='RATE']").val(txtrate);
    $("#"+txtcol).parent().parent().find("[id*='BASIS']").val(txtbasis);
    
    $("#"+txtcol).parent().parent().find("[id*='FORMULA']").val(txtformula);
    $("#"+txtcol).parent().parent().find("[id*='SQNO']").val(txtsqno); 

    if(txtactual == 1)
    {
      $("#"+txtcol).parent().parent().find("[id*='ACTUAL']").prop('checked','true');
    }     
    else
    {
      $("#"+txtcol).parent().parent().find("[id*='ACTUAL']").removeAttr('checked');
    }  

    if(txtgst == 1)
    {
      $("#"+txtcol).parent().parent().find("[id*='calGST']").prop('checked','true');
      $("#"+txtcol).parent().parent().find("[id*='calIGST']").removeAttr('readonly');
      $("#"+txtcol).parent().parent().find("[id*='AMTIGST']").removeAttr('readonly');
      $("#"+txtcol).parent().parent().find("[id*='calCGST']").removeAttr('readonly');
      $("#"+txtcol).parent().parent().find("[id*='calSGST']").removeAttr('readonly');
      $("#"+txtcol).parent().parent().find("[id*='AMTCGST']").removeAttr('readonly');
      $("#"+txtcol).parent().parent().find("[id*='AMTSGST']").removeAttr('readonly');
    }     
    else
    {
      $("#"+txtcol).parent().parent().find("[id*='calGST']").removeAttr('checked');
    } 

    var totaltaxableamount = 0;
    $('#Material').find('.participantRow').each(function()
      {
        var amount1 = $(this).find('[id*="DISAFTT_AMT"]').val();

        totaltaxableamount += parseFloat(amount1);
      });
    if(txtrate > 0)
    {
      txtamount = 0;
      txtamount = parseFloat((totaltaxableamount*txtrate)/100).toFixed(2);
      if(intRegex.test(txtamount)){
      txtamount = (txtamount +'.00');
      }
      $("#"+txtcol).parent().parent().find("[id*='VALUE']").val(txtamount);
    }
    else
    {
      if(intRegex.test(txtamount)){
      txtamount = (txtamount +'.00');
      }
      $("#"+txtcol).parent().parent().find("[id*='VALUE']").val(txtamount);
    }
    
    $("#ctiddetpopup").hide();
    $("#CTIDdetcodesearch").val(''); 
    $("#CTIDdetnamesearch").val(''); 
    $("#CTIDdetratesearch").val(''); 
    $("#CTIDdetamountsearch").val(''); 
    $("#CTIDdetformulasearch").val(''); 
    CTIDDetCodeFunction();
    event.preventDefault();
    
});
}


//====================================== VENDOR ======================================

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

$('#txtVID_popup').click(function(event){
  

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
  $('.clsvendorid').click(function(){

    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texdesc =   $("#txt"+id+"").data("desc");
    var oldSLID =   $("#VID_REF").val();
    
    $("#txtVID_popup").val(texdesc);
    $("#txtVID_popup").blur();
    $("#VID_REF").val(txtval);
    if (txtval != oldSLID)
    {
        
        $('#TotalValue').val('0.00');
       
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

 
    }

    $("#vendoridpopup").hide();
  $("#vendorcodesearch").val(''); 
  $("#vendornamesearch").val(''); 
  VendorCodeFunction();

    event.preventDefault();
  });
}

//====================================== TRANSPORTER ======================================

let TRASPORTER_VARID = "#TRASPORTER_TABLE2";
let TRASPORTER_VARID2 = "#TRASPORTER_TABLE";
let TRASPORTER_HEADERS = document.querySelectorAll(TRASPORTER_VARID2 + " th");

TRASPORTER_HEADERS.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(TRASPORTER_VARID, ".CLASS_TRASPORTER", "td:nth-child(" + (i + 1) + ")");
  });
});

function TRASPORTER_CODE_FUNCTION() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("TRASPORTER_CODE_SEARCH");
  filter = input.value.toUpperCase();
  table = document.getElementById("TRASPORTER_TABLE2");
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

function TRASPORTER_NAME_FUNCTION() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("TRASPORTER_NAME_SEARCH");
  filter = input.value.toUpperCase();
  table = document.getElementById("TRASPORTER_TABLE2");
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


$('#Header_Form').on('click','[id*="txt_TRASPORTER_popup"]',function(event){

  $('#HIDDEN_TRASPORTER_ID').val($(this).attr('id'));
  $('#HIDDEN_TRASPORTER_ID2').val($(this).parent().parent().find('[id*="TRASPORTER_NAME"]').attr('id'));

  showSelectedCheck($("#TRASPORTER_NAME").val(),"SELECT_TRASPORTER_NAME");
  $("#TRASPORTER_OPEN_POPUP").show();
});

$("#TRASPORTER_CLOSE_POPUP").click(function(event){
  $("#TRASPORTER_OPEN_POPUP").hide();
});

$(".CLASS_TRASPORTER").click(function(){
  var fieldid = $(this).attr('id');
  var txtval  = $("#txt"+fieldid+"").val();
  var texdesc = $("#txt"+fieldid+"").data("desc");
  
  var txtid   = $('#HIDDEN_TRASPORTER_ID').val();
  var txt_id2 = $('#HIDDEN_TRASPORTER_ID2').val();

  $('#'+txtid).val(texdesc);
  $('#'+txt_id2).val(txtval);
  $("#TRASPORTER_OPEN_POPUP").hide();
  event.preventDefault();
});


//====================================== JOB WORK INVOICE ======================================

let sqtid = "#SalesQuotationTable2";
let sqtid2 = "#SalesQuotationTable";
let salesquotationheaders = document.querySelectorAll(sqtid2 + " th");

salesquotationheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(sqtid, ".clssqid", "td:nth-child(" + (i + 1) + ")");
  });
});

function SalesQuotationCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("SalesQuotationcodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("SalesQuotationTable2");
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

function SalesQuotationNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("SalesQuotationnamesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("SalesQuotationTable2");
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

$('#Material').on('click','[id*="txtJWID_popup"]',function(event){
  
  var id = $(this).attr('id');
  var id2 = $(this).parent().parent().find('[id*="JWID_REF"]').attr('id');
  var id3 = $(this).parent().parent().find('[id*="SSO_DATE"]').attr('id');

  var ROW_ID = id.split('_').pop();

  $('#hdn_sqid').val(id);
  $('#hdn_sqid2').val(id2);
  $('#hdn_sqid3').val(id3);

  var VID_REF    = $('#VID_REF').val();
  var fieldid = $(this).parent().parent().find('[id*="JWID_REF"]').attr('id');

  if(VID_REF ===""){
    showAlert('Please select Vendor.','txtVID_popup');
  }
  else{

    $("#JWID_REFpopup").show();
    $("#tbody_SQ").html('');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    $.ajax({
        url:'{{route("transaction",[$FormId,"getCodeNo"])}}',
        type:'POST',
        data:{'id':$('#VID_REF').val(),'fieldid':fieldid},
        success:function(data) {
          $("#tbody_SQ").html(data);
          BindSalesQuotation(ROW_ID);
          showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_SQ").html('');
        },
    });
    

  }

});

$("#JWID_REF_closePopup").click(function(event){
  $("#JWID_REFpopup").hide();
});

function BindSalesQuotation(ROW_ID){
  $(".clssqid").click(function(){

    $('#txtJWID_popup_'+ROW_ID).val('');
    $('#JWID_REF_'+ROW_ID).val('');
    $('#GEJID_REF_'+ROW_ID).val('');
    $('#GRJID_REF_'+ROW_ID).val('');
    $('#JWCID_REF_'+ROW_ID).val('');
    $('#JWOID_REF_'+ROW_ID).val('');
    $('#PROID_REF_'+ROW_ID).val('');
    $('#SOID_REF_'+ROW_ID).val('');
    $('#SQID_REF_'+ROW_ID).val('');
    $('#SEID_REF_'+ROW_ID).val('');
   
    $('#popupITEMID_'+ROW_ID).val('');
    $('#ITEMID_REF_'+ROW_ID).val('');
    $('#ItemName_'+ROW_ID).val('');
    $('#popupMUOM_'+ROW_ID).val('');
    $('#MAIN_UOMID_REF_'+ROW_ID).val('');
    $('#SI_RATE_'+ROW_ID).val('');
    $('#SO_QTY_'+ROW_ID).val('');
    $('#HIDNO_'+ROW_ID).val('');
    $('#RATEPUOM_'+ROW_ID).val('');
    $('#DISCPER_'+ROW_ID).val('');
    $('#DISCOUNT_AMT_'+ROW_ID).val('');
    $('#DISAFTT_AMT_'+ROW_ID).val('');
    $('#IGST_'+ROW_ID).val('');
    $('#IGSTAMT_'+ROW_ID).val('');
    $('#CGST_'+ROW_ID).val('');
    $('#CGSTAMT_'+ROW_ID).val('');
    $('#SGST_'+ROW_ID).val('');
    $('#SGSTAMT_'+ROW_ID).val('');
    $('#TGST_AMT_'+ROW_ID).val('');
    $('#TOT_AMT_'+ROW_ID).val('');

    $('#SQMUOM_'+ROW_ID).val('');
    $('#SQMUOMQTY_'+ROW_ID).val('');
    $('#SQAUOM_'+ROW_ID).val('');
    $('#SQAUOMQTY_'+ROW_ID).val('');
    $('#popupAUOM_'+ROW_ID).val('');
    $('#ALT_UOMID_QTY_'+ROW_ID).val('');
    $('#ALT_UOMID_REF_'+ROW_ID).val('');
    $('#SO_FQTY_'+ROW_ID).val('');
    $('#flagtype_'+ROW_ID).prop("checked", false);

    $('#TotalHiddenQty_'+ROW_ID).val('');
    $('#HiddenRowId_'+ROW_ID).val('');
    $('#GRNNO_'+ROW_ID).val('');
    $('#STORE_NAME_'+ROW_ID).val('');

    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");
    var texdescdate =   $("#txt"+fieldid+"").data("descdate");
    
    var txtid= $('#hdn_sqid').val();
    var txt_id2= $('#hdn_sqid2').val();
    var txt_id3= $('#hdn_sqid3').val();

    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
    $('#'+txt_id3).val(texdescdate);
    $("#JWID_REFpopup").hide();

    $("#SalesQuotationcodesearch").val(''); 
    $("#SalesQuotationnamesearch").val(''); 
    SalesQuotationCodeFunction();
    event.preventDefault();
  });
}


//====================================== ITEM ======================================

let itemtid = "#ItemIDTable2";
let itemtid2 = "#ItemIDTable";
let itemtidheaders = document.querySelectorAll(itemtid2 + " th");

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



$('#Material').on('click','[id*="popupITEMID"]',function(event){
       
  var JWID_REF = $(this).parent().parent().find('[id*="JWID_REF"]').val();
  var JWID_REF_ATTR_ID = $(this).parent().parent().find('[id*="JWID_REF"]').attr('id');
  var taxstate = $.trim($('#Tax_State').val());

  if(JWID_REF ===""){
    showAlert('Please Select Job Work Invoice.',JWID_REF_ATTR_ID);
  }
  else{
  
    $("#tbody_ItemID").html('');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("transaction",[$FormId,"getItemList"])}}',
        type:'POST',
        data:{'id':JWID_REF, 'taxstate':taxstate},
        success:function(data) {
          $("#tbody_ItemID").html(data);   
          bindItemEvents();                     
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_ItemID").html('');                        
        },
    }); 

    $("#ITEMIDpopup").show();

  }

  var id = $(this).attr('id');
  var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
  var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
  var id4 = $(this).parent().parent().find('[id*="Itemspec"]').attr('id');
  var id5 = $(this).parent().parent().find('[id*="SQMUOM"]').attr('id');
  var id6 = $(this).parent().parent().find('[id*="SQMUOMQTY"]').attr('id');
  var id66 = $(this).parent().parent().find('[id*="SI_RATE"]').attr('id');
  var id7 = $(this).parent().parent().find('[id*="SQAUOM"]').attr('id');
  var id8 = $(this).parent().parent().find('[id*="SQAUOMQTY"]').attr('id');
  var id9 = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
  var id10 = $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
  var id11 = $(this).parent().parent().find('[id*="SO_QTY"]').attr('id');
  var id12 = $(this).parent().parent().find('[id*="popupAUOM"]').attr('id');
  var id13 = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');
  var id14 = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
  var id15 = $(this).parent().parent().find('[id*="RATEPUOM"]').attr('id');
  var id16 = $(this).parent().parent().find('[id*="SO_FQTY"]').attr('id');

 
  $('#hdn_ItemID').val(id);
  $('#hdn_ItemID2').val(id2);
  $('#hdn_ItemID3').val(id3);
  $('#hdn_ItemID4').val(id4);
  $('#hdn_ItemID5').val(id5);
  $('#hdn_ItemID6').val(id6);
  $('#hdn_ItemID66').val(id66);
  $('#hdn_ItemID7').val(id7);
  $('#hdn_ItemID8').val(id8);
  $('#hdn_ItemID9').val(id9);
  $('#hdn_ItemID10').val(id10);
  $('#hdn_ItemID11').val(id11);
  $('#hdn_ItemID12').val(id12);
  $('#hdn_ItemID13').val(id13);
  $('#hdn_ItemID14').val(id14);
  $('#hdn_ItemID15').val(id15);
  $('#hdn_ItemID16').val(id16);

});

$("#ITEMID_closePopup").click(function(event){
  $("#ITEMIDpopup").hide();
  $('.js-selectall').prop("checked", false);
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
      var txtmuom =  $(this).find('[id*="itemuom"]').text().trim();
      var fieldid4 = $(this).find('[id*="uomqty"]').attr('id');
      var txtauomid =  $("#txt"+fieldid4+"").val();
      var txtauomqty =  $("#txt"+fieldid4+"").data("desc");
      var txtmuomqty =  $(this).find('[id*="uomqty"]').text().trim();
      var fieldid5 = $(this).find('[id*="irate"]').attr('id');
      var txtruom =  $("#txt"+fieldid5+"").val();
      var txtmqtyf = $("#txt"+fieldid5+"").data("desc");
      var fieldid6 = $(this).find('[id*="itax"]').attr('id');
      var txttax2 =  $("#txt"+fieldid6+"").val();
      var txttax1 = $("#txt"+fieldid6+"").data("desc");
     
      var rcount1 = parseInt($(this).closest('table').find('.clsitemid').length);
      var rcount2 = $('#hdn_ItemID21').val();
      var r_count2 = 0;

      var desc1 =  $("#txt"+fieldid+"").data("desc1");
      var desc2 =  $("#txt"+fieldid+"").data("desc2");
      var desc3 =  $("#txt"+fieldid+"").data("desc3");
      var desc4 =  $("#txt"+fieldid+"").data("desc4");
      var desc5 =  $("#txt"+fieldid+"").data("desc5");

      var uniquerowid         =   $(this).find('[id*="uniquerowid"]').attr('id');
      var item_unique_row_id  =   $("#"+uniquerowid).data("desc0");
      var ITEM_SEID_REF       =   $("#"+uniquerowid).data("desc1");
      var ITEM_SQID_REF       =   $("#"+uniquerowid).data("desc2");
      var ITEM_SOID_REF       =   $("#"+uniquerowid).data("desc3");
      var ITEM_PROID_REF      =   $("#"+uniquerowid).data("desc4");
      var ITEM_JWOID_REF      =   $("#"+uniquerowid).data("desc5");
      var ITEM_JWCID_REF      =   $("#"+uniquerowid).data("desc6");
      var ITEM_GRJID_REF      =   $("#"+uniquerowid).data("desc7");
      var ITEM_GRNNO          =   $("#"+uniquerowid).data("desc8");
      var ITEM_GEJID_REF    =   $("#"+uniquerowid).data("desc9");

      var ITEM_IGST           =   $("#"+uniquerowid).data("desc21");
      var ITEM_CGST           =   $("#"+uniquerowid).data("desc22");
      var ITEM_SGST           =   $("#"+uniquerowid).data("desc23");
      var ITEM_IGSTAMT        =   $("#"+uniquerowid).data("desc24");
      var ITEM_CGSTAMT        =   $("#"+uniquerowid).data("desc25");
      var ITEM_SGSTAMT        =   $("#"+uniquerowid).data("desc26");



      var totalvalue = 0.00;
      var txttaxamt1 = 0.00;
      var txttaxamt2 = 0.00;
      var txttottaxamt = 0.00;
      var txttotamtatax =0.00;

      txtruom = parseFloat(txtruom).toFixed(5); 
    
      txtauomqty = (parseFloat(txtmuomqty))*parseFloat(txtauomqty);


        
      var txtamt = parseFloat((parseFloat(txtmuomqty)*parseFloat(txtruom))).toFixed(2);
      if(desc3 == undefined || desc3 == '')
      {
          desc3 = 0.0000;
      }
      else
      {
        
        txtamt = parseFloat(parseFloat(txtamt) - parseFloat((parseFloat(txtamt)*parseFloat(desc3))/100)).toFixed(2)
      }
      if(desc4 == undefined || desc4 == '')
      {
          desc4 = 0.00;
      }
      else
      {
        
        txtamt = parseFloat(parseFloat(txtamt) - parseFloat(desc4)).toFixed(2)
      }
      if(txttax1 == undefined || txttax1 == '')
      {
        txttax1 = 0.0000;
          txttaxamt1 = 0;
      }
      else
      {
          txttaxamt1 = parseFloat((parseFloat(txtamt)*parseFloat(txttax1))/100).toFixed(2);
      }
      if(txttax2 == undefined || txttax2 == '')
      {
        txttax2 = 0.0000;
          txttaxamt2 = 0;
      }
      else
      {
          txttaxamt2 = parseFloat((parseFloat(txtamt)*parseFloat(txttax2))/100).toFixed(2);
      }
      var txttottaxamt = parseFloat((parseFloat(txttaxamt1)+parseFloat(txttaxamt2))).toFixed(2);
      var txttotamtatax = parseFloat((parseFloat(txtamt)+parseFloat(txttottaxamt))).toFixed(2);
       
      if(intRegex.test(txtauomqty)){
          txtauomqty = (txtauomqty +'.000');
      }

      if(intRegex.test(txtmuomqty)){
        txtmuomqty = (txtmuomqty +'.000');
      }
      if(intRegex.test(txtruom)){
        txtruom = (txtruom +'.00000');
      }
      if(intRegex.test(txtamt)){
        txtamt = (txtamt +'.00');
      }
      if(intRegex.test(txttax1)){
        txttax1 = (txttax1 +'.0000');
      }
      if(intRegex.test(txttax2)){
        txttax2 = (txttax2 +'.0000');
      }
      if(intRegex.test(txttaxamt1)){
        txttaxamt1 = (txttaxamt1 +'.00');
      }
      if(intRegex.test(txttaxamt2)){
        txttaxamt2 = (txttaxamt2 +'.00');
      }
      if(intRegex.test(txttottaxamt)){
        txttottaxamt = (txttottaxamt +'.00');
      }
      if(intRegex.test(txttotamtatax)){
        txttotamtatax = (txttotamtatax +'.00');
      }

      var SalesEnq2 = [];
      $('#Material').find('.participantRow').each(function(){
        if($(this).find('[id*="ITEMID_REF"]').val() != ''){

          var seitem  = $(this).find('[id*="exist"]').val();

          SalesEnq2.push(seitem);
          r_count2 = parseInt(r_count2) + 1;
        }
      });

      
        

      if($(this).find('[id*="chkId"]').is(":checked") == true){

        

        if(SalesEnq2.indexOf(item_unique_row_id) != -1){
              
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
          $('#hdn_ItemID66').val('');
          $('#hdn_ItemID7').val('');
          $('#hdn_ItemID8').val('');
          $('#hdn_ItemID9').val('');
          $('#hdn_ItemID10').val('');
          $('#hdn_ItemID11').val('');
          $('#hdn_ItemID12').val('');
          $('#hdn_ItemID13').val('');
          $('#hdn_ItemID14').val('');
          $('#hdn_ItemID15').val('');
          $('#hdn_ItemID16').val('');
          $('#hdn_ItemID17').val('');
          $('#hdn_ItemID18').val('');
          $('#hdn_ItemID19').val('');
          $('#hdn_ItemID20').val('');

          txtval = '';
          texdesc = '';
          txtname = '';
          txtmuom = '';
          txtauom = '';
          txtmuomid = '';
          txtauomid = '';
          txtauomqty='';
          txtmuomqty='';
          txtruom = '';
          txtamt = '';
          txttax1 = '';
          txttax2 = '';
       
       
 
          return false;
        }

        
              
        if($('#hdn_ItemID').val() == "" && txtval != ''){

          var txtid= $('#hdn_ItemID').val();
          var txt_id2= $('#hdn_ItemID2').val();
          var txt_id3= $('#hdn_ItemID3').val();
          var txt_id4= $('#hdn_ItemID4').val();
          var txt_id5= $('#hdn_ItemID5').val();
          var txt_id6= $('#hdn_ItemID6').val();
          var txt_id66= $('#hdn_ItemID66').val();
          var txt_id7= $('#hdn_ItemID7').val();
          var txt_id8= $('#hdn_ItemID8').val();
          var txt_id9= $('#hdn_ItemID9').val();
          var txt_id10= $('#hdn_ItemID10').val();
          var txt_id11= $('#hdn_ItemID11').val();
          var txt_id12= $('#hdn_ItemID12').val();
          var txt_id13= $('#hdn_ItemID13').val();
          var txt_id14= $('#hdn_ItemID14').val();
          var txt_id15= $('#hdn_ItemID15').val();
          var txt_id16= $('#hdn_ItemID16').val();


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
         
          $clone.find('[id*="GEJID_REF_"]').val(ITEM_GEJID_REF);
          $clone.find('[id*="GRNNO_"]').val(ITEM_GRNNO);
          $clone.find('[id*="GRJID_REF"]').val(ITEM_GRJID_REF);
          $clone.find('[id*="JWCID_REF_"]').val(ITEM_JWCID_REF);
          $clone.find('[id*="JWOID_REF_"]').val(ITEM_JWOID_REF);
          $clone.find('[id*="PROID_REF_"]').val(ITEM_PROID_REF);
          $clone.find('[id*="SOID_REF_"]').val(ITEM_SOID_REF);
          $clone.find('[id*="SQID_REF_"]').val(ITEM_SQID_REF);
          $clone.find('[id*="SEID_REF_"]').val(ITEM_SEID_REF);
          $clone.find('[id*="exist"]').val(item_unique_row_id);          

          $clone.find('[id*="ITEMID_REF"]').val(txtval);
          $clone.find('[id*="ItemName"]').val(txtname);
          $clone.find('[id*="Itemspec"]').val(txtspec);
          $clone.find('[id*="SQMUOM"]').val(txtmuom);
          $clone.find('[id*="SQMUOMQTY"]').val(txtmuomqty);
          $clone.find('[id*="SI_RATE"]').val(txtruom);
          $clone.find('[id*="SQAUOM"]').val(txtauom);
          $clone.find('[id*="SQAUOMQTY"]').val(txtauomqty);
          $clone.find('[id*="popupMUOM"]').val(txtmuom);
          $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
          $clone.find('[id*="SO_QTY"]').val('');
          $clone.find('[id*="SO_FQTY"]').val(txtmuomqty);
          $clone.find('[id*="popupAUOM"]').val(txtauom);
          $clone.find('[id*="ALT_UOMID_REF"]').val(txtauomid);
          $clone.find('[id*="ALT_UOMID_QTY"]').val(txtauomqty);
          $clone.find('[id*="RATEPUOM"]').val(txtruom);
          $clone.find('[id*="DISAFTT_AMT"]').val(txtamt);
          $clone.find('[id*="TOT_AMT"]').val(txttotamtatax);
          $clone.find('[id*="TGST_AMT"]').val(txttottaxamt);

          
          $clone.find('[id*="HIDNO"]').val(desc1);
          $clone.find('[id*="DISCPER"]').val(desc3);
          $clone.find('[id*="DISCOUNT_AMT"]').val(desc4);
          

          $clone.find('[id*="TotalHiddenQty"]').val('');
          $clone.find('[id*="HiddenRowId"]').val('');


          $clone.find('[id*="IGST"]').val(ITEM_IGST);
          $clone.find('[id*="CGST"]').val(ITEM_CGST);
          $clone.find('[id*="SGST"]').val(ITEM_SGST);
          $clone.find('[id*="IGSTAMT"]').val(ITEM_IGSTAMT);
          $clone.find('[id*="CGSTAMT"]').val(ITEM_CGSTAMT);
          $clone.find('[id*="SGSTAMT"]').val(ITEM_SGSTAMT);
          

          $tr.closest('table').append($clone);   
          var rowCount = $('#Row_Count1').val();
            rowCount = parseInt(rowCount)+1;
            $('#Row_Count1').val(rowCount);
            var tvalue = parseFloat(txttotamtatax).toFixed(2);
          totalvalue = $('#TotalValue').val();
          totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
          totalvalue = parseFloat(totalvalue).toFixed(2);
          $('#TotalValue').val(totalvalue);

  
          if((parseFloat($clone.find('[id*="IGSTAMT"]').val()) > 0) || (parseFloat($clone.find('[id*="CGSTAMT"]').val() > 0)) || (parseFloat($clone.find('[id*="SGSTAMT"]').val()) > 0)){
            $clone.find('[id*="flagtype"]').prop('checked',true); 
          }

        }
        else{

          var txtid= $('#hdn_ItemID').val();
          var txt_id2= $('#hdn_ItemID2').val();
          var txt_id3= $('#hdn_ItemID3').val();
          var txt_id4= $('#hdn_ItemID4').val();
          var txt_id5= $('#hdn_ItemID5').val();
          var txt_id6= $('#hdn_ItemID6').val();
          var txt_id66= $('#hdn_ItemID66').val();
          var txt_id7= $('#hdn_ItemID7').val();
          var txt_id8= $('#hdn_ItemID8').val();
          var txt_id9= $('#hdn_ItemID9').val();
          var txt_id10= $('#hdn_ItemID10').val();
          var txt_id11= $('#hdn_ItemID11').val();
          var txt_id12= $('#hdn_ItemID12').val();
          var txt_id13= $('#hdn_ItemID13').val();
          var txt_id14= $('#hdn_ItemID14').val();
          var txt_id15= $('#hdn_ItemID15').val();
          var txt_id16= $('#hdn_ItemID16').val();

          $('#'+txtid).val(texdesc);
          $('#'+txt_id2).val(txtval);
          $('#'+txt_id3).val(txtname);
          $('#'+txt_id4).val(txtspec);
          $('#'+txt_id5).val(txtmuom);
          $('#'+txt_id6).val(txtmuomqty);
          $('#'+txt_id66).val(txtruom);
          $('#'+txt_id7).val(txtauom);
          $('#'+txt_id8).val(txtauomqty);
          $('#'+txt_id9).val(txtmuom);
          $('#'+txt_id10).val(txtmuomid);
          $('#'+txt_id11).val('');
          $('#'+txt_id12).val(txtauom);
          $('#'+txt_id13).val(txtauomid);
          $('#'+txt_id14).val(txtauomqty);
          $('#'+txt_id15).val(txtruom);
          $('#'+txt_id16).val(txtmuomqty);

          
          $('#'+txtid).parent().parent().find('[id*="DISAFTT_AMT"]').val(txtamt);
          $('#'+txtid).parent().parent().find('[id*="TOT_AMT"]').val(txttotamtatax);
          $('#'+txtid).parent().parent().find('[id*="TGST_AMT"]').val(txttottaxamt);

          $('#'+txtid).parent().parent().find('[id*="HIDNO"]').val(desc1);
          $('#'+txtid).parent().parent().find('[id*="DISCPER"]').val(desc3);
          $('#'+txtid).parent().parent().find('[id*="DISCOUNT_AMT"]').val(desc4);

          $('#'+txtid).parent().parent().find('[id*="GEJID_REF"]').val(ITEM_GEJID_REF);
          $('#'+txtid).parent().parent().find('[id*="GRNNO"]').val(ITEM_GRNNO);
          $('#'+txtid).parent().parent().find('[id*="GRJID_REF"]').val(ITEM_GRJID_REF);
          $('#'+txtid).parent().parent().find('[id*="JWCID_REF"]').val(ITEM_JWCID_REF);
          $('#'+txtid).parent().parent().find('[id*="JWOID_REF"]').val(ITEM_JWOID_REF);
          $('#'+txtid).parent().parent().find('[id*="PROID_REF"]').val(ITEM_PROID_REF);
          $('#'+txtid).parent().parent().find('[id*="SOID_REF"]').val(ITEM_SOID_REF);
          $('#'+txtid).parent().parent().find('[id*="SQID_REF"]').val(ITEM_SQID_REF);
          $('#'+txtid).parent().parent().find('[id*="SEID_REF"]').val(ITEM_SEID_REF);
          $('#'+txtid).parent().parent().find('[id*="exist"]').val(item_unique_row_id);

          $('#'+txtid).parent().parent().find('[id*="TotalHiddenQty"]').val('');
          $('#'+txtid).parent().parent().find('[id*="HiddenRowId"]').val('');


          $('#'+txtid).parent().parent().find('[id*="IGST"]').val(ITEM_IGST);
          $('#'+txtid).parent().parent().find('[id*="CGST"]').val(ITEM_CGST);
          $('#'+txtid).parent().parent().find('[id*="SGST"]').val(ITEM_SGST);
          $('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').val(ITEM_IGSTAMT);
          $('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').val(ITEM_CGSTAMT);
          $('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').val(ITEM_SGSTAMT);

          if((parseFloat($('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').val()) > 0) || (parseFloat($('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').val()) > 0) || (parseFloat($('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').val()) > 0)){
          $('#'+txtid).parent().parent().find('[id*="flagtype"]').prop('checked',true); 

        }

        
        var tvalue = parseFloat(txttotamtatax).toFixed(2);
        totalvalue = $('#TotalValue').val();
        totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
        totalvalue = parseFloat(totalvalue).toFixed(2);
        $('#TotalValue').val(totalvalue);
        
        $('#hdn_ItemID').val('');
        $('#hdn_ItemID2').val('');
        $('#hdn_ItemID3').val('');
        $('#hdn_ItemID4').val('');
        $('#hdn_ItemID5').val('');
        $('#hdn_ItemID6').val('');
        $('#hdn_ItemID66').val('');
        $('#hdn_ItemID7').val('');
        $('#hdn_ItemID8').val('');
        $('#hdn_ItemID9').val('');
        $('#hdn_ItemID10').val('');
        $('#hdn_ItemID11').val('');
        $('#hdn_ItemID12').val('');
        $('#hdn_ItemID13').val('');
        $('#hdn_ItemID14').val('');
        $('#hdn_ItemID15').val('');
        $('#hdn_ItemID16').val('');

      }


    }
		
	});

    $(".blurRate").blur(); 
    $("#Itemcodesearch").val(''); 
    $("#Itemnamesearch").val(''); 
    $("#ItemUOMsearch").val(''); 
    $("#ItemGroupsearch").val(''); 
    $("#ItemCategorysearch").val(''); 
    $("#ItemStatussearch").val(''); 
    $('.remove').removeAttr('disabled'); 
    $('.js-selectall').prop("checked", false);
	  $("#ITEMIDpopup").hide();
    event.preventDefault();
		
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
        var txtmuom =  $(this).parent().parent().children('[id*="itemuom"]').text().trim();
        var fieldid4 = $(this).parent().parent().children('[id*="uomqty"]').attr('id');
        var txtauomid =  $("#txt"+fieldid4+"").val();
        var txtauomqty =  $("#txt"+fieldid4+"").data("desc");
        var txtmuomqty =  $(this).parent().parent().children('[id*="uomqty"]').text().trim();
        var fieldid5 = $(this).parent().parent().children('[id*="irate"]').attr('id');
        var txtruom =  $("#txt"+fieldid5+"").val();
        var txtmqtyf = $("#txt"+fieldid5+"").data("desc");
        var fieldid6 = $(this).parent().parent().children('[id*="itax"]').attr('id');
        var txttax2 =  $("#txt"+fieldid6+"").val();
        var txttax1 = $("#txt"+fieldid6+"").data("desc");


        var desc1 =  $("#txt"+fieldid+"").data("desc1");
        var desc2 =  $("#txt"+fieldid+"").data("desc2");
        var desc3 =  $("#txt"+fieldid+"").data("desc3");
        var desc4 =  $("#txt"+fieldid+"").data("desc4");
        var desc5 =  $("#txt"+fieldid+"").data("desc5");

        var uniquerowid         =   $(this).parent().parent().find('[id*="uniquerowid"]').attr('id');
        var item_unique_row_id  =   $("#"+uniquerowid).data("desc0");
        var ITEM_SEID_REF       =   $("#"+uniquerowid).data("desc1");
        var ITEM_SQID_REF       =   $("#"+uniquerowid).data("desc2");
        var ITEM_SOID_REF       =   $("#"+uniquerowid).data("desc3");
        var ITEM_PROID_REF      =   $("#"+uniquerowid).data("desc4");
        var ITEM_JWOID_REF      =   $("#"+uniquerowid).data("desc5");
        var ITEM_JWCID_REF      =   $("#"+uniquerowid).data("desc6");
        var ITEM_GRJID_REF      =   $("#"+uniquerowid).data("desc7");
        var ITEM_GRNNO          =   $("#"+uniquerowid).data("desc8");
        var ITEM_GEJID_REF    =   $("#"+uniquerowid).data("desc9");

        var ITEM_IGST           =   $("#"+uniquerowid).data("desc21");
        var ITEM_CGST           =   $("#"+uniquerowid).data("desc22");
        var ITEM_SGST           =   $("#"+uniquerowid).data("desc23");
        var ITEM_IGSTAMT        =   $("#"+uniquerowid).data("desc24");
        var ITEM_CGSTAMT        =   $("#"+uniquerowid).data("desc25");
        var ITEM_SGSTAMT        =   $("#"+uniquerowid).data("desc26");


        var totalvalue = 0.00;
        var txttaxamt1 = 0.00;
        var txttaxamt2 = 0.00;
        var txttottaxamt = 0.00;
        var txttotamtatax =0.00;
 
        txtruom = parseFloat(txtruom).toFixed(5); 
      
        txtauomqty = (parseFloat(txtmuomqty))*parseFloat(txtauomqty);


        
        var txtamt = parseFloat((parseFloat(txtmuomqty)*parseFloat(txtruom))).toFixed(2);
        if(desc3 == undefined || desc3 == '')
          {
              desc3 = 0.0000;
          }
          else
          {
            
            txtamt = parseFloat(parseFloat(txtamt) - parseFloat((parseFloat(txtamt)*parseFloat(desc3))/100)).toFixed(2)
          }
          if(desc4 == undefined || desc4 == '')
          {
              desc4 = 0.00;
          }
          else
          {
           
            txtamt = parseFloat(parseFloat(txtamt) - parseFloat(desc4)).toFixed(2)
          }
        if(txttax1 == undefined || txttax1 == '')
          {
            txttax1 = 0.0000;
             txttaxamt1 = 0;
          }
          else
          {
             txttaxamt1 = parseFloat((parseFloat(txtamt)*parseFloat(txttax1))/100).toFixed(2);
          }
          if(txttax2 == undefined || txttax2 == '')
          {
            txttax2 = 0.0000;
             txttaxamt2 = 0;
          }
          else
          {
             txttaxamt2 = parseFloat((parseFloat(txtamt)*parseFloat(txttax2))/100).toFixed(2);
          }
        var txttottaxamt = parseFloat((parseFloat(txttaxamt1)+parseFloat(txttaxamt2))).toFixed(2);
        var txttotamtatax = parseFloat((parseFloat(txtamt)+parseFloat(txttottaxamt))).toFixed(2);
       
        if(intRegex.test(txtauomqty)){
            txtauomqty = (txtauomqty +'.000');
        }

        if(intRegex.test(txtmuomqty)){
          txtmuomqty = (txtmuomqty +'.000');
        }
        if(intRegex.test(txtruom)){
          txtruom = (txtruom +'.00000');
        }
        if(intRegex.test(txtamt)){
          txtamt = (txtamt +'.00');
        }
        if(intRegex.test(txttax1)){
          txttax1 = (txttax1 +'.0000');
        }
        if(intRegex.test(txttax2)){
          txttax2 = (txttax2 +'.0000');
        }
        if(intRegex.test(txttaxamt1)){
          txttaxamt1 = (txttaxamt1 +'.00');
        }
        if(intRegex.test(txttaxamt2)){
          txttaxamt2 = (txttaxamt2 +'.00');
        }
        if(intRegex.test(txttottaxamt)){
          txttottaxamt = (txttottaxamt +'.00');
        }
        if(intRegex.test(txttotamtatax)){
          txttotamtatax = (txttotamtatax +'.00');
        }

        var SalesEnq2 = [];
        $('#Material').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != ''){

            var seitem  = $(this).find('[id*="exist"]').val();
            SalesEnq2.push(seitem);

          }
        });
        

        if($(this).is(":checked") == true){

          if(SalesEnq2.indexOf(item_unique_row_id) != -1){
              
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
            $('#hdn_ItemID66').val('');
            $('#hdn_ItemID7').val('');
            $('#hdn_ItemID8').val('');
            $('#hdn_ItemID9').val('');
            $('#hdn_ItemID10').val('');
            $('#hdn_ItemID11').val('');
            $('#hdn_ItemID12').val('');
            $('#hdn_ItemID13').val('');
            $('#hdn_ItemID14').val('');
            $('#hdn_ItemID15').val('');
            $('#hdn_ItemID16').val('');
            $('#hdn_ItemID17').val('');
            $('#hdn_ItemID18').val('');
            $('#hdn_ItemID19').val('');
            $('#hdn_ItemID20').val('');

            txtval = '';
            texdesc = '';
            txtname = '';
            txtmuom = '';
            txtauom = '';
            txtmuomid = '';
            txtauomid = '';
            txtauomqty='';
            txtmuomqty='';
            txtruom = '';
            txtamt = '';
            txttax1 = '';
            txttax2 = '';
          
           
 
            return false;
          }

              
                      if($('#hdn_ItemID').val() == "" && txtval != ''){
                        var txtid= $('#hdn_ItemID').val();
                        var txt_id2= $('#hdn_ItemID2').val();
                        var txt_id3= $('#hdn_ItemID3').val();
                        var txt_id4= $('#hdn_ItemID4').val();
                        var txt_id5= $('#hdn_ItemID5').val();
                        var txt_id6= $('#hdn_ItemID6').val();
                        var txt_id66= $('#hdn_ItemID66').val();
                        var txt_id7= $('#hdn_ItemID7').val();
                        var txt_id8= $('#hdn_ItemID8').val();
                        var txt_id9= $('#hdn_ItemID9').val();
                        var txt_id10= $('#hdn_ItemID10').val();
                        var txt_id11= $('#hdn_ItemID11').val();
                        var txt_id12= $('#hdn_ItemID12').val();
                        var txt_id13= $('#hdn_ItemID13').val();
                        var txt_id14= $('#hdn_ItemID14').val();
                        var txt_id15= $('#hdn_ItemID15').val();
                        var txt_id16= $('#hdn_ItemID16').val();

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
                        
                        $clone.find('[id*="GEJID_REF"]').val(ITEM_GEJID_REF);
                        $clone.find('[id*="GRNNO"]').val(ITEM_GRNNO);
                        $clone.find('[id*="GRJID_REF"]').val(ITEM_GRJID_REF);
                        $clone.find('[id*="JWCID_REF_"]').val(ITEM_JWCID_REF);
                        $clone.find('[id*="JWOID_REF_"]').val(ITEM_JWOID_REF);
                        $clone.find('[id*="PROID_REF_"]').val(ITEM_PROID_REF);
                        $clone.find('[id*="SOID_REF_"]').val(ITEM_SOID_REF);
                        $clone.find('[id*="SQID_REF_"]').val(ITEM_SQID_REF);
                        $clone.find('[id*="SEID_REF_"]').val(ITEM_SEID_REF);
                        $clone.find('[id*="exist"]').val(item_unique_row_id);

                        $clone.find('[id*="ITEMID_REF"]').val(txtval);
                        $clone.find('[id*="ItemName"]').val(txtname);
                        $clone.find('[id*="Itemspec"]').val(txtspec);
                        $clone.find('[id*="SQMUOM"]').val(txtmuom);
                        $clone.find('[id*="SQMUOMQTY"]').val(txtmuomqty);
                        $clone.find('[id*="SI_RATE"]').val(txtruom);
                        $clone.find('[id*="SQAUOM"]').val(txtauom);
                        $clone.find('[id*="SQAUOMQTY"]').val(txtauomqty);
                        $clone.find('[id*="popupMUOM"]').val(txtmuom);
                        $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
                        $clone.find('[id*="SO_QTY"]').val('');
                        $clone.find('[id*="SO_FQTY"]').val(txtmuomqty);
                        $clone.find('[id*="popupAUOM"]').val(txtauom);
                        $clone.find('[id*="ALT_UOMID_REF"]').val(txtauomid);
                        $clone.find('[id*="ALT_UOMID_QTY"]').val(txtauomqty);
                        $clone.find('[id*="RATEPUOM"]').val(txtruom);
                        $clone.find('[id*="DISAFTT_AMT"]').val(txtamt);
                        $clone.find('[id*="TOT_AMT"]').val(txttotamtatax);
                        $clone.find('[id*="TGST_AMT"]').val(txttottaxamt);

                        
                        $clone.find('[id*="HIDNO"]').val(desc1);
                        $clone.find('[id*="DISCPER"]').val(desc3);
                        $clone.find('[id*="DISCOUNT_AMT"]').val(desc4);
                        

                        $clone.find('[id*="TotalHiddenQty"]').val('');
                        $clone.find('[id*="HiddenRowId"]').val('');


                        $clone.find('[id*="IGST"]').val(ITEM_IGST);
                        $clone.find('[id*="CGST"]').val(ITEM_CGST);
                        $clone.find('[id*="SGST"]').val(ITEM_SGST);
                        $clone.find('[id*="IGSTAMT"]').val(ITEM_IGSTAMT);
                        $clone.find('[id*="CGSTAMT"]').val(ITEM_CGSTAMT);
                        $clone.find('[id*="SGSTAMT"]').val(ITEM_SGSTAMT);
                        

                        $tr.closest('table').append($clone);   
                        var rowCount = $('#Row_Count1').val();
                          rowCount = parseInt(rowCount)+1;
                          $('#Row_Count1').val(rowCount);
                          var tvalue = parseFloat(txttotamtatax).toFixed(2);
                        totalvalue = $('#TotalValue').val();
                        totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
                        totalvalue = parseFloat(totalvalue).toFixed(2);
                        $('#TotalValue').val(totalvalue);

                

                        if((parseFloat($clone.find('[id*="IGSTAMT"]').val()) > 0) || (parseFloat($clone.find('[id*="CGSTAMT"]').val() > 0)) || (parseFloat($clone.find('[id*="SGSTAMT"]').val()) > 0)){
                          $clone.find('[id*="flagtype"]').prop('checked',true); 
                        }

                        $(".blurRate").blur();

                        $("#ITEMIDpopup").hide();
                        $('.js-selectall').prop("checked", false);

                        event.preventDefault();
                      }
                      else
                      {
                      var txtid= $('#hdn_ItemID').val();
                      var txt_id2= $('#hdn_ItemID2').val();
                      var txt_id3= $('#hdn_ItemID3').val();
                      var txt_id4= $('#hdn_ItemID4').val();
                      var txt_id5= $('#hdn_ItemID5').val();
                      var txt_id6= $('#hdn_ItemID6').val();
                      var txt_id66= $('#hdn_ItemID66').val();
                      var txt_id7= $('#hdn_ItemID7').val();
                      var txt_id8= $('#hdn_ItemID8').val();
                      var txt_id9= $('#hdn_ItemID9').val();
                      var txt_id10= $('#hdn_ItemID10').val();
                      var txt_id11= $('#hdn_ItemID11').val();
                      var txt_id12= $('#hdn_ItemID12').val();
                      var txt_id13= $('#hdn_ItemID13').val();
                      var txt_id14= $('#hdn_ItemID14').val();
                      var txt_id15= $('#hdn_ItemID15').val();
                      var txt_id16= $('#hdn_ItemID16').val();

                      $('#'+txtid).val(texdesc);
                      $('#'+txt_id2).val(txtval);
                      $('#'+txt_id3).val(txtname);
                      $('#'+txt_id4).val(txtspec);
                      $('#'+txt_id5).val(txtmuom);
                      $('#'+txt_id6).val(txtmuomqty);
                      $('#'+txt_id66).val(txtruom);
                      $('#'+txt_id7).val(txtauom);
                      $('#'+txt_id8).val(txtauomqty);
                      $('#'+txt_id9).val(txtmuom);
                      $('#'+txt_id10).val(txtmuomid);
                      $('#'+txt_id11).val('');
                      $('#'+txt_id12).val(txtauom);
                      $('#'+txt_id13).val(txtauomid);
                      $('#'+txt_id14).val(txtauomqty);
                      $('#'+txt_id15).val(txtruom);
                      $('#'+txt_id16).val(txtmuomqty);

                     
                      $('#'+txtid).parent().parent().find('[id*="DISAFTT_AMT"]').val(txtamt);
                      $('#'+txtid).parent().parent().find('[id*="TOT_AMT"]').val(txttotamtatax);
                      $('#'+txtid).parent().parent().find('[id*="TGST_AMT"]').val(txttottaxamt);

                      $('#'+txtid).parent().parent().find('[id*="HIDNO"]').val(desc1);
                      $('#'+txtid).parent().parent().find('[id*="DISCPER"]').val(desc3);
                      $('#'+txtid).parent().parent().find('[id*="DISCOUNT_AMT"]').val(desc4);
                      
                      $('#'+txtid).parent().parent().find('[id*="GEJID_REF"]').val(ITEM_GEJID_REF);
                      $('#'+txtid).parent().parent().find('[id*="GRNNO"]').val(ITEM_GRNNO);
                      $('#'+txtid).parent().parent().find('[id*="GRJID_REF"]').val(ITEM_GRJID_REF);
                      $('#'+txtid).parent().parent().find('[id*="JWCID_REF"]').val(ITEM_JWCID_REF);
                      $('#'+txtid).parent().parent().find('[id*="JWOID_REF"]').val(ITEM_JWOID_REF);
                      $('#'+txtid).parent().parent().find('[id*="PROID_REF"]').val(ITEM_PROID_REF);
                      $('#'+txtid).parent().parent().find('[id*="SOID_REF"]').val(ITEM_SOID_REF);
                      $('#'+txtid).parent().parent().find('[id*="SQID_REF"]').val(ITEM_SQID_REF);
                      $('#'+txtid).parent().parent().find('[id*="SEID_REF"]').val(ITEM_SEID_REF);
                      $('#'+txtid).parent().parent().find('[id*="exist"]').val(item_unique_row_id);

                      $('#'+txtid).parent().parent().find('[id*="TotalHiddenQty"]').val('');
                      $('#'+txtid).parent().parent().find('[id*="HiddenRowId"]').val('');


                      $('#'+txtid).parent().parent().find('[id*="IGST"]').val(ITEM_IGST);
                      $('#'+txtid).parent().parent().find('[id*="CGST"]').val(ITEM_CGST);
                      $('#'+txtid).parent().parent().find('[id*="SGST"]').val(ITEM_SGST);
                      $('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').val(ITEM_IGSTAMT);
                      $('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').val(ITEM_CGSTAMT);
                      $('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').val(ITEM_SGSTAMT);

                      

                      
                        var tvalue = parseFloat(txttotamtatax).toFixed(2);
                        totalvalue = $('#TotalValue').val();
                        totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
                        totalvalue = parseFloat(totalvalue).toFixed(2);
                        $('#TotalValue').val(totalvalue);

                        if((parseFloat($('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').val()) > 0) || (parseFloat($('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').val()) > 0) || (parseFloat($('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').val()) > 0)){
                        $('#'+txtid).parent().parent().find('[id*="flagtype"]').prop('checked',true); 
                      }
                     

                     
                      $('#hdn_ItemID').val('');
                      $('#hdn_ItemID2').val('');
                      $('#hdn_ItemID3').val('');
                      $('#hdn_ItemID4').val('');
                      $('#hdn_ItemID5').val('');
                      $('#hdn_ItemID6').val('');
                      $('#hdn_ItemID66').val('');
                      $('#hdn_ItemID7').val('');
                      $('#hdn_ItemID8').val('');
                      $('#hdn_ItemID9').val('');
                      $('#hdn_ItemID10').val('');
                      $('#hdn_ItemID11').val('');
                      $('#hdn_ItemID12').val('');
                      $('#hdn_ItemID13').val('');
                      $('#hdn_ItemID14').val('');
                      $('#hdn_ItemID15').val('');
                      $('#hdn_ItemID16').val('');


                      }


                      

                      $(".blurRate").blur();
                      $("#ITEMIDpopup").hide();
                      $('.js-selectall').prop("checked", false);
                      event.preventDefault();
            }
            else if($(this).is(":checked") == false){

              var id = item_unique_row_id;
      
              var r_count = $('#Row_Count1').val();
              $('#Material').find('.participantRow').each(function(){

                var seitem  = $(this).find('[id*="exist"]').val();
                
                if(id == seitem){
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text('Item already exists.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk1');

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
        ItemCodeFunction();
        event.preventDefault();
      });
    }

//====================================== ADD/REMOVE TABLE ROW ======================================

$("#Material").on('click','.add', function() {
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
        $clone.find('[id*="SQA"]').val('');
        $clone.find('[id*="SEQID_REF"]').val('');
        $clone.find('[id*="ITEMID_REF"]').val('');
        $clone.find('[id*="flagtype"]').prop('checked', false);
       
        $clone.find('[id*="TotalHiddenQty"]').val('');
        $clone.find('[id*="HiddenRowId"]').val('');
                      
        

        $tr.closest('table').append($clone);         
        var rowCount1 = $('#Row_Count1').val();
		    rowCount1 = parseInt(rowCount1)+1;
        $('#Row_Count1').val(rowCount1);
        $clone.find('.remove').removeAttr('disabled'); 
        //$(".blurRate").blur();  
        event.preventDefault();
    });

    $("#Material").on('click', '.remove', function() {
        var rowCount = $(this).closest('table').find('.participantRow').length;
        if (rowCount > 1) {
        var totalvalue = $('#TotalValue').val();
        totalvalue = parseFloat(totalvalue - $(this).closest('.participantRow').find('[id*="TOT_AMT_"]').val()).toFixed(2);
        $('#TotalValue').val(totalvalue);
        $(this).closest('.participantRow').remove();   
        //$(".blurRate").blur();   
        } 
        if (rowCount <= 1) { 
              $(".blurRate").blur();
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


    $("#TC").on('click', '.add', function() {
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('.participantRow3').last();
        var lastTr = allTrs[allTrs.length-1];
        var $clone = $(lastTr).clone();
        $clone.find('td').each(function(){
            var id = $(this).attr('id') || null;
            if(id) {
                var i = id.substr(id.length-1);
                var prefix = id.substr(0, (id.length-1));
                $(this).attr('id', prefix+(+i+1));
            }

        }); 

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
        $clone.find("[id*='tdinputid']").html('');
        $clone.find('[id*="TNCDID_REF"]').val('');
        $clone.find('[id*="TNCismandatory"]').val('');
        $tr.closest('table').append($clone);         
        var rowCount2 = $('#Row_Count2').val();
		    rowCount2 = parseInt(rowCount2)+1;
        $('#Row_Count2').val(rowCount2);
        // $clone.find('.remove').removeAttr('disabled'); 
        
        event.preventDefault();
    });
    $("#TC").on('click', '.remove', function() {
        var rowCount2 = $(this).closest('table').find('.participantRow3').length;
        if (rowCount2 > 1) {
        $(this).closest('.participantRow3').remove();     
        } 
        if (rowCount2 <= 1) { 
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
    $("#udf").on('click', '.add', function() {
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('tbody').last();
        var lastTr = allTrs[allTrs.length-1];
        var $clone = $(lastTr).clone();
        $clone.find('td').each(function(){
            var id = $(this).attr('id') || null;
            if(id) {
                var i = id.substr(id.length-1);
                var prefix = id.substr(0, (id.length-1));
                $(this).attr('id', prefix+(+i+1));
            }

        }); 

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
        $clone.find("[id*='udfinputid']").html('');
        $clone.find('[id*="UDFSQID_REF"]').val('');
        $clone.find('[id*="UDFismandatory"]').val('');
        $tr.closest('table').append($clone);         
        var rowCount3 = $('#Row_Count3').val();
		    rowCount3 = parseInt(rowCount3)+1;
        $('#Row_Count3').val(rowCount3);
        // $clone.find('.remove').removeAttr('disabled'); 
        
        event.preventDefault();
    });
    $("#udf").on('click', '.remove', function() {
        var rowCount3 = $(this).closest('table').find('.participantRow4').length;
        if (rowCount3 > 1) {
        $(this).closest('.participantRow4').remove();     
        } 
        if (rowCount3 <= 1) { 
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

    $("#CT").on('click', '.add', function() {
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('.participantRow5').last();
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
        $clone.find('[id*="calGST"]').removeAttr('checked');
        if($clone.find('[id*="calGST"]').is(":checked") == false)
        {
          $clone.find('[id*="calIGST"]').prop('disabled','true');
          $clone.find('[id*="calCGST"]').prop('disabled','true');
          $clone.find('[id*="calSGST"]').prop('disabled','true');
          $clone.find('[id*="AMTIGST"]').prop('disabled','true');
          $clone.find('[id*="AMTCGST"]').prop('disabled','true');
          $clone.find('[id*="AMTSGST"]').prop('disabled','true');
        }
        $clone.find('[id*="TID_REF"]').val('');
        $clone.find('[id*="BASIS"]').val('');
        $clone.find('[id*="SQNO"]').val('');
        $tr.closest('table').append($clone);         
        var rowCount4 = $('#Row_Count4').val();
		    rowCount4 = parseInt(rowCount4)+1;
        $('#Row_Count4').val(rowCount4);
        // $clone.find('.remove').removeAttr('disabled'); 
        
        event.preventDefault();
    });
    $("#CT").on('click', '.remove', function() {
        var rowCount4 = $(this).closest('table').find('.participantRow5').length;
        if (rowCount4 > 1) {
        $(this).closest('.participantRow5').remove();    
        } 
        if (rowCount4 <= 1) {          
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




$(document).ready(function(e) {
  
  var Material = $("#Material").html(); 
    $('#hdnmaterial').val(Material);
    var count1 = <?php echo json_encode($objCount1); ?>;
    var count2 = <?php echo json_encode($objCount2); ?>;
    var count3 = <?php echo json_encode($objCount3); ?>;
    var count4 = <?php echo json_encode($objCount4); ?>;
  
    $('#Row_Count1').val(count1);
    $('#Row_Count2').val(count2);
    $('#Row_Count3').val(count3);
    $('#Row_Count4').val(count4);
  
    var obj = <?php echo json_encode($DATA_MAT); ?>;
    var objtnc = <?php echo json_encode($objSOTNC); ?>;    
    var tncheader = <?php echo json_encode($objTNCHeader); ?>;
    var tncdetails = <?php echo json_encode($objTNCDetails); ?>;
    var soudf = <?php echo json_encode($objSOUDF); ?>;
    var udfforso = <?php echo json_encode($objUdfSOData2); ?>;
    var calheader = <?php echo json_encode($objCalHeader); ?>;
    var caldetails = <?php echo json_encode($objCalDetails); ?>;
    var SOCal = <?php echo json_encode($objSOCAL); ?>;
    var taxstate = <?php echo json_encode($TAXSTATE); ?>;

    var totalvalue = 0.00;
    $.each(SOCal, function( sockey, socvalue ) {
        $.each( calheader, function( calkey, calvalue ){ 
            if(socvalue.CTID_REF == calvalue.CTID)
            {
                $('#txtCTID_popup').val(calvalue.CTCODE);
            }
        });
        $.each( caldetails, function( caldkey, caldvalue ){ 
            if(socvalue.TID_REF == caldvalue.TID)
            {
                $('#popupTID_'+sockey).val(caldvalue.COMPONENT);
                $('#BASIS_'+sockey).val(caldvalue.BASIS);
                $('#SQNO_'+sockey).val(caldvalue.SQNO);
                $('#FORMULA_'+sockey).val(caldvalue.FORMULA);
                
            }
        });

        if(taxstate =="OutofState")
            { 
              $('#calIGST_'+sockey).removeAttr('readonly');
              var gstamt = parseFloat((socvalue.IGST*socvalue.VALUE)/100).toFixed(2);
              var totgst = parseFloat(gstamt).toFixed(2);
              $('#AMTIGST_'+sockey).val(gstamt);
              $('#TOTGSTAMT_'+sockey).val(totgst);
              var tvalue = 0.00;
              tvalue = parseFloat(tvalue) + parseFloat(socvalue.VALUE);
              tvalue = parseFloat(tvalue) + parseFloat(totgst);
              tvalue = parseFloat(tvalue).toFixed(2);
            }
            else
            {
              $('#calCGST_'+sockey).removeAttr('readonly');
              $('#calSGST_'+sockey).removeAttr('readonly');
              var gstamt2 = parseFloat((socvalue.CGST*socvalue.VALUE)/100).toFixed(2);
              var gstamt3 = parseFloat((socvalue.SGST*socvalue.VALUE)/100).toFixed(2);
              var totgst2 = parseFloat(parseFloat(gstamt2)+parseFloat(gstamt3)).toFixed(2);
              $('#AMTCGST_'+sockey).val(gstamt2);
              $('#AMTSGST_'+sockey).val(gstamt3);
              $('#TOTGSTAMT_'+sockey).val(totgst2);
              var tvalue = 0.00;
              tvalue = parseFloat(tvalue) + parseFloat(socvalue.VALUE);
              tvalue = parseFloat(tvalue) + parseFloat(totgst2);
              tvalue = parseFloat(tvalue).toFixed(2);
            }
            totalvalue += + tvalue;
    });
   
    $('#TotalValue').val(totalvalue);

    $.each( soudf, function( soukey, souvalue ) {
        $.each( udfforso, function( usokey, usovalue ) { 
            if(souvalue.UDFJWRID_REF == usovalue.UDFJWRID)
            {
                $('#popupUDFSOID_'+soukey).val(usovalue.LABEL);
            }
        
            if(souvalue.UDFJWRID_REF == usovalue.UDFJWRID)
            {        
                    var txtvaltype2 =   usovalue.VALUETYPE;
                    var txt_id41 = $('#udfinputid_'+soukey).attr('id');
                    var strdyn2 = txt_id41.split('_');
                    var lastele2 =   strdyn2[strdyn2.length-1];
                    var dynamicid2 = "udfvalue_"+lastele2;
                    
                    var chkvaltype2 =  txtvaltype2.toLowerCase();
                    var strinp2 = '';

                    if(chkvaltype2=='date'){

                    strinp2 = '<input disabled type="date" placeholder="dd/mm/yyyy" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';       

                    }
                    else if(chkvaltype2=='time'){
                    strinp2= '<input disabled type="time" placeholder="h:i" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';

                    }
                    else if(chkvaltype2=='numeric'){
                    strinp2 = '<input disabled type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"   > ';

                    }
                    else if(chkvaltype2=='text'){

                    strinp2 = '<input disabled type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';
                    
                    }
                    else if(chkvaltype2=='boolean'){
                      if(souvalue.VALUE == "1")
                      {
                        strinp2 = '<input disabled type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" checked> ';
                      }
                      else{
                        strinp2 = '<input disabled type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" > ';
                      }
                    }
                    else if(chkvaltype2=='combobox'){

                    var txtoptscombo2 =   usovalue.DESCRIPTIONS;
                    var strarray2 = txtoptscombo2.split(',');
                    var opts2 = '';

                    for (var i = 0; i < strarray2.length; i++) {
                        opts2 = opts2 + '<option value="'+strarray2[i]+'">'+strarray2[i]+'</option> ';
                    }

                    strinp2 = '<select disabled name="'+dynamicid2+ '" id="'+dynamicid2+'" class="form-control" required>'+opts2+'</select>' ;
                   
                    }
                   
                    
                    $('#'+txt_id41).html('');  
                    $('#'+txt_id41).html(strinp2);   //set dynamic input
                    $('#'+dynamicid2).val(souvalue.VALUE);
                    $('#UDFismandatory_'+soukey).val(usovalue.ISMANDATORY); // mandatory
                
            }
        });
    });
    $.each( objtnc, function( tnckey, tncvalue ) {
        $.each( tncheader, function( tnchkey, tnchvalue ) { 
            if(tncvalue.TNCID_REF == tnchvalue.TNCID)
            {
                $('#txtTNCID_popup').val(tnchvalue.TNC_CODE);
            }
        });
        $.each( tncdetails, function( tncdkey, tncdvalue ) { 
            if(tncvalue.TNCDID_REF == tncdvalue.TNCDID)
            {
                $('#popupTNCDID_'+tnckey).val(tncdvalue.TNC_NAME);
            }
            if(tncvalue.TNCDID_REF == tncdvalue.TNCDID)
            {        
                    var txtvaltype =   tncdvalue.VALUE_TYPE;
                    var txt_id4 = $('#tdinputid_'+tnckey).attr('id');
                    var strdyn = txt_id4.split('_');
                    var lastele =   strdyn[strdyn.length-1];
                    var dynamicid = "tncdetvalue_"+lastele;
                    
                    var chkvaltype =  txtvaltype.toLowerCase();
                    var strinp = '';

                    if(chkvaltype=='date'){

                    strinp = '<input disabled type="date" placeholder="dd/mm/yyyy" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"  > ';       

                    }
                    else if(chkvaltype=='time'){
                    strinp= '<input disabled type="time" placeholder="h:i" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"  > ';

                    }
                    else if(chkvaltype=='numeric'){
                    strinp = '<input disabled type="text" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"   > ';

                    }
                    else if(chkvaltype=='text'){

                    strinp = '<input disabled type="text" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"  > ';
                    
                    }
                    else if(chkvaltype=='boolean'){
                      if(tncvalue.VALUE == "1")
                      {
                        strinp = '<input disabled type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" checked> ';
                      }
                      else{
                        strinp = '<input disabled type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" > ';
                      }                    
                    }
                    else if(chkvaltype=='combobox'){

                    var txtoptscombo =   tncdvalue.DESCRIPTIONS;
                    var strarray = txtoptscombo.split(',');
                    var opts = '';

                    for (var i = 0; i < strarray.length; i++) {
                        opts = opts + '<option value="'+strarray[i]+'">'+strarray[i]+'</option> ';
                    }

                    strinp = '<select disabled name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" required>'+opts+'</select>' ;
                   
                    }
                   
                    
                    $('#'+txt_id4).html('');  
                    $('#'+txt_id4).html(strinp);   //set dynamic input
                    $('#'+dynamicid).val(tncvalue.VALUE);
                    $('#TNCismandatory_'+tnckey).val(tncdvalue.IS_MANDATORY); // mandatory
                
            }
        });
    });
    
    $.each( obj, function( key, value ) {
        
        if(parseFloat(value.RETURN_QTY) > 0){

          var amtbeforedis = parseFloat(value.RATE_PUOM*value.RETURN_QTY).toFixed(2);

        }
        else{

          var amtbeforedis = parseFloat(value.RATE_PUOM).toFixed(2);

        }


        var dipercent = 0;
        var diamount = 0;

        if(dipercent > 0){
          var amtafterdis = parseFloat(amtbeforedis - (amtbeforedis*dipercent)/100).toFixed(2);
        }
        else{
          var amtafterdis = parseFloat(amtbeforedis - diamount).toFixed(2);
        }

        if(intRegex.test(amtafterdis)){
            amtafterdis = amtafterdis +'.00';
        }
        
        $('#DISAFTT_AMT_'+key).val(amtafterdis);
        var igstpercent = value.IGST;
        var igstamount  = parseFloat(((amtafterdis*igstpercent)/100)).toFixed(2);
        var cgstpercent = value.CGST;
        var cgstamount  = parseFloat((amtafterdis*cgstpercent)/100).toFixed(2);
        var sgstpercent = value.SGST;
        var sgstamount  = parseFloat((amtafterdis*sgstpercent)/100).toFixed(2);
        var totgsamt = parseFloat(parseFloat(igstamount) + parseFloat(cgstamount) + parseFloat(sgstamount)).toFixed(2);
        var amtaftergst = parseFloat(parseFloat(amtafterdis) + parseFloat(totgsamt)).toFixed(2);
        amtaftergst = parseFloat(amtaftergst).toFixed(2);

        if(intRegex.test(totgsamt)){
            totgsamt = totgsamt +'.00';
        }
        if(intRegex.test(amtaftergst)){
            amtaftergst = amtaftergst +'.00';
        }
        totalvalue += + amtaftergst;
        
        $('#TOT_AMT_'+key).val(amtaftergst);
        $('#TGST_AMT_'+key).val(totgsamt);
        $('#IGSTAMT_'+key).val(igstamount);
        $('#CGSTAMT_'+key).val(cgstamount);
        $('#SGSTAMT_'+key).val(sgstamount);
        if($('#DISCPER_'+key).val() > '.0000')
        {
          $('#DISCOUNT_AMT_'+key).prop('disabled',true);
        }
        else
        {
          $('#DISCOUNT_AMT_'+key).removeAttr('disabled');
        }
        if($('#DISCOUNT_AMT_'+key).val() > '.0000')
        {
          $('#DISCPER_'+key).prop('disabled',true);
        }
        else
        {
          $('#DISCPER_'+key).removeAttr('disabled');
        }

    });


    totalvalue = parseFloat(totalvalue).toFixed(2);
    $('#TotalValue').val(totalvalue);

    var d = new Date(); 
    var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    d.setDate(d.getDate() + 29);
    var todate = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    $('#JWRDT').val(today);

    
    
    
    $('#Material').on('keyup',"[id*='ALT_UOMID_QTY']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.000')
      }
      event.preventDefault();
    });
    function bindTotalValue()
    {
      var totalvalue = 0.00;
      var tvalue = 0.00;
      var ctvalue = 0.00;
      var ctgstvalue = 0.00;
      $('#Material').find('.participantRow').each(function()
      {
        tvalue = $(this).find('[id*="TOT_AMT"]').val();
        totalvalue = parseFloat(totalvalue) + parseFloat(tvalue);
        totalvalue = parseFloat(totalvalue).toFixed(2);
      });
      if($('#CTID_REF').val() != '')
      {
        $('#CT').find('.participantRow5').each(function()
        {
          ctvalue = $(this).find('[id*="VALUE"]').val();
          ctgstvalue = $(this).find('[id*="TOTGSTAMT"]').val();
          totalvalue = parseFloat(totalvalue) + parseFloat(ctvalue);
          totalvalue = parseFloat(totalvalue) + parseFloat(ctgstvalue);
          totalvalue = parseFloat(totalvalue).toFixed(2);
        });
      }
      $('#TotalValue').val(totalvalue);
    }

    $('#Material').on('focusout',"[id*='SO_QTY']",function(){

var totalvalue  = 0.00;
var itemid      = $(this).parent().parent().find('[id*="ITEMID_REF"]').val();
var mqty        = $(this).val();

var altuomid    = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').val();
var txtid       = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
var irate       = $(this).parent().parent().find('[id*="RATEPUOM"]').val();

$(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
$(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
$(this).parent().parent().find('[id*="SGSTAMT"]').val('0');


if(parseFloat(mqty) > 0){
  var tamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);
}
else{
  var tamt = parseFloat(parseFloat(irate)).toFixed(2);
}


var dispercnt = $(this).parent().parent().find('[id*="DISCPER"]').val();

var disamt = 0 ;      
if (dispercnt != '' && dispercnt != '.0000')
{
    disamt =  parseFloat((parseFloat(tamt)*parseFloat(dispercnt))/100).toFixed(2);
}
else if ($(this).parent().parent().find('[id*="DISCOUNT_AMT"]').val() != '' && $(this).parent().parent().find('[id*="DISCOUNT_AMT"]').val() != '0.00')
{
    disamt = $(this).parent().parent().find('[id*="DISCOUNT_AMT"]').val();
}


//alert(tamt);return false;
//tamt = parseFloat(parseFloat(tamt) - parseFloat(disamt)).toFixed(2);


var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
var tp1amt = parseFloat((tamt * tp1)/100).toFixed(2);
var tp2amt = parseFloat((tamt * tp2)/100).toFixed(2);
var tp3amt = parseFloat((tamt * tp3)/100).toFixed(2);
var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2); 
var totamt = parseFloat(parseFloat(tamt) + parseFloat(taxamt)).toFixed(2);

if(altuomid!=''){
      
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("transaction",[$FormId,"getaltuomqty"])}}',
        type:'POST',
        data:{'id':altuomid, 'itemid':itemid, 'mqty':mqty},
        success:function(data) {
          if(intRegex.test(data)){
              data = (data +'.000');
          }
          $("#"+txtid).val(data);                        
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#"+txtid).val('');                        
        },
    }); 
        
}



if(intRegex.test($(this).val())){
  $(this).val($(this).val()+'.000');
}
if(intRegex.test(tamt)){
  tamt = tamt +'.00';
}
if(intRegex.test(totamt)){
  totamt = totamt +'.00';
}
if(intRegex.test(taxamt)){
  taxamt = taxamt +'.00';
}
if(intRegex.test(tp1amt)){
  tp1amt = tp1amt +'.00';
}
if(intRegex.test(tp2amt)){
  tp2amt = tp2amt +'.00';
}
if(intRegex.test(tp3amt)){
  tp3amt = tp3amt +'.00';
}



$(this).parent().parent().find('[id*="DISAFTT_AMT"]').val(tamt);
$(this).parent().parent().find('[id*="TOT_AMT"]').val(totamt);
$(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
$(this).parent().parent().find('[id*="IGSTAMT"]').val(tp1amt);
$(this).parent().parent().find('[id*="CGSTAMT"]').val(tp2amt);
$(this).parent().parent().find('[id*="SGSTAMT"]').val(tp3amt);



bindTotalValue();
if($('#CTID_REF').val()!='')
{
bindGSTCalTemplate();
}
bindTotalValue();
event.preventDefault();
});


$('#Material').on('focusout',"[id*='RATEPUOM']",function()
{
  var mqty = $(this).parent().parent().find('[id*="SO_QTY"]').val();
  var irate = $(this).val();
  var taxamt = $(this).parent().parent().find('[id*="TGST_AMT"]').val();
          
  if(parseFloat(mqty) > 0){
    var tamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);
  }
  else{
    var tamt = parseFloat(parseFloat(irate)).toFixed(2);
  }


  //var dispercnt = $(this).parent().parent().find('[id*="DISCPER"]').val();
  var dispercnt = 0;
  var disamt = 0 ;      
  if (dispercnt != '' && dispercnt != '.0000')
  {
     disamt =  parseFloat((parseFloat(tamt)*parseFloat(dispercnt))/100).toFixed(2);
  }
  else if ($(this).parent().parent().find('[id*="DISCOUNT_AMT"]').val() != '' && $(this).parent().parent().find('[id*="DISCOUNT_AMT"]').val() != '0.00')
  {
     //disamt = $(this).parent().parent().find('[id*="DISCOUNT_AMT"]').val();
     disamt = 0;
  }

  tamt = parseFloat(parseFloat(tamt) - parseFloat(disamt)).toFixed(2);        
  var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
  var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
  var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
  $(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
  $(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
  $(this).parent().parent().find('[id*="SGSTAMT"]').val('0');
  var tp1amt = parseFloat((tamt * tp1)/100).toFixed(2);
  var tp2amt = parseFloat((tamt * tp2)/100).toFixed(2);
  var tp3amt = parseFloat((tamt * tp3)/100).toFixed(2);
  var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2); 
  var totamt = parseFloat(parseFloat(tamt) + parseFloat(taxamt)).toFixed(2);
if(intRegex.test($(this).val())){
  $(this).val($(this).val()+'.00000')
}
if(intRegex.test(tamt)){
  tamt = tamt +'.00';
}
if(intRegex.test(totamt)){
totamt = totamt +'.00';
}
if(intRegex.test(taxamt)){
taxamt = taxamt +'.00';
}
if(intRegex.test(tp1amt)){
  tp1amt = tp1amt +'.00';
}
if(intRegex.test(tp2amt)){
  tp2amt = tp2amt +'.00';
}
if(intRegex.test(tp3amt)){
  tp3amt = tp3amt +'.00';
}
$(this).parent().parent().find('[id*="DISAFTT_AMT"]').val(tamt);
$(this).parent().parent().find('[id*="TOT_AMT"]').val(totamt);
$(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
$(this).parent().parent().find('[id*="IGSTAMT"]').val(tp1amt);
$(this).parent().parent().find('[id*="CGSTAMT"]').val(tp2amt);
$(this).parent().parent().find('[id*="SGSTAMT"]').val(tp3amt);
bindTotalValue();
if($('#CTID_REF').val()!='')
{
bindGSTCalTemplate();
}
bindTotalValue();
event.preventDefault();
});     

    $('#Material').on('focusout',"[id*='DISCPER']",function()
    { 
      var mqty = $(this).parent().parent().find('[id*="SO_QTY"]').val();
      var irate = $(this).parent().parent().find('[id*="RATEPUOM"]').val();
      var totamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);
      var dpert = $(this).val();
      var disamt = $(this).parent().parent().find('[id*="DISCOUNT_AMT"]').val();
      if (dpert != '' && dpert != '.0000')
      {
        var amtfd = parseFloat(parseFloat(totamt) - (parseFloat(totamt)*parseFloat(dpert))/100).toFixed(2);
        if(intRegex.test($(this).val())){
          $(this).val($(this).val()+'.0000')
        }
      var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
      var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
      var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
      $(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
      $(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
      $(this).parent().parent().find('[id*="SGSTAMT"]').val('0');
      var tp1amt = parseFloat((amtfd * tp1)/100).toFixed(2);
      var tp2amt = parseFloat((amtfd * tp2)/100).toFixed(2);
      var tp3amt = parseFloat((amtfd * tp3)/100).toFixed(2);
     
      var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2);      
      var netamt = parseFloat(parseFloat(amtfd) + parseFloat(taxamt)).toFixed(2);
      if(intRegex.test(amtfd)){
        amtfd = amtfd +'.00';
      }
      if(intRegex.test(taxamt)){
      taxamt = taxamt +'.00';
      }
      if(intRegex.test(tp1amt)){
        tp1amt = tp1amt +'.00';
      }
      if(intRegex.test(tp2amt)){
        tp2amt = tp2amt +'.00';
      }
      if(intRegex.test(tp3amt)){
        tp3amt = tp3amt +'.00';
      }
      if(intRegex.test(netamt)){
        netamt = netamt +'.00';
      }
      $(this).parent().parent().find('[id*="DISCOUNT_AMT"]').prop('disabled',true);
      $(this).parent().parent().find('[id*="DISAFTT_AMT"]').val(amtfd);
      $(this).parent().parent().find('[id*="TOT_AMT"]').val(netamt);
      $(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
      $(this).parent().parent().find('[id*="IGSTAMT"]').val(tp1amt);
      $(this).parent().parent().find('[id*="CGSTAMT"]').val(tp2amt);
      $(this).parent().parent().find('[id*="SGSTAMT"]').val(tp3amt);
      }
      else if (disamt != '' && disamt != '.00')
      {
        var amtfd = parseFloat(parseFloat(totamt) - parseFloat(disamt)).toFixed(2);
        if(intRegex.test($(this).val())){
          $(this).val($(this).val()+'.00')
        }
      var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
      var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
      var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
      $(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
      $(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
      $(this).parent().parent().find('[id*="SGSTAMT"]').val('0');
      var tp1amt = parseFloat((amtfd * tp1)/100).toFixed(2);
      var tp2amt = parseFloat((amtfd * tp2)/100).toFixed(2);
      var tp3amt = parseFloat((amtfd * tp3)/100).toFixed(2);
     
      var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2);      
      var netamt = parseFloat(parseFloat(amtfd) + parseFloat(taxamt)).toFixed(2);
      if(intRegex.test(amtfd)){
        amtfd = amtfd +'.00';
      }
      if(intRegex.test(taxamt)){
      taxamt = taxamt +'.00';
      }
      if(intRegex.test(tp1amt)){
        tp1amt = tp1amt +'.00';
      }
      if(intRegex.test(tp2amt)){
        tp2amt = tp2amt +'.00';
      }
      if(intRegex.test(tp3amt)){
        tp3amt = tp3amt +'.00';
      }
      if(intRegex.test(netamt)){
        netamt = netamt +'.00';
      }
      $(this).parent().parent().find('[id*="DISCPER"]').prop('readonly',true);
      $(this).parent().parent().find('[id*="DISAFTT_AMT"]').val(amtfd);
      $(this).parent().parent().find('[id*="TOT_AMT"]').val(netamt);
      $(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
      $(this).parent().parent().find('[id*="IGSTAMT"]').val(tp1amt);
      $(this).parent().parent().find('[id*="CGSTAMT"]').val(tp2amt);
      $(this).parent().parent().find('[id*="SGSTAMT"]').val(tp3amt);
      }
      else{
        var amtfd = parseFloat(totamt).toFixed(2);
        var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
        var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
        var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
        $(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
        $(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
        $(this).parent().parent().find('[id*="SGSTAMT"]').val('0');
        var tp1amt = parseFloat((amtfd * tp1)/100).toFixed(2);
        var tp2amt = parseFloat((amtfd * tp2)/100).toFixed(2);
        var tp3amt = parseFloat((amtfd * tp3)/100).toFixed(2);
        var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2);      
      var netamt = parseFloat(parseFloat(amtfd) + parseFloat(taxamt)).toFixed(2);
        if(intRegex.test($(this).val())){
          $(this).val($(this).val()+'.00')
        }
        if(intRegex.test(amtfd)){
          amtfd = amtfd +'.00';
        }
        if(intRegex.test(taxamt)){
        taxamt = taxamt +'.00';
        }
        if(intRegex.test(tp1amt)){
          tp1amt = tp1amt +'.00';
        }
        if(intRegex.test(tp2amt)){
          tp2amt = tp2amt +'.00';
        }
        if(intRegex.test(tp3amt)){
          tp3amt = tp3amt +'.00';
        }
        if(intRegex.test(netamt)){
          netamt = netamt +'.00';
        }
        $(this).parent().parent().find('[id*="DISCOUNT_AMT"]').removeAttr('disabled');
        $(this).parent().parent().find('[id*="DISAFTT_AMT"]').val(amtfd);
        $(this).parent().parent().find('[id*="TOT_AMT"]').val(netamt);
        $(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
        $(this).parent().parent().find('[id*="IGSTAMT"]').val(tp1amt);
        $(this).parent().parent().find('[id*="CGSTAMT"]').val(tp2amt);
        $(this).parent().parent().find('[id*="SGSTAMT"]').val(tp3amt);
      }
      bindTotalValue();
      if($('#CTID_REF').val()!='')
      {
      bindGSTCalTemplate();
      }
      bindTotalValue();
      event.preventDefault();
    });

    $('#Material').on('focusout',"[id*='DISCOUNT_AMT']",function()
    {
      var mqty = $(this).parent().parent().find('[id*="SO_QTY"]').val();
      var irate = $(this).parent().parent().find('[id*="RATEPUOM"]').val();
      var totamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);
      var dpert = $(this).val();
      var dispercent = $(this).parent().parent().find('[id*="DISCPER"]').val();
      if (dpert != '' && dpert != '.00')
      {
        var amtfd = parseFloat(totamt) - parseFloat(dpert);
        if(intRegex.test($(this).val())){
          $(this).val($(this).val()+'.00')
        }
        var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
        var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
        var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
        $(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
        $(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
        $(this).parent().parent().find('[id*="SGSTAMT"]').val('0');
        var tp1amt = parseFloat((amtfd * tp1)/100).toFixed(2);
        var tp2amt = parseFloat((amtfd * tp2)/100).toFixed(2);
        var tp3amt = parseFloat((amtfd * tp3)/100).toFixed(2);
        var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2);
        var netamt = parseFloat(parseFloat(amtfd) + parseFloat(taxamt)).toFixed(2);
        if(intRegex.test(amtfd)){
          amtfd = amtfd +'.00';
        }
        if(intRegex.test(taxamt)){
        taxamt = taxamt +'.00';
        }
        if(intRegex.test(tp1amt)){
          tp1amt = tp1amt +'.00';
        }
        if(intRegex.test(tp2amt)){
          tp2amt = tp2amt +'.00';
        }
        if(intRegex.test(tp3amt)){
          tp3amt = tp3amt +'.00';
        }
        if(intRegex.test(netamt)){
          netamt = netamt +'.00';
        }
        $(this).parent().parent().find('[id*="DISCPER"]').prop('disabled',true);
        $(this).parent().parent().find('[id*="DISAFTT_AMT"]').val(amtfd);
        $(this).parent().parent().find('[id*="TOT_AMT"]').val(netamt);
        $(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
        $(this).parent().parent().find('[id*="IGSTAMT"]').val(tp1amt);
        $(this).parent().parent().find('[id*="CGSTAMT"]').val(tp2amt);
        $(this).parent().parent().find('[id*="SGSTAMT"]').val(tp3amt);
      }
      else if (dispercent != '' && dispercent != '.0000')
      {
        var amtfd = parseFloat(parseFloat(totamt) - (parseFloat(totamt)*parseFloat(dispercent))/100).toFixed(2);
        if(intRegex.test($(this).val())){
          $(this).val($(this).val()+'.00')
        }
      var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
      var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
      var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
      $(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
      $(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
      $(this).parent().parent().find('[id*="SGSTAMT"]').val('0');
      var tp1amt = parseFloat((amtfd * tp1)/100).toFixed(2);
      var tp2amt = parseFloat((amtfd * tp2)/100).toFixed(2);
      var tp3amt = parseFloat((amtfd * tp3)/100).toFixed(2);
     
      var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2);      
      var netamt = parseFloat(parseFloat(amtfd) + parseFloat(taxamt)).toFixed(2);
      if(intRegex.test(amtfd)){
        amtfd = amtfd +'.00';
      }
      if(intRegex.test(taxamt)){
      taxamt = taxamt +'.00';
      }
      if(intRegex.test(tp1amt)){
        tp1amt = tp1amt +'.00';
      }
      if(intRegex.test(tp2amt)){
        tp2amt = tp2amt +'.00';
      }
      if(intRegex.test(tp3amt)){
        tp3amt = tp3amt +'.00';
      }
      if(intRegex.test(netamt)){
        netamt = netamt +'.00';
      }
      $(this).parent().parent().find('[id*="DISCOUNT_AMT"]').prop('readonly',true);
      $(this).parent().parent().find('[id*="DISAFTT_AMT"]').val(amtfd);
      $(this).parent().parent().find('[id*="TOT_AMT"]').val(netamt);
      $(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
      $(this).parent().parent().find('[id*="IGSTAMT"]').val(tp1amt);
      $(this).parent().parent().find('[id*="CGSTAMT"]').val(tp2amt);
      $(this).parent().parent().find('[id*="SGSTAMT"]').val(tp3amt);
      }
      else{
        var amtfd = parseFloat(totamt).toFixed(2);
        var tp1 = $(this).parent().parent().find('[id*="IGST_"]').val();
        var tp2 = $(this).parent().parent().find('[id*="CGST_"]').val();
        var tp3 = $(this).parent().parent().find('[id*="SGST_"]').val();
        $(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
        $(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
        $(this).parent().parent().find('[id*="SGSTAMT"]').val('0');
        var tp1amt = parseFloat((amtfd * tp1)/100).toFixed(2);
        var tp2amt = parseFloat((amtfd * tp2)/100).toFixed(2);
        var tp3amt = parseFloat((amtfd * tp3)/100).toFixed(2);
        var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2);
        var netamt = parseFloat(parseFloat(amtfd) + parseFloat(taxamt)).toFixed(2);
        if(intRegex.test($(this).val())){
          $(this).val($(this).val()+'.00')
        }
        if(intRegex.test(amtfd)){
          amtfd = amtfd +'.00';
        }
        if(intRegex.test(taxamt)){
        taxamt = taxamt +'.00';
        }
        if(intRegex.test(tp1amt)){
          tp1amt = tp1amt +'.00';
        }
        if(intRegex.test(tp2amt)){
          tp2amt = tp2amt +'.00';
        }
        if(intRegex.test(tp3amt)){
          tp3amt = tp3amt +'.00';
        }
        if(intRegex.test(netamt)){
          netamt = netamt +'.00';
        }
        $(this).parent().parent().find('[id*="DISCPER"]').removeAttr('disabled');
        $(this).parent().parent().find('[id*="DISAFTT_AMT"]').val(amtfd);
        $(this).parent().parent().find('[id*="TOT_AMT"]').val(netamt);
        $(this).parent().parent().find('[id*="TGST_AMT"]').val(taxamt);
        $(this).parent().parent().find('[id*="IGSTAMT"]').val(tp1amt);
        $(this).parent().parent().find('[id*="CGSTAMT"]').val(tp2amt);
        $(this).parent().parent().find('[id*="SGSTAMT"]').val(tp3amt);
      }
      bindTotalValue();
      if($('#CTID_REF').val()!='')
      {
      bindGSTCalTemplate();
      }
      bindTotalValue();
      event.preventDefault();
    });

    

    $('#Material').on('focusout',"[id*='IGST']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000')
      }
    });

    $('#Material').on('focusout',"[id*='IGST_AMT']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00')
      }
    });

    $('#Material').on('focusout',"[id*='CGST']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000')
      }
    });

    $('#Material').on('focusout',"[id*='CGST_AMT']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00')
      }
    });

    $('#Material').on('focusout',"[id*='SGST']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000')
      }
    });

    $('#Material').on('focusout',"[id*='SGST_AMT']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00')
      }
    });

    $('#Material').on('focusout',"[id*='TGST_AMT']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00')
      }
    });

    $('#Material').on('keyup',"[id*='TOT_AMT']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00')
      }
    });

    $('#CT').on('focusout',"[id*='calSGST_']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000')
      }
    });

    $('#CT').on('focusout',"[id*='calCGST_']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000')
      }
    });

    $('#CT').on('focusout',"[id*='calIGST_']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000')
      }
    });

    $('#btnAdd').on('click', function() {
        var viewURL = '{{route("transaction",[$FormId,"add"])}}';
                  window.location.href=viewURL;
    });
    $('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });
     

//SO Date Check
$('#JWRDT').change(function( event ) {
            var today = new Date();     
            var d = new Date($(this).val()); 
            today.setHours(0, 0, 0, 0) ;
            d.setHours(0, 0, 0, 0) ;
            var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
            if (d < today) {
                $(this).val(sodate);
                $("#alert").modal('show');
                $("#AlertMessage").text('SO Date cannot be less than Current date');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                event.preventDefault();
            }
            else
            {
                event.preventDefault();
            }

           
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
     
   }//fnUndoNo




  $("#CT").on('change',"[id*='calGST']",function() {
      if ($(this).is(":checked") == true){
          if($.trim($('#Tax_State').val()) == 'OutofState')
          {
            $(this).parent().parent().find('[id*="calIGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="calIGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="calCGST"]').prop('readonly','true');
            $(this).parent().parent().find('[id*="calSGST"]').prop('readonly','true');
            $(this).parent().parent().find('[id*="AMTIGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="AMTIGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="AMTCGST"]').prop('readonly','true');
            $(this).parent().parent().find('[id*="AMTSGST"]').prop('readonly','true');
            $(this).parent().parent().find('[id*="calCGST"]').val('0');
            $(this).parent().parent().find('[id*="calSGST"]').val('0');
            $(this).parent().parent().find('[id*="AMTCGST"]').val('0');
            $(this).parent().parent().find('[id*="AMTSGST"]').val('0');
            $(this).parent().parent().find('[id*="TOTGSTAMT"]').val('0');
            bindTotalValue();
            event.preventDefault();
          }
          else
          {
            $(this).parent().parent().find('[id*="calIGST"]').prop('readonly','true');
            $(this).parent().parent().find('[id*="calCGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="calSGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="calCGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="calSGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="AMTIGST"]').prop('readonly','true');
            $(this).parent().parent().find('[id*="AMTCGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="AMTSGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="AMTCGST"]').removeAttr('disabled');
            $(this).parent().parent().find('[id*="AMTSGST"]').removeAttr('readonly');
            $(this).parent().parent().find('[id*="calIGST"]').val('0');
            $(this).parent().parent().find('[id*="AMTIGST"]').val('0');
            $(this).parent().parent().find('[id*="TOTGSTAMT"]').val('0');
            bindTotalValue();
            event.preventDefault();
          }
      }
      else
      {
          $(this).parent().parent().find('[id*="calIGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="calCGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="calSGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="AMTIGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="AMTCGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="AMTSGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="calIGST"]').val('0');
          $(this).parent().parent().find('[id*="calCGST"]').val('0');
          $(this).parent().parent().find('[id*="calSGST"]').val('0');
          $(this).parent().parent().find('[id*="AMTIGST"]').val('0');
          $(this).parent().parent().find('[id*="AMTCGST"]').val('0');
          $(this).parent().parent().find('[id*="AMTSGST"]').val('0');
          $(this).parent().parent().find('[id*="TOTGSTAMT"]').val('0');
          bindTotalValue();
          event.preventDefault();
      }
  });
  $("#CT").on('change',"[id*='calIGST_']",function() {
      var rate = $(this).val();
      var total = $(this).parent().parent().find('[id*="VALUE_"]').val();
      var gstamt = parseFloat((rate*total)/100).toFixed(2);
      var totgst = $(this).parent().parent().find('[id*="TOTGSTAMT_"]').val();
      totgst = parseFloat(parseFloat(gstamt)).toFixed(2);;
      $(this).parent().parent().find('[id*="AMTIGST_"]').val(gstamt);
      $(this).parent().parent().find('[id*="TOTGSTAMT_"]').val(totgst);
      bindTotalValue();
      event.preventDefault();
  });
  $("#CT").on('change',"[id*='calCGST_']",function() {
      var rate2 = $(this).val();
      var total2 = $(this).parent().parent().find('[id*="VALUE_"]').val();
      var gstamt2 = parseFloat((rate2*total2)/100).toFixed(2);
      var totgst2 = $(this).parent().parent().find('[id*="TOTGSTAMT_"]').val();
      var sgstamt = $(this).parent().parent().find('[id*="AMTSGST_"]').val();
      totgst2 = parseFloat(parseFloat(sgstamt) + parseFloat(gstamt2)).toFixed(2);
      $(this).parent().parent().find('[id*="AMTCGST_"]').val(gstamt2);
      $(this).parent().parent().find('[id*="TOTGSTAMT_"]').val(totgst2);
      bindTotalValue();
      event.preventDefault();
  }); 
  $("#CT").on('change',"[id*='calSGST_']",function() {
      var rate3 = $(this).val();
      var total3 = $(this).parent().parent().find('[id*="VALUE_"]').val();
      var gstamt3 = parseFloat((rate3*total3)/100).toFixed(2);
      var totgst3 = $(this).parent().parent().find('[id*="TOTGSTAMT_"]').val();
      var cgstamt = $(this).parent().parent().find('[id*="AMTCGST_"]').val();
      totgst3 = parseFloat(parseFloat(cgstamt) + parseFloat(gstamt3)).toFixed(2);;
      $(this).parent().parent().find('[id*="AMTSGST_"]').val(gstamt3);
      $(this).parent().parent().find('[id*="TOTGSTAMT_"]').val(totgst3);
      bindTotalValue();
      event.preventDefault();
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

$(document).ready(function() {

    $('#frm_trn_view1').bootstrapValidator({
       
        fields: {
            txtlabel: {
                validators: {
                    notEmpty: {
                        message: 'The JWR NO is required'
                    }
                }
            },            
        },
        submitHandler: function(validator, form, submitButton) {
            alert( "Handler for .submit() called." );
             event.preventDefault();
             $("#frm_trn_view").submit();
        }
    });
});


$( "#btnSaveData" ).click(function() {
  var formSalesOrder = $("#frm_trn_view");
  if(formSalesOrder.valid()){
    validateForm("fnSaveData","update");
  }
});
$( "#btnApprove" ).click(function() {
  var formSalesOrder = $("#frm_trn_view");
  if(formSalesOrder.valid()){
    validateForm("fnApproveData","approve");
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

     var trnsoForm = $("#frm_trn_view");
    var formData = trnsoForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("transactionmodify",[$FormId,"update"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
       
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.JWRNO){
                showError('ERROR_JWRNO',data.errors.JWRNO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in JWR NO.');
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

     var trnsoForm = $("#frm_trn_view");
    var formData = trnsoForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("transactionmodify",[$FormId,"Approve"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
       
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.JWRNO){
                showError('ERROR_JWRNO',data.errors.JWRNO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in CSV NO.');
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
    // $("[id*=txtlabel]").focus();
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




function showAlert(msg,smgid){
  $("#FocusId").val(smgid);
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


$('#Material').on('change','[id*="flagtype"]',function(event){
  $('#Material').find('.participantRow').each(function(){ 

    var divid       =   $(this).find('[id]').attr('id');
    var IGST        =   $(this).find('[id*="IGST"]').val();
    var CGST        =   $(this).find('[id*="CGST"]').val();
    var SGST        =   $(this).find('[id*="SGST"]').val();
    var ITEMID_REF  =   $(this).find('[id*="ITEMID_REF"]').val();
    var Tax_State   =   $("#Tax_State").val();

    if($(this).find('[id*="flagtype"]').is(":checked") == false){
      $(this).find('[id*="IGST"]').val('0.000');
      $(this).find('[id*="IGSTAMT"]').val('0.000');
      $(this).find('[id*="CGST"]').val('0.000');
      $(this).find('[id*="CGSTAMT"]').val('0.000');
      $(this).find('[id*="SGST"]').val('0.000');
      $(this).find('[id*="SGSTAMT"]').val('0.000');
      $(this).find('[id*="TGST_AMT"]').val('0.000');
      $(this).find('[id*="TOT_AMT"]').val($(this).find('[id*="DISAFTT_AMT"]').val());
    }
    else if($(this).find('[id*="flagtype"]').is(":checked") == true && IGST < 1 && CGST < 1 && SGST < 1){
      
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
          url:'{{route("transaction",[$FormId,"getTax"])}}',
          type:'POST',
          data:{ITEMID_REF:ITEMID_REF,Tax_State:Tax_State},
          success:function(data) {
              var myObj = JSON.parse(data);

              if(Tax_State =='OutofState'){
                $("#CGST_"+divid).val(myObj[0]);
                $("#SGST_"+divid).val(myObj[1]);
              }
              else{
                $("#IGST_"+divid).val(myObj[0]);
              }

              $("#RATEPUOM_"+divid).blur();

          },
          error:function(data){
            console.log("Error: Something went wrong.");
          },
      });        

    }
    $(".blurRate").blur();
  });
});

//====================================== VALIDATE FORM ======================================

function validateForm(saveAction,saveMsg){

  $("#FocusId").val('');

  var JWRNO             = $.trim($("#JWRNO").val());
  var JWRDT             = $.trim($("#JWRDT").val());
  var VID_REF           = $.trim($("#VID_REF").val());
  var VENDOR_INVOICE_NO = $.trim($("#VENDOR_INVOICE_NO").val());

  if(JWRNO ===""){
    showAlert('Please Enter JWR NO.','JWRNO');
  }
  else if(JWRDT ===""){
    showAlert('Please Select JWR Date.','JWRDT');
  } 
  else if(VID_REF ===""){
    showAlert('Please Select Vendor.','txtVID_popup');
  }
  else if(VENDOR_INVOICE_NO ===""){
    showAlert('Please Select Vendor Invoice No.','VENDOR_INVOICE_NO');
  }
  else{

    event.preventDefault();

    var RackArray   = []; 
    var allblank00  = [];
    var allblank01  = [];
    var allblank02  = [];
    var allblank03  = [];
    var allblank04  = [];
    var allblank05  = [];
    var allblank06  = [];
    var allblank07  = [];
    var allblank08  = [];
    var allblank09  = [];
    var allblank5 = [];
    var allblank6 = [];
    var allblank7 = [];
    var allblank8 = [];
    var allblank9 = [];
    var allblank10 = [];
    var allblank11 = [];
    var allblank12 = [];
    var allblank66 = [];
    var allblank77 = [];

    var focustext00 = "";
    var focustext01 = "";
    var focustext02 = "";
    var focustext03 = "";
    var focustext04 = "";
    var focustext05 = "";
    var focustext06 = "";
    var focustext07 = "";
    var focustext08 = "";
    var focustext09 = "";
    var focustext10 = "";
    var focustext11 = "";
    var focustext12 = "";

      
    $('#Material').find('.participantRow').each(function(){

      var SSOID_REF       =   $.trim($(this).find('[id*="JWID_REF"]').val());
      var ITEMID_REF      =   $.trim($(this).find('[id*="ITEMID_REF"]').val());
      var exist           =   $.trim($(this).find('[id*="exist"]').val());
      var SQMUOMQTY       =   $.trim($(this).find('[id*="SQMUOMQTY"]').val());
      var SO_QTY          =   $.trim($(this).find('[id*="SO_QTY"]').val());
      var TotalHiddenQty  =   $.trim($(this).find('[id*="TotalHiddenQty"]').val());
      

      if($.trim($(this).find('[id*="JWID_REF"]').val()) != ""){
        allblank00.push('true');
      }
      else{
        allblank00.push('false');
        focustext00 = $(this).find("[id*=txtJWID_popup]").attr('id');
      }  

      if($.trim($(this).find("[id*=ITEMID_REF]").val())!=""){
        allblank01.push('true');
      }
      else{
        allblank01.push('false');
        focustext01 = $(this).find("[id*=popupITEMID]").attr('id');
      }

      if (RackArray.indexOf(exist) > -1) {
        allblank02.push('true');
      }
      else{
        allblank02.push('false');
        focustext02 = $(this).find("[id*=popupITEMID]").attr('id');
      }

      if($.trim($(this).find("[id*=popupMUOM]").val())!=""){
        allblank03.push('true');
      }
      else{
        allblank03.push('false');
        focustext03 = $(this).find("[id*=popupMUOM]").attr('id');
      }  

      if($.trim($(this).find('[id*="SO_QTY"]').val()) != ""){
        allblank04.push('true');  
      }
      else{
        allblank04.push('false');
        focustext04 = $(this).find("[id*=SO_QTY]").attr('id');
      }

      if(parseFloat(SQMUOMQTY) >= parseFloat(SO_QTY)){
        allblank05.push('true');
      }
      else{
        allblank05.push('false');
        focustext05 = $(this).find("[id*=SO_QTY]").attr('id');
      }   


      if($.trim($(this).find('[id*="RATEPUOM"]').val()) != ""){
        if(parseFloat($.trim($(this).find('[id*="RATEPUOM"]').val())) > 0.000 ){
          allblank08.push('true');
        }
        else{
          allblank08.push('false');
          focustext08 = $(this).find("[id*=RATEPUOM]").attr('id');
        }  
      }
      else{
        allblank08.push('false');
        focustext08 = $(this).find("[id*=RATEPUOM]").attr('id');
      } 

      if(TotalHiddenQty !=""){
        allblank09.push('true');
      }
      else{
        allblank09.push('false');
        focustext09 = $(this).find("[id*=SO_QTY]").attr('id');
      }
      

      if(parseFloat(SO_QTY) == parseFloat(TotalHiddenQty)){
        allblank09.push('true');
      }
      else{
        allblank09.push('false');
        focustext09 = $(this).find("[id*=SO_QTY]").attr('id');
      }

      RackArray.push(exist);

    });

    

    $('#udf').find('.participantRow4').each(function(){
          if($.trim($(this).find("[id*=UDFSOID_REF]").val())!="")
            {
                allblank8.push('true');
                    if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
                          if($.trim($(this).find('[id*="udfvalue"]').val()) != "")
                          {
                            allblank9.push('true');
                          }
                          else
                          {
                            allblank9.push('false');
                          }
                    }  
            }                
    });



    if($('#TNCID_REF').val() !=""){
        $('#TC').find('.participantRow3').each(function(){
          if($.trim($(this).find("[id*=TNCDID_REF]").val())!="")
            {
                allblank66.push('true');
                    if($.trim($(this).find("[id*=TNCismandatory]").val())=="1"){
                          if($.trim($(this).find('[id*="tncdetvalue"]').val()) != "")
                          {
                            allblank77.push('true');
                          }
                          else
                          {
                            allblank77.push('false');
                          } 
                    } 
            }
            else
            {
                allblank66.push('false');
            } 
        });
    }


    if($('#CTID_REF').val() !=""){
        $('#CT').find('.participantRow5').each(function(){
          if($.trim($(this).find("[id*=TID_REF]").val())!="")
            {
                
                    if($(this).find("[id*=calGST]").is(":checked") == true)
                    {
                      if($.trim($('#Tax_State').val())!="WithinState")
                      {
                        if($.trim($(this).find("[id*=calIGST]").val())!="0")
                        {
                          allblank11.push('true');
                        }
                        else
                        {
                          allblank11.push('false');
                        }
                      }
                      else
                      {
                        if($.trim($(this).find("[id*=calCGST]").val())!="0")
                        {
                          allblank11.push('true');
                        }
                        else
                        {
                          allblank11.push('false');
                        }
                        if($.trim($(this).find("[id*=calSGST]").val())!="0")
                        {
                          allblank11.push('true');
                        }
                        else
                        {
                          allblank11.push('false');
                        }
                      }
                    } 
            }
            else
            {
                allblank10.push('false');
            } 
        });
    }


    if(jQuery.inArray("false", allblank00) !== -1){
      showAlert('Please Select Job Work Invoice In Material Tab.',focustext00);
    }
    else if(jQuery.inArray("false", allblank01) !== -1){
      showAlert('Please select Item Code in Material Tab.',focustext01);
    }
    else if(jQuery.inArray("true", allblank02) !== -1){
      showAlert('Duplicate Job Work Invoice/Item Code in Material Tab.',focustext02);
    }
    else if(jQuery.inArray("false", allblank03) !== -1){
      showAlert('UOM section is missing in Material Tab.',focustext03);
    }
    else if(jQuery.inArray("false", allblank04) !== -1){
      showAlert('Return Qty cannot be blank in Material Tab.',focustext04);
    }
    else if(jQuery.inArray("false", allblank05) !== -1){
      showAlert('Return Qty cannot be greater then Invoice Qty in Material Tab.',focustext05);
    }
    else if(jQuery.inArray("false", allblank08) !== -1){
      showAlert('Rate cannot be zero or blank in Material Tab.',focustext08);
    }
    else if(jQuery.inArray("false", allblank09) !== -1){
      showAlert('Return Qty not equal of store Qty in Material Tab.',focustext09);
    }
    else if(jQuery.inArray("false", allblank9) !== -1){
      showAlert('Please enter  Value / Comment in UDF Tab.');
    }
    else if(jQuery.inArray("false", allblank66) !== -1){
      showAlert('Please select Terms & Condition Description in T&C Tab.');
    }
    else if(jQuery.inArray("false", allblank77) !== -1){
      showAlert('Please enter Value / Comment in T&C Tab.');
    }
    else if(jQuery.inArray("false", allblank10) !== -1){
      showAlert('Please select Calculation Component in Calculation Template Tab.');
    }
    else if(jQuery.inArray("false", allblank11) !== -1){
      showAlert('Please Enter GST Rate / Value in Calculation Template Tab.');
    }
    else{
        $("#alert").modal('show');
        $("#AlertMessage").text('Do you want to '+saveMsg+' to record.');
        $("#YesBtn").data("funcname",saveAction);
        $("#YesBtn").focus();
        $("#OkBtn").hide();
        highlighFocusBtn('activeYes');
    }
  }

}

//====================================== SELECT POPUP ======================================

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

/*================================== STORE FUNCTION ==================================*/

function getStore(ROW_ID){

  var ITEMID_REF  = $("#ITEMID_REF_"+ROW_ID).val();

  if(ITEMID_REF ===""){
    $("#FocusId").val("popupITEMID_"+ROW_ID);    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Item In Material.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else{
    getStoreDetails(ROW_ID);
    $("#StoreModal").show();
  }

  event.preventDefault();
}

function getStoreDetails(ROW_ID){

  var ITEMID_REF  = $("#ITEMID_REF_"+ROW_ID).val();
  var ITEMROWID   = $("#HiddenRowId_"+ROW_ID).val();
  var UOMID_REF   = $("#MAIN_UOMID_REF_"+ROW_ID).val();

  $("#StoreTable").html('');
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
      url:'{{route("transaction",[$FormId,"getStoreDetails"])}}',
      type:'POST',
      data:{ITEMID_REF:ITEMID_REF,ROW_ID:ROW_ID,ITEMROWID:ITEMROWID,ACTION_TYPE:'EDIT',UOMID_REF:UOMID_REF},
      success:function(data) {
        $("#StoreTable").html(data);                
      },
      error:function(data){
        console.log("Error: Something went wrong.");
        $("#StoreTable").html('');                        
      },
  }); 
}

$("#StoreModalClose").click(function(event){
  $("#StoreModal").hide();
});


function checkStoreQty(ROW_ID,stockQty,userQty,key){

  if(userQty > stockQty){
    $("#UserQty_"+key).val('');
    $("#FocusId").val("#UserQty_"+key);
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Dispatch Qty should not greater then Stock-in-hand');
    $("#alert").modal('show')
    $("#OkBtn1").focus();
    return false;
  } 
  else{

      var NewQtyArr = [];
      var NewIdArr  = [];
      var NewStArr  = [];

      $('#StoreTable').find('.participantRow33').each(function(){

          if($.trim($(this).find("[id*=UserQty]").val())!=""){  
            var UserQty      = parseFloat($.trim($(this).find("[id*=UserQty]").val()));
            var BatchId      = $.trim($(this).find("[id*=BATCHID]").val());
            var StoreName    = $.trim($(this).find("[id*=STORENAME]").val());

            NewQtyArr.push(UserQty);
            NewIdArr.push(BatchId+"_"+UserQty);
          
            if($.inArray(StoreName, NewStArr) === -1) NewStArr.push(StoreName);

          }                
      });

      var TotalQty= getArraySum(NewQtyArr); 
      $("#TotalHiddenQty_"+ROW_ID).val(TotalQty);
      $("#HiddenRowId_"+ROW_ID).val(NewIdArr);
      $("#SO_QTY_"+ROW_ID).val(TotalQty);
      $("#STORE_NAME_"+ROW_ID).val(NewStArr);
      $(".blurRate").blur(); 

    }
}

function getArraySum(a){
  var total=0;
  for(var i in a) { 
    total += a[i];
  }
  return total;
}
</script>



@endpush