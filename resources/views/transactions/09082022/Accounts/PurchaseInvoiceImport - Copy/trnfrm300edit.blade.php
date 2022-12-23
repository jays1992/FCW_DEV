
@extends('layouts.app')
@section('content')
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Purchase Invoice Import (PII)</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSave" ><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" {{($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-lock"></i>  Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div><!--row-->
    </div>

    <!-- <form id="frm_trn_edit" onsubmit="return validateForm()"  method="POST" class="needs-validation"  > -->
    <div class="container-fluid filter">
    <form id="frm_trn_edit"  method="POST"> 
      @csrf
      {{isset($objHDR->IPO_ID[0]) ? method_field('PUT') : '' }}
	<div class="inner-form">
	
		<div class="row">
        <div class="col-lg-2 pl"><p>IPO No</p></div>
        <div class="col-lg-2 pl">
                <input type="text" name="PII_NO" id="PII_NO" value="{{ $objHDR->PII_NO}}" class="form-control mandatory" maxlength="30" autocomplete="off" style="text-transform:uppercase" autofocus readonly >
        </div>
        <div class="col-lg-2 pl"><p>IPO Date</p></div>
        <div class="col-lg-2 pl">
                  <input type="date" name="PII_DT" id="PII_DT" value="{{ $objHDR->PII_DT}}" class="form-control mandatory"  placeholder="dd/mm/yyyy" />
        </div>

        <div class="col-lg-2 pl"><p>Vendor</p></div>
        <div class="col-lg-2 pl">
                <input type="text" name="txtvendor" id="txtvendor" class="form-control" value="{{$objPBVID->VCODE}}-{{$objPBVID->NAME}}" readonly  />
                <input type="hidden" name="VID_REF" id="VID_REF"  class="form-control" value="{{ $objHDR->VID_REF}}" />
        </div>
    </div>


    <div class="row">
      <div class="col-lg-2 pl"><p>FC</p></div>
      <div class="col-lg-2 pl">
          <input type="checkbox" name="SOFC" id="SOFC" class="form-checkbox" {{$objHDR->FC == 1 ? 'checked' : ''}} >
      </div>
      
      <div class="col-lg-2 pl"><p>Currency</p></div>
      <div class="col-lg-2 pl" id="divcurrency" >
          @if (count($objsocurrency) > 0)
          <input type="text" name="CRID_popup" id="txtCRID_popup" class="form-control"  autocomplete="off" value="{{ $objsocurrency[0] }}" readonly   />
          @else
          <input type="text" name="CRID_popup" id="txtCRID_popup" class="form-control"  autocomplete="off"   disabled/>
          @endif
          <input type="hidden" name="CRID_REF" id="CRID_REF" class="form-control" autocomplete="off"   value="{{ $objHDR->CRID_REF }}" />
          
      </div>
      
      <div class="col-lg-2 pl"><p>Conversion Factor</p></div>
      <div class="col-lg-2 pl">
          <input type="text" name="CONVFACT" id="CONVFACT" class="form-control" maxlength="100" autocomplete="off" value="{{ $objHDR->CONV_FACTOR }}" />
      </div>
  </div>


    <div class="row">
        <div class="col-lg-2 pl"><p> Vendor Invoice No</p></div> 
        <div class="col-lg-2 pl">
                <input type="text" name="VENDOR_INNO" id="VENDOR_INNO" value="{{ $objHDR->VENDOR_INVOICE_NO}}" maxlength="30" class="form-control" autocomplete="off" />
        </div>
        <div class="col-lg-2 pl"><p>Vendor Invoice Date</p></div>
        <div class="col-lg-2 pl">
                <input type="date" name="VENDOR_INDT" id="VENDOR_INDT" value="{{ $objHDR->VENDOR_INVOICE_DT}}" class="form-control"  placeholder="dd/mm/yyyy"  />
        </div>
        <div class="col-lg-2 pl"><p>Bill To </p></div>
        <div class="col-lg-2 pl" id="div_billto">
            <input type="text" name="txtBILLTO" id="txtBILLTO" class="form-control"  autocomplete="off" value="{{$objBillAddress[0]}}" readonly  />
            <input type="hidden" name="BILLTO" id="BILLTO" class="form-control" autocomplete="off" value="{{ $objHDR->BILL_TO}}" />
        </div>
        
        
    </div>
    <div class="row">
        <div class="col-lg-2 pl"><p>Ship To</p></div>
        <div class="col-lg-2 pl" id="div_shipto">
            <input type="text" name="txtSHIPTO" id="txtSHIPTO" class="form-control"  autocomplete="off" value="{{$objShpAddress[0]}}"  readonly  />
            <input type="hidden" name="SHIPTO" id="SHIPTO" class="form-control" autocomplete="off" value="{{ $objHDR->SHIP_TO}}" />
            <input type="hidden" name="Tax_State" id="Tax_State" class="form-control" value="{{$TAXSTATE[0]}}" autocomplete="off" />
        </div>

        <div class="col-lg-2 pl"><p>Remarks</p></div>
        <div class="col-lg-6 pl">
            <input type="text" name="REMARKS" id="REMARKS" autocomplete="off" class="form-control" value="{{ $objHDR->REMARKS}}" maxlength="200"  >
        </div>

    </div>

    <div class="row">
      <div class="col-lg-2 pl"><p>FOB</p></div>
      <div class="col-lg-2 pl">
        <select  name="FOB" id="FOB" class="form-control" autocomplete="off" >
          <option {{isset($objHDR) && $objHDR->FOB == "NO"?'selected="selected"':''}} value="NO">NO</option>
          <option {{isset($objHDR) && $objHDR->FOB == "YES"?'selected="selected"':''}} value="YES">YES</option>
        </select>
      </div>

      <div class="col-lg-2 pl"><p>BOE No</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="BOE_NO" id="BOE_NO" value="{{ $objHDR->BOE_NO}}" class="form-control" autocomplete="off" >
      </div>

      <div class="col-lg-2 pl"><p>BOE Date</p></div>
      <div class="col-lg-2 pl">
        <input type="date" name="BOE_DT" id="BOE_DT" value="{{ $objHDR->BOE_DT}}" class="form-control" autocomplete="off"  placeholder="dd/mm/yyyy" >
      </div>
	</div>


  <div class="row">	
          
		<div class="col-lg-2 pl"><p>Imort Duty</p></div>
		<div class="col-lg-2 pl">
			<select  name="IMPORT_DUTYID_REF" id="IMPORT_DUTYID_REF" class="form-control" autocomplete="off" >
				<option value="">Select</option>
				@if(isset($objImportDuty) && !empty($objImportDuty))
				@foreach($objImportDuty as $val)
				<option {{isset($objHDR) && $objHDR->IMPORT_DUTYID_REF == $val->IMPORT_DUTYID?'selected="selected"':''}}  value="{{$val->IMPORT_DUTYID}}">{{$val->IMPORT_DUTY_CODE}}</option>
				@endforeach
				@endif
			</select>  
		</div>
    
		<div class="col-lg-2 pl"><p>Ref No </p></div>
		<div class="col-lg-2 pl">
			<input type="text" name="REF_NO" id="REF_NO" value="{{ $objHDR->REF_NO}}" class="form-control" maxlength="100" autocomplete="off" >
		</div>
		
        <div class="col-lg-2 pl"><p>Credit Days</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="Credit_days" id="Credit_days" value="{{ $objHDR->CREDIT_DAYS}}" onkeyup="getDueDate()" maxlength="200" class="form-control"  autocomplete="off"   />
        </div>
		
	</div>


    <div class="row">

        <div class="col-lg-2 pl"><p>Due Date</p></div>
        <div class="col-lg-2 pl">
            <input type="date" name="DUE_DATE" id="DUE_DATE" class="form-control" value="{{ $objHDR->DUE_DATE}}"  placeholder="dd/mm/yyyy"  />                        
        </div>

        <div class="col-lg-2 pl"><p>Total Amount</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="tot_amt" id="tot_amt" class="form-control"  autocomplete="off" readonly  />
        </div>

        <div  class="col-lg-2 pl"><p>Reverse GST</p></div>
        <div  class="col-lg-1 pl">
            <input type="checkbox" name="GST_Reverse" id="GST_Reverse"  {{$objHDR->REVERSE_GST == 1 ? 'checked' : ''}}    />                          
        </div>

         

       
    </div>

    <div class="row">
        <div  class="col-lg-2 pl"><p>GST Input Not Avail</p></div>
        <div  class="col-lg-1 pl">
            <input type="checkbox" name="GST_N_Avail" id="GST_N_Avail"  {{$objHDR->GST_INPUT_NOT_AVAIL == 1 ? 'checked' : ''}}   />
        </div> 
    </div>
   
	</div>

	<div class="container-fluid">
		<div class="row">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#Material">Material</a></li> 
        <li><a data-toggle="tab" href="#TC">T&C</a></li> 
        <li><a data-toggle="tab" href="#CT">Calculation Template</a></li>
        <li><a data-toggle="tab" href="#PaymentSlabs">Payment Slabs</a></li>                
        <li><a data-toggle="tab" href="#TDS">TDS</a></li> 
        <li><a data-toggle="tab" href="#udf">UDF</a></li>
			</ul>
      <div class="tab-content">

    
        <div id="Material" class="tab-pane fade in active">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0"> 
                  <tr>
                    <th rowspan="2">GRN No <input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="{{$objCount1}}" ></th>
                    <th rowspan="2">Item Code  </th>
                    <th rowspan="2">Item Name</th>
                    <th rowspan="2">Item Specification</th>
                    <th rowspan="2">Main UoM (MU)</th>
                    <th rowspan="2">Received Qty  MU</th>
                    <th rowspan="2">Alt UOM (AU)</th>
                    <th rowspan="2">Received Qty AU</th>
                    <th rowspan="2">Bill Qty (MU)</th>
                    <th rowspan="2">UOM of Bill Qty</th>
                    <th rowspan="2">Rate as per MU</th>
                    <th colspan="2">Discount</th>
                    <th rowspan="2">IPO Amount</th>
                    <th rowspan="2">Freight Amount</th>
                    <th rowspan="2">Insurance Amount</th>
                    <th rowspan="2">Assessable Value</th>
                    <th rowspan="2">Custom Duty Rate %</th>
                    <th rowspan="2">Custom Duty Amount</th>
                    <th rowspan="2">SWS Rate %</th>
                    <th rowspan="2">SWS Amount</th>
                    <th rowspan="2">Total Custom Duty</th>
                    <th rowspan="2">Taxable Value</th>
                    <th rowspan="2">IGST Rate %</th>
                    <th rowspan="2">IGST Amount</th>
                    <th rowspan="2">Value after GST</th>
                    <th rowspan="2" width="3%">Action</th>
                  </tr>
                  <tr>
                    <th>%</th>
                    <th>Amount</th>
                  </tr>          
                  </thead>
                    <tbody>
                    @if(!empty($objPBMAT))
                      @foreach($objPBMAT as $key => $row)
                        <tr  class="participantRow">
                        <td><input type="text" name="txtGRN_NO_{{$key}}" id="txtGRN_NO_{{$key}}" value="{{$row->GRN_NO}}" class="form-control"  autocomplete="off"  readonly style="width:120px;" /></td>
                        <td hidden><input type="hidden" name="GRNID_REF_{{$key}}" id="GRNID_REF_{{$key}}" value="{{$row->GRNID_REF}}" class="form-control" autocomplete="off" /></td>
                        <td hidden><input type="hidden" name="IPO_ID_REF_{{$key}}" id="IPO_ID_REF_{{$key}}" value="{{$row->IPO_ID_REF}}" class="form-control" autocomplete="off" /></td>
                      
                        <td><input type="text" name="popupITEMID_{{$key}}" id="popupITEMID_{{$key}}" value="{{$row->ICODE}}" class="form-control"  autocomplete="off"  readonly/></td>
                        <td hidden><input type="hidden" name="ITEMID_REF_{{$key}}" id="ITEMID_REF_{{$key}}" value="{{$row->ITEMID_REF}}" class="form-control" autocomplete="off" /></td>
                        <td><input type="text" name="ItemName_{{$key}}" id="ItemName_{{$key}}" value="{{$row->ITEM_NAME}}" class="form-control"  autocomplete="off"  readonly/></td>
                        <td><input type="text" name="Itemspec_{{$key}}" id="Itemspec_{{$key}}" value="{{$row->ITEM_SPECI}}" class="form-control"  autocomplete="off"  /></td>
                        
                        <td><input type="text" name="popupMUOM_{{$key}}" id="popupMUOM_{{$key}}" value="{{$row->MAIN_UOM_CODE}}" class="form-control"  autocomplete="off"  readonly/></td>
                        <td hidden><input type="hidden" name="MAIN_UOMID_REF_{{$key}}" id="MAIN_UOMID_REF_{{$key}}" value="{{$row->MAIN_UOMID_REF}}" class="form-control"  autocomplete="off" /></td>
                        <td><input type="text" name="SO_QTY_{{$key}}" id="SO_QTY_{{$key}}" value="{{$row->RECEIVED_MAIN_QTY}}"  onkeyup="onChangeAmount(this.id,this.value)" class="form-control three-digits" maxlength="13"  autocomplete="off" readonly  /></td>
                        <td hidden><input type="hidden" name="SO_FQTY_{{$key}}" id="SO_FQTY_{{$key}}" class="form-control three-digits" maxlength="13"  autocomplete="off"  readonly/></td>
                        <td><input type="text" name="popupAUOM_{{$key}}" id="popupAUOM_{{$key}}" value="{{$row->AULT_UOM_CODE}}" class="form-control"  autocomplete="off"  readonly/></td>
                        <td hidden><input type="hidden" name="ALT_UOMID_REF_{{$key}}" id="ALT_UOMID_REF_{{$key}}" value="{{$row->ALT_UOMID_REF}}" class="form-control"  autocomplete="off"  readonly/></td>
                        <td><input type="text" name="ALT_UOMID_QTY_{{$key}}" id="ALT_UOMID_QTY_{{$key}}"  class="form-control three-digits" maxlength="13" autocomplete="off"  readonly/></td>
                      
                        <td><input type="text" name="BILL_QTY_{{$key}}" id="BILL_QTY_{{$key}}" value="{{$row->BILL_QTY}}" onkeyup="onChangeAmount(this.id,this.value)" class="form-control" maxlength="13"  autocomplete="off" /></td>
                        <td><input type="text" name="UOM_OF_BILL_QTY_{{$key}}" id="UOM_OF_BILL_QTY_{{$key}}"  value="{{$row->MAIN_UOM_CODE}}" onkeyup="onChangeAmount(this.id,this.value)" class="form-control" maxlength="13"  autocomplete="off" readonly /></td>

                        <td><input type="text" name="RATEPUOM_{{$key}}" id="RATEPUOM_{{$key}}" value="{{$row->RATE_ASP_MU}}" onkeyup="onChangeAmount(this.id,this.value)" class="form-control" maxlength="13"  autocomplete="off" /></td>
                        <td><input type="text" name="DISC_PER_{{$key}}" id="DISC_PER_{{$key}}" value="{{ intval($row->DISC_PER) > 0 ? $row->DISC_PER:'0.00' }}" onkeyup="onChangeAmount(this.id,this.value)" class="form-control four-digits" maxlength="8"  autocomplete="off" style="width: 50px;" /></td>
                        <td><input type="text" name="DISC_AMT_{{$key}}" id="DISC_AMT_{{$key}}" value="{{ intval($row->DISC_AMT) > 0 ? $row->DISC_AMT:'0.00' }}" onkeyup="onChangeAmount(this.id,this.value)" class="form-control two-digits" maxlength="15"  autocomplete="off"  /></td>
                        <td><input type="text" name="DISAFTT_AMT_{{$key}}" id="DISAFTT_AMT_{{$key}}"  class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                        
                        <td><input type="text" name="FREIGHT_AMT_{{$key}}"           id="FREIGHT_AMT_{{$key}}"            value="{{ intval($row->FREIGHT_AMT) > 0 ? $row->FREIGHT_AMT:'0.00' }}"      onkeyup="onChangeAmount(this.id,this.value)" class="form-control four-digits"   autocomplete="off"/></td>
                        <td><input type="text" name="INSURANCE_AMT_{{$key}}"         id="INSURANCE_AMT_{{$key}}"          value="{{ intval($row->INSURANCE_AMT) > 0 ? $row->INSURANCE_AMT:'0.00' }}"  onkeyup="onChangeAmount(this.id,this.value)" class="form-control four-digits"   autocomplete="off"/></td>
                        <td><input type="text" name="ASSESSABLE_VALUE_{{$key}}"      id="ASSESSABLE_VALUE_{{$key}}"       value="{{$row->ASSESSABLE_VALUE}}"  class="form-control four-digits" readonly  autocomplete="off"/></td>
                        <td><input type="text" name="CUSTOME_DUTY_RATE_PER_{{$key}}" id="CUSTOME_DUTY_RATE_PER_{{$key}}"  value="{{$row->CUSTOME_DUTY_RATE}}"  class="form-control four-digits" readonly  autocomplete="off"/></td>
                        <td><input type="text" name="CUSTOME_DUTY_RATE_AMT_{{$key}}" id="CUSTOME_DUTY_RATE_AMT_{{$key}}"  value=""  class="form-control four-digits" readonly  autocomplete="off"/></td>
                        <td><input type="text" name="SWS_RATE_PER_{{$key}}"          id="SWS_RATE_PER_{{$key}}"           value="{{$row->SWS_RATE}}"  class="form-control four-digits" readonly  autocomplete="off"/></td>
                        <td><input type="text" name="SWS_RATE_AMT_{{$key}}"          id="SWS_RATE_AMT_{{$key}}"           value=""  class="form-control four-digits" readonly  autocomplete="off"/></td>
                        <td><input type="text" name="TOTAL_CUSTOME_DUTY_{{$key}}"    id="TOTAL_CUSTOME_DUTY_{{$key}}"     value=""  class="form-control four-digits" readonly  autocomplete="off"/></td>
                        <td><input type="text" name="TAXABLE_VALUE_{{$key}}"         id="TAXABLE_VALUE_{{$key}}"          value=""  class="form-control four-digits" readonly  autocomplete="off"/></td>
                        <td><input type="text" name="IGST_RATE_PER_{{$key}}"         id="IGST_RATE_PER_{{$key}}"          value="{{$row->IGST}}"  class="form-control four-digits" readonly  autocomplete="off"/></td>
                        <td><input type="text" name="IGST_RATE_AMT_{{$key}}"         id="IGST_RATE_AMT_{{$key}}"          value=""  class="form-control four-digits" readonly  autocomplete="off"/></td>
                        <td><input type="text" name="VALUE_AFTER_GST_{{$key}}"       id="VALUE_AFTER_GST_{{$key}}"        value=""  class="form-control four-digits" readonly  autocomplete="off"/></td>

                        <td align="center" ><button class="btn add material" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button> <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                      
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
                <input type="text" name="txtTNCID_popup" id="txtTNCID_popup" class="form-control"  autocomplete="off"  readonly/>
                @if(!empty($objPBTNC))
                    <input type="hidden" name="TNCID_REF" id="TNCID_REF" class="form-control" value="{{$objPBTNC[0]->TNCID_REF}}" autocomplete="off" />
                @else
                    <input type="hidden" name="TNCID_REF" id="TNCID_REF" class="form-control"  autocomplete="off" />
                @endif
                </div>
            </div>
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:240px;width:50%;">
                <table id="example3" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                    <tr >
                        <th>Terms & Conditions Description<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2"  value="{{$objCount3}}" /></th>
                        <th>Value / Comment</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="tncbody">
                @if(!empty($objPBTNC))
                  @foreach($objPBTNC as $Tkey => $Trow)
                      <tr  class="participantRow3">
                          <td><input type="text" name={{"popupTNCDID_".$Tkey}} id={{"popupTNCDID_".$Tkey}} class="form-control"  autocomplete="off"  readonly/></td>
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
                          <td><input type="text" name="popupTNCDID_0" id="popupTNCDID_0" class="form-control"  autocomplete="off"  readonly/></td>
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
                <input type="text" name="txtCTID_popup" id="txtCTID_popup" class="form-control"  autocomplete="off"  readonly/>
                @if(!empty($objPBCAL))
                  <input type="hidden" name="CTID_REF" id="CTID_REF" class="form-control" value="{{$objPBCAL[0]->CTID_REF}}" autocomplete="off" />
                @else
                  <input type="hidden" name="CTID_REF" id="CTID_REF" class="form-control" autocomplete="off" />
                @endif
                </div>
            </div>
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:240px;" >
                <table id="example5" class="display nowrap table table-striped table-bordered itemlist " width="100%" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0">
                      <tr>
                          <th>Calculation Component<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3" value="{{$objCount4}}" /></th>
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
                  @if(!empty($objPBCAL))
                    @foreach($objPBCAL as $Ckey => $Crow)
                        <?php
                        $Crow->CGST=0;
                        $Crow->SGST=0;
                        $Crow->GST=0;
                        $Crow->ACTUAL='';
                        ?>

                        <tr  class="participantRow5">
                            <td><input type="text" name={{"popupTID_".$Ckey}} id={{"popupTID_".$Ckey}}  class="form-control"  autocomplete="off"  readonly/></td>
                            <td hidden><input type="hidden" name={{"TID_REF_".$Ckey}}  id={{"TID_REF_".$Ckey}}  class="form-control" autocomplete="off" value="{{$Crow->TID_REF}}" /></td>
                            <td><input type="text" name={{"RATE_".$Ckey}}  id={{"RATE_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->RATE}}"  readonly/></td>
                            <td hidden><input type="hidden" name={{"BASIS_".$Ckey}} id={{"BASIS_".$Ckey}} class="form-control" autocomplete="off"  /></td>
                            <td hidden><input type="hidden" name={{"SQNO_".$Ckey}} id={{"SQNO_".$Ckey}} class="form-control" autocomplete="off" /></td>
                            <td hidden><input type="hidden" name={{"FORMULA_".$Ckey}} id={{"FORMULA_".$Ckey}} class="form-control" autocomplete="off" /></td>
                            <td><input type="text" name={{"VALUE_".$Ckey}} id={{"VALUE_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$Crow->VALUE}}" readonly/></td>
                            <td style="text-align:center;" ><input type="checkbox" class="filter-none" name={{"calGST_".$Ckey}} id={{"calGST_".$Ckey}} {{$Crow->GST == 1 ? 'checked' : ''}}   ></td>
                            
                            <td><input type="text" name={{"calIGST_".$Ckey}} id={{"calIGST_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->IGST}}" readonly/></td>
                      @php
                          $IGSTAMT=number_format((($Crow->VALUE*$Crow->IGST)/100),2, '.', '');
                      @endphp        
                            <td><input type="text" name={{"AMTIGST_".$Ckey}} id={{"AMTIGST_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$IGSTAMT}}"  readonly/></td>
                            <td><input type="text" name={{"calCGST_".$Ckey}} id={{"calCGST_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->CGST}}" readonly/></td>
                      @php
                          $CGSTAMT=number_format((($Crow->VALUE*$Crow->CGST)/100),2, '.', '');
                      @endphp       
                            <td><input type="text" name={{"AMTCGST_".$Ckey}} id={{"AMTCGST_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$CGSTAMT}}" readonly/></td>
                            <td><input type="text" name={{"calSGST_".$Ckey}} id={{"calSGST_".$Ckey}} class="form-control four-digits" maxlength="8" autocomplete="off" value="{{$Crow->SGST}}" readonly/></td>
                      @php
                          $SGSTAMT=number_format((($Crow->VALUE*$Crow->SGST)/100),2, '.', '');
                      @endphp
                            <td><input type="text" name={{"AMTSGST_".$Ckey}} id={{"AMTSGST_".$Ckey}} class="form-control two-digits" maxlength="15" autocomplete="off" value="{{$SGSTAMT}}" readonly/></td>
                      @php
                          $TOTGSTAMT=number_format(($IGSTAMT+$CGSTAMT+$SGSTAMT),2, '.', '');
                      @endphp      
                            <td><input type="text" name={{"TOTGSTAMT_".$Ckey}} id={{"TOTGSTAMT_".$Ckey}} class="form-control two-digits"  maxlength="15" autocomplete="off" value="{{$TOTGSTAMT}}" readonly/></td>
                            <td style="text-align:center;"><input type="checkbox" class="filter-none" name={{"calACTUAL_".$Ckey}} id={{"calACTUAL_".$Ckey}}  {{$Crow->ACTUAL == 1 ? 'checked' : ''}} /></td>
                            <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                        </tr>
                        <tr></tr>
                    @endforeach 
                    @else
                    <tr  class="participantRow5">
                        <td><input type="text" name="popupTID_0" id="popupTID_0" class="form-control"  autocomplete="off"  readonly/></td>
                        <td hidden><input type="hidden" name="TID_REF_0" id="TID_REF_0" class="form-control" autocomplete="off" /></td>
                        <td><input type="text" name="RATE_0" id="RATE_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                        <td hidden><input type="hidden" name="BASIS_0" id="BASIS_0" class="form-control" autocomplete="off" /></td>
                        <td hidden><input type="hidden" name="SQNO_0" id="SQNO_0" class="form-control" autocomplete="off" /></td>
                        <td><input type="text" name="VALUE_0" id="VALUE_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                        <td style="text-align:center;" ><input type="checkbox" class="filter-none" name="calGST_0" id="calGST_0" value="" ></td>
                        <td><input type="text" name="calIGST_0" id="calIGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                        <td><input type="text" name="AMTIGST_0" id="AMTIGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                        <td><input type="text" name="calCGST_0" id="calCGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                        <td><input type="text" name="AMTCGST_0" id="AMTCGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                        <td><input type="text" name="calSGST_0" id="calSGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly/></td>
                        <td><input type="text" name="AMTSGST_0" id="AMTSGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly/></td>
                        <td><input type="text" name="TOTGSTAMT_0" id="TOTGSTAMT_0" class="form-control two-digits"  maxlength="15" autocomplete="off"  readonly/></td>
                        <td style="text-align:center;"><input type="checkbox" class="filter-none" name="calACTUAL_0" id="calACTUAL_0" value=""   ></td>
                        <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                    </tr>
                    <tr></tr>
                  @endif
                  </tbody>
                </table>
            </div>	
        </div>
        <div id="PaymentSlabs" class="tab-pane fade">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:55%;">
                <table id="example6" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                      <tr >
                          <th>Day(s)<input class="form-control" type="hidden" name="Row_Count5" id ="Row_Count5" value="{{$objCount5}}" /></th>
                          <th>Due %</th>
                          <th>Remarks</th>
                          <th>Due Date</th>
                          <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                    @if(!empty($objPBPSLB))
                      @foreach($objPBPSLB as $Pkey => $Prow)
                          <tr  class="participantRow6">
                              <td> <input type="text" class="form-control" id={{"PAY_DAYS_".$Pkey}} name={{"PAY_DAYS_".$Pkey}} value="{{$Prow->PAY_DAYS}}" autocomplete="off"  /> </td>
                              <td> <input type="text" class="form-control four-digits" id={{"DUE_".$Pkey}} name={{"DUE_".$Pkey}} value="{{$Prow->DUE}}" maxlength="8" autocomplete="off" /> </td>
                              <td> <input type="text" class="form-control" id={{"PSREMARKS_".$Pkey}} name={{"PSREMARKS_".$Pkey}} value="{{$Prow->REMARKS}}" autocomplete="off"  /> </td>
                              <td> <input type="text" class="form-control" id={{"DUE_DATE_".$Pkey}} name={{"DUE_DATE_".$Pkey}} value="{{$Prow->DUE_DATE}}" autocomplete="off"  /> </td>
                              
                              <td align="center" style="min-width: 100px;" ><button class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" ><i class="fa fa-trash" ></i></button></td>
                          </tr>
                          <tr></tr>
                      @endforeach 
                      @else
                      <tr  class="participantRow6">
                              <td> <input type="text" class="form-control" id="PAY_DAYS_0" name="PAY_DAYS_0"  autocomplete="off" /> </td>
                              <td> <input type="text" class="form-control four-digits" id="DUE_0" name="DUE_0"  maxlength="8" autocomplete="off" /> </td>
                              <td> <input type="text" class="form-control" id="PSREMARKS_0" name="PSREMARKS_0" autocomplete="off"  /> </td>
                              <td> <input type="date" class="form-control" id="DUE_DATE_0" name="DUE_DATE_0" autocomplete="off"  readonly /> </td>
                              <td align="center" style="min-width: 100px;" ><button class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button><button class="btn remove" title="Delete" data-toggle="tooltip" type="button" ><i class="fa fa-trash" ></i></button></td>
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
                  <select name="drpTDS" id="drpTDS" class="form-control">
                      <option value=""></option>    
                      <option {{ isset($objHDR->TDS) && $objHDR->TDS =='1'?'selected="selected"':'' }} value="Yes">Yes</option>
                      <option {{ isset($objHDR->TDS) && $objHDR->TDS =='0'?'selected="selected"':'' }} value="No">No</option>
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
                    @if(!empty($objPBTDS))
                      @foreach($objPBTDS as $tkey => $trow)
                        <tr  class="participantRow7">
                            <td style="text-align:center;" >
                            <input type="text" name={{"txtTDS_".$tkey}} id={{"txtTDS_".$tkey}} class="form-control" value="{{$trow->CODE}}" autocomplete="off"  readonly/></td>
                            <td hidden><input type="hidden" name={{"TDSID_REF_".$tkey}} id={{"TDSID_REF_".$tkey}} value="{{$trow->TDSID_REF}}" class="form-control" autocomplete="off" /></td>
                            <td><input type="text" name={{"TDSLedger_".$tkey}} id={{"TDSLedger_".$tkey}} class="form-control" value="{{$trow->CODE_DESC}}" autocomplete="off"  readonly/></td>
                            <td  align="center" style="text-align:center;" ><input type="checkbox" name={{"TDSApplicable_".$tkey}} id={{"TDSApplicable_".$tkey}} {{isset($trow->TDS_APPLICABLE) && $trow->TDS_APPLICABLE == 1?'checked' : ''}}/></td>
                            <td><input type="text" name={{"ASSESSABLE_VL_TDS_".$tkey}} id={{"ASSESSABLE_VL_TDS_".$tkey}} class="form-control two-digits" value="{{$trow->ASSESSABLE_VL_TDS}}" maxlength="15"  autocomplete="off"  /></td>
                            <td><input type="text" name={{"TDS_RATE_".$tkey}} id={{"TDS_RATE_".$tkey}} class="form-control four-digits" maxlength="12" value="{{$trow->TDS_RATE}}" autocomplete="off"  /></td>
                            <td hidden><input type="hidden" name={{"TDS_EXEMPT_".$tkey}} id={{"TDS_EXEMPT_".$tkey}} class="form-control two-digits" value="{{$trow->TDS_EXEMP_LIMIT}}" /></td>
                            <td><input type="text" name={{"TDS_AMT_".$tkey}} id={{"TDS_AMT_".$tkey}} class="form-control two-digits" maxlength="15"  autocomplete="off"  value="{{$trow->TDS_AMT}}" readonly/></td>
                            <td><input type="text" name={{"ASSESSABLE_VL_SURCHARGE_".$tkey}} id={{"ASSESSABLE_VL_SURCHARGE_".$tkey}} class="form-control two-digits" value="{{$trow->ASSESSABLE_VL_SURCHARGE}}" maxlength="15"  autocomplete="off" /></td>
                            <td><input type="text" name={{"SURCHARGE_RATE_".$tkey}} id={{"SURCHARGE_RATE_".$tkey}} class="form-control four-digits" maxlength="12" value="{{$trow->SURCHARGE_RATE}}"  autocomplete="off"  /></td>
                            <td hidden><input type="hidden" name={{"SURCHARGE_EXEMPT_".$tkey}} id={{"SURCHARGE_EXEMPT_".$tkey}} class="form-control two-digits" value="{{$trow->SURCHARGE_EXEMP_LIMIT}}" /></td>
                            <td><input type="text" name={{"SURCHARGE_AMT_".$tkey}} id={{"SURCHARGE_AMT_".$tkey}} class="form-control two-digits" maxlength="15" value="{{$trow->SURCHARGE_AMT}}" autocomplete="off"  readonly/></td>
                            <td><input type="text" name={{"ASSESSABLE_VL_CESS_".$tkey}} id={{"ASSESSABLE_VL_CESS_".$tkey}} class="form-control two-digits" maxlength="15" value="{{$trow->ASSESSABLE_VL_CESS}}" autocomplete="off" /></td>
                            <td><input type="text" name={{"CESS_RATE_".$tkey}} id={{"CESS_RATE_".$tkey}} class="form-control four-digits" maxlength="12" value="{{$trow->CESS_RATE}}" autocomplete="off"  /></td>
                            <td hidden><input type="hidden" name={{"CESS_EXEMPT_".$tkey}} id={{"CESS_EXEMPT_".$tkey}} class="form-control two-digits" value="{{$trow->CESS_EXEMP_LIMIT}}" /></td>
                            <td><input type="text" name={{"CESS_AMT_".$tkey}} id={{"CESS_AMT_".$tkey}} class="form-control two-digits" maxlength="15" value="{{$trow->CESS_AMT}}" autocomplete="off"  readonly/></td>
                            <td><input type="text" name={{"ASSESSABLE_VL_SPCESS_".$tkey}} id={{"ASSESSABLE_VL_SPCESS_".$tkey}} class="form-control two-digits" maxlength="15" value="{{$trow->ASSESSABLE_VL_SPCESS}}"  autocomplete="off" /></td>
                            <td><input type="text" name={{"SPCESS_RATE_".$tkey}} id={{"SPCESS_RATE_".$tkey}} class="form-control four-digits" maxlength="12" value="{{$trow->SPCESS_RATE}}" autocomplete="off"  /></td>
                            <td hidden><input type="hidden" name={{"SPCESS_EXEMPT_".$tkey}} id={{"SPCESS_EXEMPT_".$tkey}} class="form-control two-digits" value="{{$trow->SP_CESS_EXEMP_LIMIT}}" /></td>
                            <td><input type="text" name={{"SPCESS_AMT_".$tkey}} id={{"SPCESS_AMT_".$tkey}} class="form-control two-digits" maxlength="15" value="{{$trow->SPCESS_AMT}}"  autocomplete="off"  readonly/></td>
                  @php
                    $TotalTDS=number_format(($trow->TDS_AMT+$trow->SURCHARGE_AMT+$trow->CESS_AMT+$trow->SPCESS_AMT),2, '.', '');
                  @endphp
                            <td><input type="text" name={{"TOT_TD_AMT_".$tkey}} id={{"TOT_TD_AMT_".$tkey}} class="form-control two-digits" maxlength="15" value="{{$TotalTDS}}"  autocomplete="off"  readonly/></td>
                            <td style="min-width: 100px;" ><button class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                            <button class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                        </tr>
                        <tr></tr>
                      @endforeach
                    @else
                        <tr  class="participantRow7">
                            <td style="text-align:center;" >
                            <input type="text" name="txtTDS_0" id="txtTDS_0" class="form-control"  autocomplete="off"  readonly/></td>
                            <td hidden><input type="hidden" name="TDSID_REF_0" id="TDSID_REF_0" class="form-control" autocomplete="off" /></td>
                            <td><input type="text" name="TDSLedger_0" id="TDSLedger_0" class="form-control"  autocomplete="off"  readonly/></td>
                            <td  align="center" style="text-align:center;" ><input type="checkbox" name="TDSApplicable_0" id="TDSApplicable_0" /></td>
                            <td><input type="text" name="ASSESSABLE_VL_TDS_0" id="ASSESSABLE_VL_TDS_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  /></td>
                            <td><input type="text" name="TDS_RATE_0" id="TDS_RATE_0" class="form-control four-digits" maxlength="12"  autocomplete="off"  /></td>
                            <td hidden><input type="hidden" name="TDS_EXEMPT_0" id="TDS_EXEMPT_0" class="form-control two-digits" /></td>
                            <td><input type="text" name="TDS_AMT_0" id="TDS_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                            <td><input type="text" name="ASSESSABLE_VL_SURCHARGE_0" id="ASSESSABLE_VL_SURCHARGE_0" class="form-control two-digits" maxlength="15"  autocomplete="off" /></td>
                            <td><input type="text" name="SURCHARGE_RATE_0" id="SURCHARGE_RATE_0" class="form-control four-digits" maxlength="12"  autocomplete="off"  /></td>
                            <td hidden><input type="hidden" name="SURCHARGE_EXEMPT_0" id="SURCHARGE_EXEMPT_0" class="form-control two-digits" /></td>
                            <td><input type="text" name="SURCHARGE_AMT_0" id="SURCHARGE_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                            <td><input type="text" name="ASSESSABLE_VL_CESS_0" id="ASSESSABLE_VL_CESS_0" class="form-control two-digits" maxlength="15"  autocomplete="off" /></td>
                            <td><input type="text" name="CESS_RATE_0" id="CESS_RATE_0" class="form-control four-digits" maxlength="12"  autocomplete="off"  /></td>
                            <td hidden><input type="hidden" name="CESS_EXEMPT_0" id="CESS_EXEMPT_0" class="form-control two-digits" /></td>
                            <td><input type="text" name="CESS_AMT_0" id="CESS_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                            <td><input type="text" name="ASSESSABLE_VL_SPCESS_0" id="ASSESSABLE_VL_SPCESS_0" class="form-control two-digits" maxlength="15"  autocomplete="off" /></td>
                            <td><input type="text" name="SPCESS_RATE_0" id="SPCESS_RATE_0" class="form-control four-digits" maxlength="12"  autocomplete="off"  /></td>
                            <td hidden><input type="hidden" name="SPCESS_EXEMPT_0" id="SPCESS_EXEMPT_0" class="form-control two-digits" /></td>
                            <td><input type="text" name="SPCESS_AMT_0" id="SPCESS_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                            <td><input type="text" name="TOT_TD_AMT_0" id="TOT_TD_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                            <td style="min-width: 100px;" ><button class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                            <button class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
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
                        <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count6" id ="Row_Count6"  value="{{$objCount2}}" /></th>
                        <th>Value / Comments</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($objPBUDF))
                      @foreach($objPBUDF as $Ukey => $Urow)
                          <tr  class="participantRow4">
                              <td><input type="text" name={{"popupUDFPBID_".$Ukey}} id={{"popupUDFPBID_".$Ukey}}  class="form-control"  autocomplete="off"  readonly/></td>
                              <td hidden><input type="hidden" name={{"UDFPBID_REF_".$Ukey}}  id={{"UDFPBID_REF_".$Ukey}} class="form-control" value="{{$Urow->PII_UDFID}}" autocomplete="off" /></td>
                              <td hidden><input type="hidden" name={{"UDFismandatory_".$Ukey}} id={{"UDFismandatory_".$Ukey}} class="form-control"  autocomplete="off" /></td>
                              <td id={{"udfinputid_".$Ukey}}>
                              {{-- dynamic input --}} 
                              </td>
                              <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                          </tr>
                          <tr></tr>
                      @endforeach 
                      @else
                      @if($objUdfPBData)
                        @foreach($objUdfPBData as $uindex=>$uRow)
                          <tr  class="participantRow4">
                              <td><input type="text" name={{"popupUDFPBID_".$uindex}} id={{"popupUDFPBID_".$uindex}} class="form-control" value="{{$uRow->LABEL}}" autocomplete="off"  readonly/></td>
                              <td hidden><input type="hidden" name={{"UDFPBID_REF_".$uindex}} id={{"UDFPBID_REF_".$uindex}} class="form-control" value="{{$uRow->UDFPIIID}}" autocomplete="off"   /></td>
                              <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->ISMANDATORY}}" class="form-control"   autocomplete="off" /></td>
                              <td id={{"udfinputid_".$uindex}} >
                              </td>
                              <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>                          
                          </tr>
                          <tr></tr>
                        @endforeach
                        @else
                          <tr  class="participantRow4">
                              <td><input type="text" name="popupUDFPBID_0" id="popupUDFPBID_0" class="form-control" value="" autocomplete="off"  readonly/></td>
                              <td hidden><input type="hidden" name="UDFPBID_REF_0" id="UDFPBID_REF_0" class="form-control" value="" autocomplete="off"   /></td>
                              <td hidden><input type="hidden" name="UDFismandatory_0" id="UDFismandatory_0" value="" class="form-control"   autocomplete="off" /></td>
                              <td id="udfinputid_0" >
                              </td>
                              <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>                          
                          </tr>
                          <tr></tr>
                        @endif 
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



<!--VENDOR dropdown-->

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
<!--VENDOR dropdown-->

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
      <td class="ROW2"><input type="text" id="BillTocodesearch" class="form-control" onkeyup="BillToCodeFunction()"></td>
      <td class="ROW3"><input type="text" id="BillTonamesearch" class="form-control" onkeyup="BillToNameFunction()"></td>
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
      <td class="ROW2"><input type="text" id="ShipTocodesearch" class="form-control" onkeyup="ShipToCodeFunction()"></td>
      <td class="ROW3"><input type="text" id="ShipTonamesearch" class="form-control" onkeyup="ShipToNameFunction()"></td>
    </tr>
    </tbody>
    </table>
      <table id="ShipToTable2" class="display nowrap table  table-striped table-bordered" >
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
          <td class="ROW1"> <input type="checkbox" name="SELECT_TNCID_REF[]" id="tncidcode_{{ $tncindex }}" class="clstncid" value="{{ $tncRow-> TNCID }}" ></td>
          <td class="ROW2">{{ $tncRow-> TNC_CODE }} <input type="hidden" id="txttncidcode_{{ $tncindex }}" data-desc="{{ $tncRow-> TNC_CODE }} - {{ $tncRow-> TNC_DESC }}"  value="{{ $tncRow-> TNCID }}"/></td>
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
        <tr >
          <td class="ROW1"> <input type="checkbox" name="SELECT_CTID_REF[]" id="CTIDcode_{{ $calindex }}" class="clsctid" value="{{ $calRow-> CTID }}" ></td>
          <td class="ROW2">{{ $calRow-> CTCODE }} <input type="hidden" id="txtCTIDcode_{{ $calindex }}" data-desc="{{ $calRow-> CTCODE }} - {{ $calRow-> CTDESCRIPTION }}"  value="{{ $calRow-> CTID }}"/></td>
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

<!-- Good Receipt Dropdown -->
<div id="GRNpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='GRN_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Good Receipt Note</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="GRNTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="hdn_GRN" id="hdn_GRN"/>
            <input type="hidden" name="hdn_GRN2" id="hdn_GRN2"/></td>
          </tr>
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Good Receipt No</th>
      <th class="ROW3">Good Receipt Date</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="GRNcodesearch" class="form-control" onkeyup="GRNCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="GRNnamesearch" class="form-control" onkeyup="GRNNameFunction()"></td>
      </tr>
    </tbody>
    </table>
      <table id="GRNTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_GRN">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Good Receipt Dropdown-->

<!-- Item Code Dropdown -->
<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%"  >
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

            <input type="hidden" name="fieldid22" id="hdn_ItemID22"/>
            <input type="hidden" name="fieldid23" id="hdn_ItemID23"/>
            <input type="hidden" name="fieldid24" id="hdn_ItemID24"/>
            <input type="hidden" name="fieldid25" id="hdn_ItemID25"/>
            <input type="hidden" name="fieldid26" id="hdn_ItemID26"/>
            <input type="hidden" name="fieldid27" id="hdn_ItemID27"/>
            <input type="hidden" name="fieldid28" id="hdn_ItemID28"/>
            <input type="hidden" name="fieldid29" id="hdn_ItemID29"/>
            <input type="hidden" name="fieldid30" id="hdn_ItemID30"/>
            <input type="hidden" name="fieldid31" id="hdn_ItemID31"/>
            <input type="hidden" name="fieldid32" id="hdn_ItemID32"/>
            <input type="hidden" name="fieldid33" id="hdn_ItemID33"/>
            


            </td>
      </tr>
      <!-- <tr class="topnav"><button class="btn topnavbt" id="btnOk"><i class="fa fa-check"></i> OK</button></tr> -->
      <tr>
            <th style="width:8%;" id="all-check" >Select</th>
            <th style="width:10%;">Item Code</th>
            <th style="width:10%;">Name</th>
            <th style="width:8%;">Main UOM</th>
            <th style="width:8%;">Main QTY</th>
            <th style="width:8%;">Item Group</th>
            <th style="width:8%;">Item Category</th>
            <th style="width:8%;">Business Unit</th>
            <th style="width:8%;">ALPS Part No.</th>
            <th style="width:8%;">Customer Part No.</th>
            <th style="width:8%;">OEM Part No.</th>
            <th style="width:8%;">Status</th>
      </tr>
    </thead>
    <tbody>
    <tr>
    <td style="width:8%;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
    <td style="width:10%;"><input type="text" id="Itemcodesearch" class="form-control" onkeyup="ItemCodeFunction()"></td>
    <td style="width:10%;"><input type="text" id="Itemnamesearch" class="form-control" onkeyup="ItemNameFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemUOMsearch" class="form-control" onkeyup="ItemUOMFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemQTYsearch" class="form-control" onkeyup="ItemQTYFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemGroupsearch" class="form-control" onkeyup="ItemGroupFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemCategorysearch" class="form-control" onkeyup="ItemCategoryFunction()"></td>
    
    <td style="width:8%;"><input type="text" id="ItemBUsearch" class="form-control" onkeyup="ItemBUFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemAPNsearch" class="form-control" onkeyup="ItemAPNFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemCPNsearch" class="form-control" onkeyup="ItemCPNFunction()"></td>
    <td style="width:8%;"><input type="text" id="ItemOEMPNsearch" class="form-control" onkeyup="ItemOEMPNFunction()"></td>

    <td style="width:8%;"><input  type="text" id="ItemStatussearch" class="form-control" onkeyup="ItemStatusFunction()"></td>
    </tr>
    </tbody>
    </table>
      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%"  >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_ItemID">     
          <div class="loader" id="item_loader" style="display:none;"></div>
          
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
    <table id="altuomTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
    <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_altuom"/>
            <input type="hidden" name="fieldid2" id="hdn_altuom2"/>
            <input type="hidden" name="fieldid3" id="hdn_altuom3"/>
            <input type="hidden" name="fieldid4" id="hdn_altuom4"/></td>
          </tr>
    
    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Description</th>
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
      <table id="altuomTable2" class="display nowrap table  table-striped table-bordered" >
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
    <table id="CurrencyTable" class="display nowrap table  table-striped table-bordered" >
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
      <table id="CurrencyTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
          <!-- <tr>
            <th>GLCode</th>
            <th>GLName</th>
          </tr> -->
          
        </thead>
        <tbody>
        @foreach ($objothcurrency as $crindex=>$crRow)
        <tr >
          <td class="ROW1"> <input type="checkbox" name="SELECT_CRID_REF[]" id="cridcode_{{ $crindex }}" class="clscrid" value="{{ $crRow-> CRID }}" ></td>
          <td class="ROW2">{{ $crRow-> CRCODE }} <input type="hidden" id="txtcridcode_{{ $crindex }}" data-desc="{{ $crRow-> CRCODE }}"  value="{{ $crRow-> CRID }}"/></td>
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
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" onclick="getFocus()" style="display:none;margin-left: 90px;">
            <div id="alert-active" class="activeOk1"></div>OK</button>
			<input type="hidden" id="FocusId" >
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
.single button {
    background: #eff7fb;
    width: 30px;
    border: 1px;
    padding: 10px 0;
    margin: 5px 0;
    text-align: center;
    font-weight: bold;
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
    width: 18%;
}

.loader {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 20px;
  height: 20px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
}
/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
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


$(function() {

  <?php if(isset($objPBMAT) && !empty($objPBMAT)){ foreach($objPBMAT as $key => $row){?>
    onChangeAmount('BILL_QTY_<?php echo $key ?>','<?php echo $row->BILL_QTY;?>');
    editAltUomQty('ALT_UOMID_QTY_<?php echo $key ?>','<?php echo $row->ALT_UOMID_REF;?>','<?php echo $row->ITEMID_REF;?>','<?php echo $row->BILL_QTY;?>');
  <?php }} ?> 

  <?php if(isset($objHDR->VID_REF) && $objHDR->VID_REF !=""){ ?>
    getBillAddress('<?php echo $objHDR->VID_REF;?>')
    getShipAddress('<?php echo $objHDR->VID_REF;?>')
  <?php } ?>

});


//------------------------
  //Vendor Account
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

$('#txtvendor').click(function(event){
  

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

  $(".clsvendorid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc")+'-'+$("#txt"+fieldid+"").data("desc2"); 
        var oldVID_REF =   $("#VID_REF").val();
        var MaterialClone = $('#hdnMaterial').val();   
        var TDSClone = $('#hdnTDS').val();   
        var TNCClone = $('#hdnTNC').val();   
        var CalculationClone = $('#hdnCalculation').val();   
        var PaymentClone = $('#hdnPayment').val();   
        $('#txtvendor').val(texdesc);
        $('#VID_REF').val(txtval);
        if (txtval != oldVID_REF)
        {
            $('#Material').html(MaterialClone);
            $('#TDS').html(TDSClone);
            $('#TC').html(TNCClone);
            $('#CT').html(CalculationClone);
            $('#PaymentSlabs').html(PaymentClone);
            $('#tot_amt').val('0.00');
            $('#Row_Count1').val('1');
            $('#Row_Count2').val('1');
            $('#Row_Count3').val('1');
            $('#Row_Count4').val('1');
            $('#Row_Count5').val('1');
        }
        
        $("#vendoridpopup").hide();
        $("#vendorcodesearch").val(''); 
        $("#vendornamesearch").val(''); 
        VendorCodeFunction();

        var customid = txtval;
              if(customid!=''){
                $("#Credit_days").val('');
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
                        $("#Credit_days").val(data);     
                        
                        var d = data; 
                        d = parseInt(d) - 1;
                        var pdate =$('#PII_DT').val();
                        var ddate = new Date(pdate);
                        var newddate = new Date(ddate);
                        newddate.setDate(newddate.getDate() + d);
                        var piddate = newddate.getFullYear() + "-" + ("0" + (newddate.getMonth() + 1)).slice(-2) + "-" + ('0' + newddate.getDate()).slice(-2) ;
                        $("#DUE_DATE").val(piddate);
                        
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#Credit_days").val('');   
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
                  $.ajax({
                      url:'{{route("transaction",[$FormId,"getTDSApplicability"])}}',
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
                                  url:'{{route("transaction",[$FormId,"getTDSDetails"])}}',
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
                  
              }
              event.preventDefault();
    });

}

  
  //Vendor Account Ends
//------------------------

//------------------------
  //Goods Receipt Note Dropdown
  let grntid = "#GRNTable2";
      let grntid2 = "#GRNTable";
      let GRNheaders = document.querySelectorAll(grntid2 + " th");

      // Sort the table element when clicking on the table headers
      GRNheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(grntid, ".clsgrnid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function GRNCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("GRNcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("GRNTable2");
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

  function GRNNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("GRNnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("GRNTable2");
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

  $('#Material').on('focus','[id*="txtGRN_NO_"]',function(event){
    var customid = $('#VID_REF').val();
    var fieldid = $(this).parent().parent().find('[id*="GRNID_REF"]').attr('id');
    $("#tbody_GRN").html('');
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      })
      $.ajax({
          url:'{{route("transaction",[$FormId,"getgoodsreceiptnote"])}}',
          type:'POST',
          data:{'id':customid,'fieldid':fieldid},
          success:function(data) {
            $("#tbody_GRN").html(data);
            BindGoodsReceiptNote();
            showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $("#tbody_GRN").html('');
          },
      });

      $("#GRNpopup").show();
      var id = $(this).attr('id');
      var id2 = $(this).parent().parent().find('[id*="GRN_ID"]').attr('id');

      $('#hdn_GRN').val(id);
      $('#hdn_GRN2').val(id2);
    });

    $("#GRN_closePopup").click(function(event){
      $("#GRNpopup").hide();
    });
      
    function BindGoodsReceiptNote(){
      $(".clsgrnid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        var txtid= $('#hdn_GRN').val();
        var txt_id2= $('#hdn_GRN2').val();

        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        $("#GRNpopup").hide();
        
        $("#GRNcodesearch").val(''); 
        $("#GRNnamesearch").val(''); 
        GRNCodeFunction();
        event.preventDefault();
      });
    }

      

  //Goods Receipt Note Dropdown Ends
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
  $('#div_billto').on('focus','#txtBILLTO',function(event){
    showSelectedCheck($("#BILLTO").val(),"SELECT_BILLTO");
         $("#BillTopopup").show();
         event.preventDefault();
      });

      $("#BillToclosePopup").click(function(event){
        $("#BillTopopup").hide();
        event.preventDefault();
      });

      function BindBillAddress(){
        $(".clsbillto").dblclick(function(){
          var fieldid = $(this).attr('id');
          var txtval =    $("#txt"+fieldid+"").val();
          var texdesc =   $("#txt"+fieldid+"").data("desc");
          $('#txtBILLTO').val(texdesc);
          $('#BILLTO').val(txtval);
          $("#BillTopopup").hide();
          $("#BillTocodesearch").val(''); 
          $("#BillTonamesearch").val(''); 
          BillToCodeFunction();        
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

  $('#div_shipto').on('focus','#txtSHIPTO',function(event){
    showSelectedCheck($("#SHIPTO").val(),"SELECT_SHIPTO");
         $("#ShipTopopup").show();
         event.preventDefault();
      });

      $("#ShipToclosePopup").click(function(event){
        $("#ShipTopopup").hide();
        event.preventDefault();
      });

      function BindShipAddress(){
        $(".clsshipto").dblclick(function(){
          var fieldid = $(this).attr('id');
          var txtval =    $("#txt"+fieldid+"").val();
          var texdesc =   $(this).children('[id*="txtshipadd"]').text().trim();
          var taxstate =  $("#txt"+fieldid+"").data("desc");
          var oldShipto =   $("#SHIPTO").val();
          var MaterialClone = $('#hdnMaterial').val();   
          var TDSClone = $('#hdnTDS').val();   
          var TNCClone = $('#hdnTNC').val();   
          var CalculationClone = $('#hdnCalculation').val();   
          var PaymentClone = $('#hdnPayment').val();   

          if (txtval != oldShipto)
          {
            $('#Material').html(MaterialClone);
            $('#TDS').html(TDSClone);
            $('#TC').html(TNCClone);
            $('#CT').html(CalculationClone);
            $('#PaymentSlabs').html(PaymentClone);
            $('#tot_amt').val('0.00');
            $('#Row_Count1').val('1');
            $('#Row_Count2').val('1');
            $('#Row_Count3').val('1');
            $('#Row_Count4').val('1');
            $('#Row_Count5').val('1');
          }
          $('#txtSHIPTO').val(texdesc);
          $('#SHIPTO').val(txtval);
          $('#Tax_State').val(taxstate);
          $("#ShipTopopup").hide();
          $("#ShipTocodesearch").val(''); 
          $("#ShipTonamesearch").val(''); 
          ShipToCodeFunction();        
          event.preventDefault();
        });
      }
  //Ship Address Ends
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
  $('#TC').on('focus','#txtTNCID_popup',function(event){
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
// $('#TC').on('focus','[id*="popupTNCDID"]',function(){
//      $("#tncdetpopup").show();
//      $('#hdn_tncdet').val($(this).attr('id'));
//      event.preventDefault();
//   });

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
  $('#CT').on('focus','#txtCTID_popup',function(event){
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
        CTIDCodeFunction();
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
                  $('#Row_Count3').val(data);
                    bindCTIDDetailsEvents();
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                  $('#Row_Count3').val('0');
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
                  var TaxableAmount = $(this).find('[id*="ASSESSABLE_VALUE_"]').val();
                  if (!isNaN(TaxableAmount) && TaxableAmount.length !== 0) {
                    netTaxableAmount += parseFloat(TaxableAmount);
                    }                      
                  
                  var GSTAmount = $(this).find('[id*="TGST_AMT_"]').val();
                  if (!isNaN(GSTAmount) && GSTAmount.length !== 0) {
                    netGSTAmount += parseFloat(GSTAmount);
                    }
                  
                  var TotalAmount = $(this).find('[id*="VALUE_AFTER_GST_"]').val();
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
            var tttdsamt21 = 0.00;
              $('#Material').find('.participantRow').each(function()
              {
                  if($('#GST_N_Avail').is(':checked') == true)
                  {
                    tvalue = $(this).find('[id*="ASSESSABLE_VALUE_"]').val();
                    totalvalue = parseFloat(totalvalue) + parseFloat(tvalue);
                    totalvalue = parseFloat(totalvalue).toFixed(2);
                  }
                  else
                  {
                    tvalue = $(this).find('[id*="VALUE_AFTER_GST_"]').val();
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
                  if($('#GST_N_Avail').is(':checked') == true)
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
            
              if($('#drpTDS').val() == 'Yes')
              {
                $('#TDS').find('.participantRow7').each(function()
                {
                    if($(this).find('[id*="TOT_TD_AMT"]').val() != '' && $(this).find('[id*="TOT_TD_AMT"]').val() != '.00')
                    {
                      tttdsamt21 = $(this).find('[id*="TOT_TD_AMT"]').val();
                      totalvalue = parseFloat(parseFloat(totalvalue)+parseFloat(tttdsamt21)).toFixed(2);
                    }
                });
              }
                $('#tot_amt').val(totalvalue);
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
// $('#CT').on('focus','[id*="popupTID"]',function(){
//      $("#ctiddetpopup").show();
//      $('#hdn_ctiddet').val($(this).attr('id'));
//      event.preventDefault();
//   });

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
                var amount1 = $(this).find('[id*="ASSESSABLE_VALUE_"]').val();

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
//Calculation Details Ends
//------------------------
//------------------------
  //HSN Account
    let hsntid = "#hsnCodeTable2";
    let hsntid2 = "#hsnCodeTable";
    let hsnheaders = document.querySelectorAll(hsntid2 + " th");

      // Sort the table element when clicking on the table headers
      hsnheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(hsntid, ".clshsnid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function hsnCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("hsncodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("hsnCodeTable2");
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

  function hsnNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("hsnnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("hsnCodeTable2");
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

  $('#Material').on('focus','[id*="txtHSN"]',function(event){
    $("#hsn_popup").show();
    var id = $(this).attr('id');
    var id2 = $(this).parent().parent().find('[id*="HSNID_REF"]').attr('id');
    var id3 = $(this).parent().parent().find('[id*="IGST_"]').attr('id');
    var id4 = $(this).parent().parent().find('[id*="CGST_"]').attr('id');
    var id5 = $(this).parent().parent().find('[id*="SGST_"]').attr('id');
    $('#hdn_hsnID').val(id);
    $('#hdn_hsnID2').val(id2);
    $('#hdn_hsnID3').val(id3);
    $('#hdn_hsnID4').val(id4);
    $('#hdn_hsnID5').val(id5);
    event.preventDefault();
  });

  $("#hsn_closePopup").click(function(event){
    $("#hsn_popup").hide();
    event.preventDefault();
  });
  
  $(".clshsnid").dblclick(function(){
      var fieldid = $(this).attr('id');
      var txtid =    $("#txt"+fieldid+"").val();
      var txtval =   $("#txt"+fieldid+"").data("desc");
      var txtdesc =   $("#txt"+fieldid+"").data("desc2");

      var txt_id1= $('#hdn_hsnID').val();
      var txt_id2= $('#hdn_hsnID2').val();
      $('#'+txt_id1).val(txtval);
      $('#'+txt_id2).val(txtid);
      $("#hsn_popup").hide();
      $("#hsncodesearch").val(''); 
      $("#hsnnamesearch").val(''); 
      hsnCodeFunction(); 
      var taxstate = $('#Tax_State').val();     
      var customid = txtid;
        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
        });
        $.ajax({
              url:'{{route("transaction",[$FormId,"gettaxCode"])}}',
              type:'POST',
              data:{'id':customid,'taxstate':taxstate},
              success:function(data) {                
                  $('#hdn_hsnID6').val(data);
              },
              error:function(data){
                console.log("Error: Something went wrong.");
              },
        });
        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
          $.ajax({
              url:'{{route("transaction",[$FormId,"gettax"])}}',
              type:'POST',
              data:{'id':customid,'taxstate':taxstate},
              success:function(data) {
                if(taxstate == 'WithinState')
                {
                  var txt_id3= $('#hdn_hsnID3').val();
                  var txt_id4= $('#hdn_hsnID4').val();
                  $('#'+txt_id3).val('0.0000');
                  $('#'+txt_id3).parent().parent().find('[id*="IGST_AMT"]').val('0.00');
                  $('#'+txt_id4).val(data);
                  var taxamount = $('#'+txt_id3).parent().parent().find('[id*="TAX_AMT"]').val();
                  var amt1 = parseFloat((parseFloat(data)*parseFloat(taxamount))/100).toFixed(2);
                  $('#'+txt_id4).parent().parent().find('[id*="CGST_AMT"]').val(amt1);
                  var TaxCode1 = $('#hdn_hsnID6').val();
                    $.ajaxSetup({
                          headers: {
                              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                          }
                    });
                    $.ajax({
                          url:'{{route("transaction",[$FormId,"gettax2"])}}',
                          type:'POST',
                          data:{'id':customid,'taxstate':taxstate,'TaxCode1':TaxCode1},
                          success:function(data) {                
                                var txt_id5= $('#hdn_hsnID5').val();
                                $('#'+txt_id5).val(data);
                                var taxamount2 = $('#'+txt_id5).parent().parent().find('[id*="TAX_AMT"]').val();
                                var amt2 = parseFloat((parseFloat(data)*parseFloat(taxamount2))/100).toFixed(2);
                                $('#'+txt_id4).parent().parent().find('[id*="SGST_AMT"]').val(amt2);
                                var amt4 = $('#'+txt_id5).parent().parent().find('[id*="CGST_AMT"]').val();
                                var amt3 = parseFloat(parseFloat(taxamount2)+parseFloat(amt2)+parseFloat(amt4)).toFixed(2);
                                $('#'+txt_id4).parent().parent().find('[id*="TT_AMT"]').val(amt3);
                          },
                          error:function(data){
                            console.log("Error: Something went wrong.");
                          },
                    });
                }
                else if(taxstate == 'OutofState')
                {
                  var txt_id3= $('#hdn_hsnID3').val();
                  var txt_id4= $('#hdn_hsnID4').val();
                  var txt_id5= $('#hdn_hsnID5').val();
                  $('#'+txt_id4).val('0.0000');
                  $('#'+txt_id4).parent().parent().find('[id*="CGST_AMT"]').val('0.00');
                  $('#'+txt_id5).val('0.0000');
                  $('#'+txt_id4).parent().parent().find('[id*="SGST_AMT"]').val('0.00');
                  $('#'+txt_id3).val(data);
                  var taxamount = $('#'+txt_id3).parent().parent().find('[id*="TAX_AMT"]').val();
                  var amt1 = parseFloat((parseFloat(data)*parseFloat(taxamount))/100).toFixed(2);
                  $('#'+txt_id4).parent().parent().find('[id*="IGST_AMT"]').val(amt1);
                  var amt3 = parseFloat(parseFloat(taxamount)+parseFloat(amt1)).toFixed(2);
                  $('#'+txt_id4).parent().parent().find('[id*="TT_AMT"]').val(amt3);
                }
              },
              error:function(data){
                console.log("Error: Something went wrong.");
              },
          });
    var totalamount = 0.00;
    $('#Material').find('.participantRow').each(function()
    {
        if($(this).find('[id*="TT_AMT"]').val() != '')
        {
          var ttamt21 = $(this).find('[id*="TT_AMT"]').val();
          totalamount = parseFloat(parseFloat(totalamount)+parseFloat(ttamt21)).toFixed(2);
        }
    });

    $('#TC').find('.participantRow3').each(function()
    {
        if($(this).find('[id*="TOT_TD_AMT"]').val() != '')
        {
          var tttdsamt21 = $(this).find('[id*="TOT_TD_AMT"]').val();
          totalamount = parseFloat(parseFloat(totalamount)+parseFloat(tttdsamt21)).toFixed(2);
        }
    });
    $('#tot_amt').val(totalamount);
              
      event.preventDefault();
  });
  //HSN Account Ends
//------------------------
//------------------------
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

  
//------------------------
     
$(document).ready(function(e) {
var Material = $("#Material").html(); 
$('#hdnMaterial').val(Material);
var TDS = $("#TDS").html(); 
$('#hdnTDS').val(TDS);
var TNC = $("#TC").html(); 
$('#hdnTNC').val(TNC);
var Calculation = $("#CT").html(); 
$('#hdnCalculation').val(Calculation);
var Payment = $("#PaymentSlabs").html(); 
$('#hdnPayment').val(Payment);
$('#tot_amt').ForceNumericOnly();
$('#Credit_days').ForceNumericOnly();
$("[id*='QTY_']").ForceNumericOnly();
$("[id*='BILL_RATEPUOM_']").ForceNumericOnly();
$("[id*='DISC_PER_']").ForceNumericOnly();
$("[id*='DISC_AMT_']").ForceNumericOnly();
$("[id*='TAX_AMT']").ForceNumericOnly();
$("[id*='TGST_AMT_']").ForceNumericOnly();
$("[id*='RATE']").ForceNumericOnly();
$("[id*='VALUE_']").ForceNumericOnly();
$("[id*='IGST']").ForceNumericOnly();
$("[id*='CGST']").ForceNumericOnly();
$("[id*='SGST']").ForceNumericOnly();
$("[id*='TT_AMT']").ForceNumericOnly();
$("[id*='hdnCCAMT']").ForceNumericOnly();
$("[id*='ASSESSABLE_VL_']").ForceNumericOnly();
$("[id*='TDS_RATE']").ForceNumericOnly();
$("[id*='TDS_AMT']").ForceNumericOnly();
$("[id*='SURCHARGE_RATE']").ForceNumericOnly();
$("[id*='SURCHARGE_AMT']").ForceNumericOnly();
$("[id*='CESS_RATE']").ForceNumericOnly();
$("[id*='CESS_AMT']").ForceNumericOnly();
$("[id*='SPCESS_RATE']").ForceNumericOnly();
$("[id*='SPCESS_AMT']").ForceNumericOnly();
$("[id*='TOT_TD_AMT']").ForceNumericOnly();

var objlastdt = <?php echo json_encode($objHDR->PII_DT); ?>;
var ardate    = <?php echo json_encode($objHDR->PII_DT); ?>;

$('#PII_DT').attr('min',objlastdt);
$('#PII_DT').attr('max',ardate);

var objtnc = <?php echo json_encode($objPBTNC); ?>;
var tncheader = <?php echo json_encode($objTNCHeader); ?>;
var tncdetails = <?php echo json_encode($objTNCDetails); ?>;
var calheader = <?php echo json_encode($objCalHeader); ?>;
var caldetails = <?php echo json_encode($objCalDetails); ?>;
var PBCal = <?php echo json_encode($objPBCAL); ?>;
var apudf = <?php echo json_encode($objUdfPBData); ?>;
var count2 = <?php echo json_encode($objCountUDF); ?>;  
var pbudf = <?php echo json_encode($objPBUDF); ?>;
var udfforpb = <?php echo json_encode($objUdfPBData2); ?>;
var totalvalue = 0.00;

//TNC TAB Data Start
//------------------------

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

                      strinp = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"  > ';       

                    }
                    else if(chkvaltype=='time'){
                      strinp= '<input type="time" placeholder="h:i" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"  > ';

                    }
                    else if(chkvaltype=='numeric'){
                      strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"   > ';

                    }
                    else if(chkvaltype=='text'){

                      strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" autocomplete="off" class="form-control"  > ';
                    
                    }
                    else if(chkvaltype=='boolean'){
                      if(tncvalue.VALUE == "1")
                      {
                        strinp = '<input type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" checked> ';
                      }
                      else{
                        strinp = '<input type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" > ';
                      }                    
                    }
                    else if(chkvaltype=='combobox'){
                      var txtoptscombo =   tncdvalue.DESCRIPTIONS;
                      var strarray = txtoptscombo.split(',');
                      var opts = '';
                      for (var i = 0; i < strarray.length; i++) 
                      {
                          opts = opts + '<option value="'+strarray[i]+'">'+strarray[i]+'</option> ';
                      }
                      strinp = '<select name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" required>'+opts+'</select>' ;                   
                    }
                    $('#'+txt_id4).html('');  
                    $('#'+txt_id4).html(strinp);   //set dynamic input
                    $('#'+dynamicid).val(tncvalue.VALUE);
                    $('#TNCismandatory_'+tnckey).val(tncdvalue.IS_MANDATORY); // mandatory
                
            }
        });
    });

//TNC TAB Data End
//------------------------
//Calculation Template TAB Data Start
//------------------------
$.each(PBCal, function( pbckey, pbcvalue ) {
        $.each( calheader, function( calkey, calvalue ){ 
            if(pbcvalue.CTID_REF == calvalue.CTID)
            {
                $('#txtCTID_popup').val(calvalue.CTCODE);
            }
        });
        $.each( caldetails, function( caldkey, caldvalue ){ 
            if(pbcvalue.TID_REF == caldvalue.TID)
            {
                $('#popupTID_'+pbckey).val(caldvalue.COMPONENT);
                $('#BASIS_'+pbckey).val(caldvalue.BASIS);
                $('#SQNO_'+pbckey).val(caldvalue.SQNO);
                $('#FORMULA_'+pbckey).val(caldvalue.FORMULA);
                
            }
        });
        
    });
    
    // $('#tot_amt').val(totalvalue);
    bindTotalValue();
    

//Calculation Template TAB Data End
//------------------------

//UDF Template TAB Data Start
//------------------------
$.each( pbudf, function( pbukey, pbuvalue ) {
        $.each( udfforpb, function( upbkey, upbvalue ) { 
            if(pbuvalue.UDFPIIID_REF == upbvalue.UDFPIIID)
            {
                $('#popupUDFPBID_'+pbukey).val(upbvalue.LABEL);
            }
        
            if(pbuvalue.UDFPIIID_REF == upbvalue.UDFPIIID)
            {        
                    var txtvaltype2 =   upbvalue.VALUETYPE;
                    var txt_id41 = $('#udfinputid_'+pbukey).attr('id');
                    var strdyn2 = txt_id41.split('_');
                    var lastele2 =   strdyn2[strdyn2.length-1];
                    var dynamicid2 = "udfvalue_"+lastele2;
                    
                    var chkvaltype2 =  txtvaltype2.toLowerCase();
                    var strinp2 = '';

                    if(chkvaltype2=='date'){

                    strinp2 = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';       

                    }
                    else if(chkvaltype2=='time'){
                    strinp2= '<input type="time" placeholder="h:i" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';

                    }
                    else if(chkvaltype2=='numeric'){
                    strinp2 = '<input type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"   > ';

                    }
                    else if(chkvaltype2=='text'){

                    strinp2 = '<input type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';
                    
                    }
                    else if(chkvaltype2=='boolean'){
                      if(pbuvalue.VALUE == "1")
                      {
                        strinp2 = '<input type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" checked> ';
                      }
                      else{
                        strinp2 = '<input type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" > ';
                      }
                    }
                    else if(chkvaltype2=='combobox'){

                    var txtoptscombo2 =   upbvalue.DESCRIPTIONS;
                    var strarray2 = txtoptscombo2.split(',');
                    var opts2 = '';

                    for (var i = 0; i < strarray2.length; i++) {
                        opts2 = opts2 + '<option value="'+strarray2[i]+'">'+strarray2[i]+'</option> ';
                    }

                    strinp2 = '<select name="'+dynamicid2+ '" id="'+dynamicid2+'" class="form-control" required>'+opts2+'</select>' ;
                   
                    }
                   
                    
                    $('#'+txt_id41).html('');  
                    $('#'+txt_id41).html(strinp2);   //set dynamic input
                    $('#'+dynamicid2).val(pbuvalue.VALUE);
                    $('#UDFismandatory_'+pbukey).val(upbvalue.ISMANDATORY); // mandatory
                
            }
        });
    });

//UDF Template TAB Data End
//------------------------

$('#udf').find('.participantRow4').each(function(){
  var txt_id4 = $(this).find('[id*="udfinputid"]').attr('id');
  var udfid = $(this).find('[id*="UDFPBID_REF"]').val();
  $.each( apudf, function( apukey, apuvalue ) {
    if(apuvalue.UDFPIIID == udfid)
    {
      var txtvaltype2 =   apuvalue.VALUETYPE;
      var strdyn2 = txt_id4.split('_');
      var lastele2 =   strdyn2[strdyn2.length-1];
      var dynamicid2 = "udfvalue_"+lastele2;
      
      var chkvaltype2 =  txtvaltype2.toLowerCase();
      var strinp2 = '';

      if(chkvaltype2=='date'){
      strinp2 = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';       
      }
      else if(chkvaltype2=='time'){
      strinp2= '<input type="time" placeholder="h:i" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';
      }
      else if(chkvaltype2=='numeric'){
      strinp2 = '<input type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"   > ';
      }
      else if(chkvaltype2=='text'){
      strinp2 = '<input type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';          
      }
      else if(chkvaltype2=='boolean'){            
          strinp2 = '<input type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" > ';
      }
      else if(chkvaltype2=='combobox'){
      var txtoptscombo2 =   apuvalue.DESCRIPTIONS;
      var strarray2 = txtoptscombo2.split(',');
      var opts2 = '';
      for (var i = 0; i < strarray2.length; i++) {
          opts2 = opts2 + '<option value="'+strarray2[i]+'">'+strarray2[i]+'</option> ';
      }
      strinp2 = '<select name="'+dynamicid2+ '" id="'+dynamicid2+'" class="form-control" required>'+opts2+'</select>' ;          
      }
      $('#'+txt_id4).html('');  
      $('#'+txt_id4).html(strinp2);
    }
  });
});

$('#btnAdd').on('click', function() {
  var viewURL = '{{route("transaction",[$FormId,"add"])}}';
              window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
});

function bindTotalValue()
    {
      var totalvalue = 0.00;
      var tvalue = 0.00;
      var ctvalue = 0.00;
      var ctgstvalue = 0.00;
      var tttdsamt21 = 0.00;
      $('#Material').find('.participantRow').each(function()
      {
        if($('#GST_N_Avail').is(':checked') == true)
        {
          tvalue = $(this).find('[id*="ASSESSABLE_VALUE_"]').val();
          totalvalue = parseFloat(totalvalue) + parseFloat(tvalue);
          totalvalue = parseFloat(totalvalue).toFixed(2);
        }
        else
        {
          tvalue = $(this).find('[id*="VALUE_AFTER_GST_"]').val();
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
          if($('#GST_N_Avail').is(':checked') == true)
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
      if($('#drpTDS').val() == 'Yes')
      {
        $('#TDS').find('.participantRow7').each(function()
        {
            if($(this).find('[id*="TOT_TD_AMT"]').val() != '' && $(this).find('[id*="TOT_TD_AMT"]').val() != '.00')
            {
              tttdsamt21 = $(this).find('[id*="TOT_TD_AMT"]').val();
              totalvalue = parseFloat(parseFloat(totalvalue)+parseFloat(tttdsamt21)).toFixed(2);
            }
        });
      }
      $('#tot_amt').val(totalvalue);
  }

  function bindGSTCalTemplate()
  { 
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
                  if($(this).find('[id*="ASSESSABLE_VALUE_"]').val() != '' && $(this).find('[id*="ASSESSABLE_VALUE_"]').val() != '.00') {                   
                  var TaxableAmount = $(this).find('[id*="ASSESSABLE_VALUE_"]').val();
                  if (!isNaN(TaxableAmount) && TaxableAmount.length !== 0) {
                    netTaxableAmount += parseFloat(TaxableAmount);
                    }                      
                  }
                  else
                  {
                    var TaxableAmount = 0;
                    netTaxableAmount += 0;
                  }
                  if($(this).find('[id*="TGST_AMT_"]').val() != '' && $(this).find('[id*="TGST_AMT_"]').val() != '.00') {                   
                    var GSTAmount = $(this).find('[id*="TGST_AMT_"]').val();
                    if (!isNaN(GSTAmount) && GSTAmount.length !== 0) {
                      netGSTAmount += parseFloat(GSTAmount);
                    }                      
                  }
                  else
                  {
                    var GSTAmount = 0;
                    netGSTAmount += 0;
                  }
                  if($(this).find('[id*="VALUE_AFTER_GST_"]').val() != '' && $(this).find('[id*="VALUE_AFTER_GST_"]').val() != '.00') {                   
                    var TotalAmount = $(this).find('[id*="VALUE_AFTER_GST_"]').val();
                    if (!isNaN(TotalAmount) && TotalAmount.length !== 0) {
                      netTotalAmount += parseFloat(TotalAmount);
                    }                      
                  }
                  else
                  {
                    var TotalAmount = 0;
                    netTotalAmount += 0;
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
                        if(netTaxableAmount != '0')
                        {
                          totamount = parseFloat((rate * netTaxableAmount)/100).toFixed(2);
                        }
                        else
                        {
                          totamount = 0;
                        }
                      }
                      if(basis == 'Item GST Amount')
                      {
                        if(netGSTAmount != '0')
                        {
                          totamount = parseFloat((rate * netGSTAmount)/100).toFixed(2);
                        }
                        else
                        {
                          totamount = 0;
                        }
                      }
                      if(basis == 'Amount After GST Item')
                      {
                        if(netTotalAmount != '0')
                        {
                          totamount = parseFloat((rate * netTotalAmount)/100).toFixed(2);
                        }
                        else
                        {
                          totamount = 0;
                        }
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
              tvalue = $(this).find('[id*="VALUE_AFTER_GST_"]').val();
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
            $('#tot_amt').val(totalvalue);
            event.preventDefault();
    }

$('#GST_N_Avail').on('change', function() 
{
    // bindTotalValue();
    if($('#CTID_REF').val()!='')
    {
      bindGSTCalTemplate();
    }
    bindTotalValue();
    event.preventDefault();
});




$("#TDS").on('change', "[id*='TDSApplicable']", function() 
{
  var totalamount = 0.00;
  if($(this).is(':checked') == true)
  {
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
    $(this).parent().parent().find("[id*='ASSESSABLE_VL_']").val('0.00');
    $(this).parent().parent().find("[id*='AMT_']").val('0.00');
  }
  bindTotalValue();
  if($('#CTID_REF').val()!='')
  {
    bindGSTCalTemplate();
  }
  bindTotalValue();
  if($('#tot_amt').val() < '0.00')
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




$("#TDS").on('focusout', "[id*='ASSESSABLE_VL_']", function() 
{
  var totalamount = 0.00;
    if(intRegex.test($(this).val())){
      $(this).val($(this).val() +'.00');
    }
    var taxamtTDS =  $(this).parent().parent().find("[id*='ASSESSABLE_VL_TDS']").val();
    var tdsamt = 0.00;
    var tdsrate = $(this).parent().parent().find("[id*='TDS_RATE_']").val();
    var tdsexempt = $(this).parent().parent().find("[id*='TDS_EXEMPT_']").val();
    if (parseFloat(taxamtTDS) > parseFloat(tdsexempt))
    {
        tdsamt = parseFloat(((parseFloat(taxamtTDS) - parseFloat(tdsexempt))*parseFloat(tdsrate))/100).toFixed(2);
    }
    else
    {
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

    $('#Material').find('.participantRow').each(function()
    {
        if($(this).find('[id*="TT_AMT"]').val() != '')
        {
          var ttamt21 = $(this).find('[id*="TT_AMT"]').val();
          totalamount = parseFloat(parseFloat(totalamount)+parseFloat(ttamt21)).toFixed(2);
        }
    });

    $('#TDS').find('.participantRow7').each(function()
    {
        if($(this).find('[id*="TOT_TD_AMT"]').val() != '')
        {
          var tttdsamt21 = $(this).find('[id*="TOT_TD_AMT"]').val();
          totalamount = parseFloat(parseFloat(totalamount)+parseFloat(tttdsamt21)).toFixed(2);
        }
    });
    $('#tot_amt').val(totalamount);
});

$('#PaymentSlabs').on('change','[id*="PAY_DAYS_"]',function( event ) {
    var d = $(this).val(); 
    d = parseInt(d) - 1;
    var pdate =$('#PII_DT').val();
    var ddate = new Date(pdate);
    var newddate = new Date(ddate);
    newddate.setDate(newddate.getDate() + d);
    var piddate = newddate.getFullYear() + "-" + ("0" + (newddate.getMonth() + 1)).slice(-2) + "-" + ('0' + newddate.getDate()).slice(-2) ;
    $(this).parent().parent().find('[id*="DUE_DATE_"]').val(piddate);
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
      if(intRegex.test($(this).val())){
        $(this).val($(this).val() +'.0000');
      }
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
      if(intRegex.test($(this).val())){
        $(this).val($(this).val() +'.0000');
      }
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
      if(intRegex.test($(this).val())){
        $(this).val($(this).val() +'.0000');
      }
      event.preventDefault();
  }); 

//delete row
$("#Material").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('.participantRow').length;
    if (rowCount > 1) {
      var totalvalue = $('#tot_amt').val();
      totalvalue = parseFloat(totalvalue - $(this).closest('.participantRow').find('[id*="VALUE_AFTER_GST"]').val()).toFixed(2);
      $('#tot_amt').val(totalvalue);
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
    }
    event.preventDefault();
});
$("#PaymentSlabs").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('.participantRow6').length;
    if (rowCount > 1) {
    $(this).closest('.participantRow6').remove();     
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
    }
    event.preventDefault();
});


//add row
$("#Material").on('click', '.add', function() {
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
  $clone.find('input:text').removeAttr('disabled');
  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count1').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count1').val(rowCount1);
  $clone.find('.remove').removeAttr('disabled'); 
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
  $clone.find('input:hidden').val('');
  $tr.closest('table').append($clone);         
  var rowCount5 = $('#Row_Count5').val();
  rowCount5 = parseInt(rowCount5)+1;
  $('#Row_Count5').val(rowCount5);
  $clone.find('.remove').removeAttr('disabled'); 
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
  window.location.href = "{{route('transaction',[$FormId,'add'])}}";
}//fnUndoYes


window.fnUndoNo = function (){
    $("#PII_NO").focus();
}//fnUndoNo

});
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {




    $('#frm_trn_edit1').bootstrapValidator({       
        fields: {
            txtlabel: {
                validators: {
                    notEmpty: {
                        message: 'The Document Number is required'
                    }
                }
            },            
        },
        submitHandler: function(validator, form, submitButton) {
            alert( "Handler for .submit() called." );
             event.preventDefault();
             $("#frm_trn_edit").submit();
        }
    });
});



$("#btnSave").click(function() {
  var formPurchaseBill = $("#frm_trn_edit");
  if(formPurchaseBill.valid()){



 $("#FocusId").val('');
  var PII_NO          	=   $.trim($("#PII_NO").val());
  var PII_DT          	=   $.trim($("#PII_DT").val());
  var VID_REF   			=   $.trim($("#VID_REF").val());
  var SOFC				=   $('#SOFC').is(":checked");
  var CRID_REF        	=   $.trim($("#CRID_REF").val());
  var CONVFACT        	=   $.trim($("#CONVFACT").val());
  var VENDOR_INNO			=   $.trim($("#VENDOR_INNO").val());
  var VENDOR_INDT   		=   $.trim($("#VENDOR_INDT").val());
  var BOE_NO       		=   $.trim($("#BOE_NO").val());
  var BOE_DT       		=   $.trim($("#BOE_DT").val());
  var IMPORT_DUTYID_REF	=   $.trim($("#IMPORT_DUTYID_REF").val());
  var Credit_days			=   $.trim($("#Credit_days").val());

 
  if(PII_NO ===""){
    $("#FocusId").val('PII_NO');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter value in IPO No.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
	else if(PII_DT ===""){
		$("#FocusId").val('PII_DT');
		$("#PII_DT").val('');  
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please select IPO Date.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	}  
	else if(VID_REF ===""){
		$("#FocusId").val('txtvendor');
		$("#VID_REF").val('');  
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please Select Vendor.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	}
	else if(SOFC == false){
		$("#FocusId").val('SOFC');
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please select FC.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	}
	else if(CRID_REF ===""){
		$("#FocusId").val('txtCRID_popup'); 
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please select Currency.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	} 
	else if(CONVFACT ===""){
		$("#FocusId").val('CONVFACT'); 
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please select Conversion Factor.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	} 
	else if(VENDOR_INNO ===""){
		$("#FocusId").val('VENDOR_INNO'); 
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please enter Vendor Invoice No.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	} 
	else if(VENDOR_INDT ===""){
		$("#FocusId").val('VENDOR_INDT'); 
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please select Vendor Invoice Date.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	} 
	else if(BOE_NO ===""){
		$("#FocusId").val('BOE_NO'); 
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please enter BOE No.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	} 
	else if(BOE_DT ===""){
		$("#FocusId").val('BOE_DT'); 
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please select BOE Date.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	} 
	else if(IMPORT_DUTYID_REF ===""){
		$("#FocusId").val('IMPORT_DUTYID_REF'); 
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please select Imort Duty');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	} 
	else if(Credit_days ===""){
		$("#FocusId").val('Credit_days'); 
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please select Credit Days.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	} 
  else{
    event.preventDefault();
    var allblank0 	= [];
		var allblank1 	= [];
		var allblank2 	= [];
		var allblank3 	= [];
		var allblank4 	= [];
		var allblank5 	= [];
		var allblank6 	= [];
		var allblank7 	= [];
		var allblank8 	= [];
		var allblank9 	= [];
		var allblank10 	= [];
		var allblank11 	= [];
		var allblank12 	= [];
		var allblank13 	= [];
		var focustext   = "";
      
		$('#Material').find('.participantRow').each(function(){

			if($.trim($(this).find("[id*=GRNID_REF]").val()) ===""){
				allblank0.push('false');
				focustext = $(this).find("[id*=txtGRN_NO]").attr('id');
			}
      else if($.trim($(this).find("[id*=ITEMID_REF]").val()) ===""){
				allblank1.push('false');
				focustext = $(this).find("[id*=popupITEMID]").attr('id');
			}
			else if($.trim($(this).find("[id*=SO_QTY]").val()) ===""){
				allblank2.push('false');
				focustext = $(this).find("[id*=SO_QTY]").attr('id');
			}
      else if($.trim($(this).find("[id*=BILL_QTY]").val()) ===""){
				allblank3.push('false');
				focustext = $(this).find("[id*=BILL_QTY]").attr('id');
			}
      else if(parseFloat($.trim($(this).find("[id*=BILL_QTY]").val())) > parseFloat($.trim($(this).find("[id*=SO_QTY]").val())) ){
				allblank4.push('false');
				focustext = $(this).find("[id*=BILL_QTY]").attr('id');
			}
			else if($.trim($(this).find("[id*=RATEPUOM]").val()) ===""){
				allblank5.push('false');
				focustext = $(this).find("[id*=RATEPUOM]").attr('id');
			}
			else{
        allblank0.push('true');
				allblank1.push('true');
				allblank2.push('true');
				allblank3.push('true');
        allblank4.push('true');
        allblank5.push('true');
				focustext   = "";
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
        if($('#CTID_REF').val() !="")
        {
            $('#CT').find('.participantRow5').each(function(){
              if($.trim($(this).find("[id*=TID_REF]").val())!="")
                {
                  allblank8.push('true');
                        if($(this).find("[id*=calGST]").is(":checked") == true)
                        {
                          if($.trim($('#Tax_State').val())!="WithinState")
                          {
                            if($.trim($(this).find("[id*=calIGST]").val())!="0")
                            {
                              allblank9.push('true');
                            }
                            else
                            {
                              allblank9.push('false');
                            }
                          }
                          else
                          {
                            if($.trim($(this).find("[id*=calCGST]").val())!="0")
                            {
                              allblank9.push('true');
                            }
                            else
                            {
                              allblank9.push('false');
                            }
                            if($.trim($(this).find("[id*=calSGST]").val())!="0")
                            {
                              allblank9.push('true');
                            }
                            else
                            {
                              allblank9.push('false');
                            }
                          }
                        } 
                }
                else
                {
                    allblank8.push('false');
                } 
            });
        }
        $('#PaymentSlabs').find('.participantRow6').each(function(){
              if($.trim($(this).find("[id*=PAY_DAYS_]").val())!="")
                {
                  if($.trim($(this).find('[id*="DUE_"]').val()) != "")
                  {
                    allblank10.push('true');
                  }
                  else
                  {
                    allblank10.push('false');
                  }       
                }                
        });
        $('#udf').find('.participantRow4').each(function(){
              if($.trim($(this).find("[id*=UDFPBID_REF_]").val())!="")
                {
                    allblank8.push('true');
                        if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
                              if($.trim($(this).find('[id*="udfvalue"]').val()) != "")
                              {
                                allblank11.push('true');
                              }
                              else
                              {
                                allblank11.push('false');
                              }
                        }  
                }                
        });

    }
        
        if(jQuery.inArray("false", allblank0) !== -1){
          $("#alert").modal('show');
          $("#FocusId").val(focustext);
          $("#AlertMessage").text('Please select GRN No in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        else if(jQuery.inArray("false", allblank1) !== -1){
          $("#alert").modal('show');
          $("#FocusId").val(focustext);
          $("#AlertMessage").text('Please select Item Code in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        else if(jQuery.inArray("false", allblank2) !== -1){
          $("#alert").modal('show');
          $("#FocusId").val(focustext);
          $("#AlertMessage").text('Please enter Received Qty MU in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        else if(jQuery.inArray("false", allblank3) !== -1){
          $("#alert").modal('show');
          $("#FocusId").val(focustext);
          $("#AlertMessage").text('Please enter Bill Qty (MU) in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
        }
        else if(jQuery.inArray("false", allblank4) !== -1){
          $("#alert").modal('show');
          $("#FocusId").val(focustext);
          $("#AlertMessage").text('Bill Qty (MU) should not greater then Received Qty MU in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
        }
        else if(jQuery.inArray("false", allblank5) !== -1){
          $("#FocusId").val(focustext);
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please enter Rate as per MU in Material Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          }  
          else if(jQuery.inArray("false", allblank6) !== -1){
          $("#FocusId").val(focustext);
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please select Terms & Condition Description in T&C Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          } 
        
          
        else if(jQuery.inArray("false", allblank7) !== -1){
          $("#FocusId").val($("#tot_amt"));
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please enter Value / Comment in T&C Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          } 
        else if(jQuery.inArray("false", allblank9) !== -1){
          $("#FocusId").val($("#tot_amt"));
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please enter GST Rate in Calculation Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          }
        else if(jQuery.inArray("false", allblank10) !== -1){
          $("#FocusId").val($("#tot_amt"));
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please enter Due details in Payment Slab Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          } 
        else if(jQuery.inArray("false", allblank11) !== -1){
          $("#FocusId").val($("#tot_amt"));
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please enter Value / Comment in UDF Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
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
});

$("#btnApprove").click(function() {
  var formPurchaseBill = $("#frm_trn_edit");
  if(formPurchaseBill.valid()){


 $("#FocusId").val('');
  var PII_NO          	=   $.trim($("#PII_NO").val());
  var PII_DT          	=   $.trim($("#PII_DT").val());
  var VID_REF   			=   $.trim($("#VID_REF").val());
  var SOFC				=   $('#SOFC').is(":checked");
  var CRID_REF        	=   $.trim($("#CRID_REF").val());
  var CONVFACT        	=   $.trim($("#CONVFACT").val());
  var VENDOR_INNO			=   $.trim($("#VENDOR_INNO").val());
  var VENDOR_INDT   		=   $.trim($("#VENDOR_INDT").val());
  var BOE_NO       		=   $.trim($("#BOE_NO").val());
  var BOE_DT       		=   $.trim($("#BOE_DT").val());
  var IMPORT_DUTYID_REF	=   $.trim($("#IMPORT_DUTYID_REF").val());
  var Credit_days			=   $.trim($("#Credit_days").val());
	

  if(PII_NO ===""){
    $("#FocusId").val('PII_NO');
    $("#ProceedBtn").focus();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter value in IPO No.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
	else if(PII_DT ===""){
		$("#FocusId").val('PII_DT');
		$("#PII_DT").val('');  
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please select IPO Date.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	}  
	else if(VID_REF ===""){
		$("#FocusId").val('txtvendor');
		$("#VID_REF").val('');  
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please Select Vendor.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	}
	else if(SOFC == false){
		$("#FocusId").val('SOFC');
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please select FC.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	}
	else if(CRID_REF ===""){
		$("#FocusId").val('txtCRID_popup'); 
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please select Currency.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	} 
	else if(CONVFACT ===""){
		$("#FocusId").val('CONVFACT'); 
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please select Conversion Factor.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	} 
	else if(VENDOR_INNO ===""){
		$("#FocusId").val('VENDOR_INNO'); 
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please enter Vendor Invoice No.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	} 
	else if(VENDOR_INDT ===""){
		$("#FocusId").val('VENDOR_INDT'); 
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please select Vendor Invoice Date.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	} 
	else if(BOE_NO ===""){
		$("#FocusId").val('BOE_NO'); 
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please enter BOE No.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	} 
	else if(BOE_DT ===""){
		$("#FocusId").val('BOE_DT'); 
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please select BOE Date.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	} 
	else if(IMPORT_DUTYID_REF ===""){
		$("#FocusId").val('IMPORT_DUTYID_REF'); 
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please select Imort Duty');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	} 
	else if(Credit_days ===""){
		$("#FocusId").val('Credit_days'); 
		$("#ProceedBtn").focus();
		$("#YesBtn").hide();
		$("#NoBtn").hide();
		$("#OkBtn1").show();
		$("#AlertMessage").text('Please select Credit Days.');
		$("#alert").modal('show');
		$("#OkBtn1").focus();
		return false;
	} 
 else{
  event.preventDefault();
    var allblank0 	= [];
		var allblank1 	= [];
		var allblank2 	= [];
		var allblank3 	= [];
		var allblank4 	= [];
		var allblank5 	= [];
		var allblank6 	= [];
		var allblank7 	= [];
		var allblank8 	= [];
		var allblank9 	= [];
		var allblank10 	= [];
		var allblank11 	= [];
		var allblank12 	= [];
		var allblank13 	= [];
		var focustext   = "";
      
		$('#Material').find('.participantRow').each(function(){

			if($.trim($(this).find("[id*=GRNID_REF]").val()) ===""){
				allblank0.push('false');
				focustext = $(this).find("[id*=txtGRN_NO]").attr('id');
			}
      else if($.trim($(this).find("[id*=ITEMID_REF]").val()) ===""){
				allblank1.push('false');
				focustext = $(this).find("[id*=popupITEMID]").attr('id');
			}
			else if($.trim($(this).find("[id*=SO_QTY]").val()) ===""){
				allblank2.push('false');
				focustext = $(this).find("[id*=SO_QTY]").attr('id');
			}
      else if($.trim($(this).find("[id*=BILL_QTY]").val()) ===""){
				allblank3.push('false');
				focustext = $(this).find("[id*=BILL_QTY]").attr('id');
			}
      else if(parseFloat($.trim($(this).find("[id*=BILL_QTY]").val())) > parseFloat($.trim($(this).find("[id*=SO_QTY]").val())) ){
				allblank4.push('false');
				focustext = $(this).find("[id*=BILL_QTY]").attr('id');
			}
			else if($.trim($(this).find("[id*=RATEPUOM]").val()) ===""){
				allblank5.push('false');
				focustext = $(this).find("[id*=RATEPUOM]").attr('id');
			}
			else{
        allblank0.push('true');
				allblank1.push('true');
				allblank2.push('true');
				allblank3.push('true');
        allblank4.push('true');
        allblank5.push('true');
				focustext   = "";
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
        if($('#CTID_REF').val() !="")
        {
            $('#CT').find('.participantRow5').each(function(){
              if($.trim($(this).find("[id*=TID_REF]").val())!="")
                {
                  allblank8.push('true');
                        if($(this).find("[id*=calGST]").is(":checked") == true)
                        {
                          if($.trim($('#Tax_State').val())!="WithinState")
                          {
                            if($.trim($(this).find("[id*=calIGST]").val())!="0")
                            {
                              allblank9.push('true');
                            }
                            else
                            {
                              allblank9.push('false');
                            }
                          }
                          else
                          {
                            if($.trim($(this).find("[id*=calCGST]").val())!="0")
                            {
                              allblank9.push('true');
                            }
                            else
                            {
                              allblank9.push('false');
                            }
                            if($.trim($(this).find("[id*=calSGST]").val())!="0")
                            {
                              allblank9.push('true');
                            }
                            else
                            {
                              allblank9.push('false');
                            }
                          }
                        } 
                }
                else
                {
                    allblank8.push('false');
                } 
            });
        }
        $('#PaymentSlabs').find('.participantRow6').each(function(){
              if($.trim($(this).find("[id*=PAY_DAYS_]").val())!="")
                {
                  if($.trim($(this).find('[id*="DUE_"]').val()) != "")
                  {
                    allblank10.push('true');
                  }
                  else
                  {
                    allblank10.push('false');
                  }       
                }                
        });
        $('#udf').find('.participantRow4').each(function(){
              if($.trim($(this).find("[id*=UDFPBID_REF_]").val())!="")
                {
                    allblank8.push('true');
                        if($.trim($(this).find("[id*=UDFismandatory]").val())=="1"){
                              if($.trim($(this).find('[id*="udfvalue"]').val()) != "")
                              {
                                allblank11.push('true');
                              }
                              else
                              {
                                allblank11.push('false');
                              }
                        }  
                }                
        });

    }
        
        if(jQuery.inArray("false", allblank0) !== -1){
          $("#alert").modal('show');
          $("#FocusId").val(focustext);
          $("#AlertMessage").text('Please select GRN No in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        else if(jQuery.inArray("false", allblank1) !== -1){
          $("#alert").modal('show');
          $("#FocusId").val(focustext);
          $("#AlertMessage").text('Please select Item Code in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        else if(jQuery.inArray("false", allblank2) !== -1){
          $("#alert").modal('show');
          $("#FocusId").val(focustext);
          $("#AlertMessage").text('Please enter Received Qty MU in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        else if(jQuery.inArray("false", allblank3) !== -1){
          $("#alert").modal('show');
          $("#FocusId").val(focustext);
          $("#AlertMessage").text('Please enter Bill Qty (MU) in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
        }
        else if(jQuery.inArray("false", allblank4) !== -1){
          $("#alert").modal('show');
          $("#FocusId").val(focustext);
          $("#AlertMessage").text('Bill Qty (MU) should not greater then Received Qty MU in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
        }
        else if(jQuery.inArray("false", allblank5) !== -1){
          $("#FocusId").val(focustext);
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please enter Rate as per MU in Material Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          }  
          else if(jQuery.inArray("false", allblank6) !== -1){
          $("#FocusId").val(focustext);
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please select Terms & Condition Description in T&C Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          } 
        
          
        else if(jQuery.inArray("false", allblank7) !== -1){
          $("#FocusId").val($("#tot_amt"));
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please enter Value / Comment in T&C Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          } 
        else if(jQuery.inArray("false", allblank9) !== -1){
          $("#FocusId").val($("#tot_amt"));
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please enter GST Rate in Calculation Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          }
        else if(jQuery.inArray("false", allblank10) !== -1){
          $("#FocusId").val($("#tot_amt"));
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please enter Due details in Payment Slab Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          } 
         else if(jQuery.inArray("false", allblank11) !== -1){
          $("#FocusId").val($("#tot_amt"));
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please enter Value / Comment in UDF Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          }
        else{
                $("#alert").modal('show');
                $("#AlertMessage").text('Do you want to Approve to record.');
                $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name
                $("#YesBtn").focus();
                $("#OkBtn").hide();
                highlighFocusBtn('activeYes');
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

var trnpbForm = $("#frm_trn_edit");
var formData = trnpbForm.serialize();
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
            // $("#frm_mst_country").trigger("reset");

            $("#alert").modal('show');
            $("#OkBtn").focus();
            // window.location.href="{{ route('transaction',[90,'index']) }}";
        }
        else if(data.cancel) {                   
            console.log("cancel MSG="+data.msg);
            
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();

            $("#AlertMessage").text(data.msg);

            $(".text-danger").hide();
            // $("#frm_mst_country").trigger("reset");

            $("#alert").modal('show');
            $("#OkBtn1").focus();
            // window.location.href="{{ route('transaction',[90,'index']) }}";
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

var trnpbForm = $("#frm_trn_edit");
var formData = trnpbForm.serialize();
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
            // $("#frm_mst_country").trigger("reset");

            $("#alert").modal('show');
            $("#OkBtn").focus();
            // window.location.href="{{ route('transaction',[90,'index']) }}";
        }
        else if(data.cancel) {                   
            console.log("cancel MSG="+data.msg);
            
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();

            $("#AlertMessage").text(data.msg);

            $(".text-danger").hide();
            // $("#frm_mst_country").trigger("reset");

            $("#alert").modal('show');
            $("#OkBtn1").focus();
            // window.location.href="{{ route('transaction',[90,'index']) }}";
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

function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}

$("#NoBtn").click(function(){
    $("#alert").modal('hide');
    $("#LABEL").focus();
});

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
    $(".text-danger").hide();
});

function showError(pId,pVal){
    $("#"+pId+"").text(pVal);
    $("#"+pId+"").show();
}

function getFocus(){
	
	if($("#FocusId").val() !=''){
		var FocusId=$("#FocusId").val();
		$("#"+FocusId).focus();
	}
	
  $("#closePopup").click();
}

function highlighFocusBtn(pclass){
    $(".activeYes").hide();
    $(".activeNo").hide();
    $("."+pclass+"").show();
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

      $('#txtCRID_popup').focus(function(event){
        showSelectedCheck($("#CRID_REF").val(),"SELECT_CRID_REF");
         $("#cridpopup").show();
      });

      $("#crid_closePopup").click(function(event){
        $("#cridpopup").hide();
      });

      $(".clscrid").click(function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        
        $('#txtCRID_popup').val(texdesc);
        $('#CRID_REF').val(txtval);
        $("#cridpopup").hide();

        getConversionFactor('CONVFACT',txtval);

        $("#currencycodesearch").val(''); 
        $("#currencynamesearch").val(''); 
        CurrencyCodeFunction();
        event.preventDefault();
      });

      function getConversionFactor(CONVFACT,CRID_REF){

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url:'{{route("transaction",[$FormId,"getConversionFactor"])}}',
            type:'POST',
            data:{'CRID_REF':CRID_REF},
            success:function(data) {

              if(data !=""){
                $('#'+CONVFACT).val(data);
                $('#'+CONVFACT).prop('readonly','true');
              }
              else{
                $('#'+CONVFACT).val('');
                $('#'+CONVFACT).removeAttr('readonly');

                $("#FocusId").val(CONVFACT);
                $("#ProceedBtn").focus();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Conversion factor not found.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
              }

            },
            error:function(data){
              console.log("Error: Something went wrong.");
              $('#'+CONVFACT).val('');
              $('#'+CONVFACT).removeAttr('readonly');
            },
        }); 

      }

  //Currency Dropdown Ends
//------------------------  



function getDueDate(){
  
  $('#DUE_DATE').val('');
  var date  = $("#PII_DT").val();
  var days  = $("#Credit_days").val();
  

  if(days !=""){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url:'{{route("transaction",[$FormId,"getDueDate"])}}',
        type:'POST',
        data:{'date':date,'days':days},
        success:function(data) {
          $('#DUE_DATE').val(data);
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $('#DUE_DATE').val('');
        },
    }); 

  }    

}

//==================================POPUP =======================================

$('#Material').on('focus','[id*="popupITEMID"]',function(event){

var txtGRN_NO         = $(this).parent().parent().find('[id*="txtGRN_NO"]').val();
var GRNID_REF         = $(this).parent().parent().find('[id*="GRNID_REF"]').val();
var taxstate          = $.trim($('#Tax_State').val());
var IMPORT_DUTYID_REF = $.trim($('#IMPORT_DUTYID_REF').val());
var VID_REF           = $.trim($('#VID_REF').val());


if(VID_REF ===""){
  $("#FocusId").val('txtvendor');
  $("#VID_REF").val('');  
  $("#ProceedBtn").focus();
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please Select Vendor.');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
}
else if(IMPORT_DUTYID_REF ===""){
  $("#FocusId").val('IMPORT_DUTYID_REF'); 
  $("#ProceedBtn").focus();
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please select Imort Duty');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
} 
else if(txtGRN_NO ===""){
  $("#FocusId").val('txtGRN_NO'); 
  $("#ProceedBtn").focus();
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please select GRN No');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
} 
else{
  $('#item_loader').show();
  $("#tbody_ItemID").html('');
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getItemDetails"])}}',
    type:'POST',
    data:{'taxstate':taxstate,'IMPORT_DUTYID_REF':IMPORT_DUTYID_REF,'VID_REF':VID_REF,'GRNID_REF':GRNID_REF},
    success:function(data) {
      $('#item_loader').hide();
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
  
  $('#hdn_ItemID').val($(this).parent().parent().find('[id*="IPO_ID_REF"]').attr('id'));
  $('#hdn_ItemID2').val($(this).attr('id'));
  $('#hdn_ItemID3').val($(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id'));
  $('#hdn_ItemID4').val($(this).parent().parent().find('[id*="ItemName"]').attr('id'));
  $('#hdn_ItemID5').val($(this).parent().parent().find('[id*="Itemspec"]').attr('id'));
  $('#hdn_ItemID6').val($(this).parent().parent().find('[id*="popupMUOM"]').attr('id'));
  $('#hdn_ItemID7').val($(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id'));
  $('#hdn_ItemID8').val($(this).parent().parent().find('[id*="SO_QTY"]').attr('id'));
  $('#hdn_ItemID9').val($(this).parent().parent().find('[id*="popupAUOM"]').attr('id'));
  $('#hdn_ItemID10').val($(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id'));
  $('#hdn_ItemID11').val($(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id'));
  $('#hdn_ItemID12').val($(this).parent().parent().find('[id*="BILL_QTY"]').attr('id'));
  $('#hdn_ItemID13').val($(this).parent().parent().find('[id*="UOM_OF_BILL_QTY"]').attr('id'));
  $('#hdn_ItemID14').val($(this).parent().parent().find('[id*="RATEPUOM"]').attr('id'));
  $('#hdn_ItemID15').val($(this).parent().parent().find('[id*="DISC_PER"]').attr('id'));
  $('#hdn_ItemID16').val($(this).parent().parent().find('[id*="DISC_AMT"]').attr('id'));
  $('#hdn_ItemID17').val($(this).parent().parent().find('[id*="DISAFTT_AMT"]').attr('id'));

  $('#hdn_ItemID22').val($(this).parent().parent().find('[id*="FREIGHT_AMT"]').attr('id'));
  $('#hdn_ItemID23').val($(this).parent().parent().find('[id*="INSURANCE_AMT"]').attr('id'));
  $('#hdn_ItemID24').val($(this).parent().parent().find('[id*="ASSESSABLE_VALUE"]').attr('id'));
  $('#hdn_ItemID25').val($(this).parent().parent().find('[id*="CUSTOME_DUTY_RATE_PER"]').attr('id'));
  $('#hdn_ItemID26').val($(this).parent().parent().find('[id*="CUSTOME_DUTY_RATE_AMT"]').attr('id'));
  $('#hdn_ItemID27').val($(this).parent().parent().find('[id*="SWS_RATE_PER"]').attr('id'));
  $('#hdn_ItemID28').val($(this).parent().parent().find('[id*="SWS_RATE_AMT"]').attr('id'));
  $('#hdn_ItemID29').val($(this).parent().parent().find('[id*="TOTAL_CUSTOME_DUTY"]').attr('id'));
  $('#hdn_ItemID30').val($(this).parent().parent().find('[id*="TAXABLE_VALUE"]').attr('id'));
  $('#hdn_ItemID31').val($(this).parent().parent().find('[id*="IGST_RATE_PER"]').attr('id'));
  $('#hdn_ItemID32').val($(this).parent().parent().find('[id*="IGST_RATE_AMT"]').attr('id'));
  $('#hdn_ItemID33').val($(this).parent().parent().find('[id*="VALUE_AFTER_GST"]').attr('id'));

}

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

        var fieldid               =   $(this).attr('id');
        var item_id               =   $("#txt"+fieldid+"").data("desc1");
        var item_code             =   $("#txt"+fieldid+"").data("desc2");
        var item_name             =   $("#txt"+fieldid+"").data("desc3");
        var item_speci            =   $("#txt"+fieldid+"").data("desc4");
        var item_main_uom_id      =   $("#txt"+fieldid+"").data("desc5");
        var item_main_uom_code    =   $("#txt"+fieldid+"").data("desc6");
        var item_recive_qty_mum   =   $("#txt"+fieldid+"").data("desc7");
        var item_alt_uom_id       =   $("#txt"+fieldid+"").data("desc8");
        var item_alt_uom_code     =   $("#txt"+fieldid+"").data("desc9");
        var item_recive_qty_alt   =   $("#txt"+fieldid+"").data("desc10");
        var item_rate_asp_mu      =   $("#txt"+fieldid+"").data("desc11");
        var item_disc_per         =   $("#txt"+fieldid+"").data("desc12");
        var item_disc_amt         =   $("#txt"+fieldid+"").data("desc13");
        var item_freight_amount   =   $("#txt"+fieldid+"").data("desc14");
        var item_insurance_amount =   $("#txt"+fieldid+"").data("desc15");
        var item_assessable_value =   $("#txt"+fieldid+"").data("desc16");
        var customer_duty_rate    =   $("#txt"+fieldid+"").data("desc17");
        var sws_rate              =   $("#txt"+fieldid+"").data("desc18");
        var igst_rate             =   $("#txt"+fieldid+"").data("desc19");
        var item_IPO_ID_REF       =   $("#txt"+fieldid+"").data("desc20");
        var item_unique_row_id    =   $("#txt"+fieldid+"").data("desc21");

        var ipo_amount          = parseFloat(parseFloat(item_rate_asp_mu)*parseFloat(item_recive_qty_mum)).toFixed(2);
        var freight_amount      = item_freight_amount;
        var insurance_amount    = item_disc_amt;
        var assessable_value    = parseFloat(parseFloat(ipo_amount)+parseFloat(freight_amount)+parseFloat(insurance_amount)).toFixed(2);
        var custom_duty_amount  = parseFloat((parseFloat(assessable_value)*parseFloat(customer_duty_rate))/100).toFixed(2);
        var sws_amount          = parseFloat((parseFloat(custom_duty_amount)*parseFloat(sws_rate))/100).toFixed(2);
        var total_custom_duty   = parseFloat(custom_duty_amount)+parseFloat(sws_amount);
        var taxable_value       = parseFloat(parseFloat(assessable_value)+parseFloat(custom_duty_amount)+parseFloat(sws_amount)).toFixed(2);
        var igst_amount         = parseFloat((parseFloat(taxable_value)*parseFloat(igst_rate))/100).toFixed(2);
        var value_after_gst     = parseFloat(taxable_value)+parseFloat(igst_amount);


        var existArr = [];
        $('#Material').find('.participantRow').each(function(){

          if($(this).find('[id*="ITEMID_REF"]').val() != ''){
            var GRNID_REF   =   $(this).find('[id*="GRNID_REF_"]').val();
            var IPO_ID_REF  =   $(this).find('[id*="IPO_ID_REF_"]').val();
            var ITEMID_REF  =   $(this).find('[id*="ITEMID_REF_"]').val();
            var exist_val   =   GRNID_REF +'-'+ IPO_ID_REF +'-'+  ITEMID_REF;
            existArr.push(exist_val);
          }
          
        });


        if($(this).find('[id*="chkId"]').is(":checked") == true){

          if(jQuery.inArray(item_unique_row_id, existArr) !== -1){

            $("#ITEMIDpopup").hide();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Item already exists.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');

            $(".blurRate").blur();
            $('.js-selectall').prop("checked", false);
            return false;
            event.preventDefault();

          }
                
          if( $('#hdn_ItemID').val() == '' && item_id != ''){
            
            var $tr = $('.participantRow').closest('table');
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
            $clone.find('[id*="IPO_ID_REF"]').val(item_IPO_ID_REF);
            $clone.find('[id*="popupITEMID_0"]').val(item_code);
            $clone.find('[id*="ITEMID_REF"]').val(item_id);
            $clone.find('[id*="ItemName"]').val(item_name);
            $clone.find('[id*="Itemspec"]').val(item_speci);
            $clone.find('[id*="popupMUOM"]').val(item_main_uom_code);
            $clone.find('[id*="MAIN_UOMID_REF"]').val(item_main_uom_id);
            $clone.find('[id*="SO_QTY"]').val(item_recive_qty_mum);
            $clone.find('[id*="popupAUOM"]').val(item_alt_uom_code);
            $clone.find('[id*="ALT_UOMID_REF"]').val(item_alt_uom_id);
            $clone.find('[id*="ALT_UOMID_QTY"]').val(item_recive_qty_alt);
            $clone.find('[id*="BILL_QTY"]').val(item_recive_qty_mum);
            $clone.find('[id*="UOM_OF_BILL_QTY"]').val(item_main_uom_code);
            $clone.find('[id*="RATEPUOM"]').val(item_rate_asp_mu);
            $clone.find('[id*="DISC_PER"]').val(item_disc_per);
            $clone.find('[id*="DISC_AMT"]').val(item_disc_amt);
            $clone.find('[id*="DISAFTT_AMT"]').val(ipo_amount);
              
            $clone.find('[id*="FREIGHT_AMT"]').val(freight_amount);
            $clone.find('[id*="INSURANCE_AMT"]').val(insurance_amount);
            $clone.find('[id*="ASSESSABLE_VALUE"]').val(assessable_value);
            $clone.find('[id*="CUSTOME_DUTY_RATE_PER"]').val(customer_duty_rate);
            $clone.find('[id*="CUSTOME_DUTY_RATE_AMT"]').val(custom_duty_amount);
            $clone.find('[id*="SWS_RATE_PER"]').val(sws_rate);
            $clone.find('[id*="SWS_RATE_AMT"]').val(sws_amount);
            $clone.find('[id*="TOTAL_CUSTOME_DUTY"]').val(total_custom_duty);
            $clone.find('[id*="TAXABLE_VALUE"]').val(taxable_value);
            $clone.find('[id*="IGST_RATE_PER"]').val(igst_rate);
            $clone.find('[id*="IGST_RATE_AMT"]').val(igst_amount);
            $clone.find('[id*="VALUE_AFTER_GST"]').val(value_after_gst);

            $tr.closest('table').append($clone);   
            var rowCount = $('#Row_Count1').val();
            rowCount = parseInt(rowCount)+1;
            $('#Row_Count1').val(rowCount);

            
            var tvalue = parseFloat(value_after_gst).toFixed(2);
            totalvalue = $('#tot_amt').val();
            totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
            totalvalue = parseFloat(totalvalue).toFixed(2);
            $('#tot_amt').val(totalvalue);

            $(".blurRate").blur();
            event.preventDefault();
          }
          else{

              $('#'+$('#hdn_ItemID').val()).val(item_IPO_ID_REF);
              $('#'+$('#hdn_ItemID2').val()).val(item_code);
              $('#'+$('#hdn_ItemID3').val()).val(item_id);
              $('#'+$('#hdn_ItemID4').val()).val(item_name);
              $('#'+$('#hdn_ItemID5').val()).val(item_speci);
              $('#'+$('#hdn_ItemID6').val()).val(item_main_uom_code);
              $('#'+$('#hdn_ItemID7').val()).val(item_main_uom_id);
              $('#'+$('#hdn_ItemID8').val()).val(item_recive_qty_mum);
              $('#'+$('#hdn_ItemID9').val()).val(item_alt_uom_code);
              $('#'+$('#hdn_ItemID10').val()).val(item_alt_uom_id);
              $('#'+$('#hdn_ItemID11').val()).val(item_recive_qty_alt);
              $('#'+$('#hdn_ItemID12').val()).val(item_recive_qty_mum);
              $('#'+$('#hdn_ItemID13').val()).val(item_main_uom_code);
              $('#'+$('#hdn_ItemID14').val()).val(item_rate_asp_mu);
              $('#'+$('#hdn_ItemID15').val()).val(item_disc_per);
              $('#'+$('#hdn_ItemID16').val()).val(item_disc_amt);
              $('#'+$('#hdn_ItemID17').val()).val(ipo_amount);
              
              $('#'+$('#hdn_ItemID22').val()).val(freight_amount);
              $('#'+$('#hdn_ItemID23').val()).val(insurance_amount);
              $('#'+$('#hdn_ItemID24').val()).val(assessable_value);
              $('#'+$('#hdn_ItemID25').val()).val(customer_duty_rate);
              $('#'+$('#hdn_ItemID26').val()).val(custom_duty_amount);
              $('#'+$('#hdn_ItemID27').val()).val(sws_rate);
              $('#'+$('#hdn_ItemID28').val()).val(sws_amount);
              $('#'+$('#hdn_ItemID29').val()).val(total_custom_duty);
              $('#'+$('#hdn_ItemID30').val()).val(taxable_value);
              $('#'+$('#hdn_ItemID31').val()).val(igst_rate);
              $('#'+$('#hdn_ItemID32').val()).val(igst_amount);
              $('#'+$('#hdn_ItemID33').val()).val(value_after_gst);

              var tvalue = parseFloat(value_after_gst).toFixed(2);
              totalvalue = $('#tot_amt').val();
              totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
              totalvalue = parseFloat(totalvalue).toFixed(2);
              $('#tot_amt').val(totalvalue);

              $(".blurRate").blur();
              $('#hdn_ItemID').val('');
              event.preventDefault();

          }

          $("#ITEMIDpopup").hide();
        }
        

      });

      $('.js-selectall').prop("checked", false);
      $("#ITEMIDpopup").hide();
      event.preventDefault();

});
$('[id*="chkId"]').change(function(){
  if($(this).is(":checked") == true){

    var fieldid               =   $(this).parent().parent().attr('id');
    var item_id               =   $("#txt"+fieldid+"").data("desc1");
    var item_code             =   $("#txt"+fieldid+"").data("desc2");
    var item_name             =   $("#txt"+fieldid+"").data("desc3");
    var item_speci            =   $("#txt"+fieldid+"").data("desc4");
    var item_main_uom_id      =   $("#txt"+fieldid+"").data("desc5");
    var item_main_uom_code    =   $("#txt"+fieldid+"").data("desc6");
    var item_recive_qty_mum   =   $("#txt"+fieldid+"").data("desc7");
    var item_alt_uom_id       =   $("#txt"+fieldid+"").data("desc8");
    var item_alt_uom_code     =   $("#txt"+fieldid+"").data("desc9");
    var item_recive_qty_alt   =   $("#txt"+fieldid+"").data("desc10");
    var item_rate_asp_mu      =   $("#txt"+fieldid+"").data("desc11");
    var item_disc_per         =   $("#txt"+fieldid+"").data("desc12");
    var item_disc_amt         =   $("#txt"+fieldid+"").data("desc13");
    var item_freight_amount   =   $("#txt"+fieldid+"").data("desc14");
    var item_insurance_amount =   $("#txt"+fieldid+"").data("desc15");
    var item_assessable_value =   $("#txt"+fieldid+"").data("desc16");
    var customer_duty_rate    =   $("#txt"+fieldid+"").data("desc17");
    var sws_rate              =   $("#txt"+fieldid+"").data("desc18");
    var igst_rate             =   $("#txt"+fieldid+"").data("desc19");
    var item_IPO_ID_REF       =   $("#txt"+fieldid+"").data("desc20");
    var item_unique_row_id    =   $("#txt"+fieldid+"").data("desc21");

    var ipo_amount          = parseFloat(parseFloat(item_rate_asp_mu)*parseFloat(item_recive_qty_mum)).toFixed(2);
    var freight_amount      = item_freight_amount;
    var insurance_amount    = item_disc_amt;
    var assessable_value    = parseFloat(parseFloat(ipo_amount)+parseFloat(freight_amount)+parseFloat(insurance_amount)).toFixed(2);
    var custom_duty_amount  = parseFloat((parseFloat(assessable_value)*parseFloat(customer_duty_rate))/100).toFixed(2);
    var sws_amount          = parseFloat((parseFloat(custom_duty_amount)*parseFloat(sws_rate))/100).toFixed(2);
    var total_custom_duty   = parseFloat(custom_duty_amount)+parseFloat(sws_amount);
    var taxable_value       = parseFloat(parseFloat(assessable_value)+parseFloat(custom_duty_amount)+parseFloat(sws_amount)).toFixed(2);
    var igst_amount         = parseFloat((parseFloat(taxable_value)*parseFloat(igst_rate))/100).toFixed(2);
    var value_after_gst     = parseFloat(taxable_value)+parseFloat(igst_amount);


    var existArr = [];
    $('#Material').find('.participantRow').each(function(){

      if($(this).find('[id*="ITEMID_REF"]').val() != ''){
        var GRNID_REF   =   $(this).find('[id*="GRNID_REF_"]').val();
        var IPO_ID_REF  =   $(this).find('[id*="IPO_ID_REF_"]').val();
        var ITEMID_REF  =   $(this).find('[id*="ITEMID_REF_"]').val();
        var exist_val   =   GRNID_REF +'-'+ IPO_ID_REF +'-'+  ITEMID_REF;
        existArr.push(exist_val);
      }
      
    });

    if(jQuery.inArray(item_unique_row_id, existArr) !== -1){

      $("#ITEMIDpopup").hide();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Item already exists.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');

      $(".blurRate").blur();
      event.preventDefault();

    }
    else{

      $('#'+$('#hdn_ItemID').val()).val(item_IPO_ID_REF);
      $('#'+$('#hdn_ItemID2').val()).val(item_code);
      $('#'+$('#hdn_ItemID3').val()).val(item_id);
      $('#'+$('#hdn_ItemID4').val()).val(item_name);
      $('#'+$('#hdn_ItemID5').val()).val(item_speci);
      $('#'+$('#hdn_ItemID6').val()).val(item_main_uom_code);
      $('#'+$('#hdn_ItemID7').val()).val(item_main_uom_id);
      $('#'+$('#hdn_ItemID8').val()).val(item_recive_qty_mum);
      $('#'+$('#hdn_ItemID9').val()).val(item_alt_uom_code);
      $('#'+$('#hdn_ItemID10').val()).val(item_alt_uom_id);
      $('#'+$('#hdn_ItemID11').val()).val(item_recive_qty_alt);
      $('#'+$('#hdn_ItemID12').val()).val(item_recive_qty_mum);
      $('#'+$('#hdn_ItemID13').val()).val(item_main_uom_code);
      $('#'+$('#hdn_ItemID14').val()).val(item_rate_asp_mu);
      $('#'+$('#hdn_ItemID15').val()).val(item_disc_per);
      $('#'+$('#hdn_ItemID16').val()).val(item_disc_amt);
      $('#'+$('#hdn_ItemID17').val()).val(ipo_amount);
      
      $('#'+$('#hdn_ItemID22').val()).val(freight_amount);
      $('#'+$('#hdn_ItemID23').val()).val(insurance_amount);
      $('#'+$('#hdn_ItemID24').val()).val(assessable_value);
      $('#'+$('#hdn_ItemID25').val()).val(customer_duty_rate);
      $('#'+$('#hdn_ItemID26').val()).val(custom_duty_amount);
      $('#'+$('#hdn_ItemID27').val()).val(sws_rate);
      $('#'+$('#hdn_ItemID28').val()).val(sws_amount);
      $('#'+$('#hdn_ItemID29').val()).val(total_custom_duty);
      $('#'+$('#hdn_ItemID30').val()).val(taxable_value);
      $('#'+$('#hdn_ItemID31').val()).val(igst_rate);
      $('#'+$('#hdn_ItemID32').val()).val(igst_amount);
      $('#'+$('#hdn_ItemID33').val()).val(value_after_gst);

      var tvalue = parseFloat(value_after_gst).toFixed(2);
      totalvalue = $('#tot_amt').val();
      totalvalue =  parseFloat(totalvalue) + parseFloat(tvalue);
      totalvalue = parseFloat(totalvalue).toFixed(2);
      $('#tot_amt').val(totalvalue);

      $(".blurRate").blur();
      $("#ITEMIDpopup").hide();
      event.preventDefault();

    }

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

      function altuomNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("altuomnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("altuomTable2");
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

      

  $('#Material').on('focus','[id*="popupAUOM"]',function(event){
        var ItemID = $(this).parent().parent().find('[id*="ITEMID_REF"]').val();
        var fieldid = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');
        
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
                      data:{'id':ItemID,'fieldid':fieldid},
                      success:function(data) {
                        
                        $("#tbody_altuom").html(data);   
                        bindAltUOM(); 
                        showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);                     
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
        var id3 = $(this).parent().parent().find('[id*="SO_QTY"]').attr('id');
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
        var mqty = $('#'+txtid).parent().parent().find('[id*="SO_QTY"]').val();

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
        
        altuomCodeFunction();
        event.preventDefault();
      });
    }

      
function onChangeAmount(textid,textval){
	
  if($.trim(textval) !="" && parseFloat($.trim(textval)) >= 0 ){
  
      var expstr				=	textid.split("_");
      var indexid 			= 	$(expstr).last()[0];
      
      var tot_amt_pre			    =	  $("#tot_amt").val();
      var value_after_gst_pre	=	  $("#VALUE_AFTER_GST_"+indexid).val();
      var tot_amt     		    = 	parseFloat(parseFloat(tot_amt_pre) - parseFloat(value_after_gst_pre));
      
      var customer_duty_rate  = 	$("#CUSTOME_DUTY_RATE_PER_"+indexid).val();
      var sws_rate            = 	$("#SWS_RATE_PER_"+indexid).val();
      var igst_rate           = 	$("#IGST_RATE_PER_"+indexid).val();

      var ipo_qty             =   $.trim($("#BILL_QTY_"+indexid).val()) !=""?$.trim($("#BILL_QTY_"+indexid).val()):"1";
      var rate_as_per_mu      =   parseFloat($("#RATEPUOM_"+indexid).val()) * parseFloat(ipo_qty);

      var disc_per            =   $.trim($("#DISC_PER_"+indexid).val()) !=""?parseFloat($.trim($("#DISC_PER_"+indexid).val())):parseFloat("0.00");
      var disc_percent        =   parseFloat((parseFloat(rate_as_per_mu)*parseFloat(disc_per))/100).toFixed(2);
      var disc_amount         =   $.trim($("#DISC_AMT_"+indexid).val()) !=""?parseFloat($.trim($("#DISC_AMT_"+indexid).val())):parseFloat("0.00");

      
      var ipo_amount          =   parseFloat((rate_as_per_mu - disc_amount - disc_percent)).toFixed(2);
      var freight_amount      = 	$("#FREIGHT_AMT_"+indexid).val();
      var insurance_amount    = 	$("#INSURANCE_AMT_"+indexid).val();
      var assessable_value    = 	parseFloat(parseFloat(ipo_amount)+parseFloat(freight_amount)+parseFloat(insurance_amount)).toFixed(2);
      var custom_duty_amount  = 	parseFloat((parseFloat(assessable_value)*parseFloat(customer_duty_rate))/100).toFixed(2);
      var sws_amount          = 	parseFloat((parseFloat(custom_duty_amount)*parseFloat(sws_rate))/100).toFixed(2);
      var total_custom_duty   = 	parseFloat(parseFloat(custom_duty_amount)+parseFloat(sws_amount)).toFixed(2);
      var taxable_value       = 	parseFloat(parseFloat(assessable_value)+parseFloat(custom_duty_amount)+parseFloat(sws_amount)).toFixed(2);
      var igst_amount         = 	parseFloat((parseFloat(taxable_value)*parseFloat(igst_rate))/100).toFixed(2);
      var value_after_gst     = 	parseFloat(parseFloat(taxable_value) + parseFloat(igst_amount)).toFixed(2);
      var tot_amt_new     	  =	  parseFloat(parseFloat(tot_amt) + parseFloat(value_after_gst)).toFixed(2);


      $("#DISAFTT_AMT_"+indexid).val(ipo_amount);
      $("#FREIGHT_AMT_"+indexid).val(freight_amount);
      $("#INSURANCE_AMT_"+indexid).val(insurance_amount);
      $("#ASSESSABLE_VALUE_"+indexid).val(assessable_value);
      $("#CUSTOME_DUTY_RATE_PER_"+indexid).val(customer_duty_rate);
      $("#CUSTOME_DUTY_RATE_AMT_"+indexid).val(custom_duty_amount);
      $("#SWS_RATE_PER_"+indexid).val(sws_rate);
      $("#SWS_RATE_AMT_"+indexid).val(sws_amount);
      $("#TOTAL_CUSTOME_DUTY_"+indexid).val(total_custom_duty);
      $("#TAXABLE_VALUE_"+indexid).val(taxable_value);
      $("#IGST_RATE_PER_"+indexid).val(igst_rate);
      $("#IGST_RATE_AMT_"+indexid).val(igst_amount);
      $("#VALUE_AFTER_GST_"+indexid).val(value_after_gst);
      $("#tot_amt").val(tot_amt_new);

  }

}

$('#Material').on('click','[id*="DISC_AMT"]',function(event){
    $(this).parent().parent().find('[id*="DISC_AMT"]').prop('readonly',false);
    $(this).parent().parent().find('[id*="DISC_PER"]').prop('readonly',true);
    $(this).parent().parent().find('[id*="DISC_PER"]').val('');
});

$('#Material').on('click','[id*="DISC_PER"]',function(event){
    $(this).parent().parent().find('[id*="DISC_PER"]').prop('readonly',false);
    $(this).parent().parent().find('[id*="DISC_AMT"]').prop('readonly',true);
    $(this).parent().parent().find('[id*="DISC_AMT"]').val('');
});

function editAltUomQty(txt_id4,altuomid,itemid,mqty){

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

}

function getBillAddress(customid){

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

}

function getShipAddress(customid){


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