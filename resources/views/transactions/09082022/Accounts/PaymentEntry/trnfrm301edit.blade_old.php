
@extends('layouts.app')
@section('content')
    

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Payment Entry</a>
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
    
<div class="container-fluid filter">
  <form id="frm_trn_pay"  method="POST"> 
      @csrf
      {{isset($objPAYHDR->PAYMENTID) ? method_field('PUT') : '' }}

	<div class="inner-form">
	
		<div class="row">
        <div class="col-lg-1 pl"><p>Payment No</p></div>
        <div class="col-lg-2 pl">
              <input type="text" name="PAYMENT_NO" id="PAYMENT_NO" value="{{$objPAYHDR->PAYMENT_NO}}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
        </div>
        <div class="col-lg-1 pl"><p>Payment Date</p></div>
        <div class="col-lg-2 pl">
              <input type="date" name="PAYMENT_DT" id="PAYMENT_DT" value="{{$objPAYHDR->PAYMENT_DT}}" class="form-control mandatory"  placeholder="dd/mm/yyyy" />
        </div>
        <div class="col-lg-1 pl"><p>Payment For</p></div>
        <div class="col-lg-2 pl">
              <input type="checkbox" name="chk_Vendor" id="chk_Vendor" {{$objPAYHDR->PAYMENT_FOR == 'Vendor' ? 'checked' : ''}} /> &nbsp;&nbsp;<label>   Vendor </label>
        </div>
        <div class="col-lg-2 pl">
              <input type="checkbox" name="chk_Customer" id="chk_Customer" {{$objPAYHDR->PAYMENT_FOR == 'Customer' ? 'checked' : ''}} />&nbsp;&nbsp;<label>   Customer </label>
        </div>
        <div class="col-lg-1 pl">
              <input type="checkbox" name="chk_Account" id="chk_Account" {{$objPAYHDR->PAYMENT_FOR == 'Account' ? 'checked' : ''}} />&nbsp;&nbsp;<label>   Account </label> 
              <input type="hidden" name="hdnpaymentfor" id="hdnpaymentfor" value="{{$objPAYHDR->PAYMENT_FOR}}" />
              <input type="hidden" name="hdnInvoice" id="hdnInvoice" />
              <input type="hidden" name="hdnAccount" id="hdnAccount" />
              <input type="hidden" name="hdnCostCenter" id="hdnCostCenter" />
              <input type="hidden" name="hdnCostCenter2" id="hdnCostCenter2" />
        </div>
    </div>
    <div class="row" id="divcust1" style="display:none;">
        <div class="col-lg-1 pl"><p> Customer / Vendor</p></div> 
        <div class="col-lg-2 pl">
            @if($objPAYHDR->CUSTMER_VENDOR_ID != '')
               <input type="text" name="txtcustomer" id="txtcustomer" class="form-control" value="{{$objPAYCUSTVNDR->CODE}}-{{$objPAYCUSTVNDR->NAME}}" readonly  />
            @else
               <input type="text" name="txtcustomer" id="txtcustomer" class="form-control" readonly  />
            @endif
                <input type="hidden" name="CUSTMER_VENDOR_ID" id="CUSTMER_VENDOR_ID"  class="form-control " value="{{$objPAYHDR->CUSTMER_VENDOR_ID}}" />
        </div>
        <div class="col-lg-1 pl"><p>Payment On Account</p></div>        
        <div class="col-lg-2 pl">
            <input type="checkbox" name="chk_PayAccount" id="chk_PayAccount" {{$objPAYHDR->PAYMENT_ON_ACCOUNT == 1 ? 'checked' : ''}} />
        </div>
        <div class="col-lg-1 pl" id="div_account_amt" style="display:none;"><p>Amount</p></div>        
        <div class="col-lg-2 pl" id="div_account_amt2" style="display:none;">
            <input type="text" name="AMOUNT" id="AMOUNT" class="form-control two-digits"  autocomplete="off" value="{{$objPAYHDR->AMOUNT}}" />
        </div>
    </div>
    <div class="row"  id="divcust2"  style="display:none;">
        <div class="col-lg-1 pl"><p>Cash / Bank Account</p></div>
        <div class="col-lg-2 pl">
                <input type="text" name="txtcashbk" id="txtcashbk" class="form-control" value="{{$objPAYCASHBANK->BCODE}}-{{$objPAYCASHBANK->NAME}}" readonly  />
                <input type="hidden" name="CASH_BANK_ID" id="CASH_BANK_ID"  class="form-control " value="{{$objPAYHDR->CASH_BANK_ID}}" />
                <input type="hidden" name="PAYMENT_TYPE" id="PAYMENT_TYPE"  class="form-control " value="{{$objPAYHDR->PAYMENT_TYPE}}" />
        </div>
        <div class="col-lg-1 pl"><p>Transaction Date</p></div>
        <div class="col-lg-2 pl">
            <input type="date" name="TRANSACTION_DT" id="TRANSACTION_DT" class="form-control"  placeholder="dd/mm/yyyy" value="{{$objPAYHDR->TRANSACTION_DT}}"  />
        </div>
        <div class="col-lg-1 pl"><p>Instrument Type</p></div>
        <div class="col-lg-2 pl">
            <select name="INSTRUMENT_TYPE" id="INSTRUMENT_TYPE" class="form-control">
                <option value="Cheque" {{$objPAYHDR->INSTRUMENT_TYPE == 'Cheque' ? 'selected' : ''}} >Cheque</option>
                <option value="NEFT" {{$objPAYHDR->INSTRUMENT_TYPE == 'NEFT' ? 'selected' : ''}} >NEFT</option>
                <option value="RTGS" {{$objPAYHDR->INSTRUMENT_TYPE == 'RTGS' ? 'selected' : ''}} >RTGS</option>
            </select>                        
        </div>
        <div class="col-lg-1 pl"><p>Instrument No</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="INSTRUMENT_NO" id="INSTRUMENT_NO" class="form-control"  autocomplete="off" value="{{$objPAYHDR->INSTRUMENT_NO}}" />                        
        </div>
    </div>
    <div class="row"  id="divcust3"  style="display:none;">	
        <div class="col-lg-1 pl"><p>Bank Charge</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="BANK_CHARGE" id="BANK_CHARGE" class="form-control two-digits"  autocomplete="off" value="{{$objPAYHDR->BANK_CHARGE}}" />
        </div>                          
        <div class="col-lg-1 pl"><p>Amount</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="tot_amt1" id="tot_amt1" class="form-control"  autocomplete="off" readonly  />
        </div>
        <div class="col-lg-2 pl"><p>Common Narration</p></div>
        <div class="col-lg-4 pl">
            <input type="text" name="NARRATION" id="NARRATION" class="form-control"  autocomplete="off" value="{{$objPAYHDR->NARRATION}}" />                        
        </div>
    </div>
    <div class="row"  id="divcust4"  style="display:none;">	
        <div class="col-lg-1 pl"><p>Centerlized Payment</p></div>
        <div class="col-lg-2 pl">
            <input type="checkbox" name="CENTERLIZED_PAYMENT" id="CENTERLIZED_PAYMENT" {{$objPAYHDR->CENTERLIZED_PAYMENT == 1 ? 'checked' : ''}}   />
        </div> 
    </div>
	</div>

	<div class="container-fluid">
		<div class="row" id="tabs">
			<ul class="nav nav-tabs">
				<li class="active" id="div_invoice" style="display:none"><a data-toggle="tab" href="#Invoice">Invoice Details</a></li> 
        <li id="div_account" style="display:none"><a data-toggle="tab" href="#Account">Account</a></li> 
        <!-- <li id="div_tds"><a data-toggle="tab" href="#TDS">TDS</a></li>         -->
        <li  style="display:none"><a data-toggle="tab" href="#CostCenter">Cost Center</a></li> 
			</ul>
      <div class="tab-content">
        <div id="Invoice" class="tab-pane fade in ">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                          <tr>
                              <th width="12%">Doc No<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="{{$objCount1}}"></th>
                              <th width="12%">Doc Type</th>
                              <th width="12%">Doc Date</th>
                              <th width="12%">Amount</th>
                              <th width="12%">Balance Due</th>
                              <th width="12%">Payment Amount</th>
                              <th width="12%">Remarks</th>
                              <th width="12%">Branch</th>
                              <th width="5%">Action</th>
                          </tr>
                    </thead>
                    <tbody>
                    @if(!empty($objPAYINV))
                      @foreach($objPAYINV as $key => $row)
                          <tr  class="participantRow2">
                              <td><input type="text" name={{"txtDoc_NO_".$key}} id={{"txtDoc_NO_".$key}} class="form-control"  autocomplete="off" value="{{$row->DOCNO}}" readonly/></td>
                              <td hidden><input type="hidden" name={{"DOCNO_ID_".$key}} id={{"DOCNO_ID_".$key}} class="form-control" value="{{$row->DOCNO_ID}}" autocomplete="off" /></td>
                              <td><input type="text" name={{"Doc_Type_".$key}} id={{"Doc_Type_".$key}} class="form-control"  autocomplete="off" value="{{$row->DOC_TYPE}}" readonly/></td>
                              <td><input type="text" name={{"DocDate_".$key}} id={{"DocDate_".$key}} class="form-control" autocomplete="off" placeholder="dd/mm/yyyy" value="{{$row->DOCDT}}" readonly /></td>
                              <td><input type="text" name={{"DocAmount_".$key}} id={{"DocAmount_".$key}} class="form-control two-digits"  autocomplete="off" value="{{$row->DOCAMT}}" readonly/></td>
                      @php
                          $BALANCE_DUE=number_format(($row->BALANCE_DUE+$row->PAYMENT_AMT),2, '.', '');
                      @endphp
                              <td><input type="text" name={{"BALANCE_DUE_".$key}} id={{"BALANCE_DUE_".$key}} class="form-control two-digits" maxlength="15" value="{{$BALANCE_DUE}}" autocomplete="off"  readonly/></td>
                              <td><input type="text" name={{"PAYMENT_AMT_".$key}} id={{"PAYMENT_AMT_".$key}} class="form-control two-digits" maxlength="15" value="{{$row->PAYMENT_AMT}}" autocomplete="off" /></td>
                              <td><input type="text" name={{"REMARKS_".$key}} id={{"REMARKS_".$key}} class="form-control" maxlength="200" autocomplete="off" value="{{$row->REMARKS}}" /></td>
                              <td><input type="text" name={{"TxtBranch_".$key}} id={{"TxtBranch_".$key}} class="form-control" value="{{$row->BRANCH}}" readonly  /></td>
                              <td hidden><input type="hidden" name={{"BRID_REF_".$key}} id={{"BRID_REF_".$key}} class="form-control" autocomplete="off" value="{{$row->BRID_REF}}" /></td>
                              <td align="center" ><button class="btn add ainvoice" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                              <button class="btn remove dinvoice" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                          </tr>
                          <tr></tr>
                      @endforeach
                    @else
                          <tr  class="participantRow2">
                              <td><input type="text" name="txtDoc_NO_0" id="txtDoc_NO_0" class="form-control"  autocomplete="off"  readonly/></td>
                              <td hidden><input type="hidden" name="DOCNO_ID_0" id="DOCNO_ID_0" class="form-control" autocomplete="off" /></td>
                              <td><input type="text" name="Doc_Type_0" id="Doc_Type_0" class="form-control"  autocomplete="off"  readonly/></td>
                              <td><input type="text" name="DocDate_0" id="DocDate_0" class="form-control" autocomplete="off" placeholder="dd/mm/yyyy" readonly /></td>
                              <td><input type="text" name="DocAmount_0" id="DocAmount_0" class="form-control two-digits"  autocomplete="off"  readonly/></td>
                              <td><input type="text" name="BALANCE_DUE_0" id="BALANCE_DUE_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                              <td><input type="text" name="PAYMENT_AMT_0" id="PAYMENT_AMT_0" class="form-control two-digits" maxlength="15"  autocomplete="off" /></td>
                              <td><input type="text" name="REMARKS_0" id="REMARKS_0" class="form-control" maxlength="200" autocomplete="off"  /></td>
                              <td><input type="text" name="TxtBranch_0" id="TxtBranch_0" class="form-control" readonly  /></td>
                              <td hidden><input type="hidden" name="BRID_REF_0" id="BRID_REF_0" class="form-control" autocomplete="off" /></td>
                              <td align="center" ><button class="btn add ainvoice" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                              <button class="btn remove dinvoice" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                          </tr>
                          <tr></tr>
                    @endif
                    </tbody>
                </table>
            </div>	
        </div> 
        <div id="Account" class="tab-pane fade in">
            <div class="row" style="margin-top:10px;margin-left:3px;" >	
                <div class="col-lg-2 pl"><p>Bank/Cash</p></div>
                <div class="col-lg-2 pl">
                    <input type="text" name="txtbnkcsh" id="txtbnkcsh" class="form-control" value="{{$objPAYCASHBANK->BCODE}}-{{$objPAYCASHBANK->NAME}}" readonly  />
                    <input type="hidden" name="BANK_CASH_ID" id="BANK_CASH_ID"  class="form-control" value="{{$objPAYHDR->CASH_BANK_ID }}" />
                </div>
            </div>
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:240px;" >
                <table id="example3" class="display nowrap table table-striped table-bordered itemlist " width="100%" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0">
                      <tr>
                          <th>Account Code<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3" value="{{$objCount2}}"></th>
                          <th>Account Name</th>
                          <th>Amount</th>
                          <th>IGST Rate</th>
                          <th>IGST Amount</th>
                          <th>CGST Rate</th>
                          <th>CGST Amount</th>
                          <th>SGST Rate</th>
                          <th>SGST Amount</th>
                          <th>Cost Center</th>
                          <th>Total</th>
                          <th width="8%">Action</th>
                      </tr>
                  </thead>
                  <tbody id="tbody_account">
                  @if(!empty($objPAYACCOUNT))
                    @foreach($objPAYACCOUNT as $gkey => $grow)
                      <tr  class="participantRow3">
                          <td><input type="text" name={{"popupAccount_".$gkey}} id={{"popupAccount_".$gkey}} class="form-control" value="{{$grow->GLCODE}}" autocomplete="off" readonly/></td>
                          <td hidden><input type="hidden" name={{"GLID_REF_".$gkey}} id={{"GLID_REF_".$gkey}} class="form-control" value="{{$grow->GLID_REF}}" autocomplete="off" /></td>
                          <td><input type="text" name={{"AccountName_".$gkey}} id={{"AccountName_".$gkey}} class="form-control"  autocomplete="off" value="{{$grow->GLNAME}}" readonly/></td>
                          <td><input type="text" name={{"AMOUNT_".$gkey}} id={{"AMOUNT_".$gkey}} class="form-control two-digits" maxlength="15" value="{{$grow->AMOUNT}}" autocomplete="off"  /></td>
                          <td><input type="text" name={{"calIGST_".$gkey}} id={{"calIGST_".$gkey}} class="form-control four-digits" maxlength="8" value="{{$grow->IGST}}" autocomplete="off"  /></td>
                      @php
                          $AmtIGST=number_format((($grow->AMOUNT*$grow->IGST)/100),2, '.', '');
                      @endphp
                          <td><input type="text" name={{"AMTIGST_".$gkey}} id={{"AMTIGST_".$gkey}} class="form-control two-digits" maxlength="15" value="{{$AmtIGST}}" autocomplete="off"  /></td>
                          <td><input type="text" name={{"calCGST_".$gkey}} id={{"calCGST_".$gkey}} class="form-control four-digits" maxlength="8" value="{{$grow->CGST}}" autocomplete="off"  /></td>
                      @php
                          $AmtCGST=number_format((($grow->AMOUNT*$grow->CGST)/100),2, '.', '');
                      @endphp
                          <td><input type="text" name={{"AMTCGST_".$gkey}} id={{"AMTCGST_".$gkey}} class="form-control two-digits" maxlength="15" value="{{$AmtCGST}}" autocomplete="off"  /></td>
                          <td><input type="text" name={{"calSGST_".$gkey}} id={{"calSGST_".$gkey}} class="form-control four-digits" maxlength="8" value="{{$grow->SGST}}" autocomplete="off"  /></td>
                      @php
                          $AmtSGST=number_format((($grow->AMOUNT*$grow->SGST)/100),2, '.', '');
                      @endphp
                          <td><input type="text" name={{"AMTSGST_".$gkey}} id={{"AMTSGST_".$gkey}} class="form-control two-digits" maxlength="15" value="{{$AmtSGST}}" autocomplete="off"  /></td>
                          <td align="center" ><button class="btn" id={{"BtnCCID_".$gkey}} name={{"BtnCCID_".$gkey}} type="button"><i class="fa fa-clone"></i></button></td>
                          <td hidden><input type="text" name={{"CCID_REF_".$gkey}} id={{"CCID_REF_".$gkey}} class="form-control"  autocomplete="off" value="{{$grow->CCID_REF}}"  readonly/></td>
                      @php
                          $AmtIGST=number_format((($grow->AMOUNT*$grow->IGST)/100),2, '.', '');
                          $AmtCGST=number_format((($grow->AMOUNT*$grow->CGST)/100),2, '.', '');
                          $AmtSGST=number_format((($grow->AMOUNT*$grow->SGST)/100),2, '.', '');
                          $TotAmt=number_format(($grow->AMOUNT+$AmtIGST+$AmtCGST+$AmtSGST),2, '.', '');
                      @endphp
                          <td><input type="text" name={{"TOTAMT_".$gkey}} id={{"TOTAMT_".$gkey}} class="form-control two-digits"  maxlength="15" autocomplete="off" value="{{$TotAmt}}"  readonly/></td>
                          <td align="center" ><button class="btn add aaccount" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove daccount" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                      </tr>
                      <tr></tr>
                    @endforeach
                  @else
                      <tr  class="participantRow3">
                          <td><input type="text" name="popupAccount_0" id="popupAccount_0" class="form-control"  autocomplete="off"  readonly/></td>
                          <td hidden><input type="hidden" name="GLID_REF_0" id="GLID_REF_0" class="form-control" autocomplete="off" /></td>
                          <td><input type="text" name="AccountName_0" id="AccountName_0" class="form-control"  autocomplete="off"  readonly/></td>
                          <td><input type="text" name="AMOUNT_0" id="AMOUNT_0" class="form-control two-digits" maxlength="15" autocomplete="off"  /></td>
                          <td><input type="text" name="calIGST_0" id="calIGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  /></td>
                          <td><input type="text" name="AMTIGST_0" id="AMTIGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  /></td>
                          <td><input type="text" name="calCGST_0" id="calCGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  /></td>
                          <td><input type="text" name="AMTCGST_0" id="AMTCGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  /></td>
                          <td><input type="text" name="calSGST_0" id="calSGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  /></td>
                          <td><input type="text" name="AMTSGST_0" id="AMTSGST_0" class="form-control two-digits" maxlength="15" autocomplete="off"  /></td>
                          <td align="center" ><button class="btn" id="BtnCCID_0" name="BtnCCID_0" type="button"><i class="fa fa-clone"></i></button></td>
                          <td hidden><input type="text" name="CCID_REF_0" id="CCID_REF_0" class="form-control"  autocomplete="off"  readonly/></td>
                          <td><input type="text" name="TOTAMT_0" id="TOTAMT_0" class="form-control two-digits"  maxlength="15" autocomplete="off"  readonly/></td>
                          <td align="center" ><button class="btn add aaccount" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove daccount" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                      </tr>
                      <tr></tr>
                  @endif
                  </tbody>
                </table>
            </div>	
        </div>  
        <div id="CostCenter" class="tab-pane fade" >
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
                <table id="example5" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                    <tr >
                        <th>GLID<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2" value="{{$objCount3}}"></th>
                        <th>CCID</th>
                        <th>CC_AMT</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($objPAYCCD))
                      @foreach($objPAYCCD as $ckey => $crow)
                        <tr  class="participantRow5">
                            <td><input type="text" name={{"GLID_".$ckey}} id={{"GLID_".$ckey}} class="form-control"  autocomplete="off" value="{{$crow->GLID_REF}}" readonly/></td>
                            <td><input type="text" name={{"CCID_".$ckey}} id={"CCID_".$ckey}} class="form-control" autocomplete="off" value="{{$crow->CCID_REF}}"  readonly/></td>
                            <td><input type="text" name={{"GL_AMT_".$ckey}} id={{"GL_AMT_".$ckey}} class="form-control two-digits" value="{{$crow->AMT}}"  autocomplete="off" readonly/></td>                        
                        </tr>
                        <tr></tr>
                      @endforeach
                    @else
                      <tr  class="participantRow5">
                          <td><input type="text" name="GLID_0" id="GLID_0" class="form-control"  autocomplete="off"  readonly/></td>
                          <td><input type="text" name="CCID_0" id="CCID_0" class="form-control" autocomplete="off"   readonly/></td>
                          <td><input type="text" name="GL_AMT_0" id="GL_AMT_0" class="form-control two-digits"   autocomplete="off" readonly/></td>                        
                      </tr>
                      <tr></tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>      
        <div id="TDS" class="tab-pane fade">            
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                <table id="example7" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                          <tr>
                              <th width="20%">Section Code<input class="form-control" type="hidden" name="Row_Count4" id ="Row_Count4"></th>
                              <th width="20%">Section Name</th>
                              <th width="20%">Assesee Type</th>
                              <th width="20%">Amount Payable</th>                         
                              <th width="20%">Action</th>
                          </tr>
                    </thead>
                    <tbody id="tbody_tds">
                          <tr  class="participantRow7">
                              <td style="text-align:center;" >
                              <input type="text" name="txtSectionCode_0" id="txtSectionCode_0" class="form-control"  autocomplete="off"  readonly/></td>
                              <td hidden><input type="hidden" name="SECTIONID_REF_0" id="SECTIONID_REF_0" class="form-control" autocomplete="off" /></td>
                              <td><input type="text" name="Section_Name_0" id="Section_Name_0" class="form-control"  autocomplete="off"  readonly/></td>
                              <input type="text" name="txtAssesee_0" id="txtAssesee_0" class="form-control"  autocomplete="off"  readonly/></td>
                              <td hidden><input type="hidden" name="ASSESEEID_REF_0" id="ASSESEEID_REF_0" class="form-control" autocomplete="off" /></td>
                              <td><input type="text" name="Section_AMOUNT_0" id="Section_AMOUNT_0" class="form-control two-digits" maxlength="15"  autocomplete="off"  readonly/></td>
                              <td style="min-width: 100px;" ><button class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                              <button class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                          </tr>
                          <tr></tr>
                    </tbody>
            </table>
            </div>
        </div> 
      </div>
    </div>
  </div>
		
	<!-- </div> -->
	</form>
</div>
@endsection
@section('alert')


<!-- Invoice Dropdown -->
<div id="Invoicepopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:100%;">
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
                  <th id="all-check" style="width:4%;" >Select</th>
                  <th style="width:16%;">Type</th>
                  <th style="width:16%;">Doc No.</th>
                  <th style="width:16%;">Doc DT</th>
                  <th style="width:16%;">Branch</th>
                  <th style="width:16%;">Total Amount</th>
                  <th style="width:16%;">Balance Amount</th>
          </tr>
      </thead>
      <tbody>
        <tr>
        <td style="width:4%;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
          <td style="width:16%;">
            <input type="text" id="typesearch" onkeyup="TypeFunction()">
          </td>
          <td style="width:16%;">
            <input type="text" id="docsearch" onkeyup="DocFunction()">
          </td>
          <td style="width:16%;">
            <input type="text" id="datesearch" onkeyup="DateFunction()">
          </td>
          <td style="width:16%;">
            <input type="text" id="branchsearch" onkeyup="BranchFunction()">
          </td>
          <td style="width:16%;">
            <input type="text" id="totalsearch" onkeyup="TotalFunction()">
          </td>
          <td style="width:16%;">
            <input type="text" id="balsearch" onkeyup="BalFunction()">
          </td>
        </tr>
      </tbody>
    </table>
    <table id="CodeTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;">
      <thead id="thead2">
      </thead>
      <tbody id="tbody_invoice">      
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
 <div class="modal-dialog modal-md">
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
            <th>Code</th>
            <th>Name</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="Accountcodesearch" onkeyup="AccountCodeFunction()">
    </td>
    <td>
    <input type="text" id="Accountnamesearch" onkeyup="AccountNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="AccountCodeTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
        </thead>
        <tbody id="tbody_account">
        @foreach ($objgeneralledger as $index=>$glRow)
          <tr id="glidcode_{{ $index }}" class="clsglid">
              <td width="50%">{{ $glRow-> GLCODE }}
                <input type="hidden" id="txtglidcode_{{ $index }}" data-desc="{{ $glRow-> GLCODE }}" data-desc2="{{ $glRow-> GLNAME }}"  value="{{ $glRow-> GLID }}"/>
              </td>
              <td>{{ $glRow-> GLNAME }}</td>
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

<!-- Customer / Vendor Dropdown -->
<div id="Custpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='CustclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Customer / Vendor</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CustTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>Code</th>
            <th>Name</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="Custcodesearch" onkeyup="CustCodeFunction()">
    </td>
    <td>
    <input type="text" id="Custnamesearch" onkeyup="CustNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="CustTable2" class="display nowrap table  table-striped table-bordered" width="100%">
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
 <div class="modal-dialog modal-md" style="width:100%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='bank_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Bank Master</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="BankCodeTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>    
    <tr>
            <th style="width:14%;">Code</th>
            <th style="width:14%;">Name</th>
            <th style="width:14%;">Branch</th>
            <th style="width:14%;">IFSC</th>
            <th style="width:14%;">Account Type</th>
            <th style="width:14%;">Account Number</th>
            <th style="width:16%;">Address</th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td style="width:14%;">
        <input type="text" id="bankcodesearch" onkeyup="BankCodeFunction()">
      </td>
      <td style="width:14%;">
        <input type="text" id="banknamesearch" onkeyup="BankNameFunction()">
      </td>
      <td style="width:14%;">
        <input type="text" id="bankbranchsearch" onkeyup="BankBranchFunction()">
      </td>
      <td style="width:14%;">
        <input type="text" id="bankifscsearch" onkeyup="BankIFSCFunction()">
      </td>
      <td style="width:14%;">
        <input type="text" id="bankacctypesearch" onkeyup="BankAccTypeFunction()">
      </td>
      <td style="width:14%;">
        <input type="text" id="bankaccnumbersearch" onkeyup="BankAccNumberFunction()">
      </td>
      <td style="width:16%;">
        <input type="text" id="bankaddresssearch" onkeyup="BankAddressFunction()">
      </td>
    </tr>
    </tbody>
    </table>
      <table id="BankCodeTable2" class="display nowrap table  table-striped table-bordered" style="width:100%;">
        <thead id="thead2">
        </thead>
        <tbody id="tbody_bank">
        @foreach ($objBank as $index=>$bkRow)
          <tr id="bkidcode_{{ $index }}" class="clsbkid">
              <td style="width:14%;">{{ $bkRow-> BCODE }}
                <input type="hidden" id="txtbkidcode_{{ $index }}" data-desc="{{ $bkRow-> BCODE }}" data-desc2="{{ $bkRow-> NAME }}"  data-desc3="{{ $bkRow-> BANK_CASH }}" value="{{ $bkRow-> BID }}"/>
              </td>
              <td style="width:14%;">{{ $bkRow-> NAME }}</td>
              <td style="width:14%;">{{ $bkRow-> BRANCH }}</td>
              <td style="width:14%;">{{ $bkRow-> IFSC }}</td>
              <td style="width:14%;">{{ $bkRow-> ACTYPE }}</td>
              <td style="width:14%;">{{ $bkRow-> ACNO }}</td>
              <td style="width:16%;">{{ $bkRow-> ADD1 }} {{ $bkRow-> ADD2 }} {{ $bkRow-> CITY }} {{ $bkRow-> STATE }} {{ $bkRow-> COUNTRY }} {{ $bkRow-> PIN }}</td>
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
 <div class="modal-dialog modal-md" style="width:100%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='bank2_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Bank Master</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="Bank2CodeTable" class="display nowrap table  table-striped table-bordered" style="width:100%;">
    <thead>
    <tr id="none-select" class="searchalldata" hidden>                
          <td> 
              <input type="hidden" name="hdn_bank1" id="hdn_bank1"/>
              <input type="hidden" name="hdn_bank12" id="hdn_bank12"/>
              <input type="hidden" name="hdn_bank13" id="hdn_bank13"/>
          </td>
    </tr>
    <tr>
            <th style="width:14%;">Code</th>
            <th style="width:14%;">Name</th>
            <th style="width:14%;">Branch</th>
            <th style="width:14%;">IFSC</th>
            <th style="width:14%;">Account Type</th>
            <th style="width:14%;">Account Number</th>
            <th style="width:16%;">Address</th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td style="width:14%;">
        <input type="text" id="bank2codesearch" onkeyup="Bank2CodeFunction()">
      </td>
      <td style="width:14%;">
        <input type="text" id="bank2namesearch" onkeyup="Bank2NameFunction()">
      </td>
      <td style="width:14%;">
        <input type="text" id="bank2branchsearch" onkeyup="Bank2BranchFunction()">
      </td>
      <td style="width:14%;">
        <input type="text" id="bank2ifscsearch" onkeyup="Bank2IFSCFunction()">
      </td>
      <td style="width:14%;">
        <input type="text" id="bank2acctypesearch" onkeyup="Bank2AccTypeFunction()">
      </td>
      <td style="width:14%;">
        <input type="text" id="bank2accnumbersearch" onkeyup="Bank2AccNumberFunction()">
      </td>
      <td style="width:16%;">
        <input type="text" id="bank2addresssearch" onkeyup="Bank2AddressFunction()">
      </td>
    </tr>    
    </tbody>
    </table>
      <table id="Bank2CodeTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
        </thead>
        <tbody id="tbody_bank2">
        @foreach ($objBank as $index=>$bkRow)
          <tr id="bkidcode_{{ $index }}" class="clsbk2id">
              <td style="width:14%;">{{ $bkRow-> BCODE }}
                <input type="hidden" id="txtbkidcode_{{ $index }}" data-desc="{{ $bkRow-> BCODE }}" data-desc2="{{ $bkRow-> NAME }}"  value="{{ $bkRow-> BID }}"/>
              </td>
              <td style="width:14%;">{{ $bkRow-> NAME }}</td>
              <td style="width:14%;">{{ $bkRow-> BRANCH }}</td>
              <td style="width:14%;">{{ $bkRow-> IFSC }}</td>
              <td style="width:14%;">{{ $bkRow-> ACTYPE }}</td>
              <td style="width:14%;">{{ $bkRow-> ACNO }}</td>
              <td style="width:16%;">{{ $bkRow-> ADD1 }} {{ $bkRow-> ADD2 }} {{ $bkRow-> CITY }} {{ $bkRow-> STATE }} {{ $bkRow-> COUNTRY }} {{ $bkRow-> PIN }}</td>
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

  function DocFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("docsearch");
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

  function DateFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("datesearch");
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

  function BranchFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("branchsearch");
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

  function TotalFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("totalsearch");
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

  function BalanceFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("balancesearch");
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

  $('#Invoice').on('click','[id*="txtDoc_NO_"]',function(event){
    var CommonValue           =   $('#hdnpaymentfor').val();
    if($('#CENTERLIZED_PAYMENT').is(':checked') == true)
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
                    $('#hdn_Invoice7').val('');
                    $('#hdn_Invoice8').val('');
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
                $clone.find('[id*="txtDoc_NO_"]').val(txtdocno);
                $clone.find('[id*="DOCNO_ID_"]').val(txtval);
                $clone.find('[id*="Doc_Type_"]').val(txtsource);
                $clone.find('[id*="DocDate_"]').val(txtdocdt);
                $clone.find('[id*="TxtBranch_"]').val(txtbranch);
                $clone.find('[id*="BRID_REF_"]').val(txtbrdid);
                $clone.find('[id*="DocAmount_"]').val(txtdocamt);
                $clone.find('[id*="BALANCE_DUE_"]').val(txtbalamt);
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

                $('#'+txtid).val(txtdocno);
                $('#'+txt_id2).val(txtval);
                $('#'+txt_id3).val(txtsource);
                $('#'+txt_id4).val(txtdocdt);
                $('#'+txt_id5).val(txtdocamt);
                $('#'+txt_id6).val(txtbalamt);
                $('#'+txt_id7).val(txtbranch);
                $('#'+txt_id8).val(txtbrdid);

                $('#hdn_Invoice').val('');
                $('#hdn_Invoice2').val('');
                $('#hdn_Invoice3').val('');
                $('#hdn_Invoice4').val('');
                $('#hdn_Invoice5').val('');
                $('#hdn_Invoice6').val('');
                $('#hdn_Invoice7').val('');
                $('#hdn_Invoice8').val('');
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
                $clone.find('.dinvoice').removeAttr('disabled'); 
                $clone.find('[id*="txtDoc_NO_"]').val(txtdocno);
                $clone.find('[id*="DOCNO_ID_"]').val(txtval);
                $clone.find('[id*="Doc_Type_"]').val(txtsource);
                $clone.find('[id*="DocDate_"]').val(txtdocdt);
                $clone.find('[id*="TxtBranch_"]').val(txtbranch);
                $clone.find('[id*="BRID_REF_"]').val(txtbrdid);
                $clone.find('[id*="DocAmount_"]').val(txtdocamt);
                $clone.find('[id*="BALANCE_DUE_"]').val(txtbalamt);
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

                $('#'+txtid).val(txtdocno);
                $('#'+txt_id2).val(txtval);
                $('#'+txt_id3).val(txtsource);
                $('#'+txt_id4).val(txtdocdt);
                $('#'+txt_id5).val(txtdocamt);
                $('#'+txt_id6).val(txtbalamt);
                $('#'+txt_id7).val(txtbranch);
                $('#'+txt_id8).val(txtbrdid);

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

      function CustCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Custcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("CustTable2");
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

  function CustNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Custnamesearch");
        filter = input.value.toUpperCase();
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

  $('#txtcustomer').on('click',function(event){
    var CommonValue = $('#hdnpaymentfor').val();
      $("#tbody_Cust").html('');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[$FormId,"getCustVendor"])}}',
            type:'POST',
            data:{'CommonValue':CommonValue},
            success:function(data) {
              $("#tbody_Cust").html(data);    
              bindCustomerVendor();                    
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              $("#tbody_Cust").html('');                        
            },
        });
    $("#Custpopup").show();    
    event.preventDefault();
  });

  $("#CustclosePopup").click(function(event){
    $("#Custpopup").hide();
    event.preventDefault();
  });

  function bindCustomerVendor(){
    $(".clscustid").dblclick(function(){

          var fieldid               =   $(this).attr('id');
          var txtval                =   $("#txt"+fieldid+"").val();
          var texdesc               =   $("#txt"+fieldid+"").data("desc")+'-'+$("#txt"+fieldid+"").data("desc2"); 
          var oldCUSTMER_VENDOR_ID  =   $("#CUSTMER_VENDOR_ID").val();
          var InvoiceClone          =   $('#hdnInvoice').val(); 
          
          $('#txtcustomer').val(texdesc);
          $('#CUSTMER_VENDOR_ID').val(txtval);
          if (txtval != oldCUSTMER_VENDOR_ID)
          {
              // $('#Invoice').html(InvoiceClone);
              $('#Row_Count1').val('1');
              $('#example2').find('.participantRow2').each(function(){
                $(this).find('input:text').val('');
                $(this).find('input:hidden').val('');
                var rowcount = $('#Row_Count1').val();
                if(rowcount > 1)
                {
                  $(this).closest('.participantRow2').remove();
                  rowcount = parseInt(rowcount) - 1;
                  $('#Row_Count1').val(rowcount);
                }
              });
          }
          $("#Custpopup").hide();
          $("#Custcodesearch").val(''); 
          $("#Custnamesearch").val(''); 
          CustCodeFunction();
          
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

      function BankNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("banknamesearch");
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

      function BankBranchFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bankbranchsearch");
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

      function BankIFSCFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bankifscsearch");
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

      function BankAccTypeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bankacctypesearch");
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

      function BankAccNumberFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bankaccnumbersearch");
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

      function BankAddressFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bankaddresssearch");
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

  $('#txtcashbk').on('click',function(event){    
    $("#bank_popup").show();    
    event.preventDefault();
  });

  $("#bank_closePopup").click(function(event){
    $("#bank_popup").hide();
    event.preventDefault();
  });

  $(".clsbkid").dblclick(function(){
      var fieldid               =   $(this).attr('id');
      var txtval                =   $("#txt"+fieldid+"").val();
      var texdesc               =   $("#txt"+fieldid+"").data("desc")+'-'+$("#txt"+fieldid+"").data("desc2");
      var txtflag               =   $("#txt"+fieldid+"").data("desc3");

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
      $('#PAYMENT_TYPE').val(txtflag);
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

      function Bank2NameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bank2namesearch");
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

      function Bank2BranchFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bank2branchsearch");
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

      function Bank2IFSCFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bank2ifscsearch");
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

      function Bank2AccTypeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bank2acctypesearch");
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

      function Bank2AccNumberFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bank2accnumbersearch");
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

      function Bank2AddressFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("bank2addresssearch");
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

  $('#Account').on('click','#txtbnkcsh',function(event){    
    $("#bank2_popup").show();    
    event.preventDefault();
  });

  $("#bank2_closePopup").click(function(event){
    $("#bank2_popup").hide();
    event.preventDefault();
  });

  $(".clsbk2id").dblclick(function(){
      var fieldid               =   $(this).attr('id');
      var txtval                =   $("#txt"+fieldid+"").val();
      var texdesc               =   $("#txt"+fieldid+"").data("desc")+'-'+$("#txt"+fieldid+"").data("desc2"); 
            
      $('#txtbnkcsh').val(texdesc);
      $('#BANK_CASH_ID').val(txtval);
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

  function AccountNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Accountnamesearch");
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
  $('#Account').on('click','[id*="popupAccount_"]',function(event){
        if($('#drp_bnkcsh').val() == '')
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
            $("#account_popup").show();
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

      $(".clsglid").dblclick(function(){
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

          $("#account_popup").hide();
          $("#Accountcodesearch").val(''); 
          $("#Accountnameesearch").val(''); 
          AccountCodeFunction();        
          event.preventDefault();
      });
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

  $('#costpopup').on('focus','[id*="CostCenter"]',function(event){
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
    $(".clscccd").dblclick(function(){
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

      var totalvalue = 0.00;
      var tvalue = 0.00;
      $('#example2').find('.participantRow2').each(function()
      {
          if($(this).find('[id*="PAYMENT_AMT_"]').val() != '')
          {
            tvalue = $(this).find('[id*="PAYMENT_AMT_"]').val();
            if($(this).find('[id*="Doc_Type_"]').val() == 'PURCHASE_INVOICE' 
                || $(this).find('[id*="Doc_Type_"]').val() == 'SERVICE_PURCHASE_INVOICE'
                || $(this).find('[id*="Doc_Type_"]').val() == 'IMPORT_PURCHASE_INVOICE'
                || $(this).find('[id*="Doc_Type_"]').val() == 'AP_CREDIT_NOTE'
                || $(this).find('[id*="Doc_Type_"]').val() == 'AP_INVOICE'
                || $(this).find('[id*="Doc_Type_"]').val() == 'SALES_INVOICE'
                || $(this).find('[id*="Doc_Type_"]').val() == 'AR_DEBIT_NOTE'
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
      $('#tot_amt1').val(totalvalue);

var Invoice = $("#Invoice").html(); 
$('#hdnInvoice').val(Invoice);
var Account = $("#Account").html(); 
$('#hdnAccount').val(Account);
var CostCenter2 = $("#costpopup").html(); 
$('#hdnCostCenter').val(CostCenter2);
var CostCenter = $("#CostCenter").html(); 
$('#hdnCostCenter2').val(CostCenter);
$('#BANK_CHARGE').ForceNumericOnly();
$('#AMOUNT').ForceNumericOnly();
$('#tot_amt1').ForceNumericOnly();
$("[id*='DocAmount_']").ForceNumericOnly();
$("[id*='BALANCE_DUE_']").ForceNumericOnly();
$("[id*='PAYMENT_AMT_']").ForceNumericOnly();
$("[id*='AMOUNT_']").ForceNumericOnly();
$("[id*='calIGST']").ForceNumericOnly();
$("[id*='AMTIGST']").ForceNumericOnly();
$("[id*='calCGST']").ForceNumericOnly();
$("[id*='AMTCGST']").ForceNumericOnly();
$("[id*='calSGST']").ForceNumericOnly();
$("[id*='AMTSGST']").ForceNumericOnly();
$("[id*='TOTAMT_']").ForceNumericOnly();
$("[id*='AMTSGST']").ForceNumericOnly();

var objPayHDR = <?php echo json_encode($objPAYHDR); ?>;
var objlastdt = <?php echo json_encode($objlastdt[0]->PAYMENT_DT); ?>;
var today = new Date(); 
var ardate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
$('#PAYMENT_DT').attr('min',objlastdt);
$('#PAYMENT_DT').attr('max',ardate);


if(objPayHDR.PAYMENT_FOR == 'Vendor')
{
  $('#chk_Customer').prop('checked', false);
  $('#chk_Account').prop('checked', false);
  $('#hdnpaymentfor').val('Vendor');
  $('#div_invoice').show();
  $('#div_account').hide();
  $('#div_invoice').addClass('active');
  $('#Invoice').show();
  $('#Account').hide();
  $('#divcust1').show();
  $('#divcust2').show();
  $('#divcust3').show();
  $('#divcust4').show();
  $('#txtbnkcsh').val('');
  $('#BANK_CASH_ID').val('');
}
else if(objPayHDR.PAYMENT_FOR == 'Customer')
{
  $('#chk_Vendor').prop('checked', false);
  $('#chk_Account').prop('checked', false);
  $('#hdnpaymentfor').val('Customer');
  $('#div_invoice').show();
  $('#div_account').hide();
  $('#div_invoice').addClass('active');
  $('#Invoice').show();
  $('#Account').hide();
  $('#divcust1').show();
  $('#divcust2').show();
  $('#divcust3').show();
  $('#divcust4').show();
  $('#txtbnkcsh').val('');
  $('#BANK_CASH_ID').val('');
}
else if(objPayHDR.PAYMENT_FOR == 'Account')
{
  $('#chk_Vendor').prop('checked', false);
  $('#chk_Customer').prop('checked', false);
  $('#hdnpaymentfor').val('Account');
  $('#div_invoice').hide();
  $('#div_account').show();
  $('#div_account').addClass('active');
  $('#Account').addClass('active');
  $('#Invoice').removeClass('active');
  $('#Invoice').hide();
  $('#Account').show();
  $('#divcust1').hide();
  $('#divcust2').hide();
  $('#divcust3').hide();
  $('#divcust4').hide();
  $('#txtcashbk').val('');
  $('#CASH_BANK_ID').val('');
  $('#txtcustomer').val('');
  $('#CUSTMER_VENDOR_ID').val('');
}

if(objPayHDR.PAYMENT_ON_ACCOUNT == "1")
{
  $('#div_account_amt').show();
  $('#div_account_amt2').show();
  $('#div_invoice').hide();  
  $('#div_invoice').removeClass('active');
  $('#Invoice').removeClass('active');
  $('#Invoice').hide();
}
else
{
  $('#div_account_amt').hide();
  $('#div_account_amt2').hide();
  $('#AMOUNT').val('');
  $('#div_invoice').show();  
  $('#div_invoice').addClass('active');
  $('#Invoice').addClass('active');
  $('#Invoice').show();
}

$('#chk_Vendor').on('change',function()
{
  if($(this).is(':checked') == true)
  {
    $('#chk_Customer').prop('checked', false);
    $('#chk_Account').prop('checked', false);
    $('#hdnpaymentfor').val('Vendor');
    $('#div_invoice').show();
    $('#div_account').hide();
    $('#div_invoice').addClass('active');
    $('#Invoice').show();
    $('#Account').hide();
    $('#divcust1').show();
    $('#divcust2').show();
    $('#divcust3').show();
    $('#divcust4').show();
    $('#txtcustomer').val('');
    $('#CUSTMER_VENDOR_ID').val('');
    var InvoiceClone = $('#hdnInvoice').val(); 
    var AccountClone = $('#hdnAccount').val(); 
    var CostClone = $('#hdnCostCenter2').val();
    // $('#Invoice').html(InvoiceClone);
    $('#Row_Count1').val('1');
    $('#example2').find('.participantRow2').each(function(){
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count1').val();
      if(rowcount > 1)
      {
        $(this).closest('.participantRow2').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count1').val(rowcount);
      }
    });
    $('#example3').find('.participantRow3').each(function(){
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count3').val();
      if(rowcount > 1)
      {
        $(this).closest('.participantRow3').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count3').val(rowcount);
      }
    });
    $('#Row_Count3').val('1');
    $('#example5').find('.participantRow5').each(function(){
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count2').val();
      if(rowcount > 1)
      {
        $(this).closest('.participantRow5').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count2').val(rowcount);
      }
    });
    $('#Row_Count2').val('1');
  }
  else
  {
    $(this).prop('checked', false);
    $('#hdnpaymentfor').val('');
    $('#div_invoice').hide();
    $('#div_account').hide();
    $('#div_invoice').removeClass('active');
    $('#Invoice').hide();
    $('#Account').hide();
    $('#divcust1').hide();
    $('#divcust2').hide();
    $('#divcust3').hide();
    $('#divcust4').hide();
    $('#txtcustomer').val('');
    $('#CUSTMER_VENDOR_ID').val('');
    var InvoiceClone = $('#hdnInvoice').val(); 
    var AccountClone = $('#hdnAccount').val(); 
    var CostClone = $('#hdnCostCenter2').val();
    $('#Row_Count1').val('1');
    $('#example2').find('.participantRow2').each(function(){
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count1').val();
      if(rowcount > 1)
      {
        $(this).closest('.participantRow2').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count1').val(rowcount);
      }
    });
    $('#example3').find('.participantRow3').each(function(){
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count3').val();
      if(rowcount > 1)
      {
        $(this).closest('.participantRow3').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count3').val(rowcount);
      }
    });
    $('#Row_Count3').val('1');
    $('#example5').find('.participantRow5').each(function(){
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count2').val();
      if(rowcount > 1)
      {
        $(this).closest('.participantRow5').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count2').val(rowcount);
      }
    });
    $('#Row_Count2').val('1');
  }
});
$('#chk_Customer').on('change',function()
{
  if($(this).is(':checked') == true)
  {
    $('#chk_Vendor').prop('checked', false);
    $('#chk_Account').prop('checked', false);
    $('#hdnpaymentfor').val('Customer');
    $('#div_invoice').show();
    $('#div_account').hide();
    $('#div_invoice').addClass('active');
    $('#Invoice').show();
    $('#Account').hide();
    $('#divcust1').show();
    $('#divcust2').show();
    $('#divcust3').show();
    $('#divcust4').show();
    $('#txtcustomer').val('');
    $('#CUSTMER_VENDOR_ID').val('');
    var InvoiceClone = $('#hdnInvoice').val(); 
    var AccountClone = $('#hdnAccount').val(); 
    var CostClone = $('#hdnCostCenter2').val();
    $('#Row_Count1').val('1');
    $('#example2').find('.participantRow2').each(function(){
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count1').val();
      if(rowcount > 1)
      {
        $(this).closest('.participantRow2').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count1').val(rowcount);
      }
    });
    $('#example3').find('.participantRow3').each(function(){
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count3').val();
      if(rowcount > 1)
      {
        $(this).closest('.participantRow3').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count3').val(rowcount);
      }
    });
    $('#Row_Count3').val('1');
    $('#example5').find('.participantRow5').each(function(){
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count2').val();
      if(rowcount > 1)
      {
        $(this).closest('.participantRow5').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count2').val(rowcount);
      }
    });
    $('#Row_Count2').val('1');
  }
  else
  {
    $(this).prop('checked', false);
    $('#hdnpaymentfor').val('');
    $('#div_invoice').hide();
    $('#div_account').hide();
    $('#div_invoice').removeClass('active');
    $('#Invoice').hide();
    $('#Account').hide();
    $('#divcust1').hide();
    $('#divcust2').hide();
    $('#divcust3').hide();
    $('#divcust4').hide();
    $('#txtcustomer').val('');
    $('#CUSTMER_VENDOR_ID').val('');
    var InvoiceClone = $('#hdnInvoice').val(); 
    var AccountClone = $('#hdnAccount').val(); 
    var CostClone = $('#hdnCostCenter2').val();
    $('#Row_Count1').val('1');
    $('#example2').find('.participantRow2').each(function(){
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count1').val();
      if(rowcount > 1)
      {
        $(this).closest('.participantRow2').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count1').val(rowcount);
      }
    });
    $('#example3').find('.participantRow3').each(function(){
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count3').val();
      if(rowcount > 1)
      {
        $(this).closest('.participantRow3').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count3').val(rowcount);
      }
    });
    $('#Row_Count3').val('1');
    $('#example5').find('.participantRow5').each(function(){
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count2').val();
      if(rowcount > 1)
      {
        $(this).closest('.participantRow5').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count2').val(rowcount);
      }
    });
    $('#Row_Count2').val('1');
  }
});
$('#chk_Account').on('change',function()
{
  if($(this).is(':checked') == true)
  {
    $('#chk_Vendor').prop('checked', false);
    $('#chk_Customer').prop('checked', false);
    $('#hdnpaymentfor').val('Account');
    $('#div_invoice').hide();
    $('#div_account').show();
    $('#div_account').addClass('active');
    $('#Account').addClass('active');
    $('#Invoice').removeClass('active');
    $('#Invoice').hide();
    $('#Account').show();
    $('#divcust1').hide();
    $('#divcust2').hide();
    $('#divcust3').hide();
    $('#divcust4').hide();
    $('#txtcustomer').val('');
    $('#CUSTMER_VENDOR_ID').val('');
    var InvoiceClone = $('#hdnInvoice').val(); 
    var AccountClone = $('#hdnAccount').val(); 
    var CostClone = $('#hdnCostCenter2').val();
    $('#Row_Count1').val('1');
    $('#example2').find('.participantRow2').each(function(){
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count1').val();
      if(rowcount > 1)
      {
        $(this).closest('.participantRow2').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count1').val(rowcount);
      }
    });
    $('#example3').find('.participantRow3').each(function(){
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count3').val();
      if(rowcount > 1)
      {
        $(this).closest('.participantRow3').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count3').val(rowcount);
      }
    });
    $('#Row_Count3').val('1');
    $('#example5').find('.participantRow5').each(function(){
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count2').val();
      if(rowcount > 1)
      {
        $(this).closest('.participantRow5').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count2').val(rowcount);
      }
    });
    $('#Row_Count2').val('1');
  }
  else
  {
    $(this).prop('checked', false);
    $('#hdnpaymentfor').val('');
    $('#div_invoice').hide();
    $('#div_account').hide();
    $('#div_account').removeClass('active');
    $('#Account').removeClass('active');
    $('#Invoice').removeClass('active');
    $('#Invoice').hide();
    $('#Account').hide();
    $('#divcust1').hide();
    $('#divcust2').hide();
    $('#divcust3').hide();
    $('#divcust4').hide();
    $('#txtcustomer').val('');
    $('#CUSTMER_VENDOR_ID').val('');
    var InvoiceClone = $('#hdnInvoice').val(); 
    var AccountClone = $('#hdnAccount').val(); 
    var CostClone = $('#hdnCostCenter2').val();
    $('#Row_Count1').val('1');
    $('#example2').find('.participantRow2').each(function(){
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count1').val();
      if(rowcount > 1)
      {
        $(this).closest('.participantRow2').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count1').val(rowcount);
      }
    });
    $('#example3').find('.participantRow3').each(function(){
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count3').val();
      if(rowcount > 1)
      {
        $(this).closest('.participantRow3').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count3').val(rowcount);
      }
    });
    $('#Row_Count3').val('1');
    $('#example5').find('.participantRow5').each(function(){
      $(this).find('input:text').val('');
      $(this).find('input:hidden').val('');
      var rowcount = $('#Row_Count2').val();
      if(rowcount > 1)
      {
        $(this).closest('.participantRow5').remove();
        rowcount = parseInt(rowcount) - 1;
        $('#Row_Count2').val(rowcount);
      }
    });
    $('#Row_Count2').val('1');
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

$('#chk_PayAccount').on('change',function()
{
  if($(this).is(':checked') == true)
  {
    $('#div_account_amt').show();
    $('#div_account_amt2').show();
    $('#AMOUNT').val('');
    $('#div_invoice').hide();
    $('#div_account').hide();
    $('#div_account').removeClass('active');
    $('#Account').removeClass('active');
    $('#Invoice').removeClass('active');
    $('#Invoice').hide();
    $('#Account').hide();    
    var InvoiceClone = $('#hdnInvoice').val(); 
    var AccountClone = $('#hdnAccount').val(); 
    var CostClone = $('#hdnCostCenter2').val();
    $('#Invoice').html(InvoiceClone);
    $('#Row_Count1').val('1');
    $('#Account').html(AccountClone);
    $('#Row_Count3').val('1');
    $('#CostCenter').html(CostClone);
    $('#Row_Count2').val('1');
    bindTotalValue();
    event.preventDefault();
  }
  else
  {
    $(this).prop('checked', false);
    $('#div_invoice').show();
    $('#div_account').hide();
    $('#div_account').removeClass('active');
    $('#Account').removeClass('active');
    $('#Invoice').addClass('active');
    $('#Invoice').show();
    $('#Account').hide();
    $('#div_account_amt').hide();
    $('#div_account_amt2').hide();
    $('#AMOUNT').val('');
    var InvoiceClone = $('#hdnInvoice').val(); 
    var AccountClone = $('#hdnAccount').val(); 
    var CostClone = $('#hdnCostCenter2').val();
    $('#Invoice').html(InvoiceClone);
    $('#Row_Count1').val('1');
    $('#Account').html(AccountClone);
    $('#Row_Count3').val('1');
    $('#CostCenter').html(CostClone);
    $('#Row_Count2').val('1');
    bindTotalValue();
    event.preventDefault();
  }
});

$('#BANK_CHARGE').on('focusout',function(){
  if($(this).val() != '' && $(this).val() != '.00' && $(this).val() > '0')
  {
    if(intRegex.test($(this).val())){
      $(this).val($(this).val()+'.00');
    }
  }
    bindTotalValue();
    event.preventDefault();
});

$('#AMOUNT').on('focusout',function(){
  if($(this).val() != '' && $(this).val() != '.00' && $(this).val() > '0')
  {
    if(intRegex.test($(this).val())){
      $(this).val($(this).val()+'.00');
    }
  }
    bindTotalValue();
    event.preventDefault();
});

function bindTotalValue()
    {
      var totalvalue = 0.00;
      var tvalue = 0.00;
      $('#example2').find('.participantRow2').each(function()
      {
          if($(this).find('[id*="PAYMENT_AMT_"]').val() != '')
          {
            tvalue = $(this).find('[id*="PAYMENT_AMT_"]').val();
            if($(this).find('[id*="Doc_Type_"]').val() == 'PURCHASE_INVOICE' 
                || $(this).find('[id*="Doc_Type_"]').val() == 'SERVICE_PURCHASE_INVOICE'
                || $(this).find('[id*="Doc_Type_"]').val() == 'IMPORT_PURCHASE_INVOICE'
                || $(this).find('[id*="Doc_Type_"]').val() == 'AP_CREDIT_NOTE'
                || $(this).find('[id*="Doc_Type_"]').val() == 'AP_INVOICE'
                || $(this).find('[id*="Doc_Type_"]').val() == 'SALES_INVOICE'
                || $(this).find('[id*="Doc_Type_"]').val() == 'AR_DEBIT_NOTE'
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
      $('#tot_amt1').val(totalvalue);
  }


$("#Invoice").on('focusout', "[id*='PAYMENT_AMT_']", function() 
{
  if($(this).val() != '' && $(this).val() != '.00' && $(this).val() > '0')
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
      $("#AlertMessage").text('Please Payment Amount cannot be greater than Balance Amt.');
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
  if($(this).val() != '' && $(this).val() != '.00' && $(this).val() > '0')
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
    // bindTotalValue();
    // if($('#CTID_REF').val()!='')
    // {
    // bindGSTCalTemplate();
    // }
    // bindTotalValue();
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
    if($(this).val() != '' && $(this).val() != '.0000' && $(this).val() > '0')
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
    if($(this).val() != '' && $(this).val() != '.0000' && $(this).val() > '0')
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
      event.preventDefault();
    }
    else
    {
      $(this).val('0.0000');
      $(this).parent().parent().find('[id*="AMTCGST_"]').val('0.00');
      $(this).parent().parent().find('[id*="AMTIGST_"]').removeAttr('disabled');
      $(this).parent().parent().find('[id*="calIGST_"]').removeAttr('disabled');
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
    if($(this).val() != '' && $(this).val() != '.0000' && $(this).val() > '0')
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
      event.preventDefault();
    }
    else
    {
      $(this).val('0.0000');
      $(this).parent().parent().find('[id*="AMTSGST_"]').val('0.00');
      $(this).parent().parent().find('[id*="AMTIGST_"]').removeAttr('disabled');
      $(this).parent().parent().find('[id*="calIGST_"]').removeAttr('disabled');
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
    if($(this).val() != '' && $(this).val() != '.00' && $(this).val() > '0')
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
    if($(this).val() != '' && $(this).val() != '.00' && $(this).val() > '0')
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
      event.preventDefault();
    }
    else
    {
      $(this).val('0.00');
      $(this).parent().parent().find('[id*="calIGST_"]').val('0.0000');
      $(this).parent().parent().find('[id*="AMTIGST_"]').removeAttr('disabled');
      $(this).parent().parent().find('[id*="calIGST_"]').removeAttr('disabled');
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
    if($(this).val() != '' && $(this).val() != '.00' && $(this).val() > '0')
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
      event.preventDefault();
    }
    else
    {
      $(this).val('0.00');
      $(this).parent().parent().find('[id*="calIGST_"]').val('0.0000');
      $(this).parent().parent().find('[id*="AMTIGST_"]').removeAttr('disabled');
      $(this).parent().parent().find('[id*="calIGST_"]').removeAttr('disabled');
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
    $("#PAYMENT_NO").focus();
}//fnUndoNo

});
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

//   $("#btnSave").on("submit", function( event ) {

//     if ($("#frm_trn_pay").valid()) {
//         // Do something
//         alert( "Handler for .submit() called." );
//         event.preventDefault();
//     }
// });


    $('#frm_trn_pay1').bootstrapValidator({       
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
             $("#frm_trn_pay").submit();
        }
    });
});



$("#btnSave").click(function() {
var formPaymentEntry = $("#frm_trn_pay");
if(formPaymentEntry.valid()){
 $("#FocusId").val('');
 var PAYMENT_NO          =   $.trim($("#PAYMENT_NO").val());
 var PAYMENT_DT          =   $.trim($("#PAYMENT_DT").val());
 var hdnpaymentfor       =   $.trim($("#hdnpaymentfor").val());
 var CUSTMER_VENDOR_ID   =   $.trim($("#CUSTMER_VENDOR_ID").val());
 var CASH_BANK_ID        =   $.trim($("#CASH_BANK_ID").val());
 var TRANSACTION_DT      =   $.trim($("#TRANSACTION_DT").val());
 var INSTRUMENT_TYPE     =   $.trim($("#INSTRUMENT_TYPE").val());
 var INSTRUMENT_NO       =   $.trim($("#INSTRUMENT_NO").val());
 var BANK_CASH_ID        =   $.trim($("#BANK_CASH_ID").val());
 var PAYMENT_TYPE        =   $.trim($("#PAYMENT_TYPE").val());
 var AMOUNT              =   $.trim($("#AMOUNT").val());

 var TotalAmount = $("#tot_amt1").val();

 if(TotalAmount < '0.00' || TotalAmount < '0')
 {
     $("#FocusId").val($("#PAYMENT_NO"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Total Payment Amount cannot be less than Zero.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }


 if(PAYMENT_NO ===""){
     $("#FocusId").val($("#PAYMENT_NO"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please enter value in Payment Number.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(PAYMENT_DT ===""){
    $("#FocusId").val($("#PAYMENT_DT"));
     $("#PAYMENT_DT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select Payment Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }  
 else if(hdnpaymentfor ===""){
     $("#FocusId").val($("#hdnpaymentfor"));
     $("#hdnpaymentfor").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select One Option from Vendor / Customer / Invoice for paymemt.');
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
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && PAYMENT_TYPE == "Bank" && CASH_BANK_ID != "" 
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
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && PAYMENT_TYPE == "Bank" && CASH_BANK_ID != "" 
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
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && PAYMENT_TYPE == "Bank" && CASH_BANK_ID != "" 
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
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && PAYMENT_TYPE == "Bank" && CASH_BANK_ID != "" 
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
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && PAYMENT_TYPE == "Bank" && CASH_BANK_ID != "" && TRANSACTION_DT !="" &&  INSTRUMENT_TYPE !="" && INSTRUMENT_NO===""){
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
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && PAYMENT_TYPE == "Bank" && CASH_BANK_ID != "" && TRANSACTION_DT !="" &&  INSTRUMENT_TYPE ===""){
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
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
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
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
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
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && AMOUNT ===""){
     $("#FocusId").val($("#AMOUNT"));
     $("#AMOUNT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Amount for Payment on Account');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && AMOUNT ==="0.00"){
     $("#FocusId").val($("#AMOUNT"));
     $("#AMOUNT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Amount for Payment on Account');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && AMOUNT ===".00"){
     $("#FocusId").val($("#AMOUNT"));
     $("#AMOUNT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Amount for Payment on Account');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && PAYMENT_TYPE == "Bank" && CASH_BANK_ID != "" && TRANSACTION_DT !="" &&  INSTRUMENT_TYPE !="" && INSTRUMENT_NO===""){
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
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && PAYMENT_TYPE == "Bank" && CASH_BANK_ID != "" && TRANSACTION_DT !="" &&  INSTRUMENT_TYPE ===""){
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
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
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
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
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
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && AMOUNT ===""){
     $("#FocusId").val($("#AMOUNT"));
     $("#AMOUNT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Amount for Payment on Account');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && AMOUNT ==="0.00"){
     $("#FocusId").val($("#AMOUNT"));
     $("#AMOUNT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Amount for Payment on Account');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && AMOUNT ===".00"){
     $("#FocusId").val($("#AMOUNT"));
     $("#AMOUNT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Amount for Payment on Account');
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

        if($('#chk_Vendor').is(':checked') == true && $('#chk_PayAccount').is(':checked') == false)
        {
          $('#example2').find('.participantRow2').each(function()
          {    
              if($.trim($(this).find("[id*=txtDoc_NO_]").val())!="")
              {
                    allblank.push('true');
                    if($.trim($(this).find("[id*=PAYMENT_AMT_]").val())!="")
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
        if($('#chk_Customer').is(':checked') == true && $('#chk_PayAccount').is(':checked') == false)
        {
          $('#example2').find('.participantRow2').each(function()
          {    
              if($.trim($(this).find("[id*=txtDoc_NO_]").val())!="")
              {
                    allblank.push('true');
                    if($.trim($(this).find("[id*=PAYMENT_AMT_]").val())!="")
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
          $("#AlertMessage").text('Please enter Paymemt Amount in Invoice Details Tab.');
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
var formPaymentEntry = $("#frm_trn_pay");
if(formPaymentEntry.valid()){
 $("#FocusId").val('');
 var PAYMENT_NO          =   $.trim($("#PAYMENT_NO").val());
 var PAYMENT_DT          =   $.trim($("#PAYMENT_DT").val());
 var hdnpaymentfor       =   $.trim($("#hdnpaymentfor").val());
 var CUSTMER_VENDOR_ID   =   $.trim($("#CUSTMER_VENDOR_ID").val());
 var CASH_BANK_ID        =   $.trim($("#CASH_BANK_ID").val());
 var TRANSACTION_DT      =   $.trim($("#TRANSACTION_DT").val());
 var INSTRUMENT_TYPE     =   $.trim($("#INSTRUMENT_TYPE").val());
 var INSTRUMENT_NO       =   $.trim($("#INSTRUMENT_NO").val());
 var BANK_CASH_ID        =   $.trim($("#BANK_CASH_ID").val());
 var PAYMENT_TYPE        =   $.trim($("#PAYMENT_TYPE").val());
 var AMOUNT              =   $.trim($("#AMOUNT").val());

 var TotalAmount = $("#tot_amt1").val();

 if(TotalAmount < '0.00' || TotalAmount < '0')
 {
     $("#FocusId").val($("#PAYMENT_NO"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Total Payment Amount cannot be less than Zero.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }


 if(PAYMENT_NO ===""){
     $("#FocusId").val($("#PAYMENT_NO"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please enter value in Payment Number.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(PAYMENT_DT ===""){
    $("#FocusId").val($("#PAYMENT_DT"));
     $("#PAYMENT_DT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select Payment Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }  
 else if(hdnpaymentfor ===""){
     $("#FocusId").val($("#hdnpaymentfor"));
     $("#hdnpaymentfor").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select One Option from Vendor / Customer / Invoice for paymemt.');
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
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && PAYMENT_TYPE == "Bank" && CASH_BANK_ID != "" 
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
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && PAYMENT_TYPE == "Bank" && CASH_BANK_ID != "" 
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
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && PAYMENT_TYPE == "Bank" && CASH_BANK_ID != "" 
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
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && PAYMENT_TYPE == "Bank" && CASH_BANK_ID != "" 
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
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && PAYMENT_TYPE == "Bank" && CASH_BANK_ID != "" && TRANSACTION_DT !="" &&  INSTRUMENT_TYPE !="" && INSTRUMENT_NO===""){
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
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && PAYMENT_TYPE == "Bank" && CASH_BANK_ID != "" && TRANSACTION_DT !="" &&  INSTRUMENT_TYPE ===""){
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
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
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
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
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
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && AMOUNT ===""){
     $("#FocusId").val($("#AMOUNT"));
     $("#AMOUNT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Amount for Payment on Account');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && AMOUNT ==="0.00"){
     $("#FocusId").val($("#AMOUNT"));
     $("#AMOUNT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Amount for Payment on Account');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Customer').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && AMOUNT ===".00"){
     $("#FocusId").val($("#AMOUNT"));
     $("#AMOUNT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Amount for Payment on Account');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && PAYMENT_TYPE == "Bank" && CASH_BANK_ID != "" && TRANSACTION_DT !="" &&  INSTRUMENT_TYPE !="" && INSTRUMENT_NO===""){
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
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && PAYMENT_TYPE == "Bank" && CASH_BANK_ID != "" && TRANSACTION_DT !="" &&  INSTRUMENT_TYPE ===""){
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
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
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
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
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
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && AMOUNT ===""){
     $("#FocusId").val($("#AMOUNT"));
     $("#AMOUNT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Amount for Payment on Account');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && AMOUNT ==="0.00"){
     $("#FocusId").val($("#AMOUNT"));
     $("#AMOUNT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Amount for Payment on Account');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if($('#chk_Vendor').is(':checked') == true && CUSTMER_VENDOR_ID !="" && $('#chk_PayAccount').is(':checked') == true
        && AMOUNT ===".00"){
     $("#FocusId").val($("#AMOUNT"));
     $("#AMOUNT").val('');  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please Enter Amount for Payment on Account');
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

        if($('#chk_Vendor').is(':checked') == true && $('#chk_PayAccount').is(':checked') == false)
        {
          $('#example2').find('.participantRow2').each(function()
          {    
              if($.trim($(this).find("[id*=txtDoc_NO_]").val())!="")
              {
                    allblank.push('true');
                    if($.trim($(this).find("[id*=PAYMENT_AMT_]").val())!="")
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
        if($('#chk_Customer').is(':checked') == true && $('#chk_PayAccount').is(':checked') == false)
        {
          $('#example2').find('.participantRow2').each(function()
          {    
              if($.trim($(this).find("[id*=txtDoc_NO_]").val())!="")
              {
                    allblank.push('true');
                    if($.trim($(this).find("[id*=PAYMENT_AMT_]").val())!="")
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
          $("#AlertMessage").text('Please enter Paymemt Amount in Invoice Details Tab.');
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
        else{
                $("#alert").modal('show');
                $("#AlertMessage").text('Do you want to save to record.');
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

// $("#btnSave" ).click(function() {
//     var formReqData = $("#frm_trn_pay");
//     if(formReqData.valid()){
//       validateForm();
//     }
// });

window.fnSaveData = function (){
//validate and save data
event.preventDefault();

     var trnseForm = $("#frm_trn_pay");
    var formData = trnseForm.serialize();
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

     var trnseForm = $("#frm_trn_pay");
    var formData = trnseForm.serialize();
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
    var FocusId=$("#FocusId").val();
    $("#"+FocusId).focus();
    $("#closePopup").click();
}

function highlighFocusBtn(pclass){
    $(".activeYes").hide();
    $(".activeNo").hide();
    $("."+pclass+"").show();
}

    





</script>


@endpush