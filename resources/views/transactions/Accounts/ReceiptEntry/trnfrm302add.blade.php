
@extends('layouts.app')
@section('content')
@inject('helper', 'App\Helpers\Helper') 
    

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Receipt Entry</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSave" ><i class="fa fa-save"></i> Save</button>
                        <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i>  Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div><!--row-->
    </div>

    <form id="frm_trn_rcpt" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >
    <div class="container-fluid filter">

	<div class="inner-form">
	
		<div class="row">
        <div class="col-lg-1 pl"><p>Receipt No*</p></div>
        <div class="col-lg-2 pl">
            
              @if(isset($objRCPTDOCNO->SYSTEM_GRSR) && $objRCPTDOCNO->SYSTEM_GRSR == "1")
                  <input type="text" name="RECEIPT_NO" id="RECEIPT_NO" value="{{ isset($objDataNo)?$objDataNo:'' }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
              @elseif(isset($objRCPTDOCNO->MANUAL_SR) &&  $objRCPTDOCNO->MANUAL_SR == "1")
                  <input type="text" name="RECEIPT_NO" id="RECEIPT_NO" value="{{ old('RECEIPT_NO') }}" class="form-control mandatory" maxlength="{{isset($objPBDOCNO->MANUAL_MAXLENGTH)?$objPBDOCNO->MANUAL_MAXLENGTH:''}}" autocomplete="off" style="text-transform:uppercase"  >
            @else
                <input type="text" name="RECEIPT_NO" id="RECEIPT_NO" value="{{ old('RECEIPT_NO') }}" class="form-control mandatory" maxlength="30" autocomplete="off" style="text-transform:uppercase"  >
            @endif
        </div>
        <div class="col-lg-1 pl"><p>Receipt Date*</p></div>
        <div class="col-lg-2 pl">
                  <input type="date" name="RECEIPT_DT" id="RECEIPT_DT" value="{{ old('RECEIPT_DT') }}" onchange="checkPeriodClosing('{{$FormId}}',this.value,1)" class="form-control mandatory"  placeholder="dd/mm/yyyy" />
        </div>
        <div class="col-lg-1 pl"><p>Receipt For*</p></div>
        <div class="col-lg-2 pl">
              <input type="checkbox" name="chk_Customer" id="chk_Customer" checked />&nbsp;&nbsp;<label>   Customer </label>
        </div>
        <div class="col-lg-2 pl">
              <input type="checkbox" name="chk_Vendor" id="chk_Vendor"    /> &nbsp;&nbsp;<label>   Vendor </label>
        </div>        
        <div class="col-lg-1 pl">
              <input type="checkbox" name="chk_Account" id="chk_Account"    />&nbsp;&nbsp;<label>   Account </label> 
              <input type="hidden" name="hdnreceiptfor" id="hdnreceiptfor" value="Customer" />
              <input type="hidden" name="hdnInvoice" id="hdnInvoice" />
              <input type="hidden" name="hdnAccount" id="hdnAccount" />
              <input type="hidden" name="hdnCostCenter" id="hdnCostCenter" />
              <input type="hidden" name="hdnCostCenter2" id="hdnCostCenter2" />
              <input type="hidden" name="hdntds" id="hdntds" />
        </div>
    </div>
    <div class="row" id="divcust1" >
        <div class="col-lg-1 pl"><p> Customer / Vendor*</p></div> 
        <div class="col-lg-2 pl">
                <input type="text" name="txtcustomer" id="txtcustomer" class="form-control"  readonly  />
                <input type="hidden" name="CUSTMER_VENDOR_ID" id="CUSTMER_VENDOR_ID"  class="form-control " />
        </div>
        <div class="col-lg-1 pl"><p>Receipt On Account*</p></div>        
        <div class="col-lg-2 pl">
            <input type="checkbox" name="chk_RcptAccount" id="chk_RcptAccount" />
        </div>
        <div class="col-lg-1 pl" id="div_account_amt" style="display:none;"><p>Amount</p></div>        
        <div class="col-lg-2 pl" id="div_account_amt2" style="display:none;">
            <input type="text" name="AMOUNT" id="AMOUNT" class="form-control two-digits"  autocomplete="off" />
        </div>
    </div>


    <div class="row" >
        <div class="col-lg-1 pl"><p>Type</p></div>        
        <div class="col-lg-2 pl">
        <select name="PDC_ECH_TYPE" id="PDC_ECH_TYPE" class="form-control">                
                <option value="">Type</option>
                <option value="PDC">PDC</option>
                <option value="ECH">ECH</option>
            </select>  
        </div>
        <div class="col-lg-1 pl"><p>PDC/ECH (Y/N)</p></div>        
        <div class="col-lg-2 pl">
        <input type="checkbox" name="PDC_STATUS" id="PDC_STATUS" />
        </div>
        <div class="col-lg-1 pl"><p>PDC/ECH Date</p></div>
        <div class="col-lg-2 pl">
        <input type="date" name="PDC_DT" id="PDC_DT"   class="form-control mandatory"  placeholder="dd/mm/yyyy" />
        </div>
             <div class="col-lg-1 pl"><p>Clear PDC/ECH (Y/N)</p></div>        
        <div class="col-lg-2 pl">
        <input type="checkbox" name="CLEAR_PDC_STATUS" id="CLEAR_PDC_STATUS" />
        </div>
        </div>

        <div class="row" >

   
        <div class="col-lg-1 pl"><p>Clear PDC/ECH Date</p></div>
        <div class="col-lg-2 pl">
        <input type="date" name="CLEAR_PDC_DT" id="CLEAR_PDC_DT"   class="form-control mandatory"  placeholder="dd/mm/yyyy" />
        </div>
    </div>


    <div class="row">
		<div id="divcust2">
			<div class="col-lg-1 pl"><p>Cash / Bank Account*</p></div>
			<div class="col-lg-2 pl">
					<input type="text" name="txtcashbk" id="txtcashbk" class="form-control"  readonly  />
					<input type="hidden" name="CASH_BANK_ID" id="CASH_BANK_ID"  class="form-control " />
					<input type="hidden" name="RECEIPT_TYPE" id="RECEIPT_TYPE"  class="form-control " />         
					 <label id="BALANCE_SHOW"></label>    
			</div>
		</div>
        <div class="col-lg-1 pl"><p>Transaction Date*</p></div>
        <div class="col-lg-2 pl">
            <input type="date" name="TRANSACTION_DT" id="TRANSACTION_DT" class="form-control"  placeholder="dd/mm/yyyy"  />
        </div>
        <div class="col-lg-1 pl"><p>Instrument Type*</p></div>
        <div class="col-lg-2 pl">
            <select name="INSTRUMENT_TYPE" id="INSTRUMENT_TYPE" class="form-control">
                <option value="Cheque">Cheque</option>
                <option value="NEFT">NEFT</option>
                <option value="RTGS">RTGS</option>
            </select>                        
        </div>
        <div class="col-lg-1 pl"><p>Instrument No*</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="INSTRUMENT_NO" id="INSTRUMENT_NO" class="form-control"  autocomplete="off"  />                        
        </div>
    </div>

    <div class="row">
    <div class="col-lg-1 pl"><p>Foreign Currency</p></div>
    <div class="col-lg-1 pl">
        <input type="checkbox" name="FC" id="FC" class="form-checkbox" >
    </div>                            
    <div class="col-lg-1 pl col-md-offset-1"><p>Currency</p></div>
    <div class="col-lg-2 pl" id="divcurrency" >
        <input type="text" name="CRID_popup" id="txtCRID_popup" class="form-control"  autocomplete="off"  disabled/>
        <input type="hidden" name="CRID_REF" id="CRID_REF" class="form-control" autocomplete="off" />                                
    </div>                            
    <div class="col-lg-1 pl"><p>Conversion Factor</p></div>
    <div class="col-lg-2 pl">
        <input type="text" name="CONVFACT" id="CONVFACT" autocomplete="off" onkeyup="MultiCurrency_Conversion('tot_amt1')" class="form-control" readonly  maxlength="100" />
    </div>
</div>   

    <div class="row"  id="divcust3" >	
        <div class="col-lg-1 pl"><p>Bank Charge</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="BANK_CHARGE" id="BANK_CHARGE" class="form-control two-digits"  autocomplete="off"  />
        </div>                          
        <div class="col-lg-1 pl"><p>Amount*</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="tot_amt1" id="tot_amt1" class="form-control"  autocomplete="off" readonly  />
        </div>
        <div class="col-lg-1 pl"><p>Common Narration</p></div>
        <div class="col-lg-5 pl">
            <input type="text" name="NARRATION" id="NARRATION" class="form-control"  autocomplete="off"  />                        
        </div>
    </div>


    <div class="row"  id=""  >	
        <div class="col-lg-1 pl"><p>Round of GL</p></div>
        <div class="col-lg-2 pl">
        <input type="text" name="GLpopup" id="txtGLpopup" class="form-control mandatory"  autocomplete="off"  readonly/>
        <input type="hidden" name="GLID_REF_ROUNDOFF" id="GLID_REF_ROUNDOFF" class="form-control" autocomplete="off" />
        <label id="BALANCE_ROUNDGL_SHOW"></label>
        </div>                          
        <div class="col-lg-1 pl"><p>Round of GL Amount</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="ROUNDOFF_AMT" id="ROUNDOFF_AMT" class="form-control"  autocomplete="off"  onkeyup="calculateBankAmount()"   />
        </div>
        <div class="col-lg-1 pl"><p>Round of Mode</p></div>
        <div class="col-lg-2 pl">
        <select name="ROUNDOFF_MODE" id="ROUNDOFF_MODE" class="form-control" onchange="calculateBankAmount()" >
                <option value="Positive">Debit</option>
                <option value="Negative">Credit</option>
              </select>                                  
        </div>
        <div id="multi_currency_section" style="display:none">
        <div class="col-lg-1 pl"  ><p id="currency_section"></p></div>
        <div class="col-lg-2 pl">
        <input type="text"  name="TotalValue_Conversion" id="TotalValue_Conversion" class="form-control"  autocomplete="off" readonly  />
    </div>
    </div>

    </div>




    <div class="row"  id="divcust4" >	
        <div class="col-lg-1 pl"><p>Centerlized Receipt</p></div>
        <div class="col-lg-2 pl">
            <input type="checkbox" name="CENTERLIZED_RECEIPT" id="CENTERLIZED_RECEIPT"    />
        </div> 
    </div>
	</div>

	<div class="container-fluid">
		<div class="row" id="tabs">
			<ul class="nav nav-tabs">
				<li class="active" id="div_invoice" ><a data-toggle="tab" href="#Invoice" id="Invoice_Tab" >Invoice Details</a></li> 
        <li id="div_account" style="display:none"><a data-toggle="tab" href="#Account">Account</a></li> 
        <li id="div_tds"><a data-toggle="tab" href="#TDS">TDS</a></li>    
        <li  style="display:none"><a data-toggle="tab" href="#CostCenter">Cost Center</a></li> 
			</ul>
      <div class="tab-content">
        <div id="Invoice" class="tab-pane fade in active">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                          <tr>
                              <th width="12%">Doc No<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
                              <th width="12%">Doc Type</th>
                              <th width="12%">Doc Date</th>
                              <th width="12%">Amount</th>
                              <th width="12%">Balance Due</th>
                              <th width="12%">Receipt Amount</th>
                              <th width="12%">Remarks</th>
                              <th width="12%">Branch</th>
                              <th width="5%">Action</th>
                          </tr>
                    </thead>
                    <tbody>
                          <tr  class="participantRow2">
                              <td><input type="text" name="txtDoc_NO_0" id="txtDoc_NO_0" class="form-control"  autocomplete="off"  readonly/></td>
                              <td hidden><input type="hidden" name="DOCNO_ID_0" id="DOCNO_ID_0" class="form-control" autocomplete="off" /> 
                              <input type="hidden" name="rowcount1[]" id="rowcount1[]" class="form-control" autocomplete="off" /></td>
                            
                              <td><input type="text" name="Doc_Type_0" id="Doc_Type_0" class="form-control"  autocomplete="off"  readonly/></td>
                              <td><input type="text" name="DocDate_0" id="DocDate_0" class="form-control" autocomplete="off" placeholder="dd/mm/yyyy" readonly /></td>
                              <td><input type="text" name="DocAmount_0" id="DocAmount_0" class="form-control two-digits"  autocomplete="off"  readonly/></td>
                              <td><input type="text" name="BALANCE_DUE_0" id="BALANCE_DUE_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                              <td><input type="text" name="RECEIPT_AMT_0" id="RECEIPT_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off" /></td>
                              <td><input type="text" name="REMARKS_0" id="REMARKS_0" class="form-control" maxlength="200" autocomplete="off"  /></td>
                              <td><input type="text" name="TxtBranch_0" id="TxtBranch_0" class="form-control" readonly  /></td>
                              <td hidden><input type="hidden" name="BRID_REF_0" id="BRID_REF_0" class="form-control" autocomplete="off" /></td>
                              <td align="center" ><button class="btn add ainvoice" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                              <button class="btn remove dinvoice" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                          </tr>
                          <tr></tr>
                    </tbody>
                </table>
            </div>	
        </div> 
        <div id="Account" class="tab-pane fade in">
            <div class="row" style="margin-top:10px;margin-left:3px;" >	
                <div class="col-lg-2 pl"><p>Bank/Cash</p></div>
                <div class="col-lg-2 pl">
                    <input type="text" name="txtbnkcsh" id="txtbnkcsh" class="form-control"  readonly  />
                    <input type="hidden" name="BANK_CASH_ID" id="BANK_CASH_ID"  class="form-control " />
                    <label id="BALANCE_ACCOUNT_SHOW"></label>
                </div>

                <div class="col-lg-1 pl"><p>Bank Amount</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="BANK_AMOUNT" id="BANK_AMOUNT" value='0.00' class="form-control" autocomplete="off"  readonly  />
                </div>

                <div class="col-lg-1 pl"><p>Common Narration</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="BANK_REMARKS" id="BANK_REMARKS" class="form-control" autocomplete="off" />
                </div>
				<div class="col-lg-1 pl"><p>Sub Ledger</p></div>
                  <div class="col-lg-1 pl">
                      <input type="checkbox" name="SubGL" id="SubGL" onclick="getSubLedger()"  />
                      <input type="hidden" name="hdnAccounting" id="hdnAccounting" class="form-control"  autocomplete="off"  />
                      <input type="hidden" name="hdnCostCenter2" id="hdnCostCenter2" class="form-control" autocomplete="off" />
                  </div>

            </div>
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:240px;" >
                <table id="example3" class="display nowrap table table-striped table-bordered itemlist " width="100%" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0">
                      <tr>
                          <th>Account Code<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3"></th>
                          <th>Account Name</th>
                          <th>Account Balance</th>
                          <th>Amount</th>
                          <th>IGST Rate</th>
                          <th>IGST Amount</th>
                          <th>CGST Rate</th>
                          <th>CGST Amount</th>
                          <th>SGST Rate</th>
                          <th>SGST Amount</th>
                          <th>Cost Center</th>
                          <th>Type</th>
                          <th>Total</th>
                          <th width="8%">Action</th>
                      </tr>
                  </thead>
                  <tbody id="tbody_account">
                      <tr  class="participantRow3">
                          <td><input type="text" name="popupAccount_0" id="popupAccount_0" class="form-control"  autocomplete="off"  readonly/></td>
                          <td hidden><input type="hidden" name="GLID_REF_0" id="GLID_REF_0" class="form-control" autocomplete="off" /></td>
                          <td><input type="text" name="AccountName_0" id="AccountName_0" class="form-control"  autocomplete="off"  readonly/></td>
                          <td><input type="text" name="ACCOUNT_BALANCE_0" id="ACCOUNT_BALANCE_0" class="form-control"  autocomplete="off"  readonly/></td>
                          <td><input type="text" name="AMOUNT_0" id="AMOUNT_0" class="form-control two-digits right" maxlength="15" autocomplete="off"  /></td>
                          <td><input type="text" name="calIGST_0" id="calIGST_0" class="form-control four-digits right" maxlength="8" autocomplete="off"  /></td>
                          <td><input type="text" name="AMTIGST_0" id="AMTIGST_0" class="form-control two-digits right" maxlength="15" autocomplete="off"  /></td>
                          <td><input type="text" name="calCGST_0" id="calCGST_0" class="form-control four-digits right" maxlength="8" autocomplete="off"  /></td>
                          <td><input type="text" name="AMTCGST_0" id="AMTCGST_0" class="form-control two-digits right" maxlength="15" autocomplete="off"  /></td>
                          <td><input type="text" name="calSGST_0" id="calSGST_0" class="form-control four-digits right" maxlength="8" autocomplete="off"  /></td>
                          <td><input type="text" name="AMTSGST_0" id="AMTSGST_0" class="form-control two-digits right" maxlength="15" autocomplete="off"  /></td>
                          <td align="center" ><button class="btn" id="BtnCCID_0" name="BtnCCID_0" type="button"><i class="fa fa-clone"></i></button></td>
                          <td hidden><input type="text" name="CCID_REF_0" id="CCID_REF_0" class="form-control"  autocomplete="off"  readonly/></td>
                            <td>                                 
                            <select name="TYPE_0" id="TYPE_0" class="form-control" style="width: 85px;">
                            <option value="Advance">Advance</option>
                            <option value="Loan">Loan</option>                            
                            <option value="Medical">Medical</option>
                            <option value="Conveyance">Conveyance</option>
                            <option value="Others">Others</option>
                            </select></td>
                          <td><input type="text" name="TOTAMT_0" id="TOTAMT_0" class="form-control two-digits right"  maxlength="15" autocomplete="off"  readonly/></td>
                          <td align="center" ><button class="btn add aaccount" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button><button class="btn remove daccount" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                      </tr>
                      <tr></tr>
                  </tbody>

                  <style>
                  .total-row{
                    text-align:right;
                    font-size:13px;
                    font-weight:bold;
                  }
                  .right{
                    text-align:right !important;
                  }
                  </style>
                  <tr>
                    <td class="total-row" colspan="2" style="text-align:center ! important;">TOTAL</td>
                    <td class="total-row" id="AMOUNT_TOTAL"></td>
                    <td class="total-row"></td>
                    <td class="total-row" id="AMTIGST_TOTAL"></td>
                    <td class="total-row"></td>
                    <td class="total-row" id="AMTCGST_TOTAL"></td>
                    <td class="total-row"></td>
                    <td class="total-row" id="AMTSGST_TOTAL"></td>
                    <td class="total-row"></td>
                    <td class="total-row"></td>
                    <td class="total-row"></td>
                    <td class="total-row" id="TOTAMT_TOTAL"></td>
                    <td class="total-row"></td>
                  </tr>

                </table>
            </div>	
        </div>  

        <div id="TDS" class="tab-pane fade">
          
          <div class="row" style="margin-top:10px;margin-left:3px;" >	
              <div class="col-lg-1 pl"><p>TDS Applicable</p></div>
              <div class="col-lg-2 pl">
                <select name="drpTDS" id="drpTDS" class="form-control">
                    <option value=""></option>    
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
              </div>
          </div>
          <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
              <table id="example7" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0">
                        <tr>
                            <th width="8%">TDS<input class="form-control" type="hidden" name="Row_Count6" id ="Row_Count6"></th>
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
                  </tbody>
          </table>
          </div>	
      </div>

        <div id="CostCenter" class="tab-pane fade" >
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
                <table id="example5" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                    <tr >
                        <th>GLID<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2"></th>
                        <th>CCID</th>
                        <th>CC_AMT</th>
                    </tr>
                    </thead>
                    <tbody>
                      <tr  class="participantRow5">
                          <td><input type="text" name="GLID_0" id="GLID_0" class="form-control"  autocomplete="off"  readonly/></td>
                          <td><input type="text" name="CCID_0" id="CCID_0" class="form-control" autocomplete="off"   readonly/></td>
                          <td><input type="text" name="GL_AMT_0" id="GL_AMT_0" class="form-control two-digits"   autocomplete="off" readonly/></td>                        
                      </tr>
                      <tr></tr>
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




<!-- Round of GL Popup starts here   -->
<div id="GL_popup" class="modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" id="GL_closePopup1">&times;</button>
            </div>
            <div class="modal-body">
                <div class="tablename"><p>General Ledger</p></div>
                <div class="single single-select table-responsive table-wrapper-scroll-y my-custom-scrollbar">
                <input type="hidden" class="mainitem_tab1">
                    <table id="GeneralLedger" class="display nowrap table table-striped table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                            <th style="width:10%;">Select</th> 
                                <th style="width:30%;"> Code</th>
                                <th style="width:60%;"> Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <th style="text-align:center; width:10%;">&#10004;</th>

                       

                                <td style="width:30%;"> 
                                    <input type="text" id="GLcodesearch" class="form-control" onkeyup="GLCodeFunction()"  />
                                </td>
                                <td style="width:60%;">
                                    <input type="text" id="glnamesearch" class="form-control" onkeyup="GLNameFunction()"  />
                                </td>
                          
                            </tr>
                        </tbody>
                    </table>
                    <table id="GeneralLedgerTable2" class="display nowrap table table-striped table-bordered">
                        <thead id="thead2">
                        <tr>
        
                <td id="item_seach" colspan="4">please wait...</td>
          </tr>
                        </thead>
                        <tbody id="GLresult">
                           
                        </tbody>
                    </table>
                </div>
                <div class="cl"></div>
            </div>
        </div>
    </div>
</div>




<!-- Invoice Dropdown -->
<div id="Invoicepopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:95%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='Invoice_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Invoice</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CodeTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
      <thead>
          <tr id="none-select" class="searchalldata" hidden>
                
                <td> 
                    <input type="hidden" name="hdn_Invoice" id="hdn_Invoice"/>
                    <input type="hidden" name="hdn_Invoice2" id="hdn_Invoice2"/>
                    <input type="hidden" name="hdn_Invoice3" id="hdn_Invoice3"/>
                    <input type="hidden" name="hdn_Invoice4" id="hdn_Invoice4"/>
                    <input type="hidden" name="hdn_Invoice5" id="hdn_Invoice5"/>
                    <input type="hidden" name="hdn_Invoice6" id="hdn_Invoice6"/>
                    <input type="hidden" name="hdn_Invoice61" id="hdn_Invoice61"/>
                    <input type="hidden" name="hdn_Invoice7" id="hdn_Invoice7"/>
                    <input type="hidden" name="hdn_Invoice8" id="hdn_Invoice8"/>
                    <input type="hidden" name="hdn_Invoice9" id="hdn_Invoice9"/>
                    <input type="hidden" name="hdn_Invoice10" id="hdn_Invoice10"/>
                    <input type="hidden" name="hdn_Invoice11" id="hdn_Invoice11"/>
                    <input type="hidden" name="hdn_Invoice12" id="hdn_Invoice12"/>
                    <input type="hidden" name="hdn_Invoice13" id="hdn_Invoice13"/>
                    <input type="hidden" name="hdn_Invoice14" id="hdn_Invoice14"/>
                    <input type="hidden" name="hdn_Invoice15" id="hdn_Invoice15"/>
                    <input type="hidden" name="hdn_Invoice16" id="hdn_Invoice16"/>
                    <input type="hidden" name="hdn_Invoice17" id="hdn_Invoice17"/>
                    <input type="hidden" name="hdn_Invoice18" id="hdn_Invoice18"/>
                    <input type="hidden" name="hdn_Invoice19" id="hdn_Invoice19"/>
                    <input type="hidden" name="hdn_Invoice20" id="hdn_Invoice20"/>
                    <input type="hidden" name="hdn_Invoice21" id="hdn_Invoice21" value="0"/>
                    <input type="hidden" name="hdn_Invoice22" id="hdn_Invoice22"/>
                    <input type="hidden" name="hdn_Invoice23" id="hdn_Invoice23"/>
                    <input type="hidden" name="hdn_Invoice24" id="hdn_Invoice24"/>
                    <input type="hidden" name="hdn_Invoice25" id="hdn_Invoice25"/>
                    <input type="hidden" name="hdn_Invoice26" id="hdn_Invoice26"/>
                    <input type="hidden" name="hdn_Invoice27" id="hdn_Invoice27"/>
                </td>
          </tr>
          <tr>  
                  <th id="all-check" style="width:10%; text-align: center;">Select</th>
                  <th style="width:15%;">Type</th>
                  <th style="width:15%;">Doc No.</th>
                  <th style="width:15%;">Doc DT</th>
                  <th style="width:15%;">Branch</th>
                  <th style="width:7%;">Total Amount</th>
                  <th style="width:7%;">Balance Amount</th>
                  <th style="width:8%;">Supplier Invoice No</th>
                  <th style="width:8%;">Due Days</th>
          </tr>
      </thead>
      <tbody>
        <tr>
        <td style="width:10%; text-align: center;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
          <td style="width:15%;">
            <input type="text" id="typesearch" class="form-control" autocomplete="off" onkeyup="TypeFunction()">
          </td>
          <td style="width:15%;">
            <input type="text" id="docsearch" class="form-control" autocomplete="off" onkeyup="DocFunction()">
          </td>
          <td style="width:15%;">
            <input type="text" id="datesearch" class="form-control" autocomplete="off" onkeyup="DateFunction()">
          </td>
          <td style="width:15%;">
            <input type="text" id="branchsearch" class="form-control" autocomplete="off" onkeyup="BranchFunction()">
          </td>
          <td style="width:7%;">
            <input type="text" id="totalsearch" class="form-control" autocomplete="off" onkeyup="TotalFunction()">
          </td>
          <td style="width:7%;">
            <input type="text" id="balsearch" class="form-control" autocomplete="off" onkeyup="BalFunction()">
          </td>
          <td style="width:8%;">
            <input type="text" id="supplier_invoiceno" class="form-control" autocomplete="off" onkeyup="SupplierInvoiceFunction()">
          </td>
          <td style="width:8%;">
            <input type="text" id="due_day" class="form-control" autocomplete="off" onkeyup="DueDaysFunction()">
          </td>
        </tr>
      </tbody>
    </table>
    <table id="CodeTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;">
      <thead id="thead2">
      </thead>
      <tbody id="tbody_invoice" style="font-size:12px;">      
      </tbody>
    </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Invoice Dropdown-->


<!--Account dropdown-->

<div id="account_popup" class="modal" role="dialog"  data-backdrop="static">
 <div class="modal-dialog modal-md" style="width:60%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='account_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Account</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="AccountCodeTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr id="none-select" class="searchalldata" hidden>                
          <td> 
              <input type="hidden" name="hdn_Account" id="hdn_Account"/>
              <input type="hidden" name="hdn_Account2" id="hdn_Account2"/>
              <input type="hidden" name="hdn_Account3" id="hdn_Account3"/>
          </td>
    </tr>
    <tr>
    <th style="width:10%;">Select</th> 
            <th style="width:30%;">Code</th>
            <th style="width:60%;">Name</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <th style="text-align:center; width:10%;">&#10004;</th>
    <td style="width:30%;">
    <input type="text" id="Accountcodesearch" class="form-control" onkeyup="AccountCodeFunction()">
    </td>
    <td style="width:60%;">
    <input type="text" id="Accountnamesearch" class="form-control" onkeyup="AccountNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="AccountCodeTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
        </thead>
        <tbody id="tbody_account">
        {{-- @foreach ($objgeneralledger as $index=>$glRow)
          <tr>
          <td style="text-align:center; width:10%"> <input type="checkbox"   id="glidcode_{{ $index }}" class="clsglid" value="{{ $glRow->GLID }}" name="glname[]" ></td>
              <td style="width:30%">{{ $glRow-> GLCODE }}
                <input type="hidden" id="txtglidcode_{{ $index }}" data-desc="{{ $glRow-> GLCODE }}" data-desc2="{{ $glRow-> GLNAME }}"  value="{{ $glRow-> GLID }}"/>
              </td>
              <td style="width:60%">{{ $glRow-> GLNAME }}</td>
          </tr>
        @endforeach --}}
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>







<div id="glsl_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" id='gl_closePopup' >&times;</button>
        </div>
      <div class="modal-body">
      <div class="tablename"><p>GL Account</p></div>
      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
      <table id="GlCodeTable" class="display nowrap table  table-striped table-bordered" >
      <thead>
      <tr id="none-select" class="searchalldata" hidden>
              <td> <input type="hidden" name="hdn_GLID" id="hdn_GLID"/>
              <input type="hidden" name="hdn_GLID2" id="hdn_GLID2"/>
              <input type="hidden" name="hdn_GLID3" id="hdn_GLID3"/>
              <input type="hidden" name="hdn_GLID4" id="hdn_GLID4"/></td>
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
          <td class="ROW2"><input type="text" id="glcodesearch" class="form-control" autocomplete="off" onkeyup="GLCodeFunction()"></td>
          <td class="ROW3"><input type="text" id="glnamesearch" class="form-control" autocomplete="off" onkeyup="GLNameFunction()"></td>
        </tr>
  
      </tbody>
      </table>
        <table id="GlCodeTable2" class="display nowrap table  table-striped table-bordered" width="100%">
          <thead id="thead2">
          </thead>
          <tbody id="tbody_glsl">
          </tbody>
        </table>
      </div>
      <div class="cl"></div>
        </div>
      </div>
    </div>
  </div>




<!--Account dropdown-->
<!-- TDS Dropdown -->
<div id="Sectionpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='SectionclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Section</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SectionTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>Code</th>
            <th>Name</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="Sectioncodesearch" onkeyup="SectionCodeFunction()">
    </td>
    <td>
    <input type="text" id="Sectionnamesearch" onkeyup="SectionNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="SectionTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
         
        </thead>
        <tbody id="tbody_Section">
       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- TDS Dropdown-->



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
					

<!-- Customer / Vendor Dropdown -->
<div id="Custpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='CustclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Customer / Vendor</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CustTable" class="display nowrap table  table-striped table-bordered">
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
      <table id="CustTable2" class="display nowrap table  table-striped table-bordered">
        <thead id="thead2">
        </thead>
        <tbody id="tbody_Cust">
       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Customer / Vendor Dropdown-->

<!--Cost Centre dropdown-->

<div id="costpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:100%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='cc_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Cost Center</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CostTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>
    <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="hdn_CCID" id="hdn_CCID"/>
            <input type="hidden" name="hdn_CCID2" id="hdn_CCID2"/>
            <input type="hidden" name="hdn_CCID3" id="hdn_CCID3"/>
            <input type="hidden" name="hdn_CCID4" id="hdn_CCID4"/>
            <input type="hidden" name="hdn_CCID5" id="hdn_CCID5"/>
            <input type="hidden" name="hdn_CCID6" id="hdn_CCID6"/>
            <input type="hidden" name="hdn_CCID7" id="hdn_CCID7"/>
            </td>
    </tr>
    <tr>
            <th style="width:20%;">Account Code</th>
            <th style="width:20%;">Account Name</th>
            <th style="width:20%;">Cost Centre Code</th>
            <th style="width:20%;">Amount</th>
            <th style="width:20%;">Action</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    
    </tr>
    </tbody>
    </table>
      <table id="CostTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;">
        <thead id="thead2">
        </thead>
        <tbody id="tbody_cc"> 
          <tr class="participantRow9">
            <td style="width:20%;">
            <input type="text" name="ppGLID_0" id="ppGLID_0" class="form-control"  autocomplete="off"  readonly/></td>
            <td hidden><input type="hidden" name="hdnGLID_0" id="hdnGLID_0" class="form-control" autocomplete="off" /></td>
            <td style="width:20%;"><input type="text" name="hdnGLName_0" id="hdnGLName_0" class="form-control"  autocomplete="off"  readonly/></td>
            <td style="width:20%;"><input type="text" name="CostCenter_0" id="CostCenter_0" class="form-control" maxlength="20"  autocomplete="off" readonly  /></td>
            <td hidden><input type="hidden" name="hdnCCID_0" id="hdnCCID_0" class="form-control" autocomplete="off" /></td>
            <td style="width:20%;"><input type="text" name="hdnAMT_0" id="hdnAMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  /></td>
            <td style="width:20%;"><button class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
            <button class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
          </tr>      
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="ppcostcenter" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='closeppcostcenter' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Cost Center</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ppcostcenter1" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr id="none-select" class="searchalldata" hidden>
            <td> <input type="hidden" name="hdn_cc1" id="hdn_cc1"/>
            <input type="hidden" name="hdn_cc2" id="hdn_cc2"/></td>
    </tr>
    <tr>
            <th>Code</th>
            <th>Name</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="ppcostcodesearch" onkeyup="ppcostCodeFunction()">
    </td>
    <td>
    <input type="text" id="ppcostnamesearch" onkeyup="ppcostNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="ppcostcenter2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
        </thead>
        <tbody id="tbody_ppcost">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!--Cost Centre dropdown-->

<!--Bank dropdown Header Part-->

<div id="bank_popup" class="modal" role="dialog"  data-backdrop="static">
 <div class="modal-dialog modal-md" style="width:90%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='bank_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Bank Master</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="BankCodeTable" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
    <thead>    
    <tr>
            <th style="width:10%; text-align:center;" id="all-check">Select</th>
            <th style="width:20%;">Code</th>
            <th style="width:20%;">Name</th>
            <th style="width:10%;">Branch</th>
            <th style="width:10%;">IFSC</th>
            <th style="width:10%;">Account Type</th>
            <th style="width:10%;">Account Number</th>
            <th style="width:10%;">Address</th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td style="width:10%;text-align:center;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" disabled /></td>
      <td style="width:20%;">
        <input type="text" id="bankcodesearch" class="form-control" autocomplete="off" onkeyup="BankCodeFunction()">
      </td>
      <td style="width:20%;">
        <input type="text" id="banknamesearch" class="form-control" autocomplete="off" onkeyup="BankNameFunction()">
      </td>
      <td style="width:10%;">
        <input type="text" id="bankbranchsearch" class="form-control" autocomplete="off" onkeyup="BankBranchFunction()">
      </td>
      <td style="width:10%;">
        <input type="text" id="bankifscsearch" class="form-control" autocomplete="off" onkeyup="BankIFSCFunction()">
      </td>
      <td style="width:10%;">
        <input type="text" id="bankacctypesearch" class="form-control" autocomplete="off" onkeyup="BankAccTypeFunction()">
      </td>
      <td style="width:10%;">
        <input type="text" id="bankaccnumbersearch" class="form-control" autocomplete="off" onkeyup="BankAccNumberFunction()">
      </td>
      <td style="width:10%;">
        <input type="text" id="bankaddresssearch" class="form-control" autocomplete="off" onkeyup="BankAddressFunction()">
      </td>
    </tr>
    </tbody>
    </table>
      <table id="BankCodeTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
        <thead id="thead2">
        </thead>
        <tbody id="tbody_bank" style="font-size:12px;">
        @foreach ($objBank as $index=>$bkRow)
          <tr >
              <td style="width:10%; text-align: center;"><input type="checkbox" id="bkidcode_{{ $index }}" name="BANKPOPUP1[]"  value="{{ $bkRow-> BID }}" class="js-selectall1 clsbkid"  ></td>
              <td style="width:20%;">{{ $bkRow-> BCODE }}
                <input type="hidden" id="txtbkidcode_{{ $index }}" data-desc="{{ $bkRow-> BCODE }}" data-desc2="{{ $bkRow-> NAME }}"  data-glid_ref="{{ $bkRow->GLID_REF }}"  data-desc3="{{ $bkRow-> BANK_CASH }}" value="{{ $bkRow-> BID }}"/>
              </td>
              <td style="width:20%;">{{ $bkRow-> NAME }}</td>
              <td style="width:10%;">{{ $bkRow-> BRANCH }}</td>
              <td style="width:10%;">{{ $bkRow-> IFSC }}</td>
              <td style="width:10%;">{{ $bkRow-> ACTYPE }}</td>
              <td style="width:10%;">{{ $bkRow-> ACNO }}</td>
              <td style="width:10%;">{{ $bkRow-> ADD1 }} {{ $bkRow-> ADD2 }} {{ $bkRow-> CITY }} {{ $bkRow-> STATE }} {{ $bkRow-> COUNTRY }} {{ $bkRow-> PIN }}</td>
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
<!--Bank dropdown Header Part-->

<!--Bank dropdown Account Tab-->

<div id="bank2_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%;">
     <div class="modal-content">
       <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" id='bank2_closePopup' >&times;</button>
       </div>
     <div class="modal-body">
     <div class="tablename"><p>Bank Master</p></div>
     <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
     <table id="Bank2CodeTable" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
     <thead>    
     <tr>
             <th style="width:10%; text-align:center;" id="all-check">Select</th>
             <th style="width:20%;">Code</th>
             <th style="width:20%;">Name</th>
             <th style="width:10%;">Branch</th>
             <th style="width:10%;">IFSC</th>
             <th style="width:10%;">Account Type</th>
             <th style="width:10%;">Account Number</th>
             <th style="width:10%;">Address</th>
     </tr>
     </thead>
     <tbody>
     <tr>
       <td style="width:10%;text-align:center;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" disabled /></td>
       <td style="width:20%;">
         <input type="text" id="bank2codesearch" class="form-control" autocomplete="off" onkeyup="Bank2CodeFunction()">
       </td>
       <td style="width:20%;">
         <input type="text" id="bank2namesearch" class="form-control" autocomplete="off" onkeyup="Bank2NameFunction()">
       </td>
       <td style="width:10%;">
         <input type="text" id="bank2branchsearch" class="form-control" autocomplete="off" onkeyup="Bank2BranchFunction()">
       </td>
       <td style="width:10%;">
         <input type="text" id="bank2ifscsearch" class="form-control" autocomplete="off" onkeyup="Bank2IFSCFunction()">
       </td>
       <td style="width:10%;">
         <input type="text" id="bank2acctypesearch" class="form-control" autocomplete="off" onkeyup="Bank2AccTypeFunction()">
       </td>
       <td style="width:10%;">
         <input type="text" id="bank2accnumbersearch" class="form-control" autocomplete="off" onkeyup="Bank2AccNumberFunction()">
       </td>
       <td style="width:10%;">
         <input type="text" id="bank2addresssearch" class="form-control" autocomplete="off" onkeyup="Bank2AddressFunction()">
       </td>
     </tr>
     </tbody>
     </table>
       <table id="Bank2CodeTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;" >
         <thead id="thead2">
         </thead>
         <tbody id="tbody_bank2" style="font-size:12px;">
         @foreach ($objBank as $index=>$bkRow)
           <tr >
               <td style="width:10%; text-align: center;"><input type="checkbox" id="bkidcode2_{{ $index }}" name="CASHBANKPOPUP2[]" value="{{ $bkRow-> BID }}" class="js-selectall1 clsbkid2"  ></td>
               <td style="width:20%;">{{ $bkRow-> BCODE }}
                <input type="hidden" id="txtbkidcode2_{{ $index }}" data-desc="{{ $bkRow-> BCODE }}"  data-glid_ref="{{ $bkRow->GLID_REF }}" data-desc2="{{ $bkRow-> NAME }}"  value="{{ $bkRow-> BID }}"/>
               </td>
               <td style="width:20%;">{{ $bkRow-> NAME }}</td>
               <td style="width:10%;">{{ $bkRow-> BRANCH }}</td>
               <td style="width:10%;">{{ $bkRow-> IFSC }}</td>
               <td style="width:10%;">{{ $bkRow-> ACTYPE }}</td>
               <td style="width:10%;">{{ $bkRow-> ACNO }}</td>
               <td style="width:10%;">{{ $bkRow-> ADD1 }} {{ $bkRow-> ADD1 }} {{ $bkRow-> ADD2 }} {{ $bkRow-> CITY }} {{ $bkRow-> STATE }} {{ $bkRow-> COUNTRY }} {{ $bkRow-> PIN }}</td>
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

<!--Bank dropdown Account Tab-->

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

//------------------------
//------------------------
  //Invoicepopup 
  let cltid = "#CodeTable2";
    let cltid2 = "#CodeTable";
    let clheaders = document.querySelectorAll(cltid2 + " th");

      // Sort the table element when clicking on the table headers
      clheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(cltid, ".clsclid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function TypeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("typesearch");
        filter = input.value.toUpperCase();
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

  function DocFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("docsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("CodeTable2");
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

  function DateFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("datesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("CodeTable2");
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

  function BranchFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("branchsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("CodeTable2");
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

  function TotalFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("totalsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("CodeTable2");
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

  function BalFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("balsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("CodeTable2");
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
  function SupplierInvoiceFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("supplier_invoiceno");
        filter = input.value.toUpperCase();
        table = document.getElementById("CodeTable2");
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
  function DueDaysFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("due_day");
        filter = input.value.toUpperCase();
        table = document.getElementById("CodeTable2");
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

  $('#Invoice').on('click','[id*="txtDoc_NO_"]',function(event){
    var CommonValue           =   $('#hdnreceiptfor').val();
    if($('#CENTERLIZED_RECEIPT').is(':checked') == true)
    {
      var centralized = 1;
    }
    else
    {
      var centralized = 0;
    }
    var Customid              =   $("#CUSTMER_VENDOR_ID").val();
    if(Customid != '')
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[$FormId,"getCustVdrDocument"])}}',
            type:'POST',
            data:{'CommonValue':CommonValue,'Customid':Customid, 'centralized':centralized},
            success:function(data){
              $("#tbody_invoice").html(data);
              bindInvoiceDocument();                    
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              $("#tbody_invoice").html('');                        
            },
        });
    }
    $("#Invoicepopup").show();  

        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="DOCNO_ID"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="Doc_Type_"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="DocDate_"]').attr('id');
        var id5 = $(this).parent().parent().find('[id*="DocAmount_"]').attr('id');
        var id6 = $(this).parent().parent().find('[id*="BALANCE_DUE_"]').attr('id');
        var id61 = $(this).parent().parent().find('[id*="RECEIPT_AMT_"]').attr('id');
        var id7 = $(this).parent().parent().find('[id*="TxtBranch_"]').attr('id');
        var id8 = $(this).parent().parent().find('[id*="BRID_REF_"]').attr('id');


        $('#hdn_Invoice').val(id);
        $('#hdn_Invoice2').val(id2);
        $('#hdn_Invoice3').val(id3);
        $('#hdn_Invoice4').val(id4);
        $('#hdn_Invoice5').val(id5);
        $('#hdn_Invoice6').val(id6);
        $('#hdn_Invoice7').val(id7);
        $('#hdn_Invoice8').val(id8);
        $('#hdn_Invoice61').val(id61);

      event.preventDefault();
  });

  $("#Invoice_closePopup").click(function(event){
    $("#Invoicepopup").hide();
    $('.js-selectall').prop("checked", false);
    event.preventDefault();
  });

  function bindInvoiceDocument(){
      $('#CodeTable2').off(); 
      $('.js-selectall').change(function(){
        var isChecked = $(this).prop("checked");
        var selector = $(this).data('target');
        $(selector).prop("checked", isChecked);

        $('#CodeTable2').find('.clsinvoiceid').each(function(){

          var fieldid   =   $(this).attr('id');
          var txtval    =   $("#txt"+fieldid+"").val();
          var txtdocno  =   $("#txt"+fieldid+"").data("desc")
          var txtdocdt  =   $("#txt"+fieldid+"").data("desc2")
          var txtbranch =   $("#txt"+fieldid+"").data("desc3")
          var txtdocamt =   $("#txt"+fieldid+"").data("desc4")
          var txtbalamt =   $("#txt"+fieldid+"").data("desc5")
          var txtbrdid  =   $("#txt"+fieldid+"").data("desc6")
          var txtsource =   $("#txt"+fieldid+"").data("desc7"); 
        

     

          var INVOICE = [];
          $('#example2').find('.participantRow2').each(function(){
            if($(this).find('[id*="txtDoc_NO_"]').val() != '')
            {
              var item = $(this).find('[id*="txtDoc_NO_"]').val();
              INVOICE.push(item);
            }
          });
          if($(this).find('[id*="chkId"]').is(":checked") == true) 
          {

              var txtinvoice = txtdocno;

              if(jQuery.inArray(txtinvoice, INVOICE) !== -1)
              {
                    $("#Invoicepopup").hide();
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('Document already exists.');
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    $('#hdn_Invoice').val('');
                    $('#hdn_Invoice2').val('');
                    $('#hdn_Invoice3').val('');
                    $('#hdn_Invoice4').val('');
                    $('#hdn_Invoice5').val('');
                    $('#hdn_Invoice6').val('');
                    $('#hdn_Invoice61').val('');
                    $('#hdn_Invoice7').val('');
                    $('#hdn_Invoice8').val('');
                    $('#hdn_Invoice9').val('');
                    txtval = '';
                    txtdocno = '';
                    txtdocdt = '';
                    txtbranch = '';
                    txtdocamt = '';
                    txtbalamt = '';
                    txtbrdid = '';
                    txtsource = '';
                    $('.js-selectall').prop("checked", false);
                    return false;
                    event.preventDefault();
              }

              if($('#hdn_Invoice').val() == "" && txtval != '')
              {
                
                var $tr = $('.ainvoice').closest('table');
                var allTrs = $tr.find('.participantRow2').last();
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
                $clone.find('[id*="txtDoc_NO_"]').val(txtdocno);
                $clone.find('[id*="DOCNO_ID_"]').val(txtval);
                $clone.find('[id*="Doc_Type_"]').val(txtsource);
                $clone.find('[id*="DocDate_"]').val(txtdocdt);
                $clone.find('[id*="TxtBranch_"]').val(txtbranch);
                $clone.find('[id*="BRID_REF_"]').val(txtbrdid);
                $clone.find('[id*="DocAmount_"]').val(txtdocamt);
                $clone.find('[id*="BALANCE_DUE_"]').val(txtbalamt);
                $clone.find('[id*="RECEIPT_AMT_"]').val(txtbalamt);
                $tr.closest('table').append($clone);   
                var rowCount = $('#Row_Count1').val();
                rowCount = parseInt(rowCount)+1;
                $('#Row_Count1').val(rowCount);

                $('#hdn_Invoice').val('');
                $('#hdn_Invoice2').val('');
                $('#hdn_Invoice3').val('');
                $('#hdn_Invoice4').val('');
                $('#hdn_Invoice5').val('');
                $('#hdn_Invoice6').val('');
                $('#hdn_Invoice61').val('');
                $('#hdn_Invoice7').val('');
                $('#hdn_Invoice8').val('');
                $('#hdn_Invoice9').val('');

                event.preventDefault();
              }
              else
              {

                var txtid= $('#hdn_Invoice').val();
                var txt_id2= $('#hdn_Invoice2').val();
                var txt_id3= $('#hdn_Invoice3').val();
                var txt_id4= $('#hdn_Invoice4').val();
                var txt_id5= $('#hdn_Invoice5').val();
                var txt_id6= $('#hdn_Invoice6').val();
                var txt_id61= $('#hdn_Invoice61').val();
                var txt_id7= $('#hdn_Invoice7').val();
                var txt_id8= $('#hdn_Invoice8').val();
               

               

                $('#'+txtid).val(txtdocno);
                $('#'+txt_id2).val(txtval);
                $('#'+txt_id3).val(txtsource);
                $('#'+txt_id4).val(txtdocdt);
                $('#'+txt_id5).val(txtdocamt);
                $('#'+txt_id6).val(txtbalamt);
                $('#'+txt_id7).val(txtbranch);
                $('#'+txt_id8).val(txtbrdid);
                $('#'+txt_id61).val(txtbalamt);
               
              


                

                $('#hdn_Invoice').val('');
                $('#hdn_Invoice2').val('');
                $('#hdn_Invoice3').val('');
                $('#hdn_Invoice4').val('');
                $('#hdn_Invoice5').val('');
                $('#hdn_Invoice6').val('');
                $('#hdn_Invoice61').val('');
                $('#hdn_Invoice7').val('');
                $('#hdn_Invoice8').val('');
                $('#hdn_Invoice61').val('');
                event.preventDefault();
              }
          }
          else if($(this).find('[id*="chkId"]').is(":checked") == false)
          {
       
            var invoice  = txtdocno;
            $('#example2').find('.participantRow2').each(function()
            {
              var docno = $(this).find('[id*="txtDoc_NO_"]').val();

              if(docno == invoice)
              {
                  var rowCount = $('#Row_Count1').val();
                  if (rowCount > 1) {
                    $(this).closest('.participantRow2').remove(); 
                    rowCount = parseInt(rowCount)-1;
                    $('#Row_Count1').val(rowCount);
                    event.preventDefault();
                  }
                  else 
                  {
                    $(document).find('.dinvoice').prop('disabled', true);  
                    $("#Invoicepopup").hide();
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
              }
            });
            // event.preventDefault();
          }

          // $("#Invoicepopup").hide();
          $("#typesearch").val(''); 
          $("#docsearch").val(''); 
          $("#datesearch").val(''); 
          $("#branchsearch").val('');
          $("#totalsearch").val(''); 
          $("#balancesearch").val('');
          $('.remove').removeAttr('disabled'); 
          DocFunction();
          bindTotalValue();
          
          event.preventDefault();
        });
        event.preventDefault();
      });
      $('[id*="chkId"]').change(function(){

        var fieldid   =   $(this).parent().parent().attr('id');
        var txtval    =   $("#txt"+fieldid+"").val();
        var txtdocno  =   $("#txt"+fieldid+"").data("desc")
        var txtdocdt  =   $("#txt"+fieldid+"").data("desc2")
        var txtbranch =   $("#txt"+fieldid+"").data("desc3")
        var txtdocamt =   $("#txt"+fieldid+"").data("desc4")
        var txtbalamt =   $("#txt"+fieldid+"").data("desc5")
        var txtbrdid  =   $("#txt"+fieldid+"").data("desc6")
        var txtsource =   $("#txt"+fieldid+"").data("desc7");

        var INVOICE = [];
        $('#example2').find('.participantRow2').each(function(){
          if($(this).find('[id*="txtDoc_NO_"]').val() != '')
          {
            var item = $(this).find('[id*="txtDoc_NO_"]').val();
            INVOICE.push(item);
          }
        });

        if($(this).is(":checked") == true) 
          {

              var txtinvoice = txtdocno;

              if(jQuery.inArray(txtinvoice, INVOICE) !== -1)
              {
                    $("#Invoicepopup").hide();
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('Document already exists.');
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    $('#hdn_Invoice').val('');
                    $('#hdn_Invoice2').val('');
                    $('#hdn_Invoice3').val('');
                    $('#hdn_Invoice4').val('');
                    $('#hdn_Invoice5').val('');
                    $('#hdn_Invoice6').val('');
                    $('#hdn_Invoice7').val('');
                    $('#hdn_Invoice8').val('');
                    $('#hdn_Invoice61').val('');
                    txtval = '';
                    txtdocno = '';
                    txtdocdt = '';
                    txtbranch = '';
                    txtdocamt = '';
                    txtbalamt = '';
                    txtbrdid = '';
                    txtsource = '';
                    $('.js-selectall').prop("checked", false);
                    return false;
                    event.preventDefault();
              }

              if($('#hdn_Invoice').val() == "" && txtval != '')
              {
                
                var $tr = $('.ainvoice').closest('table');
                var allTrs = $tr.find('.participantRow2').last();
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

                $clone.find('.dinvoice').removeAttr('disabled'); 
                $clone.find('[id*="txtDoc_NO_"]').val(txtdocno);
                $clone.find('[id*="DOCNO_ID_"]').val(txtval);
                $clone.find('[id*="Doc_Type_"]').val(txtsource);
                $clone.find('[id*="DocDate_"]').val(txtdocdt);
                $clone.find('[id*="TxtBranch_"]').val(txtbranch);
                $clone.find('[id*="BRID_REF_"]').val(txtbrdid);
                $clone.find('[id*="DocAmount_"]').val(txtdocamt);
                $clone.find('[id*="BALANCE_DUE_"]').val(txtbalamt);
                $clone.find('[id*="RECEIPT_AMT_"]').val(txtbalamt);
                $tr.closest('table').append($clone);   
                var rowCount = $('#Row_Count1').val();
                rowCount = parseInt(rowCount)+1;
                $('#Row_Count1').val(rowCount);

                $('#hdn_Invoice').val('');
                $('#hdn_Invoice2').val('');
                $('#hdn_Invoice3').val('');
                $('#hdn_Invoice4').val('');
                $('#hdn_Invoice5').val('');
                $('#hdn_Invoice6').val('');
                $('#hdn_Invoice7').val('');
                $('#hdn_Invoice8').val('');
                $('#hdn_Invoice61').val('');

                event.preventDefault();
              }
              else
              {

                var txtid= $('#hdn_Invoice').val();
                var txt_id2= $('#hdn_Invoice2').val();
                var txt_id3= $('#hdn_Invoice3').val();
                var txt_id4= $('#hdn_Invoice4').val();
                var txt_id5= $('#hdn_Invoice5').val();
                var txt_id6= $('#hdn_Invoice6').val();
                var txt_id7= $('#hdn_Invoice7').val();
                var txt_id8= $('#hdn_Invoice8').val();
                var txt_id61= $('#hdn_Invoice61').val();

                $('#'+txtid).val(txtdocno);
                $('#'+txt_id2).val(txtval);
                $('#'+txt_id3).val(txtsource);
                $('#'+txt_id4).val(txtdocdt);
                $('#'+txt_id5).val(txtdocamt);
                $('#'+txt_id6).val(txtbalamt);
                $('#'+txt_id7).val(txtbranch);
                $('#'+txt_id8).val(txtbrdid);
                $('#'+txt_id61).val(txtbalamt);
                
                

                

               
              

                $('#hdn_Invoice').val('');
                $('#hdn_Invoice2').val('');
                $('#hdn_Invoice3').val('');
                $('#hdn_Invoice4').val('');
                $('#hdn_Invoice5').val('');
                $('#hdn_Invoice6').val('');
                $('#hdn_Invoice7').val('');
                $('#hdn_Invoice8').val('');
                // event.preventDefault();
              }
          }
          else if($(this).is(":checked") == false)
          {
            var invoice  = txtdocno;
            $('#example2').find('.participantRow2').each(function()
            {
              var docno = $(this).find('[id*="txtDoc_NO_"]').val();

              if(docno == invoice)
              {
                  var rowCount = $('#Row_Count1').val();
                  if (rowCount > 1) {
                    $(this).closest('.participantRow2').remove(); 
                    rowCount = parseInt(rowCount)-1;
                    $('#Row_Count1').val(rowCount);
                    event.preventDefault();
                  }
                  else 
                  {
                    $(document).find('.dinvoice').prop('disabled', true);  
                    $("#Invoicepopup").hide();
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
                // event.preventDefault(); 
              }
            });
            // event.preventDefault();
          }

          // $("#Invoicepopup").hide();
          $("#typesearch").val(''); 
          $("#docsearch").val(''); 
          $("#datesearch").val(''); 
          $("#branchsearch").val('');
          $("#totalsearch").val(''); 
          $("#balancesearch").val('');
          DocFunction();
          bindTotalValue();
          event.preventDefault();

      });
  }
  //Invoicepopup Ends
//------------------------

//------------------------
  //Customer / Vendor
  let Custtid = "#CustTable2";
    let Custtid2 = "#CustTable";
    let Custheaders = document.querySelectorAll(Custtid2 + " th");

      // Sort the table element when clicking on the table headers
      Custheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(Custtid, ".clscustid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function CustomerCodeFunction(FORMID) 
    {
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
          table = document.getElementById("CustTable2");
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

    function CustomerNameFunction(FORMID) 
    {
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
          table = document.getElementById("CustTable2");
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
    
    function loadCustomer(CODE,NAME,FORMID)
    {
      var url	=	'<?php echo asset('');?>transaction/'+FORMID+'/getCustVendor';
        $("#tbody_Cust").html('');
        var CommonValue = $('#hdnreceiptfor').val();
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          url:url,
          type:'POST',
          data:{'CODE':CODE,'NAME':NAME,'CommonValue':CommonValue},
            success:function(data) {
            $("#tbody_Cust").html(data); 
            bindCustomerVendor();  
            showSelectedCheck($("#CUSTMER_VENDOR_ID").val(),"SELECT_SLID_REF");
            event.preventDefault();
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $("#tbody_Cust").html('');                        
          },
        });
    }

  $('#txtcustomer').on('click',function(event){
    var CODE = ''; 
    var NAME = ''; 
    var FORMID = "{{$FormId}}";
    loadCustomer(CODE,NAME,FORMID);
    $("#Custpopup").show();    
    event.preventDefault();
  });

  $("#CustclosePopup").click(function(event){
    $("#Custpopup").hide();
    $("#customercodesearch").val(''); 
    $("#customernamesearch").val(''); 
    event.preventDefault();
  });

  function bindCustomerVendor(){
    $(".clscustid").click(function(){
         
          var fieldid               =   $(this).attr('id');
          var txtval                =   $("#txt"+fieldid+"").val();
          var texdesc               =   $("#txt"+fieldid+"").data("desc")+'-'+$("#txt"+fieldid+"").data("desc2"); 
          var oldCUSTMER_VENDOR_ID  =   $("#CUSTMER_VENDOR_ID").val();
          var InvoiceClone          =   $('#hdnInvoice').val(); 

          
          $('#txtcustomer').val(texdesc);
          $('#CUSTMER_VENDOR_ID').val(txtval);


          var TDSClone = $('#hdnTDS').val();       


          if (txtval != oldCUSTMER_VENDOR_ID)
          {
              $('#Invoice').html(InvoiceClone);
           
              $('#TDS').html(TDSClone);
   
              $('#Row_Count6').val('1');
			  $('#Row_Count1').val('1');

              var CommonValue =   $('#hdnreceiptfor').val();
              
              var customid=txtval; 
              if(CommonValue=='Vendor'){
              $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  })

                  $.ajax({
                      url:'{{route("transaction",[302,"getTDSApplicability"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data){
                        if(data == 1){

                          $('#drpTDS').val('Yes');
                              $.ajaxSetup({
                                  headers: {
                                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                  }
                              });
                              $.ajax({
                                  url:'{{route("transaction",[302,"getTDSDetails"])}}',
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
        }else if(CommonValue=='Customer'){
          $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  })

                  $.ajax({
                      url:'{{route("transaction",[302,"getTDSApplicability_customer"])}}',
                      type:'POST',
                      data:{'id':customid},
                      success:function(data){
                        if(data == 1){

                          $('#drpTDS').val('Yes');
                              $.ajaxSetup({
                                  headers: {
                                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                  }
                              });
                              $.ajax({
                                  url:'{{route("transaction",[302,"getTDSDetails_customer"])}}',
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

                  getTaxStatus_customer(customid);
        }

          }
          $("#Custpopup").hide();
          $("#Custcodesearch").val(''); 
          $("#Custnamesearch").val(''); 
         
          
          event.preventDefault();
      });
  }
  //Customer / Vendor Ends
//------------------------

//------------------------
  //Bank Dropdown Header
  let Banktid = "#BankCodeTable2";
    let Banktid2 = "#BankCodeTable";
    let Bankheaders = document.querySelectorAll(Banktid2 + " th");

      // Sort the table element when clicking on the table headers
      Bankheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(Banktid, ".clsbkid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function BankCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bankcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("BankCodeTable2");
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

      function BankNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("banknamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("BankCodeTable2");
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

      function BankBranchFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bankbranchsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("BankCodeTable2");
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

      function BankIFSCFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bankifscsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("BankCodeTable2");
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

      function BankAccTypeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bankacctypesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("BankCodeTable2");
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

      function BankAccNumberFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bankaccnumbersearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("BankCodeTable2");
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

      function BankAddressFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bankaddresssearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("BankCodeTable2");
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

  $('#txtcashbk').on('click',function(event){    
    showSelectedCheck($("#CASH_BANK_ID").val(),"BANKPOPUP1");
    $("#bank_popup").show();    
    event.preventDefault();
  });

  $("#bank_closePopup").click(function(event){
    $("#bank_popup").hide();
    event.preventDefault();
  });

  $(".clsbkid").click(function(){
      var fieldid               =   $(this).attr('id');
      var txtval                =   $("#txt"+fieldid+"").val();
      var texdesc               =   $("#txt"+fieldid+"").data("desc")+'-'+$("#txt"+fieldid+"").data("desc2");
      var txtflag               =   $("#txt"+fieldid+"").data("desc3");
      var glid_ref               =   $("#txt"+fieldid+"").data("glid_ref");

      if(txtflag == 'C')
      {
        txtflag = 'Cash';
      }
      else if(txtflag == 'B')
      {
        txtflag = 'Bank';
      }
      else
      {
        txtflag = '';
      }
            
      $('#txtcashbk').val(texdesc);
      $('#CASH_BANK_ID').val(txtval);
      $('#RECEIPT_TYPE').val(txtflag);
      getBalance(glid_ref,'BALANCE','BALANCE_SHOW'); 
      $("#bank_popup").hide();
      $("#bankcodesearch").val(''); 
      $("#banknamesearch").val('');
      $("#bankbranchsearch").val(''); 
      $("#bankifscsearch").val(''); 
      $("#bankacctypesearch").val(''); 
      $("#bankaccnumbersearch").val('');
      $("#bankaddresssearch").val('');
      BankCodeFunction();
      
      event.preventDefault();
  });
  
  //Bank Dropdown Header Ends
//------------------------

//------------------------
  //Bank Dropdown Header
  let Bank2tid = "#Bank2CodeTable2";
    let Bank2tid2 = "#Bank2CodeTable";
    let Bank2headers = document.querySelectorAll(Bank2tid2 + " th");

      // Sort the table element when clicking on the table headers
      Bank2headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(Bank2tid, ".clsbk2id", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function Bank2CodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bank2codesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("Bank2CodeTable2");
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

      function Bank2NameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bank2namesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("Bank2CodeTable2");
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

      function Bank2BranchFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bank2branchsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("Bank2CodeTable2");
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

      function Bank2IFSCFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bank2ifscsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("Bank2CodeTable2");
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

      function Bank2AccTypeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bank2acctypesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("Bank2CodeTable2");
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

      function Bank2AccNumberFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bank2accnumbersearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("Bank2CodeTable2");
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

      function Bank2AddressFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bank2addresssearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("Bank2CodeTable2");
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

  $('#Account').on('click','#txtbnkcsh',function(event){    

    showSelectedCheck($("#BANK_CASH_ID").val(),"CASHBANKPOPUP2");

    $("#bank2_popup").show();    
    event.preventDefault();
  });

  $("#bank2_closePopup").click(function(event){
    $("#bank2_popup").hide();
    event.preventDefault();
  });

  $(".clsbkid2").click(function(){
      var fieldid               =   $(this).attr('id');
      var txtval                =   $("#txt"+fieldid+"").val();
      var texdesc               =   $("#txt"+fieldid+"").data("desc")+'-'+$("#txt"+fieldid+"").data("desc2"); 
      var glid_ref               =   $("#txt"+fieldid+"").data("glid_ref");
            
      $('#txtbnkcsh').val(texdesc);
      $('#BANK_CASH_ID').val(txtval);
      getBalance(glid_ref,'BALANCE_ACCOUNT','BALANCE_ACCOUNT_SHOW'); 
      $("#bank2_popup").hide();
      $("#bank2codesearch").val(''); 
      $("#bank2namesearch").val('');
      $("#bank2branchsearch").val(''); 
      $("#bank2ifscsearch").val(''); 
      $("#bank2acctypesearch").val(''); 
      $("#bank2accnumbersearch").val('');
      $("#bank2addresssearch").val('');
      Bank2CodeFunction();      
      event.preventDefault();
  });
  
  //Bank Dropdown Header Ends
//------------------------



//------------------------
  //Account Popup
  let Accountid = "#AccountCodeTable2";
      let Accountid2 = "#AccountCodeTable";
      let Accountheaders = document.querySelectorAll(Accountid2 + " th");

      // Sort the table element when clicking on the table headers
      Accountheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(Accountid, ".clsAccount", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function AccountCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Accountcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("AccountCodeTable2");
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

  function AccountNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Accountnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("AccountCodeTable2");
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
  $('#Account').on('click','[id*="popupAccount_"]',function(event){
        if($('#BANK_CASH_ID').val() == '')
        {
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Select Bank / Cash Option');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
            return false;
        }
        else
        {  
          
          var SL = $('#SubGL').is(':checked');



          // if(SL=='true'){
          // }else{
          //   resetTab();            
          // }



          
          var fieldid = $(this).parent().parent().find('[id*="GLID_REF"]').attr('id');
          $("#tbody_glsl").html('');

          $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[302,"getglsl"])}}',
            type:'POST',
            data:{'SL':SL,fieldid:fieldid},
            success:function(data) {
              $("#tbody_glsl").html(data);    
              bindGeneralLedger();  
              showSelectedCheck($("#"+fieldid).val(),"SELECT_"+fieldid);                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              $("#tbody_glsl").html('');                        
            },
        });

          $("#glsl_popup").show();

          var id=$(this).attr('id');
          var result = id.split('_');     
          var hidden_val=$("#GLID_REF_"+result[1]).val();
          showSelectedCheck(hidden_val,"glname");


            //$("#account_popup").show();
            var id = $(this).attr('id');
            var id2 = $(this).parent().parent().find('[id*="GLID_REF_"]').attr('id');
            var id3 = $(this).parent().parent().find('[id*="AccountName_"]').attr('id');
            $('#hdn_Account').val(id);
            $('#hdn_Account2').val(id2);
            $('#hdn_Account3').val(id3);
            event.preventDefault();
            
        }
      });

      $("#account_closePopup").click(function(event){
        $("#account_popup").hide();
        event.preventDefault();
      });

      $("#gl_closePopup").click(function(event){
        $("#glsl_popup").hide();
        event.preventDefault();
      });


      function bindGeneralLedger() {
      $(".clsglid").click(function(){
          var fieldid = $(this).attr('id');
          var txtval =    $("#txt"+fieldid+"").val();
          var txtcode =   $("#txt"+fieldid+"").data("desc");
          var txtname =   $("#txt"+fieldid+"").data("desc2");
          var txtid= $('#hdn_Account').val();
          var txt_id2= $('#hdn_Account2').val();
          var txt_id3= $('#hdn_Account3').val();

          $('#'+txtid).val(txtcode);
          $('#'+txt_id2).val(txtval);
          $('#'+txt_id3).val(txtname);

          var rowid="ACCOUNT_BALANCE_"+txt_id3.split('_').pop();   
          getBalanceGrid(txtval,rowid); 

          $("#glsl_popup").hide();
          $("#Accountcodesearch").val(''); 
          $("#Accountnameesearch").val(''); 
          AccountCodeFunction();        
          event.preventDefault();
      });

    }
  //Bill Address Ends
//------------------------

//------------------------
  //Cost Center Dropdown
    


  $('#Account').on('click','[id*="BtnCCID"]',function(event){
    $("#costpopup").show();
    var id = $(this).parent().parent().find('[id*="CCID_REF"]').attr('id');
    var glcode = $(this).parent().parent().find('[id*="popupAccount_"]').val();
    var glid = $(this).parent().parent().find('[id*="GLID_REF"]').val();
    var gldesc = $(this).parent().parent().find('[id*="AccountName_"]').val();
    var glamt = $(this).parent().parent().find('[id*="AMOUNT_"]').val();
        $('#hdn_CCID').val(id);
        $('#hdn_CCID2').val(glcode);
        $('#hdn_CCID3').val(glid);
        $('#hdn_CCID4').val(gldesc);
        $('#hdn_CCID5').val(glamt);

        var objcost = <?php echo json_encode($objCostCenter); ?>;    
        var gl12 = [];
        $('#example5').find('.participantRow5').each(function(){
          if($(this).find('[id*="GLID"]').val() != '')
          {
            var glitem = $(this).find('[id*="GLID"]').val();
            gl12.push(glitem);
          }
        });

        if(jQuery.inArray(glid, gl12) !== -1)
        {          
          $('#example5').find('.participantRow5').each(function(){           

            if($(this).find('[id*="GLID"]').val() == glid)
            {
                var ccid = $(this).find('[id*="CCID"]').val();
                var GL_AMT = $(this).find('[id*="GL_AMT_"]').val();
                var cccode = '';
                $.each( objcost, function( cckey, ccvalue ) {
                  if(ccvalue.CCID == ccid)
                  {
                    cccode = ccvalue.CCCODE;
                  }
                });

                var $tr = $('.participantRow9').closest('#CostTable2');
                var allTrs = $tr.find('.participantRow9').last();
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

                $clone.find('[id*="ppGLID"]').val(glcode);
                $clone.find('[id*="hdnGLID"]').val(glid);
                $clone.find('[id*="hdnGLName"]').val(gldesc);
                $clone.find('[id*="hdnAMT_"]').val(GL_AMT);
                $clone.find('[id*="hdnCCID_"]').val(ccid);
                $clone.find('[id*="CostCenter_"]').val(cccode);
                $tr.closest('#CostTable2').append($clone);                
            }
          });

          $('#CostTable2').find('.participantRow9').each(function()
          {
            if($(this).find('[id*="hdnGLID"]').val() == '')
            {
              $(this).closest("tr").remove();
            }
          });

        }
        else
        {
            $('#CostTable2').find('.participantRow9').each(function(){
                $(this).find('[id*="ppGLID"]').val(glcode);
                $(this).find('[id*="hdnGLID"]').val(glid);
                $(this).find('[id*="hdnGLName"]').val(gldesc);
            });
        }
        bindCostCenter();        
        event.preventDefault();
  });

  $("#costpopup").on('click',"#cc_closePopup",function(event){
        var gl_amt = $('#hdn_CCID5').val();
        var ccamt = 0.00;
        if(gl_amt != '')
        {
          $('#CostTable2').find('.participantRow9').each(function(){
              var ccamt2 = $(this).find('[id*="hdnAMT"]').val();
              ccamt = parseFloat(parseFloat(ccamt)+parseFloat(ccamt2)).toFixed(2);
          });
          if(ccamt != 'NaN' && ccamt != '0.00' && ccamt != 0 )
          {
            if (parseFloat(ccamt) != parseFloat(gl_amt))
            {
                  $('[id*="hdnAMT_"]').val('');
                  $("#FocusId").val($('[id*="hdnAMT_"]'));
                  $("#ProceedBtn").focus();
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text('Cost Center Amount must be equal to Amount entered in Account tab.');
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
            }          
            else
            {
            }
          }
        }        

        $('#CostTable2').find('.participantRow9').each(function(){

            var GLID_REF = $(this).find('[id*="hdnGLID_"]').val();
            var CCID_REF = $(this).find('[id*="hdnCCID_"]').val();
            var GLAMT_REF = $(this).find('[id*="hdnAMT_"]').val();
            var txtid = $('#hdn_CCID').val();
            var CostCenter12= [];
            $('#example5').find('.participantRow5').each(function(){
              if($(this).find('[id*="GLID"]').val() != '')
              {
                var ccitem = $(this).find('[id*="GLID"]').val()+'-'+$(this).find('[id*="CCID"]').val();
                CostCenter12.push(ccitem);
              }
            });

            var costitem = GLID_REF+'-'+CCID_REF;
            if(jQuery.inArray(costitem, CostCenter12) !== -1)
            {
              $('#example5').find('.participantRow5').each(function(){
              if($(this).find('[id*="GLID"]').val() != '')
                {
                  if(costitem == $(this).find('[id*="GLID"]').val()+'-'+$(this).find('[id*="CCID"]').val())
                  {
                    $(this).find('[id*="GL_AMT"]').val(GLAMT_REF);
                  }
                }
              });
              if ($('#'+txtid).val().indexOf(CCID_REF) !== -1) {                
              } 
              else 
              {
                if($('#'+txtid).val() == '')
                {
                  $('#'+txtid).val(CCID_REF);
                }
                else
                {
                  $('#'+txtid).val($('#'+txtid).val()+','+CCID_REF);
                }
              }
            }
            else
            {
              if(GLAMT_REF != 'NaN' && GLAMT_REF != '')
              {
                var $tr = $('.participantRow5').closest('table');
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

                $clone.find('[id*="GLID_"]').val(GLID_REF);
                $clone.find('[id*="CCID_"]').val(CCID_REF);
                $clone.find('[id*="GL_AMT_"]').val(GLAMT_REF);
                $tr.closest('table').append($clone);   
                var rowCount3 = $('#Row_Count2').val();
                rowCount3 = parseInt(rowCount3)+1;
                $('#Row_Count2').val(rowCount3); 
                if ($('#'+txtid).val().indexOf(CCID_REF) !== -1) {                
                } 
                else 
                {
                  if($('#'+txtid).val() == '')
                  {
                    $('#'+txtid).val(CCID_REF);
                  }
                  else
                  {
                    $('#'+txtid).val($('#'+txtid).val()+','+CCID_REF);
                  }
                }
                $('#example5').find('.participantRow5').each(function()
                  {
                    if($(this).find('[id*="GLID"]').val() == '')
                    {
                      $(this).closest("tr").remove();
                    }
                });
              }
            }
        });
        $('#CostTable2').off(); 
        $("#costpopup").hide();
        var ccpop = $('#hdnCostCenter').val();
        $("#costpopup").html(ccpop);
        event.preventDefault();      
      });

    function bindCostCenter(){
      $('#CostTable2').on('focusout','[id*="hdnAMT"]',function(event){        
        if($(this).val() != '')
        {
          if(intRegex.test($(this).val())){
            $(this).val($(this).val() +'.00');
          }
        }
        
      });      
    }

      

  //Cost Center Dropdown Ends
//------------------------

//------------------------
 //Cost Center Dropdown2

  let cid = "#ppcostcenter2";
    let cid2 = "#ppcostcenter1";
    let ccheaders = document.querySelectorAll(cid2 + " th");

      // Sort the table element when clicking on the table headers
      ccheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(cid, ".clscccd", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function ppcostCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ppcostcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ppcostcenter2");
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

  function ppcostNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ppcostnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ppcostcenter2");
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

  $('#costpopup').on('click','[id*="CostCenter"]',function(event){
    var customid = $(this).parent().parent().find('[id*="hdnGLID"]').val();
      $("#tbody_ppcost").html('');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[$FormId,"getCostCenter"])}}',
            type:'POST',
            data:{'customid':customid},
            success:function(data) {
              $("#tbody_ppcost").html(data);    
              bindCostCenter2();                    
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              $("#tbody_ppcost").html('');                        
            },
        });
    $("#ppcostcenter").show();
    var id = $(this).attr('id');
    var id2 = $(this).parent().parent().find('[id*="hdnCCID"]').attr('id');
    $('#hdn_cc1').val(id);
    $('#hdn_cc2').val(id2);
    event.preventDefault();
  });

  $("#closeppcostcenter").click(function(event){
    $("#ppcostcenter").hide();
    event.preventDefault();
  });

  function bindCostCenter2()
  {
    $('#ppcostcenter2').off(); 
    $(".clscccd").click(function(){
      var fieldid = $(this).attr('id');
      var txtid =    $("#txt"+fieldid+"").val();
      var txtval =   $("#txt"+fieldid+"").data("desc");

      var txt_id1= $('#hdn_cc1').val();
      var txt_id2= $('#hdn_cc2').val();
      
      $('#'+txt_id1).val(txtval);
      $('#'+txt_id2).val(txtid);
      $("#ppcostcenter").hide();
      $("#ppcostcodesearch").val(''); 
      $("#ppcostnamesearch").val(''); 
      ppcostCodeFunction();
      event.preventDefault();
    });
  }  
  



 //Cost Center2 Dropdown Ends
//------------------------

//------------------------
     
$(document).ready(function(e) {
var Invoice = $("#Invoice").html(); 
$('#hdnInvoice').val(Invoice);
var tds = $("#TDS").html(); 
$('#hdntds').val(tds);
var Account = $("#Account").html(); 
$('#hdnAccount').val(Account);
var CostCenter2 = $("#costpopup").html(); 
$('#hdnCostCenter').val(CostCenter2);
var CostCenter = $("#CostCenter").html(); 
$('#hdnCostCenter2').val(CostCenter);


var objlastdt = <?php echo json_encode($objlastdt[0]->RECEIPT_DT); ?>;
var today = new Date(); 
var ardate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
$('#RECEIPT_DT').attr('min',objlastdt);
$('#RECEIPT_DT').attr('max',ardate);
$('#RECEIPT_DT').val(ardate);

$("#Row_Count1").val("1");
$("#Row_Count3").val("1");
$("#Row_Count2").val("1");

$('#chk_Vendor').on('change',function()
{
  if($(this).is(':checked') == true)
  {
    $('#chk_Customer').prop('checked', false);
    $('#chk_Account').prop('checked', false);
    $('#hdnreceiptfor').val('Vendor');



    // $('#div_invoice').show();
    // $('#div_account').hide();
    // $('#div_invoice').addClass('active');
    // $('#div_tds').removeClass('active');
    // $('#Invoice').show();
    // $('#Account').hide();


    $('#div_invoice').show();
    $('#div_account').hide();
    $('.nav-tabs li:eq(0) a').tab('show');




    $('#divcust1').show();
    $('#divcust2').show();
    $('#divcust3').show();
    $('#divcust4').show();
    $('#txtcustomer').val('');
    $('#CUSTMER_VENDOR_ID').val('');
    $('#AMOUNT').val('');
    $('#txtcashbk').val('');
    $('#CASH_BANK_ID').val('');
    $('#RECEIPT_TYPE').val('');
    $('#TRANSACTION_DT').val('');
    $('#INSTRUMENT_NO').val('');
    $('#BANK_CHARGE').val('');
    $('#NARRATION').val('');
    var InvoiceClone = $('#hdnInvoice').val(); 
    var AccountClone = $('#hdnAccount').val(); 
    var CostClone = $('#hdnCostCenter2').val();
    var tds = $('#hdntds').val();
    $('#Invoice').html(InvoiceClone);
    $('#Row_Count1').val('1');
    $('#Account').html(AccountClone);
    $('#Row_Count3').val('1');
    $('#CostCenter').html(CostClone);
    $('#TDS').html(tds);
    $('#Row_Count2').val('1');
    $('#Row_Count6').val('1');

    if($("#chk_RcptAccount").is(':checked') == true){
      $('#div_invoice').hide();
      $('#div_account').hide();
      $('.nav-tabs li:eq(2) a').tab('show');
      }else{
      $('#div_invoice').show();
      $('#div_account').hide();
      $('.nav-tabs li:eq(0) a').tab('show');
      }

  }
  // else
  // {
  //   $(this).prop('checked', false);
  //   $('#hdnreceiptfor').val('');
  //   $('#div_invoice').hide();
  //   $('#div_account').hide();
  //   $('#div_invoice').removeClass('active');
  //   $('#div_tds').removeClass('active');
  //   $('#Invoice').hide();
  //   $('#Account').hide();
  //   $('#divcust1').hide();
  //   $('#divcust2').hide();
  //   $('#divcust3').hide();
  //   $('#divcust4').hide();
  //   $('#txtcustomer').val('');
  //   $('#CUSTMER_VENDOR_ID').val('');
  //   $('#AMOUNT').val('');
  //   $('#txtcashbk').val('');
  //   $('#CASH_BANK_ID').val('');
  //   $('#RECEIPT_TYPE').val('');
  //   $('#TRANSACTION_DT').val('');
  //   $('#INSTRUMENT_NO').val('');
  //   $('#BANK_CHARGE').val('');
  //   $('#NARRATION').val('');
  //   var InvoiceClone = $('#hdnInvoice').val(); 
  //   var AccountClone = $('#hdnAccount').val(); 
  //   var CostClone = $('#hdnCostCenter2').val();
  //   var tds = $('#hdntds').val();
  //   $('#Invoice').html(InvoiceClone);
  //   $('#Row_Count1').val('1');
  //   $('#Account').html(AccountClone);
  //   $('#Row_Count3').val('1');
  //   $('#CostCenter').html(CostClone);
  //   $('#TDS').html(tds);
  //   $('#Row_Count2').val('1');
  //   $('#Row_Count6').val('1');
  // }
});
$('#chk_Customer').on('change',function()
{
  if($(this).is(':checked') == true)
  {
    $('#chk_Vendor').prop('checked', false);
    $('#chk_Account').prop('checked', false);
    $('#hdnreceiptfor').val('Customer');

    // $('#div_invoice').show();
    // $('#div_account').hide();
    // $('#div_invoice').addClass('active');
    // $('#div_tds').removeClass('active');
    // $('#Invoice').show();
    // $('#Account').hide();
    $('#div_invoice').show();
    $('#div_account').hide();
    $('.nav-tabs li:eq(0) a').tab('show');

    $('#divcust1').show();
    $('#divcust2').show();
    $('#divcust3').show();
    $('#divcust4').show();
    $('#txtcustomer').val('');
    $('#CUSTMER_VENDOR_ID').val('');
    $('#AMOUNT').val('');
    $('#txtcashbk').val('');
    $('#CASH_BANK_ID').val('');
    $('#RECEIPT_TYPE').val('');
    $('#TRANSACTION_DT').val('');
    $('#INSTRUMENT_NO').val('');
    $('#BANK_CHARGE').val('');
    $('#NARRATION').val('');
    var InvoiceClone = $('#hdnInvoice').val(); 
    var AccountClone = $('#hdnAccount').val(); 
    var CostClone = $('#hdnCostCenter2').val();
    var tds = $('#hdntds').val();
    $('#Invoice').html(InvoiceClone);
    $('#Row_Count1').val('1');
    $('#Account').html(AccountClone);
    $('#Row_Count3').val('1');
    $('#CostCenter').html(CostClone);
    $('#TDS').html(tds);
    $('#Row_Count2').val('1');

    if($("#chk_RcptAccount").is(':checked') == true){
      $('#div_invoice').hide();
      $('#div_account').hide();
      $('.nav-tabs li:eq(2) a').tab('show');
    }else{
    
    $('#div_invoice').show();
    $('#div_account').hide();
    $('.nav-tabs li:eq(0) a').tab('show');
    }
  }
  // else
  // {
  //   $(this).prop('checked', false);
  //   $('#hdnreceiptfor').val('');
  //   $('#div_invoice').hide();
  //   $('#div_account').hide();
  //   $('#div_invoice').removeClass('active');
  //   $('#div_tds').removeClass('active');
  //   $('#Invoice').hide();
  //   $('#Account').hide();
  //   $('#divcust1').hide();
  //   $('#divcust2').hide();
  //   $('#divcust3').hide();
  //   $('#divcust4').hide();
  //   $('#txtcustomer').val('');
  //   $('#CUSTMER_VENDOR_ID').val('');
  //   $('#AMOUNT').val('');
  //   $('#txtcashbk').val('');
  //   $('#CASH_BANK_ID').val('');
  //   $('#RECEIPT_TYPE').val('');
  //   $('#TRANSACTION_DT').val('');
  //   $('#INSTRUMENT_NO').val('');
  //   $('#BANK_CHARGE').val('');
  //   $('#NARRATION').val('');
  //   var InvoiceClone = $('#hdnInvoice').val(); 
  //   var AccountClone = $('#hdnAccount').val(); 
  //   var CostClone = $('#hdnCostCenter2').val();
  //   var tds = $('#hdntds').val();
  //   $('#Invoice').html(InvoiceClone);
  //   $('#Row_Count1').val('1');
  //   $('#Account').html(AccountClone);
  //   $('#Row_Count3').val('1');
  //   $('#CostCenter').html(CostClone);
  //   $('#TDS').html(tds);
  //   $('#Row_Count2').val('1');
  //   $('#Row_Count6').val('1');
  // }
});
$('#chk_Account').on('change',function()
{
  if($(this).is(':checked') == true)
  {
    $('#chk_Vendor').prop('checked', false);
    $('#chk_Customer').prop('checked', false);
    $('#hdnreceiptfor').val('Account');

    $('#chk_RcptAccount').prop('checked', false);
    $('#div_account_amt').hide();
    $('#div_account_amt2').hide();
    $('#AMOUNT').val('');

    // $('#div_invoice').hide();
    // $('#div_account').show();
    // $('#div_account').addClass('active');
    // $('#Account').addClass('active');
    // $('#Invoice').removeClass('active');
    // $('#div_tds').removeClass('active');
    // $('#Invoice').hide();
    // $('#Account').show();

    
    $('#div_invoice').hide();
    $('#div_account').show();
    $('.nav-tabs li:eq(1) a').tab('show');	


    $('#divcust1').hide();
    $('#divcust2').hide();
    $('#divcust3').hide();
    $('#divcust4').hide();
    $('#txtcustomer').val('');
    $('#CUSTMER_VENDOR_ID').val('');
    $('#AMOUNT').val('');
    $('#txtcashbk').val('');
    $('#CASH_BANK_ID').val('');
    $('#RECEIPT_TYPE').val('');
    $('#TRANSACTION_DT').val('');
    $('#INSTRUMENT_NO').val('');
    $('#BANK_CHARGE').val('');
    $('#NARRATION').val('');
    var InvoiceClone = $('#hdnInvoice').val(); 
    var AccountClone = $('#hdnAccount').val(); 
    var CostClone = $('#hdnCostCenter2').val();
    var tds = $('#hdntds').val();
    $('#Invoice').html(InvoiceClone);
    $('#Row_Count1').val('1');
    $('#Account').html(AccountClone);
    $('#Row_Count3').val('1');
    $('#CostCenter').html(CostClone);
    $('#TDS').html(tds);
    $('#Row_Count2').val('1');
    $('#Row_Count6').val('1');
  }
  // else
  // {
  //   $(this).prop('checked', false);
  //   $('#hdnreceiptfor').val('');
  //   $('#div_invoice').hide();
  //   $('#div_account').hide();
  //   $('#div_account').removeClass('active');
  //   $('#Account').removeClass('active');
  //   $('#Invoice').removeClass('active');
  //   $('#div_tds').removeClass('active');
  //   $('#Invoice').hide();
  //   $('#Account').hide();
  //   $('#divcust1').hide();
  //   $('#divcust2').hide();
  //   $('#divcust3').hide();
  //   $('#divcust4').hide();
  //   $('#txtcustomer').val('');
  //   $('#CUSTMER_VENDOR_ID').val('');
  //   $('#AMOUNT').val('');
  //   $('#txtcashbk').val('');
  //   $('#CASH_BANK_ID').val('');
  //   $('#RECEIPT_TYPE').val('');
  //   $('#TRANSACTION_DT').val('');
  //   $('#INSTRUMENT_NO').val('');
  //   $('#BANK_CHARGE').val('');
  //   $('#NARRATION').val('');
  //   var InvoiceClone = $('#hdnInvoice').val(); 
  //   var AccountClone = $('#hdnAccount').val(); 
  //   var CostClone = $('#hdnCostCenter2').val();
  //   var tds = $('#hdntds').val();
  //   $('#Invoice').html(InvoiceClone);
  //   $('#Row_Count1').val('1');
  //   $('#Account').html(AccountClone);
  //   $('#Row_Count3').val('1');
  //   $('#CostCenter').html(CostClone);
  //   $('#TDS').html(tds);    
  //   $('#Row_Count2').val('1');
  //   $('#Row_Count6').val('1');
  // }
});

$('#btnAdd').on('click', function() {
  var viewURL = '{{route("transaction",[$FormId,"add"])}}';
              window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
});

$('#chk_RcptAccount').on('change',function()
{
  if($(this).is(':checked') == true)
  {
    
    // $('#div_invoice').hide();
    // $('#div_account').hide();
    // $('#div_account').removeClass('active');
    // $('#Account').removeClass('active');
    // $('#Invoice').removeClass('active');
    // $('#Invoice').hide();
    // $('#Account').hide();

    
  //   $('#div_invoice').hide();
  //   $('#div_account').hide();
	// $('#TDS').show();
	// $('#TDS').addClass("active");
	// $('#div_invoice').removeClass('active');
   // $('.nav-tabs li:eq(2) a').tab('show');
    $('#divcust4').hide();
    $('#div_account_amt').show();
    $('#div_account_amt2').show();
    $('#AMOUNT').val('');
    $('#txtcashbk').val('');
    $('#CASH_BANK_ID').val('');
    $('#RECEIPT_TYPE').val('');
    $('#TRANSACTION_DT').val('');
    $('#INSTRUMENT_NO').val('');
    $('#BANK_CHARGE').val('');
    $('#NARRATION').val('');
    var InvoiceClone = $('#hdnInvoice').val(); 
    var AccountClone = $('#hdnAccount').val(); 
    var CostClone = $('#hdnCostCenter2').val();
   // var tds = $('#hdntds').val();
    //$('#Invoice').html(InvoiceClone);
    $('#Row_Count1').val('1');
    $('#Account').html(AccountClone);
    $('#Row_Count3').val('1');
    $('#CostCenter').html(CostClone);
   // $('#TDS').html(tds);
    $('#Row_Count2').val('1');
    //$('#Row_Count6').val('1');
    bindTotalValue();

    $('#div_invoice').hide();
    $('#div_account').hide();
    $('.nav-tabs li:eq(2) a').tab('show');

    event.preventDefault();
  }
  else{
    $('#div_account_amt').hide();
    $('#div_account_amt2').hide();
    $('#AMOUNT').val('');
    $('#div_invoice').show();
    $('#div_account').hide();
    $('#Invoice_Tab').click();

    $('#CostCenter').find('.participantRow5').each(function(){
      var rowcount = $(this).closest('table').find('.participantRow5').length;
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      $(this).find('input:checked').prop('checked', false);
      if(rowcount > 1)
      {
        $(this).closest('.participantRow5').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count2').val(rowcount);
      }
    });

    $('#Account').find('.participantRow3').each(function(){
    var rowcount = $(this).closest('table').find('.participantRow3').length;
    $(this).find('input:text').val('');
    $(this).find('input:hidden').val('');
    $(this).find('input:checked').prop('checked', false);
    if(rowcount > 1)
    {
      $(this).closest('.participantRow3').remove();
      rowcount = parseInt(rowcount) - 1;
      $('#Row_Count3').val(rowcount);
    }
  });

  $('#Invoice').find('.participantRow2').each(function(){
    var rowcount = $(this).closest('table').find('.participantRow2').length;
    $(this).find('input:text').val('');
    $(this).find('input:hidden').val('');
    $(this).find('input:checked').prop('checked', false);
    if(rowcount > 1)
    {
      $(this).closest('.participantRow2').remove();
      rowcount = parseInt(rowcount) - 1;
      $('#Row_Count1').val(rowcount);
    }
  });
    
  $('#TDS').find('.participantRow7').each(function(){
    var rowcount = $(this).closest('table').find('.participantRow7').length;
    $(this).find('input:text').val('');
    $(this).find('input:hidden').val('');
    $(this).find('input:checked').prop('checked', false);
    
    if(rowcount > 1){
      $(this).closest('.participantRow7').remove();
      rowcount = parseInt(rowcount) - 1;
      $('#Row_Count6').val(rowcount);
    }
  });

  $("#drpTDS").val('');
  bindTotalValue();
  event.preventDefault();
  }
});

$('#BANK_CHARGE').on('focusout',function(){
  if($(this).val() != '' && $(this).val() != '.00' && $(this).val() > '0.00')
  {
    if(intRegex.test($(this).val())){
      $(this).val($(this).val()+'.00');
    }
  }
    bindTotalValue();
    event.preventDefault();
});

$('#AMOUNT').on('focusout',function(){
  if($(this).val() != '' && $(this).val() != '.00' && $(this).val() > '0.00')
  {
    if(intRegex.test($(this).val())){
      $(this).val($(this).val()+'.00');
    }
  }
    bindTotalValue();
    event.preventDefault();
});


$('#ROUNDOFF_AMT').on('focusout',function(){

if($(this).val() != '' && $(this).val() != '.00' && $(this).val() > '0')
{
  if(intRegex.test($(this).val())){
    $(this).val($(this).val()+'.00');
  }
}
  bindTotalValue();
  event.preventDefault();
});

$('#ROUNDOFF_MODE').on('change',function(){  
  bindTotalValue();
  event.preventDefault();
});




$("#Invoice").on('focusout', "[id*='RECEIPT_AMT_']", function() 
{
  if($(this).val() != '' && $(this).val() != '.00' && $(this).val() > '0.00')
  {
    if(intRegex.test($(this).val())){
      $(this).val($(this).val()+'.00');
    }
    var balanceamt = $(this).parent().parent().find('[id*="BALANCE_DUE_"]').val();
    if(parseFloat($(this).val()) > parseFloat(balanceamt))
    {
      $(this).val('');
      $("#ProceedBtn").focus();
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Receipt Amount cannot be greater than Balance Amt.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }
  }

    bindTotalValue();
    event.preventDefault();
});

$("#Account").on('focusout', "[id*='AMOUNT_']", function() 
{
  if($(this).val() != '' && $(this).val() != '.00' && $(this).val() > '0.00')
  { 
      var amt = $(this).val();
      if($(this).parent().parent().find('[id*="calIGST_"]').val() != '')
      {
        var igst      = $(this).parent().parent().find('[id*="calIGST_"]').val();
        var igstamt   = parseFloat((parseFloat(amt)*parseFloat(igst))/100).toFixed(2);
      }
      else
      {
        var igst      = 0;
        var igstamt   = 0;
      }
      if($(this).parent().parent().find('[id*="calCGST_"]').val() != '')
      {
        var cgst      = $(this).parent().parent().find('[id*="calCGST_"]').val();
        var cgstamt   = parseFloat((parseFloat(amt)*parseFloat(cgst))/100).toFixed(2);
      }
      else
      {
        var cgst      = 0;
        var cgstamt   = 0;
      }
      if($(this).parent().parent().find('[id*="calSGST_"]').val() != '')
      {
        var sgst      = $(this).parent().parent().find('[id*="calSGST_"]').val();
        var sgstamt   = parseFloat((parseFloat(amt)*parseFloat(sgst))/100).toFixed(2);
      }
      else
      {
        var sgst      = 0;
        var sgstamt   = 0;
      }
      
      var taxamt = parseFloat(parseFloat(igstamt) + parseFloat(cgstamt) + parseFloat(sgstamt)).toFixed(2); 
      var totamt = parseFloat(parseFloat(amt) + parseFloat(taxamt)).toFixed(2);
      
    if(intRegex.test($(this).val())){
      $(this).val($(this).val()+'.00');
    }
    if(intRegex.test(totamt)){
      totamt = totamt +'.00';
    }
    if(intRegex.test(igstamt)){
      igstamt = igstamt +'.00';
    }
    if(intRegex.test(cgstamt)){
      cgstamt = cgstamt +'.00';
    }
    if(intRegex.test(sgstamt)){
      sgstamt = sgstamt +'.00';
    }
    if(intRegex.test(igst)){
      igst = igst +'.0000';
    }
    if(intRegex.test(cgst)){
      cgst = cgst +'.0000';
    }
    if(intRegex.test(sgst)){
      sgst = sgst +'.0000';
    }
    $(this).parent().parent().find('[id*="TOTAMT_"]').val(totamt);
    $(this).parent().parent().find('[id*="AMTIGST_"]').val(igstamt);
    $(this).parent().parent().find('[id*="AMTCGST_"]').val(cgstamt);
    $(this).parent().parent().find('[id*="AMTSGST_"]').val(sgstamt);
    $(this).parent().parent().find('[id*="calIGST_"]').val(igst);
    $(this).parent().parent().find('[id*="calCGST_"]').val(cgst);
    $(this).parent().parent().find('[id*="calSGST_"]').val(sgst);
    bindTotalValue();
    event.preventDefault();  
  }  
});

$("#Account").on('focusout', "[id*='calIGST_']", function() 
{
  if($(this).parent().parent().find('[id*="AMOUNT_"]').val() == '')
  {
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Amount cannot be blank.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');
    return false;
  }
  else
  {
    if($(this).val() != '' && $(this).val() != '.0000' && $(this).val() > '0.0000')
    { 
        var amt       = $(this).parent().parent().find('[id*="AMOUNT_"]').val();
        var igst      = $(this).parent().parent().find('[id*="calIGST_"]').val();
        var igstamt   = parseFloat((parseFloat(amt)*parseFloat(igst))/100).toFixed(2);
        
        
          var cgst      = 0;
          var cgstamt   = 0;
        
          var sgst      = 0;
          var sgstamt   = 0;
        
        
        var taxamt = parseFloat(parseFloat(igstamt) + parseFloat(cgstamt) + parseFloat(sgstamt)).toFixed(2); 
        var totamt = parseFloat(parseFloat(amt) + parseFloat(taxamt)).toFixed(2);
        
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000');
      }
      if(intRegex.test(totamt)){
        totamt = totamt +'.00';
      }
      if(intRegex.test(igstamt)){
        igstamt = igstamt +'.00';
      }
      if(intRegex.test(cgstamt)){
        cgstamt = cgstamt +'.00';
      }
      if(intRegex.test(sgstamt)){
        sgstamt = sgstamt +'.00';
      }
      if(intRegex.test(igst)){
        igst = igst +'.0000';
      }
      if(intRegex.test(cgst)){
        cgst = cgst +'.0000';
      }
      if(intRegex.test(sgst)){
        sgst = sgst +'.0000';
      }
      $(this).parent().parent().find('[id*="TOTAMT_"]').val(totamt);
      $(this).parent().parent().find('[id*="AMTIGST_"]').val(igstamt);
      $(this).parent().parent().find('[id*="AMTCGST_"]').val(cgstamt);
      $(this).parent().parent().find('[id*="AMTSGST_"]').val(sgstamt);
      $(this).parent().parent().find('[id*="calCGST_"]').val(cgst);
      $(this).parent().parent().find('[id*="calSGST_"]').val(sgst);
      $(this).parent().parent().find('[id*="AMTCGST_"]').prop('disabled','true');
      $(this).parent().parent().find('[id*="AMTSGST_"]').prop('disabled','true');
      $(this).parent().parent().find('[id*="calCGST_"]').prop('disabled','true');
      $(this).parent().parent().find('[id*="calSGST_"]').prop('disabled','true');
      bindTotalValue();
      event.preventDefault();
    }
    else
    {
      $(this).val('0.0000');
      $(this).parent().parent().find('[id*="AMTIGST_"]').val('0.00');
      $(this).parent().parent().find('[id*="AMTCGST_"]').removeAttr('disabled');
      $(this).parent().parent().find('[id*="AMTSGST_"]').removeAttr('disabled');
      $(this).parent().parent().find('[id*="calCGST_"]').removeAttr('disabled');
      $(this).parent().parent().find('[id*="calSGST_"]').removeAttr('disabled');
      bindTotalValue();
      event.preventDefault();
    }
  }
});

$("#Account").on('focusout', "[id*='calCGST_']", function() 
{
  if($(this).parent().parent().find('[id*="AMOUNT_"]').val() == '')
  {
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Amount cannot be blank.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');
    return false;
  }
  else
  {
    if($(this).val() != '' && $(this).val() != '.0000' && $(this).val() > '0.0000')
    { 
        var amt       = $(this).parent().parent().find('[id*="AMOUNT_"]').val();
        var cgst      = $(this).parent().parent().find('[id*="calCGST_"]').val();
        var cgstamt   = parseFloat((parseFloat(amt)*parseFloat(cgst))/100).toFixed(2);        
        
        var igst      = 0;
        var igstamt   = 0;
        
        if($(this).parent().parent().find('[id*="calSGST_"]').val() != '')
        {
          var sgst      = $(this).parent().parent().find('[id*="calSGST_"]').val();
          var sgstamt   = parseFloat((parseFloat(amt)*parseFloat(sgst))/100).toFixed(2);
        }
        else
        {
          var sgst      = 0;
          var sgstamt   = 0;
        }
        
        var taxamt = parseFloat(parseFloat(igstamt) + parseFloat(cgstamt) + parseFloat(sgstamt)).toFixed(2); 
        var totamt = parseFloat(parseFloat(amt) + parseFloat(taxamt)).toFixed(2);
        
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000');
      }
      if(intRegex.test(totamt)){
        totamt = totamt +'.00';
      }
      if(intRegex.test(igstamt)){
        igstamt = igstamt +'.00';
      }
      if(intRegex.test(cgstamt)){
        cgstamt = cgstamt +'.00';
      }
      if(intRegex.test(sgstamt)){
        sgstamt = sgstamt +'.00';
      }
      if(intRegex.test(igst)){
        igst = igst +'.0000';
      }
      if(intRegex.test(cgst)){
        cgst = cgst +'.0000';
      }
      if(intRegex.test(sgst)){
        sgst = sgst +'.0000';
      }
      $(this).parent().parent().find('[id*="TOTAMT_"]').val(totamt);
      $(this).parent().parent().find('[id*="AMTIGST_"]').val(igstamt);
      $(this).parent().parent().find('[id*="AMTCGST_"]').val(cgstamt);
      $(this).parent().parent().find('[id*="AMTSGST_"]').val(sgstamt);
      $(this).parent().parent().find('[id*="calCGST_"]').val(cgst);
      $(this).parent().parent().find('[id*="calSGST_"]').val(sgst);
      $(this).parent().parent().find('[id*="AMTIGST_"]').prop('disabled','true');
      $(this).parent().parent().find('[id*="calIGST_"]').prop('disabled','true');
      bindTotalValue();
      event.preventDefault();
    }
    else
    {
      $(this).val('0.0000');
      $(this).parent().parent().find('[id*="AMTCGST_"]').val('0.00');
      $(this).parent().parent().find('[id*="AMTIGST_"]').removeAttr('disabled');
      $(this).parent().parent().find('[id*="calIGST_"]').removeAttr('disabled');
      bindTotalValue();
      event.preventDefault();
    }
  }
});

$("#Account").on('focusout', "[id*='calSGST_']", function() 
{
  if($(this).parent().parent().find('[id*="AMOUNT_"]').val() == '')
  {
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Amount cannot be blank.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');
    return false;
  }
  else
  {
    if($(this).val() != '' && $(this).val() != '.0000' && $(this).val() > '0.0000')
    { 
        var amt       = $(this).parent().parent().find('[id*="AMOUNT_"]').val();
        var sgst      = $(this).parent().parent().find('[id*="calSGST_"]').val();
        var sgstamt   = parseFloat((parseFloat(amt)*parseFloat(sgst))/100).toFixed(2);
        
        if($(this).parent().parent().find('[id*="calIGST_"]').val() != '')
        {
          var igst      = $(this).parent().parent().find('[id*="calIGST_"]').val();
          var igstamt   = parseFloat((parseFloat(amt)*parseFloat(igst))/100).toFixed(2);
        }
        else
        {
          var igst      = 0;
          var igstamt   = 0;
        }
        if($(this).parent().parent().find('[id*="calCGST_"]').val() != '')
        {
          var cgst      = $(this).parent().parent().find('[id*="calCGST_"]').val();
          var cgstamt   = parseFloat((parseFloat(amt)*parseFloat(cgst))/100).toFixed(2);
        }
        else
        {
          var cgst      = 0;
          var cgstamt   = 0;
        }
        
        var taxamt = parseFloat(parseFloat(igstamt) + parseFloat(cgstamt) + parseFloat(sgstamt)).toFixed(2); 
        var totamt = parseFloat(parseFloat(amt) + parseFloat(taxamt)).toFixed(2);
        
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.0000');
      }
      if(intRegex.test(totamt)){
        totamt = totamt +'.00';
      }
      if(intRegex.test(igstamt)){
        igstamt = igstamt +'.00';
      }
      if(intRegex.test(cgstamt)){
        cgstamt = cgstamt +'.00';
      }
      if(intRegex.test(sgstamt)){
        sgstamt = sgstamt +'.00';
      }
      if(intRegex.test(igst)){
        igst = igst +'.0000';
      }
      if(intRegex.test(cgst)){
        cgst = cgst +'.0000';
      }
      if(intRegex.test(sgst)){
        sgst = sgst +'.0000';
      }
      $(this).parent().parent().find('[id*="TOTAMT_"]').val(totamt);
      $(this).parent().parent().find('[id*="AMTIGST_"]').val(igstamt);
      $(this).parent().parent().find('[id*="AMTCGST_"]').val(cgstamt);
      $(this).parent().parent().find('[id*="AMTSGST_"]').val(sgstamt);
      $(this).parent().parent().find('[id*="calCGST_"]').val(cgst);
      $(this).parent().parent().find('[id*="calIGST_"]').val(igst);
      $(this).parent().parent().find('[id*="AMTIGST_"]').prop('disabled','true');
      $(this).parent().parent().find('[id*="calIGST_"]').prop('disabled','true');
      bindTotalValue();
      event.preventDefault();
    }
    else
    {
      $(this).val('0.0000');
      $(this).parent().parent().find('[id*="AMTSGST_"]').val('0.00');
      $(this).parent().parent().find('[id*="AMTIGST_"]').removeAttr('disabled');
      $(this).parent().parent().find('[id*="calIGST_"]').removeAttr('disabled');
      bindTotalValue();
      event.preventDefault();
    }
  }
});

$("#Account").on('focusout', "[id*='AMTIGST_']", function() 
{
  if($(this).parent().parent().find('[id*="AMOUNT_"]').val() == '')
  {
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Amount cannot be blank.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');
    return false;
  }
  else
  {
    if($(this).val() != '' && $(this).val() != '.00' && $(this).val() > '0.00')
    { 
        var amt       = $(this).parent().parent().find('[id*="AMOUNT_"]').val();
        var igstamt   = $(this).parent().parent().find('[id*="AMTIGST_"]').val();
        var igst      = parseFloat((parseFloat(igstamt)*100)/parseFloat(amt)).toFixed(4);
        
        if($(this).parent().parent().find('[id*="calCGST_"]').val() != '')
        {
          var cgst      = $(this).parent().parent().find('[id*="calCGST_"]').val();
          var cgstamt   = parseFloat((parseFloat(amt)*parseFloat(cgst))/100).toFixed(2);
        }
        else
        {
          var cgst      = 0;
          var cgstamt   = 0;
        }
        if($(this).parent().parent().find('[id*="calSGST_"]').val() != '')
        {
          var sgst      = $(this).parent().parent().find('[id*="calSGST_"]').val();
          var sgstamt   = parseFloat((parseFloat(amt)*parseFloat(sgst))/100).toFixed(2);
        }
        else
        {
          var sgst      = 0;
          var sgstamt   = 0;
        }
        
        var taxamt = parseFloat(parseFloat(igstamt) + parseFloat(cgstamt) + parseFloat(sgstamt)).toFixed(2); 
        var totamt = parseFloat(parseFloat(amt) + parseFloat(taxamt)).toFixed(2);
        
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00');
      }
      if(intRegex.test(totamt)){
        totamt = totamt +'.00';
      }
      if(intRegex.test(igstamt)){
        igstamt = igstamt +'.00';
      }
      if(intRegex.test(cgstamt)){
        cgstamt = cgstamt +'.00';
      }
      if(intRegex.test(sgstamt)){
        sgstamt = sgstamt +'.00';
      }
      if(intRegex.test(igst)){
        igst = igst +'.0000';
      }
      if(intRegex.test(cgst)){
        cgst = cgst +'.0000';
      }
      if(intRegex.test(sgst)){
        sgst = sgst +'.0000';
      }
      $(this).parent().parent().find('[id*="TOTAMT_"]').val(totamt);
      $(this).parent().parent().find('[id*="AMTCGST_"]').val(cgstamt);
      $(this).parent().parent().find('[id*="AMTSGST_"]').val(sgstamt);
      $(this).parent().parent().find('[id*="calIGST_"]').val(igst);
      $(this).parent().parent().find('[id*="calCGST_"]').val(cgst);
      $(this).parent().parent().find('[id*="calSGST_"]').val(sgst);
      $(this).parent().parent().find('[id*="AMTCGST_"]').prop('disabled','true');
      $(this).parent().parent().find('[id*="AMTSGST_"]').prop('disabled','true');
      $(this).parent().parent().find('[id*="calCGST_"]').prop('disabled','true');
      $(this).parent().parent().find('[id*="calSGST_"]').prop('disabled','true');
      bindTotalValue();
      event.preventDefault();
    }
    else
    {
      $(this).val('0.00');
      $(this).parent().parent().find('[id*="calIGST_"]').val('0.0000');
      $(this).parent().parent().find('[id*="AMTCGST_"]').removeAttr('disabled');
      $(this).parent().parent().find('[id*="AMTSGST_"]').removeAttr('disabled');
      $(this).parent().parent().find('[id*="calCGST_"]').removeAttr('disabled');
      $(this).parent().parent().find('[id*="calSGST_"]').removeAttr('disabled');
      bindTotalValue();
      event.preventDefault();
    }
  }
});

$("#Account").on('focusout', "[id*='AMTCGST_']", function() 
{
  if($(this).parent().parent().find('[id*="AMOUNT_"]').val() == '')
  {
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Amount cannot be blank.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');
    return false;
  }
  else
  {
    if($(this).val() != '' && $(this).val() != '.00' && $(this).val() > '0.00')
    { 
        var amt       = $(this).parent().parent().find('[id*="AMOUNT_"]').val();
        var cgstamt   = $(this).parent().parent().find('[id*="AMTCGST_"]').val();
        var cgst      = parseFloat((parseFloat(cgstamt)*100)/parseFloat(amt)).toFixed(4);       
        
        var igst      = 0;
        var igstamt   = 0;
        
        if($(this).parent().parent().find('[id*="calSGST_"]').val() != '')
        {
          var sgst      = $(this).parent().parent().find('[id*="calSGST_"]').val();
          var sgstamt   = parseFloat((parseFloat(amt)*parseFloat(sgst))/100).toFixed(2);
        }
        else
        {
          var sgst      = 0;
          var sgstamt   = 0;
        }
        
        var taxamt = parseFloat(parseFloat(igstamt) + parseFloat(cgstamt) + parseFloat(sgstamt)).toFixed(2); 
        var totamt = parseFloat(parseFloat(amt) + parseFloat(taxamt)).toFixed(2);
        
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00');
      }
      if(intRegex.test(totamt)){
        totamt = totamt +'.00';
      }
      if(intRegex.test(igstamt)){
        igstamt = igstamt +'.00';
      }
      if(intRegex.test(cgstamt)){
        cgstamt = cgstamt +'.00';
      }
      if(intRegex.test(sgstamt)){
        sgstamt = sgstamt +'.00';
      }
      if(intRegex.test(igst)){
        igst = igst +'.0000';
      }
      if(intRegex.test(cgst)){
        cgst = cgst +'.0000';
      }
      if(intRegex.test(sgst)){
        sgst = sgst +'.0000';
      }
      $(this).parent().parent().find('[id*="TOTAMT_"]').val(totamt);
      $(this).parent().parent().find('[id*="AMTCGST_"]').val(cgstamt);
      $(this).parent().parent().find('[id*="AMTSGST_"]').val(sgstamt);
      $(this).parent().parent().find('[id*="calIGST_"]').val(igst);
      $(this).parent().parent().find('[id*="calCGST_"]').val(cgst);
      $(this).parent().parent().find('[id*="calSGST_"]').val(sgst);
      $(this).parent().parent().find('[id*="AMTIGST_"]').prop('disabled','true');
      $(this).parent().parent().find('[id*="calIGST_"]').prop('disabled','true');
      bindTotalValue();
      event.preventDefault();
    }
    else
    {
      $(this).val('0.00');
      $(this).parent().parent().find('[id*="calIGST_"]').val('0.0000');
      $(this).parent().parent().find('[id*="AMTIGST_"]').removeAttr('disabled');
      $(this).parent().parent().find('[id*="calIGST_"]').removeAttr('disabled');
      bindTotalValue();
      event.preventDefault();
    }
  }
});

$("#Account").on('focusout', "[id*='AMTSGST_']", function() 
{
  if($(this).parent().parent().find('[id*="AMOUNT_"]').val() == '')
  {
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Amount cannot be blank.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk1');
    return false;
  }
  else
  {
    if($(this).val() != '' && $(this).val() != '.00' && $(this).val() > '0.00')
    { 
        var amt       = $(this).parent().parent().find('[id*="AMOUNT_"]').val();
        var sgstamt   = $(this).parent().parent().find('[id*="AMTSGST_"]').val();
        var sgst      = parseFloat((parseFloat(sgstamt)*100)/parseFloat(amt)).toFixed(4);       
        
        var igst      = 0;
        var igstamt   = 0;
        
        if($(this).parent().parent().find('[id*="calCGST_"]').val() != '')
        {
          var cgst      = $(this).parent().parent().find('[id*="calCGST_"]').val();
          var cgstamt   = parseFloat((parseFloat(amt)*parseFloat(cgst))/100).toFixed(2);
        }
        else
        {
          var cgst      = 0;
          var cgstamt   = 0;
        }
        
        var taxamt = parseFloat(parseFloat(igstamt) + parseFloat(cgstamt) + parseFloat(sgstamt)).toFixed(2); 
        var totamt = parseFloat(parseFloat(amt) + parseFloat(taxamt)).toFixed(2);
        
      if(intRegex.test($(this).val())){
        $(this).val($(this).val()+'.00');
      }
      if(intRegex.test(totamt)){
        totamt = totamt +'.00';
      }
      if(intRegex.test(igstamt)){
        igstamt = igstamt +'.00';
      }
      if(intRegex.test(cgstamt)){
        cgstamt = cgstamt +'.00';
      }
      if(intRegex.test(sgstamt)){
        sgstamt = sgstamt +'.00';
      }
      if(intRegex.test(igst)){
        igst = igst +'.0000';
      }
      if(intRegex.test(cgst)){
        cgst = cgst +'.0000';
      }
      if(intRegex.test(sgst)){
        sgst = sgst +'.0000';
      }
      $(this).parent().parent().find('[id*="TOTAMT_"]').val(totamt);
      $(this).parent().parent().find('[id*="AMTCGST_"]').val(cgstamt);
      $(this).parent().parent().find('[id*="AMTSGST_"]').val(sgstamt);
      $(this).parent().parent().find('[id*="calIGST_"]').val(igst);
      $(this).parent().parent().find('[id*="calCGST_"]').val(cgst);
      $(this).parent().parent().find('[id*="calSGST_"]').val(sgst);
      $(this).parent().parent().find('[id*="AMTIGST_"]').prop('disabled','true');
      $(this).parent().parent().find('[id*="calIGST_"]').prop('disabled','true');
      bindTotalValue();
      event.preventDefault();
    }
    else
    {
      $(this).val('0.00');
      $(this).parent().parent().find('[id*="calIGST_"]').val('0.0000');
      $(this).parent().parent().find('[id*="AMTIGST_"]').removeAttr('disabled');
      $(this).parent().parent().find('[id*="calIGST_"]').removeAttr('disabled');
      bindTotalValue();
      event.preventDefault();
    }
  }
});

//delete row
$("#Invoice").on('click', '.dinvoice', function() {
    var rowCount = $(this).closest('table').find('.participantRow2').length;
    if (rowCount > 1) {
    $(this).closest('.participantRow2').remove();     
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

    bindTotalValue(); 
    event.preventDefault();
});
$("#Account").on('click', '.daccount', function() {
    var rowCount = $(this).closest('table').find('.participantRow3').length;
    if (rowCount > 1) {
    $(this).closest('.participantRow3').remove();     
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
$("#CostTable2").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('.participantRow9').length;
    if (rowCount > 1) {
    $(this).closest('.participantRow9').remove();     
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
$("#Invoice").on('click', '.ainvoice', function() {
  var $tr = $(this).closest('table');
  var allTrs = $tr.find('.participantRow2').last();
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
  $clone.find('input:hidden').val('');
  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count1').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count1').val(rowCount1);
  $clone.find('.dinvoice').removeAttr('disabled'); 
  event.preventDefault();
});

$("#Account").on('click', '.aaccount', function() {
  var $tr = $(this).closest('table');
  var allTrs = $tr.find('.participantRow3').last();
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
  $clone.find('input:hidden').val('');
  $tr.closest('table').append($clone);         
  var rowCount5 = $('#Row_Count3').val();
  rowCount5 = parseInt(rowCount5)+1;
  $('#Row_Count3').val(rowCount5);
  $clone.find('.daccount').removeAttr('disabled'); 
  event.preventDefault();
});

$("#CostTable2").on('click', '.add', function() {
  var $tr = $(this).closest('table');
  var allTrs = $tr.find('.participantRow9').last();
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

  $clone.find('[id*="hdnAMT_"]').val('');
  $clone.find('[id*="hdnCCID_"]').val('');
  $clone.find('[id*="CostCenter_"]').val('');
  $tr.closest('table').append($clone);
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
    $("#RECEIPT_NO").focus();
}//fnUndoNo

});
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

  $("#btnSave").on("submit", function( event ) {

    if ($("#frm_trn_rcpt").valid()) {
        // Do something
        alert( "Handler for .submit() called." );
        event.preventDefault();
    }
});


    $('#frm_trn_rcpt1').bootstrapValidator({       
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
             $("#frm_trn_rcpt").submit();
        }
    });
});



function validateForm(){
 
 $("#FocusId").val('');
 var RECEIPT_NO          =   $.trim($("#RECEIPT_NO").val());
 var RECEIPT_DT          =   $.trim($("#RECEIPT_DT").val());
 var hdnreceiptfor       =   $.trim($("#hdnreceiptfor").val());
 var CUSTMER_VENDOR_ID   =   $.trim($("#CUSTMER_VENDOR_ID").val());
 var CASH_BANK_ID        =   $.trim($("#CASH_BANK_ID").val());
 var TRANSACTION_DT      =   $.trim($("#TRANSACTION_DT").val());
 var INSTRUMENT_TYPE     =   $.trim($("#INSTRUMENT_TYPE").val());
 var INSTRUMENT_NO       =   $.trim($("#INSTRUMENT_NO").val());
 var BANK_CASH_ID        =   $.trim($("#BANK_CASH_ID").val());
 var RECEIPT_TYPE        =   $.trim($("#RECEIPT_TYPE").val());
 var AMOUNT              =   $.trim($("#AMOUNT").val());
 var PDC_DT              =   $.trim($("#PDC_DT").val());
 var CLEAR_PDC_DT        =   $.trim($("#CLEAR_PDC_DT").val());


 var TotalAmount = $("#tot_amt1").val();

 if(TotalAmount < '0.00' || TotalAmount < '0')
 {
     $("#FocusId").val($("#RECEIPT_NO"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Total Receipt Amount cannot be less than Zero.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }


 if(RECEIPT_NO ===""){
     $("#FocusId").val($("#RECEIPT_NO"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please enter value in Receipt Number.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(RECEIPT_DT ===""){
    $("#FocusId").val($("#RECEIPT_DT"));
     $("#RECEIPT_DT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select Receipt Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }  
 else if(hdnreceiptfor ===""){
     $("#FocusId").val($("#hdnreceiptfor"));
     $("#hdnreceiptfor").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select One Option from Vendor / Customer / Invoice for Receipt.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID ===""){
     $("#FocusId").val($("#CUSTMER_VENDOR_ID"));
     $("#CUSTMER_VENDOR_ID").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Vendor.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID ===""){
     $("#FocusId").val($("#CUSTMER_VENDOR_ID"));
     $("#CUSTMER_VENDOR_ID").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Customer.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }

 else if($('#PDC_STATUS').is(':checked') == true && PDC_DT ===""){
     $("#FocusId").val($("PDC_DT"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select PDC Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 

 else if($('#CLEAR_PDC_STATUS').is(':checked') == true && CLEAR_PDC_DT ===""){
     $("#FocusId").val($("CLEAR_PDC_DT"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Clear PDC Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && CASH_BANK_ID == ""){
     $("#FocusId").val($("#CASH_BANK_ID"));
     $("#CASH_BANK_ID").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Cash / Bank Account.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && CASH_BANK_ID != "" && TRANSACTION_DT ===""){
     $("#FocusId").val($("#TRANSACTION_DT"));
     $("#TRANSACTION_DT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Transaction Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 

 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && RECEIPT_TYPE == "Bank" && CASH_BANK_ID != "" 
        && TRANSACTION_DT !="" &&  INSTRUMENT_TYPE===""){
     $("#FocusId").val($("#INSTRUMENT_TYPE"));
     $("#INSTRUMENT_TYPE").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Instrument Type');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && RECEIPT_TYPE == "Bank" && CASH_BANK_ID != "" 
        && TRANSACTION_DT !="" &&  INSTRUMENT_TYPE !="" && INSTRUMENT_NO===""){
     $("#FocusId").val($("#INSTRUMENT_NO"));
     $("#INSTRUMENT_NO").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Instrument Number');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && CASH_BANK_ID == ""){
     $("#FocusId").val($("#CASH_BANK_ID"));
     $("#CASH_BANK_ID").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Cash / Bank Account.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && CASH_BANK_ID != "" && TRANSACTION_DT ===""){
     $("#FocusId").val($("#TRANSACTION_DT"));
     $("#TRANSACTION_DT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Transaction Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 } 
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && RECEIPT_TYPE == "Bank" && CASH_BANK_ID != "" 
        && TRANSACTION_DT !="" &&  INSTRUMENT_TYPE===""){
     $("#FocusId").val($("#INSTRUMENT_TYPE"));
     $("#INSTRUMENT_TYPE").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Instrument Type');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && RECEIPT_TYPE == "Bank" && CASH_BANK_ID != "" 
        && TRANSACTION_DT !="" &&  INSTRUMENT_TYPE !="" && INSTRUMENT_NO===""){
     $("#FocusId").val($("#INSTRUMENT_NO"));
     $("#INSTRUMENT_NO").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Instrument Number');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_RcptAccount').is(':checked') == true
        && RECEIPT_TYPE == "Bank" && CASH_BANK_ID != "" && TRANSACTION_DT !="" &&  INSTRUMENT_TYPE !="" && INSTRUMENT_NO===""){
     $("#FocusId").val($("#INSTRUMENT_NO"));
     $("#INSTRUMENT_NO").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Instrument Number');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_RcptAccount').is(':checked') == true
        && RECEIPT_TYPE == "Bank" && CASH_BANK_ID != "" && TRANSACTION_DT !="" &&  INSTRUMENT_TYPE ===""){
     $("#FocusId").val($("#INSTRUMENT_TYPE"));
     $("#INSTRUMENT_TYPE").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Instrument Type');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_RcptAccount').is(':checked') == true
        && CASH_BANK_ID != "" && TRANSACTION_DT ===""){
     $("#FocusId").val($("#TRANSACTION_DT"));
     $("#TRANSACTION_DT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Transaction Date');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_RcptAccount').is(':checked') == true
        && CASH_BANK_ID ===""){
     $("#FocusId").val($("#CASH_BANK_ID"));
     $("#CASH_BANK_ID").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Cash / Bank Account');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_RcptAccount').is(':checked') == true
        && AMOUNT ===""){
     $("#FocusId").val($("#AMOUNT"));
     $("#AMOUNT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Amount for Receipt on Account');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_RcptAccount').is(':checked') == true
        && AMOUNT ==="0.00"){
     $("#FocusId").val($("#AMOUNT"));
     $("#AMOUNT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Amount for Receipt on Account');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_RcptAccount').is(':checked') == true
        && AMOUNT ===".00"){
     $("#FocusId").val($("#AMOUNT"));
     $("#AMOUNT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Amount for Receipt on Account');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_RcptAccount').is(':checked') == true
        && RECEIPT_TYPE == "Bank" && CASH_BANK_ID != "" && TRANSACTION_DT !="" &&  INSTRUMENT_TYPE !="" && INSTRUMENT_NO===""){
     $("#FocusId").val($("#INSTRUMENT_NO"));
     $("#INSTRUMENT_NO").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Instrument Number');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_RcptAccount').is(':checked') == true
        && RECEIPT_TYPE == "Bank" && CASH_BANK_ID != "" && TRANSACTION_DT !="" &&  INSTRUMENT_TYPE ===""){
     $("#FocusId").val($("#INSTRUMENT_TYPE"));
     $("#INSTRUMENT_TYPE").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Instrument Type');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_RcptAccount').is(':checked') == true
        && CASH_BANK_ID != "" && TRANSACTION_DT ===""){
     $("#FocusId").val($("#TRANSACTION_DT"));
     $("#TRANSACTION_DT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Transaction Date');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_RcptAccount').is(':checked') == true
        && CASH_BANK_ID ===""){
     $("#FocusId").val($("#CASH_BANK_ID"));
     $("#CASH_BANK_ID").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Select Cash / Bank Account');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_RcptAccount').is(':checked') == true
        && AMOUNT ===""){
     $("#FocusId").val($("#AMOUNT"));
     $("#AMOUNT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Amount for Receipt on Account');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_RcptAccount').is(':checked') == true
        && AMOUNT ==="0.00"){
     $("#FocusId").val($("#AMOUNT"));
     $("#AMOUNT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Amount for Receipt on Account');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_RcptAccount').is(':checked') == true
        && AMOUNT ===".00"){
     $("#FocusId").val($("#AMOUNT"));
     $("#AMOUNT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Amount for Receipt on Account');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Account').is(':checked') == true && BANK_CASH_ID ==="")
 {
     $("#FocusId").val($("#txtbnkcsh"));
     $("#txtbnkcsh").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select Bank / Cash Account in Account Tab');
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

        if($('#chk_Vendor').is(':checked') == true && $('#chk_RcptAccount').is(':checked') == false)
        {
          $('#example2').find('.participantRow2').each(function()
          {    
              if($.trim($(this).find("[id*=txtDoc_NO_]").val())!="")
              {
                    allblank.push('true');
                    if($.trim($(this).find("[id*=RECEIPT_AMT_]").val())!="")
                    {
                          allblank2.push('true');                          
                    }
                    else
                    {
                          allblank2.push('false');
                    }
              }
              else
              {
                    allblank.push('false');
              }
          });
        }
        if($('#chk_Customer').is(':checked') == true && $('#chk_RcptAccount').is(':checked') == false)
        {
          $('#example2').find('.participantRow2').each(function()
          {    
              if($.trim($(this).find("[id*=txtDoc_NO_]").val())!="")
              {
                    allblank.push('true');
                    if($.trim($(this).find("[id*=RECEIPT_AMT_]").val())!="")
                    {
                          allblank2.push('true');                          
                    }
                    else
                    {
                          allblank2.push('false');
                    }
              }
              else
              {
                    allblank.push('false');
              }
          });
        }

        
        if($('#chk_Account').is(':checked') == true && BANK_CASH_ID !="")
        {
            $('#example3').find('.participantRow3').each(function(){
              if($.trim($(this).find("[id*=popupAccount_]").val())!="")
                {
                    allblank3.push('true');
                        if($.trim($(this).find("[id*=AMOUNT_]").val())!="" && $.trim($(this).find("[id*=AMOUNT_]").val())!="0.00"
                           && $.trim($(this).find("[id*=AMOUNT_]").val())!=".00")
                           {
                              allblank4.push('true');                               
                           } 
                           else
                           {
                              allblank4.push('false');
                           }
                }
                else
                {
                    allblank3.push('false');
                } 
            });
        }

    }
        
        if(jQuery.inArray("false", allblank) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Document in Invoice Details Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        else if(jQuery.inArray("false", allblank2) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please enter Receipt Amount in Invoice Details Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        else if(jQuery.inArray("false", allblank3) !== -1){
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please select Account Code in Account Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          } 
        else if(jQuery.inArray("false", allblank4) !== -1){
          $("#FocusId").val($("#tot_amt"));
          $("#ProceedBtn").focus();
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please enter Amount in Account Tab.');
          $("#alert").modal('show');
          $("#OkBtn1").focus();
          return false;
          }
          else if(checkPeriodClosing('{{$FormId}}',$("#RECEIPT_DT").val(),0) ==0){
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
                $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                $("#YesBtn").focus();
                $("#OkBtn").hide();
                highlighFocusBtn('activeYes');
          }

}

$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

$("#btnSave" ).click(function() {
    var formReqData = $("#frm_trn_rcpt");
    if(formReqData.valid()){
      validateForm();
    }
});

window.fnSaveData = function (){

//validate and save data
event.preventDefault();

     var trnseForm = $("#frm_trn_rcpt");
    var formData = trnseForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$("#btnSave").hide(); 
$(".buttonload").show(); 
$("#btnApprove").prop("disabled", true);
$.ajax({
    url:'{{ route("transaction",[$FormId,"save"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $(".buttonload").hide(); 
      $("#btnSave").show();   
      $("#btnApprove").prop("disabled", false);
       
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
        $("#btnSave").show();   
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
  //console.log(all_location_id); 
 
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





  

//Round of GL Starts here
let GL = "#GeneralLedgerTable2";
      let GL2 = "#GeneralLedger";
      let GLheaders = document.querySelectorAll(GL2 + " th");

      // Sort the table element when clicking on the table headers
      GLheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(GL, ".clsspid_reasoncode1", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function GLCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("GLcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("GeneralLedgerTable2");
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

  function GLNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("glnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("GeneralLedgerTable2");
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


  $("#GL_closePopup1").click(function(event){
        $("#GL_popup").hide();
      });

  function bindGLEvents(){
      $(".clsspid_gl").click(function(){       

        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc");
        var texcode =   $("#txt"+fieldid+"").data("code"); 
        $('#txtGLpopup').val(texcode);
        $('#GLID_REF_ROUNDOFF').val(txtval); 
        getBalance(txtval,'BALANCE_ROUNDGL','BALANCE_ROUNDGL_SHOW');    
        $("#GL_popup").hide();   
        $("#GLcodesearch").val(''); 
        $("#glnamesearch").val('');            
        event.preventDefault();
      });
  }

  $('#txtGLpopup').on('click',function(event){     
                $("#GLresult").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  });
                  $("#item_seach").show();
                  $.ajax({
                      url:'{{route("transaction",[302,"get_gl_detail"])}}',
                      type:'POST',
                      data:{},
                      success:function(data) {                                
                        $("#item_seach").hide();
                        $("#GLresult").html(data);   
                        showSelectedCheck($("#GLID_REF_ROUNDOFF").val(),"getgl");
                        bindGLEvents();                                        
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#GLresult").html('');                        
                      },
                  }); 

                  showSelectedCheck($("#REASONCODE1_REF").val(),"getgl");
                  $("#GL_popup").show();         
    });



    function getTaxStatus_customer(customid){

var TaxStatus = $.ajax({type: 'POST',url:'{{route("transaction",[302,"getTaxStatus_customer"])}}',async: false,dataType: 'json',data: {id:customid},done: function(response) {return response;}}).responseText;
  
if(TaxStatus =="1"){
  $(".ExceptionalGST").show();
  $("#EXE_GST").prop('checked', true);
}
else{
  $(".ExceptionalGST").hide();
  $("#EXE_GST").prop('checked', false);
}  

}
    function getTaxStatus(customid){

var TaxStatus = $.ajax({type: 'POST',url:'{{route("transaction",[302,"getTaxStatus"])}}',async: false,dataType: 'json',data: {id:customid},done: function(response) {return response;}}).responseText;
  
if(TaxStatus =="1"){
  $(".ExceptionalGST").show();
  $("#EXE_GST").prop('checked', true);
}
else{
  $(".ExceptionalGST").hide();
  $("#EXE_GST").prop('checked', false);
}  

}


//--------------------TDS SECTION STARTS HERE---------------------------

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
    //bindGSTCalTemplate();
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
     // bindGSTCalTemplate();
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
      //bindGSTCalTemplate();
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


function getExceptionalGst(){
  if($("#EXE_GST").is(":checked") == false){
    $('#TotalValue').val('0.00');    
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



  }
  bindTotalValue();

}

//--------------------TDS SECTION ENDS HERE-----------------------------


    function bindTotalValue()
    {
      var totalvalue = 0.00;
      var totalvalue2 = 0.00;
      var tvalue = 0.00;
      var tvalue2 = 0.00;


      var CommonValue = $('#hdnreceiptfor').val();     
     

      if($('#hdnreceiptfor').val() != 'Account')
      {
        $('#Invoice').find('.participantRow2').each(function()
        {
            if($(this).find('[id*="RECEIPT_AMT_"]').val() != '')
            {
              tvalue = $(this).find('[id*="RECEIPT_AMT_"]').val();
              if($(this).find('[id*="Doc_Type_"]').val() == 'PURCHASE_INVOICE' 
                  || $(this).find('[id*="Doc_Type_"]').val() == 'SERVICE_PURCHASE_INVOICE'
                  || $(this).find('[id*="Doc_Type_"]').val() == 'IMPORT_PURCHASE_INVOICE'
                  || $(this).find('[id*="Doc_Type_"]').val() == 'AP_CREDIT_NOTE'
                  || $(this).find('[id*="Doc_Type_"]').val() == 'AP_INVOICE'
                  || $(this).find('[id*="Doc_Type_"]').val() == 'SALES_INVOICE'
                  || $(this).find('[id*="Doc_Type_"]').val() == 'AR_DEBIT_NOTE'
				  || $(this).find('[id*="Doc_Type_"]').val() == 'MANUAL_JOURNAL_DEBIT'
                )
              {
                totalvalue = parseFloat(parseFloat(totalvalue) + parseFloat(tvalue)).toFixed(2);
              }
              else if($(this).find('[id*="Doc_Type_"]').val() == 'PURCHASE_RETURN' 
                  || $(this).find('[id*="Doc_Type_"]').val() == 'AP_DEBIT_NOTE'
                  || $(this).find('[id*="Doc_Type_"]').val() == 'DEBIT_NOTE_STOCK'
                  || $(this).find('[id*="Doc_Type_"]').val() == 'SALES_RETURN'
                  || $(this).find('[id*="Doc_Type_"]').val() == 'AR_CREDIT_NOTE'
                  || $(this).find('[id*="Doc_Type_"]').val() == 'CREDIT_NOTE_STOCK'
				  || $(this).find('[id*="Doc_Type_"]').val() == 'MANUAL_JOURNAL_CREDIT'
                )
              {
                totalvalue = parseFloat(parseFloat(totalvalue) - parseFloat(tvalue)).toFixed(2);
              }
              else 
              {
              totalvalue = parseFloat(parseFloat(totalvalue) + parseFloat(tvalue)).toFixed(2);
              }
            }
        });
        if( $('#BANK_CHARGE').val() != '')    
        {
          totalvalue = parseFloat(parseFloat(totalvalue) + parseFloat($('#BANK_CHARGE').val())).toFixed(2);
        } 
        if( $('#AMOUNT').val() != '')    
        {
          totalvalue = parseFloat(parseFloat(totalvalue) + parseFloat($('#AMOUNT').val())).toFixed(2);
        }  

        if($('#ROUNDOFF_MODE :selected').val()=='Negative' && $("#ROUNDOFF_AMT").val() !=''){
          totalvalue = parseFloat(parseFloat(totalvalue) + parseFloat($('#ROUNDOFF_AMT').val())).toFixed(2);
        }else if($('#ROUNDOFF_MODE :selected').val()=='Positive' && $("#ROUNDOFF_AMT").val() !=''){
          totalvalue = parseFloat(parseFloat(totalvalue) - parseFloat($('#ROUNDOFF_AMT').val())).toFixed(2);
        }


        if(CommonValue=='Vendor'){
        if($('#drpTDS').val() == 'Yes'){
    $('#TDS').find('.participantRow7').each(function(){
      if($(this).find('[id*="TOT_TD_AMT"]').val() != '' && $(this).find('[id*="TOT_TD_AMT"]').val() != '.00')
      {

        tttdsamt21 = $(this).find('[id*="TOT_TD_AMT"]').val();
        totalvalue = parseFloat(parseFloat(totalvalue)-parseFloat(tttdsamt21)).toFixed(2);
      }
    });
  }
  }else if(CommonValue=='Customer'){
    if($('#drpTDS').val() == 'Yes'){
    $('#TDS').find('.participantRow7').each(function(){
      if($(this).find('[id*="TOT_TD_AMT"]').val() != '' && $(this).find('[id*="TOT_TD_AMT"]').val() != '.00')
      {

        tttdsamt21 = $(this).find('[id*="TOT_TD_AMT"]').val();
        totalvalue = parseFloat(parseFloat(totalvalue)+parseFloat(tttdsamt21)).toFixed(2);
      }
    });
  }
  }

       
        $('#tot_amt1').val(totalvalue);
        MultiCurrency_Conversion('tot_amt1'); 
      }
      else
      {
        $('#Account').find('.participantRow3').each(function()
        {
          tvalue2 = $(this).find('[id*="TOTAMT_"]').val();    
          totalvalue2 = parseFloat(parseFloat(totalvalue2) + parseFloat(tvalue2)).toFixed(2);      
        });


        if($('#ROUNDOFF_MODE :selected').val()=='Negative' && $("#ROUNDOFF_AMT").val() !=''){
          totalvalue2 = parseFloat(parseFloat(totalvalue2) + parseFloat($('#ROUNDOFF_AMT').val())).toFixed(2);
        }else if($('#ROUNDOFF_MODE :selected').val()=='Positive' && $("#ROUNDOFF_AMT").val() !=''){
          totalvalue2 = parseFloat(parseFloat(totalvalue2) - parseFloat($('#ROUNDOFF_AMT').val())).toFixed(2);
        }

        if(CommonValue=='Vendor'){
        if($('#drpTDS').val() == 'Yes'){
    $('#TDS').find('.participantRow7').each(function(){
      if($(this).find('[id*="TOT_TD_AMT"]').val() != '' && $(this).find('[id*="TOT_TD_AMT"]').val() != '.00')
      {

        tttdsamt21 = $(this).find('[id*="TOT_TD_AMT"]').val();
        totalvalue2 = parseFloat(parseFloat(totalvalue2)-parseFloat(tttdsamt21)).toFixed(2);
      }
    });
  }
        }else  if(CommonValue=='Customer'){
          if($('#drpTDS').val() == 'Yes'){
    $('#TDS').find('.participantRow7').each(function(){
      if($(this).find('[id*="TOT_TD_AMT"]').val() != '' && $(this).find('[id*="TOT_TD_AMT"]').val() != '.00')
      {

        tttdsamt21 = $(this).find('[id*="TOT_TD_AMT"]').val();
        totalvalue2 = parseFloat(parseFloat(totalvalue2)+parseFloat(tttdsamt21)).toFixed(2);
      }
    });
  }
        }


        $('#tot_amt1').val(totalvalue2);
        MultiCurrency_Conversion('tot_amt1'); 
      }

      calculateBankAmount();

  }



function calculateBankAmount(){
  var CommonValue         = $('#hdnreceiptfor').val(); 
  var ROUNDOFF_MODE       = $('#ROUNDOFF_MODE').val(); 
  var ROUNDOFF_AMT        = $('#ROUNDOFF_AMT').val(); 

  var TOTAL_ROUNDOFF_AMT  = ROUNDOFF_AMT !=''?parseFloat(ROUNDOFF_AMT):0;
  var TOTAL_ACCOUNT_AMT   = 0;
  var TOTAL_TDS_AMT       = 0;
  var TOTAL_BANK_AMOUNT   = 0;

  if(CommonValue=='Account'){

    $('#Account').find('.participantRow3').each(function(){
      if($(this).find('[id*="TOTAMT_"]').val() != '' && $(this).find('[id*="TOTAMT_"]').val() != '.00'){
        var TOTAMT        = $(this).find('[id*="TOTAMT_"]').val();  
        TOTAL_ACCOUNT_AMT = parseFloat(TOTAL_ACCOUNT_AMT)+parseFloat(TOTAMT);  
      }
    });

    if($('#drpTDS').val() == 'Yes'){
      $('#TDS').find('.participantRow7').each(function(){
        if($(this).find('[id*="TOT_TD_AMT"]').val() != '' && $(this).find('[id*="TOT_TD_AMT"]').val() != '.00'){
          var TOT_TD_AMT  = $(this).find('[id*="TOT_TD_AMT"]').val();
          TOTAL_TDS_AMT   = parseFloat(TOTAL_TDS_AMT)+parseFloat(TOT_TD_AMT);
        }
      });
    }
  
    if(ROUNDOFF_MODE ==='Positive'){
      var TOTAL_BANK_AMOUNT = parseFloat((parseFloat(TOTAL_ACCOUNT_AMT)-parseFloat(TOTAL_TDS_AMT))-TOTAL_ROUNDOFF_AMT).toFixed(2);
    }
    else if(ROUNDOFF_MODE ==='Negative'){
      var TOTAL_BANK_AMOUNT = parseFloat((parseFloat(TOTAL_ACCOUNT_AMT)-parseFloat(TOTAL_TDS_AMT))+TOTAL_ROUNDOFF_AMT).toFixed(2);
    }
    else{
      var TOTAL_BANK_AMOUNT = parseFloat('0.00').toFixed(2);
    }

  }

  $('#BANK_AMOUNT').val(TOTAL_BANK_AMOUNT); 
  getTotalRowValue();
}

function getTotalRowValue(){

  var AMOUNT  = 0;
  var AMTIGST = 0;
  var AMTCGST = 0;
  var AMTSGST = 0;
  var TOTAMT  = 0;

  $('#Account').find('.participantRow3').each(function(){
    AMOUNT  = $(this).find('[id*="AMOUNT"]').val() > 0?AMOUNT+parseFloat($(this).find('[id*="AMOUNT"]').val()):AMOUNT;
    AMTIGST = $(this).find('[id*="AMTIGST"]').val() > 0?AMTIGST+parseFloat($(this).find('[id*="AMTIGST"]').val()):AMTIGST;
    AMTCGST = $(this).find('[id*="AMTCGST"]').val() > 0?AMTCGST+parseFloat($(this).find('[id*="AMTCGST"]').val()):AMTCGST;
    AMTSGST = $(this).find('[id*="AMTSGST"]').val() > 0?AMTSGST+parseFloat($(this).find('[id*="AMTSGST"]').val()):AMTSGST;
    TOTAMT  = $(this).find('[id*="TOTAMT"]').val() > 0?TOTAMT+parseFloat($(this).find('[id*="TOTAMT"]').val()):TOTAMT;
  });

  AMOUNT  = AMOUNT > 0?parseFloat(AMOUNT).toFixed(2):'';
  AMTIGST = AMTIGST > 0?parseFloat(AMTIGST).toFixed(2):'';
  AMTCGST = AMTCGST > 0?parseFloat(AMTCGST).toFixed(2):'';
  AMTSGST = AMTSGST > 0?parseFloat(AMTSGST).toFixed(2):'';
  TOTAMT  = TOTAMT > 0?parseFloat(TOTAMT).toFixed(2):'';

  $("#AMOUNT_TOTAL").text(AMOUNT);
  $("#AMTIGST_TOTAL").text(AMTIGST);
  $("#AMTCGST_TOTAL").text(AMTCGST);
  $("#AMTSGST_TOTAL").text(AMTSGST);
  $("#TOTAMT_TOTAL").text(TOTAMT);
}


function getBalance(bankid,fieldid,fieldidshow){
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  var TaxStatus = $.ajax({type: 'POST',
  url:'{{route("transaction",[301,"getBalance"])}}',
  async: false,
  dataType: 'json',
  data: {id:bankid},
  done: function(response) {return response;}}).responseText;
  var TaxStatus=parseFloat(TaxStatus).toFixed(2);
  if(TaxStatus == '' || TaxStatus==0){
  $("#"+fieldid).text('Balance 0.00');
  $("#"+fieldidshow).text('Balance 0.00');
}
else if(TaxStatus > 0 ){
  $("#"+fieldid).text('Balance '+TaxStatus);
  $("#"+fieldidshow).text('Balance '+Math.abs(TaxStatus).toFixed(2)+' Dr');
}else if(TaxStatus < 0 ){
  $("#"+fieldid).text('Balance '+TaxStatus);
  $("#"+fieldidshow).text('Balance '+Math.abs(TaxStatus).toFixed(2)+' Cr');
}
}



function getBalanceGrid(bankid,fieldid){
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  var TaxStatus = $.ajax({type: 'POST',
  url:'{{route("transaction",[301,"getBalance"])}}',
  async: false,
  dataType: 'json',
  data: {id:bankid},
  done: function(response) {return response;}}).responseText;
  var TaxStatus=parseFloat(TaxStatus).toFixed(2);
  if(TaxStatus == '' || TaxStatus==0){
  $("#"+fieldid).val('0.00');

}
else if(TaxStatus > 0 ){
  $("#"+fieldid).val(Math.abs(TaxStatus).toFixed(2)+' Dr');
}else if(TaxStatus < 0 ){
  $("#"+fieldid).val(Math.abs(TaxStatus).toFixed(2)+' Cr');
}
}

$('#PDC_STATUS').on('change',function()
{
  if($(this).is(':checked') == false)
  {
    $('#PDC_DT').val('');
  }

});

$('#CLEAR_PDC_STATUS').on('change',function()
{
  if($(this).is(':checked') == false)
  {
    $('#CLEAR_PDC_DT').val('');
  }

});


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
    showSelectedCheck($("#CRID_REF").val(),"SELECT_CRID");
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
        $('#CONVFACT').val(GetConvFector(txtval));
        $("#currencycodesearch").val(''); 
        $("#currencynamesearch").val(''); 
        MultiCurrency_Conversion('tot_amt1'); 
        event.preventDefault();
      });

      

  //Currency Dropdown Ends		


  $("#FC").change(function() {
      if ($(this).is(":checked") == true){
          $(this).parent().parent().find('#txtCRID_popup').removeAttr('disabled');
          $(this).parent().parent().find('#txtCRID_popup').prop('readonly','true');
          $('#CONVFACT').prop('readonly',false);
         
      }
      else
      {
          $(this).parent().parent().find('#txtCRID_popup').prop('disabled','true');
          $(this).parent().parent().find('#txtCRID_popup').removeAttr('readonly');
          $(this).parent().parent().find('#txtCRID_popup').val('');
          $(this).parent().parent().find('#CRID_REF').val('');
          $(this).parent().parent().find('#CONVFACT').val('');
          $('#CONVFACT').prop('readonly',true);
         
      }
	  MultiCurrency_Conversion('tot_amt1'); 
  });
  
  function getSubLedger(){
  var SL = $('#SubGL').is(':checked');
  if(SL='false'){
    resetTab(); 
  }  
}

function resetTab(){

$('#Account').find('.participantRow3').each(function(){
  var rowcount = $(this).closest('table').find('.participantRow3').length;
  $(this).find('input:text').val('');
  $(this).find('input:hidden').val('');
  $(this).find('input:checkbox').prop('checked', false);

  if(rowcount > 1){
    $(this).closest('.participantRow3').remove();
    rowcount = parseInt(rowcount) - 1;
    $('#Row_Count1').val(rowcount);
  }
});

}
</script>


@endpush