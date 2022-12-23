
@extends('layouts.app')
@section('content')
   

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Vendor Quotation (VQ)</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                  <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                  <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                  <button class="btn topnavbt" id="btnSaveFormData" ><i class="fa fa-save"></i> Save</button>
                  <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
                  <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                  <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                  <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                  <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                  <button style="display:none" class="btn topnavbt buttonload_approve" > <i class="fa fa-refresh fa-spin"></i> {{Session::get('approve')}}</button>
                  <button class="btn topnavbt" id="btnApprove" {{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}} ><i class="fa fa-lock"></i> Approved</button>
                  <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                  <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div>
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->

    
    <form id="frm_trn_add" method="POST"  >

    <div class="container-fluid purchase-order-view">
        
            @csrf
            <div class="container-fluid filter">

                    <div class="inner-form">
                    
                        <div class="row">
                            <div class="col-lg-2 pl"><p>VQ No*</p></div>
                            <div class="col-lg-2 pl">
                              <input {{$ActionStatus}} type="text" name="VQ_NO" id="VQ_NO" value="{{ isset($objMstResponse->VQ_NO)?$objMstResponse->VQ_NO:'' }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
                              <input type="hidden" name="VQID" id="VQID" value="{{ isset($objMstResponse->VQID)?$objMstResponse->VQID:'' }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
                            </div>
                            
                            <div class="col-lg-2 pl"><p>VQ Date*</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$ActionStatus}} type="date" name="VQ_DT" id="VQ_DT" onchange="checkPeriodClosing('{{$FormId}}',this.value,1)" value="{{ isset($objMstResponse->VQ_DT)?$objMstResponse->VQ_DT:'' }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
                            </div>

                            
                            <div class="col-lg-2 pl"><p>Vendor*</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$ActionStatus}} type="text" name="Vendor_popup" id="txtvendor_popup" class="form-control mandatory"  autocomplete="off" value="{{ isset($objvendorcode2->VCODE)?$objvendorcode2->VCODE:'' }}  {{ isset($objvendorcode2->NAME)?$objvendorcode2->NAME:'' }}" readonly/>
                                <input type="hidden" name="VID_REF" id="VID_REF" value="{{ isset($objvendorcode2->VID)?$objvendorcode2->VID:'' }} " class="form-control" autocomplete="off" />
                                <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />                                                                
                                <input type="hidden" name="hdnct" id="hdnct" class="form-control" autocomplete="off" />
                                <input type="hidden" name="hdnct22" id="hdnct22" class="form-control" autocomplete="off" value='<div class="row" style="margin-top:10px;margin-left:3px;"><div class="col-lg-2 pl"><p>Calculation Template</p></div> <div class="col-lg-2 pl"> <input type="text" name="txtCTID_popup" id="txtCTID_popup" class="form-control" autocomplete="off" readonly=""> <input type="hidden" name="CTID_REF" id="CTID_REF" class="form-control" autocomplete="off"> </div> </div>                                  <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:240px;">                                      <table id="example5" class="display nowrap table table-striped table-bordered itemlist " width="100%" style="height:auto !important;">                                          <thead id="thead1" style="position: sticky;top: 0">                                              <tr>                                                  <th>Calculation Component<input class="form-control" type="hidden" name="Row_Count4" id="Row_Count4"></th>                                                  <th>Rate</th>                                                  <th>Value</th>                                                  <th>GST Applicable</th>                                                  <th>IGST Rate</th>                                                  <th>IGST Amount</th>                                                  <th>CGST Rate</th>                                                  <th>CGST Amount</th>                                                  <th>SGST Rate</th>                                                  <th>SGST Amount</th>                                                  <th>Total GST Amount</th>                                                  <th hidden="">As per Actual</th>                                                  <th width="8%">Action</th>                                              </tr>                                          </thead>                                          <tbody id="tbody_ctid">                                              <tr class="participantRow5">                                                  <td><input type="text" name="popupTID_0" id="popupTID_0" class="form-control" autocomplete="off" readonly=""></td>                                                  <td hidden=""><input type="hidden" name="TID_REF_0" id="TID_REF_0" class="form-control" autocomplete="off"></td>                                                  <td><input type="text" name="RATE_0" id="RATE_0" class="form-control four-digits" maxlength="8" autocomplete="off" readonly=""></td>                                                  <td hidden=""><input type="hidden" name="BASIS_0" id="BASIS_0" class="form-control" autocomplete="off"></td>                                                  <td hidden=""><input type="hidden" name="SQNO_0" id="SQNO_0" class="form-control" autocomplete="off"></td>                                                  <td><input type="text" name="VALUE_0" id="VALUE_0" class="form-control two-digits" maxlength="15" autocomplete="off" readonly=""></td>                                                  <td style="text-align:center;"><input type="checkbox" class="filter-none" name="calGST_0" id="calGST_0" value=""></td>                                                  <td><input type="text" name="calIGST_0" id="calIGST_0" class="form-control four-digits" maxlength="8" autocomplete="off" readonly=""></td>                                                  <td><input type="text" name="AMTIGST_0" id="AMTIGST_0" class="form-control two-digits" maxlength="15" autocomplete="off" readonly=""></td>                                                  <td><input type="text" name="calCGST_0" id="calCGST_0" class="form-control four-digits" maxlength="8" autocomplete="off" readonly=""></td>                                                  <td><input type="text" name="AMTCGST_0" id="AMTCGST_0" class="form-control two-digits" maxlength="15" autocomplete="off" readonly=""></td>                                                  <td><input type="text" name="calSGST_0" id="calSGST_0" class="form-control four-digits" maxlength="8" autocomplete="off" readonly=""></td>                                                  <td><input type="text" name="AMTSGST_0" id="AMTSGST_0" class="form-control two-digits" maxlength="15" autocomplete="off" readonly=""></td>                                                  <td><input type="text" name="TOTGSTAMT_0" id="TOTGSTAMT_0" class="form-control two-digits" maxlength="15" autocomplete="off" readonly=""></td>                                                  <td hidden="" style="text-align:center;"><input type="checkbox" class="filter-none" name="calACTUAL_0" id="calACTUAL_0" value=""></td>                                                  <td align="center"><button class="btn add" title="add" data-toggle="tooltip" type="button" disabled=""><i class="fa fa-plus"></i></button> <button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled=""><i class="fa fa-trash"></i></button></td>                                              </tr>                                              <tr></tr>                                          </tbody>                                  </table>                                  </div>	                              ' />                                                                
                            </div>
                        </div>
                        

                        <div class="row">
                            <div class="col-lg-2 pl"><p>Bill To*</p></div>
                            <div class="col-lg-2 pl" id="div_billto">
                              <input {{$ActionStatus}} type="text" name="txtBILLTO" id="txtBILLTO" class="form-control"  autocomplete="off" value="{{ isset($objBillAddress[0])?$objBillAddress[0]:'' }}" readonly  />
                              <input type="hidden" name="BILLTO" id="BILLTO" class="form-control" autocomplete="off" value="{{ isset($objMstResponse->BILL_TO)?$objMstResponse->BILL_TO:'' }}" />
                            </div>
                           
                            <div class="col-lg-2 pl"><p>Ship To*</p></div>
                            <div class="col-lg-2 pl" id="div_shipto">
                              <input {{$ActionStatus}} type="text" name="txtSHIPTO" id="txtSHIPTO" class="form-control"  autocomplete="off" value="{{ isset($objShpAddress[0])?$objShpAddress[0]:'' }}" readonly  />
                              <input type="hidden" name="SHIPTO" id="SHIPTO" class="form-control" autocomplete="off" value="{{ isset($objMstResponse->SHIP_TO)?$objMstResponse->SHIP_TO:'' }}" />
                              <input type="hidden" name="Tax_State" id="Tax_State" class="form-control" autocomplete="off" value=" {{ isset($TAXSTATE[0])?$TAXSTATE[0]:'' }}"   />
                            </div> 

                            <div class="col-lg-2 pl"><p>Quotation No</p></div>
                            <div class="col-lg-2 pl">
                              
                                <input {{$ActionStatus}} type="text" name="VENDOR_QNO" id="VENDOR_QNO" maxlength="15" class="form-control" value="{{ isset($objMstResponse->VENDOR_QNO)?$objMstResponse->VENDOR_QNO:'' }}" style="text-transform:uppercase" autocomplete="off"/>
                            </div>

                      
                        </div>
                        <div class="row">

                            <div class="col-lg-2 pl"><p>Date </p></div>
                            <div class="col-lg-2 pl">
                                <input {{$ActionStatus}} type="date" name="VENDOR_QDT" id="VENDOR_QDT" class="form-control " autocomplete="off" placeholder="dd/mm/yyyy" value="{{ isset($objMstResponse->VENDOR_QDT)?$objMstResponse->VENDOR_QDT:'' }}" >
                            </div>

                          <div class="col-lg-2 pl"><p>Quotation Validity From*</p></div>
                          <div class="col-lg-2 pl">
                              <input {{$ActionStatus}} type="date" name="QUOTATION_VFR" id="QUOTATION_VFR" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy"  value="{{ isset($objMstResponse->QUOTATION_VFR)?$objMstResponse->QUOTATION_VFR:'' }}" >
                          </div>
                          
                          <div class="col-lg-2 pl"><p>Quotation Validity To*</p></div>
                          <div class="col-lg-2 pl">
                              <input {{$ActionStatus}} type="date" name="QUOTATION_VTO" id="QUOTATION_VTO" class="form-control mandatory" autocomplete="off"  placeholder="dd/mm/yyyy" value="{{ isset($objMstResponse->QUOTATION_VTO)?$objMstResponse->QUOTATION_VTO:'' }}"  min="{{ isset($objMstResponse->QUOTATION_VFR)?$objMstResponse->QUOTATION_VFR:'' }}" >
                          </div>
                          
                         
                      </div>
                        <div class="row">
                            
                            <div class="col-lg-2 pl"><p>Direct </p></div>
                            <div class="col-lg-2 pl">
                                  <input {{$ActionStatus}} type="checkbox" name="DIRECT_VQ" id="DIRECT_VQ" class="form-checkbox" value='{{isset($objMstResponse->DIRECT_VQ) && $objMstResponse->DIRECT_VQ == 1 ? 1 : 0}}' {{ isset($objMstResponse->DIRECT_VQ) && $objMstResponse->DIRECT_VQ == 1 ? "checked" : "" }} disabled >
                            </div>

                            <div class="col-lg-2 pl"><p>Total Value</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$ActionStatus}} type="text" name="TotalValue" id="TotalValue" class="form-control"  autocomplete="off" readonly  />
                            </div>

                            <div class="col-lg-2 pl"><p>Remarks</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$ActionStatus}} type="text" name="REMARKS" id="REMARKS" value="{{ isset($objMstResponse->REMARKS)?$objMstResponse->REMARKS:''  }}" autocomplete="off" class="form-control" maxlength="200"  >
                            </div>
                            
                        </div>
                    </div>

                    <div class="container-fluid purchase-order-view">

                        <div class="row">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#Material">Material</a></li>
                                <li><a data-toggle="tab" href="#TC">T & C</a></li>
                                <li><a data-toggle="tab" href="#udf">UDF</a></li>
                                <li><a data-toggle="tab" href="#CT">Calculation Template</a></li>
                                {{-- <li><a data-toggle="tab" href="#PaymentSlabs">Payment Slabs</a></li>	 --}}
                            </ul>
                            
                            
                            
                            <div class="tab-content">

                                <div id="Material" class="tab-pane fade in active">
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                                        <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                                <tr>
                                                    <th rowspan="2">RFQ No<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="{{ $objList1Count }}"></th>
                                                    
                                                    <th rowspan="2">Item Code</th>
                                                    <th rowspan="2">Item Name</th>
                                                    <th rowspan="2">Item Specification</th>
                                                    <th rowspan="2" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                                                    <th rowspan="2" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                                                    <th rowspan="2" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
                                                    <th rowspan="2">UoM</th>
                                                    <th rowspan="2">RFQ Qty</th>
                                                    <th rowspan="2">VQ Qty</th>
                                                    <th rowspan="2">Rate Per UoM</th>
                                                    <th colspan="2">Discount</th>
                                                    <th rowspan="2">Amount after<br/> discount</th>
                                                    <th rowspan="2">IGST Rate %</th>
                                                    <th rowspan="2">IGST Amount</th>
                                                    <th rowspan="2">CGST Rate %</th>
                                                    <th rowspan="2">CGST Amount</th>
                                                    <th rowspan="2">SGST Rate %</th>
                                                    <th rowspan="2">SGST Amount</th>
                                                    <th rowspan="2">Total GST Amount</th>
                                                    <th rowspan="2">Total after GST</th>
                                                    <th rowspan="2" width="3%">Action</th>
                                                </tr>
                                                    <tr>
                                                        <th>%</th>
                                                        <th>Amount</th>
                                                    </tr>
                                            </thead>
                                            <tbody>
                                            @if(!empty($objList1))
                                              @foreach($objList1 as $key => $row)
                                                <tr  class="participantRow">
                                                    <td hidden><input type="text" name="MRSNO_{{ $key }}" id="MRSNO_{{ $key }}" value="{{ $row->MRSID_REF }}" > </td>
                                                    <td style="text-align:center;" >
                                                      <input {{$ActionStatus}} type="text" name="txtRFQ_popup_{{ $key }}" id="txtRFQ_popup_{{ $key }}"    value="{{ $row->RFQ_NO }}" class="form-control CLS_RFQ"  autocomplete="off"  readonly style="width:100px;" />
                                                    </td>
                                                    <td  hidden ><input type="text" name="RFQID_{{ $key }}" id="RFQID_{{ $key }}"      value="{{ $row->RFQNO }}" class="form-control" autocomplete="off" /></td>
                                                    <td  hidden ><input type="text" name="PINO_{{ $key }}" id="PINO_{{ $key }}"    value="{{ $row->PIID_REF }}"  class="form-control" autocomplete="off" /></td>
                                                    <td><input {{$ActionStatus}} type="text" name="popupITEMID_{{ $key }}" id="popupITEMID_{{ $key }}"  value="{{ $row->ICODE }}" class="form-control"  autocomplete="off"  readonly style="width:100px;" /></td>
                                                    <td   hidden><input type="text" name="ITEMID_REF_{{ $key }}" id="ITEMID_REF_{{ $key }}"   value="{{ $row->ITEMID_REF }}"  class="form-control" autocomplete="off" /></td>
                                                    <td><input {{$ActionStatus}} type="text" name="ItemName_{{ $key }}" id="ItemName_{{ $key }}"    value="{{ $row->NAME }}" class="form-control"  autocomplete="off"  readonly style="width:200px;" /></td>
                                                    <td ><input {{$ActionStatus}} type="text" name="Itemspec_{{ $key }}" id="Itemspec_{{ $key }}" value="{{ $row->ITEMSPECI }}"  class="form-control"  autocomplete="off"  /></td>
                                                    <td {{$AlpsStatus['hidden']}}  ><input {{$ActionStatus}} type="text" name="Alpspartno_0" id="Alpspartno_0" class="form-control"  autocomplete="off"  readonly  /></td>
                                                    <td {{$AlpsStatus['hidden']}}  ><input {{$ActionStatus}} type="text" name="Custpartno_0" id="Custpartno_0" class="form-control"  autocomplete="off"  readonly  /></td>
                                                    <td {{$AlpsStatus['hidden']}}  ><input {{$ActionStatus}} type="text" name="OEMpartno_0" id="OEMpartno_0" class="form-control"  autocomplete="off"  readonly /></td>
                                                    <td><input {{$ActionStatus}} type="text" name="popupMUOM_{{ $key }}" id="popupMUOM_{{ $key }}" value="{{ $row->UOMCODE }} - {{ $row->DESCRIPTIONS }}"  class="form-control"  autocomplete="off"  readonly style="width:100px;"/></td>
                                                    <td hidden><input type="hidden" name="MAIN_UOMID_REF_{{ $key }}" id="MAIN_UOMID_REF_{{ $key }}" value="{{ $row->UOMID_REF }}"  class="form-control"  autocomplete="off" /></td>
                                                    <td hidden><input type="text" name="REMARKS_{{ $key }}" id="REMARKS_{{ $key }}" class="form-control" value="" autocomplete="off" style="width:200px;"  /></td>
                                                    <td><input {{$ActionStatus}} type="text" name="RFQ_QTY_{{ $key }}" id="RFQ_QTY_{{ $key }}"  value="{{ $row->TOTAL_PENDING }}" class="form-control three-digits" maxlength="13"  autocomplete="off"  readonly/></td>
                                                    <td><input {{$ActionStatus}} type="text" name="VQ_QTY_{{ $key }}" id="VQ_QTY_{{ $key }}"    value="{{ $row->QUOTATION_QTY }}"  class="form-control three-digits" maxlength="13"  autocomplete="off" onkeypress="return isNumberDecimalKey(event,this)"  /></td>
                                                    <td hidden><input type="hidden" name="SO_FQTY_{{ $key }}" id="SO_FQTY_{{ $key }}" value="{{ $row->QUOTATION_QTY }}" class="form-control three-digits" maxlength="13"  autocomplete="off"  readonly/></td>
                                                    
                                                    <td  hidden><input type="text" name="HID_VQ_QTY_{{ $key }}" id="HID_VQ_QTY_{{ $key }}" value="{{ $row->QUOTATION_QTY }}" class="form-control three-digits" maxlength="13"  autocomplete="off"  /></td>
                                                    
                                                    <td hidden><input type="hidden" name="popupAUOM_{{ $key }}" id="popupAUOM_{{ $key }}"         value="0" class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name="ALT_UOMID_REF_{{ $key }}" id="ALT_UOMID_REF_{{ $key }}" value="0" class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name="ALT_UOMID_QTY_{{ $key }}" id="ALT_UOMID_QTY_{{ $key }}" value="0" class="form-control three-digits" maxlength="13" autocomplete="off"  readonly/></td>
                                                   
                                                    <td><input {{$ActionStatus}} type="text" name="RATEPUOM_{{ $key }}" id="RATEPUOM_{{ $key }}"         value="{{ $row->RATEP_UOM }}" class="form-control five-digits blurRate" maxlength="13"  autocomplete="off" onkeypress="return isNumberDecimalKey(event,this)" /></td>
                                                    <td><input {{$ActionStatus}}  type="text" name="DISCPER_{{ $key }}" id="DISCPER_{{ $key }}"           value="{{ $row->DISCOUNT_PER }}" class="form-control four-digits blurDISCPER" maxlength="8"  autocomplete="off" style="width: 50px;"  onkeypress="return isNumberDecimalKey(event,this)" {{$AlpsStatus['disabled']}} /></td>
                                                    <td><input {{$ActionStatus}}  type="text" name="DISCOUNT_AMT_{{ $key }}" id="DISCOUNT_AMT_{{ $key }}" value="{{ $row->DISCOUNT_AMT }}"  class="form-control two-digits blurDISCOUNT_AMT" maxlength="15"  autocomplete="off"  onkeypress="return isNumberDecimalKey(event,this)" {{$AlpsStatus['disabled']}} /></td>
                                                    <td><input {{$ActionStatus}} type="text" name="DISAFTT_AMT_{{ $key }}" id="DISAFTT_AMT_{{ $key }}"   value=""                         class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td><input {{$ActionStatus}} type="text" name="IGST_{{ $key }}" id="IGST_{{ $key }}"                 value="{{ $row->IGST }}"         class="form-control four-digits" maxlength="8"  autocomplete="off"  readonly/></td>
                                                    <td><input {{$ActionStatus}} type="text" name="IGSTAMT_{{ $key }}" id="IGSTAMT_{{ $key }}"           value=""                         class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td><input {{$ActionStatus}} type="text" name="CGST_{{ $key }}" id="CGST_{{ $key }}"                 value="{{ $row->CGST }}"         class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                    <td><input {{$ActionStatus}} type="text" name="CGSTAMT_{{ $key }}" id="CGSTAMT_{{ $key }}"           value=""                         class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td><input {{$ActionStatus}} type="text" name="SGST_{{ $key }}" id="SGST_{{ $key }}"                 value="{{ $row->SGST }}"         class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                    <td><input {{$ActionStatus}} type="text" name="SGSTAMT_{{ $key }}" id="SGSTAMT_{{ $key }}"           value=""                         class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td><input {{$ActionStatus}} type="text" name="TGST_AMT_{{ $key }}" id="TGST_AMT_{{ $key }}"         value=""                         class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td><input {{$ActionStatus}} type="text" name="TOT_AMT_{{ $key }}" id="TOT_AMT_{{ $key }}"           value=""                                 class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td align="center" >
                                                      <button {{$ActionStatus}} class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                                                      <button {{$ActionStatus}} class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"  @if($loop->first) disabled @endif><i class="fa fa-trash" ></i></button>
                                                    </td>
                                                
                                                </tr>
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
                                        @if(!empty($objSavedTNCHeader))
                                          <input {{$ActionStatus}} type="text" name="txtTNCID_popup" id="txtTNCID_popup" value="{{ $objSavedTNCHeader[0]->TNC_CODE }} - {{ $objSavedTNCHeader[0]->TNC_DESC }}" class="form-control"  autocomplete="off"  readonly/>
                                          <input type="hidden" name="TNCID_REF" id="TNCID_REF" value="{{ $objSavedTNCHeader[0]->TNCID }}" class="form-control" autocomplete="off" />
                                        @else
                                          <input {{$ActionStatus}} type="text" name="txtTNCID_popup" id="txtTNCID_popup" class="form-control"  autocomplete="off"  readonly/>
                                          <input type="hidden" name="TNCID_REF" id="TNCID_REF" class="form-control" autocomplete="off" />
                                        @endif
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:240px;width:50%;">
                                        
                                        <table id="example3" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                            <tr >
                                                <th>Terms & Conditions Description<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2" value="{{ $objCount2 }}"></th>
                                                <th>Value / Comment</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody id="tncbody">                                    
                                            @if(!empty($objSOTNC))
                                                @foreach($objSOTNC as $Tkey => $Trow)
                                                    <tr  class="participantRow3">
                                                    
                                                    <td><input {{$ActionStatus}} type="text" name={{"popupTNCDID_".$Tkey}} id={{"popupTNCDID_".$Tkey}} class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name={{"TNCDID_REF_".$Tkey}} id={{"TNCDID_REF_".$Tkey}} class="form-control" value="{{$Trow->TNCDID_REF}}" autocomplete="off" /></td>
                                                    <td hidden><input type="hidden" name={{"TNCismandatory_".$Tkey}} id={{"TNCismandatory_".$Tkey}} class="form-control" autocomplete="off" /></td>
                                                    <td id={{"tdinputid_".$Tkey}}>
                                                    {{-- dynamic input --}} 
                                                    </td>
                                                        <td align="center" ><button class="btn add TNC" title="add" data-toggle="tooltip"  type="button" disabled><i class="fa fa-plus"></i></button><button  class="btn remove DTNC" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                    </tr>
                                                <tr></tr>
                                                @endforeach 
                                            @else
                                            <tr  class="participantRow3">
                                              <td><input {{$ActionStatus}} type="text" name="popupTNCDID_0" id="popupTNCDID_0" class="form-control"  autocomplete="off"  readonly/></td>
                                              <td hidden><input type="hidden" name="TNCDID_REF_0" id="TNCDID_REF_0" class="form-control" autocomplete="off" /></td>
                                              <td hidden><input type="hidden" name="TNCismandatory_0" id="TNCismandatory_0" class="form-control" autocomplete="off" /></td>
                                              <td id="tdinputid_0">
                                                {{-- dynamic input --}} 
                                              </td>
                                                <td align="center" ><button  class="btn add TNC" title="add" data-toggle="tooltip"  type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DTNC" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                            </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                    

                                <div id="udf" class="tab-pane fade">
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
                                        <table id="example4" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                            <tr >
                                                <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3" value="{{ $objCountUDF }}"></th>
                                                <th>Value / Comments</th>
                                            </tr>
                                            </thead>
                                            
                                            <tbody>
                                              @if(isset($objUdf) && !empty($objUdf))
                                              @foreach($objUdf as $udfkey => $udfrow)
                                              <tr  class="participantRow4">
                                                <td>
                                                  <input {{$ActionStatus}} name={{"udffie_popup_".$udfkey}} id={{"txtudffie_popup_".$udfkey}} value="{{$udfrow->LABEL}}" class="form-control @if ($udfrow->ISMANDATORY==1) mandatory @endif" autocomplete="off" maxlength="100" disabled/>
                                                </td>
                                
                                                <td hidden>
                                                  <input type="text" name='{{"udffie_".$udfkey}}' id='{{"hdnudffie_popup_".$udfkey}}' value="{{$udfrow->VQMID}}" class="form-control" maxlength="100" />
                                                </td>
                                
                                                <td hidden>
                                                  <input type="text" name={{"udffieismandatory_".$udfkey}} id={{"udffieismandatory_".$udfkey}} class="form-control" maxlength="100" value="{{$udfrow->ISMANDATORY}}" />
                                                </td>
                                
                                                <td id="{{"tdinputid_".$udfkey}}">
                                                  @php
                              
                                                      $dynamicid = "udfvalue_".$udfkey;
                                                      $chkvaltype = strtolower($udfrow->VALUETYPE); 

                                                    if($chkvaltype=='date'){

                                                      $strinp = '<input '.$ActionStatus.' type="date" placeholder="dd/mm/yyyy" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'" /> ';       

                                                    }else if($chkvaltype=='time'){

                                                        $strinp= '<input '.$ActionStatus.' type="time" placeholder="h:i" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control"  value="'.$udfrow->UDF_VALUE.'"/> ';

                                                    }else if($chkvaltype=='numeric'){
                                                    $strinp = '<input'.$ActionStatus.' type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'"/> ';

                                                    }else if($chkvaltype=='text'){

                                                    $strinp = '<input '.$ActionStatus.' type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'"/> ';

                                                    }else if($chkvaltype=='boolean'){
                                                        $boolval = ''; 
                                                        if($udfrow->UDF_VALUE=='on' || $udfrow->UDF_VALUE=='1' ){
                                                          $boolval="checked";
                                                        }
                                                        $strinp = '<input '.$ActionStatus.' type="checkbox" name="'.$dynamicid. '" id="'.$dynamicid.'" class=""  '.$boolval.' /> ';

                                                    }else if($chkvaltype=='combobox'){
                                                      $strinp='';
                                                    $txtoptscombo =   strtolower($udfrow->DESCRIPTIONS); ;
                                                    $strarray =  explode(',',$txtoptscombo);
                                                    $opts = '';
                                                    $chked='';
                                                      for ($i = 0; $i < count($strarray); $i++) {
                                                        $chked='';
                                                        if($strarray[$i]==$udfrow->UDF_VALUE){
                                                          $chked='selected="selected"';
                                                        }
                                                        $opts = $opts.'<option value="'.$strarray[$i].'"'.$chked.'  >'.$strarray[$i].'</option> ';
                                                      }

                                                      $strinp = '<select '.$ActionStatus.' name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" >'.$opts.'</select>' ;


                                                    }
                                                    echo $strinp;
                                                    @endphp
                                                </td>
                                              </tr>
                                              @endforeach
                                              @endif
                                              </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div id="CT" class="tab-pane fade">
                                  <div class="row" style="margin-top:10px;margin-left:3px;" >	
                                      <div class="col-lg-2 pl"><p>Calculation Template</p></div>
                                      <div class="col-lg-2 pl">

                                       <input {{$ActionStatus}} type="text" name="txtCTID_popup" id="txtCTID_popup" class="form-control"  autocomplete="off"  readonly/>
                                       @if(!empty($objVQCAL))
                                         <input type="hidden" name="CTID_REF" id="CTID_REF" class="form-control" value="{{$objVQCAL[0]->CTID_REF}}" autocomplete="off" />
                                         @else
                                         <input type="hidden" name="CTID_REF" id="CTID_REF" class="form-control" autocomplete="off" />
                                        @endif
                                      </div>
                                  </div>
                                  <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:240px;" >
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
                                                  <th hidden>As per Actual</th>
                                                  <th width="8%">Action</th>
                                              </tr>
                                          </thead>
                                          <tbody id="tbody_ctid">
                                             
                                              
                                
                                              @if(!empty($objVQCAL))
                                              @foreach($objVQCAL as $Ckey => $Crow)

                                              

                                                  <tr  class="participantRow5">
                                                      
                                                      <td><input {{$ActionStatus}} type="text" name={{"popupTID_".$Ckey}} id={{"popupTID_".$Ckey}}  class="form-control"  autocomplete="off"  readonly/></td>
                                                      <td hidden><input type="hidden" name={{"TID_REF_".$Ckey}}  id={{"TID_REF_".$Ckey}}  class="form-control" autocomplete="off" value="{{$Crow->TID_REF}}" /></td>
                                                      <td><input {{$ActionStatus}} type="text" name={{"RATE_".$Ckey}}  id={{"RATE_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->RATE}}"  readonly/></td>
                                                      <td hidden><input type="hidden" name={{"BASIS_".$Ckey}} id={{"BASIS_".$Ckey}} class="form-control" autocomplete="off"  /></td>
                                                      <td hidden><input type="hidden" name={{"SQNO_".$Ckey}} id={{"SQNO_".$Ckey}} class="form-control" autocomplete="off" /></td>
                                                      <td hidden><input type="hidden" name={{"FORMULA_".$Ckey}} id={{"FORMULA_".$Ckey}} class="form-control" autocomplete="off" /></td>
                                                      <td><input {{$ActionStatus}} type="text" name={{"VALUE_".$Ckey}} id={{"VALUE_".$Ckey}}   class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$Crow->VALUE}}" readonly/></td>
                                                      <td style="text-align:center;" ><input {{$ActionStatus}} type="checkbox" class="filter-none" name={{"calGST_".$Ckey}} id={{"calGST_".$Ckey}} {{$Crow->GST == 1 ? 'checked' : ''}}   ></td>
                                                      
                                                      <td><input {{$ActionStatus}} type="text" name={{"calIGST_".$Ckey}} id={{"calIGST_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->IGST}}" readonly/></td>
                                                      <td><input {{$ActionStatus}} type="text" name={{"AMTIGST_".$Ckey}} id={{"AMTIGST_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                      <td><input {{$ActionStatus}} type="text" name={{"calCGST_".$Ckey}} id={{"calCGST_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->CGST}}" readonly/></td>
                                                      <td><input {{$ActionStatus}} type="text" name={{"AMTCGST_".$Ckey}} id={{"AMTCGST_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                      <td><input {{$ActionStatus}} type="text" name={{"calSGST_".$Ckey}} id={{"calSGST_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->SGST}}" readonly/></td>
                                                      <td><input {{$ActionStatus}} type="text" name={{"AMTSGST_".$Ckey}} id={{"AMTSGST_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                      <td><input {{$ActionStatus}} type="text" name={{"TOTGSTAMT_".$Ckey}} id={{"TOTGSTAMT_".$Ckey}} class="form-control two-digits"  maxlength="15" autocomplete="off"  readonly/></td>
                                                      <td hidden style="text-align:center;"><input {{$ActionStatus}} type="checkbox" class="filter-none" name={{"calACTUAL_".$Ckey}} id={{"calACTUAL_".$Ckey}}  ></td>
                                                      <td align="center" ><button  class="btn add" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                  </tr>
                                                  <tr></tr>
                                              @endforeach 
                                              @else
                                              <tr  class="participantRow5">                                                  
                                                  <td><input {{$ActionStatus}} type="text" name="popupTID_0" id="popupTID_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                  <td hidden><input type="hidden" name="TID_REF_0" id="TID_REF_0" class="form-control" autocomplete="off" /></td>
                                                  <td><input {{$ActionStatus}} type="text" name="RATE_0" id="RATE_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                  <td hidden><input type="hidden" name="BASIS_0" id="BASIS_0" class="form-control" autocomplete="off" /></td>
                                                  <td hidden><input type="hidden" name="SQNO_0" id="SQNO_0" class="form-control" autocomplete="off" /></td>
                                                  <td><input {{$ActionStatus}} type="text" name="VALUE_0" id="VALUE_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                  <td style="text-align:center;" ><input type="checkbox" class="filter-none" name="calGST_0" id="calGST_0" value="" ></td>
                                                  <td><input {{$ActionStatus}} type="text" name="calIGST_0" id="calIGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                  <td><input {{$ActionStatus}} type="text" name="AMTIGST_0" id="AMTIGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                  <td><input {{$ActionStatus}} type="text" name="calCGST_0" id="calCGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                  <td><input {{$ActionStatus}} type="text" name="AMTCGST_0" id="AMTCGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                  <td><input {{$ActionStatus}} type="text" name="calSGST_0" id="calSGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                  <td><input {{$ActionStatus}} type="text" name="AMTSGST_0" id="AMTSGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                  <td><input {{$ActionStatus}} type="text" name="TOTGSTAMT_0" id="TOTGSTAMT_0" class="form-control two-digits"  maxlength="15" autocomplete="off"  readonly/></td>
                                                  <td hidden style="text-align:center;"><input  type="checkbox" class="filter-none" name="calACTUAL_0" id="calACTUAL_0" value=""   ></td>
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

<!-- Bill To Dropdown -->
<div id="BillTopopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='BillToclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Bill To</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="BillToTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Name</th>
      <th class="ROW3">Address</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="BillTocodesearch" class="form-control" autocomplete="off" onkeyup="BillToCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="BillTonamesearch" class="form-control" autocomplete="off" onkeyup="BillToNameFunction()"></td>
    </tr>
    </tbody>
    </table>
      <table id="BillToTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
         
        </thead>
        <tbody id="tbody_BillTo">
       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Bill To Dropdown-->

<!-- Ship To Dropdown -->
<div id="ShipTopopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ShipToclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Ship To</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ShipToTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Name</th>
      <th class="ROW3">Address</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="ShipTocodesearch" class="form-control" autocomplete="off" onkeyup="ShipToCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="ShipTonamesearch" class="form-control" autocomplete="off" onkeyup="ShipToNameFunction()"></td>
    </tr>
    </tbody>
    </table>
      <table id="ShipToTable2" class="display nowrap table  table-striped table-bordered">
        <thead id="thead2">
         
        </thead>
        <tbody id="tbody_ShipTo">
       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Ship To Dropdown-->

<!-- TNC Header Dropdown -->
<div id="TNCIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='TNCID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>GL Account</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="TNCIDTable" class="display nowrap table  table-striped table-bordered" >
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
        <td class="ROW2"><input type="text" id="TNCcodesearch" class="form-control" autocomplete="off" onkeyup="TNCCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="TNCnamesearch" class="form-control" autocomplete="off" onkeyup="TNCNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="TNCIDTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
         
        </thead>
        <tbody>
        @foreach ($objTNCHeader as $tncindex=>$tncRow)
        <tr >
        <td class="ROW1"> <input type="checkbox" name="SELECT_TNCID_REF[]" id="tncidcode_{{ $tncindex }}" class="clstncid" value="{{ $tncRow-> TNCID }}" ></td>
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
<!-- TNC Header Dropdown-->

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
    <input type="text" id="tncdetcodesearch" autocomplete="off" onkeyup="TNCDetCodeFunction()">
    </td>
    <td>
    <input type="text" id="tncdetnamesearch" autocomplete="off" onkeyup="TNCDetNameFunction()">
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
<!-- TNC Details Dropdown-->

<!-- Calculation Header Dropdown -->
<div id="CTIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='CTID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Calculation Template</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CTIDTable" class="display nowrap table  table-striped table-bordered" >
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
        <td class="ROW2"><input type="text" id="CTIDcodesearch" class="form-control" autocomplete="off" onkeyup="CTIDCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="CTIDnamesearch" class="form-control" autocomplete="off" onkeyup="CTIDNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="CTIDTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
         
        </thead>
        <tbody>
        @foreach ($objCalculationHeader as $calindex=>$calRow)
        <tr >
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
<!-- Calculation Header Dropdown-->

<!-- Calculation Details Dropdown -->
<div id="ctiddetpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ctiddet_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Terms & Condition Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CTIDDetTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>Component</th>
            <th>Basis</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>Formula</th>
    </tr>
    <tr hidden>
            <input type="hidden" name="fieldid" id="hdn_ctiddet"/>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="CTIDdetcodesearch" autocomplete="off" onkeyup="CTIDDetCodeFunction()">
    </td>
    <td>
    <input type="text" id="CTIDdetnamesearch" autocomplete="off" onkeyup="CTIDDetNameFunction()">
    </td>
    <td>
    <input type="text" id="CTIDdetratesearch" autocomplete="off" onkeyup="CTIDDetRateFunction()">
    </td>
    <td>
    <input type="text" id="CTIDdetamountsearch" autocomplete="off" onkeyup="CTIDDetAmountFunction()">
    </td>
    <td>
    <input type="text" id="CTIDdetformulasearch" autocomplete="off" onkeyup="CTIDDetFormulaFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="CTIDDetTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_ctiddetails">       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Calculation Details Dropdown-->



<!-- Currency Dropdown -->
<div id="cridpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='crid_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Currency</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CurrencyTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>Code</th>
            <th>Description</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="currencycodesearch" autocomplete="off" onkeyup="CurrencyCodeFunction()">
    </td>
    <td>
    <input type="text" id="currencynamesearch" autocomplete="off" onkeyup="CurrencyNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="CurrencyTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <!-- <tr>
            <th>GLCode</th>
            <th>GLName</th>
          </tr> -->
          
        </thead>
        <tbody>
        @foreach ($objothcurrency as $crindex=>$crRow)
        <tr id="cridcode_{{ $crindex }}" class="clscrid">
          <td width="50%">{{ $crRow-> CRCODE }}
          <input type="hidden" id="txtcridcode_{{ $crindex }}" data-desc="{{ $crRow-> CRCODE }}"  value="{{ $crRow-> CRID }}"/>
          </td>
          <td>{{ $crRow-> CRDESCRIPTION }}</td>
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
<!-- Currency Dropdown-->

<!-- Vendor Dropdown -->
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
        <td class="ROW2"><input type="text" id="vendorcodesearch" class="form-control" autocomplete="off" onkeyup="VendorCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="vendornamesearch" class="form-control" autocomplete="off" onkeyup="VendorNameFunction()"></td>
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
<!-- Vendor Dropdown-->

<!-- Sales Quotation Dropdown -->
<div id="RFQpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='RFQ_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>RFQ Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="RFQTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata"  >            
            <td> <input type="hidden" name="fieldid" id="hdn_rfqid"/>
            <input type="hidden" name="fieldid2" id="hdn_rfqid2"/>
            <input type="hidden" name="fieldid3" id="hdn_rfqid3"/>
            </td>
          </tr>

    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">No</th>
      <th class="ROW3">Date</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="RFQcodesearch" class="form-control" autocomplete="off" onkeyup="RFQCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="RFQnamesearch" class="form-control" autocomplete="off" onkeyup="RFQNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="RFQTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_RFQ">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Sales Quotation Dropdown-->

<!-- Item Code Dropdown -->
<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%;" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
    <thead>
      <tr id="none-select" class="searchalldata" >
            
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
            <input type="hidden" name="fieldid100" id="hdn_ItemID100"/>
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
            <input type="hidden" name="fieldid21" id="hdn_ItemID21" />
            <input type="hidden" name="fieldid22" id="hdn_ItemID22" />
            </td>
      </tr>
      <!-- <tr class="topnav"><button class="btn topnavbt" id="btnOk"><i class="fa fa-check"></i> OK</button></tr> -->
      <tr>
            <th style="width:8%;text-align:center;" id="all-check">Select</th>
            <th style="width:10%;">Code</th>
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
    <td style="width:8%;text-align:center;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
    <td style="width:10%;">
    <input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction()">
    </td>
    <td style="width:10%;">
    <input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemNameFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="ItemUOMFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemQTYsearch" class="form-control" autocomplete="off" onkeyup="ItemQTYFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="ItemGroupFunction()">
    </td>
    <td style="width:8%;">
    <input type="text" id="ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="ItemCategoryFunction()">
    </td>

    <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction()"></td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="ItemAPNFunction()"></td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="ItemCPNFunction()"></td>
    <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="ItemOEMPNFunction()"></td>

    <td style="width:8%;">
    <input type="text" id="ItemStatussearch" class="form-control" autocomplete="off" onkeyup="ItemStatusFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
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

<!-- ALT UOM Dropdown -->
<div id="altuompopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='altuom_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Vendor</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="altuomTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_altuom"/>
            <input type="hidden" name="fieldid2" id="hdn_altuom2"/>
            <input type="hidden" name="fieldid3" id="hdn_altuom3"/>
            <input type="hidden" name="fieldid4" id="hdn_altuom4"/></td>
          </tr>
    <tr>
            <th>UOM Code</th>
            <th>UOM Desc</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="altuomcodesearch" autocomplete="off" onkeyup="altuomCodeFunction()">
    </td>
    <td>
    <input type="text" id="altuomnamesearch" autocomplete="off" onkeyup="altuomNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="altuomTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          
        </thead>
        <tbody id="tbody_altuom">       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- ALT UOM Dropdown-->



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

//UDF Tab Starts
//------------------------

let udftid = "#UDFSOIDTable2";
      let udftid2 = "#UDFSOIDTable";
      let udfheaders = document.querySelectorAll(udftid2 + " th");

      // Sort the table element when clicking on the table headers
      udfheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(udftid, ".clsudfsoid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function UDFSOIDCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("UDFSOIDcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("UDFSOIDTable2");
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

  function UDFSOIDNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("UDFSOIDnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("UDFSOIDTable2");
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


$("#udfsoid_closePopup").on("click",function(event){ 
     $("#udfsoidpopup").hide();
});

$('.clsudfsoid').dblclick(function(){
    
        var id = $(this).attr('id');
        var txtid =    $("#txt"+id+"").val();
        var txtname =   $("#txt"+id+"").data("desc");
        var fieldid2 = $(this).find('[id*="udfvalue"]').attr('id');
        var txtvaluetype = $.trim($(this).find('[id*="udfvalue"]').text().trim());
        var txtismandatory =  $("#txt"+fieldid2+"").val();
        var txtdescription =  $("#txt"+fieldid2+"").data("desc");
        
        var txtcol = $('#hdn_UDFSOID').val();
        $("#"+txtcol).val(txtname);
        $("#"+txtcol).parent().parent().find("[id*='UDFSOID_REF']").val(txtid);
        $("#"+txtcol).parent().parent().find("[id*='UDFismandatory']").val(txtismandatory);
        
        var txt_id4 = $("#"+txtcol).parent().parent().find("[id*='udfinputid']").attr('id');  //<td> id 

        var strdyn = txt_id4.split('_');
        var lastele =   strdyn[strdyn.length-1];

        var dynamicid = "udfvalue_"+lastele;

        var chkvaltype2 =  txtvaluetype.toLowerCase();
        var strinp = '';

        if(chkvaltype2=='date'){

          strinp = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';       

        }else if(chkvaltype2=='time'){
          strinp= '<input type="time" placeholder="h:i" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';

        }else if(chkvaltype2=='numeric'){
          strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';

        }else if(chkvaltype2=='text'){

          strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';
        
        }else if(chkvaltype2=='boolean'){

          strinp = '<input type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" /> ';
        
        }else if(chkvaltype2=='combobox'){
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

        $("#udfsoidpopup").hide();
        $("#UDFSOIDcodesearch").val(''); 
        $("#UDFSOIDnamesearch").val(''); 
        UDFSOIDCodeFunction();
        event.preventDefault();
            
 });
 
//UDF Tab Ends
//------------------------
      

//------------------------
  //TNC Header
      let tnctid = "#TNCIDTable2";
      let tnctid2 = "#TNCIDTable";
      let tncheaders = document.querySelectorAll(tnctid2 + " th");

      // Sort the table element when clicking on the table headers
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
      
        var customid = txtval;
        if(customid!=''){
          
          $('#tbody_tncdetails').html('<tr><td colspan="2">Please wait..</td></tr>');
          // $('#tncbody').html('<tr><td colspan="2">Please wait..</td></tr>');

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

      

  //TNC Header Ends
//------------------------

//TNC Details Starts
//------------------------

      let tncdettid = "#TNCDetTable2";
      let tncdettid2 = "#TNCDetTable";
      let tncdetheaders = document.querySelectorAll(tncdettid2 + " th");

      // Sort the table element when clicking on the table headers
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
           
            event.preventDefault();
            
        });
  }
//TNC Details Ends
//------------------------

//------------------------
  //Calculation Header
  let cttid = "#CTIDTable2";
      let cttid2 = "#CTIDTable";
      let ctheaders = document.querySelectorAll(cttid2 + " th");

      // Sort the table element when clicking on the table headers
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

     
      $('#CT').on('click',"[id='txtCTID_popup']",function(){
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
        // var txtid= $('#hdn_fieldid').val();
        // var txt_id2= $('#hdn_fieldid2').val();
        
        $('#txtCTID_popup').val(texdesc);
        $('#CTID_REF').val(txtval);
        $("#CTIDpopup").hide();
        $("#CTIDcodesearch").val(''); 
        $("#CTIDnamesearch").val(''); 
       
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

      

  //Calculation Header Ends
//------------------------

//Calculation Details Starts
//------------------------

      let ctiddettid = "#CTIDDetTable2";
      let ctiddettid2 = "#CTIDDetTable";
      let ctiddetheaders = document.querySelectorAll(ctiddettid2 + " th");

      // Sort the table element when clicking on the table headers
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
            var txtRFQno =  $("#txt"+fieldid3+"").val();
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
            $("#"+txtcol).parent().parent().find("[id*='SQNO']").val(txtRFQno); 

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
           
            event.preventDefault();
            
        });
  }
//Calculation Details Ends
//------------------------

//------------------------
  

//------------------------
//Vendor Starts
//------------------------

     // START VENDOR CODE FUNCTION
let tid = "#VendorCodeTable2";
let tid2 = "#VendorCodeTable";
let headers = document.querySelectorAll(tid2 + " th");

      
headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(tid, ".clsvendorid", "td:nth-child(" + (i + 1) + ")");
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

$('#txtvendor_popup').click(function(event){
  

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
            var oldVenID =   $("#VID_REF").val();

            
            $("#txtvendor_popup").val(texdesc);
            $("#txtvendor_popup").blur();
            $("#VID_REF").val(txtval);

            if (txtval != oldVenID)
            {
              var objCOMPANY = <?php echo json_encode($objCOMPANY); ?>; 
              $('#Material').find('.participantRow').each(function()
              {
                $(this).find('input:text').val('');
                $(this).find('input:hidden').val('');
                if(objCOMPANY.NAME.indexOf('ALPS') != -1)
                {
                  $(this).find('[id*="DISCPER"]').val('');
                  $(this).find('[id*="DISCOUNT_AMT"]').val('');
                  $(this).find('[id*="DISCPER"]').prop('disabled',true);
                  $(this).find('[id*="DISCOUNT_AMT"]').prop('disabled',true);
                }
                var rowcount = $('#Row_Count1').val();
                if(rowcount > 1)
                {
                  $(this).closest('.participantRow').remove();
                  rowcount = parseInt(rowcount) - 1;
                  $('#Row_Count1').val(rowcount);
                }
              });
              $('#CT').find('.participantRow5').each(function()
              {
                $(this).find('input:text').val('');
                $(this).find('input:hidden').val('');
                var rowcount = $('#Row_Count4').val();
                if(rowcount > 1)
                {
                  $(this).closest('.participantRow5').remove();
                  rowcount = parseInt(rowcount) - 1;
                  $('#Row_Count4').val(rowcount);
                }
              });

                $('#TotalValue').val('0.00');
                $('#Row_Count1').val('1');
                $('#Row_Count4').val('1');
                if ($('#DirectSO').is(":checked") == true){
                    $('#Material').find('[id*="txtRFQ_popup"]').prop('disabled','true')
                    event.preventDefault();
                }
                else
                {
                    $('#Material').find('[id*="txtRFQ_popup"]').removeAttr('disabled');
                    event.preventDefault();
                }
            }

            $("#vendoridpopup").hide();
            $("#vendorcodesearch").val(''); 
            $("#vendornamesearch").val('');

           
            var customid = txtval;
              if(customid!=''){
                  
                $("#txtBILLTO").val('');
                $("#BILLTO").val('');
                $("#txtBILLTO1").val('');
                $("#BILLTO1").val('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"getBillTo"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#txtBILLTO1").hide();
                        $("#div_billto").html(data);
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#txtBILLTO").hide();
                        $("#txtBILLTO1").show();
                      },
                  });  

                $("#txtSHIPTO").val('');
                $("#SHIPTO").val('');
                $("#txtSHIPTO1").val('');
                $("#SHIPTO1").val('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"getShipTo"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#txtSHIPTO1").hide();
                        $("#div_shipto").html(data);
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#txtSHIPTO").hide();
                        $("#txtSHIPTO1").show();
                      },
                  });  
                  $("#tbody_BillTo").html('');
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"getBillAddress"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#tbody_BillTo").html(data);
                        BindBillAddress();
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_BillTo").html('');
                      },
                  });   
                  $("#tbody_ShipTo").html('');
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"getShipAddress"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#tbody_ShipTo").html(data);       
                        BindShipAddress();                 
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_ShipTo").html('');
                      },
                  });  
                  // $("#tbody_RFQ").html('');
                  // $.ajaxSetup({
                  //     headers: {
                  //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  //     }
                  // })
                  // $.ajax({
                  //     url:'{{route("transaction",[$FormId,"getRFQ"])}}',
                  //     type:'POST',
                  //     data:{'id':$('#VID_REF').val()},
                  //     success:function(data) {
                  //       $("#tbody_RFQ").html(data);
                  //       BindRFQuotation();
                  //     },
                  //     error:function(data){
                  //       console.log("Error: Something went wrong.");
                  //       $("#tbody_RFQ").html('');
                  //     },
                  // });
              }
              event.preventDefault();
        });
  }
//Vendor Ends
//------------------------

//------------------------
  //Bill Address
  let billtoid = "#BillToTable2";
      let billtoid2 = "#BillToTable";
      let billtoheaders = document.querySelectorAll(billtoid2 + " th");

      // Sort the table element when clicking on the table headers
      billtoheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(billtoid, ".clsbillto", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function BillToCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("BillTocodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("BillToTable2");
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

  function BillToNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("BillTonamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("BillToTable2");
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
  $('#div_billto').on('click','#txtBILLTO',function(event){

        var customid  = $("#VID_REF").val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#tbody_BillTo").html('');
        $.ajax({
            url:'{{route("transaction",[$FormId,"getBillAddress"])}}',
            type:'POST',
            data:{'id':customid},
            success:function(data) {
              $("#tbody_BillTo").html(data);
              BindBillAddress();
              showSelectedCheck($("#BILLTO").val(),"SELECT_BILLTO");
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              $("#tbody_BillTo").html('');
            },
        });   

        
         $("#BillTopopup").show();
         event.preventDefault();
      });

      $("#BillToclosePopup").click(function(event){
        $("#BillTopopup").hide();
        event.preventDefault();
      });

      function BindBillAddress(){
        $(".clsbillto").click(function(){
          var fieldid = $(this).attr('id');
          var txtval =    $("#txt"+fieldid+"").val();
          var texdesc =   $("#txt"+fieldid+"").data("desc");
          $('#txtBILLTO').val(texdesc);
          $('#BILLTO').val(txtval);
          $("#BillTopopup").hide();
          $("#BillTocodesearch").val(''); 
          $("#BillTonamesearch").val(''); 
        
          event.preventDefault();
        });
      }
  //Bill Address Ends
//------------------------

//------------------------
  //Ship Address
  let shiptoid = "#ShipToTable2";
      let shiptoid2 = "#ShipToTable";
      let shiptoheaders = document.querySelectorAll(shiptoid2 + " th");

      // Sort the table element when clicking on the table headers
      shiptoheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(shiptoid, ".clsshipto", "td:nth-child(" + (i + 1) + ")");
        });
      });

  function ShipToCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ShipTocodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ShipToTable2");
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

  function ShipToNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ShipTonamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ShipToTable2");
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

  $('#div_shipto').on('click','#txtSHIPTO',function(event){

    var customid  = $("#VID_REF").val();

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });


    $("#tbody_ShipTo").html('');
    $.ajax({
        url:'{{route("transaction",[$FormId,"getShipAddress"])}}',
        type:'POST',
        data:{'id':customid},
        success:function(data) {
          $("#tbody_ShipTo").html(data);       
          BindShipAddress();  
          showSelectedCheck($("#SHIPTO").val(),"SELECT_SHIPTO");               
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_ShipTo").html('');
        },
    });  


    
         $("#ShipTopopup").show();
         event.preventDefault();
      });

      $("#ShipToclosePopup").click(function(event){
        $("#ShipTopopup").hide();
        event.preventDefault();
      });

      function BindShipAddress(){
        $(".clsshipto").click(function(){
          var fieldid = $(this).attr('id');
          var txtval =    $("#txt"+fieldid+"").val();
          var texdesc =   $(this).children('[id*="txtshipadd"]').text().trim();
          var taxstate =  $("#txt"+fieldid+"").data("desc");
          var oldShipto =   $("#SHIPTO").val();
          var MaterialClone = $('#hdnmaterial').val();
         // var CTClone = $('#hdnct').val();

          if (txtval != oldShipto)
          {
              $('#Material').html(MaterialClone);
             // $('#CT').html(CTClone);

              $('#TotalValue').val('0.00');
              $('#Row_Count1').val('1');
              if ($('#DirectSO').is(":checked") == true){
                    $('#Material').find('[id*="txtRFQ_popup"]').prop('disabled','true')
                    event.preventDefault();
              }
              else
              {
                  $('#Material').find('[id*="txtRFQ_popup"]').removeAttr('disabled');
                  event.preventDefault();
              }
          }
          $('#txtSHIPTO').val(texdesc);
          $('#SHIPTO').val(txtval);
          $('#Tax_State').val(taxstate);
          $("#ShipTopopup").hide();
          $("#ShipTocodesearch").val(''); 
          $("#ShipTonamesearch").val(''); 
        
          event.preventDefault();
        });
      }
  //Ship Address Ends
//------------------------

//------------------------
  //Currency Dropdown
  let crtid = "#CurrencyTable2";
      let crtid2 = "#CurrencyTable";
      let currencyheaders = document.querySelectorAll(crtid2 + " th");

      // Sort the table element when clicking on the table headers
      currencyheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(crtid, ".clscrid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function CurrencyCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("currencycodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("CurrencyTable2");
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

  function CurrencyNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("currencynamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("CurrencyTable2");
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

  $('#txtCRID_popup').click(function(event){
         $("#cridpopup").show();
      });

      $("#crid_closePopup").click(function(event){
        $("#cridpopup").hide();
      });

      $(".clscrid").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        $('#txtCRID_popup').val(texdesc);
        $('#CRID_REF').val(txtval);
        $("#cridpopup").hide();
        var fcurrency = txtval;
        var dcurrency = <?php echo json_encode($objcurrency); ?>;
        var cconverter = <?php echo json_encode($objCurrencyconverter); ?>;
        $.each( cconverter, function( cckey, ccvalue ) {
          var fromdate = ccvalue.EFFDATE;
          var enddate = ccvalue.ENDDATE;
          var d = new Date(); 
          var today = d.getFullYear() + "-" + (d.getMonth()+1) + "-" + d.getDate();
         
            if (fcurrency == ccvalue.TOCRID_REF && dcurrency == ccvalue.FROMCRID_REF && fromdate <= today && enddate >= today)
            {
              $('#CONVFACT').val(ccvalue.FRAMOUNT);
              $('#CONVFACT').prop('readonly','true');
            }
            else
            {
              $('#CONVFACT').val('');
              $('#CONVFACT').removeAttr('readonly');
            }
          });
        $("#currencycodesearch").val(''); 
        $("#currencynamesearch").val(''); 
     
        event.preventDefault();
      });

      

  //Currency Dropdown Ends
//------------------------

//------------------------
  //Sales Person Dropdown
      let sptid = "#SalesPersonTable2";
      let sptid2 = "#SalesPersonTable";
      let salespersonheaders = document.querySelectorAll(sptid2 + " th");

      // Sort the table element when clicking on the table headers
      salespersonheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sptid, ".clsspid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function SalesPersonCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesPersoncodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesPersonTable2");
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

  function SalesPersonNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesPersonnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesPersonTable2");
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

  $('#txtSPID_popup').click(function(event){
         $("#SPIDpopup").show();
      });

      $("#SPID_closePopup").click(function(event){
        $("#SPIDpopup").hide();
      });

      $(".clsspid").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        $('#txtSPID_popup').val(texdesc);
        $('#SPID_REF').val(txtval);
        $("#SPIDpopup").hide();
        
        $("#SalesPersoncodesearch").val(''); 
        $("#SalesPersonnamesearch").val(''); 
        SalesPersonCodeFunction();
        event.preventDefault();
      });

      

  //Sales Person Dropdown Ends
//------------------------

//------------------------
  //RFQ Dropdown
      let sqtid = "#RFQTable2";
      let sqtid2 = "#RFQTable";
      let salesquotationheaders = document.querySelectorAll(sqtid2 + " th");

      // Sort the table element when clicking on the table headers
      salesquotationheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sqtid, ".clssqid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function RFQCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("RFQcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("RFQTable2");
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

  function RFQNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("RFQnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("RFQTable2");
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

  $('#Material').on('click','[id*="txtRFQ_popup"]',function(event){

        var VID_REF    = $('#VID_REF').val();
        if(VID_REF ===""){
          showAlert('Please select Vendor.');
          return false;
        }

        if($("#DIRECT_VQ").is(":checked")==true) {
            $(this).prop("disabled",true);
            return false;
        }

        
          var id = $(this).attr('id');
          var id2 = $(this).parent().parent().find('[id*="RFQID"]').attr('id');      
          //var id3 = $(this).parent().parent().find('[id*="RFQID"]').attr('id');   

           var fieldid = $(this).parent().parent().find('[id*="RFQID"]').attr('id');   

          $('#hdn_rfqid').val(id);
          $('#hdn_rfqid2').val(id2);
          //$('#hdn_rfqid3').val(id3);
        
          $("#RFQpopup").show();
          $("#tbody_RFQ").html('');
          //$("#tbody_RFQ").html('Loading...');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          })
          $.ajax({
              url:'{{route("transaction",[$FormId,"getRFQ"])}}',
              type:'POST',
              data:{'id':$('#VID_REF').val(),'fieldid':fieldid},
              success:function(data) {
                $("#tbody_RFQ").html(data);
                BindRFQuotation();
                
                showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $("#tbody_RFQ").html('');
              },
          });

          
      });

      $("#RFQ_closePopup").click(function(event){
        $("#RFQpopup").hide();
      });

      function BindRFQuotation(){
          $(".clsrfqid").click(function(){
            var fieldid = $(this).attr('id');
            var txtval =    $("#txt"+fieldid+"").val();
            var texdesc =   $("#txt"+fieldid+"").data("desc");
            var texdescdate =   $("#txt"+fieldid+"").data("descdate");
            
            var txtid= $('#hdn_rfqid').val();
            var txt_id2= $('#hdn_rfqid2').val();
            //var txt_id3= $('#hdn_rfqid3').val();

            $('#'+txtid).val(texdesc);
            $('#'+txt_id2).val(txtval);
           // $('#'+txt_id3).val(texdescdate);
            $("#RFQpopup").hide();            
            $("#RFQcodesearch").val(''); 
            $("#RFQnamesearch").val(''); 
          

            $("#"+txtid).parent().parent().find('[id*="popupITEMID"]').val('');
            $("#"+txtid).parent().parent().find('[id*="ITEMID_REF"]').val('');
            $("#"+txtid).parent().parent().find('[id*="ItemName"]').val('');
            $("#"+txtid).parent().parent().find('[id*="popupMUOM"]').val('');
            $("#"+txtid).parent().parent().find('[id*="MAIN_UOMID_REF"]').val('');
            $("#"+txtid).parent().parent().find('[id*="REMARKS"]').val('');
            $("#"+txtid).parent().parent().find('[id*="RFQ_QTY"]').val('');
            $("#"+txtid).parent().parent().find('[id*="VQ_QTY"]').val('');
            $("#"+txtid).parent().parent().find('[id*="HID_VQ_QTY"]').val('');
            $("#"+txtid).parent().parent().find('[id*="RATEPUOM"]').val('');
            $("#"+txtid).parent().parent().find('[id*="DISCPER"]').val('');
            $("#"+txtid).parent().parent().find('[id*="DISCOUNT_AMT"]').val('');
            $("#"+txtid).parent().parent().find('[id*="DISAFTT_AMT"]').val('');
            $("#"+txtid).parent().parent().find('[id*="IGST"]').val('');
            $("#"+txtid).parent().parent().find('[id*="IGSTAMT"]').val('');
            $("#"+txtid).parent().parent().find('[id*="CGST"]').val('');
            $("#"+txtid).parent().parent().find('[id*="CGSTAMT"]').val('');
            $("#"+txtid).parent().parent().find('[id*="SGST"]').val('');
            $("#"+txtid).parent().parent().find('[id*="SGSTAMT"]').val('');
            $("#"+txtid).parent().parent().find('[id*="TGST_AMT"]').val('');
            $("#"+txtid).parent().parent().find('[id*="TOT_AMT"]').val('');


            event.preventDefault();
          });
      }

      

  //Sales Quotation Dropdown Ends
//------------------------

//------------------------
  //Item ID Dropdown
        
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

  if(filter.length == 0 && $("#DIRECT_VQ").prop("checked")==true)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3 && $("#DIRECT_VQ").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = filter; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
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
}

function ItemNameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Itemnamesearch");
  filter = input.value.toUpperCase();

  if(filter.length == 0 && $("#DIRECT_VQ").prop("checked")==true ) 
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3 && $("#DIRECT_VQ").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = filter; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
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
}

function ItemUOMFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemUOMsearch");
  filter = input.value.toUpperCase();  
  if(filter.length == 0 && $("#DIRECT_VQ").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3 && $("#DIRECT_VQ").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = filter; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
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
  if(filter.length == 0 && $("#DIRECT_VQ").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3 && $("#DIRECT_VQ").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = filter; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
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
}

function ItemCategoryFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemCategorysearch");
  filter = input.value.toUpperCase();
  if(filter.length == 0 && $("#DIRECT_VQ").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3 && $("#DIRECT_VQ").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = filter; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
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
}

function ItemBUFunction() {
var input, filter, table, tr, td, i, txtValue;
input = document.getElementById("ItemBUsearch");
filter = input.value.toUpperCase();
if(filter.length == 0 && $("#DIRECT_VQ").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3 && $("#DIRECT_VQ").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = filter; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
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
}

function ItemAPNFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemAPNsearch");
  filter = input.value.toUpperCase();
  if(filter.length == 0 && $("#DIRECT_VQ").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3 && $("#DIRECT_VQ").prop("checked")==true )
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = filter; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else
  {
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
}

function ItemCPNFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemCPNsearch");
  filter = input.value.toUpperCase();
  if(filter.length == 0 && $("#DIRECT_VQ").prop("checked")==true)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART); 
  }
  else if(filter.length >= 3 && $("#DIRECT_VQ").prop("checked")==true)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = filter; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART);
  }
  else
  {
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
}

function ItemOEMPNFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemOEMPNsearch");
  filter = input.value.toUpperCase();
  if(filter.length == 0 && $("#DIRECT_VQ").prop("checked")==true)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = ''; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART);
  }
  else if(filter.length >= 3 && $("#DIRECT_VQ").prop("checked")==true)
  {
    if ($('#Tax_State').length) 
    {
      var taxstate = $('#Tax_State').val();
    }
    else
    {
      var taxstate = '';
    }
    var CODE = ''; 
    var NAME = ''; 
    var MUOM = ''; 
    var GROUP = ''; 
    var CTGRY = ''; 
    var BUNIT = ''; 
    var APART = ''; 
    var CPART = ''; 
    var OPART = filter; 
    loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART);
  }
  else
  {
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
}

function ItemStatusFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("ItemStatussearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("ItemIDTable2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[11];
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

function ItemSpecFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemSpecsearch");
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

function loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART){
	
		$("#tbody_ItemID").html('');
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
      url:'{{route("transaction",[$FormId,"getItemDetails"])}}',
			type:'POST',
			data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART},
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

}
    $('#Material').on('click','[id*="popupITEMID"]',function(event){

          var RFQ_ID = $(this).parent().parent().find('[id*="RFQID"]').val();
          var VENDORID = $("#VID_REF").val();
          var VQID = $("#VQID").val();

          if(VENDORID ===""){
              showAlert('Please select Vendor.');
              return false;
          }

          var fromItems=0;
          if(!$("#DIRECT_VQ").prop("checked")) {
              if(RFQ_ID ===""){
                showAlert('Please select RFQ No.');
                return false;
              }
              $(".js-selectall").attr('disabled',false);
          }else{
              fromItems = 1;
              $(".js-selectall").attr('disabled',true);
          }

          var taxstate = $.trim($('#Tax_State').val());
          if(RFQ_ID!=''){
                  $("#tbody_ItemID").html('');
                  $('.js-selectall').attr("disabled", true);
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url:'{{route("transaction",[$FormId,"getItemDetailsRFQwise"])}}',
                        type:'POST',
                        data:{'id':RFQ_ID, 'taxstate':taxstate,'vendorid':VENDORID,'fromitems':fromItems,'vqid':VQID,'mode':'edit' },
                        success:function(data) {
                          $("#tbody_ItemID").html(data);   
                          $('.js-selectall').attr("disabled", false);
                          bindItemEvents();                     
                        },
                        error:function(data){
                          console.log("Error: Something went wrong.");
                          $("#tbody_ItemID").html('');                        
                        },
                    }); 
          }
          else
          {
            /*
                  $("#tbody_ItemID").html('');
                  $('.js-selectall').attr("disabled", true);
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url:'{{route("transaction",[$FormId,"getItemDetailsWithoutRFQ"])}}',
                        type:'POST',
                        data:{'id':RFQ_ID, 'taxstate':taxstate,'vendorid':VENDORID,'fromitems':fromItems,'mode':'edit'},
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
            */

            if ($('#Tax_State').length){
              var taxstate = $('#Tax_State').val();
            }
            else
            {
              var taxstate = '';
            }

            var CODE = ''; 
            var NAME = ''; 
            var MUOM = ''; 
            var GROUP = ''; 
            var CTGRY = ''; 
            var BUNIT = ''; 
            var APART = ''; 
            var CPART = ''; 
            var OPART = ''; 
            var FORMID = "{{$FormId}}";
            loadItem(taxstate,CODE,NAME,MUOM,GROUP,CTGRY,BUNIT,APART,CPART,OPART);


          }

          $("#ITEMIDpopup").show();
          var id = $(this).attr('id');
          var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
          var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
          var id4 = $(this).parent().parent().find('[id*="Itemspec"]').attr('id');

          //var id5 = $(this).parent().parent().find('[id*="SQMUOM"]').attr('id');
          // var id6 = $(this).parent().parent().find('[id*="SQMUOMQTY"]').attr('id');

        // var id7 = $(this).parent().parent().find('[id*="SQAUOM"]').attr('id');
        // var id8 = $(this).parent().parent().find('[id*="SQAUOMQTY"]').attr('id');

          var id9 = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
          var id10 = $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
          var id100 = $(this).parent().parent().find('[id*="MRSNO"]').attr('id');
          var id11 = $(this).parent().parent().find('[id*="VQ_QTY"]').attr('id');
          var id12 = $(this).parent().parent().find('[id*="popupAUOM"]').attr('id');
          var id13 = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');
          var id14 = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
          var id15 = $(this).parent().parent().find('[id*="RATEPUOM"]').attr('id');
          var id16 = $(this).parent().parent().find('[id*="SO_FQTY"]').attr('id');

          $('#hdn_ItemID').val(id);
          $('#hdn_ItemID2').val(id2);
          $('#hdn_ItemID3').val(id3);
          $('#hdn_ItemID4').val(id4);
          // $('#hdn_ItemID5').val(id5);
          //  $('#hdn_ItemID6').val(id6);
          //$('#hdn_ItemID7').val(id7);
          //$('#hdn_ItemID8').val(id8);
          $('#hdn_ItemID9').val(id9);
          $('#hdn_ItemID10').val(id10);
          $('#hdn_ItemID100').val(id100);
          $('#hdn_ItemID11').val(id11);
          $('#hdn_ItemID12').val(id12);
          $('#hdn_ItemID13').val(id13);
          $('#hdn_ItemID14').val(id14);
          $('#hdn_ItemID15').val(id15);
          $('#hdn_ItemID16').val(id16);
          $('#hdn_ItemID17').val(RFQ_ID);
          var r_count = 0;
          var SalesEnq = [];
          $('#Material').find('.participantRow').each(function(){
            if($(this).find('[id*="ITEMID_REF"]').val() != '')
            {
              //SalesEnq.push($(this).find('[id*="RFQID"]').val()+'-'+$(this).find('[id*="ITEMID_REF"]').val());
              SalesEnq.push($(this).find('[id*="RFQID"]').val());
              r_count = parseInt(r_count)+1;
              $('#hdn_ItemID22').val(r_count);
            }
          });

          $('#hdn_ItemID18').val(SalesEnq.join(', '));

          var ItemID = [];
          $('#Material').find('.participantRow').each(function(){
            if($(this).find('[id*="ITEMID_REF"]').val() != '')
            {
              ItemID.push($(this).find('[id*="ITEMID_REF"]').val());
            }
          });

          $('#hdn_ItemID19').val(ItemID.join(', '));

          // var EnquiryID = [];
          // $('#Material').find('.participantRow').each(function(){
          //   if($(this).find('[id*="RFQID_REF"]').val() != '')
          //   {
          //     EnquiryID.push($(this).find('[id*="PINO"]').val());
          //   }
          // });
          // $('#hdn_ItemID20').val(EnquiryID.join(', '));
          // var SQID = [];
          // $('#Material').find('.participantRow').each(function(){
          //   if($(this).find('[id*="SQID_REF"]').val() != '')
          //   {
          //     SQID.push($(this).find('[id*="SQID_REF"]').val());
          //   }
          // });
          // $('#hdn_ItemID21').val(SQID.join(', '));
          event.preventDefault();          
    }); //ON FOCUS 

    $("#ITEMID_closePopup").click(function(event){
      $("#ITEMIDpopup").hide();
      $('.js-selectall').prop("checked", false);
    });

    function bindItemEvents(){

      $('#ItemIDTable2').off(); 
      var objCOMPANY = <?php echo json_encode($objCOMPANY); ?>; 
      //all checkbox
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
          var apartno =  $("#txt"+fieldid3+"").data("desc2");
          var cpartno =  $("#txt"+fieldid3+"").data("desc3");
          var opartno =  $("#txt"+fieldid3+"").data("desc4");
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
          var fieldid7 = $(this).find('[id*="rfqid"]').attr('id');
          var txtenqno = $("#txt"+fieldid7+"").val();
          var txtenqid = $("#txt"+fieldid7+"").data("desc");
          var rcount1 = parseInt($(this).closest('table').find('.clsitemid').length);
          var rcount2 = $('#hdn_ItemID21').val();
          var r_count2 = 0;
          if(rcount2 == '')
          {
            rcount2 = 0;
          }

          var MRSNO =  $("#MRSNO"+fieldid+"").val();

          var desc1 =  $("#txt"+fieldid+"").data("desc1");
          var desc2  =  $("#txt"+fieldid+"").data("desc2");
          var desc3 =  $("#txt"+fieldid+"").data("desc3");
          var desc4 =  $("#txt"+fieldid+"").data("desc4");
          var rfqid =  $("#txt"+fieldid+"").data("desc5");          
          var pino =  $("#txt"+fieldid+"").data("pino"); 
         
          var rfqqty =  $("#txt"+fieldid+"").data("rfqqty");
              rfqqty = parseFloat(rfqqty).toFixed(3); 


          var txtmuomqty  =  desc1;
          var txtruom     =  desc2;

          if(txtenqno == undefined)
          {
            txtenqno = '';
          }
          if(txtenqid == undefined)
          {
            txtenqid = '';
          }
        
        var totalvalue = 0.00;
        var txttaxamt1 = 0.00;
        var txttaxamt2 = 0.00;
        var txttottaxamt = 0.00;
        var txttotamtatax =0.00;
        
        txtruom = parseFloat(txtruom).toFixed(5); 
        txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);
        
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
        // var intRegex = /^\d+$/;
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
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            var seitem = $(this).find('[id*="RFQID"]').val()+'-'+$(this).find('[id*="PINO"]').val()+'-'+$(this).find('[id*="ITEMID_REF"]').val()+'-'+$(this).find('[id*="MRSNO"]').val();
            SalesEnq2.push(seitem);
            r_count2 = parseInt(r_count2) + 1;
          }
        });
        
        var rfqids =  $('#hdn_ItemID18').val();
        var itemids =  $('#hdn_ItemID19').val();
        var pinos =  $('#hdn_ItemID20').val();
        var MRSNO_HID =  $('#hdn_ItemID100').val();
    
            if($(this).find('[id*="chkId"]').is(":checked") == true) 
            {
              rcount1 = parseInt(rcount2)+parseInt(rcount1);
              if(parseInt(r_count2) >= parseInt(rcount1))
              {
                $('#hdn_ItemID').val('');
                    $('#hdn_ItemID2').val('');
                    $('#hdn_ItemID3').val('');
                    $('#hdn_ItemID4').val('');
                    $('#hdn_ItemID5').val('');
                    $('#hdn_ItemID6').val('');
                    $('#hdn_ItemID7').val('');
                    $('#hdn_ItemID8').val('');
                    $('#hdn_ItemID9').val('');
                    $('#hdn_ItemID10').val('');
                    $('#hdn_ItemID100').val('');
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
                    txtenqno = '';
                    txtenqid = '';
                    $('.js-selectall').prop("checked", false);
                    $("#ITEMIDpopup").hide();
                    return false;
              }
              //var txtenqitem = desc5;
              var txtenqitem = rfqid+"-"+pino+"-"+txtval+"-"+MRSNO;
              if(jQuery.inArray(txtenqitem, SalesEnq2) !== -1){
                    $("#ITEMIDpopup").hide();
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('Item already exists1.');
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
                    $('#hdn_ItemID8').val('');
                    $('#hdn_ItemID9').val('');
                    $('#hdn_ItemID10').val('');
                    $('#hdn_ItemID100').val('');
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
                    txtenqno = '';
                    txtenqid = '';
                    $('.js-selectall').prop("checked", false);
                    $("#ITEMIDpopup").hide();
                    return false;
              }

             
                  if($('#hdn_ItemID').val() == "" && txtval != '')
                  {
                    var txtid= $('#hdn_ItemID').val();
                    var txt_id2= $('#hdn_ItemID2').val();
                    var txt_id3= $('#hdn_ItemID3').val();
                    var txt_id4= $('#hdn_ItemID4').val();
                    var txt_id5= $('#hdn_ItemID5').val();
                    var txt_id6= $('#hdn_ItemID6').val();
                    var txt_id7= $('#hdn_ItemID7').val();
                    var txt_id8= $('#hdn_ItemID8').val();
                    var txt_id9= $('#hdn_ItemID9').val();
                    var txt_id10= $('#hdn_ItemID10').val();
                    var txt_id100= $('#hdn_ItemID100').val();
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
                        $clone.find('[id*="ITEMID_REF"]').val(txtval);
                        $clone.find('[id*="PINO"]').val(pino);
                        $clone.find('[id*="ItemName"]').val(txtname);
                        $clone.find('[id*="Itemspec"]').val(txtspec);
                        $clone.find('[id*="Alpspartno"]').val(apartno);
                        $clone.find('[id*="Custpartno"]').val(cpartno);
                        $clone.find('[id*="OEMpartno"]').val(opartno);
                        $clone.find('[id*="popupMUOM"]').val(txtmuom);
                        $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
                        $clone.find('[id*="VQ_QTY"]').val(txtmuomqty);
                        $clone.find('[id*="SO_FQTY"]').val(txtmuomqty);
                        $clone.find('[id*="popupAUOM"]').val(txtauom);
                        $clone.find('[id*="ALT_UOMID_REF"]').val(txtauomid);
                        $clone.find('[id*="ALT_UOMID_QTY"]').val(txtauomqty);
                        $clone.find('[id*="RATEPUOM"]').val(txtruom);
                        $clone.find('[id*="DISAFTT_AMT"]').val(txtamt);
                        $clone.find('[id*="TOT_AMT"]').val(txttotamtatax);
                        $clone.find('[id*="TGST_AMT"]').val(txttottaxamt);
                        $clone.find('[id*="HID_VQ_QTY"]').val(desc1);
                        $clone.find('[id*="DISCPER"]').val(desc3);
                        $clone.find('[id*="DISCOUNT_AMT"]').val(desc4);

                        $clone.find('[id*="RFQ_QTY"]').val(rfqqty);

                        $clone.find('[id*="MRSNO"]').val(MRSNO);


                        if($.trim($('#Tax_State').val()) == 'OutofState')
                        {
                          $clone.find('[id*="IGST"]').val(txttax1);
                          $clone.find('[id*="IGSTAMT"]').val(txttaxamt1);
                          $clone.find('[id*="SGST"]').prop('disabled',true); 
                          $clone.find('[id*="CGST"]').prop('disabled',true); 
                          $clone.find('[id*="SGSTAMT"]').prop('disabled',true); 
                          $clone.find('[id*="CGSTAMT"]').prop('disabled',true); 
                        }
                        else
                        {
                          $clone.find('[id*="CGST"]').val(txttax1);
                          $clone.find('[id*="IGST"]').prop('disabled',true); 
                          $clone.find('[id*="SGST"]').val(txttax2);
                          $clone.find('[id*="CGSTAMT"]').val(txttaxamt1);
                          $clone.find('[id*="SGSTAMT"]').val(txttaxamt2);
                          $clone.find('[id*="IGSTAMT"]').prop('disabled',true); 
                        }
                        
                        $tr.closest('table').append($clone);   
                        var rowCount = $('#Row_Count1').val();
                        rowCount = parseInt(rowCount)+1;
                        $('#Row_Count1').val(rowCount);
                        var tvalue = parseFloat(txttotamtatax).toFixed(2);
                        totalvalue = $('#TotalValue').val();
                        totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
                        totalvalue = parseFloat(totalvalue).toFixed(2);
                        $('#TotalValue').val(totalvalue);
                        
                      if($clone.find('[id*="txtRFQ_popup"]').val() == '')
                      {
                       // $clone.find('[id*="SQMUOM"]').val('');
                        //$clone.find('[id*="SQMUOMQTY"]').val('');
                        //$clone.find('[id*="SQAUOM"]').val('');
                        //$clone.find('[id*="SQAUOMQTY"]').val('');
                      }
                      if(objCOMPANY.NAME.indexOf('ALPS') != -1)
                      {
                        $clone.find('[id*="DISCPER"]').prop('disabled',true); 
                        $clone.find('[id*="DISCOUNT_AMT"]').prop('disabled',true); 
                        $clone.find('[id*="DISCPER"]').val(''); 
                        $clone.find('[id*="DISCOUNT_AMT"]').val(''); 
                      }

                     doCalculation(); //$(".blurRate").blur();

                    //  $("#ITEMIDpopup").hide();
                    //  event.preventDefault();
                  }
                  else
                  {
                      var txtid= $('#hdn_ItemID').val();
                      var txt_id2= $('#hdn_ItemID2').val();
                      var txt_id3= $('#hdn_ItemID3').val();
                      var txt_id4= $('#hdn_ItemID4').val();
                     // var txt_id5= $('#hdn_ItemID5').val();
                     // var txt_id6= $('#hdn_ItemID6').val();
                     // var txt_id7= $('#hdn_ItemID7').val();
                     // var txt_id8= $('#hdn_ItemID8').val();
                      var txt_id9= $('#hdn_ItemID9').val();
                      var txt_id10= $('#hdn_ItemID10').val();
                      var txt_id100= $('#hdn_ItemID100').val();
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
                      //$('#'+txt_id5).val(txtmuom);
                     // $('#'+txt_id6).val(txtmuomqty);
                     // $('#'+txt_id7).val(txtauom);
                     // $('#'+txt_id8).val(txtauomqty);
                      $('#'+txt_id9).val(txtmuom);
                      $('#'+txt_id10).val(txtmuomid);
                      $('#'+txt_id11).val(txtmuomqty);
                      $('#'+txt_id12).val(txtauom);
                      $('#'+txt_id13).val(txtauomid);
                      $('#'+txt_id14).val(txtauomqty);
                      $('#'+txt_id15).val(txtruom);
                      $('#'+txt_id16).val(txtmuomqty);

                      $('#'+txt_id100).val(MRSNO);

                      $('#'+txtid).parent().parent().find('[id*="PINO"]').val(pino);
                      // $('#'+txtid).parent().parent().find('[id*="RFQID_REF"]').val(txtenqid);
                      $('#'+txtid).parent().parent().find('[id*="DISAFTT_AMT"]').val(txtamt);
                      $('#'+txtid).parent().parent().find('[id*="TOT_AMT"]').val(txttotamtatax);
                      $('#'+txtid).parent().parent().find('[id*="TGST_AMT"]').val(txttottaxamt);

                      $('#'+txtid).parent().parent().find('[id*="HID_VQ_QTY"]').val(desc1);
                      $('#'+txtid).parent().parent().find('[id*="DISCPER"]').val(desc3);
                      $('#'+txtid).parent().parent().find('[id*="DISCOUNT_AMT"]').val(desc4);

                      $('#'+txtid).parent().parent().find('[id*="RFQ_QTY"]').val(rfqqty);
                      $('#'+txtid).parent().parent().find('[id*="Alpspartno"]').val(apartno);
                      $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
                      $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);

                      if($.trim($('#Tax_State').val()) == 'OutofState')
                        {
                          $('#'+txtid).parent().parent().find('[id*="IGST"]').val(txttax1);
                          $('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').val(txttaxamt1);
                          $('#'+txtid).parent().parent().find('[id*="SGST"]').prop('disabled',true); 
                          $('#'+txtid).parent().parent().find('[id*="CGST"]').prop('disabled',true);
                          $('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').prop('disabled',true); 
                          $('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').prop('disabled',true); 
                        }
                        else
                        {
                          $('#'+txtid).parent().parent().find('[id*="CGST"]').val(txttax1);
                          $('#'+txtid).parent().parent().find('[id*="IGST"]').prop('disabled',true); 
                          $('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').prop('disabled',true); 
                          $('#'+txtid).parent().parent().find('[id*="SGST"]').val(txttax2);
                          $('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').val(txttaxamt2);
                          $('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').val(txttaxamt1);
                        }
                        var tvalue = parseFloat(txttotamtatax).toFixed(2);
                        totalvalue = $('#TotalValue').val();
                        totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
                        totalvalue = parseFloat(totalvalue).toFixed(2);
                        $('#TotalValue').val(totalvalue);
                        if($('#'+txtid).parent().parent().find('[id*="txtRFQ_popup"]').val() == '')
                        {
                         // $('#'+txtid).parent().parent().find('[id*="SQMUOM"]').val('');
                         // $('#'+txtid).parent().parent().find('[id*="SQMUOMQTY"]').val('');
                         // $('#'+txtid).parent().parent().find('[id*="SQAUOM"]').val('');
                         // $('#'+txtid).parent().parent().find('[id*="SQAUOMQTY"]').val('');
                        }
                        if(objCOMPANY.NAME.indexOf('ALPS') != -1)
                        {
                           $('#'+txtid).parent().parent().find('[id*="DISCPER"]').prop('disabled',true); 
                           $('#'+txtid).parent().parent().find('[id*="DISCOUNT_AMT"]').prop('disabled',true); 
                           $('#'+txtid).parent().parent().find('[id*="DISCPER"]').val(''); 
                           $('#'+txtid).parent().parent().find('[id*="DISCOUNT_AMT"]').val(''); 
                        }
                        $('#hdn_ItemID').val('');
                      $('#hdn_ItemID2').val('');
                      $('#hdn_ItemID3').val('');
                      $('#hdn_ItemID4').val('');
                      $('#hdn_ItemID5').val('');
                      $('#hdn_ItemID6').val('');
                      $('#hdn_ItemID7').val('');
                      $('#hdn_ItemID8').val('');
                      $('#hdn_ItemID9').val('');
                      $('#hdn_ItemID10').val('');
                      $('#hdn_ItemID100').val('');
                      $('#hdn_ItemID11').val('');
                      $('#hdn_ItemID12').val('');
                      $('#hdn_ItemID13').val('');
                      $('#hdn_ItemID14').val('');
                      $('#hdn_ItemID15').val('');
                      $('#hdn_ItemID16').val('');
                      
                      doCalculation();  //$(".blurRate").blur();
                        
                  }
                  // $('.js-selectall').prop("checked", false);  
                  // $("#ITEMIDpopup").hide(); //hide items
                  // event.preventDefault();
                  
            }  ////all checkbox user checked
            else if($(this).is(":checked") == false)  //////all checkbox user unchecked
            {
                var id = txtval;
                var enqid = txtenqid;
                var sqno = txtenqno;
                var r_count = $('#Row_Count1').val();
                $('#Material').find('.participantRow').each(function()
                {
                    var itemid = $(this).find('[id*="ITEMID_REF"]').val();
                    var rfqrefid = $(this).find('[id*="RFQID_REF"]').val();
                    var quotationno = $(this).find('[id*="txtRFQ_popup"]').val();
                    if(id == itemid /*&& enqid == rfqrefid && sqno == quotationno*/ )
                    {
                        var rowCount = $('#Row_Count1').val();
                        if (rowCount > 1) {
                          var totalvalue = $('#TotalValue').val();
                          totalvalue = parseFloat(totalvalue - $(this).closest('.participantRow').find('[id*="TOT_AMT_"]').val()).toFixed(2);
                          $('#TotalValue').val(totalvalue);
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
              event.preventDefault();
            }

          $("#Itemcodesearch").val(''); 
          $("#Itemnamesearch").val(''); 
          $("#ItemUOMsearch").val(''); 
          $("#ItemGroupsearch").val(''); 
          $("#ItemCategorysearch").val(''); 
          $("#ItemStatussearch").val(''); 
          $('.remove').removeAttr('disabled'); 
       
          event.preventDefault();
        });

        $('#ITEMIDpopup').hide();
        return false;
        event.preventDefault();

      });//.js-selectall END

      $('[id*="chkId"]').change(function(){ //// checkbox checked or unchecked single checkbox clicked 

        var fieldid = $(this).parent().parent().attr('id');
        var txtval =   $("#txt"+fieldid+"").val();
        var texdesc =  $("#txt"+fieldid+"").data("desc");
        var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
        var txtname =  $("#txt"+fieldid2+"").val();
        var txtspec =  $("#txt"+fieldid2+"").data("desc");
        var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
        var txtmuomid =  $("#txt"+fieldid3+"").val();
        var txtauom =  $("#txt"+fieldid3+"").data("desc");
        var apartno =  $("#txt"+fieldid3+"").data("desc2");
        var cpartno =  $("#txt"+fieldid3+"").data("desc3");
        var opartno =  $("#txt"+fieldid3+"").data("desc4");
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
        var fieldid7 = $(this).parent().parent().children('[id*="ise"]').attr('id');
        var txtenqno = $("#txt"+fieldid7+"").val();
        var txtenqid = $("#txt"+fieldid7+"").data("desc");

        var MRSNO =  $("#MRSNO"+fieldid+"").val();

          var desc1 =  $("#txt"+fieldid+"").data("desc1");
          var desc2  =  $("#txt"+fieldid+"").data("desc2");
          var desc3 =  $("#txt"+fieldid+"").data("desc3");
          var desc4 =  $("#txt"+fieldid+"").data("desc4");
          var rfqid =  $("#txt"+fieldid+"").data("desc5");
          var pino =  $("#txt"+fieldid+"").data("pino");
          var rfqqty =  $("#txt"+fieldid+"").data("rfqqty");
              rfqqty = parseFloat(rfqqty).toFixed(3); 

          var txtmuomqty  =  desc1;
          var txtruom     =  desc2;

          if(txtenqno == undefined)
          {
            txtenqno = '';
          }
          if(txtenqid == undefined)
          {
            txtenqid = '';
          }
          var totalvalue = 0.00;
        var txttaxamt1 = 0.00;
        var txttaxamt2 = 0.00;
        var txttottaxamt = 0.00;
        var txttotamtatax =0.00;
        
        txtruom = parseFloat(txtruom).toFixed(5); 
        txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);
        
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
        // var intRegex = /^\d+$/;
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
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            var seitem = $(this).find('[id*="RFQID"]').val()+'-'+$(this).find('[id*="PINO"]').val()+'-'+$(this).find('[id*="ITEMID_REF"]').val()+'-'+$(this).find('[id*="MRSNO"]').val();
            SalesEnq2.push(seitem);
          }
        });
        
        var rfqids =  $('#hdn_ItemID18').val();
        var itemids =  $('#hdn_ItemID19').val();
        var pinos =  $('#hdn_ItemID20').val();
        var MRSNO_HID =  $('#hdn_ItemID100').val();
    
            if($(this).is(":checked") == true) 
            {
             
              var txtenqitem = rfqid+'-'+pino+'-'+txtval+'-'+MRSNO;

              if(jQuery.inArray(txtenqitem, SalesEnq2) !== -1)
              {
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
                    $('#hdn_ItemID7').val('');
                    $('#hdn_ItemID8').val('');
                    $('#hdn_ItemID9').val('');
                    $('#hdn_ItemID10').val('');
                    $('#hdn_ItemID100').val('');
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
                    txtenqno = '';
                    txtenqid = '';
                    $('.js-selectall').prop("checked", false);
                    $("#ITEMIDpopup").hide();
                    return false;
              }   

              if($('#hdn_ItemID').val() == "" && txtval != '')
              {
                var txtid= $('#hdn_ItemID').val();
                var txt_id2= $('#hdn_ItemID2').val();
                var txt_id3= $('#hdn_ItemID3').val();
                var txt_id4= $('#hdn_ItemID4').val();
                //var txt_id5= $('#hdn_ItemID5').val();
                //var txt_id6= $('#hdn_ItemID6').val();
                // var txt_id7= $('#hdn_ItemID7').val();
                //var txt_id8= $('#hdn_ItemID8').val();
                var txt_id9= $('#hdn_ItemID9').val();
                var txt_id10= $('#hdn_ItemID10').val();
                var txt_id100= $('#hdn_ItemID100').val();
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
                $clone.find('[id*="RFQID_REF"]').val(txtenqid);
                $clone.find('[id*="ITEMID_REF"]').val(txtval);
                $clone.find('[id*="ItemName"]').val(txtname);
                $clone.find('[id*="Itemspec"]').val(txtspec);
                $clone.find('[id*="Alpspartno"]').val(apartno);
                $clone.find('[id*="Custpartno"]').val(cpartno);
                $clone.find('[id*="OEMpartno"]').val(opartno);
                $clone.find('[id*="popupMUOM"]').val(txtmuom);
                $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
                $clone.find('[id*="VQ_QTY"]').val(txtmuomqty);
                $clone.find('[id*="SO_FQTY"]').val(txtmuomqty);
                $clone.find('[id*="popupAUOM"]').val(txtauom);
                $clone.find('[id*="ALT_UOMID_REF"]').val(txtauomid);
                $clone.find('[id*="ALT_UOMID_QTY"]').val(txtauomqty);
                $clone.find('[id*="RATEPUOM"]').val(txtruom);
                $clone.find('[id*="DISAFTT_AMT"]').val(txtamt);
                $clone.find('[id*="TOT_AMT"]').val(txttotamtatax);
                $clone.find('[id*="TGST_AMT"]').val(txttottaxamt);
                

                $clone.find('[id*="HID_VQ_QTY"]').val(desc1);
                $clone.find('[id*="DISCPER"]').val(desc3);
                $clone.find('[id*="DISCOUNT_AMT"]').val(desc4);
                $clone.find('[id*="RFQ_QTY"]').val(rfqqty);

                $clone.find('[id*="MRSNO"]').val(MRSNO);

                if($.trim($('#Tax_State').val()) == 'OutofState')
                {
                  $clone.find('[id*="IGST"]').val(txttax1);
                  $clone.find('[id*="SGST"]').prop('disabled',true); 
                  $clone.find('[id*="CGST"]').prop('disabled',true); 
                  $clone.find('[id*="SGSTAMT"]').prop('disabled',true); 
                  $clone.find('[id*="CGSTAMT"]').prop('disabled',true);
                  $clone.find('[id*="IGSTAMT"]').val(txttaxamt1);
                }
                else
                {
                  $clone.find('[id*="CGST"]').val(txttax1);
                  $clone.find('[id*="IGST"]').prop('disabled',true); 
                  $clone.find('[id*="SGST"]').val(txttax2);
                  $clone.find('[id*="SGSTAMT"]').val(txttaxamt2);; 
                  $clone.find('[id*="CGSTAMT"]').val(txttaxamt1);;
                  $clone.find('[id*="IGSTAMT"]').prop('disabled',true);
                }
                $tr.closest('table').append($clone);   
                var rowCount = $('#Row_Count1').val();
                  rowCount = parseInt(rowCount)+1;
                  $('#Row_Count1').val(rowCount);
                  var tvalue = parseFloat(txttotamtatax).toFixed(2);
                totalvalue = $('#TotalValue').val();
                totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
                totalvalue = parseFloat(totalvalue).toFixed(2);
                $('#TotalValue').val(totalvalue);

                if($clone.find('[id*="txtRFQ_popup"]').val() == '')
                {
                  // $clone.find('[id*="SQMUOM"]').val('');
                  // $clone.find('[id*="SQMUOMQTY"]').val('');
                  // $clone.find('[id*="SQAUOM"]').val('');
                  // $clone.find('[id*="SQAUOMQTY"]').val('');
                } 
                if(objCOMPANY.NAME.indexOf('ALPS') != -1)
                {
                  $clone.find('[id*="DISCPER"]').prop('disabled',true); 
                  $clone.find('[id*="DISCOUNT_AMT"]').prop('disabled',true); 
                  $clone.find('[id*="DISCPER"]').val(''); 
                  $clone.find('[id*="DISCOUNT_AMT"]').val(''); 
                }
                  doCalculation();  //$(".blurRate").blur();

                
              }
              else
              {
              var txtid= $('#hdn_ItemID').val();
              var txt_id2= $('#hdn_ItemID2').val();
              var txt_id3= $('#hdn_ItemID3').val();
              var txt_id4= $('#hdn_ItemID4').val();
              //var txt_id5= $('#hdn_ItemID5').val();
              // var txt_id6= $('#hdn_ItemID6').val();
              // var txt_id7= $('#hdn_ItemID7').val();
              //var txt_id8= $('#hdn_ItemID8').val();
              var txt_id9= $('#hdn_ItemID9').val();
              var txt_id10= $('#hdn_ItemID10').val();
              var txt_id100= $('#hdn_ItemID100').val();
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
              // $('#'+txt_id5).val(txtmuom);
              // $('#'+txt_id6).val(txtmuomqty);
              //$('#'+txt_id7).val(txtauom);
              // $('#'+txt_id8).val(txtauomqty);
              $('#'+txt_id9).val(txtmuom);
              $('#'+txt_id10).val(txtmuomid);
              $('#'+txt_id11).val(txtmuomqty);
              $('#'+txt_id12).val(txtauom);
              $('#'+txt_id13).val(txtauomid);
              $('#'+txt_id14).val(txtauomqty);
              $('#'+txt_id15).val(txtruom);
              $('#'+txt_id16).val(txtmuomqty);

              $('#'+txt_id100).val(MRSNO);

              $('#'+txtid).parent().parent().find('[id*="RFQID_REF"]').val(txtenqid);
              $('#'+txtid).parent().parent().find('[id*="DISAFTT_AMT"]').val(txtamt);
              $('#'+txtid).parent().parent().find('[id*="TOT_AMT"]').val(txttotamtatax);
              $('#'+txtid).parent().parent().find('[id*="TGST_AMT"]').val(txttottaxamt);

              $('#'+txtid).parent().parent().find('[id*="HID_VQ_QTY"]').val(desc1);
              $('#'+txtid).parent().parent().find('[id*="DISCPER"]').val(desc3);
              $('#'+txtid).parent().parent().find('[id*="DISCOUNT_AMT"]').val(desc4);
              $('#'+txtid).parent().parent().find('[id*="RFQ_QTY"]').val(rfqqty);
              $('#'+txtid).parent().parent().find('[id*="Alpspartno"]').val(apartno);
              $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
              $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);
              if($.trim($('#Tax_State').val()) == 'OutofState')
                {
                  $('#'+txtid).parent().parent().find('[id*="IGST"]').val(txttax1);
                  $('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').val(txttaxamt1);
                  $('#'+txtid).parent().parent().find('[id*="SGST"]').prop('disabled',true); 
                  $('#'+txtid).parent().parent().find('[id*="CGST"]').prop('disabled',true); 
                  $('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').prop('disabled',true);
                  $('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').prop('disabled',true);
                }
                else
                {
                  $('#'+txtid).parent().parent().find('[id*="CGST"]').val(txttax1);
                  $('#'+txtid).parent().parent().find('[id*="IGST"]').prop('disabled',true); 
                  $('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').prop('disabled',true); 
                  $('#'+txtid).parent().parent().find('[id*="SGST"]').val(txttax2);
                  $('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').val(txttaxamt1);
                  $('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').val(txttaxamt1);
                }
                if(objCOMPANY.NAME.indexOf('ALPS') != -1)
                {
                  $('#'+txtid).parent().parent().find('[id*="DISCPER"]').prop('disabled',true); 
                  $('#'+txtid).parent().parent().find('[id*="DISCOUNT_AMT"]').prop('disabled',true); 
                  $('#'+txtid).parent().parent().find('[id*="DISCPER"]').val(''); 
                  $('#'+txtid).parent().parent().find('[id*="DISCOUNT_AMT"]').val(''); 
                }
                var tvalue = parseFloat(txttotamtatax).toFixed(2);
                totalvalue = $('#TotalValue').val();
                totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
                totalvalue = parseFloat(totalvalue).toFixed(2);
                $('#TotalValue').val(totalvalue);
                doCalculation(); // $(".blurRate").blur();


              // $("#ITEMIDpopup").hide();
              $('#hdn_ItemID').val('');
              $('#hdn_ItemID2').val('');
              $('#hdn_ItemID3').val('');
              $('#hdn_ItemID4').val('');
              $('#hdn_ItemID5').val('');
              $('#hdn_ItemID6').val('');
              $('#hdn_ItemID7').val('');
              $('#hdn_ItemID8').val('');
              $('#hdn_ItemID9').val('');
              $('#hdn_ItemID10').val('');
              $('#hdn_ItemID100').val('');
              $('#hdn_ItemID11').val('');
              $('#hdn_ItemID12').val('');
              $('#hdn_ItemID13').val('');
              $('#hdn_ItemID14').val('');
              $('#hdn_ItemID15').val('');
              $('#hdn_ItemID16').val('');
              if($('#'+txtid).parent().parent().find('[id*="txtRFQ_popup"]').val() == '')
                {
                  //$('#'+txtid).parent().parent().find('[id*="SQMUOM"]').val('');
                  // $('#'+txtid).parent().parent().find('[id*="SQMUOMQTY"]').val('');
                  // $('#'+txtid).parent().parent().find('[id*="SQAUOM"]').val('');
                //  $('#'+txtid).parent().parent().find('[id*="SQAUOMQTY"]').val('');
                }
              }

              // $("#ITEMIDpopup").hide(); //hide items
              event.preventDefault();
            }
            else if($(this).is(":checked") == false) ////single checkbox clicked unchecked
            {
              var id = txtval;
              var enqid = txtenqid;
              var sqno = txtenqno;
              var r_count = $('#Row_Count1').val();
              $('#Material').find('.participantRow').each(function()
              {
                var itemid = $(this).find('[id*="ITEMID_REF"]').val();
                var rfqrefid = $(this).find('[id*="RFQID_REF"]').val();
                var quotationno = $(this).find('[id*="txtRFQ_popup"]').val();
                if(id == itemid /*&& enqid == rfqrefid && sqno == quotationno*/ )
                {
                    var rowCount = $('#Row_Count1').val();
                    if (rowCount > 1) {
                      var totalvalue = $('#TotalValue').val();
                      totalvalue = parseFloat(totalvalue - $(this).closest('.participantRow').find('[id*="TOT_AMT_"]').val()).toFixed(2);
                      $('#TotalValue').val(totalvalue);
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
        $("#ItemUOMsearch").val(''); 
        $("#ItemGroupsearch").val(''); 
        $("#ItemCategorysearch").val(''); 
        $("#ItemStatussearch").val(''); 
        $('.remove').removeAttr('disabled'); 
        
        event.preventDefault();
      });
    }

      

  //Item ID Dropdown Ends
//------------------------

//------------------------
  //ALT UOM Dropdown
  let altutid = "#altuomTable2";
      let altutid2 = "#altuomTable";
      let altutidheaders = document.querySelectorAll(altutid2 + " th");

      // Sort the table element when clicking on the table headers
      altutidheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(altutid, ".clsaltuom", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function altuomCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("altuomcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("altuomTable2");
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

      function altuomNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("altuomnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("altuomTable2");
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

      

  $('#Material').on('keydown','[id*="popupAUOM"]',function(event){
        var ItemID = $(this).parent().parent().find('[id*="ITEMID_REF"]').val();
        
        if(ItemID !=''){
                $("#tbody_altuom").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"getAltUOM"])}}',
                      type:'POST',
                      data:{'id':ItemID},
                      success:function(data) {
                        
                        $("#tbody_altuom").html(data);   
                        bindAltUOM();                     
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_altuom").html('');                        
                      },
                  }); 
        }
        else
        {
                $("#altuompopup").hide();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Please Select Item First.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                return false;
        }

        $("#altuompopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="VQ_QTY"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
        
        $('#hdn_altuom').val(id);
        $('#hdn_altuom2').val(id2);
        $('#hdn_altuom3').val(id3);
        $('#hdn_altuom4').val(id4);
        event.preventDefault();
      });

      $("#altuom_closePopup").click(function(event){
        $("#altuompopup").hide();
      });

    function bindAltUOM(){

      $('#altuomTable2').off(); 

      $(".clsaltuom").dblclick(function(){
        var fieldid = $(this).attr('id');
        var txtval =   $("#txt"+fieldid+"").val();
        var texdesc =  $("#txt"+fieldid+"").data("desc");
        var txtid= $('#hdn_altuom').val();
        var txt_id2= $('#hdn_altuom2').val();
        var txt_id3= $('#hdn_altuom3').val();
        var txt_id4= $('#hdn_altuom4').val();
        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);

        var itemid = $('#'+txtid).parent().parent().find('[id*="ITEMID_REF"]').val();
        var altuomid = txtval;
        var mqty = $('#'+txtid).parent().parent().find('[id*="VQ_QTY"]').val();

        if(altuomid!=''){
              $('#'+txt_id4).val('');
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
                        $('#'+txt_id4).val(data);                        
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $('#'+txt_id4).val('');                        
                      },
                  }); 
                      
              }

        $("#altuompopup").hide();
        $("#altuomcodesearch").val(''); 
        $("#altuomnamesearch").val(''); 
        
       
        event.preventDefault();
      });
    }

      

  //Alt UOM Dropdown Ends
//------------------------

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
        $clone.find('[id*="RFQID"]').val('');
        $clone.find('[id*="RFQID_REF"]').val('');
        $clone.find('[id*="PINO"]').val('');
        $clone.find('[id*="ITEMID_REF"]').val('');
        $tr.closest('table').append($clone);         
        var rowCount1 = $('#Row_Count1').val();
		    rowCount1 = parseInt(rowCount1)+1;
        $('#Row_Count1').val(rowCount1);
        $clone.find('.remove').removeAttr('disabled');        
        
        event.preventDefault();
    });

    $("#Material").on('click', '.remove', function() {
        var rowCount = $(this).closest('table').find('.participantRow').length;
        if (rowCount > 1) {
          var totalvalue = $('#TotalValue').val();
          totalvalue = parseFloat(totalvalue - $(this).closest('.participantRow').find('[id*="TOT_AMT_"]').val()).toFixed(2);
          $('#TotalValue').val(totalvalue);
            $(this).closest('.participantRow').remove();  
            var rowCount1 = $('#Row_Count1').val();
            rowCount1 = parseInt(rowCount1)-1;
            $('#Row_Count1').val(rowCount1);
          doCalculation();
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
                //$(".blurRate").blur();
                doCalculation();
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
            var rowCount2 = $('#Row_Count2').val();
            rowCount2 = parseInt(rowCount2)-1;
            $('#Row_Count2').val(rowCount2); 
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
          var rowCount4 = $('#Row_Count4').val();
          rowCount4 = parseInt(rowCount4)-1;
          $('#Row_Count4').val(rowCount4);
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

    $("#example6").on('click', '.add', function() {
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('.participantRow6').last();
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
        $tr.closest('table').append($clone);         
        var rowCount5 = $('#Row_Count5').val();
		    rowCount5 = parseInt(rowCount5)+1;
        $('#Row_Count5').val(rowCount5);
        $clone.find('.remove').removeAttr('disabled'); 
        
        event.preventDefault();
    });
    $("#example6").on('click', '.remove', function() {
        var rowCount5 = $(this).closest('table').find('.participantRow6').length;
        if (rowCount5 > 1) {
          $(this).closest('.participantRow6').remove();     
          var rowCount5 = $('#Row_Count5').val();
          rowCount5 = parseInt(rowCount5)-1;
          $('#Row_Count5').val(rowCount5);
        } 
        if (rowCount5 <= 1) {          
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

    $(document).ready(function(e) {

$("#TotalValue").val('0.00000');

var count2 = <?php echo json_encode($objCount2); ?>;
var count4 = <?php echo json_encode($objCount4); ?>;

$('#Row_Count2').val(count2);
$('#Row_Count4').val(count4);

// var count4 = <?php echo json_encode($objCount4); ?>;
// $('#Row_Count4').val(count4);
// var obj = <?php echo json_encode($objList1); ?>;

var objtnc = <?php echo json_encode($objSOTNC); ?>;
var tncheader = <?php echo json_encode($objTNCHeader); ?>;
  var tncdetails = <?php echo json_encode($objTNCDetails); ?>;
var calheader = <?php echo json_encode($objCalHeader); ?>;
var caldetails = <?php echo json_encode($objCalDetails); ?>;

var SOCal = <?php echo json_encode($objVQCAL); ?>;
 var taxstate = <?php echo json_encode($TAXSTATE); ?>;

var totalvalue = 0.00;
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
// totalvalue = parseFloat(totalvalue).toFixed(2);
// var grosstotal = parseFloat(totalvalue) + parseFloat($('#TotalValue').val());  
// $('#TotalValue').val(grosstotal);

$('#TotalValue').val(totalvalue);

//------------



$.each( objtnc, function( tnckey, tncvalue ) {

$.each( tncheader, function( tnchkey, tnchvalue ) { 
    if(tncvalue.TNCID_REF == tnchvalue.TNCID)
    {
        $('#txtTNCID_popup').val(tnchvalue.TNC_CODE+' - '+tnchvalue.TNC_DESC);
    }
});

$.each( tncdetails, function( tncdkey, tncdvalue ) { 

  if(tncvalue.TNCDID_REF == tncdvalue.TNCDID)
  {
      $('#popupTNCDID_'+tnckey).val(tncdvalue.TNC_NAME);
  }

  if( $.trim(tncvalue.TNCDID_REF) == $.trim(tncdvalue.TNCDID))
  {        
            var txtvaltype =   tncdvalue.VALUE_TYPE;
            var txt_id4 = $('#tdinputid_'+tnckey).attr('id');
            var strdyn = txt_id4.split('_');
            var lastele =   strdyn[strdyn.length-1];
            var dynamicid = "tncdetvalue_"+lastele;
            
            var chkvaltype =  txtvaltype.toLowerCase();
            var strinp = '';

            if(chkvaltype=='date'){

            strinp = '<input {{$ActionStatus}} type="date" placeholder="dd/mm/yyyy" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"  > ';       

            }
            else if(chkvaltype=='time'){
            strinp= '<input {{$ActionStatus}} type="time" placeholder="h:i" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"  > ';

            }
            else if(chkvaltype=='numeric'){
            strinp = '<input {{$ActionStatus}} type="text" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"   > ';

            }
            else if(chkvaltype=='text'){

            strinp = '<input {{$ActionStatus}} type="text" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"  > ';
            
            }
            else if(chkvaltype=='boolean'){
              if(tncvalue.VALUE == "1")
              {
                strinp = '<input {{$ActionStatus}} type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" checked> ';
              }
              else{
                strinp = '<input {{$ActionStatus}} type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" > ';
              }                    
            }
            else if(chkvaltype=='combobox'){

            var txtoptscombo =   tncdvalue.DESCRIPTIONS;
            var strarray = txtoptscombo.split(',');
            var opts = '';

            for (var i = 0; i < strarray.length; i++) {
                opts = opts + '<option value="'+strarray[i]+'">'+strarray[i]+'</option> ';
            }

            strinp = '<select {{$ActionStatus}} name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" required>'+opts+'</select>' ;
            
            }
             
            $('#'+txt_id4).html('');  
            $('#'+txt_id4).html(strinp);   //set dynamic input
            $('#'+dynamicid).val(tncvalue.VALUE);
            $('#TNCismandatory_'+tnckey).val(tncdvalue.IS_MANDATORY); // mandatory
        
    }
});
});  



///----------------
var obj = <?php echo json_encode($objList1); ?>;
$.each( obj, function( key, value ) {
     // var sqid = value.VQPINO;
     
     
      var itemid = value.ITEMID_REF;
    
      var amtbeforedis = parseFloat(value.RATEP_UOM*value.QUOTATION_QTY).toFixed(2);
      var dipercent = value.DISCOUNT_PER;
      var diamount = value.DISCOUNT_AMT;
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

  bindTotalValue();
  if($('#CTID_REF').val()!='')
  {
    bindGSTCalTemplate();
  }
  bindTotalValue();
///----------------
//--------------

var lastdt = <?php echo json_encode($objlastVQ_DT[0]->VQ_DT); ?>;
var today = new Date(); 
var current_date = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
$('#VQ_DT').attr('min',lastdt);
$('#VQ_DT').attr('max',current_date);

//$('#VQ_DT').val(lastVQ_DT);

//  var today = new Date(); 
//  var currentdate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
//  $('#VQ_DT').attr('min',currentdate);
//  $('#VQ_DT').val(currentdate);

 
  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  d.setDate(d.getDate() + 29);
  var todate = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;

  //$('#QUOTATION_VFR').val(today);
  //$('#QUOTATION_VTO').val(todate);

  $('#QUOTATION_VFR').change(function( event ) {
      var d = document.getElementById('QUOTATION_VFR').value; 
      var date = new Date(d);
      var newdate = new Date(date);
      newdate.setDate(newdate.getDate() + 29);
      var sodate = newdate.getFullYear() + "-" + ("0" + (newdate.getMonth() + 1)).slice(-2) + "-" + ('0' + newdate.getDate()).slice(-2) ;
      $('#QUOTATION_VTO').val(sodate);
      
  });

  var Material = $("#Material").html(); 
  //$('#hdnmaterial').val(Material);
  
  var CT = $("#CT").html(); 
  $('#hdnct').val(CT);



  //$("#Row_Count1").val(1);
  // $("#Row_Count3").val(count3);
  //$("#Row_Count5").val(1);

  // $('#udf').find('.participantRow4').each(function(){
  //   var txt_id4 = $(this).find('[id*="udfinputid"]').attr('id');
  //   var udfid = $(this).find('[id*="UDFSOID_REF"]').val();
  //   $.each( soudf, function( soukey, souvalue ) {
  //     if(souvalue.UDFSSIID == udfid)
  //     {
  //       var txtvaltype2 =   souvalue.VALUETYPE;
  //       var strdyn2 = txt_id4.split('_');
  //       var lastele2 =   strdyn2[strdyn2.length-1];
  //       var dynamicid2 = "udfvalue_"+lastele2;
        
  //       var chkvaltype2 =  txtvaltype2.toLowerCase();
  //       var strinp2 = '';

  //       if(chkvaltype2=='date'){
  //       strinp2 = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';       
  //       }
  //       else if(chkvaltype2=='time'){
  //       strinp2= '<input type="time" placeholder="h:i" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';
  //       }
  //       else if(chkvaltype2=='numeric'){
  //       strinp2 = '<input type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"   > ';
  //       }
  //       else if(chkvaltype2=='text'){
  //       strinp2 = '<input type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';          
  //       }
  //       else if(chkvaltype2=='boolean'){            
  //           strinp2 = '<input type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" > ';
  //       }
  //       else if(chkvaltype2=='combobox'){
  //       var txtoptscombo2 =   souvalue.DESCRIPTIONS;
  //       var strarray2 = txtoptscombo2.split(',');
  //       var opts2 = '';
  //       for (var i = 0; i < strarray2.length; i++) {
  //           opts2 = opts2 + '<option value="'+strarray2[i]+'">'+strarray2[i]+'</option> ';
  //       }
  //       strinp2 = '<select name="'+dynamicid2+ '" id="'+dynamicid2+'" class="form-control" required>'+opts2+'</select>' ;          
  //       }
  //       $('#'+txt_id4).html('');  
  //       $('#'+txt_id4).html(strinp2);
  //     }
  //   });
  // });

  //$(function() { $('[id*="VQ_NO"]').focus(); }); 

  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  d.setDate(d.getDate() + 29);
  var todate = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  //$('#VQ_DT').val(today);
  //$('#OVFDT').val(today);
  //$('#OVTDT').val(todate);
  //$('#VENDOR_QDT').val(today);
  
  
  
  $('#DirectSO').change(function(){
    if ($(this).is(":checked") == true){
        $('#Material').find('[id*="txtRFQ_popup"]').prop('disabled','true')
        event.preventDefault();
    }
    else
    {
        $('#Material').find('[id*="txtRFQ_popup"]').removeAttr('disabled');
        event.preventDefault();
    }
  });


  $('#Material').on('focusout',"[id*='ALT_UOMID_QTY']",function()
  {
    if(intRegex.test($(this).val())){
      $(this).val($(this).val()+'.000')
    }
    event.preventDefault();
  });

  

  $('#Material').on('focusout',"[id*='VQ_QTY']",function()
  {
    var totalvalue = 0.00;
      var itemid = $(this).parent().parent().find('[id*="ITEMID_REF"]').val();
      var mqty = $(this).val();
      var altuomid = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').val();
      var txtid = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
      var irate = $(this).parent().parent().find('[id*="RATEPUOM"]').val();
      $(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
      $(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
      $(this).parent().parent().find('[id*="SGSTAMT"]').val('0');
      var tamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);
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
      tamt = parseFloat(parseFloat(tamt) - parseFloat(disamt)).toFixed(2);   
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
      var mqty = $(this).parent().parent().find('[id*="VQ_QTY"]').val();
      var irate = $(this).val();
      var taxamt = $(this).parent().parent().find('[id*="TGST_AMT"]').val();
              
      var tamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);  
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

 // $('#Material').on('focusout',"[id*='DISCPER']",function()
  $('#Material').on('blur',"[id*='DISCPER']",function()
  { 
    var mqty = $(this).parent().parent().find('[id*="VQ_QTY"]').val();
    var irate = $(this).parent().parent().find('[id*="RATEPUOM"]').val();
    var totamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);
    var dpert = $(this).val();
   
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

  //$('#Material').on('focusout',"[id*='DISCOUNT_AMT']",function()
  $('#Material').on('blur',"[id*='DISCOUNT_AMT']",function()
  {
    var mqty = $(this).parent().parent().find('[id*="VQ_QTY"]').val();
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
      $(this).val($(this).val()+'.00')
    }
    bindTotalValue();
  });

  $('#CT').on('focusout',"[id*='calCGST_']",function()
  {
    if(intRegex.test($(this).val())){
      $(this).val($(this).val()+'.00')
    }
    bindTotalValue();
  });

  $('#CT').on('focusout',"[id*='calIGST_']",function()
  {
    if(intRegex.test($(this).val())){
      $(this).val($(this).val()+'.00')
    }
    bindTotalValue();
  });

  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("transaction",[$FormId,"add"])}}';
                window.location.href=viewURL;
  });
  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
                window.location.href=viewURL;
  });
  //to check the label duplicacy
   $('#VQ_NO').focusout(function(){
    var VQ_NO   =   $.trim($(this).val());
    if(VQ_NO ===""){
              $("#FocusId").val('VQ_NO');
              // $("[id*=txtlabel]").blur(); 
              
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('Please enter value in VQ_NO.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk1');
              // return false;
          } 
      else{ 
      var trnsoForm = $("#frm_trn_add");
      var formData = trnsoForm.serialize();
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url:'{{route("transaction",[$FormId,"checkso"])}}',
          type:'POST',
          data:formData,
          success:function(data) {
             if(data.exists) {
                  $(".text-danger").hide();
                  if(data.exists) {                   
                      console.log("cancel MSG="+data.msg);
                                    $("#YesBtn").hide();
                                    $("#NoBtn").hide();
                                    $("#OkBtn1").show();
                                    $("#AlertMessage").text(data.msg);
                                    $(".text-danger").hide();
                                    $("#VQ_NO").val('');
                                    $("#alert").modal('show');
                                    $("#OkBtn1").focus();
                                    highlighFocusBtn('activeOk1');
                  }                 
              }                
          },
          error:function(data){
            console.log("Error: Something went wrong.");
          },
      });
  }

  //getSavedBillTo();  
 // ctcal();
}); //READY FUNC

//SO Date Check
// $('#VQ_DT').change(function( event ) {
//           var today = new Date();     
//           var d = new Date($(this).val()); 
//           today.setHours(0, 0, 0, 0) ;
//           d.setHours(0, 0, 0, 0) ;
//           var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
//           if (d < today) {
//               $(this).val(sodate);
//               $("#alert").modal('show');
//               $("#AlertMessage").text('VQ Date cannot be less than Current date');
//               $("#YesBtn").hide(); 
//               $("#NoBtn").hide();  
//               $("#OkBtn1").show();
//               $("#OkBtn1").focus();
//               highlighFocusBtn('activeOk1');
//               event.preventDefault();
//           }
//           else
//           {
//               event.preventDefault();
//           }

         
//       });
//SO Date Check

//SO Validity to Date Check
$('#OVFDT').change(function( event ) {
          var d = document.getElementById('OVFDT').value; 
          var date = new Date(d);
          var newdate = new Date(date);
          newdate.setDate(newdate.getDate() + 29);
          var sodate = newdate.getFullYear() + "-" + ("0" + (newdate.getMonth() + 1)).slice(-2) + "-" + ('0' + newdate.getDate()).slice(-2) ;
          $('#OVTDT').val(sodate);
          
      });

//SO Validity to Date Check
$('#example6').on('change','[id*="PAY_DAYS"]',function( event ) {
          var d = $(this).val(); 
          d = parseInt(d) - 1;
          var sdate =$('#VQ_DT').val();
          var ddate = new Date(sdate);
          var newddate = new Date(ddate);
          newddate.setDate(newddate.getDate() + d);
          var soddate = newddate.getFullYear() + "-" + ("0" + (newddate.getMonth() + 1)).slice(-2) + "-" + ('0' + newddate.getDate()).slice(-2) ;
          $(this).parent().parent().find('[id*="DUE_DATE"]').val(soddate);
          
      });
//SO Date Check
      
  




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


 $("#SOFC").change(function() {
    if ($(this).is(":checked") == true){
        $(this).parent().parent().find('#txtCRID_popup').removeAttr('disabled');
        $(this).parent().parent().find('#txtCRID_popup').prop('readonly','true');
        event.preventDefault();
    }
    else
    {
        $(this).parent().parent().find('#txtCRID_popup').prop('disabled','true');
        $(this).parent().parent().find('#txtCRID_popup').removeAttr('readonly');
        $(this).parent().parent().find('#txtCRID_popup').val('');
        $(this).parent().parent().find('#CRID_REF').val('');
        $(this).parent().parent().find('#CONVFACT').val('');
        event.preventDefault();
    }
});

$("#CT").on('change',"[id*='calGST']",function() {
    if ($(this).is(":checked") == true){
      if($.trim($('#Tax_State').val()) == 'OutofState')
        {
          $(this).parent().parent().find('[id*="calIGST"]').removeAttr('disabled');
          $(this).parent().parent().find('[id*="calIGST"]').removeAttr('readonly');
          $(this).parent().parent().find('[id*="calCGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="calCGST"]').val('0');
          $(this).parent().parent().find('[id*="calSGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="calSGST"]').val('0');
          $(this).parent().parent().find('[id*="AMTIGST"]').removeAttr('disabled');
          $(this).parent().parent().find('[id*="AMTIGST"]').removeAttr('readonly');
          $(this).parent().parent().find('[id*="AMTCGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="AMTSGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="AMTCGST"]').val('0');
          $(this).parent().parent().find('[id*="AMTSGST"]').val('0');
          $(this).parent().parent().find('[id*="TOTGSTAMT"]').val('0');
          bindTotalValue();
          event.preventDefault();
        }
        else
        {
          $(this).parent().parent().find('[id*="calIGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="calCGST"]').removeAttr('disabled');
          $(this).parent().parent().find('[id*="calSGST"]').removeAttr('disabled');
          $(this).parent().parent().find('[id*="calCGST"]').removeAttr('readonly');
          $(this).parent().parent().find('[id*="calSGST"]').removeAttr('readonly');
          $(this).parent().parent().find('[id*="AMTIGST"]').prop('disabled','true');
          $(this).parent().parent().find('[id*="AMTCGST"]').removeAttr('disabled');
          $(this).parent().parent().find('[id*="AMTSGST"]').removeAttr('disabled');
          $(this).parent().parent().find('[id*="AMTCGST"]').removeAttr('readonly');
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


});
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



function validateForm(saveAction){

  $("#FocusId").val('');
  var VQ_NO           =   $.trim($("#VQ_NO").val());
  var VQ_DT           =   $.trim($("#VQ_DT").val());
  var QUOTATION_VFR   =   $.trim($("#QUOTATION_VFR").val());
  var QUOTATION_VTO   =   $.trim($("#QUOTATION_VTO").val());
  var BILLTO          =   $.trim($("#BILLTO").val());
  var SHIPTO          =   $.trim($("#SHIPTO").val());
  
  var VID_REF       =   $.trim($("#VID_REF").val());

  if(VQ_NO ===""){
      $("#FocusId").val($("#VQ_NO"));        
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please enter value in VQ_NO.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }
  else if(VQ_DT ===""){
      $("#FocusId").val($("#VQ_DT"));        
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select VQ Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(QUOTATION_VFR ===""){
      $("#FocusId").val($("#QUOTATION_VFR"));        
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Quotation Validity From Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(QUOTATION_VTO ===""){
      $("#FocusId").val($("#QUOTATION_VTO"));        
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Quotation Validity To Date.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  } 
  else if(VID_REF ===""){
      $("#FocusId").val($("#VID_REF"));
      $("#VID_REF").val('');          
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select Vendor.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }    
  else if(BILLTO ===""){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select BILL TO.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }    
  else if(SHIPTO ===""){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Please select SHIP TO.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
  }    
  else{
      event.preventDefault();

      if(new Date(QUOTATION_VFR)>new Date(QUOTATION_VTO)){
        $("#FocusId").val($("#QUOTATION_VFR"));
        $("#QUOTATION_VFR").val('');            
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Quotation Validity From Date must be less than To Date.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
      }


      var RackArray = []; 
      var allblank = [];
      var allblank2 = [];
      var allblank3 = [];
      var allblank4 = [];
      var allblank5 = [];
      var allblank6 = [];
      var allblank7 = [];
      var allblank8 = [];
      var allblank9 = [];
      var allblank10 = [];
      var allblank11 = [];
      var allblank12 = [];
      var allblank13 = [];
      var allblank15 = [];
      var allblank16 = [];
      var allblank17 = [];

      
      
    $('#Material').find('.participantRow').each(function(){

          var RFQIDREF     = $.trim($(this).find('[id*="RFQID"]').val());
          var PINO    = $.trim($(this).find('[id*="PINO"]').val());
          var ITEMID_REF    = $.trim($(this).find('[id*="ITEMID_REF"]').val());
          var MRSNO    = $.trim($(this).find('[id*="MRSNO"]').val());
          var custrecordno  = RFQIDREF+'-'+PINO+'-'+ITEMID_REF+'-'+MRSNO;


          if (RackArray.indexOf(custrecordno) > -1) {
            allblank15.push('true');
          }
          else{
            allblank15.push('false');
          }

         
          if($("#DIRECT_VQ").prop("checked")==false && $.trim($(this).find('[id*="RFQID"]').val())=="" ){

            allblank13.push('false');
          }
          else{
            allblank13.push('true');
          }  

          if($("#DIRECT_VQ").prop("checked")==false){
              if($.trim(parseFloat($(this).find('[id*="VQ_QTY"]').val())) <= $.trim(parseFloat($(this).find('[id*="RFQ_QTY"]').val())) ){

               // alert('true');
               allblank16.push('true');
            }
            else{
              //alert('false');
              allblank16.push('false');
            }
          }
          else{
            allblank16.push('true');
          }
         

          if($.trim($(this).find('[id*="VQ_QTY"]').val()) < '0.001'){
            allblank17.push('true');
          }
          else{
            allblank17.push('false');
          }  


          if($.trim($(this).find("[id*=ITEMID_REF]").val())!="")
          {
              allblank.push('true');
                  if($.trim($(this).find("[id*=popupMUOM]").val())!=""){
                      allblank2.push('true');
                        if($.trim($(this).find('[id*="VQ_QTY"]').val()) != "")
                        {
                          allblank3.push('true');
                        }
                        else
                        {
                          allblank3.push('false');
                        }  
                  }
                  else{
                      allblank2.push('false');
                  } 
          }
          else
          {
              allblank.push('false');
          } 
          if($.trim($(this).find("[id*=RATEPUOM]").val())!="")
          {
            allblank4.push('true');
          }
          else
          {
            allblank4.push('true');
          }
          if($.trim($('#Tax_State').val())=="WithinState")
          {
            if($.trim($(this).find("[id*=IGST]").val())!="")
            {
              allblank5.push('true');
            }
            else
            {
              allblank5.push('true');
            }
          }
          else
          {
            if($.trim($(this).find("[id*=CGST]").val())!="")
            {
              allblank5.push('true');
            }
            else
            {
              allblank5.push('true');
            }
            if($.trim($(this).find("[id*=SGST]").val())!="")
            {
              allblank5.push('true');
            }
            else
            {
              allblank5.push('true');
            }
          }
          
          RackArray.push(custrecordno);
    });

          if($('#TNCID_REF').val() !="")
          {
              $('#TC').find('.participantRow3').each(function(){
                if($.trim($(this).find("[id*=TNCDID_REF]").val())!="")
                  {
                      allblank6.push('true');
                          if($.trim($(this).find("[id*=TNCismandatory]").val())=="1"){
                                if($.trim($(this).find('[id*="tncdetvalue"]').val()) != "")
                                {
                                  allblank7.push('true');
                                }
                                else
                                {
                                  allblank7.push('false');
                                } 
                          } 
                  }
                  else
                  {
                      allblank6.push('false');
                  } 
              });
          }
          // $('#udf').find('.participantRow4').each(function(){  //udf validation
          //       if($.trim($(this).find("[id*=UDFSOID_REF]").val())!="")
          //         {
          //             allblank8.push('true');
          //                 if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
          //                       if($.trim($(this).find('[id*="udfvalue"]').val()) != "")
          //                       {
          //                         allblank9.push('true');
          //                       }
          //                       else
          //                       {
          //                         allblank9.push('false');
          //                       }
          //                 }  
          //         }                
          // });
            $("[id*=txtudffie_popup]").each(function(){
                if($.trim($(this).val())!="")
                {
                    if($.trim($(this).parent().parent().find('[id*="udffieismandatory"]').val()) == "1")
                      {
                        if($.trim($(this).parent().parent().find('[id*="udfvalue"]').val()) != "")
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


          if($('#CTID_REF').val() !="")
          {
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
          $('#example6').find('.participantRow6').each(function(){
                if($.trim($(this).find("[id*=PAY_DAYS]").val())!="")
                  {
                    if($.trim($(this).find('[id*="DUE"]').val()) != "")
                    {
                      allblank12.push('true');
                    }
                    else
                    {
                      allblank12.push('false');
                    }       
                  }                
          });

          if(jQuery.inArray("false", allblank13) !== -1){
              $("#alert").modal('show');
              $("#AlertMessage").text('Please select RFQ NO in Material Tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(jQuery.inArray("false", allblank) !== -1){
              $("#alert").modal('show');
              $("#AlertMessage").text('Please select Item Code in Material Tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(jQuery.inArray("true", allblank15) !== -1){
              $("#alert").modal('show');
              $("#AlertMessage").text('Duplicate Code in Material Tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(jQuery.inArray("false", allblank2) !== -1){
              $("#alert").modal('show');
              $("#AlertMessage").text('UOM section is missing in Material Tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(jQuery.inArray("false", allblank3) !== -1){
              $("#alert").modal('show');
              $("#AlertMessage").text('Please Enter Quantity in Material Tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(jQuery.inArray("true", allblank17) !== -1){
              $("#alert").modal('show');
              $("#AlertMessage").text('VQ Qty should be greater then zero.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(jQuery.inArray("false", allblank16) !== -1){
              $("#alert").modal('show');
              $("#AlertMessage").text('VQ Qty should not greater then RFQ Qty.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }               
          else if(jQuery.inArray("false", allblank4) !== -1){
              $("#alert").modal('show');
              $("#AlertMessage").text('Please enter Rate per UOM in Material Tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(jQuery.inArray("false", allblank5) !== -1){
              $("#alert").modal('show');
              $("#AlertMessage").text('Please enter GST Rate / Value in Material Tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(jQuery.inArray("false", allblank6) !== -1){
              $("#alert").modal('show');
              $("#AlertMessage").text('Please select Terms & Condition Description in T&C Tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(jQuery.inArray("false", allblank7) !== -1){
              $("#alert").modal('show');
              $("#AlertMessage").text('Please enter Value / Comment in T&C Tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(jQuery.inArray("false", allblank9) !== -1){
              $("#alert").modal('show');
              $("#AlertMessage").text('Please enter  Value / Comment in UDF Tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(jQuery.inArray("false", allblank10) !== -1){
              $("#alert").modal('show');
              $("#AlertMessage").text('Please select Calculation Component in Calculation Template Tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(jQuery.inArray("false", allblank11) !== -1){
              $("#alert").modal('show');
              $("#AlertMessage").text('Please Enter GST Rate / Value in Calculation Template Tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(jQuery.inArray("false", allblank12) !== -1){
              $("#alert").modal('show');
              $("#AlertMessage").text('Please Enter Due % in Payment Slabs Tab.');
              $("#YesBtn").hide(); 
              $("#NoBtn").hide();  
              $("#OkBtn1").show();
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk');
              return false;
          }
          else if(checkPeriodClosing('{{$FormId}}',$("#VQ_DT").val(),0) ==0){
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text(period_closing_msg);
              $("#alert").modal('show');
              $("#OkBtn1").focus();
            }
          else{

              $("#alert").modal('show');
              $("#AlertMessage").text('Do you want to save to record.');
              $("#YesBtn").data("funcname",saveAction);  //set dynamic fucntion name
              $("#OkBtn1").hide();
              $("#OkBtn").hide();
              $("#YesBtn").show();
              $("#NoBtn").show();
              $("#YesBtn").focus();
              highlighFocusBtn('activeYes');
          }
      

  }

}

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
  $("#btnSaveFormData").hide(); 
  $(".buttonload").show(); 
  $("#btnApprove").prop("disabled", true);
  $.ajax({
      url:'{{ route("transaction",[$FormId,"update"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
      $(".buttonload").hide(); 
      $("#btnSaveFormData").show();   
      $("#btnApprove").prop("disabled", false);
        
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
          $(".buttonload").hide(); 
          $("#btnSaveFormData").show();   
          $("#btnApprove").prop("disabled", false);
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
  $("#btnApprove").hide(); 
  $(".buttonload_approve").show();  
  $("#btnSaveFormData").prop("disabled", true);
  $.ajax({
      url:'{{ route("transaction",[$FormId,"Approve"])}}',
      type:'POST',
      data:formData,
      success:function(data) {
        $("#btnApprove").show();  
        $(".buttonload_approve").hide();  
        $("#btnSaveFormData").prop("disabled", false);
        
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
        $("#btnApprove").show();  
        $(".buttonload_approve").hide();  
        $("#btnSaveFormData").prop("disabled", false);
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



$('input[type=checkbox][name=DIRECT_VQ]').change(function() {
    if($("#DIRECT_VQ").prop("checked")) {
        $("#DIRECT_VQ").val('1');
        $("#Row_Count1").val('1');

        $("#Material").html($('#hdnmaterial').val());
       // $("#CT").html($('#hdnct').val());
        
        $('.CLS_RFQ').attr('disabled', 'disabled');

        
    }
    else {
      $("#DIRECT_VQ").val('0');
      $("#Row_Count1").val('1');


      $("#Material").html($('#hdnmaterial').val());
     // $("#CT").html($('#hdnct').val());

      $('.CLS_RFQ').removeAttr('disabled');
     
    }      
});

function doCalculation(){
  $(".blurRate").blur();
  //$(".blurDISCPER").blur();
  //$(".blurDISCOUNT_AMT").blur();

}

function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
  return false;
}



function getSavedBillTo222(){
            
    var customid = $("#VID_REF").val();;
    if(customid!=''){
        
      $("#txtBILLTO").val('');
      $("#BILLTO").val('');
      $("#txtBILLTO1").val('');
      $("#BILLTO1").val('');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[$FormId,"getBillTo"])}}',
            type:'POST',
            data:{'id':customid},
            success:function(data) {
              $("#txtBILLTO1").hide();
              $("#div_billto").html(data);
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              $("#txtBILLTO").hide();
              $("#txtBILLTO1").show();
            },
        });  

      $("#txtSHIPTO").val('');
      $("#SHIPTO").val('');
      $("#txtSHIPTO1").val('');
      $("#SHIPTO1").val('');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[$FormId,"getShipTo"])}}',
            type:'POST',
            data:{'id':customid},
            success:function(data) {
              $("#txtSHIPTO1").hide();
              $("#div_shipto").html(data);
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              $("#txtSHIPTO").hide();
              $("#txtSHIPTO1").show();
            },
        });  
        $("#tbody_BillTo").html('');
        $.ajax({
            url:'{{route("transaction",[$FormId,"getBillAddress"])}}',
            type:'POST',
            data:{'id':customid},
            success:function(data) {
              $("#tbody_BillTo").html(data);
              BindBillAddress();
              doCalculation();
              ctcal();
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              $("#tbody_BillTo").html('');
            },
        });   
        $("#tbody_ShipTo").html('');
        $.ajax({
            url:'{{route("transaction",[$FormId,"getShipAddress"])}}',
            type:'POST',
            data:{'id':customid},
            success:function(data) {
              $("#tbody_ShipTo").html(data);  
              doCalculation();
              BindShipAddress();                 
              ctcal();
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              $("#tbody_ShipTo").html('');
            },
        });  
    }
}

// getSavedBillTo();

function ctcal22(){

 // doCalculation();


var count4 = <?php echo json_encode($objCount4); ?>;
$('#Row_Count4').val(count4);
var obj = <?php echo json_encode($objList1); ?>;
  var calheader = <?php echo json_encode($objCalHeader); ?>;
  var caldetails = <?php echo json_encode($objCalDetails); ?>;
  var SOCal = <?php echo json_encode($objVQCAL); ?>;
  var taxstate = <?php echo json_encode($TAXSTATE); ?>;

  var totalvalue = 0.00;
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
  // totalvalue = parseFloat(totalvalue).toFixed(2);
  var grosstotal = parseFloat(totalvalue) + parseFloat($('#TotalValue').val());
 
  $('#TotalValue').val(grosstotal);
 
}

function ctcal(){

doCalculation();


var obj = <?php echo json_encode($objList1); ?>;
var calheader = <?php echo json_encode($objCalHeader); ?>;
var caldetails = <?php echo json_encode($objCalDetails); ?>;
var SOCal = <?php echo json_encode($objVQCAL); ?>;
var taxstate = <?php echo json_encode($TAXSTATE); ?>;

var totalvalue = 0.00;
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
// totalvalue = parseFloat(totalvalue).toFixed(2);
var grosstotal = parseFloat(totalvalue) + parseFloat($('#TotalValue').val());

$('#TotalValue').val(grosstotal);

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