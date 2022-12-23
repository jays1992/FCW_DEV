
@extends('layouts.app')
@section('content')

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[44,'index'])}}" class="btn singlebt">Sales Invoice (SI)</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSI" ><i class="fa fa-floppy-o"></i> Save</button>
                        <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button style="display:none" class="btn topnavbt buttonload_approve" > <i class="fa fa-refresh fa-spin"></i> {{Session::get('approve')}}</button>
                        <button class="btn topnavbt" id="btnApprove" {{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}} ><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div>
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->
    <div class="container-fluid purchase-order-view">
    <form id="frm_trn_si"  method="POST">   
            @csrf
            {{isset($objSI->SIID[0]) ? method_field('PUT') : '' }}
            <div class="container-fluid filter">
                    <div class="inner-form">
						<div class="row">
							<div class="col-lg-2 pl"><p>Type*</p></div>
							<div class="col-lg-2 pl">
								<select name="TYPE" id="TYPE" class="form-control" >
									   <option value="SALES" {{ isset($objSI->TYPE) && $objSI->TYPE == 'SALES' ? 'selected' : ''}}>Sales</option>
									   <option value="TRANSFER" {{ isset($objSI->TYPE) && $objSI->TYPE == 'TRANSFER' ? 'selected' : ''}}>STOCK TRANSFER</option>
								</select>
							</div>
						</div>
                        <div class="row">
                            <div class="col-lg-2 pl"><p>Sales Invoice No*</p></div>
                            <div class="col-lg-2 pl">
                            
                                <input {{$InputStatus}} type="text" name="SINO" id="SINO" value="{{ isset($objSI->SINO)?$objSI->SINO:'' }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus />
                            
                            </div>
                            <div class="col-lg-2 pl "><p>Sales Invoice Date*</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="date" name="SIDT" id="SIDT" onchange="checkPeriodClosing(44,this.value,1)" value="{{ isset($objSI->SIDT)?$objSI->SIDT:'' }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" format="mm/dd/yyyy" />
                            </div>                               
                            <div class="col-lg-2 pl"><p>Customer*</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="text" name="SubGl_popup" id="txtsubgl_popup" class="form-control mandatory" value="{{ isset($objsubglcode->SGLCODE)?$objsubglcode->SGLCODE:'' }} {{ isset($objsubglcode->SLNAME)?$objsubglcode->SLNAME:''}}"  autocomplete="off" readonly/>
                                <input type="hidden" name="SLID_REF" id="SLID_REF" class="form-control" autocomplete="off" value="{{ isset($objSI->SLID_REF)?$objSI->SLID_REF:'' }}"  />
                                <input type="hidden" name="GLID_REF" id="GLID_REF" class="form-control" autocomplete="off" value="{{ isset($objSI->GLID_REF)?$objSI->GLID_REF:'' }}"  />
                                <input type="hidden" name="hdnMaterial" id="hdnMaterial" class="form-control" autocomplete="off" />
                                <input type="hidden" name="hdnStore" id="hdnStore" class="form-control" autocomplete="off" />
                            </div>
                        </div> 
                        
                        <div class="row">
                            <div class="col-lg-2 pl"><p>Foreign Currency</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="checkbox" name="SOFC" id="SOFC" class="form-checkbox" {{isset($objSI->SIFC) && $objSI->SIFC == 1 ? 'checked' : ''}} >
                            </div>
                            
                            <div class="col-lg-2 pl"><p>Currency</p></div>
                            <div class="col-lg-2 pl" id="divcurrency" >

                                @if ($objsocurrency)
                                <input {{$InputStatus}} type="text" name="CRID_popup" id="txtCRID_popup" class="form-control"  autocomplete="off" value="{{ isset($objsocurrency[0])?$objsocurrency[0]:'' }}" readonly   />
                                @else
                                <input {{$InputStatus}} type="text" name="CRID_popup" id="txtCRID_popup" class="form-control"  autocomplete="off"   disabled/>
                                @endif
                                <input {{$InputStatus}} type="hidden" name="CRID_REF" id="CRID_REF" class="form-control" autocomplete="off"   value="{{ isset($objSI->CRID_REF)?$objSI->CRID_REF:'' }}" />
                                
                            </div>
                            
                            <div class="col-lg-2 pl"><p>Conversion Factor</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="text" name="CONVFACT" id="CONVFACT" class="form-control" maxlength="100" autocomplete="off" value="{{ isset($objSI->CONVFACT)?$objSI->CONVFACT:'' }}" {{isset($objSI->SIFC) && $objSI->SIFC  == 1 ? 'readonly' : 'disabled'}} />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-2 pl"><p>Bill To </p></div>
                            <div class="col-lg-4 pl" id="div_billto">
                                <input {{$InputStatus}} type="text" name="txtBILLTO" id="txtBILLTO" class="form-control"  autocomplete="off" value="{{ isset($objBillAddress[0])?$objBillAddress[0]:'' }}" readonly  />
                                <input type="hidden" name="BILLTO" id="BILLTO" class="form-control" autocomplete="off" value="{{ isset($objSI->BILLTO)?$objSI->BILLTO:'' }}" />
                            </div>
                            <div class="col-lg-2 pl"><p>Ship To</p></div>
                            <div class="col-lg-4 pl" id="div_shipto">
                                <input {{$InputStatus}} type="text" name="txtSHIPTO" id="txtSHIPTO" class="form-control"  autocomplete="off" value="{{ isset($objShpAddress[0])?$objShpAddress[0]:'' }}" readonly  />
                                <input type="hidden" name="SHIPTO" id="SHIPTO" class="form-control" autocomplete="off" value="{{ isset($objSI->SHIPTO)?$objSI->SHIPTO:'' }}" />
                                <input type="hidden" name="Tax_State" id="Tax_State" class="form-control" autocomplete="off" value=" {{ isset($TAXSTATE[0])?$TAXSTATE[0]:'' }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2 pl"><p>Credit Days</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="text" name="CREDIT_DAYS" id="CREDIT_DAYS" class="form-control"  autocomplete="off" value="{{ isset($objSI->CREDIT_DAYS)?$objSI->CREDIT_DAYS:'' }}"  readonly/>
                            </div>                            
                            <div class="col-lg-2 pl"><p>Due Date</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="date" name="DUE_DATE" id="DUE_DATE" class="form-control"  autocomplete="off" placeholder="dd/mm/yyyy" value="{{ isset($objSI->DUE_DATE)?$objSI->DUE_DATE:'' }}"   />
                            </div>
                            <div class="col-lg-2 pl"><p>Total Value</p></div>
                            <div class="col-lg-2 pl">
                                <input type="text" name="TotalValue" id="TotalValue" class="form-control"  autocomplete="off" readonly  />
                            </div>
                            
                        </div>

                        <div class="row" id="LUT_STATUS" style="display:none;">
                            <div class="col-lg-2 pl"><p>Lut Applicable</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="checkbox" name="LUT" id="LUT" value="1" autocomplete="off" onChange="getLutApplicable()" {{isset($objSI->LUT) && $objSI->LUT == 1 ? 'checked' : ''}}  />
                            </div>                            
                  
                            <div class="col-lg-2 pl"><p>Lut Number</p></div>
                            <div class="col-lg-2 pl">
                                <input {{$InputStatus}} type="text" name="LUT_NO" id="LUT_NO" value="{{isset($objSI->LUT_NO)?$objSI->LUT_NO:''}}" class="form-control"  autocomplete="off" {{isset($objSI->LUT) && $objSI->LUT == 1 ? '' : 'readonly'}}  />
                            </div>
                        </div>

                        <div class="row">
                        <div class="col-lg-2 pl"><p>Reverse GST</p></div>
                        <div class="col-lg-2 pl">
                            <input type="checkbox" {{$InputStatus}} name="GST_Reverse" id="GST_Reverse" {{isset($objSI->REVERSE_GST) && $objSI->REVERSE_GST == 1 ? 'checked' : ''}}    />                          
                        </div>
                        <div class="col-lg-2 pl"><p>GST Input Not Avail</p></div>
                        <div class="col-lg-2 pl">
                            <input type="checkbox" {{$InputStatus}} name="GST_N_Avail" id="GST_N_Avail"  {{isset($objSI->GST_INPUT) && $objSI->GST_INPUT == 1 ? 'checked' : ''}}    />
                        </div>      
                        
             
                        <div class="col-lg-2 pl ExceptionalGST" style="display:none;" ><p>Exemptional for GST</p></div>
                        <div class="col-lg-2 pl ExceptionalGST" style="display:none;">
                        <input type="checkbox" {{$InputStatus}} name="EXE_GST" id="EXE_GST" class="filter-none"  value="1" onchange="getExceptionalGst()" >
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
                              <li><a data-toggle="tab" href="#PaymentSlabs">Payment Slabs</a></li>
                              <li><a data-toggle="tab" href="#TDS">TDS</a></li> 
                              <li><a data-toggle="tab" href="#ADDITIONAL" id="ADDITIONAL_TAB">Additional Info</a></li>
                        </ul>
                            <div class="tab-content">
                                <div id="Material" class="tab-pane fade in active">
                                <div class="row"><div class="col-lg-4" style="padding-left: 15px;">Note:- 1 row mandatory in Material Tab </div></div>
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                                        <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                                    
                                                    <tr>
                                                        <th colspan="{{$AlpsStatus['colspan']}}"></th>
                                                        <th colspan="4">Sales Challan</th>
                                                        <th colspan="4">Sales Invoice</th>
                                                        <th colspan="17"></th>
                                                    </tr>
                                                    <tr>
                                                        <th rowspan="2" width="3%" >SC No<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
                                                        <th rowspan="2">Lead No</th>
                                                        <th rowspan="2">Lead Date</th>
                                                        <th rowspan="2">Item Code</th>
                                                        <th rowspan="2">Item Name</th>

                                                        <th rowspan="2" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                                                        <th rowspan="2" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                                                        <th rowspan="2" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>

                                                        <th rowspan="2">Item Specification</th>
                                                        <th rowspan="2">Main UOM</th>
                                                        <th rowspan="2">Qty (Main UOM)</th>
                                                        <th rowspan="2">ALT UOM</th>
                                                        <th rowspan="2">Qty (Alt UOM)</th>
                                                        <th rowspan="2">Main UOM</th>
                                                        <th rowspan="2">Qty (Main UOM)</th>
                                                        <th rowspan="2">ALT UOM</th>
                                                        <th rowspan="2">Qty (Alt UOM)</th>
                                                        <th rowspan="2">Rate Per UoM</th>
                                                        <th colspan="2">Discount</th>
                                                        <th rowspan="2">Amount after discount</th>
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
                                            @if(!empty($objSIMAT))
                                                @foreach($objSIMAT as $key => $row)

                                                @php 
    print_r($row->DEALERID_REF); 

                                                @endphp 
                                                <tr class="participantRow">
                                                    <td align="center" >
                                                    <input {{$InputStatus}} type="text" name={{"txtSC_popup_".$key}} id={{"txtSC_popup_".$key}} class="form-control" value="{{ $row->SCNO }}"   autocomplete="off"   readonly style="width:130px;"/></td>
                                                    <td hidden><input type="hidden" name={{"SCID_REF_".$key}} id={{"SCID_REF_".$key}} class="form-control" value="{{ $row->SCID_REF }}" autocomplete="off" /></td>

                                                    <td hidden><input type="hidden" name={{"DEALERID_REF_".$key}} id={{"DEALERID_REF_".$key}} class="form-control" value="" autocomplete="off" /></td>
                                                    <td hidden><input type="hidden" name={{"SCHEMEID_REF_".$key}} id={{"SCHEMEID_REF_".$key}} class="form-control" value="" autocomplete="off" /></td>

                                                    <td hidden><input type="hidden" name={{"SOID_".$key}} id={{"SOID_".$key}} class="form-control" value="{{ $row->SOID }}" autocomplete="off" /></td>
                                                    <td hidden><input type="hidden" name={{"SQID_REF_".$key}} id={{"SQID_REF_".$key}} class="form-control" value="{{ $row->SQID_REF }}" autocomplete="off" /></td>
                                                    <td hidden><input type="hidden" name={{"SEID_REF_".$key}} id={{"SEID_REF_".$key}} class="form-control" value="{{ $row->SEID_REF }}" autocomplete="off" /></td>

                                                    <td><input type="text" name={{"LEADNO_".$key}} id={{"LEADNO_".$key}}  class="form-control"  autocomplete="off"  readonly value="{{ $row->LEAD_NO }}"  style="width:130px;"/></td>
                                                    <td><input type="text" name={{"LEADDT_".$key}} id={{"LEADDT_".$key}}  class="form-control"  autocomplete="off"  readonly value="{{ $row->LEAD_DT }}"  style="width:130px;"/></td>



                                                    <td><input {{$InputStatus}} type="text" name={{"popupITEMID_".$key}} id={{"popupITEMID_".$key}} class="form-control" value="{{ $row->ICODE }}"  autocomplete="off"  readonly style="width:130px;"/></td>
                                                    
                                    

                                                    
                                                    <td hidden><input type="hidden" name={{"ITEMID_REF_".$key}} id={{"ITEMID_REF_".$key}} class="form-control" value="{{ $row->ITEMID_REF }}" autocomplete="off" /></td>
                                                    
                                                    <td><input {{$InputStatus}} type="text" name={{"ItemName_".$key}} id={{"ItemName_".$key}} class="form-control" value="{{ $row->ItemName }}" autocomplete="off"  readonly style="width:130px;"/></td>
                                                    
                                                    <td {{$AlpsStatus['hidden']}}><input type="text" name="Alpspartno_{{$key}}" id="Alpspartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->ALPS_PART_NO)?$row->ALPS_PART_NO:''}}" readonly style="width:130px;"/></td>
                                                    <td {{$AlpsStatus['hidden']}}><input type="text" name="Custpartno_{{$key}}" id="Custpartno_{{$key}}" class="form-control"  autocomplete="off" value="{{ isset($row->CUSTOMER_PART_NO)?$row->CUSTOMER_PART_NO:''}}" readonly style="width:130px;"/></td>
                                                    <td {{$AlpsStatus['hidden']}}><input type="text" name="OEMpartno_{{$key}}"  id="OEMpartno_{{$key}}" class="form-control"  autocomplete="off"  value="{{ isset($row->OEM_PART_NO)?$row->OEM_PART_NO:'' }}" readonly style="width:130px;"/></td>

                                                    
                                                    <td><input {{$InputStatus}} type="text" name={{"ITEMSPECI_".$key}} id={{"ITEMSPECI_".$key}} class="form-control" value="{{ $row->ITEMSPECI }}" autocomplete="off"  readonly style="width:130px;"/></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"SCMUOM_".$key}} id={{"SCMUOM_".$key}} class="form-control"  autocomplete="off" value="{{ $row->SCMUOM }}" readonly style="width:130px;"/></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"SCMUOMQTY_".$key}} id={{"SCMUOMQTY_".$key}} class="form-control three-digits" maxlength="13" value="{{ $row->SCMUOMQTY }}" autocomplete="off"  readonly style="width:130px;text-align:right;"/></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"SCAUOM_".$key}} id={{"SCAUOM_".$key}} class="form-control"  autocomplete="off" value="{{ $row->SCAUOM }}"  readonly style="width:130px;"/></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"SCAUOMQTY_".$key}} id={{"SCAUOMQTY_".$key}} class="form-control three-digits" value="{{ $row->SCAUOMQTY }}" maxlength="13" autocomplete="off"  readonly style="width:130px;text-align:right;"/></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"popupMUOM_".$key}} id={{"popupMUOM_".$key}} class="form-control"  autocomplete="off" value="{{ $row->popupMUOM }}" readonly style="width:130px;"/></td>
                                                    <td hidden><input type="text" name={{"MAIN_SIUOMID_REF_".$key}} id={{"MAIN_SIUOMID_REF_".$key}} class="form-control" value="{{ $row->MAIN_SIUOMID_REF }}"  autocomplete="off" /></td>
                                                    <td><input {{$InputStatus}} {{$AlpsStatus['readonly']}} type="text" name={{"SIMAIN_QTY_".$key}} id={{"SIMAIN_QTY_".$key}} class="form-control three-digits" maxlength="13" value="{{ $row->SIMAIN_QTY }}"  autocomplete="off" style="width:130px;text-align:right;" /></td>
                                                    <td hidden><input type="text" name={{"SC_FQTY_".$key}} id={{"SC_FQTY_".$key}} class="form-control three-digits" maxlength="13"  autocomplete="off" value="1"  readonly/></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"popupAUOM_".$key}} id={{"popupAUOM_".$key}} class="form-control"  autocomplete="off" value="{{ $row->popupAUOM }}"   readonly style="width:130px;"/></td>
                                                    <td hidden><input type="text" name={{"ALTUOMID_REF_".$key}} id={{"ALTUOMID_REF_".$key}} class="form-control" value="{{ $row->ALT_SIUOMID_REF }}"  autocomplete="off" /></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"ALT_UOMID_QTY_".$key}} id={{"ALT_UOMID_QTY_".$key}} class="form-control three-digits" maxlength="13" value="{{ $row->ALT_UOMID_QTY }}"  autocomplete="off"  readonly style="width:130px;text-align:right;"/></td>
                                                    <td><input {{$InputStatus}} {{$AlpsStatus['readonly']}} type="text" name={{"RATEPUOM_".$key}} id={{"RATEPUOM_".$key}} class="form-control five-digits" maxlength="13" value="{{ $row->RATEPUOM }}"  autocomplete="off" style="width:130px;text-align:right;" /></td>
                                                    <td><input {{$InputStatus}} {{$AlpsStatus['disabled']}} type="text" name={{"DISPER_".$key}} id={{"DISPER_".$key}} class="form-control four-digits" maxlength="8"  value="{{ $row->DISPER }}" autocomplete="off" style="width:130px;text-align:right;" /></td>
                                                    <td><input {{$InputStatus}} {{$AlpsStatus['disabled']}} type="text" name={{"DISCOUNT_AMT_".$key}} id={{"DISCOUNT_AMT_".$key}} class="form-control two-digits" value="{{ $row->DISCOUNT_AMT }}" maxlength="15"  autocomplete="off" style="width:130px;text-align:right;"  /></td>
                                                    @php
                                                        $TaxAmt=number_format((($row->SIMAIN_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT),2, '.', '');
                                                    @endphp
                                                    <td><input {{$InputStatus}} type="text" name={{"DISAFTT_AMT_".$key}} id={{"DISAFTT_AMT_".$key}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$TaxAmt}}"  readonly style="width:130px;text-align:right;"/></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"IGST_".$key}} id={{"IGST_".$key}} class="form-control four-digits" maxlength="8" value="{{ $row->IGST }}" autocomplete="off"  readonly style="width:130px;text-align:right;"/></td>
                                                    @php
                                                        $IGSTAMT=number_format((((($row->SIMAIN_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT)*$row->IGST)/100),2, '.', '');
                                                    @endphp
                                                    <td><input {{$InputStatus}} type="text" name={{"IGSTAMT_".$key}} id={{"IGSTAMT_".$key}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$IGSTAMT}}"  readonly style="width:130px;text-align:right;"/></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"CGST_".$key}} id={{"CGST_".$key}} class="form-control four-digits" maxlength="8" value="{{ $row->CGST }}" autocomplete="off"  readonly style="width:130px;text-align:right;"/></td>
                                                    @php
                                                        $CGSTAMT=number_format((((($row->SIMAIN_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT)*$row->CGST)/100),2, '.', '');
                                                    @endphp
                                                    <td><input {{$InputStatus}} type="text" name={{"CGSTAMT_".$key}} id={{"CGSTAMT_".$key}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$CGSTAMT}}" readonly style="width:130px;text-align:right;"/></td>
                                                    <td><input {{$InputStatus}} type="text" name={{"SGST_".$key}} id={{"SGST_".$key}} class="form-control four-digits" maxlength="8" value="{{ $row->SGST }}" autocomplete="off"  readonly style="width:130px;text-align:right;"/></td>
                                                    @php
                                                        $SGSTAMT=number_format((((($row->SIMAIN_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT)*$row->SGST)/100),2, '.', '');
                                                    @endphp
                                                    <td><input {{$InputStatus}} type="text" name={{"SGSTAMT_".$key}} id={{"SGSTAMT_".$key}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$SGSTAMT}}" readonly style="width:130px;text-align:right;"/></td>
                                                    @php
                                                        $IGSTAMT=number_format((((($row->SIMAIN_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT)*$row->IGST)/100),2, '.', '');
                                                        $CGSTAMT=number_format((((($row->SIMAIN_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT)*$row->CGST)/100),2, '.', '');
                                                        $SGSTAMT=number_format((((($row->SIMAIN_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT)*$row->SGST)/100),2, '.', '');
                                                        $TOTGST =number_format(($IGSTAMT+$CGSTAMT+$SGSTAMT),2, '.', '');
                                                    @endphp
                                                    <td><input {{$InputStatus}} type="text" name={{"TGST_AMT_".$key}} id={{"TGST_AMT_".$key}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$TOTGST}}" readonly style="width:130px;text-align:right;"/></td>
                                                    @php
                                                        $TaxAmt=number_format((($row->SIMAIN_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT),2, '.', '');
                                                        $IGSTAMT=number_format((((($row->SIMAIN_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT)*$row->IGST)/100),2, '.', '');
                                                        $CGSTAMT=number_format((((($row->SIMAIN_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT)*$row->CGST)/100),2, '.', '');
                                                        $SGSTAMT=number_format((((($row->SIMAIN_QTY*$row->RATEPUOM) - $row->DISCOUNT_AMT)*$row->SGST)/100),2, '.', '');
                                                        $TOTAMT =number_format(($TaxAmt+$IGSTAMT+$CGSTAMT+$SGSTAMT),2, '.', '');
                                                    @endphp
                                                    <td><input {{$InputStatus}} type="text" name={{"TOT_AMT_".$key}} id={{"TOT_AMT_".$key}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$TOTAMT}}" readonly style="width:130px;text-align:right;"/></td>
                                                    <td align="center" >
                                                      <button {{$InputStatus}} class="btn add material" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                                                    <button {{$InputStatus}} class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                                                  </td>
                                                </tr>
                                                @endforeach
                                                @endif 
                                                <tr></tr>
                                            </tbody>
                                             <tr  class="participantRowFotter">
                                                <td colspan="6" style="text-align:center;font-weight:bold;">TOTAL</td>    
                                                <td {{$AlpsStatus['hidden']}} ></td>
                                                <td {{$AlpsStatus['hidden']}} ></td>
                                                <td {{$AlpsStatus['hidden']}} ></td>                               
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td id="SIMAIN_QTY_total"   style="text-align:right;font-weight:bold;"></td>
                                                <td></td>
                                                <td id="ALT_UOMID_QTY_total"   style="text-align:right;font-weight:bold;"></td>
                                                <td id="RATEPUOM_total"       style="text-align:right;font-weight:bold;"></td>
                                                <td></td>
                                                <td id="DISCOUNT_AMT_total"   style="text-align:right;font-weight:bold;"></td>
                                                <td id="DISAFTT_AMT_total"    style="text-align:right;font-weight:bold;"></td>
                                                <td></td>
                                                <td id="IGSTAMT_total"        style="text-align:right;font-weight:bold;"></td>
                                                <td></td>
                                                <td id="CGSTAMT_total"        style="text-align:right;font-weight:bold;"></td>
                                                <td></td>
                                                <td id="SGSTAMT_total"        style="text-align:right;font-weight:bold;"></td>
                                                <td id="TGST_AMT_total"       style="text-align:right;font-weight:bold;"></td>
                                                <td id="TOT_AMT_total"        style="text-align:right;font-weight:bold;"></td>
                                                <td></td>                                                
                                          </tr>
                                    </table>
                                    </div>	
                                </div>
                                <div id="TC" class="tab-pane fade">
                                    <div class="row" style="margin-top:10px;margin-left:3px;" >	
                                        <div class="col-lg-1 pl"><p>T&C Template</p></div>
                                        <div class="col-lg-2 pl">
                                        <input {{$InputStatus}} type="text" name="txtTNCID_popup" id="txtTNCID_popup" class="form-control"  autocomplete="off"  readonly/>
                                        @if(!empty($objSITNC))
                                         <input type="hidden" name="TNCID_REF" id="TNCID_REF" class="form-control" value="{{$objSITNC[0]->TNCID_REF}}" autocomplete="off" />
                                         @else
                                         <input type="hidden" name="TNCID_REF" id="TNCID_REF" class="form-control"  autocomplete="off" />
                                         @endif 
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:240px;width:50%;">
                                        
                                        <table id="example3" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                            <tr >
                                                <th>Terms & Conditions Description<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2"></th>
                                                <th>Value / Comment</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody id="tncbody">
                                            @if(!empty($objSITNC))
                                                @foreach($objSITNC as $Tkey => $Trow)
                                                    <tr  class="participantRow3">
                                                    <td><input {{$InputStatus}} type="text" name={{"popupTNCDID_".$Tkey}} id={{"popupTNCDID_".$Tkey}} class="form-control"  autocomplete="off"  readonly/></td>
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
                                                    <td><input {{$InputStatus}} type="text" name="popupTNCDID_0" id="popupTNCDID_0" class="form-control"  autocomplete="off"  readonly/></td>
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
                                <div id="udf" class="tab-pane fade">
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
                                        <table id="example4" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                            <tr >
                                                <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3"></th>
                                                <th>Value / Comments</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(!empty($objSIUDF))
                                            @foreach($objSIUDF as $Ukey => $Urow)
                                                <tr  class="participantRow4">
                                                    <td><input {{$InputStatus}} type="text" name={{"popupUDFSIID_".$Ukey}} id={{"popupUDFSIID_".$Ukey}}  class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name={{"UDFSIID_REF_".$Ukey}}  id={{"UDFSIID_REF_".$Ukey}} class="form-control" value="{{$Urow->SIUDFID_REF}}" autocomplete="off" /></td>
                                                    <td hidden><input type="hidden" name={{"UDFismandatory_".$Ukey}} id={{"UDFismandatory_".$Ukey}} class="form-control" autocomplete="off" /></td>
                                                    <td id={{"udfinputid_".$Ukey}}>
                                                    {{-- dynamic input --}} 
                                                    </td>
                                                    <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                </tr>
                                                <tr></tr>
                                            @endforeach 
                                            @else
                                            @foreach($objUdfSIData as $uindex=>$uRow)
                                              <tr  class="participantRow4">
                                                  <td><input {{$InputStatus}} type="text" name={{"popupUDFSIID_".$uindex}} id={{"popupUDFSIID_".$uindex}} class="form-control" value="{{$uRow->LABEL}}" autocomplete="off"  readonly/></td>
                                                  <td hidden><input type="hidden" name={{"SIUDFID_REF_".$uindex}} id={{"SIUDFID_REF_".$uindex}} class="form-control" value="{{$uRow->UDF_SIID}}" autocomplete="off"   /></td>
                                                  <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->ISMANDATORY}}" class="form-control"   autocomplete="off" /></td>
                                                  <td id={{"udfinputid_".$uindex}} >
                                                  </td>
                                                  <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button>
                                                  <button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                              </tr>
                                              <tr></tr>
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
                                        <input {{$InputStatus}} type="text" name="txtCTID_popup" id="txtCTID_popup" class="form-control"  autocomplete="off"  readonly/>
                                        @if(!empty($objSICAL))
                                         <input type="hidden" name="CTID_REF" id="CTID_REF" class="form-control" value="{{$objSICAL[0]->CTID_REF}}" autocomplete="off" />
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
                                                    <th>As per Actual</th>
                                                    <th width="8%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_ctid">
                                            @if(!empty($objSICAL))
                                                @foreach($objSICAL as $Ckey => $Crow)
                                                    <tr  class="participantRow5">
                                                        <td><input {{$InputStatus}} type="text" name={{"popupTID_".$Ckey}} id={{"popupTID_".$Ckey}}  class="form-control"  autocomplete="off"  readonly/></td>
                                                        <td hidden><input type="hidden" name={{"TID_REF_".$Ckey}}  id={{"TID_REF_".$Ckey}}  class="form-control" autocomplete="off" value="{{$Crow->TID_REF}}" /></td>
                                                        <td><input {{$InputStatus}} type="text" name={{"RATE_".$Ckey}}  id={{"RATE_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->RATE}}"  readonly/></td>
                                                        <td hidden><input type="hidden" name={{"BASIS_".$Ckey}} id={{"BASIS_".$Ckey}} class="form-control" autocomplete="off"  /></td>
                                                        <td hidden><input type="hidden" name={{"SQNO_".$Ckey}} id={{"SQNO_".$Ckey}} class="form-control" autocomplete="off" /></td>
                                                        <td hidden><input type="hidden" name={{"FORMULA_".$Ckey}} id={{"FORMULA_".$Ckey}} class="form-control" autocomplete="off" /></td>
                                                        <td><input {{$InputStatus}} type="text" name={{"VALUE_".$Ckey}} id={{"VALUE_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$Crow->VALUE}}" readonly/></td>
                                                        <td style="text-align:center;" ><input {{$InputStatus}} type="checkbox" class="filter-none" name={{"calGST_".$Ckey}} id={{"calGST_".$Ckey}} {{$Crow->GST == 1 ? 'checked' : ''}}   ></td>
                                                        
                                                        <td><input {{$InputStatus}} type="text" name={{"calIGST_".$Ckey}} id={{"calIGST_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->IGST}}" readonly/></td>
                                                        <td><input {{$InputStatus}} type="text" name={{"AMTIGST_".$Ckey}} id={{"AMTIGST_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td><input {{$InputStatus}} type="text" name={{"calCGST_".$Ckey}} id={{"calCGST_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->CGST}}" readonly/></td>
                                                        <td><input {{$InputStatus}} type="text" name={{"AMTCGST_".$Ckey}} id={{"AMTCGST_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td><input {{$InputStatus}} type="text" name={{"calSGST_".$Ckey}} id={{"calSGST_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->SGST}}" readonly/></td>
                                                        <td><input {{$InputStatus}} type="text" name={{"AMTSGST_".$Ckey}} id={{"AMTSGST_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td><input {{$InputStatus}} type="text" name={{"TOTGSTAMT_".$Ckey}} id={{"TOTGSTAMT_".$Ckey}} class="form-control two-digits"  maxlength="15" autocomplete="off"  readonly/></td>
                                                        <td style="text-align:center;"><input {{$InputStatus}} type="checkbox" class="filter-none" name={{"calACTUAL_".$Ckey}} id={{"calACTUAL_".$Ckey}} value="" {{$Crow->ACTUAL == 1 ? 'checked' : ''}}  ></td>
                                                        <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                    </tr>
                                                    <tr></tr>
                                                @endforeach 
                                                @else
                                                <tr  class="participantRow5">
                                                    <td><input {{$InputStatus}} type="text" name="popupTID_0" id="popupTID_0" class="form-control"  autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name="TID_REF_0" id="TID_REF_0" class="form-control" autocomplete="off" /></td>
                                                    <td><input {{$InputStatus}} type="text" name="RATE_0" id="RATE_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                    <td hidden><input type="hidden" name="BASIS_0" id="BASIS_0" class="form-control" autocomplete="off" /></td>
                                                    <td hidden><input type="hidden" name="SQNO_0" id="SQNO_0" class="form-control" autocomplete="off" /></td>
                                                    <td><input {{$InputStatus}} type="text" name="VALUE_0" id="VALUE_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td style="text-align:center;" ><input type="checkbox" class="filter-none" name="calGST_0" id="calGST_0" value="" ></td>
                                                    <td><input {{$InputStatus}} type="text" name="calIGST_0" id="calIGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                    <td><input {{$InputStatus}} type="text" name="AMTIGST_0" id="AMTIGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td><input {{$InputStatus}} type="text" name="calCGST_0" id="calCGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                    <td><input {{$InputStatus}} type="text" name="AMTCGST_0" id="AMTCGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td><input {{$InputStatus}} type="text" name="calSGST_0" id="calSGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                                                    <td><input  {{$InputStatus}} type="text" name="AMTSGST_0" id="AMTSGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td><input {{$InputStatus}} type="text" name="TOTGSTAMT_0" id="TOTGSTAMT_0" class="form-control two-digits"  maxlength="15" autocomplete="off"  readonly/></td>
                                                    <td style="text-align:center;"><input {{$InputStatus}} type="checkbox" class="filter-none" name="calACTUAL_0" id="calACTUAL_0" value=""   ></td>
                                                    <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                                                </tr>
                                                <tr></tr>
                                            @endif 
                                            </tbody>
                                    </table>
                                    </div>	
                                </div>
                                
                                
                                <div id="PaymentSlabs" class="tab-pane fade">
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:65%;">
                                        <table id="example6" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                            <tr >
                                                <th>Day(s)<input class="form-control" type="hidden" name="Row_Count5" id ="Row_Count5"></th>
                                                <th>Due %</th>
                                                <th>Remarks</th>
                                                <th>Due Date</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(!empty($objSIPSLB))
                                                @foreach($objSIPSLB as $Pkey => $Prow)
                                                    <tr  class="participantRow6">
                                                        <td> <input {{$InputStatus}} type="text" class="form-control" id={{"PAY_DAYS_".$Pkey}} name={{"PAY_DAYS_".$Pkey}} value="{{$Prow->PAY_DAYS}}" autocomplete="off"  /> </td>
                                                        <td> <input {{$InputStatus}} type="text" class="form-control four-digits" id={{"DUE_PERCENT_".$Pkey}} name={{"DUE_PERCENT_".$Pkey}} value="{{$Prow->DUE}}" maxlength="8" autocomplete="off" /> </td>
                                                        <td> <input {{$InputStatus}} type="text" class="form-control" id={{"DUE_AMT_".$Pkey}} name={{"DUE_AMT_".$Pkey}} value="{{$Prow->DUE_AMT}}" autocomplete="off" readonly /> </td>
                                                        <td> <input {{$InputStatus}} type="date" class="form-control" id={{"DUE_DATE_".$Pkey}} name={{"DUE_DATE_".$Pkey}} value="{{$Prow->DUE_DATE}}" autocomplete="off"  readonly /> </td>
                                                        <td align="center" ><button {{$InputStatus}} class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button {{$InputStatus}} class="btn remove" title="Delete" data-toggle="tooltip" type="button" ><i class="fa fa-trash" ></i></button></td>
                                                    </tr>
                                                    <tr></tr>
                                                @endforeach 
                                                @else
                                                <tr  class="participantRow6">
                                                        <td> <input {{$InputStatus}} type="text" class="form-control" id="PAY_DAYS_0" name="PAY_DAYS_0"  autocomplete="off" /> </td>
                                                        <td> <input {{$InputStatus}} type="text" class="form-control four-digits" id="DUE_PERCENT_0" name="DUE_PERCENT_0"  maxlength="8" autocomplete="off" /> </td>
                                                        <td> <input {{$InputStatus}} type="text" class="form-control" id="DUE_AMT_0" name="DUE_AMT_0" autocomplete="off"  readonly /> </td>
                                                        <td> <input {{$InputStatus}} type="date" class="form-control" id="DUE_DATE_0" name="DUE_DATE_0" autocomplete="off"  readonly /> </td>
                                                        <td align="center" ><button {{$InputStatus}} class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button  {{$InputStatus}} class="btn remove" title="Delete" data-toggle="tooltip" type="button" ><i class="fa fa-trash" ></i></button></td>
                                                    </tr>
                                                <tr></tr>
                                            @endif 
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div id="TDS" class="tab-pane fade">
            <div class="row" style="margin-top:10px;margin-left:3px;" >	
                <div class="col-lg-1 pl"><p>TDS Applicable</p></div>
                <div class="col-lg-2 pl">
                  <select {{$InputStatus}} name="drpTDS" id="drpTDS" class="form-control">
                      <option value=""></option>    
                      <option {{ isset($objSI->TDS) && $objSI->TDS =='1'?'selected="selected"':'' }} value="Yes">Yes</option>
                      <option {{ isset($objSI->TDS) && $objSI->TDS =='0'?'selected="selected"':'' }} value="No">No</option>
                  </select>
                </div>
            </div>
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                <table id="example7" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                          <tr>
                              <th width="8%">TDS<input class="form-control" type="hidden" name="Row_Count4" id ="Row_Count4"  value="{{$objCount6}}" /></th>
                              <th width="8%">TDS Ledger</th>
                              <th width="5%">Applicable</th>
                              <th width="8%">Assessable Value</th>
                              <th width="5%">TDS Rate</th>
                              <th width="8%">TDS Amount</th>
                              <th width="8%">Assessable Value</th>
                              <th width="5%">Surcharge Rate</th>
                              <th width="8%">Surcharge Amount</th>
                              <th width="8%">Assessable Value</th>
                              <th width="5%">Cess Rate</th>
                              <th width="8%">Cess Amount</th>
                              <th width="8%">Assessable Value</th>
                              <th width="5%">Special Cess Rate </th>
                              <th width="8%">Special Cess Amount</th>
                              <th width="8%">Total TDS Amount</th>                         
                              <th width="8%">Action</th>
                          </tr>
                    </thead>
                    <tbody id="tbody_tds">
                    @if(!empty($objSITDS))
                      @foreach($objSITDS as $tkey => $trow)
                      <tr  class="participantRow7">
                          <td style="text-align:center;" >
                          <input {{$InputStatus}} type="text" {{$InputStatus}} name={{"txtTDS_".$tkey}} id={{"txtTDS_".$tkey}} class="form-control" value="{{$trow->CODE}}" autocomplete="off"  readonly/></td>
                          <td hidden><input type="hidden" name={{"TDSID_REF_".$tkey}} id={{"TDSID_REF_".$tkey}} value="{{$trow->TDSID_REF}}" class="form-control" autocomplete="off" /></td>
                          <td><input {{$InputStatus}} type="text"  name={{"TDSLedger_".$tkey}} id={{"TDSLedger_".$tkey}} class="form-control" value="{{$trow->CODE_DESC}}" autocomplete="off"  readonly/></td>
                          <td  align="center" style="text-align:center;" ><input {{$InputStatus}} type="checkbox" name={{"TDSApplicable_".$tkey}} id={{"TDSApplicable_".$tkey}} {{isset($trow->TDS_APPLICABLE) && $trow->TDS_APPLICABLE == 1?'checked' : ''}}/></td>
                          <td><input {{$InputStatus}} type="text" name={{"ASSESSABLE_VL_TDS_".$tkey}} id={{"ASSESSABLE_VL_TDS_".$tkey}} class="form-control two-digits" value="{{$trow->ASSESSABLE_VL_TDS}}" maxlength="15"  autocomplete="off"  /></td>
                          <td><input {{$InputStatus}} type="text" name={{"TDS_RATE_".$tkey}} id={{"TDS_RATE_".$tkey}} class="form-control four-digits" maxlength="12" value="{{$trow->TDS_RATE}}" autocomplete="off"  /></td>
                          <td hidden><input type="hidden" name={{"TDS_EXEMPT_".$tkey}} id={{"TDS_EXEMPT_".$tkey}} class="form-control two-digits" value="0.00" /></td>
                          <td><input {{$InputStatus}} type="text" name={{"TDS_AMT_".$tkey}} id={{"TDS_AMT_".$tkey}} class="form-control two-digits" maxlength="15"  autocomplete="off"  value="{{$trow->TDS_AMT}}" readonly/></td>
                          <td><input {{$InputStatus}} type="text" name={{"ASSESSABLE_VL_SURCHARGE_".$tkey}} id={{"ASSESSABLE_VL_SURCHARGE_".$tkey}} class="form-control two-digits" value="{{$trow->ASSESSABLE_VL_SURCHARGE}}" maxlength="15"  autocomplete="off" /></td>
                          <td><input {{$InputStatus}} type="text" name={{"SURCHARGE_RATE_".$tkey}} id={{"SURCHARGE_RATE_".$tkey}} class="form-control four-digits" maxlength="12" value="{{$trow->SURCHARGE_RATE}}"  autocomplete="off"  /></td>
                          <td hidden><input type="hidden" name={{"SURCHARGE_EXEMPT_".$tkey}} id={{"SURCHARGE_EXEMPT_".$tkey}} class="form-control two-digits" value="0.00" /></td>
                          <td><input {{$InputStatus}} type="text" name={{"SURCHARGE_AMT_".$tkey}} id={{"SURCHARGE_AMT_".$tkey}} class="form-control two-digits" maxlength="15" value="{{$trow->SURCHARGE_AMT}}" autocomplete="off"  readonly/></td>
                          <td><input {{$InputStatus}} type="text" name={{"ASSESSABLE_VL_CESS_".$tkey}} id={{"ASSESSABLE_VL_CESS_".$tkey}} class="form-control two-digits" maxlength="15" value="{{$trow->ASSESSABLE_VL_CESS}}" autocomplete="off" /></td>
                          <td><input {{$InputStatus}} type="text" name={{"CESS_RATE_".$tkey}} id={{"CESS_RATE_".$tkey}} class="form-control four-digits" maxlength="12" value="{{$trow->CESS_RATE}}" autocomplete="off"  /></td>
                          <td hidden><input type="hidden" name={{"CESS_EXEMPT_".$tkey}} id={{"CESS_EXEMPT_".$tkey}} class="form-control two-digits" value="0.00" /></td>
                          <td><input {{$InputStatus}} type="text" name={{"CESS_AMT_".$tkey}} id={{"CESS_AMT_".$tkey}} class="form-control two-digits" maxlength="15" value="{{$trow->CESS_AMT}}" autocomplete="off"  readonly/></td>
                          <td><input {{$InputStatus}} type="text" name={{"ASSESSABLE_VL_SPCESS_".$tkey}} id={{"ASSESSABLE_VL_SPCESS_".$tkey}} class="form-control two-digits" maxlength="15" value="{{$trow->ASSESSABLE_VL_SPCESS}}"  autocomplete="off" /></td>
                          <td><input {{$InputStatus}} type="text" name={{"SPCESS_RATE_".$tkey}} id={{"SPCESS_RATE_".$tkey}} class="form-control four-digits" maxlength="12" value="{{$trow->SPCESS_RATE}}" autocomplete="off"  /></td>
                          <td hidden><input type="hidden" name={{"SPCESS_EXEMPT_".$tkey}} id={{"SPCESS_EXEMPT_".$tkey}} class="form-control two-digits" value="0.00" /></td>
                          <td><input {{$InputStatus}} type="text" name={{"SPCESS_AMT_".$tkey}} id={{"SPCESS_AMT_".$tkey}} class="form-control two-digits" maxlength="15" value="{{$trow->SPCESS_AMT}}"  autocomplete="off"  readonly/></td>
                  @php
                    $TotalTDS=number_format(($trow->TDS_AMT+$trow->SURCHARGE_AMT+$trow->CESS_AMT+$trow->SPCESS_AMT),2, '.', '');
                  @endphp
                            <td><input {{$InputStatus}} type="text" name={{"TOT_TD_AMT_".$tkey}} id={{"TOT_TD_AMT_".$tkey}} class="form-control two-digits" maxlength="15" value="{{$TotalTDS}}"  autocomplete="off"  readonly/></td>
                            <td style="min-width: 100px;" >
                            <button {{$InputStatus}} class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                            <button {{$InputStatus}} class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                          </td>
                        </tr>
                        <tr></tr>
                      @endforeach
                    @else
                        <tr  class="participantRow7">
                            <td style="text-align:center;" >
                            <input {{$InputStatus}} type="text" name="txtTDS_0" id="txtTDS_0" class="form-control"  autocomplete="off"  readonly/></td>
                            <td hidden><input type="hidden" name="TDSID_REF_0" id="TDSID_REF_0" class="form-control" autocomplete="off" /></td>
                            <td><input {{$InputStatus}} type="text" name="TDSLedger_0" id="TDSLedger_0" class="form-control"  autocomplete="off"  readonly/></td>
                            <td  align="center" style="text-align:center;" ><input {{$InputStatus}} type="checkbox" name="TDSApplicable_0" id="TDSApplicable_0" /></td>
                            <td><input {{$InputStatus}} type="text" name="ASSESSABLE_VL_TDS_0" id="ASSESSABLE_VL_TDS_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  /></td>
                            <td><input {{$InputStatus}} type="text" name="TDS_RATE_0" id="TDS_RATE_0" class="form-control four-digits" maxlength="12"  autocomplete="off"  /></td>
                            <td hidden><input type="hidden" name="TDS_EXEMPT_0" id="TDS_EXEMPT_0" class="form-control two-digits" /></td>
                            <td><input {{$InputStatus}} type="text" name="TDS_AMT_0" id="TDS_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                            <td><input {{$InputStatus}} type="text" name="ASSESSABLE_VL_SURCHARGE_0" id="ASSESSABLE_VL_SURCHARGE_0" class="form-control two-digits" maxlength="15"  autocomplete="off" /></td>
                            <td><input {{$InputStatus}} type="text" name="SURCHARGE_RATE_0" id="SURCHARGE_RATE_0" class="form-control four-digits" maxlength="12"  autocomplete="off"  /></td>
                            <td hidden><input type="hidden" name="SURCHARGE_EXEMPT_0" id="SURCHARGE_EXEMPT_0" class="form-control two-digits" /></td>
                            <td><input {{$InputStatus}} type="text" name="SURCHARGE_AMT_0" id="SURCHARGE_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                            <td><input {{$InputStatus}} type="text" name="ASSESSABLE_VL_CESS_0" id="ASSESSABLE_VL_CESS_0" class="form-control two-digits" maxlength="15"  autocomplete="off" /></td>
                            <td><input {{$InputStatus}} type="text" name="CESS_RATE_0" id="CESS_RATE_0" class="form-control four-digits" maxlength="12"  autocomplete="off"  /></td>
                            <td hidden><input type="hidden" name="CESS_EXEMPT_0" id="CESS_EXEMPT_0" class="form-control two-digits" /></td>
                            <td><input {{$InputStatus}} type="text" name="CESS_AMT_0" id="CESS_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                            <td><input {{$InputStatus}} type="text" name="ASSESSABLE_VL_SPCESS_0" id="ASSESSABLE_VL_SPCESS_0" class="form-control two-digits" maxlength="15"  autocomplete="off" /></td>
                            <td><input {{$InputStatus}} type="text" name="SPCESS_RATE_0" id="SPCESS_RATE_0" class="form-control four-digits" maxlength="12"  autocomplete="off"  /></td>
                            <td hidden><input type="hidden" name="SPCESS_EXEMPT_0" id="SPCESS_EXEMPT_0" class="form-control two-digits" /></td>
                            <td><input {{$InputStatus}} type="text" name="SPCESS_AMT_0" id="SPCESS_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                            <td><input {{$InputStatus}} type="text" name="TOT_TD_AMT_0" id="TOT_TD_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                            <td style="min-width: 100px;" >
                            <button {{$InputStatus}} class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                            <button {{$InputStatus}} class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                          </td>
                        </tr>
                        <tr></tr>
                    @endif
                    </tbody>
                </table>
            </div>	
        </div> 

        <div id="ADDITIONAL" class="tab-pane fade">
                                    <div class="row" style="margin-top:10px;margin-left:3px;" >	
                                        <div class="col-lg-2 pl"><p>Template Master</p></div>
                                        <div class="col-lg-2 pl">
                                        <input type="text" name="txtTemplate_popup" {{$InputStatus}} id="txtTemplate_popup" class="form-control"  autocomplete="off"  readonly/>
                                         <input type="hidden" name="TEMPID_REF" id="TEMPID_REF" class="form-control" autocomplete="off" />
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top:10px;margin-left:3px;" >	
                                        <div class="col-lg-2 pl"><p>Template Description</p></div>
                                        <div class="col-lg-6 pl">
                             
                                        <textarea name="Template_Description" {{$InputStatus}} id="Template_Description" cols="118" rows="10" tabindex="3" >{{ isset($Template->TEMPLATE)?$Template->TEMPLATE:'' }}</textarea>
                                        </div>
                                    </div>            
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
    </div>

@endsection
@section('alert')

<!-- Currency Dropdown -->
<div id="cridpopup" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md column3_modal">
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
      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Description</th>
    </tr>
    </thead>
    <tbody>
      <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="currencycodesearch" class="form-control" onkeyup="CurrencyCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="currencynamesearch" class="form-control" onkeyup="CurrencyNameFunction()"></td>
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
        <tr>
          <td class="ROW1"> <input type="checkbox" name="SELECT_CRID[]" id="cridcode_{{ $crindex }}" class="clscrid" value="{{ $crRow-> CRID }}" ></td>
          <td class="ROW2">{{ $crRow-> CRCODE }}
            <input type="hidden" id="txtcridcode_{{ $crindex }}" data-desc="{{ $crRow-> CRCODE }}" data-desc2="{{ $crRow-> CRDESCRIPTION }}"  value="{{ $crRow-> CRID }}"/>
          </td>
          <td class="ROW3">{{ $crRow-> CRDESCRIPTION }}</td>
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
      <table id="BillToTable" class="display nowrap table  table-striped table-bordered">
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
          <td class="ROW2"><input type="text" id="BillTocodesearch" class="form-control" onkeyup="BillToCodeFunction()"></td>
          <td class="ROW3"><input type="text" id="BillTonamesearch" class="form-control" onkeyup="BillToNameFunction()"></td>
        </tr>
      </tbody>
      </table>
        <table id="BillToTable2" class="display nowrap table  table-striped table-bordered">
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
      <table id="ShipToTable" class="display nowrap table  table-striped table-bordered">
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
          <td class="ROW2"><input type="text" id="ShipTocodesearch" class="form-control" onkeyup="ShipToCodeFunction()"></td>
          <td class="ROW3"><input type="text" id="ShipTonamesearch" class="form-control" onkeyup="ShipToNameFunction()"></td>
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
      <div class="tablename"><p>T&C</p></div>
      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
      <table id="TNCIDTable" class="display nowrap table  table-striped table-bordered">
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
        <table id="TNCIDTable2" class="display nowrap table  table-striped table-bordered">
          <thead id="thead2">
          
          </thead>
          <tbody>
          @foreach ($objTNCHeader as $tncindex=>$tncRow)
          <tr>
                <td class="ROW1"> <input type="checkbox" name="SELECT_TNCID[]" id="tncidcode_{{ $tncindex }}" class="clstncid" value="{{ $tncRow-> TNCID }}" ></td>
                <td class="ROW2">{{ $tncRow-> TNC_CODE }}
                  <input type="hidden" id="txttncidcode_{{ $tncindex }}" data-desc="{{ $tncRow-> TNC_CODE }}" data-desc2="{{ $tncRow-> TNC_DESC }}"  value="{{ $tncRow-> TNCID }}"/>
                </td>
                <td class="ROW3">{{ $tncRow-> TNC_DESC }}</td>
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
      <table id="TNCDetTable" class="display nowrap table  table-striped table-bordered">
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
        <table id="TNCDetTable2" class="display nowrap table  table-striped table-bordered" >
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
      <table id="CTIDTable" class="display nowrap table  table-striped table-bordered">
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
        <table id="CTIDTable2" class="display nowrap table  table-striped table-bordered">
          <thead id="thead2">
          
          </thead>
          <tbody>
          @foreach ($objCalculationHeader as $calindex=>$calRow)
          <tr>
              <td class="ROW1"> <input type="checkbox" name="SELECT_CTID[]" id="CTIDcode_{{ $calindex }}" class="clsctid" value="{{ $calRow-> CTID }}" ></td>
              <td class="ROW2">{{ $calRow-> CTCODE }}
                <input type="hidden" id="txtCTIDcode_{{ $calindex }}" data-desc="{{ $calRow-> CTCODE }}" data-desc2="{{ $calRow-> CTDESCRIPTION }}"  value="{{ $calRow-> CTID }}"/>
              </td>
              <td class="ROW3">{{ $calRow-> CTDESCRIPTION }}</td>
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
      <input type="text" id="CTIDdetcodesearch" onkeyup="CTIDDetCodeFunction()">
      </td>
      <td>
      <input type="text" id="CTIDdetnamesearch" onkeyup="CTIDDetNameFunction()">
      </td>
      <td>
      <input type="text" id="CTIDdetratesearch" onkeyup="CTIDDetRateFunction()">
      </td>
      <td>
      <input type="text" id="CTIDdetamountsearch" onkeyup="CTIDDetAmountFunction()">
      </td>
      <td>
      <input type="text" id="CTIDdetformulasearch" onkeyup="CTIDDetFormulaFunction()">
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

<!-- Customer  Dropdown -->
<div id="customer_popus" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='customer_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Customer</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="GlCodeTable" class="display nowrap table  table-striped table-bordered">
    <thead>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Description</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="ROW1"><span class="check_th">&#10004;</span></td>
        <td class="ROW2"><input type="text" id="customercodesearch" class="form-control" onkeyup="CustomerCodeFunction('{{$FormId}}')"></td>
        <td class="ROW3"><input type="text" id="customernamesearch" class="form-control" onkeyup="CustomerNameFunction('{{$FormId}}')"></td>
    </tr>
    </tbody>
    </table>
      <table id="GlCodeTable2" class="display nowrap table  table-striped table-bordered">
        <thead id="thead2">
        </thead>
        <tbody id="tbody_subglacct">
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- CUSTOMER Dropdown-->

<!-- Sales Challan Dropdown -->
  <div id="SCpopup" class="modal" role="dialog"  data-backdrop="static">
    <div class="modal-dialog modal-md column3_modal">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" id='SC_closePopup' >&times;</button>
        </div>
      <div class="modal-body">
      <div class="tablename"><p>Sales Challan</p></div>
      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
      <table id="SalesChallanTable" class="display nowrap table  table-striped table-bordered">
      <thead>
            <tr id="none-select" class="searchalldata" hidden>
              
              <td> <input type="hidden" name="fieldid" id="hdn_scid"/>
              <input type="hidden" name="fieldid2" id="hdn_scid2"/></td>
            </tr>
      <tr>
        <th class="ROW1">Select</th> 
        <th class="ROW2">Challan No</th>
        <th class="ROW3">Challan Date</th>
      </tr>
      </thead>
      <tbody>
      <tr>
          <th class="ROW1"><span class="check_th">&#10004;</span></th>
          <td class="ROW2"><input type="text" id="SalesChallancodesearch" class="form-control" onkeyup="SalesChallanCodeFunction()"></td>
          <td class="ROW3"><input type="text" id="SalesChallannamesearch" class="form-control" onkeyup="SalesChallanNameFunction()"></td>
        </tr>
      </tbody>
      </table>
        <table id="SalesChallanTable2" class="display nowrap table  table-striped table-bordered">
          <thead id="thead2">

          </thead>
          <tbody id="tbody_SC">     
          
          </tbody>
        </table>
      </div>
      <div class="cl"></div>
        </div>
      </div>
    </div>
  </div>
<!-- Sales Challan Dropdown-->
<!-- Item Code Dropdown -->
  <div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
    <div class="modal-dialog modal-md" style="width:90%;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button>
        </div>
      <div class="modal-body">
      <div class="tablename"><p>Item Details</p></div>
      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
      <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
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
              <input type="hidden" name="hdn_ItemID21" id="hdn_ItemID21" value="0"/>
              <input type="hidden" name="hdn_ItemID22" id="hdn_ItemID22" value="0"/>
              <input type="hidden" name="hdn_ItemID23" id="hdn_ItemID23" value="0"/>
              <input type="hidden" name="hdn_ItemID24" id="hdn_ItemID24" value="0"/>
              </td>
        </tr>
        <!-- <tr class="topnav"><button class="btn topnavbt" id="btnOk"><i class="fa fa-check"></i> OK</button></tr> -->
        <tr>
              <th style="width:8%;text-align:center;" id="all-check">Select</th>
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
          <td style="width:8%;text-align:center;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
          <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="ItemCodeFunction()"></td>
          <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="ItemNameFunction()"></td>
          <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control"  autocomplete="off" onkeyup="ItemUOMFunction()"></td>
          <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control"  autocomplete="off" onkeyup="ItemQTYFunction()"></td>
          <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="ItemGroupFunction()"></td>
          <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="ItemCategoryFunction()"></td>
          <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="ItemBUFunction()"></td>
          <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" autocomplete="off" onkeyup="ItemAPNFunction()"></td>
          <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" autocomplete="off" onkeyup="ItemCPNFunction()"></td>
          <td style="width:8%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" autocomplete="off" onkeyup="ItemOEMPNFunction()"></td>
          <td style="width:8%;"><input type="text" id="ItemStatussearch" class="form-control" autocomplete="off" onkeyup="ItemStatusFunction()"></td>
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
    <div class="modal-dialog modal-md column3_modal">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" id='altuom_closePopup' >&times;</button>
        </div>
      <div class="modal-body">
      <div class="tablename"><p>ALT UOM</p></div>
      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
      <table id="altuomTable" class="display nowrap table  table-striped table-bordered">
      <thead>
      <tr id="none-select" class="searchalldata" hidden>
              
              <td> <input type="hidden" name="fieldid" id="hdn_altuom"/>
              <input type="hidden" name="fieldid2" id="hdn_altuom2"/>
              <input type="hidden" name="fieldid3" id="hdn_altuom3"/>
              <input type="hidden" name="fieldid4" id="hdn_altuom4"/></td>
            </tr>
      <tr>
        <th class="ROW1">Select</th> 
        <th class="ROW2">UOM Code</th>
        <th class="ROW3">UOM Desc</th>
      </tr>
      </thead>
      <tbody>
      <tr>
          <th class="ROW1"><span class="check_th">&#10004;</span></th>
          <td class="ROW2"><input type="text" id="altuomcodesearch" class="form-control" onkeyup="altuomCodeFunction()"></td>
          <td class="ROW3"><input type="text" id="altuomnamesearch" class="form-control" onkeyup="altuomNameFunction()"></td>
      </tr>
      </tbody>
      </table>
        <table id="altuomTable2" class="display nowrap table  table-striped table-bordered">
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

<!-- UDF Dropdown -->
  <div id="udfsoidpopup" class="modal" role="dialog"  data-backdrop="static">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" id='udfsoid_closePopup' >&times;</button>
        </div>
      <div class="modal-body">
      <div class="tablename"><p>UDF Details</p></div>
      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
      <table id="UDFSOIDTable" class="display nowrap table  table-striped table-bordered" width="100%">
      <thead>
      <tr>
              <th>Label</th>
              <th>Value Type</th>
      </tr>
      <tr hidden>
              <input type="hidden" name="fieldid" id="hdn_UDFSOID"/>
      </tr>
      </thead>
      <tbody>
      <tr>
      <td>
      <input type="text" id="UDFSOIDcodesearch" onkeyup="UDFSOIDCodeFunction()">
      </td>
      <td>
      <input type="text" id="UDFSOIDnamesearch" onkeyup="UDFSOIDNameFunction()">
      </td>
      </tr>
      </tbody>
      </table>
        <table id="UDFSOIDTable2" class="display nowrap table  table-striped table-bordered" width="100%">
          <thead id="thead2">
          </thead>
          <tbody id="tbody_udfsoid"> 
          @foreach ($objUdfSIData as $udfindex=>$udfRow)
          <tr id="udfsoid_{{ $udfindex }}" class="clsudfsoid">
            <td width="50%">{{ $udfRow->LABEL }}
            <input type="hidden" id="txtudfsoid_{{ $udfindex }}" data-desc="{{ $udfRow->LABEL }}"  value="{{ $udfRow->UDF_SIID }}"/>
            </td>
            <td id="udfvalue_{{ $udfindex }}">{{ $udfRow-> VALUETYPE }}
            <input type="hidden" id="txtudfvalue__{{ $udfindex }}" data-desc="{{ $udfRow->DESCRIPTIONS }}"  
            value="{{ $udfRow->ISMANDATORY }}"/></td>
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
<!-- UDF Dropdown-->

<!-- Alert -->
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


<!-- Template Master Dropdown-->

<div id="Templatepopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='Template_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Template Master</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="TemplateTable" class="display nowrap table  table-striped table-bordered">
    <thead>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Name</th>
      <th class="ROW3">Date</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="Templatecodesearch" class="form-control" onkeyup="TemplateCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="Templatenamesearch" class="form-control" onkeyup="TemplateDateFunction()"></td>
    </tr>
    </tbody>
    </table>
      <table id="TemplateTable2" class="display nowrap table  table-striped table-bordered">
        <thead id="thead2">
         
        </thead>
        <tbody>
        @foreach ($objTemplateMaster as $calindex=>$TempRow)
        <tr>
            <td class="ROW1"> <input type="checkbox" name="SELECT_TEMPLATE[]" id="Templatecode_{{ $calindex }}" class="clstemplateid" value="{{ $TempRow-> TEMPLATEID }}" ></td>
            <td class="ROW2">{{ $TempRow-> TEMPLATE_NAME }}
              <input type="hidden" id="txtTemplatecode_{{ $calindex }}" data-desc="{{ $TempRow-> TEMPLATE_NAME }}" data-desc2="{{isset($TempRow->INDATE) && $TempRow->INDATE !='' && $TempRow->INDATE !='1900-01-01' ? date('d-m-Y',strtotime($TempRow->INDATE)):''}}" data-desc3="{{ $TempRow-> TEMPLATE }}"  value="{{ $TempRow-> TEMPLATEID }}"/>
            </td>
            <td class="ROW3">{{isset($TempRow->INDATE) && $TempRow->INDATE !='' && $TempRow->INDATE !='1900-01-01' ? date('d-m-Y',strtotime($TempRow->INDATE)):''}}</td>
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
<!-- Template Master Dropdown-->


@endsection


@push('bottom-css')
<style>
/*
#custom_dropdown, #frm_trn_si_filter {
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

table{
  font-size:13px;
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
        var txtvaluetype = $.trim($(this).find('[id*="udfvalue"]').text());
        var txtismandatory =  $("#txt"+fieldid2+"").val();
        var txtdescription =  $("#txt"+fieldid2+"").data("desc");
        
        var txtcol = $('#hdn_UDFSOID').val();
        $("#"+txtcol).val(txtname);
        $("#"+txtcol).parent().parent().find("[id*='SIUDFID_REF']").val(txtid);
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

  function TNCNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("TNCnamesearch");
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

  $('#txtTNCID_popup').click(function(event){
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
       
        //sub GL
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
        $('.clstncdet').click(function(){
    
            var id = $(this).attr('id');
            var txtid =    $("#txt"+id+"").val();
            var txtname =   $("#txt"+id+"").data("desc");
            var fieldid2 = $(this).find('[id*="tncvalue"]').attr('id');
            var txtvaluetype = $.trim($(this).find('[id*="tncvalue"]').text());
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

  function CTIDNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("CTIDnamesearch");
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

  $('#txtCTID_popup').click(function(event){
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
       
        //Details
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
            getActionEvent();
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
        $('.clsctiddet').click(function(){    
            var id = $(this).attr('id');
            var txtid =    $("#txt"+id+"").val();
            var txtname =   $("#txt"+id+"").data("desc");
            var fieldid2 = $(this).find('[id*="ctidbasis"]').attr('id');
            var txtbasis = $.trim($(this).find('[id*="ctidbasis"]').text());
            var txtactual =  $("#txt"+fieldid2+"").val();
            var txtgst =  $("#txt"+fieldid2+"").data("desc");
            var fieldid3 = $(this).find('[id*="ctidformula_"]').attr('id');
            var txtrate = $.trim($(this).find('[id*="ctidformula_"]').text());
            var txtsqno =  $("#txt"+fieldid3+"").val();
            var txtformula =  $("#txt"+fieldid3+"").data("desc");
            var txtamount = $.trim($(this).find('[id*="ctidamount_"]').text());
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
           
            event.preventDefault();
            
        });
  }
//Calculation Details Ends
//------------------------


//Sub GL Account Starts
//------------------------

$("#txtsubgl_popup").click(function(event){
  var CODE = ''; 
    var NAME = ''; 
    var FORMID = "{{$FormId}}";
    loadCustomer(CODE,NAME,FORMID);
    $("#customer_popus").show();
    event.preventDefault();
});

$("#customer_closePopup").on("click",function(event){ 
    $("#customer_popus").hide();
    event.preventDefault();
});
function bindSubLedgerEvents(){ 
  $(".clssubgl").click(function(){
    
            var id = $(this).attr('id');
            var txtval =    $("#txt"+id+"").val();
            var texdesc =   $("#txt"+id+"").data("desc");
            var glid    =   $("#txt"+id+"").data("desc2");
            var oldSLID =   $("#SLID_REF").val();
            var MaterialClone = $('#hdnmaterial').val();
            $("#txtsubgl_popup").val(texdesc);
            $("#txtsubgl_popup").blur();
            $("#SLID_REF").val(txtval);
            $("#GLID_REF").val(glid);
            if (txtval != oldSLID)
            {


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
            $("#customer_popus").hide();
            $("#customercodesearch").val(''); 
            $("#customernamesearch").val(''); 
          
            var customid = txtval;
              if(customid!=''){
                $("#CREDIT_DAYS").val('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"getcreditdays"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {
                        $("#CREDIT_DAYS").val(data); 
                        var d = new Date($("#DUE_DATE").val());
                        var days = parseInt(data - 1); 
                        d.setDate(d.getDate() + days);
                        var todate = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ; 
                        $("#DUE_DATE").val(todate);                       
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#CREDIT_DAYS").val('');  
                        $("#DUE_DATE").val('');                       
                      },
                  }); 
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
                    
                    
                  var BILLTO = $('#BILLTO').val();
                  var SHIPTO = $('#SHIPTO').val();
                  $.ajax({
                  url:'{{route("transaction",[156,"getTDSApplicability"])}}',
                  type:'POST',
                  data:{'id':customid},
                  success:function(data) {
                  if(data == 1)
                  {
                    $('#drpTDS').val('Yes');
                    $.ajaxSetup({
                      headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                    });
                    $.ajax({
                      url:'{{route("transaction",[156,"getTDSDetails"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data) {                        
                      $("#tbody_tds").html('');
                      $("#tbody_tds").html(data);
                      },
                      error:function(data){
                      console.log("Error: Something went wrong.");
                      var TDSBody = $('#tbody_tds').html();
                      $("#tbody_tds").html(TDSBody);
                      },
                    });
                  }
                  else
                  {
                    $('#drpTDS').val('No');
                  }
                  },
                  error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#drpTDS').val('');
                  },
                  });


                  getTaxStatus(customid);
                  
                  
              }
              event.preventDefault();
        });
  }
//Sub GL Account Ends
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

  function BillToNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("BillTonamesearch");
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
  $('#div_billto').on('click','#txtBILLTO',function(event){
          var customid = $('#SLID_REF').val();
          $("#tbody_BillTo").html('');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
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

  function ShipToNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ShipTonamesearch");
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

  $('#div_shipto').on('click','#txtSHIPTO',function(event){

          var customid = $('#SLID_REF').val();
          $("#tbody_ShipTo").html('');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
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
          var texdesc =   $("#txt"+fieldid+"").data("desc");
          var taxstate =  $("#txt"+fieldid+"").data("desc2");
          var oldShipto =   $("#SHIPTO").val();
          var MaterialClone = $('#hdnmaterial').val();

          if (txtval != oldShipto)
          {
              $('#Material').html(MaterialClone);
              $('#TotalValue').val('0.00');
              $('#Row_Count1').val('1');
              if ($('#DirectSO').is(":checked") == true){
                    $('#Material').find('[id*="txtSQ_popup"]').prop('disabled','true')
                    event.preventDefault();
              }
              else
              {
                  $('#Material').find('[id*="txtSQ_popup"]').removeAttr('disabled');
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
  //Sales Challan Dropdown
  let sctid = "#SalesChallanTable2";
      let sctid2 = "#SalesChallanTable";
      let salesChallanheaders = document.querySelectorAll(sctid2 + " th");

      // Sort the table element when clicking on the table headers
      salesChallanheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sctid, ".clsscid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function SalesChallanCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesChallancodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesChallanTable2");
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

  function SalesChallanNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesChallannamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SalesChallanTable2");
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

  $('#Material').on('click','[id*="txtSC_popup"]',function(event){
        var id = $('#SLID_REF').val();
        var BILLTO = $('#BILLTO').val();
        var SHIPTO = $('#SHIPTO').val();
        $("#tbody_SC").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  })
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"getsaleschallan"])}}',
                      type:'POST',
                      data:{'id':id,'BILLTO':BILLTO,'SHIPTO':SHIPTO},
                      success:function(data) {
                        $("#tbody_SC").html(data);
                        BindSalesChallan();
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_SC").html('');
                      },
                  });
        $("#SCpopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="SCID_REF"]').attr('id');

        $('#hdn_scid').val(id);
        $('#hdn_scid2').val(id2);
      });

      $("#SC_closePopup").click(function(event){
        $("#SCpopup").hide();
      });
      function BindSalesChallan(){
      $(".clsscid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        var leadno =   $("#txt"+fieldid+"").data("leadno");
        var leaddt =   $("#txt"+fieldid+"").data("leaddt");

        var dealerid =   $("#txt"+fieldid+"").data("dealerid");
        var schemeid =   $("#txt"+fieldid+"").data("schemeid");


        var DEALDER  = [];
        
        $('#example2').find('.participantRow').each(function(){
          if($(this).find('[id*="DEALERID_REF"]').val() != ''){
            var dealderid  = $(this).find('[id*="DEALERID_REF"]').val();
              if(dealderid!=''){
                    if(jQuery.inArray(dealderid, DEALDER) == -1){
                      DEALDER.push(dealderid);
                }
              }
          }
        });


        if(DEALDER.length != 0 && dealerid != ''){

      if(DEALDER != dealerid){
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Kindly select the Sales Challan No with Same Dealer.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;            
      }
      }

        





            
        var txtid= $('#hdn_scid').val();
        var txt_id2= $('#hdn_scid2').val();
        var rowid=txtid.split("_").pop(); 

        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        $('#LEADNO_'+rowid).val(leadno);
        $('#LEADDT_'+rowid).val(leaddt);
        $('#DEALERID_REF_'+rowid).val(dealerid);
        $('#SCHEMEID_REF_'+rowid).val(schemeid);
        $('#'+txtid).parent().parent().find('[id*="SOID"]').val('');
        $('#'+txtid).parent().parent().find('[id*="SQID_REF"]').val('');
        $('#'+txtid).parent().parent().find('[id*="SEID_REF"]').val('');
        $('#'+txtid).parent().parent().find('[id*="popupITEMID"]').val('');
        $('#'+txtid).parent().parent().find('[id*="ITEMID_REF"]').val('');
        $('#'+txtid).parent().parent().find('[id*="ItemName"]').val('');
        $('#'+txtid).parent().parent().find('[id*="ITEMSPECI"]').val('');
        $('#'+txtid).parent().parent().find('[id*="SCMUOM"]').val('');
        $('#'+txtid).parent().parent().find('[id*="SCMUOMQTY"]').val('');
        $('#'+txtid).parent().parent().find('[id*="SCAUOM"]').val('');
        $('#'+txtid).parent().parent().find('[id*="SCAUOMQTY"]').val('');
        $('#'+txtid).parent().parent().find('[id*="popupMUOM"]').val('');
        $('#'+txtid).parent().parent().find('[id*="MAIN_SIUOMID_REF"]').val('');
        $('#'+txtid).parent().parent().find('[id*="SIMAIN_QTY"]').val('');
        $('#'+txtid).parent().parent().find('[id*="SC_FQTY"]').val('');
        $('#'+txtid).parent().parent().find('[id*="popupAUOM"]').val('');
        $('#'+txtid).parent().parent().find('[id*="ALTUOMID_REF"]').val('');
        $('#'+txtid).parent().parent().find('[id*="ALT_UOMID_QTY"]').val('');
        $('#'+txtid).parent().parent().find('[id*="RATEPUOM"]').val('');
        $('#'+txtid).parent().parent().find('[id*="DISPER"]').val('');
        $('#'+txtid).parent().parent().find('[id*="DISCOUNT_AMT"]').val('');
        $('#'+txtid).parent().parent().find('[id*="DISAFTT_AMT"]').val('');
        $('#'+txtid).parent().parent().find('[id*="IGST"]').val('');
        $('#'+txtid).parent().parent().find('[id*="CGST"]').val('');
        $('#'+txtid).parent().parent().find('[id*="SGST"]').val('');
        $('#'+txtid).parent().parent().find('[id*="TGST_AMT"]').val('');
        $('#'+txtid).parent().parent().find('[id*="TOT_AMT"]').val('');
        $("#SCpopup").hide();
        
        $("#SalesChallancodesearch").val(''); 
        $("#SalesChallannamesearch").val(''); 
        GetDealerCommission(); 
        event.preventDefault();
      });
      }
      

  //Sales Challan Dropdown Ends
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
  //Item POPUP
//------------------------
//------------------------
  //CUSTOMER LIST POPUP
  let cltid = "#GlCodeTable2";
      let cltid2 = "#GlCodeTable";
      let clheaders = document.querySelectorAll(cltid2 + " th");

      // Sort the table element when clicking on the table headers
      clheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(cltid, ".clsglid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function CustomerCodeFunction(FORMID) {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("customercodesearch");
        filter = input.value.toUpperCase();
        
      if(filter.length == 0)
        {
          var CODE = ''; 
          var NAME = ''; 
          loadCustomer(CODE,NAME,FORMID); 
        }
        else if(filter.length >= 3)
        {
          var CODE = filter; 
          var NAME = ''; 
          loadCustomer(CODE,NAME,FORMID); 
        }
        else
        {
          table = document.getElementById("GlCodeTable2");
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
    }

  function CustomerNameFunction(FORMID) {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("customernamesearch");
        filter = input.value.toUpperCase();
        if(filter.length == 0)
        {
          var CODE = ''; 
          var NAME = ''; 
          loadCustomer(CODE,NAME,FORMID);
        }
        else if(filter.length >= 3)
        {
          var CODE = ''; 
          var NAME = filter; 
          loadCustomer(CODE,NAME,FORMID);  
        }
        else
        {
          table = document.getElementById("GlCodeTable2");
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
    
    function loadCustomer(CODE,NAME,FORMID){
      var url	=	'<?php echo asset('');?>transaction/'+FORMID+'/getsubledger';
        $("#tbody_subglacct").html('');
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          url:url,
          type:'POST',
          data:{'CODE':CODE,'NAME':NAME},
          success:function(data) {
          $("#tbody_subglacct").html(data); 
          bindSubLedgerEvents(); 

          },
          error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_subglacct").html('');                        
          },
        });
    }
  //CUSTOMER LIST POPUP
//------------------------
//------------------------
  //Vendor Popup Start
  let vltid = "#CodeTable2";
    let vltid2 = "#CodeTable";
    let vlheaders = document.querySelectorAll(vltid2 + " th");

      // Sort the table element when clicking on the table headers
      vlheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(vltid, ".clsclid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function CodeFunction(FORMID) {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("codesearch");
        filter = input.value.toUpperCase();
        if(filter.length == 0)
        {
          var CODE = ''; 
          var NAME = ''; 
          loadVendor(CODE,NAME,FORMID); 
        }
        else if(filter.length >= 3)
        {
          var CODE = filter; 
          var NAME = ''; 
          loadVendor(CODE,NAME,FORMID); 
        }
        else
        {
          table = document.getElementById("CodeTable2");
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
    }

  function NameFunction(FORMID) {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("namesearch");
        filter = input.value.toUpperCase();
        if(filter.length == 0)
        {
          var CODE = ''; 
          var NAME = ''; 
          loadVendor(CODE,NAME,FORMID);
        }
        else if(filter.length >= 3)
        {
          var CODE = ''; 
          var NAME = filter; 
          loadVendor(CODE,NAME,FORMID);  
        }
        else
        {
          table = document.getElementById("CodeTable2");
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
  
  function loadVendor(CODE,NAME,FORMID){
      var url	=	'<?php echo asset('');?>transaction/'+FORMID+'/getsubledger';
        $("#tbody_subglacct").html('');
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          url:url,
          type:'POST',
          data:{'CODE':CODE,'NAME':NAME},
          success:function(data) {
          $("#tbody_subglacct").html(data); 
          bindSubLedgerEvents(); 

          },
          error:function(data){
          console.log("Error: Something went wrong.");
          $("#tbody_subglacct").html('');                        
          },
        });
  }
  
  //Vendor Popup Ends
//------------------------

  $('#Material').on('click','[id*="popupITEMID"]',function(event){
        var SalesChallanID = $(this).parent().parent().find('[id*="SCID_REF"]').val();
        var SalesChallanNO = $(this).parent().parent().find('[id*="txtSC_popup"]').val();
        var taxstate = $.trim($('#Tax_State').val());
        if(SalesChallanID!=''){
                $("#tbody_ItemID").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"getItemDetailsChallanwise"])}}',
                      type:'POST',
                      data:{'id':SalesChallanNO, 'taxstate':taxstate},
                      success:function(data) {
                        $("#tbody_ItemID").html(data);   
                        bindItemEvents();                     
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_ItemID").html('');                        
                      },
                  }); 
        }

        $("#ITEMIDpopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="ITEMSPECI"]').attr('id');
        var id5 = $(this).parent().parent().find('[id*="SCMUOM"]').attr('id');
        var id6 = $(this).parent().parent().find('[id*="SCMUOMQTY"]').attr('id');
        var id7 = $(this).parent().parent().find('[id*="SCAUOM"]').attr('id');
        var id8 = $(this).parent().parent().find('[id*="SCAUOMQTY"]').attr('id');
        var id9 = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
        var id10 = $(this).parent().parent().find('[id*="MAIN_SIUOMID_REF"]').attr('id');
        var id11 = $(this).parent().parent().find('[id*="SIMAIN_QTY"]').attr('id');
        var id12 = $(this).parent().parent().find('[id*="popupAUOM"]').attr('id');
        var id13 = $(this).parent().parent().find('[id*="ALTUOMID_REF"]').attr('id');
        var id14 = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
        var id15 = $(this).parent().parent().find('[id*="RATEPUOM"]').attr('id');
        var id16 = $(this).parent().parent().find('[id*="SC_FQTY"]').attr('id');

        $('#hdn_ItemID').val(id);
        $('#hdn_ItemID2').val(id2);
        $('#hdn_ItemID3').val(id3);
        $('#hdn_ItemID4').val(id4);
        $('#hdn_ItemID5').val(id5);
        $('#hdn_ItemID6').val(id6);
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
        $('#hdn_ItemID17').val(SalesChallanNO);
        var r_count = 0;
        var SalesChallan = [];
        $('#Material').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            SalesChallan.push($(this).find('[id*="SCID_REF"]').val());
            r_count = parseInt(r_count)+1;
            $('#hdn_ItemID21').val(r_count);
          }
        });
        $('#hdn_ItemID18').val(SalesChallan.join(', '));
        var ItemID = [];
        $('#Material').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            ItemID.push($(this).find('[id*="ITEMID_REF"]').val());
          }
        });
        $('#hdn_ItemID19').val(ItemID.join(', '));
        var EnquiryID = [];
        $('#Material').find('.participantRow').each(function(){
          if($(this).find('[id*="SEID_REF"]').val() != '')
          {
            EnquiryID.push($(this).find('[id*="SEID_REF"]').val());
          }
        });
        $('#hdn_ItemID20').val(EnquiryID.join(', '));
        var QuotationID = [];
        $('#Material').find('.participantRow').each(function(){
          if($(this).find('[id*="SQID_REF"]').val() != '')
          {
            QuotationID.push($(this).find('[id*="SQID_REF"]').val());
          }
        });
        $('#hdn_ItemID22').val(QuotationID.join(', '));
        var SalesOrderID = [];
        $('#Material').find('.participantRow').each(function(){
          if($(this).find('[id*="SOID_REF"]').val() != '')
          {
            SalesOrderID.push($(this).find('[id*="SOID_REF"]').val());
          }
        });
        $('#hdn_ItemID23').val(SalesOrderID.join(', '));
        event.preventDefault();
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

    var apartno =  $("#txt"+fieldid3+"").data("desc2");
    var cpartno =  $("#txt"+fieldid3+"").data("desc3");
    var opartno =  $("#txt"+fieldid3+"").data("desc4");

    var txtmuom =  $.trim($(this).find('[id*="itemuom"]').text());
    var fieldid4 = $(this).find('[id*="uomqty"]').attr('id');
    var txtauomid =  $("#txt"+fieldid4+"").val();
    var txtauomqty =  $("#txt"+fieldid4+"").data("desc");
    var txtmuomqty =  $.trim($(this).find('[id*="uomqty"]').text());
    var fieldid5 = $(this).find('[id*="irate"]').attr('id');
    var txtruom =  $("#txt"+fieldid5+"").val();
    var txtmqtyf = $("#txt"+fieldid5+"").data("desc");
    var txtdisper = $("#txt"+fieldid5+"").data("desc1");
    var txtdisamt = $("#txt"+fieldid5+"").data("desc2");
    var fieldid6 = $(this).find('[id*="itax"]').attr('id');
    var txttax2 =  $("#txt"+fieldid6+"").val();
    var txttax1 = $("#txt"+fieldid6+"").data("desc");
    var fieldid7 = $(this).find('[id*="ise"]').attr('id');
    var txtquoteno = $("#txt"+fieldid7+"").val();
    var txtenqid = $("#txt"+fieldid7+"").data("desc");
    var txtsoid = $("#txt"+fieldid7+"").data("desc1");
    var txtscid = $("#txt"+fieldid7+"").data("desc2");
    var rcount1 = parseInt($(this).closest('table').find('.clsitemid').length);
    var rcount2 = $('#hdn_ItemID21').val();
    var r_count2 = 0;
    if(txtquoteno == undefined)
    {
      txtquoteno = '';
    }
    if(txtenqid == undefined)
    {
      txtenqid = '';
    }
    if(txtsoid == undefined)
    {
      txtsoid = '';
    }
    if(txtscid == undefined)
    {
      txtscid = '';
    }
    var totalvalue = 0.00;
    var txttaxamt1 = 0.00;
    var txttaxamt2 = 0.00;
    var txttottaxamt = 0.00;
    var txttotamtatax =0.00;

    txtruom = parseFloat(txtruom).toFixed(5);
    
    txtauomqty = (parseInt(txtmuomqty)/parseInt(txtmqtyf))*parseInt(txtauomqty);
    
    
    var txtamt = parseFloat((parseFloat(txtmuomqty)*parseFloat(txtruom))).toFixed(2);
    if(txtdisper == undefined || txtdisper == '')
    {
        txtdisper = 0.0000;
    }
    else
    {
      pad(txtamt, 6); 
      txtamt = parseFloat(parseFloat(txtamt) - parseFloat((parseFloat(txtamt)*parseFloat(txtdisper))/100)).toFixed(2)
    }
    if(txtdisamt == undefined || txtdisamt == '')
    {
        txtdisamt = 0.00;
    }
    else
    {
      pad(txtamt, 4);
      txtamt = parseFloat(parseFloat(txtamt) - parseFloat(txtdisamt)).toFixed(2)
    }
    if(txttax1 == undefined || txttax1 == '')
    {
      txttax1 = 0.0000;
        txttaxamt1 = 0.00;
    }
    else
    {
       txttaxamt1 = parseFloat((parseFloat(txtamt)*parseFloat(txttax1))/100).toFixed(2);
    }
    if(txttax2 == undefined || txttax2 == '')
    {
      txttax2 = 0.0000;
       txttaxamt2 = 0.00;
    }
    else
    {
       txttaxamt2 = parseFloat((parseFloat(txtamt)*parseFloat(txttax2))/100).toFixed(2);
    }
    

    if($("#LUT").is(":checked") == true) {
      var txttottaxamt = 0.00;
    }
    else{
      var txttottaxamt = parseFloat((parseFloat(txttaxamt1)+parseFloat(txttaxamt2))).toFixed(2);
    }

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
  var SalesChln = [];
  $('#Material').find('.participantRow').each(function(){
    if($(this).find('[id*="ITEMID_REF"]').val() != '')
    {
      var seitem = $(this).find('[id*="SCID_REF"]').val()+'-'+$(this).find('[id*="SOID"]').val()+'-'+$(this).find('[id*="SQID_REF"]').val()+'-'+$(this).find('[id*="SEID_REF"]').val()+'-'+$(this).find('[id*="ITEMID_REF"]').val();
      SalesChln.push(seitem);
      r_count2 = parseInt(r_count2) + 1;
    }
  });
  
  var saleschallan2 =  $('#hdn_ItemID18').val();
  var itemids =  $('#hdn_ItemID19').val();
  var enquiryids =  $('#hdn_ItemID20').val();
  var quotationids =  $('#hdn_ItemID22').val();
  var salesorderids =  $('#hdn_ItemID23').val();

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
              txtquoteno = '';
              txtenqid = '';
              txtsoid = '';
              txtscid = '';
              $('.js-selectall').prop("checked", false);
              return false;
        }
        var txtschitem = txtscid+'-'+txtsoid+'-'+txtquoteno+'-'+txtenqid+'-'+txtval;

        var SCHEME  = [];        
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="SCHEMEID_REF"]').val() != ''){
      var schemeid  = $(this).find('[id*="SCHEMEID_REF"]').val();
        if(schemeid!=''){
              if(jQuery.inArray(schemeid, SCHEME) == -1){
                SCHEME.push(schemeid);
          }
        }
    }
  }); 


        if(SCHEME.length === 0 && jQuery.inArray(txtschitem, SalesChln) !== -1)
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
              txtquoteno = '';
              txtenqid = '';
              txtsoid = '';
              txtscid = '';
              $('.js-selectall').prop("checked", false);
              return false;
        }

        var SCHEME  = [];        
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="SCHEMEID_REF"]').val() != ''){
      var schemeid  = $(this).find('[id*="SCHEMEID_REF"]').val();
        if(schemeid!=''){
              if(jQuery.inArray(schemeid, SCHEME) == -1){
                SCHEME.push(schemeid);
          }
        }
    }
  }); 

        if(SCHEME.length === 0 && saleschallan2.indexOf(txtscid) != -1 && salesorderids.indexOf(txtsoid) != -1 && quotationids.indexOf(txtquoteno) != -1
        && enquiryids.indexOf(txtenqid) != -1 && itemids.indexOf(txtval) != -1)
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
                      txtquoteno = '';
                      txtenqid = '';
                      txtsoid = '';
                      txtscid = '';
                      $('.js-selectall').prop("checked", false);
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

                  $clone.find('[id*="Alpspartno"]').val(apartno);
                  $clone.find('[id*="Custpartno"]').val(cpartno);
                  $clone.find('[id*="OEMpartno"]').val(opartno);

                  $clone.find('[id*="SEID_REF"]').val(txtenqid);
                  $clone.find('[id*="SQID_REF"]').val(txtquoteno);
                  $clone.find('[id*="SOID"]').val(txtsoid);
                  $clone.find('[id*="SCID_REF"]').val(txtscid);
                  $clone.find('[id*="ItemName"]').val(txtname);
                  $clone.find('[id*="ITEMSPECI"]').val(txtspec);
                  $clone.find('[id*="SCMUOM"]').val(txtmuom);
                  $clone.find('[id*="SCMUOMQTY"]').val(txtmuomqty);
                  $clone.find('[id*="SCAUOM"]').val(txtauom);
                  $clone.find('[id*="SCAUOMQTY"]').val(txtauomqty);
                  $clone.find('[id*="popupMUOM"]').val(txtmuom);
                  $clone.find('[id*="MAINUOMID_REF"]').val(txtmuomid);
                  $clone.find('[id*="SIMAIN_QTY"]').val(txtmuomqty);
                  $clone.find('[id*="SC_FQTY"]').val(txtmuomqty);
                  $clone.find('[id*="popupAUOM"]').val(txtauom);
                  $clone.find('[id*="ALTUOMID_REF"]').val(txtauomid);
                  $clone.find('[id*="ALT_UOMID_QTY"]').val(txtauomqty);
                  $clone.find('[id*="RATEPUOM"]').val(txtruom);
                  $clone.find('[id*="DISPER"]').val(txtdisper);
                  $clone.find('[id*="DISCOUNT_AMT"]').val(txtdisamt);
                  $clone.find('[id*="DISAFTT_AMT"]').val(txtamt);
                  $clone.find('[id*="TOT_AMT"]').val(txttotamtatax);
                  $clone.find('[id*="TGST_AMT"]').val(txttottaxamt);
                  if($.trim($('#Tax_State').val()) == 'OutofState'){

                    if($("#LUT").is(":checked") == true) {
                      $clone.find('[id*="IGST"]').val('0.00');
                      $clone.find('[id*="IGSTAMT"]').val('0.00');
                      $clone.find('[id*="SGST"]').prop('disabled',true); 
                      $clone.find('[id*="CGST"]').prop('disabled',true); 
                      $clone.find('[id*="SGSTAMT"]').prop('disabled',true); 
                      $clone.find('[id*="CGSTAMT"]').prop('disabled',true);  
                    }
                    else{
                      $clone.find('[id*="IGST"]').val(txttax1);
                      $clone.find('[id*="IGSTAMT"]').val(txttaxamt1);
                      $clone.find('[id*="SGST"]').prop('disabled',true); 
                      $clone.find('[id*="CGST"]').prop('disabled',true); 
                      $clone.find('[id*="SGSTAMT"]').prop('disabled',true); 
                      $clone.find('[id*="CGSTAMT"]').prop('disabled',true); 
                    }
                    
                  }
                  else
                  {

                    if($("#LUT").is(":checked") == true) {
                      $clone.find('[id*="CGST"]').val('0.00');
                      $clone.find('[id*="IGST"]').prop('disabled',true); 
                      $clone.find('[id*="SGST"]').val('0.00');
                      $clone.find('[id*="CGSTAMT"]').val('0.00');
                      $clone.find('[id*="SGSTAMT"]').val('0.00');
                      $clone.find('[id*="IGSTAMT"]').prop('disabled',true); 
                    }
                    else{
                      $clone.find('[id*="CGST"]').val(txttax1);
                      $clone.find('[id*="IGST"]').prop('disabled',true); 
                      $clone.find('[id*="SGST"]').val(txttax2);
                      $clone.find('[id*="CGSTAMT"]').val(txttaxamt1);
                      $clone.find('[id*="SGSTAMT"]').val(txttaxamt2);
                      $clone.find('[id*="IGSTAMT"]').prop('disabled',true); 
                    }
                    
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
                  
                if($clone.find('[id*="txtSC_popup"]').val() == '')
                {
                  $clone.find('[id*="SCMUOM"]').val('');
                  $clone.find('[id*="SCMUOMQTY"]').val('');
                  $clone.find('[id*="SCAUOM"]').val('');
                  $clone.find('[id*="SCAUOMQTY"]').val('');

                  $clone.find('[id*="Alpspartno"]').val('');
                  $clone.find('[id*="Custpartno"]').val('');
                  $clone.find('[id*="OEMpartno"]').val('');

                }                  
            }
            else
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
                $('#'+txt_id7).val(txtauom);
                $('#'+txt_id8).val(txtauomqty);
                $('#'+txt_id9).val(txtmuom);
                $('#'+txt_id10).val(txtmuomid);
                $('#'+txt_id11).val(txtmuomqty);
                $('#'+txt_id12).val(txtauom);
                $('#'+txt_id13).val(txtauomid);
                $('#'+txt_id14).val(txtauomqty);
                $('#'+txt_id15).val(txtruom);
                $('#'+txt_id16).val(txtmuomqty);
                $('#'+txtid).parent().parent().find('[id*="SEID_REF"]').val(txtenqid);
                $('#'+txtid).parent().parent().find('[id*="SQID_REF"]').val(txtquoteno);
                $('#'+txtid).parent().parent().find('[id*="SOID"]').val(txtsoid);
                $('#'+txtid).parent().parent().find('[id*="SCID_REF"]').val(txtscid);
                $('#'+txtid).parent().parent().find('[id*="DISPER"]').val(txtdisper);
                $('#'+txtid).parent().parent().find('[id*="DISCOUNT_AMT"]').val(txtdisamt);
                $('#'+txtid).parent().parent().find('[id*="DISAFTT_AMT"]').val(txtamt);
                $('#'+txtid).parent().parent().find('[id*="TOT_AMT"]').val(txttotamtatax);
                $('#'+txtid).parent().parent().find('[id*="TGST_AMT"]').val(txttottaxamt);

                $('#'+txtid).parent().parent().find('[id*="Alpspartno"]').val(apartno);
                $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
                $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);

                if($.trim($('#Tax_State').val()) == 'OutofState')
                  {

                    if($("#LUT").is(":checked") == true) {
                      $('#'+txtid).parent().parent().find('[id*="IGST"]').val('0.00');
                      $('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').val('0.00');
                      $('#'+txtid).parent().parent().find('[id*="SGST"]').prop('disabled',true); 
                      $('#'+txtid).parent().parent().find('[id*="CGST"]').prop('disabled',true);
                      $('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').prop('disabled',true); 
                      $('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').prop('disabled',true); 
                    }
                    else{
                      $('#'+txtid).parent().parent().find('[id*="IGST"]').val(txttax1);
                      $('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').val(txttaxamt1);
                      $('#'+txtid).parent().parent().find('[id*="SGST"]').prop('disabled',true); 
                      $('#'+txtid).parent().parent().find('[id*="CGST"]').prop('disabled',true);
                      $('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').prop('disabled',true); 
                      $('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').prop('disabled',true); 
                    }

                    
                  }
                  else
                  {

                    if($("#LUT").is(":checked") == true) {
                      $('#'+txtid).parent().parent().find('[id*="CGST"]').val('0.00');
                      $('#'+txtid).parent().parent().find('[id*="IGST"]').prop('disabled',true); 
                      $('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').prop('disabled',true); 
                      $('#'+txtid).parent().parent().find('[id*="SGST"]').val('0.00');
                      $('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').val('0.00');
                      $('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').val('0.00');
                    }
                    else{
                      $('#'+txtid).parent().parent().find('[id*="CGST"]').val(txttax1);
                      $('#'+txtid).parent().parent().find('[id*="IGST"]').prop('disabled',true); 
                      $('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').prop('disabled',true); 
                      $('#'+txtid).parent().parent().find('[id*="SGST"]').val(txttax2);
                      $('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').val(txttaxamt2);
                      $('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').val(txttaxamt1);
                    }

                    
                  }
                  var tvalue = parseFloat(txttotamtatax).toFixed(2);
                  totalvalue = $('#TotalValue').val();
                  totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
                  totalvalue = parseFloat(totalvalue).toFixed(2);
                  $('#TotalValue').val(totalvalue);
                  if($('#'+txtid).parent().parent().find('[id*="txtSQ_popup"]').val() == '')
                  {
                    $('#'+txtid).parent().parent().find('[id*="SQMUOM"]').val('');
                    $('#'+txtid).parent().parent().find('[id*="SQMUOMQTY"]').val('');
                    $('#'+txtid).parent().parent().find('[id*="SQAUOM"]').val('');
                    $('#'+txtid).parent().parent().find('[id*="SQAUOMQTY"]').val('');

                    $('#'+txtid).parent().parent().find('[id*="Alpspartno"]').val('');
                    $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val('');
                    $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val('');

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
                $('#hdn_ItemID11').val('');
                $('#hdn_ItemID12').val('');
                $('#hdn_ItemID13').val('');
                $('#hdn_ItemID14').val('');
                $('#hdn_ItemID15').val('');
                $('#hdn_ItemID16').val('');
                
            }
            
      }
      
    $("#Itemcodesearch").val(''); 
    $("#Itemnamesearch").val(''); 
    $("#ItemUOMsearch").val(''); 
    $("#ItemGroupsearch").val(''); 
    $("#ItemCategorysearch").val(''); 
    $("#ItemStatussearch").val(''); 
    $('.remove').removeAttr('disabled'); 
    
  });
  $("#ITEMIDpopup").hide();
  $('.js-selectall').prop("checked", false);
  getActionEvent();
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

    var apartno =  $("#txt"+fieldid3+"").data("desc2");
    var cpartno =  $("#txt"+fieldid3+"").data("desc3");
    var opartno =  $("#txt"+fieldid3+"").data("desc4");

    var txtmuom =  $.trim($(this).parent().parent().children('[id*="itemuom"]').text());
    var fieldid4 = $(this).parent().parent().children('[id*="uomqty"]').attr('id');
    var txtauomid =  $("#txt"+fieldid4+"").val();
    var txtauomqty =  $("#txt"+fieldid4+"").data("desc");
    var txtmuomqty =  $.trim($(this).parent().parent().children('[id*="uomqty"]').text());
    var fieldid5 = $(this).parent().parent().children('[id*="irate"]').attr('id');
    var txtruom =  $("#txt"+fieldid5+"").val();
    var txtmqtyf = $("#txt"+fieldid5+"").data("desc");
    var txtdisper = $("#txt"+fieldid5+"").data("desc1");
    var txtdisamt = $("#txt"+fieldid5+"").data("desc2");
    var fieldid6 = $(this).parent().parent().children('[id*="itax"]').attr('id');
    var txttax2 =  $("#txt"+fieldid6+"").val();
    var txttax1 = $("#txt"+fieldid6+"").data("desc");
    var fieldid7 = $(this).parent().parent().children('[id*="ise"]').attr('id');
    var txtquoteno = $("#txt"+fieldid7+"").val();
    var txtenqid = $("#txt"+fieldid7+"").data("desc");
    var txtsoid = $("#txt"+fieldid7+"").data("desc1");
    var txtscid = $("#txt"+fieldid7+"").data("desc2");
  if(txtquoteno == undefined)
    {
      txtquoteno = '';
    }
    if(txtenqid == undefined)
    {
      txtenqid = '';
    }
    if(txtsoid == undefined)
    {
      txtsoid = '';
    }
    if(txtscid == undefined)
    {
      txtscid = '';
    }
  var totalvalue = 0.00;
  var txttaxamt1 = 0.00;
  var txttaxamt2 = 0.00;
  var txttottaxamt = 0.00;
  var txttotamtatax =0.00;
  
  txtruom = parseFloat(txtruom).toFixed(5); 
  txtauomqty = (parseFloat(txtmuomqty)/parseFloat(txtmqtyf))*parseFloat(txtauomqty);
  
  var txtamt = parseFloat((parseFloat(txtmuomqty)*parseFloat(txtruom))).toFixed(2);
  if(txtdisper == undefined || txtdisper == '')
    {
        txtdisper = 0.0000;
    }
    else
    {
      pad(txtamt, 6); 
      txtamt = parseFloat(parseFloat(txtamt) - parseFloat((parseFloat(txtamt)*parseFloat(txtdisper))/100)).toFixed(2)
    }
    if(txtdisamt == undefined || txtdisamt == '')
    {
        txtdisamt = 0.00;
    }
    else
    {
      pad(txtamt, 4);
      txtamt = parseFloat(parseFloat(txtamt) - parseFloat(txtdisamt)).toFixed(2)
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

    if($("#LUT").is(":checked") == true) {
      var txttottaxamt = 0.00;
    }
    else{
      var txttottaxamt = parseFloat((parseFloat(txttaxamt1)+parseFloat(txttaxamt2))).toFixed(2);
    }


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
  var SalesChln = [];
  $('#Material').find('.participantRow').each(function(){
    if($(this).find('[id*="ITEMID_REF"]').val() != '')
    {
      var seitem = $(this).find('[id*="SCID_REF"]').val()+'-'+$(this).find('[id*="SOID"]').val()+'-'+$(this).find('[id*="SQID_REF"]').val()+'-'+$(this).find('[id*="SEID_REF"]').val()+'-'+$(this).find('[id*="ITEMID_REF"]').val();
      SalesChln.push(seitem);
    }
  });
  
  var saleschallan2 =  $('#hdn_ItemID18').val();
  var itemids =  $('#hdn_ItemID19').val();
  var enquiryids =  $('#hdn_ItemID20').val();
  var quotationids =  $('#hdn_ItemID22').val();
  var salesorderids =  $('#hdn_ItemID23').val();

      if($(this).is(":checked") == true) 
      {
        
        var SCHEME  = [];        
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="SCHEMEID_REF"]').val() != ''){
      var schemeid  = $(this).find('[id*="SCHEMEID_REF"]').val();
        if(schemeid!=''){
              if(jQuery.inArray(schemeid, SCHEME) == -1){
                SCHEME.push(schemeid);
          }
        }
    }
  }); 

        var txtschitem = txtscid+'-'+txtsoid+'-'+txtquoteno+'-'+txtenqid+'-'+txtval;
        if(SCHEME.length === 0 && jQuery.inArray(txtschitem, SalesChln) !== -1)
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
              txtquoteno = '';
              txtenqid = '';
              txtsoid = '';
              txtscid = '';
              return false;
        }

        var SCHEME  = [];        
  $('#example2').find('.participantRow').each(function(){
    if($(this).find('[id*="SCHEMEID_REF"]').val() != ''){
      var schemeid  = $(this).find('[id*="SCHEMEID_REF"]').val();
        if(schemeid!=''){
              if(jQuery.inArray(schemeid, SCHEME) == -1){
                SCHEME.push(schemeid);
          }
        }
    }
  }); 

        if(SCHEME.length === 0 && saleschallan2.indexOf(txtscid) != -1 && salesorderids.indexOf(txtsoid) != -1 && quotationids.indexOf(txtquoteno) != -1
        && enquiryids.indexOf(txtenqid) != -1 && itemids.indexOf(txtval) != -1)
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
                      txtquoteno = '';
                      txtenqid = '';
                      txtsoid = '';
                      txtscid = '';
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

                  $clone.find('[id*="Alpspartno"]').val(apartno);
                  $clone.find('[id*="Custpartno"]').val(cpartno);
                  $clone.find('[id*="OEMpartno"]').val(opartno);

                  $clone.find('[id*="SEID_REF"]').val(txtenqid);
                  $clone.find('[id*="SQID_REF"]').val(txtquoteno);
                  $clone.find('[id*="SOID"]').val(txtsoid);
                  $clone.find('[id*="SCID_REF"]').val(txtscid);
                  $clone.find('[id*="ItemName"]').val(txtname);
                  $clone.find('[id*="ITEMSPECI"]').val(txtspec);
                  $clone.find('[id*="SCMUOM"]').val(txtmuom);
                  $clone.find('[id*="SCMUOMQTY"]').val(txtmuomqty);
                  $clone.find('[id*="SCAUOM"]').val(txtauom);
                  $clone.find('[id*="SCAUOMQTY"]').val(txtauomqty);
                  $clone.find('[id*="popupMUOM"]').val(txtmuom);
                  $clone.find('[id*="MAINUOMID_REF"]').val(txtmuomid);
                  $clone.find('[id*="SIMAIN_QTY"]').val(txtmuomqty);
                  $clone.find('[id*="SC_FQTY"]').val(txtmuomqty);
                  $clone.find('[id*="popupAUOM"]').val(txtauom);
                  $clone.find('[id*="ALTUOMID_REF"]').val(txtauomid);
                  $clone.find('[id*="ALT_UOMID_QTY"]').val(txtauomqty);
                  $clone.find('[id*="RATEPUOM"]').val(txtruom);
                  $clone.find('[id*="DISPER"]').val(txtdisper);
                  $clone.find('[id*="DISCOUNT_AMT"]').val(txtdisamt);
                  $clone.find('[id*="DISAFTT_AMT"]').val(txtamt);
                  $clone.find('[id*="TOT_AMT"]').val(txttotamtatax);
                  $clone.find('[id*="TGST_AMT"]').val(txttottaxamt);
                  if($.trim($('#Tax_State').val()) == 'OutofState')
                  {

                    if($("#LUT").is(":checked") == true) {
                      $clone.find('[id*="IGST"]').val('0.00');
                      $clone.find('[id*="IGSTAMT"]').val('0.00');
                      $clone.find('[id*="SGST"]').prop('disabled',true); 
                      $clone.find('[id*="CGST"]').prop('disabled',true); 
                      $clone.find('[id*="SGSTAMT"]').prop('disabled',true); 
                      $clone.find('[id*="CGSTAMT"]').prop('disabled',true); 
                    }
                    else{
                      $clone.find('[id*="IGST"]').val(txttax1);
                      $clone.find('[id*="IGSTAMT"]').val(txttaxamt1);
                      $clone.find('[id*="SGST"]').prop('disabled',true); 
                      $clone.find('[id*="CGST"]').prop('disabled',true); 
                      $clone.find('[id*="SGSTAMT"]').prop('disabled',true); 
                      $clone.find('[id*="CGSTAMT"]').prop('disabled',true); 
                    }

                    
                  }
                  else
                  {

                    if($("#LUT").is(":checked") == true) {
                      $clone.find('[id*="CGST"]').val('0.00');
                      $clone.find('[id*="IGST"]').prop('disabled',true); 
                      $clone.find('[id*="SGST"]').val('0.00');
                      $clone.find('[id*="CGSTAMT"]').val('0.00');
                      $clone.find('[id*="SGSTAMT"]').val('0.00');
                      $clone.find('[id*="IGSTAMT"]').prop('disabled',true); 
                    }
                    else{
                      $clone.find('[id*="CGST"]').val(txttax1);
                      $clone.find('[id*="IGST"]').prop('disabled',true); 
                      $clone.find('[id*="SGST"]').val(txttax2);
                      $clone.find('[id*="CGSTAMT"]').val(txttaxamt1);
                      $clone.find('[id*="SGSTAMT"]').val(txttaxamt2);
                      $clone.find('[id*="IGSTAMT"]').prop('disabled',true); 
                    }

                    
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
                  
                  if($clone.find('[id*="txtSC_popup"]').val() == '')
                  {
                  $clone.find('[id*="SCMUOM"]').val('');
                  $clone.find('[id*="SCMUOMQTY"]').val('');
                  $clone.find('[id*="SCAUOM"]').val('');
                  $clone.find('[id*="SCAUOMQTY"]').val('');

                  $clone.find('[id*="Alpspartno"]').val('');
                  $clone.find('[id*="Custpartno"]').val('');
                  $clone.find('[id*="OEMpartno"]').val('');

                  }
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
                $('#'+txt_id7).val(txtauom);
                $('#'+txt_id8).val(txtauomqty);
                $('#'+txt_id9).val(txtmuom);
                $('#'+txt_id10).val(txtmuomid);
                $('#'+txt_id11).val(txtmuomqty);
                $('#'+txt_id12).val(txtauom);
                $('#'+txt_id13).val(txtauomid);
                $('#'+txt_id14).val(txtauomqty);
                $('#'+txt_id15).val(txtruom);
                $('#'+txt_id16).val(txtmuomqty);
                $('#'+txtid).parent().parent().find('[id*="SEID_REF"]').val(txtenqid);
                $('#'+txtid).parent().parent().find('[id*="SQID_REF"]').val(txtquoteno);
                $('#'+txtid).parent().parent().find('[id*="SOID"]').val(txtsoid);
                $('#'+txtid).parent().parent().find('[id*="SCID_REF"]').val(txtscid);
                $('#'+txtid).parent().parent().find('[id*="DISPER"]').val(txtdisper);
                $('#'+txtid).parent().parent().find('[id*="DISCOUNT_AMT"]').val(txtdisamt);
                $('#'+txtid).parent().parent().find('[id*="DISAFTT_AMT"]').val(txtamt);
                $('#'+txtid).parent().parent().find('[id*="TOT_AMT"]').val(txttotamtatax);
                $('#'+txtid).parent().parent().find('[id*="TGST_AMT"]').val(txttottaxamt);

                $('#'+txtid).parent().parent().find('[id*="Alpspartno"]').val(apartno);
                $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
                $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);

                if($.trim($('#Tax_State').val()) == 'OutofState')
                  {

                    if($("#LUT").is(":checked") == true) {
                      $('#'+txtid).parent().parent().find('[id*="IGST"]').val('0.00');
                      $('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').val('0.00');
                      $('#'+txtid).parent().parent().find('[id*="SGST"]').prop('disabled',true); 
                      $('#'+txtid).parent().parent().find('[id*="CGST"]').prop('disabled',true);
                      $('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').prop('disabled',true); 
                      $('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').prop('disabled',true); 
                    }
                    else{
                      $('#'+txtid).parent().parent().find('[id*="IGST"]').val(txttax1);
                      $('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').val(txttaxamt1);
                      $('#'+txtid).parent().parent().find('[id*="SGST"]').prop('disabled',true); 
                      $('#'+txtid).parent().parent().find('[id*="CGST"]').prop('disabled',true);
                      $('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').prop('disabled',true); 
                      $('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').prop('disabled',true); 
                    }

                    
                  }
                  else
                  {

                    if($("#LUT").is(":checked") == true) {
                      $('#'+txtid).parent().parent().find('[id*="CGST"]').val('0.00');
                      $('#'+txtid).parent().parent().find('[id*="IGST"]').prop('disabled',true); 
                      $('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').prop('disabled',true); 
                      $('#'+txtid).parent().parent().find('[id*="SGST"]').val('0.00');
                      $('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').val('0.00');
                      $('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').val('0.00');
                    }
                    else{
                      $('#'+txtid).parent().parent().find('[id*="CGST"]').val(txttax1);
                      $('#'+txtid).parent().parent().find('[id*="IGST"]').prop('disabled',true); 
                      $('#'+txtid).parent().parent().find('[id*="IGSTAMT"]').prop('disabled',true); 
                      $('#'+txtid).parent().parent().find('[id*="SGST"]').val(txttax2);
                      $('#'+txtid).parent().parent().find('[id*="SGSTAMT"]').val(txttaxamt2);
                      $('#'+txtid).parent().parent().find('[id*="CGSTAMT"]').val(txttaxamt1);
                    }

                    
                  }
                  var tvalue = parseFloat(txttotamtatax).toFixed(2);
                  totalvalue = $('#TotalValue').val();
                  totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
                  totalvalue = parseFloat(totalvalue).toFixed(2);
                  $('#TotalValue').val(totalvalue);
                  if($('#'+txtid).parent().parent().find('[id*="txtSC_popup"]').val() == '')
                  {
                    $('#'+txtid).parent().parent().find('[id*="SCMUOM"]').val('');
                    $('#'+txtid).parent().parent().find('[id*="SCMUOMQTY"]').val('');
                    $('#'+txtid).parent().parent().find('[id*="SCAUOM"]').val('');
                    $('#'+txtid).parent().parent().find('[id*="SCAUOMQTY"]').val('');

                    $('#'+txtid).parent().parent().find('[id*="Alpspartno"]').val('');
                    $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val('');
                    $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val('');

                  }

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
                $('#hdn_ItemID11').val('');
                $('#hdn_ItemID12').val('');
                $('#hdn_ItemID13').val('');
                $('#hdn_ItemID14').val('');
                $('#hdn_ItemID15').val('');
                $('#hdn_ItemID16').val('');
                event.preventDefault();
              }
          $("#ITEMIDpopup").hide();
          event.preventDefault();    
      }
      else if($(this).is(":checked") == false) 
      {
        var id = txtval;
        var enqid = txtenqid;
        var sqno = txtquoteno;
        var soid = txtsoid;
        var scid = txtscid;
        var r_count = $('#Row_Count1').val();
        $('#Material').find('.participantRow').each(function()
        {
          var itemid = $(this).find('[id*="ITEMID_REF"]').val();
          var enquiryid = $(this).find('[id*="SEID_REF"]').val();
          var ChallanId = $(this).find('[id*="SCID_REF"]').val();
          var quotationid = $(this).find('[id*="SQID_REF"]').val();
          var OrderId = $(this).find('[id*="SOID"]').val();
          if(id == itemid && enqid == enquiryid && sqno == quotationid && soid == OrderId && scid == ChallanId)
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
                $(document).find('.remove').prop('disabled', true);  
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
        $("#ITEMIDpopup").hide();
        event.preventDefault();
      }
    $("#Itemcodesearch").val(''); 
    $("#Itemnamesearch").val(''); 
    $("#ItemUOMsearch").val(''); 
    $("#ItemGroupsearch").val(''); 
    $("#ItemCategorysearch").val(''); 
    $("#ItemStatussearch").val(''); 
    $('.remove').removeAttr('disabled'); 
    getActionEvent();
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

      

  $('#Material').on('click','[id*="popupAUOM"]',function(event){
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
        var id2 = $(this).parent().parent().find('[id*="ALTUOMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="SIMAIN_QTY"]').attr('id');
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

      $(".clsaltuom").click(function(){
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
        var mqty = $('#'+txtid).parent().parent().find('[id*="SIMAIN_QTY"]').val();

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
        $clone.find('[id*="SOID"]').val('');
        $clone.find('[id*="SEID_REF"]').val('');
        $clone.find('[id*="SQID_REF"]').val('');
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

    $("#PaymentSlabs").on('click', '.add', function() {
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('.participantRow6').last();
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
        $tr.closest('table').append($clone);         
        var rowCount5 = $('#Row_Count5').val();
		    rowCount5 = parseInt(rowCount5)+1;
        $('#Row_Count5').val(rowCount5);
        $clone.find('.remove').removeAttr('disabled'); 
        
        event.preventDefault();
    });
    $("#PaymentSlabs").on('click', '.remove', function() {
        var rowCount5 = $(this).closest('table').find('.participantRow6').length;
        if (rowCount5 > 1) {
        $(this).closest('.participantRow6').remove();     
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


$(document).ready(function(e) {
  var Material = $("#Material").html(); 
    $('#hdnmaterial').val(Material);
    var count1 = <?php echo json_encode($objCount1); ?>;
    var count2 = <?php echo json_encode($objCount2); ?>;
    var count3 = <?php echo json_encode($objCount3); ?>;
    var count4 = <?php echo json_encode($objCount4); ?>;
    var count5 = <?php echo json_encode($objCount5); ?>;
    $('#Row_Count1').val(count1);
    $('#Row_Count2').val(count2);
    $('#Row_Count3').val(count3);
    $('#Row_Count4').val(count4);
    $('#Row_Count5').val(count5);
    var obj = <?php echo json_encode($objSIMAT); ?>;
    var objtnc = <?php echo json_encode($objSITNC); ?>;
    var tncheader = <?php echo json_encode($objTNCHeader); ?>;
    var tncdetails = <?php echo json_encode($objTNCDetails); ?>;
    var soudf = <?php echo json_encode($objSIUDF); ?>;
    var udfforso = <?php echo json_encode($objUdfSIData2); ?>;
    var calheader = <?php echo json_encode($objCalHeader); ?>;
    var caldetails = <?php echo json_encode($objCalDetails); ?>;
    var SOCal = <?php echo json_encode($objSICAL); ?>;
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
    // totalvalue = parseFloat(totalvalue).toFixed(2);


    $.each( soudf, function( soukey, souvalue ) {
        $.each( udfforso, function( usokey, usovalue ) { 
            if(souvalue.SIUDFID_REF == usovalue.UDF_SIID)
            {
                $('#popupUDFSOID_'+soukey).val(usovalue.LABEL);
            }
        
            if(souvalue.SIUDFID_REF == usovalue.UDF_SIID)
            {        
                    var txtvaltype2 =   usovalue.VALUETYPE;
                    var txt_id41 = $('#udfinputid_'+soukey).attr('id');
                    var strdyn2 = txt_id41.split('_');
                    var lastele2 =   strdyn2[strdyn2.length-1];
                    var dynamicid2 = "udfvalue_"+lastele2;
                    
                    var chkvaltype2 =  txtvaltype2.toLowerCase();
                    var strinp2 = '';

                    if(chkvaltype2=='date'){

                    strinp2 = '<input  {{$InputStatus}}  type="date" placeholder="dd/mm/yyyy" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';       

                    }
                    else if(chkvaltype2=='time'){
                    strinp2= '<input {{$InputStatus}} type="time" placeholder="h:i" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';

                    }
                    else if(chkvaltype2=='numeric'){
                    strinp2 = '<input {{$InputStatus}} type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"   > ';

                    }
                    else if(chkvaltype2=='text'){

                    strinp2 = '<input {{$InputStatus}} type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';
                    
                    }
                    else if(chkvaltype2=='boolean'){
                      if(souvalue.SOUVALUE == "1")
                      {
                        strinp2 = '<input {{$InputStatus}} type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" checked> ';
                      }
                      else{
                        strinp2 = '<input {{$InputStatus}} type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" > ';
                      }
                    }
                    else if(chkvaltype2=='combobox'){

                    var txtoptscombo2 =   usovalue.DESCRIPTIONS;
                    var strarray2 = txtoptscombo2.split(',');
                    var opts2 = '';

                    for (var i = 0; i < strarray2.length; i++) {
                        opts2 = opts2 + '<option value="'+strarray2[i]+'">'+strarray2[i]+'</option> ';
                    }

                    strinp2 = '<select {{$InputStatus}} name="'+dynamicid2+ '" id="'+dynamicid2+'" class="form-control" required>'+opts2+'</select>' ;
                   
                    }
                   
                    
                    $('#'+txt_id41).html('');  
                    $('#'+txt_id41).html(strinp2);   //set dynamic input
                    $('#'+dynamicid2).val(souvalue.SOUVALUE);
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

                    strinp = '<input {{$InputStatus}} type="date" placeholder="dd/mm/yyyy" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"  > ';       

                    }
                    else if(chkvaltype=='time'){
                    strinp= '<input {{$InputStatus}} type="time" placeholder="h:i" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"  > ';

                    }
                    else if(chkvaltype=='numeric'){
                    strinp = '<input {{$InputStatus}} type="text" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"   > ';

                    }
                    else if(chkvaltype=='text'){

                    strinp = '<input {{$InputStatus}} type="text" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"  > ';
                    
                    }
                    else if(chkvaltype=='boolean'){
                      if(tncvalue.VALUE == "1")
                      {
                        strinp = '<input {{$InputStatus}} type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" checked> ';
                      }
                      else{
                        strinp = '<input {{$InputStatus}} type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" > ';
                      }                    
                    }
                    else if(chkvaltype=='combobox'){

                    var txtoptscombo =   tncdvalue.DESCRIPTIONS;
                    var strarray = txtoptscombo.split(',');
                    var opts = '';

                    for (var i = 0; i < strarray.length; i++) {
                        opts = opts + '<option value="'+strarray[i]+'">'+strarray[i]+'</option> ';
                    }

                    strinp = '<select {{$InputStatus}} name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" required>'+opts+'</select>' ;
                   
                    }
                   
                    
                    $('#'+txt_id4).html('');  
                    $('#'+txt_id4).html(strinp);   //set dynamic input
                    $('#'+dynamicid).val(tncvalue.VALUE);
                    $('#TNCismandatory_'+tnckey).val(tncdvalue.IS_MANDATORY); // mandatory
                
            }
        });
    });
    
    var totalvalue = 0.00;
      var tvalue = 0.00;
      var ctvalue = 0.00;
      var ctgstvalue = 0.00;
      $('#Material').find('.participantRow').each(function()
      {
        tvalue = $(this).find('[id*="TOT_AMT_"]').val();
        totalvalue = parseFloat(totalvalue) + parseFloat(tvalue);
        totalvalue = parseFloat(totalvalue).toFixed(2);
      });
      if($('#CTID_REF').val() != '')
      {
        $('#CT').find('.participantRow5').each(function()
        {
          ctvalue = $(this).find('[id*="VALUE"]').val();
          ctgstvalue = $(this).find('[id*="TOTGSTAMT"]').val();
          if($('#GST_Reverse').is(':checked') == true)
          {
            totalvalue = parseFloat(totalvalue) + parseFloat(ctvalue);
            totalvalue = parseFloat(totalvalue).toFixed(2);
          }
          else
          {
            totalvalue = parseFloat(totalvalue) + parseFloat(ctvalue);
            totalvalue = parseFloat(totalvalue) + parseFloat(ctgstvalue);
            totalvalue = parseFloat(totalvalue).toFixed(2);
          }
        });
      }      
      $('#TotalValue').val(totalvalue);

    var d = new Date(); 
    var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    d.setDate(d.getDate() + 29);
    var todate = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
    // $('#SODT').val(today);
    // $('#OVFDT').val(today);
    // $('#OVTDT').val(todate);
    // $('#CUSTOMERDT').val(today);
    
    
    
    $('#Material').on('keyup',"[id*='ALT_UOMID_QTY']",function()
    {
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.000')
      }
      event.preventDefault();
    });


    $('#Material').on('focusout',"[id*='SIMAIN_QTY']",function()
    {
      var scqty = $(this).parent().parent().find('[id*="SCMUOMQTY"]').val(); 
		if($(this).val() > scqty)
		{
			$("#FocusId").val($(this));
			$("#ProceedBtn").focus();
			$("#YesBtn").hide();
			$("#NoBtn").hide();
			$("#OkBtn1").show();
			$("#AlertMessage").text('Please Invoice Quantity cannot be greater than Challan Quantity.');
			$("#alert").modal('show');
			$("#OkBtn1").focus();
			highlighFocusBtn('activeOk1');
			$(this).val('0.000');
			//return false;
		}
		else
		{
				var totalvalue = 0.00;
				var itemid = $(this).parent().parent().find('[id*="ITEMID_REF"]').val();
				var mqty = $(this).val();
				var altuomid = $(this).parent().parent().find('[id*="ALTUOMID_REF"]').val();
				var txtid = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
				var irate = $(this).parent().parent().find('[id*="RATEPUOM"]').val();
				$(this).parent().parent().find('[id*="IGSTAMT"]').val('0');
				$(this).parent().parent().find('[id*="CGSTAMT"]').val('0');
				$(this).parent().parent().find('[id*="SGSTAMT"]').val('0');
				var tamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);
				var dispercnt = $(this).parent().parent().find('[id*="DISPER"]').val();
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
		}
    });   

    $('#Material').on('focusout',"[id*='DISPER']",function()
    { 
      var mqty = $(this).parent().parent().find('[id*="SIMAIN_QTY"]').val();
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
      $(this).parent().parent().find('[id*="DISPER"]').prop('readonly',true);
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
      var mqty = $(this).parent().parent().find('[id*="SIMAIN_QTY"]').val();
      var irate = $(this).parent().parent().find('[id*="RATEPUOM"]').val();
      var totamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);
      var dpert = $(this).val();
      var dispercent = $(this).parent().parent().find('[id*="DISPER"]').val();
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
        $(this).parent().parent().find('[id*="DISPER"]').prop('disabled',true);
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
        $(this).parent().parent().find('[id*="DISPER"]').removeAttr('disabled');
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
        var viewURL = '{{route("transaction",[44,"add"])}}';
                  window.location.href=viewURL;
    });
    $('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });
     

//SO Date Check
// $('#SIDT').change(function( event ) {
//             var today = new Date();     
//             var d = new Date($(this).val()); 
//             today.setHours(0, 0, 0, 0) ;
//             d.setHours(0, 0, 0, 0) ;
//             var sodate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
//             if (d < today) {
//                 $(this).val(sodate);
//                 $("#alert").modal('show');
//                 $("#AlertMessage").text('SO Date cannot be less than Current date');
//                 $("#YesBtn").hide(); 
//                 $("#NoBtn").hide();  
//                 $("#OkBtn1").show();
//                 $("#OkBtn1").focus();
//                 highlighFocusBtn('activeOk1');
//                 event.preventDefault();
//             }
//             else
//             {
//                 event.preventDefault();
//             }

           
//         });
//SO Date Check


$('#PaymentSlabs').on('focusout','[id*="DUE_PERCENT_"]',function( event ) {
  var tot_percent = 0;
    $('#PaymentSlabs').find('.participantRow6').each(function(){
      var duepercent = $(this).find('[id*="DUE_PERCENT_"]').val();
      tot_percent = parseFloat(tot_percent)+parseFloat(duepercent);
      tot_percent = parseFloat(tot_percent).toFixed(4);
    });
    if(parseFloat(tot_percent).toFixed(4) > 100.0000 )
    {
                $(this).val('');
                $("#alert").modal('show');
                $("#AlertMessage").text('Due % cannot be greater than 100');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                event.preventDefault();
    }
    var d = $(this).val(); 
    if(intRegex.test($(this).val())){
              $(this).val($(this).val()+'.0000')
            }
    var total =$('#TotalValue').val();
    var DueAmt = parseFloat(parseFloat(parseFloat(d)*parseFloat(total))/100).toFixed(2);
    $(this).parent().parent().find('[id*="DUE_AMT_"]').val(DueAmt);
    event.preventDefault();
});

$('#PaymentSlabs').on('change','[id*="PAY_DAYS"]',function( event ) {
            var d = $(this).val(); 
            d = parseInt(d) - 1;
            var sdate =$('#SIDT').val();
            var ddate = new Date(sdate);
            var newddate = new Date(ddate);
            newddate.setDate(newddate.getDate() + d);
            var soddate = newddate.getFullYear() + "-" + ("0" + (newddate.getMonth() + 1)).slice(-2) + "-" + ('0' + newddate.getDate()).slice(-2) ;
            $(this).parent().parent().find('[id*="DUE_DATE"]').val(soddate);

            event.preventDefault();
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
      $("#SINO").focus();
   }//fnUndoNo


  //  $("#SOFC").change(function() {
  //     if ($(this).is(":checked") == true){
  //         $(this).parent().parent().find('#txtCRID_popup').removeAttr('disabled');
  //         $(this).parent().parent().find('#txtCRID_popup').prop('readonly','true');
  //         event.preventDefault();
  //     }
  //     else
  //     {
  //         $(this).parent().parent().find('#txtCRID_popup').prop('disabled','true');
  //         $(this).parent().parent().find('#txtCRID_popup').removeAttr('readonly');
  //         $(this).parent().parent().find('#txtCRID_popup').val('');
  //         $(this).parent().parent().find('#CRID_REF').val('');
  //         $(this).parent().parent().find('#CONVFACT').val('');
  //         event.preventDefault();
  //     }
  // });

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


  var lastdt = <?php echo json_encode($objlastSIDT[0]->SIDT); ?>;
  var si = <?php echo json_encode($objSI); ?>;
  var today = new Date(); 
  var sidate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2);
  if(lastdt < si.SIDT)
  {
	$('#SIDT').attr('min',lastdt);
  }
  else
  {
	  $('#SIDT').attr('min',si.SIDT);
  }
  $('#SIDT').attr('max',sidate);



    $('#frm_trn_si1').bootstrapValidator({
       
        fields: {
            txtlabel: {
                validators: {
                    notEmpty: {
                        message: 'The SI NO is required'
                    }
                }
            },            
        },
        submitHandler: function(validator, form, submitButton) {
            alert( "Handler for .submit() called." );
             event.preventDefault();
             $("#frm_trn_si").submit();
        }
    });
});
$( "#btnSaveSI" ).click(function() {
  var formSalesInvoice = $("#frm_trn_si");
  if(formSalesInvoice.valid()){
 
    $("#FocusId").val('');
	var TYPE           =   $.trim($("#TYPE").val());
    var SINO           =   $.trim($("#SINO").val());
    var SIDT           =   $.trim($("#SIDT").val());
    var GLID_REF       =   $.trim($("#GLID_REF").val());
    var SLID_REF       =   $.trim($("#SLID_REF").val());

    for ( instance in CKEDITOR.instances ) {
            CKEDITOR.instances.Template_Description.updateElement();
        }
    

    if(TYPE ===""){
     $("#FocusId").val($("#TYPE"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select TYPE.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(SINO ===""){
        $("#FocusId").val($("#SINO"));
        $("#SONO").val(''); 
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please enter value in SINO.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }
    else if(SIDT ===""){
        $("#FocusId").val($("#SIDT"));
        $("#SODT").val('');  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select SI Date.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }     
    else if(GLID_REF ===""){
        $("#FocusId").val($("#GLID_REF"));
        $("#GLID_REF").val('');  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select Customer.');
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
        var allblank6 = [];
        var allblank7 = [];
        var allblank8 = [];
        var allblank9 = [];
        var allblank10 = [];
        var allblank11 = [];
        var allblank12 = [];
            // $('#udfforsebody').find('.form-control').each(function () {
            $('#Material').find('.participantRow').each(function(){
                if($.trim($(this).find("[id*=ITEMID_REF]").val())!="")
                {
                    allblank.push('true');
                        if($.trim($(this).find("[id*=popupMUOM]").val())!=""){
                            allblank2.push('true');
                              if($.trim($(this).find('[id*="SIMAIN_QTY"]').val()) != "" && $.trim($(this).find('[id*="SIMAIN_QTY"]').val()) != "0.000"
									&& $.trim($(this).find('[id*="SIMAIN_QTY"]').val()) != ".000")
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
                if($.trim($('#Tax_State').val())!="WithinState")
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
            $('#udf').find('.participantRow4').each(function(){
                  if($.trim($(this).find("[id*=SIUDFID_REF]").val())!="")
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
            if($('#CTID_REF').val() !="")
            {
                $('#CT').find('.participantRow5').each(function(){
                  if($.trim($(this).find("[id*=TID_REF]").val())!="")
                    {
                        allblank10.push('true');
                            if($(this).find("[id*=calGST]").is(":checked") == true)
                            {
                              if($.trim($('#Tax_State').val())!="WithinState")
                              {
                                if($.trim($(this).find("[id*=calIGST]").val())!="")
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
                                if($.trim($(this).find("[id*=calCGST]").val())!="")
                                {
                                  allblank11.push('true');
                                }
                                else
                                {
                                  allblank11.push('false');
                                }
                                if($.trim($(this).find("[id*=calSGST]").val())!="")
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
            $('#PaymentSlabs').find('.participantRow6').each(function(){
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
            if(jQuery.inArray("false", allblank) !== -1){
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Please select item in Material Tab.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank2) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Main UOM under Sales Invoice section is missing in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank3) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Main UOM Quantity under Sales Invoice section is missing in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank4) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter Rate per UOM in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank5) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter GST Rate / Value in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank6) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please select Terms & Condition Description in T&C Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank7) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter Value / Comment in T&C Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank9) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter  Value / Comment in UDF Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank10) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please select Calculation Component in Calculation Template Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank11) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please Enter GST Rate / Value in Calculation Template Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank12) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please Enter Due % in Payment Slabs Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(checkPeriodClosing(44,$("#SIDT").val(),0) ==0){
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
                    $("#AlertMessage").text('Do you want to save the record.');
                    $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                    $("#YesBtn").focus();

                    $("#OkBtn").hide();
                    highlighFocusBtn('activeYes');

                }
            

        }

    }
});
$( "#btnApprove" ).click(function() {
  var formSalesInvoice = $("#frm_trn_si");
  if(formSalesInvoice.valid()){
 
    $("#FocusId").val('');
	var TYPE           =   $.trim($("#TYPE").val());
    var SINO           =   $.trim($("#SINO").val());
    var SIDT           =   $.trim($("#SIDT").val());
    var GLID_REF       =   $.trim($("#GLID_REF").val());
    var SLID_REF       =   $.trim($("#SLID_REF").val());

    for ( instance in CKEDITOR.instances ) {
            CKEDITOR.instances.Template_Description.updateElement();
        }
    

    if(TYPE ===""){
     $("#FocusId").val($("#TYPE"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select TYPE.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(SINO ===""){
        $("#FocusId").val($("#SINO"));
        $("#SONO").val(''); 
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please enter value in SINO.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }
    else if(SIDT ===""){
        $("#FocusId").val($("#SIDT"));
        $("#SODT").val('');  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select SI Date.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }     
    else if(GLID_REF ===""){
        $("#FocusId").val($("#GLID_REF"));
        $("#GLID_REF").val('');  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select GL Account.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    } 
    else if(SLID_REF ===""){
        $("#FocusId").val($("#SLID_REF"));
        $("#SLID_REF").val('');  
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select Sub GL Account.');
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
        var allblank6 = [];
        var allblank7 = [];
        var allblank8 = [];
        var allblank9 = [];
        var allblank10 = [];
        var allblank11 = [];
        var allblank12 = [];
            // $('#udfforsebody').find('.form-control').each(function () {
            $('#Material').find('.participantRow').each(function(){
                if($.trim($(this).find("[id*=ITEMID_REF]").val())!="")
                {
                    allblank.push('true');
                        if($.trim($(this).find("[id*=popupMUOM]").val())!=""){
                            allblank2.push('true');
                              if($.trim($(this).find('[id*="SIMAIN_QTY"]').val()) != "" && $.trim($(this).find('[id*="SIMAIN_QTY"]').val()) != "0.000"
									&& $.trim($(this).find('[id*="SIMAIN_QTY"]').val()) != ".000")
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
                if($.trim($('#Tax_State').val())!="WithinState")
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
            $('#udf').find('.participantRow4').each(function(){
                  if($.trim($(this).find("[id*=SIUDFID_REF]").val())!="")
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
            if($('#CTID_REF').val() !="")
            {
                $('#CT').find('.participantRow5').each(function(){
                  if($.trim($(this).find("[id*=TID_REF]").val())!="")
                    {
                        allblank10.push('true');
                            if($(this).find("[id*=calGST]").is(":checked") == true)
                            {
                              if($.trim($('#Tax_State').val())!="WithinState")
                              {
                                if($.trim($(this).find("[id*=calIGST]").val())!="")
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
                                if($.trim($(this).find("[id*=calCGST]").val())!="")
                                {
                                  allblank11.push('true');
                                }
                                else
                                {
                                  allblank11.push('false');
                                }
                                if($.trim($(this).find("[id*=calSGST]").val())!="")
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
            $('#PaymentSlabs').find('.participantRow6').each(function(){
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
            if(jQuery.inArray("false", allblank) !== -1){
                    $("#alert").modal('show');
                    $("#AlertMessage").text('Please select item in Material Tab.');
                    $("#YesBtn").hide(); 
                    $("#NoBtn").hide();  
                    $("#OkBtn1").show();
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank2) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Main UOM under Sales Invoice section is missing in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank3) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Main UOM Quantity under Sales Invoice section is missing in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank4) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter Rate per UOM in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank5) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter GST Rate / Value in Material Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank6) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please select Terms & Condition Description in T&C Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank7) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter Value / Comment in T&C Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank9) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please enter  Value / Comment in UDF Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank10) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please select Calculation Component in Calculation Template Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank11) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please Enter GST Rate / Value in Calculation Template Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(jQuery.inArray("false", allblank12) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Please Enter Due % in Payment Slabs Tab.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                }
                else if(checkPeriodClosing(44,$("#SIDT").val(),0) ==0){
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
                    $("#AlertMessage").text('Do you want to save the record.');
                    $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name
                    $("#YesBtn").focus();

                    $("#OkBtn").hide();
                    highlighFocusBtn('activeYes');

                }
            

        }

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

     var trnsiForm = $("#frm_trn_si");
    var formData = trnsiForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$("#btnSaveSI").hide(); 
$(".buttonload").show(); 
$("#btnApprove").prop("disabled", true);
$.ajax({
    url:'{{ route("transactionmodify",[44,"update"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $(".buttonload").hide(); 
      $("#btnSaveSI").show();   
      $("#btnApprove").prop("disabled", false);
       
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.SINO){
                showError('ERROR_SINO',data.errors.SINO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in SINO.');
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
      $("#btnSaveSI").show();   
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

     var trnsiForm = $("#frm_trn_si");
    var formData = trnsiForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$("#btnApprove").hide(); 
$(".buttonload_approve").show();  
$("#btnSaveSI").prop("disabled", true);
$.ajax({
    url:'{{ route("transactionmodify",[44,"Approve"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $("#btnApprove").show();  
      $(".buttonload_approve").hide();  
      $("#btnSaveSI").prop("disabled", false);
       
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.SINO){
                showError('ERROR_SINO',data.errors.SINO);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in SINO.');
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
      $("#btnSaveSI").prop("disabled", false);
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
    $("#SONO").focus();
});

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("transaction",[44,"index"]) }}';
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


function showHideLutApplicable(id){

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$.ajax({
  url:'{{route("transaction",[$FormId,"showHideLutApplicable"])}}',
  type:'POST',
  data:{'id':id},
  success:function(data){

    if(data !=''){
      $("#LUT_STATUS").show();
    }
    else{
      $("#LUT_STATUS").hide();
      
    }

    $("#LUT").prop('checked', false);
    $("#LUT_NO").val('');
    $("#LUT_NO").prop('readonly',true);

  },
  error:function(data){
    console.log("Error: Something went wrong.");                   
  },
});

}

function getLutApplicable(){
  if($("#LUT").is(":checked") == true) {
    $("#LUT_NO").prop('readonly',false);
    reloadMaterialTab()
  }
  else{
    $("#LUT_NO").val('');
    $("#LUT_NO").prop('readonly',true);
    reloadMaterialTab()
  }
}

function reloadMaterialTab(){
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

$("#SOFC").change(function() {
if ($(this).is(":checked") == true){
    $(this).parent().parent().find('#txtCRID_popup').removeAttr('disabled');
    $(this).parent().parent().find('#txtCRID_popup').prop('readonly','true');
    event.preventDefault();
}
else{
    $(this).parent().parent().find('#txtCRID_popup').prop('disabled','true');
    $(this).parent().parent().find('#txtCRID_popup').removeAttr('readonly');
    $(this).parent().parent().find('#txtCRID_popup').val('');
    $(this).parent().parent().find('#CRID_REF').val('');
    $(this).parent().parent().find('#CONVFACT').val('');
    event.preventDefault();
}
});

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

function CurrencyNameFunction() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("currencynamesearch");
    filter = input.value.toUpperCase();
    table = document.getElementById("CurrencyTable2");
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

$('#txtCRID_popup').click(function(event){
$("#cridpopup").show();
});

$("#crid_closePopup").click(function(event){
$("#cridpopup").hide();
});

$(".clscrid").click(function(){
var fieldid = $(this).attr('id');
var txtval =    $("#txt"+fieldid+"").val();
var texdesc =   $("#txt"+fieldid+"").data("desc")+'-'+$("#txt"+fieldid+"").data("desc2");

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
  var today = d.getFullYear() + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + d.getDate();
  
    if (fcurrency == ccvalue.TOCRID_REF && dcurrency == ccvalue.FROMCRID_REF && fromdate <= today && enddate >= today)
    {
      $('#CONVFACT').val(ccvalue.TOAMOUNT);
      $('#CONVFACT').prop('readonly','true');
      return false;
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

$(document).ready(function(){
  getActionEvent();
  CKEDITOR.replace( 'Template_Description' );
  <?php if(isset($objSI->EXE_GST) && $objSI->EXE_GST =='1'){?>
    taxStatusWiseTaxCalculation();
    $(".ExceptionalGST").show();
    $("#EXE_GST").prop('checked', true);
  <?php } ?>


  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"showHideLutApplicable"])}}',
    type:'POST',
    data:{'id':'{{ isset($objSI->SLID_REF)?$objSI->SLID_REF:'' }}'},
    success:function(data){

      if(data !=''){
        $("#LUT_STATUS").show();
      }
      else{
        $("#LUT_STATUS").hide();
        
      }
    },
    error:function(data){
      console.log("Error: Something went wrong.");                   
    },
  });

});





//Template Master 
let tempid = "#TemplateIDTable2";
let tempid2 = "#TemplateIDTable";
let tempheaders = document.querySelectorAll(tempid2 + " th");

// Sort the table element when clicking on the table headers
tempheaders.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(tempid, ".clsTemplateid", "td:nth-child(" + (i + 1) + ")");
  });
});

function TemplateCodeFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Templatecodesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("TemplateTable2");
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

function TemplateDateFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("Templatenamesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("TemplateTable2");
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

$('#ADDITIONAL').on('click','#txtTemplate_popup',function(event){
var hidden_value=$('#TEMPID_REF').val();
showSelectedCheck(hidden_value,'SELECT_TEMPLATE'); 
      $("#Templatepopup").show();
      event.preventDefault();
  });

  $("#Template_closePopup").click(function(event){
    $("#Templatepopup").hide();
  });

  $(".clstemplateid").click(function(){

    var fieldid = $(this).attr('id');
    var txtval =    $("#txt"+fieldid+"").val();
    var texdesc =   $("#txt"+fieldid+"").data("desc");
    var template_name=$("#txt"+fieldid+"").data("desc");
    var template_desc=$("#txt"+fieldid+"").data("desc3");

    // var txtid= $('#hdn_fieldid').val();
    // var txt_id2= $('#hdn_fieldid2').val();
    
    $('#txtTemplate_popup').val(texdesc);
    $('#TEMPID_REF').val(txtval);
    CKEDITOR.instances.Template_Description.setData(template_desc)
    $("#Templatepopup").hide();
    $("#Templatecodesearch").val(''); 
    $("#Templatenamesearch").val('');         
    // event.preventDefault();
  });
// End Template Master

$("#TDS").on('change', "[id*='TDSApplicable']", function(){
  var totalamount = 0.00;
  if($(this).is(':checked') == true)
  {

    $(this).parent().parent().find('[id*="TDS_RATE"]').removeAttr('readonly');

    var taxamt12 = 0.00;
    $('#Material').find('.participantRow').each(function()
    {
        if($(this).find('[id*="DISAFTT_AMT"]').val() != '')
        {
          var taxamt21 = $(this).find('[id*="DISAFTT_AMT"]').val();
          taxamt12 = parseFloat(parseFloat(taxamt12)+parseFloat(taxamt21)).toFixed(2);
        }
    });
    if($('#CTID_REF').val() != '')
    {
      $('#CT').find('.participantRow5').each(function()
      {
        var ctvalue = $(this).find('[id*="VALUE"]').val();
        taxamt12 = parseFloat(parseFloat(taxamt12)+parseFloat(ctvalue)).toFixed(2);
      });
    }
    $(this).parent().parent().find("[id*='ASSESSABLE_VL_']").val(taxamt12);
    var tdsamt = 0.00;
    var tdsrate = $(this).parent().parent().find("[id*='TDS_RATE_']").val();
    var tdsexempt = $(this).parent().parent().find("[id*='TDS_EXEMPT_']").val();
    if (parseFloat(taxamt12) > parseFloat(tdsexempt))
    {
        tdsamt = parseFloat(((parseFloat(taxamt12) - parseFloat(tdsexempt))*parseFloat(tdsrate))/100).toFixed(2);
    }
    else
    {
      tdsamt =  0.00;
    }
    $(this).parent().parent().find("[id*='TDS_AMT_']").val(tdsamt);

    var SURCHARGEamt = 0.00;
    var SURCHARGErate = $(this).parent().parent().find("[id*='SURCHARGE_RATE_']").val();
    var SURCHARGEexempt = $(this).parent().parent().find("[id*='SURCHARGE_EXEMPT_']").val();
    if (parseFloat(taxamt12) > parseFloat(SURCHARGEexempt))
    {
        SURCHARGEamt = parseFloat(((parseFloat(taxamt12) - parseFloat(SURCHARGEexempt))*parseFloat(SURCHARGErate))/100).toFixed(2);
    }
    else
    {
      SURCHARGEamt =  0.00;
    }
    $(this).parent().parent().find("[id*='SURCHARGE_AMT_']").val(SURCHARGEamt);

    var CESSamt = 0.00;
    var CESSrate = $(this).parent().parent().find("[id*='CESS_RATE_']").val();
    var CESSexempt = $(this).parent().parent().find("[id*='CESS_EXEMPT_']").val();
    if (parseFloat(taxamt12) > parseFloat(CESSexempt))
    {
        CESSamt = parseFloat(((parseFloat(taxamt12) - parseFloat(CESSexempt))*parseFloat(CESSrate))/100).toFixed(2);
    }
    else
    {
      CESSamt =  0.00;
    }
    $(this).parent().parent().find("[id*='CESS_AMT_']").val(CESSamt);

    var SPCESSamt = 0.00;
    var SPCESSrate = $(this).parent().parent().find("[id*='SPCESS_RATE_']").val();
    var SPCESSexempt = $(this).parent().parent().find("[id*='SPCESS_EXEMPT_']").val();
    if (parseFloat(taxamt12) > parseFloat(SPCESSexempt))
    {
        SPCESSamt = parseFloat(((parseFloat(taxamt12) - parseFloat(SPCESSexempt))*parseFloat(SPCESSrate))/100).toFixed(2);
    }
    else
    {
      SPCESSamt =  0.00;
    }
    $(this).parent().parent().find("[id*='SPCESS_AMT_']").val(SPCESSamt);

    var totalTDSamt = 0.00;
    totalTDSamt = parseFloat(parseFloat(tdsamt) + parseFloat(SURCHARGEamt) + parseFloat(CESSamt) + parseFloat(SPCESSamt)).toFixed(2);
    $(this).parent().parent().find("[id*='TOT_TD_AMT']").val(totalTDSamt);
  }
  else
  {
    $(this).parent().parent().find('[id*="TDS_RATE_"]').prop('readonly',true);
    $(this).parent().parent().find("[id*='ASSESSABLE_VL_']").val('0.00');
    $(this).parent().parent().find("[id*='AMT_']").val('0.00');
  }
  bindTotalValue();
  if($('#CTID_REF').val()!='')
  {
    bindGSTCalTemplate();
  }
  bindTotalValue();
  if($('#TotalValue').val() < '0.00')
  {
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Total Amount must be greater than Zero. Kindly check values in Material, Calculation Template & TDS Tab.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');
    return false;
  }
  event.preventDefault();
});


$("#TDS").on('focusout', "[id*='ASSESSABLE_VL_']", function(){
  var totalamount = 0.00;
  if(intRegex.test($(this).val())){
    $(this).val($(this).val() +'.00');
  }
    
  var taxamtTDS =  $(this).parent().parent().find("[id*='ASSESSABLE_VL_TDS']").val();
  var tdsamt = 0.00;
  var tdsrate = $(this).parent().parent().find("[id*='TDS_RATE_']").val();
  var tdsexempt = $(this).parent().parent().find("[id*='TDS_EXEMPT_']").val();

  if (parseFloat(taxamtTDS) > parseFloat(tdsexempt)){
    tdsamt = parseFloat(((parseFloat(taxamtTDS) - parseFloat(tdsexempt))*parseFloat(tdsrate))/100).toFixed(2);
  }
  else{
    tdsamt =  0.00;
  }

  $(this).parent().parent().find("[id*='TDS_AMT_']").val(tdsamt);

    var taxamtSURCHARGE =  $(this).parent().parent().find("[id*='ASSESSABLE_VL_SURCHARGE']").val();
    var SURCHARGEamt = 0.00;
    var SURCHARGErate = $(this).parent().parent().find("[id*='SURCHARGE_RATE_']").val();
    var SURCHARGEexempt = $(this).parent().parent().find("[id*='SURCHARGE_EXEMPT_']").val();
    if (parseFloat(taxamtSURCHARGE) > parseFloat(SURCHARGEexempt))
    {
        SURCHARGEamt = parseFloat(((parseFloat(taxamtSURCHARGE) - parseFloat(SURCHARGEexempt))*parseFloat(SURCHARGErate))/100).toFixed(2);
    }
    else
    {
      SURCHARGEamt =  0.00;
    }
    $(this).parent().parent().find("[id*='SURCHARGE_AMT_']").val(SURCHARGEamt);

    var taxamtCESS =  $(this).parent().parent().find("[id*='ASSESSABLE_VL_CESS']").val();
    var CESSamt = 0.00;
    var CESSrate = $(this).parent().parent().find("[id*='CESS_RATE_']").val();
    var CESSexempt = $(this).parent().parent().find("[id*='CESS_EXEMPT_']").val();
    if (parseFloat(taxamtCESS) > parseFloat(CESSexempt))
    {
        CESSamt = parseFloat(((parseFloat(taxamtCESS) - parseFloat(CESSexempt))*parseFloat(CESSrate))/100).toFixed(2);
    }
    else
    {
      CESSamt =  0.00;
    }
    $(this).parent().parent().find("[id*='CESS_AMT_']").val(CESSamt);

    var taxamtSPCESS =  $(this).parent().parent().find("[id*='ASSESSABLE_VL_SPCESS']").val();
    var SPCESSamt = 0.00;
    var SPCESSrate = $(this).parent().parent().find("[id*='SPCESS_RATE_']").val();
    var SPCESSexempt = $(this).parent().parent().find("[id*='SPCESS_EXEMPT_']").val();
    if (parseFloat(taxamtSPCESS) > parseFloat(SPCESSexempt))
    {
        SPCESSamt = parseFloat(((parseFloat(taxamtSPCESS) - parseFloat(SPCESSexempt))*parseFloat(SPCESSrate))/100).toFixed(2);
    }
    else
    {
      SPCESSamt =  0.00;
    }
    $(this).parent().parent().find("[id*='SPCESS_AMT_']").val(SPCESSamt);

    var totalTDSamt = 0.00;
    totalTDSamt = parseFloat(parseFloat(tdsamt) + parseFloat(SURCHARGEamt) + parseFloat(CESSamt) + parseFloat(SPCESSamt)).toFixed(2);
    $(this).parent().parent().find("[id*='TOT_TD_AMT']").val(totalTDSamt);

    bindTotalValue();
    if($('#CTID_REF').val()!='')
    {
      bindGSTCalTemplate();
    }
    bindTotalValue();
    if($('#TotalValue').val() < '0.00')
    {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Total Amount must be greater than Zero. Kindly check values in Material, Calculation Template & TDS Tab.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
      return false;
    }
  event.preventDefault();
});

$("#TDS").on('focusout', "[id*='TDS_RATE_']", function(){
  var totalamount = 0.00;
  if(intRegex.test($(this).val())){
    $(this).val($(this).val() +'.00');
  }
    
  var taxamtTDS =  $(this).parent().parent().find("[id*='ASSESSABLE_VL_TDS']").val();
  var tdsamt = 0.00;
  var tdsrate = $(this).parent().parent().find("[id*='TDS_RATE_']").val();
  var tdsexempt = $(this).parent().parent().find("[id*='TDS_EXEMPT_']").val();

  if (parseFloat(taxamtTDS) > parseFloat(tdsexempt)){
    tdsamt = parseFloat(((parseFloat(taxamtTDS) - parseFloat(tdsexempt))*parseFloat(tdsrate))/100).toFixed(2);
  }
  else{
    tdsamt =  0.00;
  }

  $(this).parent().parent().find("[id*='TDS_AMT_']").val(tdsamt);

    var taxamtSURCHARGE =  $(this).parent().parent().find("[id*='ASSESSABLE_VL_SURCHARGE']").val();
    var SURCHARGEamt = 0.00;
    var SURCHARGErate = $(this).parent().parent().find("[id*='SURCHARGE_RATE_']").val();
    var SURCHARGEexempt = $(this).parent().parent().find("[id*='SURCHARGE_EXEMPT_']").val();
    if (parseFloat(taxamtSURCHARGE) > parseFloat(SURCHARGEexempt))
    {
        SURCHARGEamt = parseFloat(((parseFloat(taxamtSURCHARGE) - parseFloat(SURCHARGEexempt))*parseFloat(SURCHARGErate))/100).toFixed(2);
    }
    else
    {
      SURCHARGEamt =  0.00;
    }
    $(this).parent().parent().find("[id*='SURCHARGE_AMT_']").val(SURCHARGEamt);

    var taxamtCESS =  $(this).parent().parent().find("[id*='ASSESSABLE_VL_CESS']").val();
    var CESSamt = 0.00;
    var CESSrate = $(this).parent().parent().find("[id*='CESS_RATE_']").val();
    var CESSexempt = $(this).parent().parent().find("[id*='CESS_EXEMPT_']").val();
    if (parseFloat(taxamtCESS) > parseFloat(CESSexempt))
    {
        CESSamt = parseFloat(((parseFloat(taxamtCESS) - parseFloat(CESSexempt))*parseFloat(CESSrate))/100).toFixed(2);
    }
    else
    {
      CESSamt =  0.00;
    }
    $(this).parent().parent().find("[id*='CESS_AMT_']").val(CESSamt);

    var taxamtSPCESS =  $(this).parent().parent().find("[id*='ASSESSABLE_VL_SPCESS']").val();
    var SPCESSamt = 0.00;
    var SPCESSrate = $(this).parent().parent().find("[id*='SPCESS_RATE_']").val();
    var SPCESSexempt = $(this).parent().parent().find("[id*='SPCESS_EXEMPT_']").val();
    if (parseFloat(taxamtSPCESS) > parseFloat(SPCESSexempt))
    {
        SPCESSamt = parseFloat(((parseFloat(taxamtSPCESS) - parseFloat(SPCESSexempt))*parseFloat(SPCESSrate))/100).toFixed(2);
    }
    else
    {
      SPCESSamt =  0.00;
    }
    $(this).parent().parent().find("[id*='SPCESS_AMT_']").val(SPCESSamt);

    var totalTDSamt = 0.00;
    totalTDSamt = parseFloat(parseFloat(tdsamt) + parseFloat(SURCHARGEamt) + parseFloat(CESSamt) + parseFloat(SPCESSamt)).toFixed(2);
    $(this).parent().parent().find("[id*='TOT_TD_AMT']").val(totalTDSamt);

    bindTotalValue();
    if($('#CTID_REF').val()!='')
    {
      bindGSTCalTemplate();
    }
    bindTotalValue();
    if($('#TotalValue').val() < '0.00')
    {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Total Amount must be greater than Zero. Kindly check values in Material, Calculation Template & TDS Tab.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
      return false;
    }
  event.preventDefault();
});

function getActionEvent(){
  getTotalRowValue();
  taxStatusWiseTaxCalculation();
  reverse_gst();
}

function getTotalRowValue(){

  var SIMAIN_QTY        = 0;
  var ALT_UOMID_QTY = 0;
  var RATEPUOM      = 0; 
  var DISCOUNT_AMT  = 0;
  var DISAFTT_AMT   = 0;
  var IGSTAMT       = 0;
  var CGSTAMT       = 0;
  var SGSTAMT       = 0;
  var TGST_AMT      = 0;
  var TOT_AMT       = 0;

  $('#Material').find('.participantRow').each(function(){
    SIMAIN_QTY  = $(this).find('[id*="SIMAIN_QTY"]').val() > 0? SIMAIN_QTY+parseFloat($(this).find('[id*="SIMAIN_QTY"]').val()):SIMAIN_QTY;
   
    ALT_UOMID_QTY = $(this).find('[id*="ALT_UOMID_QTY"]').val() > 0?ALT_UOMID_QTY+parseFloat($(this).find('[id*="ALT_UOMID_QTY"]').val()):ALT_UOMID_QTY;
    RATEPUOM      = $(this).find('[id*="RATEPUOM"]').val() > 0?RATEPUOM+parseFloat($(this).find('[id*="RATEPUOM"]').val()):RATEPUOM;
    DISCOUNT_AMT  = $(this).find('[id*="DISCOUNT_AMT"]').val() > 0?DISCOUNT_AMT+parseFloat($(this).find('[id*="DISCOUNT_AMT"]').val()):DISCOUNT_AMT;
    DISAFTT_AMT   = $(this).find('[id*="DISAFTT_AMT"]').val() > 0?DISAFTT_AMT+parseFloat($(this).find('[id*="DISAFTT_AMT"]').val()):DISAFTT_AMT;
    IGSTAMT       = $(this).find('[id*="IGSTAMT"]').val() > 0?IGSTAMT+parseFloat($(this).find('[id*="IGSTAMT"]').val()):IGSTAMT;
    CGSTAMT       = $(this).find('[id*="CGSTAMT"]').val() > 0?CGSTAMT+parseFloat($(this).find('[id*="CGSTAMT"]').val()):CGSTAMT;
    SGSTAMT       = $(this).find('[id*="SGSTAMT"]').val() > 0?SGSTAMT+parseFloat($(this).find('[id*="SGSTAMT"]').val()):SGSTAMT;
    TGST_AMT      = $(this).find('[id*="TGST_AMT"]').val() > 0?TGST_AMT+parseFloat($(this).find('[id*="TGST_AMT"]').val()):TGST_AMT;
    TOT_AMT       = $(this).find('[id*="TOT_AMT"]').val() > 0?TOT_AMT+parseFloat($(this).find('[id*="TOT_AMT"]').val()):TOT_AMT;
  });



  SIMAIN_QTY          = SIMAIN_QTY > 0?parseFloat(SIMAIN_QTY).toFixed(3):'';
  ALT_UOMID_QTY   = ALT_UOMID_QTY > 0?parseFloat(ALT_UOMID_QTY).toFixed(3):'';
  RATEPUOM        = RATEPUOM > 0?parseFloat(RATEPUOM).toFixed(5):'';
  DISCOUNT_AMT    = DISCOUNT_AMT > 0?parseFloat(DISCOUNT_AMT).toFixed(2):'';
  DISAFTT_AMT     = DISAFTT_AMT > 0?parseFloat(DISAFTT_AMT).toFixed(2):'';
  IGSTAMT         = IGSTAMT > 0?parseFloat(IGSTAMT).toFixed(2):'';
  CGSTAMT         = CGSTAMT > 0?parseFloat(CGSTAMT).toFixed(2):'';
  SGSTAMT         = SGSTAMT > 0?parseFloat(SGSTAMT).toFixed(2):'';
  TGST_AMT        = TGST_AMT > 0?parseFloat(TGST_AMT).toFixed(2):'';
  TOT_AMT         = TOT_AMT > 0?parseFloat(TOT_AMT).toFixed(2):'';
  
  $("#SIMAIN_QTY_total").text(SIMAIN_QTY);
  $("#ALT_UOMID_QTY_total").text(ALT_UOMID_QTY);
  $("#RATEPUOM_total").text(RATEPUOM);
  $("#DISCOUNT_AMT_total").text(DISCOUNT_AMT);
  $("#DISAFTT_AMT_total").text(DISAFTT_AMT);
  $("#IGSTAMT_total").text(IGSTAMT);
  $("#CGSTAMT_total").text(CGSTAMT);
  $("#SGSTAMT_total").text(SGSTAMT);
  $("#TGST_AMT_total").text(TGST_AMT);
  $("#TOT_AMT_total").text(TOT_AMT);

}


function getTaxStatus(customid){

  var TaxStatus = $.ajax({type: 'POST',url:'{{route("transaction",[44,"getTaxStatus"])}}',async: false,dataType: 'json',data: {id:customid},done: function(response) {return response;}}).responseText;
    
  if(TaxStatus =="1"){
    $(".ExceptionalGST").show();
    $("#EXE_GST").prop('checked', true);
  }
  else{
    $(".ExceptionalGST").hide();
    $("#EXE_GST").prop('checked', false);
  }  

}


function taxStatusWiseTaxCalculation(){
  
  if($("#EXE_GST").is(":checked") == true){
    $('#Material').find('.participantRow').each(function(){

      var id      = $(this).find('[id*="IGST"]').attr('id');
      var ROW_ID  = id.split('_').pop();

      IGST  = parseFloat($("#IGST_"+ROW_ID).val());
      CGST  = parseFloat($("#CGST_"+ROW_ID).val());
      SGST  = parseFloat($("#SGST_"+ROW_ID).val());

      if(IGST > 0){
        $("#IGST_"+ROW_ID).prop('readonly', false);
      }

      if(CGST > 0){
        $("#CGST_"+ROW_ID).prop('readonly', false);
      }

      if(SGST > 0){
        $("#SGST_"+ROW_ID).prop('readonly', false);
      }
    });
  }
}

function getExceptionalGst(){
  taxStatusWiseTaxCalculation();

  if($("#EXE_GST").is(":checked") == false){
    $('#TotalValue').val('0.00');
    $('#Material').find('.participantRow').each(function(){
      var rowcount = $(this).closest('table').find('.participantRow').length;
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');

      $(this).find('[id*="IGST"]').prop('readonly', true);
      $(this).find('[id*="CGST"]').prop('readonly', true);
      $(this).find('[id*="SGST"]').prop('readonly', true);

      if(rowcount > 1){
        $(this).closest('.participantRow').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count1').val(rowcount);
      }
    });

    
    $('#txtTNCID_popup').val('');
    $('#TNCID_REF').val('');
    $('#TC').find('.participantRow3').each(function(){
      var rowcount = $(this).closest('table').find('.participantRow3').length;
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count2').val();
      if(rowcount > 1){
        $(this).closest('.participantRow3').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count2').val(rowcount);
      }
    });

    $('#txtCTID_popup').val('');
    $('#CTID_REF').val('');
    $('#CT').find('.participantRow5').each(function(){
      var rowcount = $(this).closest('table').find('.participantRow5').length;
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      $(this).find('input:checkbox').removeAttr('checked');
      var rowcount = $('#Row_Count4').val();
      if(rowcount > 1){
        $(this).closest('.participantRow5').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count4').val(rowcount);
      }
    });

    $('#PaymentSlabs').find('.participantRow6').each(function(){
      var rowcount = $(this).closest('table').find('.participantRow6').length;
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      if(rowcount > 1){
        $(this).closest('.participantRow6').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count5').val(rowcount);
      }
    });

    $('#TDS').find('.participantRow7').each(function(){
      var rowcount = $(this).closest('table').find('.participantRow7').length;
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      $(this).find('input:checkbox').removeAttr('checked');
      var rowcount = $('#Row_Count6').val();
      if(rowcount > 1){
        $(this).closest('.participantRow7').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count6').val(rowcount);
      }
    });

  $("#SIMAIN_QTY_total").text('');
  $("#ALT_UOMID_QTY_total").text('');
  $("#RATEPUOM_total").text('');
  $("#DISCOUNT_AMT_total").text('');
  $("#DISAFTT_AMT_total").text('');
  $("#IGSTAMT_total").text('');
  $("#CGSTAMT_total").text('');
  $("#SGSTAMT_total").text('');
  $("#TGST_AMT_total").text('');
  $("#TOT_AMT_total").text('');

  }

}

function taxGstCalculation(id,value){
  var field   = id.split('_');
  var FIELD   = field[0];
  var ROW_ID  = field[1];

  if(FIELD =="CGST" && value > 0){
    $("#SGST_"+ROW_ID).val(parseFloat(value).toFixed(4));
    $("#SGST_"+ROW_ID).val(parseFloat(value).toFixed(4));
  }

  if(FIELD =="SGST" && value > 0){
    $("#CGST_"+ROW_ID).val(parseFloat(value).toFixed(4));
    $("#CGST_"+ROW_ID).val(parseFloat(value).toFixed(4));
  }

  getAllMaterialCalculation();
}


function getAllMaterialCalculation(){

  $('#Material').find('.participantRow').each(function(){

  var totalvalue  = 0.00;
  var itemid      = $(this).find('[id*="ITEMID_REF"]').val();
  var mqty        = $(this).find('[id*="PO_QTY"]').val();
  var altuomid    = $(this).find('[id*="ALT_UOMID_REF"]').val();
  var txtid       = $(this).find('[id*="ALT_UOMID_QTY"]').attr('id');
  var irate       = $(this).find('[id*="RATEPUOM"]').val();

  $(this).find('[id*="IGSTAMT"]').val('0');
  $(this).find('[id*="CGSTAMT"]').val('0');
  $(this).find('[id*="SGSTAMT"]').val('0');

  var tamt = parseFloat(parseFloat(mqty)*parseFloat(irate)).toFixed(2);
  var dispercnt = $(this).find('[id*="DISCPER"]').val();
  var disamt = 0 ;      
  if (dispercnt != '' && dispercnt != '.0000')
  {
     disamt =  parseFloat((parseFloat(tamt)*parseFloat(dispercnt))/100).toFixed(2);
  }
  else if ($(this).find('[id*="DISCOUNT_AMT"]').val() != '' && $(this).find('[id*="DISCOUNT_AMT"]').val() != '0.00')
  {
     disamt = $(this).find('[id*="DISCOUNT_AMT"]').val();
  }
  tamt = parseFloat(parseFloat(tamt) - parseFloat(disamt)).toFixed(2);   
  var tp1 = $(this).find('[id*="IGST_"]').val();
  var tp2 = $(this).find('[id*="CGST_"]').val();
  var tp3 = $(this).find('[id*="SGST_"]').val();
  var tp1amt = parseFloat((tamt * tp1)/100).toFixed(2);
  var tp2amt = parseFloat((tamt * tp2)/100).toFixed(2);
  var tp3amt = parseFloat((tamt * tp3)/100).toFixed(2);
  var taxamt = parseFloat(parseFloat(tp1amt) + parseFloat(tp2amt) + parseFloat(tp3amt)).toFixed(2); 
  var totamt = parseFloat(parseFloat(tamt) + parseFloat(taxamt)).toFixed(2);

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

  $(this).find('[id*="DISAFTT_AMT"]').val(tamt);
  $(this).find('[id*="TOT_AMT"]').val(totamt);
  $(this).find('[id*="TGST_AMT"]').val(taxamt);
  $(this).find('[id*="IGSTAMT"]').val(tp1amt);
  $(this).find('[id*="CGSTAMT"]').val(tp2amt);
  $(this).find('[id*="SGSTAMT"]').val(tp3amt);

  bindTotalValue();
  if($('#CTID_REF').val()!=''){
    bindGSTCalTemplate();
  }
  bindTotalValue();
 
  });
}


//GST Reverse Section 
$('#GST_Reverse').on('change', function() 
{
    
    if($('#CTID_REF').val()!='')
    {
      bindGSTCalTemplate();
    }
    bindTotalValue();
    event.preventDefault();
});



function reverse_gst()
    {     
      var totalvalue = 0.00;
      var tvalue = 0.00;
      var ctvalue = 0.00;
      var ctgstvalue = 0.00;
      var tttdsamt21 = 0.00;
      $('#Material').find('.participantRow').each(function()
      {      
        if($('#GST_Reverse').is(':checked') == true)
        {
          tvalue = $(this).find('[id*="DISAFTT_AMT_"]').val();
          totalvalue = parseFloat(totalvalue) + parseFloat(tvalue);
          totalvalue = parseFloat(totalvalue).toFixed(2);
        }
        else
        {
          tvalue = $(this).find('[id*="TOT_AMT_"]').val();
          totalvalue = parseFloat(totalvalue) + parseFloat(tvalue);
          totalvalue = parseFloat(totalvalue).toFixed(2);
        }   



      });
      if($('#CTID_REF').val() != '')
      {
        $('#CT').find('.participantRow5').each(function()
        {
          ctvalue = $(this).find('[id*="VALUE"]').val();
          ctgstvalue = $(this).find('[id*="TOTGSTAMT"]').val();

          if($('#GST_Reverse').is(':checked') == true)
          {
            totalvalue = parseFloat(totalvalue) + parseFloat(ctvalue);
            totalvalue = parseFloat(totalvalue).toFixed(2);
          }
          else
          {
            totalvalue = parseFloat(totalvalue) + parseFloat(ctvalue);
            totalvalue = parseFloat(totalvalue) + parseFloat(ctgstvalue);
            totalvalue = parseFloat(totalvalue).toFixed(2);
          }

        });
      }
      if($('#drpTDS').val() == 'Yes'){
        $('#TDS').find('.participantRow7').each(function(){
          if($(this).find('[id*="TOT_TD_AMT"]').val() != '' && $(this).find('[id*="TOT_TD_AMT"]').val() != '.00')
          {

            tttdsamt21 = $(this).find('[id*="TOT_TD_AMT"]').val();
            totalvalue = parseFloat(parseFloat(totalvalue)-parseFloat(tttdsamt21)).toFixed(2);
          }
        });
      }

      $('#TotalValue').val(totalvalue);
    }

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

      getActionEvent();
    }

    //GST Reverse Section 
$('#GST_Reverse').on('change', function() 
{
    
    if($('#CTID_REF').val()!='')
    {
      bindGSTCalTemplate();
    }
    bindTotalValue();
    event.preventDefault();
});


</script>


@endpush