@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Accessory Invoice</a></div>
    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
      <button class="btn topnavbt" id="btnSaveFormData" onclick="saveAction('save')" ><i class="fa fa-floppy-o"></i> Save</button>
      <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
      <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt" id="btnApprove" disabled="disabled" onclick="saveAction('approve')"><i class="fa fa-thumbs-o-up"></i> Approved</button>
      <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
      <button class="btn topnavbt" id="btnExit" onclick="return  window.location.href='{{route('home')}}'" ><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>

<form id="transaction_form" method="POST" >
  <div class="container-fluid filter"> 
    @csrf
    <div class="inner-form"> 
      <div class="row">
        <div class="col-lg-2 pl"><p>Document No*</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="DOC_NO" id="DOC_NO" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
          <script>docMissing(@json($docarray['FY_FLAG']));</script>
        </div>

        <div class="col-lg-2 pl"><p>Document Date*</p></div>
        <div class="col-lg-2 pl">
          <input type="date" name="DOC_DATE" id="DOC_DATE" value="{{date('Y-m-d')}}"  class="form-control" autocomplete="off" placeholder="dd/mm/yyyy" >
        </div>

        <div class="col-lg-1 pl"><p>Existing Customer</p></div>
        <div class="col-lg-1 pl"> <input type="radio" name="CUSTOMER_TYPE" value="EXIST" checked onchange="get_customer_type(this.value)" ></div>

        <div class="col-lg-1 pl"><p>New Customer</p></div>
        <div class="col-lg-1 pl"> <input type="radio" name="CUSTOMER_TYPE" value="NEW" onchange="get_customer_type(this.value)" ></div>
        </div>




        <div class="row">
        <div class="col-lg-2 pl"><p>Search</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" id="SEARCH_CUSTOMER" onclick="searchCustomerMaster()" class="form-control" autocomplete="off"  onkeypress="return isNumberKey(event,this)" readonly >
        </div>

        <div class="col-lg-1 pl"> 
          <i class="fa fa-search" onclick="searchCustomerMaster()" style="cursor:pointer;margin-top:5px;"></i>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-2 pl"><p>Foreign Currency</p></div>
        <div class="col-lg-1 pl">
            <input type="checkbox" name="FC" id="FC" class="form-checkbox" >
        </div>                            
        <div class="col-lg-2 pl col-md-offset-1"><p>Currency</p></div>
        <div class="col-lg-2 pl" id="divcurrency" >
            <input type="text" name="CRID_popup" id="txtCRID_popup" class="form-control"  autocomplete="off"  disabled/>
            <input type="hidden" name="CRID_REF" id="CRID_REF" class="form-control" autocomplete="off" />                                
        </div>                            
        <div class="col-lg-2 pl"><p>Conversion Factor</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="CONVFACT" id="CONVFACT" autocomplete="off" onkeyup="MultiCurrency_Conversion('TotalValue')" class="form-control" readonly  maxlength="100" />
        </div>
    </div>   

      <div class="row">
        <div class="col-lg-2 pl"><p>Customer Name*</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="CUSTOMER_NAME" id="CUSTOMER_NAME" class="form-control" autocomplete="off" readonly>
          <input type="hidden" name="CUSTOMER_ID" id="CUSTOMER_ID" class="form-control" autocomplete="off" >
          <input type="hidden" name="TAX_TYPE" id="TAX_TYPE" class="form-control" autocomplete="off" >
        </div>

        <div class="col-lg-2 pl"><p>Date of Birth</p></div>
        <div class="col-lg-2 pl"> 
          <input type="date" name="DOB" id="DOB" class="form-control" autocomplete="off" >
        </div>

        <div class="col-lg-2 pl"><p>E-Mail Id*</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="EMAIL_ID" id="EMAIL_ID" class="form-control" autocomplete="off" >
        </div>
      </div>

      <div class="row">
        <div class="col-lg-2 pl"><p>Mobile No*</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="MOBILE_NO" id="MOBILE_NO" class="form-control" autocomplete="off" placeholder='Mobile No' maxlength="12"  onkeypress="return isNumberKey(event,this)" onkeyup="validate_mobile_no()" >
        </div>

        <div class="col-lg-2 pl"><p>Address*</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="ADDRESS" id="ADDRESS" class="form-control" autocomplete="off" >
        </div>

        <div class="col-lg-2 pl"><p>Anniversary Date</p></div>
        <div class="col-lg-2 pl"> 
          <input type="date" name="ANNIVERSARY_DATE" id="ANNIVERSARY_DATE" class="form-control" autocomplete="off" >
        </div>
      </div>

      <div class="row">
        <div class="col-lg-2 pl"><p>Country*</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="COUNTRY_NAME" id="COUNTRY_NAME" class="form-control" autocomplete="off" onclick="getCountryMaster()" readonly value="{{isset($country_state_city[0]->COUNTRY_NAME)?$country_state_city[0]->COUNTRY_NAME:''}}" >
          <input type="hidden" name="COUNTRY_ID" id="COUNTRY_ID" class="form-control" autocomplete="off" value="{{isset($country_state_city[0]->COUNTRY_ID)?$country_state_city[0]->COUNTRY_ID:''}}" >
        </div>
       
        <div class="col-lg-2 pl"><p>State*</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="STATE_NAME" id="STATE_NAME" class="form-control" autocomplete="off" onclick="getStateMaster()" readonly value="{{isset($country_state_city[0]->STATE_NAME)?$country_state_city[0]->STATE_NAME:''}}" >
          <input type="hidden" name="STATE_ID" id="STATE_ID" class="form-control" autocomplete="off" value="{{isset($country_state_city[0]->STATE_ID)?$country_state_city[0]->STATE_ID:''}}">
        </div>

        <div class="col-lg-2 pl"><p>City*</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="CITY_NAME" id="CITY_NAME" class="form-control" autocomplete="off" onclick="getCityMaster()" readonly value="{{isset($country_state_city[0]->CITY_NAME)?$country_state_city[0]->CITY_NAME:''}}" >
          <input type="hidden" name="CITY_ID" id="CITY_ID" class="form-control" autocomplete="off" value="{{isset($country_state_city[0]->CITY_ID)?$country_state_city[0]->CITY_ID:''}}">
        </div>
      </div>

      <div class="row">
        

        <div class="col-lg-2 pl"><p>Pin Code*</p></div>
        <div class="col-lg-2 pl"> 
        <input type="text" name="PINCODE" id="PINCODE" class="form-control" autocomplete="off" maxlength="6" onkeypress="return isNumberKey(event,this)" >
        </div>

        <div class="col-lg-2 pl"><p>GST Type*</p></div>
        <div class="col-lg-2 pl"> 
          <select name="GST_TYPE" id="GST_TYPE" class="form-control mandatory" autocomplete="off" >
						<option value="">Select</option>
            @foreach ($objGstTypeList as $index=>$GstType)
            <option value="{{ $GstType-> GSTID }}">{{ $GstType->GSTCODE }} - {{ $GstType->DESCRIPTIONS }}</option>
            @endforeach
					</select>
        </div>
       
        <div class="col-lg-2 pl"><p>GSTIN</p></div>
        <div class="col-lg-2 pl"> 
        <input type="text" name="GST_IN" id="GST_IN" class="form-control" autocomplete="off">
        </div>
      </div>

      <div class="row">
        <div class="col-lg-2 pl"><p>Landline No</p></div>
        <div class="col-lg-2 pl"> 
        <input type="text" name="LANDLINE_NO" id="LANDLINE_NO" class="form-control" autocomplete="off" onkeypress="return isNumberKey(event,this)" >
        </div>

        <div class="col-lg-2 pl"><p>Vehicle Reg No*</p></div>
        <div class="col-lg-2 pl"> 
        <input type="text" name="VEHICLE_REG_NO" id="VEHICLE_REG_NO" class="form-control" autocomplete="off">
        </div>
       
        <div class="col-lg-2 pl"><p>Vehicle Make</p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="VEHICLE_MAKE_NAME" id="VEHICLE_MAKE_NAME" class="form-control" autocomplete="off" onclick="getVehicleMakeMaster()" readonly >
          <input type="hidden" name="VEHICLE_MAKE_ID" id="VEHICLE_MAKE_ID" class="form-control" autocomplete="off">
        </div>
      </div>


        <div class="row">
        <div class="col-lg-9 pl"></div>
        <div class="col-lg-1 pl"><p>Tax Amount*</p></div>
        <div class="col-lg-2 pl">
        <input type="text" name="TotalValue_tax" id="TotalValue_tax" class="form-control"  autocomplete="off" readonly  />
        </div>
      </div>
        <div class="row">
        <div class="col-lg-9 pl"></div>
        <div class="col-lg-1 pl"><p>Net Amount*</p></div>
        <div class="col-lg-2 pl">
        <input type="text" name="TotalValue" id="TotalValue" class="form-control"  autocomplete="off" readonly  />
        </div>
      </div>

      <div class="row">
      <div id="multi_currency_section" style="display:none">
        <div class="col-lg-9 pl"></div>
        <div class="col-lg-1 pl"><p id="currency_section"></p></div>
        <div class="col-lg-2 pl"> 
          <input type="text" name="TotalValue_Conversion" id="TotalValue_Conversion" class="form-control" autocomplete="off" readonly >
        </div>
      </div>
      </div>


        <div class="row">
        <div class="col-lg-9 pl"></div>
        <div class="col-lg-1 pl"><p>Paid Amount*</p></div>
        <div class="col-lg-2 pl">
        <input type="text" name="TotalValue_paid" id="TotalValue_paid" class="form-control"  autocomplete="off" readonly  />
        </div>
      </div>


     
    </div>

    <div class="container-fluid">
      <div class="row">
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#Material" id="MAT_TAB">Material</a></li>
          <li><a data-toggle="tab" href="#Payment" id="PAYMENT_TAB">Payment Mode</a></li>
        </ul>
                                            
        <div class="tab-content">
          
          <div id="Material" class="tab-pane fade in active">
					  <div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;" >
						  <table id="example2" class="display nowrap table table-striped table-bordered itemlist w-200" width="100%" style="height:auto !important;">


              <thead id="thead1"  style="position: sticky;top: 0">
      
                  <tr>                    
                      <th rowspan="2" style=" width:4%;">Item Code</th>
                      <th rowspan="2" style=" width:4%;">Item Name</th>
                      <th rowspan="2" style=" width:4%;">UOM</th>
                      <th rowspan="2" style=" width:4%;">Qty</th>
                      <th rowspan="2" style=" width:4%;">Rate Per UoM</th>                                          
                      <th colspan="2" style=" width:4%;">Discount</th>
                      <th rowspan="2" style=" width:4%;">Amount after discount</th>
                      <th rowspan="2" style=" width:4%;">IGST Rate %</th>
                      <th rowspan="2" style=" width:3%;">IGST Amount</th>
                      <th rowspan="2" style=" width:4%;">CGST Rate %</th>
                      <th rowspan="2" style=" width:3%;">CGST Amount</th>
                      <th rowspan="2" style=" width:4%;">SGST Rate %</th>
                      <th rowspan="2" style=" width:3%;">SGST Amount</th>
                      <th rowspan="2" style=" width:3%;">Total GST Amount</th>
                      <th rowspan="2" style=" width:3%;">Total after GST</th>
                      <th rowspan="2" style=" width:3%;">Action</th>
                  </tr>
                  <tr>
                      <th>%</th>
                      <th>Amount</th>
                  </tr>
              </thead>


							  <tbody>
								  <tr class="participantRow">
                    <td><input  type="text" style="width:130px;text-align:left;" name="popupITEMID[]" id="popupITEMID_0" onclick="getItem(this.id)" class="form-control"  autocomplete="off"  readonly/></td>

                    <td hidden><input type="hidden" name="ITEMID_REF[]" id="ITEMID_REF_0" class="form-control" autocomplete="off" /></td>

                    <td><input type="text" style="width:130px;text-align:left;" name="ItemName[]" id="ItemName_0" class="form-control"  autocomplete="off"  readonly  /></td>

                    <td><input type="text" style="width:130px;text-align:left;" name="popupMUOM[]" id="popupMUOM_0" onclick="getUomMaster(this.id)" class="form-control"  autocomplete="off"  readonly /></td>

                    <td hidden><input type="hidden" name="MAIN_UOMID_REF[]" id="MAIN_UOMID_REF_0" class="form-control"  autocomplete="off" /></td>

                    <td><input type="text" style="width:130px;text-align:right;" onkeypress="return isNumberDecimalKey(event,this)"    name="QTY[]" id="QTY_0" class="form-control three-digits"     onkeyup="dataCalculation(this.id)" onfocusout="dataDec(this,'2')" maxlength="13"  autocomplete="off"   /></td>

                    <td><input type="text"  name="RATEPUOM[]" id="RATEPUOM_0"  onkeypress="return isNumberDecimalKey(event,this)"  class="form-control five-digits blurRate" maxlength="13"  autocomplete="off" style="width:130px;text-align:right;" onkeyup="dataCalculation(this.id)" onfocusout="dataDec(this,'5')" /></td>

                    <td><input  type="text" name="DISCPER[]" onkeypress="return isNumberDecimalKey(event,this)"  id="DISCPER_0" class="form-control four-digits" maxlength="8"  autocomplete="off" style="width:130px;text-align:right;" onkeyup="dataCalculation(this.id)" onfocusout="dataDec(this,'2')" /></td>

                    <td><input  type="text" name="DISCOUNT_AMT[]" id="DISCOUNT_AMT_0" onkeypress="return isNumberDecimalKey(event,this)"  class="form-control two-digits" maxlength="15"  autocomplete="off"  style="width:130px;text-align:right;" onkeyup="dataCalculation(this.id)" onfocusout="dataDec(this,'2')" /></td>

                    <td><input type="text" name="DISAFTT_AMT[]" id="DISAFTT_AMT_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly style="width:130px;text-align:right;" /></td>

                    <td><input type="text" name="IGST[]" id="IGST_0" class="form-control four-digits" maxlength="8"  autocomplete="off"  readonly onkeyup="dataCalculation(this.id)"/></td>

                    <td><input type="text" name="IGSTAMT[]" id="IGSTAMT_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly style="width:130px;text-align:right;"/></td>

                    <td><input type="text" name="CGST[]" id="CGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly style="width:130px;text-align:right;"  onkeyup="dataCalculation(this.id)" /></td>

                    <td><input type="text" name="CGSTAMT[]" id="CGSTAMT_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly style="width:130px;text-align:right;" /></td>

                    <td><input type="text" name="SGST[]" id="SGST_0" class="form-control four-digits" maxlength="8" autocomplete="off"  readonly  style="width:130px;text-align:right;" onkeyup="dataCalculation(this.id)" /></td>

                   <td><input type="text" name="SGSTAMT[]" id="SGSTAMT_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly style="width:130px;text-align:right;"/></td>

                   <td><input type="text" name="TGST_AMT[]" id="TGST_AMT_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly style="width:130px;text-align:right;" /></td>

                   <td><input type="text" name="TOT_AMT[]" id="TOT_AMT_0" class="form-control two-digits" maxlength="15" autocomplete="off"  readonly style="width:130px;text-align:right;" /></td>
                    
                  <td align="center" >
                    <button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
                    <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>
                  </td>

								  </tr>
							  </tbody>
					    </table>
					  </div>	
				  </div>

          <div id="Payment" class="tab-pane fade in ">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px; width: 786px"  >
                <table id="example3" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                          <tr>
                              <th width="12%">Select Mode</th>
                              <th width="12%">No/Description</th>
                              <th width="12%">Amount</th>
                              <th width="8%">Action</th>
                          </tr>
                    </thead>
                    <tbody>
                          <tr  class="participantRow2">
                              <td><select name="PAYMENT_TYPE[]" id="PAYMENT_TYPE_0" class="form-control" onchange="getDocType(this.id);">
                              <option value="">Select</option>
                              <option value="Cash">Cash</option>
                              <!--<option value="Value Card">Value Card</option>-->
                              <option value="Credit Card">Credit Card</option> 
                              <option value="FOC">FOC</option>
                              <option value="UPI">UPI</option>
                              </select></td>      
                              <td>
                              <input type="text" name="DESCRIPTION[]" id="DESCRIPTION_0"   class="form-control"  autocomplete="off"  onclick="getValueCardMaster(this.id)" />
                              </td>
                              <td hidden>
                              <input type="hidden" name="VALUEID_REF[]" id="VALUEID_REF_0"      class="form-control"  autocomplete="off" />  

                              </td>

                     
                              <td><input type="text" name="PAID_AMT[]" id="PAID_AMT_0" onkeyup="getPaymentAmount(this.id,this.value)"  onfocusout="dataDec(this,'2')" class="form-control two-digits"  autocomplete="off"  /></td>
                             
                              <td align="center" ><button class="btn add ainvoice" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                              <button class="btn remove dinvoice" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
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
          <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData"><div id="alert-active" class="activeYes"></div>Yes</button>
          <button class="btn alertbt" name='NoBtn' id="NoBtn"   data-funcname="fnUndoNo" ><div id="alert-active" class="activeNo"></div>No</button>
          <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk"></div>OK</button>
          <button class="btn alertbt" name='OkBtn1' id="OkBtn1" onclick="getFocus()" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk1"></div>OK</button>
          <input type="hidden" id="FocusId" >
        </div>
		  <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="modal" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:50%;" >
    <div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" onclick="closeEvent('modal')" >&times;</button></div>
      <div class="modal-body">
	      <div class="tablename"><p id='modal_title'></p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="modal_table1" class="display nowrap table  table-striped table-bordered" >
            <thead>
              <tr>
                <th style="width:10%;">Select</th> 
                <th style="width:45%;" id='modal_th1'></th>
                <th style="width:45%;" id='modal_th2'></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th style="width:10%;"></th>
                <td style="width:45%;"><input type="text" id="text1" class="form-control" autocomplete="off" onkeyup="searchData(1)"></td>
                <td style="width:45%;"><input type="text" id="text2" class="form-control" autocomplete="off" onkeyup="searchData(2)"></td>
              </tr>
            </tbody>
          </table>

          <table id="modal_table2" class="display nowrap table  table-striped table-bordered" >
            <tbody id="modal_body" style="font-size:14px;"></tbody>
          </table>
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>



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

<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:80%">
    <div class="modal-content" >
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button></div>
      <div class="modal-body">
	      <div class="tablename"><p>Item Details</p></div>
	      <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar filter">
          <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead>
              <tr id="none-select" class="searchalldata" hidden>
                <td> 
                  <input type="hidden" id="hdn_ItemID"/>
                </td>
              </tr>

              <tr>
                <th style="width:10%;" id="all-check">Select</th>
                <th style="width:20%;">Item Code</th>
                <th style="width:20%;">Name</th>
                <th style="width:10%;">Main UOM</th>
                <th style="width:10%;">Rate</th>
                <th style="width:10%;">Item Group</th>
                <th style="width:10%;">Item Category</th>
                <th style="width:10%;">Business Unit</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th style="width:10%;"></th>
                <td style="width:20%;"><input type="text" id="Itemcodesearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
                <td style="width:20%;"><input type="text" id="Itemnamesearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
                <td style="width:10%;"><input type="text" id="ItemUOMsearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
                <td style="width:10%;"><input type="text" id="ItemQTYsearch" class="form-control" autocomplete="off" readonly></td>
                <td style="width:10%;"><input type="text" id="ItemGroupsearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
                <td style="width:10%;"><input type="text" id="ItemCategorysearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
                <td style="width:10%;"><input type="text" id="ItemBUsearch" class="form-control" autocomplete="off" onkeyup="searchItem(event)"></td>
              </tr>
            </tbody>
          </table>

          <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width:100%" >
            <thead id="thead2"></thead>
            <tbody id="tbody_ItemID" style="font-size: 13px">
              <div class="loader" style="display:none;"></div>
            </tbody>
          </table>

        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('bottom-scripts')
<script>
$(document).ready(function() {

});
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

let tid1    = "#modal_table1";
let tid2    = "#modal_table2";
let headers = document.querySelectorAll(tid1 + " th");

headers.forEach(function(element, i) {
  element.addEventListener("click", function() {
    w3.sortHTML(tid2, ".clsipoid", "td:nth-child(" + (i + 1) + ")");
  });
});

function searchData(cno){
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById('text'+cno);
  filter = input.value.toUpperCase();
  table = document.getElementById("modal_table2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[cno];
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

function closeEvent(id){
  $("#"+id).hide();
}


function get_customer_type(value){
  $("#SEARCH_CUSTOMER").val('');
  $("#SEARCH_CUSTOMER").prop('readonly',false);
  $("#CUSTOMER_NAME").prop('readonly',true);
  if(value ==='NEW'){
    $("#SEARCH_CUSTOMER").prop('readonly',true);
    $("#CUSTOMER_NAME").prop('readonly',false);
  }

  $('input:text').val('');
  $('input:hidden').val('');
  $('#DOC_NO').val("{{$docarray['DOC_NO']}}");
}


function searchCustomerMaster(){

  if($('input[name="CUSTOMER_TYPE"]:checked').val() == "EXIST"){

    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
      url:'{{route("transaction",[$FormId,"searchCustomer"])}}',
      type:'POST',
      success:function(data) {
        var html = '';

        if(data.length > 0){
          $.each(data, function(key, value) {
            html +='<tr>';
            html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindCustomerMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" data-f1="'+value.REGADDL1+'" data-f2="'+value.COUNTRY_ID+'" data-f3="'+value.COUNTRY_NAME+'" data-f4="'+value.STATE_ID+'" data-f5="'+value.STATE_NAME+'" data-f6="'+value.CITY_ID+'" data-f7="'+value.CITY_NAME+'" data-f8="'+value.REGPIN+'" data-f9="'+value.EMAILID+'" data-f10="'+value.PHNO+'" data-f11="'+value.MONO+'" data-f12="'+value.GSTTYPE+'" data-f13="'+value.GSTIN+'" ></td>';
            html +='<td style="width:45%;" >'+value.DATA_CODE+' - '+value.DATA_DESC+'</td>';
            html +='<td style="width:45%;" >'+value.MONO+'</td>';
            html +='</tr>';
          });
        }
        else{
          html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
        }

        $("#modal_body").html(html);
      },
      error: function (request, status, error) {
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(request.responseText);
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
      },
    });

    $("#modal_title").text('Customer Master');
    $("#modal_th1").text('Customer Code & Name');
    $("#modal_th2").text('Mobile No');
    $("#modal").show();
  }

}

function bindCustomerMaster(data){
var code          = $("#"+data.id).data("code");
var desc          = $("#"+data.id).data("desc");

var address       = $("#"+data.id).data("f1");
var country_id    = $("#"+data.id).data("f2");
var country_name  = $("#"+data.id).data("f3");
var state_id      = $("#"+data.id).data("f4");
var state_name    = $("#"+data.id).data("f5");
var city_id       = $("#"+data.id).data("f6");
var city_name     = $("#"+data.id).data("f7");
var pincode       = $("#"+data.id).data("f8");
var email         = $("#"+data.id).data("f9");
var phno          = $("#"+data.id).data("f10");
var mobile_no     = $("#"+data.id).data("f11");
var gst_type      = $("#"+data.id).data("f12");
var gstin         = $("#"+data.id).data("f13");

$("#CUSTOMER_ID").val(data.value);
$("#CUSTOMER_NAME").val(code+' - '+desc);

$("#ADDRESS").val(address);
$("#COUNTRY_ID").val(country_id);
$("#COUNTRY_NAME").val(country_name);
$("#STATE_ID").val(state_id);
$("#STATE_NAME").val(state_name);
$("#CITY_ID").val(city_id);
$("#CITY_NAME").val(city_name);
$("#PINCODE").val(pincode);
$("#EMAIL_ID").val(email);
$("#LANDLINE_NO").val(phno);
$("#MOBILE_NO").val(mobile_no);
$("#GST_TYPE").val(gst_type);
$("#GST_IN").val(gstin);

$("#text1").val(''); 
$("#text2").val(''); 
$("#modal_body").html('');  
$("#modal").hide(); 
}

function getCountryMaster(){
$.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$.ajax({
  url:'{{route("transaction",[$FormId,"getCountryMaster"])}}',
  type:'POST',
  success:function(data) {
    var html = '';

    if(data.length > 0){
      $.each(data, function(key, value) {
        html +='<tr>';
        html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindCountryMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" ></td>';
        html +='<td style="width:45%;" >'+value.DATA_CODE+'</td>';
        html +='<td style="width:45%;" >'+value.DATA_DESC+'</td>';
        html +='</tr>';
      });
    }
    else{
      html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
    }

    $("#modal_body").html(html);
  },
  error: function (request, status, error) {
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").show();
    $("#AlertMessage").text(request.responseText);
    $("#alert").modal('show');
    $("#OkBtn").focus();
    highlighFocusBtn('activeOk');
    $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
  },
});

$("#modal_title").text('Country Master');
$("#modal_th1").text('Code');
$("#modal_th2").text('Desc');
$("#modal").show();
}

function getCountryMaster(){
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getCountryMaster"])}}',
    type:'POST',
    success:function(data) {
      var html = '';

      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr>';
          html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindCountryMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" ></td>';
          html +='<td style="width:45%;" >'+value.DATA_CODE+'</td>';
          html +='<td style="width:45%;" >'+value.DATA_DESC+'</td>';
          html +='</tr>';
        });
      }
      else{
        html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
      }

      $("#modal_body").html(html);
    },
    error: function (request, status, error) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
      $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
    },
  });

  $("#modal_title").text('Country Master');
  $("#modal_th1").text('Code');
  $("#modal_th2").text('Desc');
  $("#modal").show();
}

function bindCountryMaster(data){
  var code  = $("#"+data.id).data("code");
  var desc  = $("#"+data.id).data("desc");

  $("#COUNTRY_ID").val(data.value);
  $("#COUNTRY_NAME").val(code+' - '+desc);
  
  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#STATE_NAME").val(''); 
  $("#CITY_NAME").val(''); 
  $("#STATE_ID").val(''); 
  $("#CITY_ID").val(''); 
  $("#modal_body").html('');  
  $("#modal").hide(); 
}

function getStateMaster(){

  var COUNTRY_ID  = $("#COUNTRY_ID").val();
 
  if(COUNTRY_ID ===""){
    $("#FocusId").val('COUNTRY_NAME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select country.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else{

    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
      url:'{{route("transaction",[$FormId,"getStateMaster"])}}',
      type:'POST',
      data:{COUNTRY_ID:COUNTRY_ID},
      success:function(data) {
        var html = '';

        if(data.length > 0){
          $.each(data, function(key, value) {
            html +='<tr>';
            html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindStateMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" ></td>';
            html +='<td style="width:45%;" >'+value.DATA_CODE+'</td>';
            html +='<td style="width:45%;" >'+value.DATA_DESC+'</td>';
            html +='</tr>';
          });
        }
        else{
          html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
        }

        $("#modal_body").html(html);
      },
      error: function (request, status, error) {
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(request.responseText);
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
      },
    });

    $("#modal_title").text('State Master');
    $("#modal_th1").text('Code');
    $("#modal_th2").text('Desc');
    $("#modal").show();

  }
}

function bindStateMaster(data){
  var code  = $("#"+data.id).data("code");
  var desc  = $("#"+data.id).data("desc");

  $("#STATE_ID").val(data.value);
  $("#STATE_NAME").val(code+' - '+desc);
  
  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#CITY_NAME").val(''); 
  $("#CITY_ID").val(''); 
  $("#modal_body").html('');  
  $("#modal").hide(); 
}

function getCityMaster(){

  var COUNTRY_ID  = $("#COUNTRY_ID").val();
  var STATE_ID    = $("#STATE_ID").val();

  if(COUNTRY_ID ===""){
    $("#FocusId").val('COUNTRY_NAME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select country.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else if(STATE_ID ===""){
    $("#FocusId").val('STATE_NAME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select state.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else{

    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
      url:'{{route("transaction",[$FormId,"getCityMaster"])}}',
      type:'POST',
      data:{COUNTRY_ID:COUNTRY_ID,STATE_ID:STATE_ID},
      success:function(data) {
        var html = '';

        if(data.length > 0){
          $.each(data, function(key, value) {
            html +='<tr>';
            html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindCityMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" ></td>';
            html +='<td style="width:45%;" >'+value.DATA_CODE+'</td>';
            html +='<td style="width:45%;" >'+value.DATA_DESC+'</td>';
            html +='</tr>';
          });
        }
        else{
          html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
        }

        $("#modal_body").html(html);
      },
      error: function (request, status, error) {
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(request.responseText);
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
      },
    });

    $("#modal_title").text('City Master');
    $("#modal_th1").text('Code');
    $("#modal_th2").text('Desc');
    $("#modal").show();

  }
}

function bindCityMaster(data){
  var code  = $("#"+data.id).data("code");
  var desc  = $("#"+data.id).data("desc");

  $("#CITY_ID").val(data.value);
  $("#CITY_NAME").val(code+' - '+desc);

  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  $("#modal").hide(); 
}

function getVehicleMakeMaster(){
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getVehicleMakeMaster"])}}',
    type:'POST',
    success:function(data) {
      var html = '';

      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr>';
          html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindVehicleMakeMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" ></td>';
          html +='<td style="width:45%;" >'+value.DATA_CODE+'</td>';
          html +='<td style="width:45%;" >'+value.DATA_DESC+'</td>';
          html +='</tr>';
        });
      }
      else{
        html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
      }

      $("#modal_body").html(html);
    },
    error: function (request, status, error) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
      $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
    },
  });

  $("#modal_title").text('Vehicle Make Master');
  $("#modal_th1").text('Code');
  $("#modal_th2").text('Desc');
  $("#modal").show();
}

function bindVehicleMakeMaster(data){
  var code  = $("#"+data.id).data("code");
  var desc  = $("#"+data.id).data("desc");

  $("#VEHICLE_MAKE_ID").val(data.value);
  $("#VEHICLE_MAKE_NAME").val(code+' - '+desc);
  
  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  $("#modal").hide(); 
}



function getValueCardMaster(id){
  var PAYMENT_TYPE=$("#PAYMENT_TYPE_"+id.split('_').pop(0)).val(); 
  if(PAYMENT_TYPE !="Value Card"){
    return false; 

  }

  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getValueCardMaster"])}}',
    type:'POST',

    success:function(data) {
      var html = '';
      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr>';
          html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindValueCardMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" data-rowid="'+id+'" ></td>';
          html +='<td style="width:45%;" >'+value.DATA_CODE+'</td>';
          html +='<td style="width:45%;" >'+value.DATA_DESC+'</td>';
          html +='</tr>';
        });
      }
      else{
        html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
      }

      $("#modal_body").html(html);
    },
    error: function (request, status, error) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
      $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
    },
  });

  $("#modal_title").text('Value Card List');
  $("#modal_th1").text('Card No');
  $("#modal_th2").text('Balance');
  $("#modal").show();
}

function bindValueCardMaster(data){
  var code  = $("#"+data.id).data("code");
  var desc  = $("#"+data.id).data("desc");
  var rowid  = $("#"+data.id).data("rowid");







    var CheckExist_valueid = [];

    $('#example3').find('.participantRow2').each(function(){

      if($(this).find('[id*="VALUEID_REF"]').val() != ''){

        var valueid  = $(this).find('[id*="VALUEID_REF"]').val();
    
          if(valueid!=''){
            CheckExist_valueid.push(valueid);
          }

      }
    });

    if($.inArray(data.value, CheckExist_valueid) !== -1 ){
      $(this).find('[id*="txtSTR_popup"]').val();
      $(this).find('[id*="STRID_REF"]').val();
      $("#VALUEID_REF_"+rowid.split('_').pop(0)).val('');
      $("#DESCRIPTION_"+rowid.split('_').pop(0)).val('');
      $("#FocusId").val("#DESCRIPTION_"+rowid);
      $("#alert").modal('show');
      $("#AlertMessage").text('Value Master already exist.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk');
      $("#modal").hide(); 
      return false;
    }
    else{
    $("#VALUEID_REF_"+rowid.split('_').pop(0)).val(data.value);
    $("#DESCRIPTION_"+rowid.split('_').pop(0)).val(code);

    }
  
  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  var CheckExist_valueid = [];
  $("#modal").hide(); 
}



function getItem(id){


  var CustType=$('input[name="CUSTOMER_TYPE"]:checked').val();
  if(CustType=="EXIST"){
    CustId=$("#CUSTOMER_ID").val(); 
  }else{
    CustId=$("#STATE_ID").val(); 
  } 
  
  if(CustType=="EXIST" && CustId==""){
  $("#FocusId").val('SEARCH_CUSTOMER');        
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please Enter Mobile No and Search Customer .');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
  }else if(CustType=="NEW" && CustId==""){
    $("#FocusId").val('STATE_NAME');        
  $("#YesBtn").hide();
  $("#NoBtn").hide();
  $("#OkBtn1").show();
  $("#AlertMessage").text('Please Select State First .');
  $("#alert").modal('show');
  $("#OkBtn1").focus();
  return false;
  }
  GetTaxType(CustId,CustType);


  loadItem();
  $('#hdn_ItemID').val(id.split('_').pop());  
  $('.js-selectall1').prop('checked',false); 
  $("#ITEMIDpopup").show();
}

function searchItem(e) {
  if(e.which == 13){
    loadItem()
  }
}

function loadItem(){

   var taxstate    = $("#TAX_TYPE").val(); 
    var CODE        = $.trim($("#Itemcodesearch").val()); 
    var NAME        = $.trim($("#Itemnamesearch").val()); 
    var MUOM        = $.trim($("#ItemUOMsearch").val()); 
    var GROUP       = $.trim($("#ItemGroupsearch").val()); 
    var CTGRY       = $.trim($("#ItemCategorysearch").val()); 
    var BUNIT       = $.trim($("#ItemBUsearch").val()); 
    var APART       = ''; 
    var CPART       = ''; 
    var OPART       = ''; 
  
  $("#tbody_ItemID").html('<tr><td colspan="11">Please wait your request is under process ...</td></tr>');

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"loadItem"])}}',
    type:'POST',
    data:{'taxstate':taxstate,'CODE':CODE,'NAME':NAME,'MUOM':MUOM,'GROUP':GROUP,'CTGRY':CTGRY,'BUNIT':BUNIT,'APART':APART,'CPART':CPART,'OPART':OPART},
    success:function(data) {
      var html = '';

      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr class="clsitemid">';
          html +='<td style="width:10%;"><input type="checkbox" id="chkId'+key+'"  value="'+key+'" class="js-selectall1"  ></td>';
          html +='<td style="width:20%;">'+value.ICODE+'</td>';
          html +='<td style="width:20%;" id="itemname_'+key+'" >'+value.INAME+'</td>';
          html +='<td style="width:10%;" id="itemuom_'+key+'" >'+value.UOMCODE+'</td>';
          html +='<td style="width:10%;" id="uomqty_'+key+'" >'+value.STDCOST+'</td>';
          html +='<td style="width:10%;" id="irate_'+key+'">'+value.GROUPCODE+'</td>';
          html +='<td style="width:10%;" id="itax_'+key+'">'+value.ICCODE+'</td>';
          html +='<td style="width:10%;">'+value.BUCODE+'</td>';
         
          html +='<td hidden>';
          html +='<input type="text" id="uniquerowid_'+key+'" value='+value.ITEMID+' >';
          html +='<input type="text" id="txt_item_id_'+key+'" value='+value.ITEMID+' >';
          html +='<input type="text" id="txt_item_code_'+key+'" value='+value.ICODE+' >';
          html +='<input type="text" id="txt_item_name_'+key+'" value='+value.INAME+' >';
          html +='<input type="text" id="txt_main_uom_id_'+key+'" value='+value.MAIN_UOMID_REF+' >';
          html +='<input type="text" id="txt_main_uom_code_'+key+'" value='+value.UOMCODE+' >';
          html +='<input type="text" id="txt_item_rate_'+key+'" value='+value.STDCOST+' >';
          html +='<input type="text" id="txt_Taxid1_'+key+'" value='+value.Taxid1+' >';

          html +='<input type="text" id="txt_Taxid2_'+key+'" value='+value.Taxid2+' >';
          html +='</td>';

          html +='</tr>';
        });
      }
      else{
        html +='<tr><td colspan="8"> Record not found.</td></tr>'; 
      }

      $("#tbody_ItemID").html(html);
      bindItemEvents(); 
    },
    error: function (request, status, error) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
      $("#tbody_ItemID").html('');                        
    },
  });
}

$("#ITEMID_closePopup").click(function(event){
  $("#ITEMIDpopup").hide();
  resetItemPopup();
});

function bindItemEvents(){
  $('#ItemIDTable2').off(); 
  $('[id*="chkId"]').change(function(){

    var index         = $(this).val();
    var item_id       = $("#txt_item_id_"+index).val();
    var item_code     = $("#txt_item_code_"+index).val();
    var item_name     = $("#txt_item_name_"+index).val();
    var main_uom_id   = $("#txt_main_uom_id_"+index).val();
    var main_uom_code = $("#txt_main_uom_code_"+index).val();
    var item_rate     = $("#txt_item_rate_"+index).val();
    var uniquerowid   = $("#uniquerowid_"+index).val();
    var txt_Taxid1    =  $("#txt_Taxid1_"+index).val();
    var txt_Taxid2    =  $("#txt_Taxid2_"+index).val();
    var row_id        = $('#hdn_ItemID').val();
    
    if($(this).is(":checked") == true){
        var checkExist  = false;
        $('#example2').find('.participantRow').each(function(){
          if(uniquerowid == $(this).find('[id*="ITEMID_REF_"]').val()){
            checkExist  = true;
          }            
        });  
    
      if(checkExist ==true){
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
        return false;  
      }                            
      else{
        $('#ITEMID_REF_'+row_id).val(item_id);
        $('#popupITEMID_'+row_id).val(item_code);
        $('#ItemName_'+row_id).val(item_name);
        $('#MAIN_UOMID_REF_'+row_id).val(main_uom_id);
        $('#popupMUOM_'+row_id).val(main_uom_code);

        if($("#TAX_TYPE").val()!="OutofState"){
        $('#CGST_'+row_id).val(txt_Taxid1);
        $('#SGST_'+row_id).val(txt_Taxid2);
        $('#IGST_'+row_id).val('0.00');
        }else{
        $('#CGST_'+row_id).val('0.00');
        $('#SGST_'+row_id).val('0.00');
        $('#IGST_'+row_id).val(txt_Taxid1);
        }
       
        $('#QTY_'+row_id).val("");
        $('#DISCPER_'+row_id).val("");
        $('#DISCOUNT_AMT_'+row_id).val("");
        $('#DISAFTT_AMT_'+row_id).val("");
        $('#IGSTAMT_'+row_id).val("");
        $('#CGSTAMT_'+row_id).val("");
        $('#SGSTAMT_'+row_id).val("");
        $('#CGSTAMT_'+row_id).val("");
        $('#TGST_AMT_'+row_id).val("");
        $('#TOT_AMT_'+row_id).val("");
        bindTotalValue();
        bindRatePerUoMEvents(item_id,row_id);
      }

      $('#hdn_ItemID').val('');
      $("#ITEMIDpopup").hide();
      
    }

  });
  resetItemPopup();
}

function bindRatePerUoMEvents(item_id,row_id){

    $.ajaxSetup({
      headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    
    $.ajax({
      url:'{{route("transaction",[$FormId,"getRatePerUoM"])}}',
      type:'POST',
      data:{ITEMIDREF:item_id},
      success:function(data) {             
        $('#RATEPUOM_'+row_id).val(data);
      },
      error:function(data){
        console.log("Error: Something went wrong.");
      },
    });
}

function resetItemPopup(){
  $("#Itemcodesearch").val(''); 
  $("#Itemnamesearch").val(''); 
  $("#ItemUOMsearch").val(''); 
  $("#ItemQTYsearch").val(''); 
  $("#ItemGroupsearch").val(''); 
  $("#ItemCategorysearch").val(''); 
  $("#ItemBUsearch").val(''); 
  $('.remove').removeAttr('disabled'); 
}

$("#Material").on('click', '.remove', function(){
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
    }
    bindTotalValue(); 
    event.preventDefault();
});

$("#Material").on('click', '.add', function(){
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

  });

  $clone.find('input:text').val('');
  $clone.find('input:hidden').val('');

  $tr.closest('table').append($clone);         
  $clone.find('.remove').removeAttr('disabled'); 
  event.preventDefault();
});

function getUomMaster(textid){

  var row_no      = textid.split('_').pop();
  var ITEMID_REF  = $.trim($("#ITEMID_REF_"+row_no).val());

  if(ITEMID_REF ===""){
    $("#FocusId").val('popupITEMID_'+row_no);        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select item.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  } 
  else{

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      url:'{{route("transaction",[$FormId,"getUomMaster"])}}',
      type:'POST',
      data:{ITEMID_REF:ITEMID_REF},
      success:function(data) {
        var html = '';

        if(data.length > 0){
          $.each(data, function(key, value) {

            html +='<tr>';
            html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindUomMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'"  data-textid="'+textid+'" ></td>';
            html +='<td style="width:45%;" >'+value.DATA_CODE+'</td>';
            html +='<td style="width:45%;" >'+value.DATA_DESC+'</td>';
            html +='</tr>';

          });
        }
        else{
          html +='<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>';
        }

        $("#modal_body").html(html);
      },
      error: function (request, status, error) {
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(request.responseText);
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        $("#material_data").html('<tr><td colspan="3" style="text-align:center;">No data available in table</td></tr>');                       
      },
    });

    $("#modal_title").text('UOM Master');
    $("#modal_th1").text('Code');
    $("#modal_th2").text('Description');
    $("#modal").show();

  }
}

function bindUomMaster(data){
  
  var textid          = $("#"+data.id).data("textid");
  var textid          = textid.split('_').pop();
  var code            = $("#"+data.id).data("code");
  var desc            = $("#"+data.id).data("desc");
 
  $("#MAIN_UOMID_REF_"+textid).val(data.value);
  $("#popupMUOM_"+textid).val(code,' - ',desc);
 
  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  $("#modal").hide(); 
}

function saveAction(action){
  validateForm(action);
}

function validateForm(action){


  var flag_exist    = [];
  var flag_status   = [];
  var flag_focus    = '';
  var flag_message  = '';
  var flag_tab_type = '';


  var input1 = document.getElementsByName('PAYMENT_TYPE[]');
  for (var i = 0; i < input1.length; i++) {

    var PAYMENT_TYPE = $.trim(document.getElementsByName('PAYMENT_TYPE[]')[i].value);
  
    if(PAYMENT_TYPE ===""){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('PAYMENT_TYPE[]')[i].id;
      flag_message  = 'Please select mode';
      flag_tab_type = 'PAYMENT_TAB';
    }
    else if($.trim(document.getElementsByName('DESCRIPTION[]')[i].value) ===""){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('DESCRIPTION[]')[i].id;
      flag_message  = 'Description should not be blank. ';
      flag_tab_type = 'PAYMENT_TAB';
    }
    else if($.trim(document.getElementsByName('PAID_AMT[]')[i].value) ===""){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('PAID_AMT[]')[i].id;
      flag_message  = 'Please enter amount';
      flag_tab_type = 'PAYMENT_TAB';
    }
  
    else if(jQuery.inArray(PAYMENT_TYPE, flag_exist) !== -1){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('PAYMENT_TYPE[]')[i].id;
      flag_message  = 'This payment mode is already exist';
      flag_tab_type = 'MAT_TAB';
    }

    flag_exist.push(PAYMENT_TYPE);

  }


  var input = document.getElementsByName('ITEMID_REF[]');
  for (var i = 0; i < input.length; i++) {

    var ITEMID_REF = $.trim(document.getElementsByName('ITEMID_REF[]')[i].value);
  
    if(ITEMID_REF ===""){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('popupITEMID[]')[i].id;
      flag_message  = 'Please select item';
      flag_tab_type = 'MAT_TAB';
    }
    else if($.trim(document.getElementsByName('MAIN_UOMID_REF[]')[i].value) ===""){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('popupMUOM[]')[i].id;
      flag_message  = 'Please select uom';
      flag_tab_type = 'MAT_TAB';
    }
    else if($.trim(document.getElementsByName('QTY[]')[i].value) ===""){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('QTY[]')[i].id;
      flag_message  = 'Please enter qty';
      flag_tab_type = 'MAT_TAB';
    }
    else if($.trim(document.getElementsByName('RATEPUOM[]')[i].value) ===""){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('RATEPUOM[]')[i].id;
      flag_message  = 'Please enter rate per UOM';
      flag_tab_type = 'MAT_TAB';
    }
    else if(jQuery.inArray(ITEMID_REF, flag_exist) !== -1){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('ITEMID_REF[]')[i].id;
      flag_message  = 'This item is already exist';
      flag_tab_type = 'MAT_TAB';
    }

    flag_exist.push(ITEMID_REF);

  }

  if($.trim($("#DOC_NO").val()) ===""){
    $("#FocusId").val('DOC_NO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please enter document no.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#DOC_DATE").val()) ===""){
    $("#FocusId").val('DOC_DATE');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select document date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#CUSTOMER_NAME").val()) ===""){
    $("#FocusId").val('CUSTOMER_NAME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Customer Name.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#EMAIL_ID").val()) ===""){
    $("#FocusId").val('EMAIL_ID');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter E-Mail Id.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#MOBILE_NO").val()) ===""){
    $("#FocusId").val('MOBILE_NO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Mobile No.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($('input[name="CUSTOMER_TYPE"]:checked').val() == "NEW" && exist_customer_by_mobile_no($.trim($("#MOBILE_NO").val()),'') > 0 ){
    $("#FocusId").val('MOBILE_NO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Mobile No Already Exist.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#ADDRESS").val()) ===""){
    $("#FocusId").val('ADDRESS');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Address.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#COUNTRY_NAME").val()) ===""){
    $("#FocusId").val('COUNTRY_NAME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Country.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#STATE_NAME").val()) ===""){
    $("#FocusId").val('STATE_NAME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select State.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#CITY_NAME").val()) ===""){
    $("#FocusId").val('CITY_NAME');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select City.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#PINCODE").val()) ===""){
    $("#FocusId").val('PINCODE');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Pin Code.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#GST_TYPE").val()) ===""){
    $("#FocusId").val('GST_TYPE');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter GST Type.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#VEHICLE_REG_NO").val()) ===""){
    $("#FocusId").val('VEHICLE_REG_NO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Vehicle Reg No.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  // else if($.trim($("#VEHICLE_MAKE_NAME").val()) ===""){
  //   $("#FocusId").val('VEHICLE_MAKE_NAME');        
  //   $("#YesBtn").hide();
  //   $("#NoBtn").hide();
  //   $("#OkBtn1").show();
  //   $("#AlertMessage").text('Please Enter Vehicle Make.');
  //   $("#alert").modal('show');
  //   $("#OkBtn1").focus();
  //   return false;
  // }

 
 
  else if(jQuery.inArray("false", flag_status) !== -1){
    $("#"+flag_tab_type).click();
    $("#FocusId").val(flag_focus);        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text(flag_message);
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }   
  else if(parseFloat($("#TotalValue").val())!=parseFloat($("#TotalValue_paid").val())){
    $("#FocusId").val('TotalValue');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Net amount and paid amount should be equal.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }  
  
  else{
    $("#alert").modal('show');
    $("#AlertMessage").text('Do you want to '+action+' to record.');
    $("#YesBtn").data("funcname","fnSaveData");
    $("#YesBtn").data("action",action);
    $("#OkBtn1").hide();
    $("#OkBtn").hide();
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#YesBtn").focus();
    highlighFocusBtn('activeYes');
  }
}

$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName  = $("#YesBtn").data("funcname");
  var action        = $("#YesBtn").data("action");

  if(action ==="save"){
    window[customFnName]('{{route("transaction",[$FormId,"save"])}}');
  }
  else if(action ==="update"){
    window[customFnName]('{{route("transaction",[$FormId,"update"])}}');
  }
  else if(action ==="approve"){
    window[customFnName]('{{route("transaction",[$FormId,"Approve"])}}');
  }
  else{
    window.location.href = '{{route("transaction",[$FormId,"index"]) }}';
  }
});

window.fnSaveData = function (path){

  event.preventDefault();
  var trnsoForm = $("#transaction_form");
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
    url:path,
    type:'POST',
    data:formData,
    success:function(data) {
      $(".buttonload").hide(); 
      $("#btnSaveFormData").show();   
      $("#btnApprove").prop("disabled", false);
       
      if(data.success){                   
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(data.msg);
        $(".text-danger").hide();
        $("#alert").modal('show');
        $("#OkBtn").focus();
      }
      else{                   
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(data.msg);
        $(".text-danger").hide();
        $("#alert").modal('show');
        $("#OkBtn1").focus();
      } 
    },
    error: function (request, status, error){
      $(".buttonload").hide(); 
      $("#btnSaveFormData").show();   
      $("#btnApprove").prop("disabled", false);
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1');
    },
  });
}

$("#NoBtn").click(function(){
  $("#alert").modal('hide');
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

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}



//============================================Calculations================================================
function dataDec(data,no){
  var text_value  = data.value !=''?parseFloat(data.value).toFixed(no):'';
  $("#"+data.id).val(text_value);
}

function dataCalculation(id){
var index             = id.split('_').pop();
var totalvalue        = 0;
var discount_amount   = 0;

var quantity          = $("#QTY_"+index).val() !=''?parseFloat($("#QTY_"+index).val()):0;



var itemid            = $("#ITEMID_REF_"+index).val();
         
var quantity1          = $("#QTY_"+index).val() !=''?parseFloat($("#QTY_"+index).val()):0;
var rate1              = $("#RATEPUOM_"+index).val() !=''?parseFloat($("#RATEPUOM_"+index).val()):0;
var amount1            = parseFloat(quantity1*rate1).toFixed(2);

var discount_percent  = $("#DISCPER_"+index).val() !=''?parseFloat($("#DISCPER_"+index).val()):0;
var discount_amount   = $("#DISCOUNT_AMT_"+index).val() !=''?parseFloat($("#DISCOUNT_AMT_"+index).val()):0;

if(id === "DISCPER_"+index){
  var discount_amount   = parseFloat((parseFloat(amount1)*parseFloat(discount_percent))/100).toFixed(2);
  $("#DISCOUNT_AMT_"+index).val(discount_amount);
}
else if(id === "DISCOUNT_AMT_"+index){
  var discount_percent  = parseFloat((parseFloat(discount_amount)*100/parseFloat(amount1))).toFixed(2);
  $("#DISCPER_"+index).val(discount_percent);
}

var amount1        = amount1 > 0?parseFloat(parseFloat(amount1) - parseFloat(discount_amount)).toFixed(2):0;   
var igst          = $("#IGST_"+index).val() !=''?parseFloat($("#IGST_"+index).val()):0;
var cgst          = $("#CGST_"+index).val() !=''?parseFloat($("#CGST_"+index).val()):0;
var sgst          = $("#SGST_"+index).val() !=''?parseFloat($("#SGST_"+index).val()):0;

var igst_amount   = igst > 0?parseFloat((amount1 * igst)/100).toFixed(2):0;
var cgst_amount   = cgst > 0?parseFloat((amount1 * cgst)/100).toFixed(2):0;
var sgst_amount   = sgst > 0?parseFloat((amount1 * sgst)/100).toFixed(2):0;

var tax_amount    = parseFloat(parseFloat(igst_amount) + parseFloat(cgst_amount) + parseFloat(sgst_amount)).toFixed(2); 
var total_amount  = parseFloat(parseFloat(amount1) + parseFloat(tax_amount)).toFixed(2);


$("#DISAFTT_AMT_"+index).val(parseFloat(amount1).toFixed(2));
$("#TOT_AMT_"+index).val(parseFloat(total_amount).toFixed(2));
$("#TGST_AMT_"+index).val(parseFloat(tax_amount).toFixed(2));

$("#IGST_"+index).val(parseFloat(igst).toFixed(2));
$("#CGST_"+index).val(parseFloat(cgst).toFixed(2));
$("#SGST_"+index).val(parseFloat(sgst).toFixed(2));

$("#IGSTAMT_"+index).val(parseFloat(igst_amount).toFixed(2));
$("#CGSTAMT_"+index).val(parseFloat(cgst_amount).toFixed(2));
$("#SGSTAMT_"+index).val(parseFloat(sgst_amount).toFixed(2));

bindTotalValue();
event.preventDefault();
}


function bindTotalValue(){
  
  var totalvalue  = 0.00;
  var tvalue      = 0.00;

  var totalvalue_tax  = 0.00;
  var tvalue_tax      = 0.00;

  var totalvalue_paid  = 0.00;
  var tvalue_paid      = 0.00;
  
  $('#Material').find('.participantRow').each(function(){
    tvalue      = $(this).find('[id*="TOT_AMT"]').val() !=''?$(this).find('[id*="TOT_AMT"]').val():0;
    totalvalue  = parseFloat(totalvalue) + parseFloat(tvalue);
    totalvalue  = parseFloat(totalvalue).toFixed(2);

    tvalue_tax      = $(this).find('[id*="TGST_AMT"]').val() !=''?$(this).find('[id*="TGST_AMT"]').val():0;
    totalvalue_tax  = parseFloat(totalvalue_tax) + parseFloat(tvalue_tax);
    totalvalue_tax  = parseFloat(totalvalue_tax).toFixed(2);
  });

  $('#Payment').find('.participantRow2').each(function(){
    tvalue_paid      = $(this).find('[id*="PAID_AMT"]').val() !=''?$(this).find('[id*="PAID_AMT"]').val():0;
    totalvalue_paid  = parseFloat(totalvalue_paid) + parseFloat(tvalue_paid);
    totalvalue_paid  = parseFloat(totalvalue_paid).toFixed(2);
  });

  MultiCurrency_Conversion('TotalValue');

  $('#TotalValue').val(totalvalue);

  $('#TotalValue_tax').val(totalvalue_tax);
  $('#PAID_AMT_0').val(totalvalue);
  $('#TotalValue_paid').val(totalvalue); 

}










$("#Payment").on('click', '.dinvoice', function(){
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

$("#Payment").on('click', '.ainvoice', function(){
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

  });

  $clone.find('input:text').val('');
  $clone.find('input:hidden').val('');

  $tr.closest('table').append($clone);         
  $clone.find('.dinvoice').removeAttr('disabled'); 
  event.preventDefault();
});



function getDocType(id){
var rowid = id.split('_').pop(0);
var PAYMENTTYPE = $("#PAYMENT_TYPE_"+rowid).val(); 
if(PAYMENTTYPE=="Value Card"){
  $("#DESCRIPTION_"+rowid).prop("readonly", true);
  $("#PAID_AMT_"+rowid).val("");  
  $("#DESCRIPTION_"+rowid).val("");
}else{
  $("#DESCRIPTION_"+rowid).prop("readonly", false);
  $("#VALUEID_REF_"+rowid).val("");
  $("#DESCRIPTION_"+rowid).val("");
  $("#PAID_AMT_"+rowid).val("");

}
bindTotalValue();
}


function getPaymentAmount(textid,value){  

  var tvalue      = 0.00;
  var totalvalue  = 0.00;  
  $('#Payment').find('.participantRow2').each(function(){
    tvalue      = $(this).find('[id*="PAID_AMT"]').val() !=''?$(this).find('[id*="PAID_AMT"]').val():0;
    totalvalue  = parseFloat(totalvalue) + parseFloat(tvalue);
    totalvalue  = parseFloat(totalvalue).toFixed(2);
  });

  $('#TotalValue_paid').val(totalvalue);
  
  // if($("#PAYMENT_TYPE_"+textid.split("_").pop(0)).val()=="Value Card"){
  // Get_Balance(textid,value);
  // }
  // bindTotalValue(); 
  
}

function Get_Balance(textid,value){

  var DESCRIPTION =$('#DESCRIPTION_'+textid.split("_").pop(0));  
  var CARDID_REF =$("#VALUEID_REF_"+textid.split("_").pop(0)).val(); 

  if($('#VALUEID_REF_'+textid.split("_").pop(0)).val() ==""){
        
    $("#FocusId").val('DESCRIPTION_'+textid.split("_").pop(0) );        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select value card.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    $("#PAID_AMT_"+textid.split("_").pop(0)).val('');
    return false;
  }

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  var balance = $.ajax({type: 'POST',
  url:'{{route("transaction",[$FormId,"Get_Card_Balance"])}}',
  async: false,
  dataType: 'json',
  data: {CARDID_REF:CARDID_REF},
  done: function(response) {return response;}}).responseText;
  if(parseFloat(balance) < parseFloat(value)){   
    $("#FocusId").val('PAID_AMT_'+textid.split("_").pop(0) );        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Amount should not be greater than current balance.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    $("#PAID_AMT_"+textid.split("_").pop(0)).val('');
    return false;
  }  
}



function GetTaxType(CustId,CustType){

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

var TaxType = $.ajax({type: 'POST',
url:'{{route("transaction",[$FormId,"GetTaxType"])}}',
async: false,
dataType: 'json',
data: {CustId:CustId,CustType:CustType},
done: function(response) {return response;}}).responseText;

$("#TAX_TYPE").val(TaxType); 
}

function validate_mobile_no(){
  if($('input[name="CUSTOMER_TYPE"]:checked').val() == "NEW" && $.trim($("#MOBILE_NO").val()).length >= 10 ){
    if(exist_customer_by_mobile_no($.trim($("#MOBILE_NO").val()),'') > 0 ){
      $("#FocusId").val('MOBILE_NO');        
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('Mobile No Already Exist.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }
  }
}



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
        MultiCurrency_Conversion('TotalValue'); 
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
	  MultiCurrency_Conversion('TotalValue'); 
  });


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