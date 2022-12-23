@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Service Invoice</a></div>
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
  </div>
</div>

<form id="form_data" method="POST"  >
  <div class="container-fluid purchase-order-view">    
    @csrf
    <div class="container-fluid filter">
      <div class="inner-form"> 
        
        <div class="row">
          <div class="col-lg-2 pl"><p>Service Invoice No*</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="hidden"  name="DOC_ID"  id="DOC_ID" value="{{isset($HDR->SIID)?$HDR->SIID:''}}" >
            <input {{$ActionStatus}} type="text"    name="DOC_NO"  id="DOC_NO"  value="{{isset($HDR->SI_NO)?$HDR->SI_NO:''}}"  class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
          </div>
                            
          <div class="col-lg-2 pl"><p>Invoice Date*</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="date" name="DOC_DATE" id="DOC_DATE" value="{{isset($HDR->SI_DATE)?$HDR->SI_DATE:''}}"  class="form-control" autocomplete="off" placeholder="dd/mm/yyyy" readonly >
          </div>

          <div class="col-lg-2 pl"><p>Job Card No*</p></div>
          <div class="col-lg-2 pl"> 
            <input {{$ActionStatus}} type="text"    name="JOB_NO"   id="JOB_NO" value="{{isset($HDR->JOB_NO)?$HDR->JOB_NO:''}}"   class="form-control" autocomplete="off" onclick="getJobCard()" readonly >
            <input {{$ActionStatus}} type="hidden"  name="JEID_REF" id="JEID_REF" value="{{isset($HDR->JEID_REF)?$HDR->JEID_REF:''}}" class="form-control" autocomplete="off" >
          </div>

        </div>

        <div class="row">
          <div class="col-lg-2 pl"><p>Job Card Date</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="date" name="JOB_DATE" id="JOB_DATE" value="{{isset($HDR->JOB_DATE)?$HDR->JOB_DATE:''}}" class="form-control" autocomplete="off" readonly>
          </div>

          <div class="col-lg-2 pl"><p>Customer Name</p></div>
          <div class="col-lg-2 pl">
            <input {{$ActionStatus}} type="text" name="CUSTOMER_NAME" id="CUSTOMER_NAME" value="{{isset($HDR->CUSTOMER_NAME)?$HDR->CUSTOMER_NAME:''}}" class="form-control" autocomplete="off" readonly>
            <input {{$ActionStatus}} type="hidden" name="CUSTOMER_ID" id="CUSTOMER_ID" value="{{isset($HDR->CUSTOMER_ID)?$HDR->CUSTOMER_ID:''}}" class="form-control" autocomplete="off" > 
            <input {{$ActionStatus}} type="hidden" name="HSNID_REF" id="HSNID_REF" value="{{isset($HDR->HSNID_REF)?$HDR->HSNID_REF:''}}" class="form-control" autocomplete="off" > 
          </div>

          <div class="col-lg-2 pl"><p>Mobile No</p></div>
          <div class="col-lg-2 pl"> 
            <input {{$ActionStatus}} type="text" name="MOBILE_NO" id="MOBILE_NO" value="{{isset($HDR->MOBILE_NO)?$HDR->MOBILE_NO:''}}" class="form-control" autocomplete="off" placeholder='Mobile No' maxlength="12"  readonly >
          </div>

        </div>

        <div class="row">
          <div class="col-lg-2 pl"><p>Address</p></div>
          <div class="col-lg-2 pl"> 
            <input {{$ActionStatus}} type="text" name="ADDRESS" id="ADDRESS" value="{{isset($HDR->ADDRESS)?$HDR->ADDRESS:''}}" class="form-control" autocomplete="off" readonly >
          </div>

          <div class="col-lg-2 pl"><p>Landline No</p></div>
          <div class="col-lg-2 pl"> 
            <input {{$ActionStatus}} type="text" name="LANDLINE_NO" id="LANDLINE_NO" value="{{isset($HDR->LANDLINE_NO)?$HDR->LANDLINE_NO:''}}" class="form-control" autocomplete="off" readonly >
          </div>
        </div>

        <div class="row"><br/></div>

        <div class="row">
          <div class="col-lg-8 pl"></div>
          <div class="col-lg-2 pl"><p>Package Amount</p></div>
          <div class="col-lg-2 pl"> 
            <input {{$ActionStatus}} type="text" name="TOTAL_PACKAGE_AMOUNT" id="TOTAL_PACKAGE_AMOUNT" value="{{isset($HDR->TOTAL_PACKAGE_AMOUNT)?number_format($HDR->TOTAL_PACKAGE_AMOUNT, 2, '.', ''):''}}" class="form-control" autocomplete="off" readonly >
          </div>
        </div>

        <div class="row">
          <div class="col-lg-8 pl"></div>
          <div class="col-lg-2 pl"><p>Discount Amount</p></div>
          <div class="col-lg-2 pl"> 
            <input {{$ActionStatus}} type="text" name="TOTAL_DISCOUONT_AMOUNT" id="TOTAL_DISCOUONT_AMOUNT" value="{{isset($HDR->TOTAL_DISCOUONT_AMOUNT)?number_format($HDR->TOTAL_DISCOUONT_AMOUNT, 2, '.', ''):''}}" class="form-control" autocomplete="off" readonly >
          </div>
        </div>

        <div class="row">
          <div class="col-lg-8 pl"></div>
          <div class="col-lg-2 pl"><p>Tax Amount</p></div>
          <div class="col-lg-2 pl"> 
            <input {{$ActionStatus}} type="text" name="TOTAL_TAX_AMOUNT" id="TOTAL_TAX_AMOUNT" value="{{isset($HDR->TOTAL_TAX_AMOUNT)?number_format($HDR->TOTAL_TAX_AMOUNT, 2, '.', ''):''}}" class="form-control" autocomplete="off" readonly >
          </div>
        </div>

        <div class="row">
          <div class="col-lg-8 pl"></div>
          <div class="col-lg-2 pl"><p>Net Amount</p></div>
          <div class="col-lg-2 pl"> 
            <input {{$ActionStatus}} type="text" name="TOTAL_NET_AMOUNT" id="TOTAL_NET_AMOUNT" value="{{isset($HDR->TOTAL_NET_AMOUNT)?number_format($HDR->TOTAL_NET_AMOUNT, 2, '.', ''):''}}" class="form-control" autocomplete="off" readonly >
          </div>
        </div>

        <div class="row">
          <div class="col-lg-8 pl"></div>
          <div class="col-lg-2 pl"><p>Paid Amount</p></div>
          <div class="col-lg-2 pl"> 
            <input {{$ActionStatus}} type="text" name="TOTAL_PAID_AMOUNT" id="TOTAL_PAID_AMOUNT" value="{{isset($HDR->TOTAL_PAID_AMOUNT)?number_format($HDR->TOTAL_PAID_AMOUNT, 2, '.', ''):''}}" class="form-control" autocomplete="off" readonly >
          </div>
        </div>

      </div>

      <div class="container-fluid purchase-order-view">
        <div class="row">
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#Package" id="PACKAGE_TAB">Package</a></li>
            <li><a data-toggle="tab" href="#Discount" id="DISCOUNT_TAB">Discount</a></li>
            <li><a data-toggle="tab" href="#Tax" id="TAX_TAB">Tax</a></li>
            <li><a data-toggle="tab" href="#Payment" id="PAYMENT_TAB">Payment</a></li>
            <li><a data-toggle="tab" href="#udf" id="UDF_TAB">UDF</a></li>
          </ul>
                                              
          <div class="tab-content">
            <div id="Package" class="tab-pane fade in active">
              <div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;" >
                <table class="display nowrap table table-striped table-bordered itemlist w-200" style="height:auto !important;width:50%">
                  <thead>
                    <tr>
                      <th>Sr.No</th>
                      <th>Package</th>
                      <th>Amount</th>
                    </tr>
                  </thead>
                  <tbody id="package_data" >
                    @if(isset($PKG) && !empty($PKG))
                    @foreach($PKG as $key=>$row)
                    <tr class="participantRow">
                      <td style="text-align:center;">{{$key+1}}</td>
                      <td><input        type="text" name="PACKAGE_NAME[]" id="PACKAGE_NAME_{{$key}}" value="{{isset($row->PACKAGE_NAME)?$row->PACKAGE_NAME:''}}"  class="form-control"  autocomplete="off" readonly {{$ActionStatus}}  /></td>
                      <td hidden><input type="text" name="PACKAGE_ID[]"   id="PACKAGE_ID_{{$key}}"   value="{{isset($row->PACKAGE_ID)?$row->PACKAGE_ID:''}}"      class="form-control"  autocomplete="off" {{$ActionStatus}} /></td>
                      <td><input        type="text" name="AMOUNT[]"       id="AMOUNT_{{$key}}"       value="{{isset($row->AMOUNT)?$row->AMOUNT:''}}"              class="form-control"  autocomplete="off" readonly {{$ActionStatus}} /></td>
                    </tr>
                    @endforeach
                    @endif
                  </tbody>
                </table>
              </div>
            </div>

            <div id="Discount" class="tab-pane fade">
            <div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;">
              <table class="display nowrap table table-striped table-bordered itemlist w-200" style="height:auto !important;width:80%">
                <thead>
                  <tr>
                    <th>Discount</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Amount</th>
                    <th>Action</th>
                  </tr>
                </thead>
							  <tbody>
                  @if(isset($DIS) && !empty($DIS))
                  @foreach($DIS as $key=>$row)
                  <tr class="discountRow">
                    <td><input        type="text" name="DISCOUNT_NAME[]"    id="DISCOUNT_NAME_{{$key}}"   value="{{isset($row->DISCOUNT_NAME)?$row->DISCOUNT_NAME:''}}"     class="form-control"  autocomplete="off"  onclick="getDiscountMaster(this.id)" readonly {{$ActionStatus}}  /></td>
                    <td hidden><input type="text" name="DISID_REF[]"        id="DISID_REF_{{$key}}"       value="{{isset($row->DISID_REF)?$row->DISID_REF:''}}"             class="form-control"  autocomplete="off" readonly {{$ActionStatus}} /></td>
                    <td><input        type="text" name="DISCOUNT_TYPE[]"    id="DISCOUNT_TYPE_{{$key}}"   value="{{isset($row->DISCOUNT_TYPE)?$row->DISCOUNT_TYPE:''}}"     class="form-control"  autocomplete="off" readonly {{$ActionStatus}} /></td>
                    <td><input        type="text" name="DISCOUNT_VALUE[]"   id="DISCOUNT_VALUE_{{$key}}"  value="{{isset($row->DISCOUNT_VALUE)?$row->DISCOUNT_VALUE:''}}"   class="form-control"  autocomplete="off" readonly {{$ActionStatus}} /></td>
                    <td><input        type="text" name="DISCOUNT_AMOUNT[]"  id="DISCOUNT_AMOUNT_{{$key}}" value="{{isset($row->DISCOUNT_AMOUNT)?$row->DISCOUNT_AMOUNT:''}}" class="form-control"  autocomplete="off" readonly {{$ActionStatus}} /></td>
                    <td align="center" >
                      <button disabled class="btn add material" title="add" data-toggle="tooltip" type="button" {{$ActionStatus}} ><i class="fa fa-plus"></i></button>
                      <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button" {{$ActionStatus}} ><i class="fa fa-trash" ></i></button>
                    </td>
                  </tr>
                  @endforeach
                  @else
                  <tr class="discountRow">
                    <td><input        type="text" name="DISCOUNT_NAME[]"    id="DISCOUNT_NAME_0"    class="form-control"  autocomplete="off"  onclick="getDiscountMaster(this.id)" readonly {{$ActionStatus}}  /></td>
                    <td hidden><input type="text" name="DISID_REF[]"        id="DISID_REF_0"        class="form-control"  autocomplete="off" readonly {{$ActionStatus}} /></td>
                    <td><input        type="text" name="DISCOUNT_TYPE[]"    id="DISCOUNT_TYPE_0"    class="form-control"  autocomplete="off" readonly {{$ActionStatus}} /></td>
                    <td><input        type="text" name="DISCOUNT_VALUE[]"   id="DISCOUNT_VALUE_0"   class="form-control"  autocomplete="off" readonly {{$ActionStatus}} /></td>
                    <td><input        type="text" name="DISCOUNT_AMOUNT[]"  id="DISCOUNT_AMOUNT_0"  class="form-control"  autocomplete="off" readonly {{$ActionStatus}} /></td>
                    <td align="center" >
                      <button disabled class="btn add material" title="add" data-toggle="tooltip" type="button" {{$ActionStatus}} ><i class="fa fa-plus"></i></button>
                      <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button" {{$ActionStatus}} ><i class="fa fa-trash" ></i></button>
                    </td>
                  </tr>
                  @endif
                </tbody>
					    </table>
					  </div>	
				  </div>

          <div id="Tax" class="tab-pane fade">
            <div class="table-responsive table-wrapper-scroll-y" style="height:280px;margin-top:10px;">
              <table class="display nowrap table table-striped table-bordered itemlist w-200" style="height:auto !important;width:50%">
                <thead>
                  <tr>
                    <th>Tax Description</th>
                    <th>Tax (%)</th>
                    <th>Tax Amount</th>
                    <th>Action</th>
                  </tr>
                </thead>
							  <tbody id="tax_data">
                  @if(isset($TAX) && !empty($TAX))
                  @foreach($TAX as $key=>$row)
                  <tr class="taxRow">
                    <td><input  type="text" name="TAX_NAME[]"   id="TAX_NAME_{{$key}}"    value="{{isset($row->TAX_NAME)?$row->TAX_NAME:''}}"     class="form-control"  autocomplete="off" {{$ActionStatus}} readonly /></td>
                    <td><input  type="text" name="TAX_PER[]"    id="TAX_PER_{{$key}}"     value="{{isset($row->TAX_PER)?$row->TAX_PER:''}}"       class="form-control"  autocomplete="off" onkeyup="getTaxAmount(this.id,this.value)" onkeypress="return isNumberDecimalKey(event,this)" {{$ActionStatus}} /></td>
                    <td><input  type="text" name="TAX_AMOUNT[]" id="TAX_AMOUNT_{{$key}}"  value="{{isset($row->TAX_AMOUNT)?$row->TAX_AMOUNT:''}}" class="form-control"  autocomplete="off" readonly {{$ActionStatus}} /></td>
                    <td align="center" >
                      <button class="btn add material" title="add" data-toggle="tooltip" type="button" {{$ActionStatus}} ><i class="fa fa-plus"></i></button>
                      <button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button" {{$ActionStatus}} ><i class="fa fa-trash" ></i></button>
                    </td>
                  </tr>
                  @endforeach
                  @endif
                </tbody>
					    </table>
					  </div>	
				  </div>

          <div id="Payment" class="tab-pane fade in ">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px; width: 786px"  >
              <table id="example3" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0">
                  <tr>
                    <th>Select Mode</th>
                    <th>No/Description</th>
                    <th>Amount</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @if(isset($PAY) && !empty($PAY))
                  @foreach($PAY as $key=>$row)
                  <tr class="participantRow2">
                    <td>
                      <select {{$ActionStatus}} name="PAYMENT_TYPE[]" id="PAYMENT_TYPE_{{$key}}" class="form-control" onchange="getDocType(this.id);">
                        <option  value="">Select</option>
                        <option {{isset($row->PAYMENT_TYPE) && $row->PAYMENT_TYPE =='Cash'?'selected="selected"':''}} value="Cash">Cash</option>
                        <option {{isset($row->PAYMENT_TYPE) && $row->PAYMENT_TYPE =='Value Card'?'selected="selected"':''}} value="Value Card">Value Card</option>
                        <option {{isset($row->PAYMENT_TYPE) && $row->PAYMENT_TYPE =='Credit Card'?'selected="selected"':''}} value="Credit Card">Credit Card</option> 
                        <option {{isset($row->PAYMENT_TYPE) && $row->PAYMENT_TYPE =='FOC'?'selected="selected"':''}} value="FOC">FOC</option>
                        <option {{isset($row->PAYMENT_TYPE) && $row->PAYMENT_TYPE =='UPI'?'selected="selected"':''}} value="UPI">UPI</option>
                      </select>
                    </td>

                    <td><input        type="text"   name="DESCRIPTION[]"  id="DESCRIPTION_{{$key}}" value="{{isset($row->DESCRIPTION)?$row->DESCRIPTION:''}}" class="form-control"  autocomplete="off"  onclick="getValueCardMaster(this.id)" {{isset($row->PAYMENT_TYPE) && $row->PAYMENT_TYPE =='Value Card'?'readonly':''}} {{$ActionStatus}} /></td>
                    <td hidden><input type="hidden" name="VALUEID_REF[]"  id="VALUEID_REF_{{$key}}" value="{{isset($row->VALUEID_REF)?$row->VALUEID_REF:''}}" class="form-control"  autocomplete="off" {{$ActionStatus}} /></td>
                    <td><input        type="text"   name="PAID_AMT[]"     id="PAID_AMT_{{$key}}"    value="{{isset($row->PAID_AMT)?$row->PAID_AMT:''}}"       class="form-control two-digits" onkeyup="getPaymentAmount(this.id,this.value)" onkeypress="return isNumberDecimalKey(event,this)"  onfocusout="dataDec(this,'2')"   autocomplete="off" {{$ActionStatus}}  /></td>
                             
                    <td align="center" >
                      <button class="btn add ainvoice" title="add" data-toggle="tooltip" type="button" {{$ActionStatus}}><i class="fa fa-plus"></i></button>
                      <button class="btn remove dinvoice" title="Delete" data-toggle="tooltip" type="button" {{$ActionStatus}}><i class="fa fa-trash" ></i></button>
                    </td>
                  </tr>
                  @endforeach
                  @endif     
                </tbody>
              </table>
            </div>	
          </div>

            <div id="udf" class="tab-pane fade">
              <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
                <table id="example4" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                  <thead id="thead1"  style="position: sticky;top: 0">
                    <tr>
                      <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3" value="{{count($objUdf)}}"></th>
                      <th>Value / Comments</th>
                    </tr>
                  </thead>                         
                  <tbody>
                    @foreach($objUdf as $udfkey => $udfrow)
                    <tr  class="participantRow4">
                      <td><input name={{"udffie_popup_".$udfkey}} id={{"txtudffie_popup_".$udfkey}} value="{{$udfrow->LABEL}}" class="form-control @if ($udfrow->ISMANDATORY==1) mandatory @endif" autocomplete="off" maxlength="100" disabled/></td>
                      <td hidden><input type="text" name='{{"udffie_".$udfkey}}' id='{{"hdnudffie_popup_".$udfkey}}' value="{{$udfrow->UDFID}}" class="form-control" maxlength="100" /></td>
                      <td hidden><input type="text" name={{"udffieismandatory_".$udfkey}} id={{"udffieismandatory_".$udfkey}} class="form-control" maxlength="100" value="{{$udfrow->ISMANDATORY}}" /></td>            
                      <td id="{{"tdinputid_".$udfkey}}">
                      @php
                      $dynamicid  = "udfvalue_".$udfkey;
                      $chkvaltype = strtolower($udfrow->VALUETYPE); 
                      $udf_value  = isset($udfrow->UDF_VALUE)?$udfrow->UDF_VALUE:'';
    
                      if($chkvaltype=='date'){
                        $strinp = '<input '.$ActionStatus.' type="date" placeholder="dd/mm/yyyy" name="'.$dynamicid.'" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control" value="" /> ';       
                      }
                      else if($chkvaltype=='time'){
                        $strinp= '<input '.$ActionStatus.' type="time" placeholder="h:i" name="'.$dynamicid.'" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control"  value=""/> ';
                      }
                      else if($chkvaltype=='numeric'){
                        $strinp = '<input '.$ActionStatus.' type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control" value=""  autocomplete="off" /> ';
                      }
                      else if($chkvaltype=='text'){
                        $strinp = '<input '.$ActionStatus.' type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" value="'.$udf_value.'" class="form-control" value=""  autocomplete="off" /> ';
                      }
                      else if($chkvaltype=='boolean'){

                        $boolval = ''; 
                        if($udf_value =='on' || $udf_value  =='1'){
                          $boolval="checked";
                        }

                        $strinp = '<input '.$ActionStatus.' type="checkbox" name="'.$dynamicid. '" id="'.$dynamicid.'"  '.$boolval.' class=""  /> ';
                      }
                      else if($chkvaltype=='combobox'){
                        $strinp       ='';
                        $txtoptscombo = strtoupper($udfrow->DESCRIPTIONS); ;
                        $strarray     = explode(',',$txtoptscombo);
                        $opts         = '';
                        $chked        ='';

                        for ($i = 0; $i < count($strarray); $i++) {
                          $chked='';
                          if($strarray[$i]==$udf_value){
                            $chked='selected="selected"';
                          }

                          $opts = $opts.'<option value="'.$strarray[$i].'" '.$chked.'  >'.$strarray[$i].'</option> ';
                        }

                        $strinp = '<select '.$ActionStatus.' name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" >'.$opts.'</select>' ;
                      }
                      echo $strinp;
                      @endphp
                      </td>
                    </tr>
                    @endforeach                             
                  </tbody>
                </table>
              </div>
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
@endsection
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

function getJobCard(){

  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getJobCard"])}}',
    type:'POST',
    success:function(data) {
      var html = '';

      if(data.length > 0){
        $.each(data, function(key, value) {
          html +='<tr>';
          html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindJobCard(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" data-f1="'+value.CUSTOMER_ID+'" data-f2="'+value.CUSTOMER_NAME+'" data-f3="'+value.MOBILE_NO+'" data-f4="'+value.ADDRESS+'" data-f5="'+value.LANDLINE_NO+'" data-f6="'+value.TOTAL+'" ></td>';
          html +='<td style="width:45%;" >'+value.DATA_CODE+'</td>';
          html +='<td style="width:45%;" >'+value.CUSTOMER_NAME+'</td>';
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

  $("#modal_title").text('Job Card');
  $("#modal_th1").text('Job Card Code');
  $("#modal_th2").text('Customer Name');
  $("#modal").show();
  
}

function bindJobCard(data){

  var JEID_REF      = data.value;
  var JOB_NO        = $("#"+data.id).data("code");
  var JOB_DATE      = $("#"+data.id).data("desc");
  var CUSTOMER_ID   = $("#"+data.id).data("f1");
  var CUSTOMER_NAME = $("#"+data.id).data("f2");
  var MOBILE_NO     = $("#"+data.id).data("f3");
  var ADDRESS       = $("#"+data.id).data("f4");
  var LANDLINE_NO   = $("#"+data.id).data("f5");
  var TOTAL         = $("#"+data.id).data("f6");
  
  $("#JEID_REF").val(JEID_REF);
  $("#JOB_NO").val(JOB_NO);
  $("#JOB_DATE").val(JOB_DATE);
  $("#CUSTOMER_ID").val(CUSTOMER_ID);
  $("#CUSTOMER_NAME").val(CUSTOMER_NAME);
  $("#MOBILE_NO").val(MOBILE_NO);
  $("#ADDRESS").val(ADDRESS);
  $("#LANDLINE_NO").val(LANDLINE_NO);
  $("#TOTAL_PACKAGE_AMOUNT").val(TOTAL);

  if(data.value !=''){
    loadPackage(data.value);
  }

  resetTab();

  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  $("#modal").hide(); 
}

function loadPackage(JEID_REF){

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"loadPackage"])}}',
    type:'POST',
    data:{JEID_REF:JEID_REF},
    success:function(data) {
      var html      = '';
      var HSNID_REF = '';

      if(data.length > 0){
        $.each(data, function(key, value) {

          if(key ==0){
            HSNID_REF=value.HSNID_REF;
            $("#HSNID_REF").val(HSNID_REF);
          }

          html +='<tr class="participantRow">';
          html +='<td style="text-align:center;">'+(key+1)+'</td>';
          html +='<td><input        type="text" name="PACKAGE_NAME[]" id="PACKAGE_NAME_'+key+'" value="'+value.PACKAGE_NAME+'"  class="form-control"  autocomplete="off" readonly  /></td>';
          html +='<td hidden><input type="text" name="PACKAGE_ID[]"   id="PACKAGE_ID_'+key+'"   value="'+value.PAMID_REF+'"     class="form-control"  autocomplete="off" /></td>';
          html +='<td><input        type="text" name="AMOUNT[]"       id="AMOUNT_'+key+'"       value="'+value.AMOUNT+'"        class="form-control"  autocomplete="off" readonly /></td>';
          html +='</tr>';

        });
      }
      else{
        html +='<tr class="participantRow">';
        html +='<td style="text-align:center;">1</td>';
        html +='<td><input        type="text" name="PACKAGE_NAME[]" id="PACKAGE_NAME_0"  class="form-control"  autocomplete="off" readonly  /></td>';
        html +='<td hidden><input type="text" name="PACKAGE_ID[]"   id="PACKAGE_ID_0"    class="form-control"  autocomplete="off" /></td>';
        html +='<td><input        type="text" name="AMOUNT[]"       id="AMOUNT_0"        class="form-control"  autocomplete="off" readonly /></td>';
        html +='</tr>';
      }
      
      
      $("#package_data").html(html);
      loadTax();
    },
    error: function (request, status, error) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
      $("#package_data").html('');                       
    },
  });
}

function loadTax(){

  var CUSTOMER_ID = $("#CUSTOMER_ID").val();
  var HSNID_REF   = $("#HSNID_REF").val();

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"loadTax"])}}',
    type:'POST',
    data:{CUSTOMER_ID:CUSTOMER_ID,HSNID_REF:HSNID_REF},
    success:function(data) {
      var html = '';

      if(data.length > 0){
        $.each(data, function(key, value){

          var TAX_PER         = parseFloat(value.TAX_RATE);
          var PACKAGE_TOTAL   = $("#TOTAL_PACKAGE_AMOUNT").val();
          var PACKAGE_TOTAL   = PACKAGE_TOTAL !=''?parseFloat(PACKAGE_TOTAL):0;
          var TOTAL_AMOUNT    = ((PACKAGE_TOTAL*TAX_PER)/100);

          html +='<tr class="taxRow">';
          html +='<td><input        type="text" name="TAX_NAME[]"  id="TAX_NAME_'+key+'" value="'+value.TAX_TYPE+'"  class="form-control"  autocomplete="off" readonly /></td>';
          html +='<td><input        type="text" name="TAX_PER[]"  id="TAX_PER_'+key+'" value="'+value.TAX_RATE+'"  class="form-control"  autocomplete="off" onkeyup="getTaxAmount(this.id,this.value)" onkeypress="return isNumberDecimalKey(event,this)" /></td>';
          html +='<td><input        type="text" name="TAX_AMOUNT[]" id="TAX_AMOUNT_'+key+'" value="'+TOTAL_AMOUNT+'" class="form-control"  autocomplete="off" readonly /></td>';
          html +='<td align="center" >';
          html +='<button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>';
          html +='<button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>';
          html +='</td>';
          html +='</tr>';

        });
      }
      else{
        html +='<tr class="taxRow">';
        html +='<td><input        type="text" name="TAX_NAME[]"  id="TAX_NAME_0"   class="form-control"  autocomplete="off" readonly /></td>';
        html +='<td><input        type="text" name="TAX_PER[]"  id="TAX_PER_0"   class="form-control"  autocomplete="off" onkeyup="getTaxAmount(this.id,this.value)" onkeypress="return isNumberDecimalKey(event,this)" /></td>';
        html +='<td><input        type="text" name="TAX_AMOUNT[]" id="TAX_AMOUNT_0"  class="form-control"  autocomplete="off" readonly /></td>';
        html +='<td align="center" >';
        html +='<button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>';
        html +='<button class="btn remove dmaterial" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button>';
        html +='</td>';
        html +='</tr>';
      }

      $("#tax_data").html(html);
      get_total_amount('TAX_AMOUNT','TOTAL_TAX_AMOUNT');
    },
    error: function (request, status, error) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
      $("#package_data").html('');                       
    },
  });
}

function getDiscountMaster(textid){

  if($("#TOTAL_PACKAGE_AMOUNT").val() ===""){
    $("#FocusId").val('JOB_NO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please select Job Card No for package.');
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
      url:'{{route("transaction",[$FormId,"getDiscountMaster"])}}',
      type:'POST',
      success:function(data) {
        var html = '';

        if(data.length > 0){
          $.each(data, function(key, value) {

            html +='<tr>';
            html +='<td style="width:10%;text-align:center;" ><input type="checkbox" id="key_'+key+'" value="'+value.DATA_ID+'" onChange="bindDiscountMaster(this)" data-code="'+value.DATA_CODE+'" data-desc="'+value.DATA_DESC+'" data-f1="'+value.DIS_OPT+'" data-f2="'+value.DIS_PERCENT+'" data-f3="'+value.DIS_AMT+'" data-textid="'+textid+'" ></td>';
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

    $("#modal_title").text('Package Master');
    $("#modal_th1").text('Code');
    $("#modal_th2").text('Desc');
    $("#modal").show();

  }
}

function bindDiscountMaster(data){
  
  var textid          = $("#"+data.id).data("textid");
  var textid          = textid.split('_').pop();
  var code            = $("#"+data.id).data("code");
  var desc            = $("#"+data.id).data("desc");

  var DIS_OPT         = $("#"+data.id).data("f1");
  var DIS_PERCENT     = $("#"+data.id).data("f2");
  var DIS_AMT         = $("#"+data.id).data("f3");

  $("#DISID_REF_"+textid).val(data.value);
  $("#DISCOUNT_NAME_"+textid).val(code+' - '+desc);
  $("#DISCOUNT_TYPE_"+textid).val(DIS_OPT);

  if(DIS_OPT ==="Percentatge"){
    var DISCOUNT_PER    = DIS_PERCENT !=''?parseFloat(DIS_PERCENT):0;
    var PACKAGE_TOTAL   = $("#TOTAL_PACKAGE_AMOUNT").val();
    var PACKAGE_TOTAL   = PACKAGE_TOTAL !=''?parseFloat(PACKAGE_TOTAL):0;
    var TOTAL_AMOUNT    = ((PACKAGE_TOTAL*DISCOUNT_PER)/100);

    $("#DISCOUNT_VALUE_"+textid).val(DIS_PERCENT);
    $("#DISCOUNT_AMOUNT_"+textid).val(parseFloat(TOTAL_AMOUNT).toFixed(2));
  }
  else{
    $("#DISCOUNT_VALUE_"+textid).val(parseFloat(DIS_AMT).toFixed(2));
    $("#DISCOUNT_AMOUNT_"+textid).val(parseFloat(DIS_AMT).toFixed(2));
  }

  get_total_amount('DISCOUNT_AMOUNT','TOTAL_DISCOUONT_AMOUNT');

  $("#text1").val(''); 
  $("#text2").val(''); 
  $("#modal_body").html('');  
  $("#modal").hide(); 
}

$("#Discount").on('click', '.remove', function(){
    var rowCount = $(this).closest('table').find('.discountRow').length;
    if (rowCount > 1) {
    $(this).closest('.discountRow').remove();  
    get_total_amount('DISCOUNT_AMOUNT','TOTAL_DISCOUONT_AMOUNT');   
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

$("#Discount").on('click', '.add', function(){
  var $tr = $(this).closest('table');
  var allTrs = $tr.find('.discountRow').last();
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


$("#Tax").on('click', '.remove', function(){
    var rowCount = $(this).closest('table').find('.taxRow').length;
    if (rowCount > 1) {
    $(this).closest('.taxRow').remove();  
    get_total_amount('TAX_AMOUNT','TOTAL_TAX_AMOUNT');  
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

$("#Tax").on('click', '.add', function(){
  var $tr = $(this).closest('table');
  var allTrs = $tr.find('.taxRow').last();
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

$("#Payment").on('click', '.dinvoice', function(){
    var rowCount = $(this).closest('table').find('.participantRow2').length;
    if (rowCount > 1) {
    $(this).closest('.participantRow2').remove();   
    get_total_amount('PAID_AMT','TOTAL_PAID_AMOUNT');   
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

function saveAction(action){
  validateForm(action);
}

function validateForm(action){

  var flag_exist    = [];
  var flag_status   = [];
  var flag_focus    = '';
  var flag_message  = '';
  var flag_tab_type = '';

  $("[id*=txtudffie_popup]").each(function(){
    if($.trim($(this).val())!=""){
      if($.trim($(this).parent().parent().find('[id*="udffieismandatory"]').val()) == "1"){
        if($.trim($(this).parent().parent().find('[id*="udfvalue"]').val()) != ""){
          flag_status.push('true');
        }
        else{
          flag_status.push('false');
          flag_focus    = $(this).parent().parent().find('[id*="udfvalue"]').attr('id');
          flag_message  = 'Please enter  Value / Comment in UDF Tab';
          flag_tab_type = 'UDF_TAB';
        }
      }             
    }             
  });

  for (var i = 0; i < document.getElementsByName('PAYMENT_TYPE[]').length; i++) {
    var payment_type = $.trim(document.getElementsByName('PAYMENT_TYPE[]')[i].value);
    if(payment_type ===""){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('PAYMENT_TYPE[]')[i].id;
      flag_message  = 'Please select mode';
      flag_tab_type = 'PAYMENT_TAB';
    }
    else if($.trim(document.getElementsByName('DESCRIPTION[]')[i].value) ===""){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('DESCRIPTION[]')[i].id;
      flag_message  = 'Please enter No/Description';
      flag_tab_type = 'PAYMENT_TAB';
    }
    else if($.trim(document.getElementsByName('PAID_AMT[]')[i].value) ===""){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('PAID_AMT[]')[i].id;
      flag_message  = 'Please enter pad amount';
      flag_tab_type = 'PAYMENT_TAB';
    }
    else if(jQuery.inArray(payment_type, flag_exist) !== -1){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('PAYMENT_TYPE[]')[i].id;
      flag_message  = 'This payment mode is already exist';
      flag_tab_type = 'PAYMENT_TAB';
    }
    flag_exist.push(payment_type);
  }

  for (var i = 0; i < document.getElementsByName('TAX_NAME[]').length; i++) {
    var taxname = $.trim(document.getElementsByName('TAX_NAME[]')[i].value);
    
    if(taxname !=""){
      if($.trim(document.getElementsByName('TAX_PER[]')[i].value) ===""){
        flag_status.push('false');
        flag_focus    = document.getElementsByName('TAX_PER[]')[i].id;
        flag_message  = 'Please enter tax';
        flag_tab_type = 'TAX_TAB';
      }
      else if(jQuery.inArray(taxname, flag_exist) !== -1){
        flag_status.push('false');
        flag_focus    = document.getElementsByName('TAX_NAME[]')[i].id;
        flag_message  = 'This tax is already exist';
        flag_tab_type = 'TAX_TAB';
      }

      flag_exist.push(taxname);
    }
  }

  for (var i = 0; i < document.getElementsByName('PACKAGE_ID[]').length; i++) {
    var package_id = $.trim(document.getElementsByName('PACKAGE_ID[]')[i].value);
    if(package_id ===""){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('PACKAGE_NAME[]')[i].id;
      flag_message  = 'Please select package';
      flag_tab_type = 'MAT_TAB';
    }
    else if($.trim(document.getElementsByName('AMOUNT[]')[i].value) ===""){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('AMOUNT[]')[i].id;
      flag_message  = 'Please select amount';
      flag_tab_type = 'MAT_TAB';
    }
    else if(jQuery.inArray(package_id, flag_exist) !== -1){
      flag_status.push('false');
      flag_focus    = document.getElementsByName('PACKAGE_ID[]')[i].id;
      flag_message  = 'This package is already exist';
      flag_tab_type = 'MAT_TAB';
    }
    flag_exist.push(package_id);
  }

  if($.trim($("#DOC_NO").val()) ===""){
    $("#FocusId").val('DOC_NO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Service Invoice No.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#DOC_DATE").val()) ===""){
    $("#FocusId").val('DOC_DATE');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Select Invoice Date.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
  else if($.trim($("#JOB_NO").val()) ===""){
    $("#FocusId").val('JOB_NO');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Please Enter Job Card No.');
    $("#alert").modal('show');
    $("#OkBtn1").focus();
    return false;
  }
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
  else if(parseFloat($.trim($("#TOTAL_NET_AMOUNT").val())) !=parseFloat($.trim($("#TOTAL_PAID_AMOUNT").val()))){
    $("#PAYMENT_TAB").click();
    $("#FocusId").val('TOTAL_PAID_AMOUNT');        
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#AlertMessage").text('Paid Amount should equal of Net Amount.');
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
  var trnsoForm = $("#form_data");
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

function isNumberKey(e,t){
    try {
        if (window.event) {
            var charCode = window.event.keyCode;
        }
        else if (e) {
            var charCode = e.which;
        }
        else { return true; }
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {         
        return false;
        }
         return true;

    }
    catch (err) {
        alert(err.Description);
    }
}

function get_total_amount(TEXTNAME,TEXTID){
  var total=0;
  var input = document.getElementsByName(TEXTNAME+'[]');
  var tsid=[];
  for (var i = 0; i < input.length; i++) {
      var a = input[i];

      var amount  = $.trim(a.value) !=''?parseFloat(a.value):0
      var total   = total+amount;
  }

  $("#"+TEXTID).val(parseFloat(total).toFixed(2));

  var packageAmount   = $("#TOTAL_PACKAGE_AMOUNT").val();
  var discountAmount  = $("#TOTAL_DISCOUONT_AMOUNT").val();
  var taxAmount       = $("#TOTAL_TAX_AMOUNT").val();

  packageAmount       = packageAmount !=''?parseFloat(packageAmount):0;
  discountAmount      = discountAmount !=''?parseFloat(discountAmount):0;
  taxAmount           = taxAmount !=''?parseFloat(taxAmount):0;

  var afterDiscount   = (packageAmount-discountAmount);
  var totalAmount     = (afterDiscount+taxAmount);
  $("#TOTAL_NET_AMOUNT").val(parseFloat(totalAmount).toFixed(2));
}

function getTaxAmount(textid,value){
  var textid          = textid.split('_').pop();
  var TAX_PER         = $.trim(value) !=''?parseFloat(value):0
  var PACKAGE_TOTAL   = $("#TOTAL_PACKAGE_AMOUNT").val();
  var PACKAGE_TOTAL   = PACKAGE_TOTAL !=''?parseFloat(PACKAGE_TOTAL):0;

  var DISCOUNT_TOTAL   = $("#TOTAL_DISCOUONT_AMOUNT").val();
  var DISCOUNT_TOTAL   = DISCOUNT_TOTAL !=''?parseFloat(DISCOUNT_TOTAL):0;
  
  var TOTAL_AMOUNT    = (((PACKAGE_TOTAL-DISCOUNT_TOTAL)*TAX_PER)/100);

  $("#TAX_AMOUNT_"+textid).val(TOTAL_AMOUNT);
  get_total_amount('TAX_AMOUNT','TOTAL_TAX_AMOUNT');  
}

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
  get_total_amount('PAID_AMT','TOTAL_PAID_AMOUNT'); 
}

function dataDec(data,no){
  var text_value  = data.value !=''?parseFloat(data.value).toFixed(no):'';
  $("#"+data.id).val(text_value);
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
  $("#modal_th1").text('Code');
  $("#modal_th2").text('Description');
  $("#modal").show();
}

function bindValueCardMaster(data){

  var code    = $("#"+data.id).data("code");
  var desc    = $("#"+data.id).data("desc");
  var rowid   = $("#"+data.id).data("rowid");

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

function getPaymentAmount(textid,value){
  get_total_amount('PAID_AMT','TOTAL_PAID_AMOUNT');  
}


function resetTab(){
  $('#Payment').find('.participantRow2').each(function(){
    var rowcount = $(this).closest('table').find('.participantRow2').length;
    $(this).find('input:text').val('');
    $(this).find('input:hidden').val('');
    $(this).find('input:checkbox').prop('checked', false);

    if(rowcount > 1){
      $(this).closest('.participantRow2').remove();
      rowcount = parseInt(rowcount) - 1;
      $('#Row_Count1').val(rowcount);
    }
  });

  $('#TOTAL_PAID_AMOUNT').val('');
  get_total_amount('PAID_AMT','TOTAL_PAID_AMOUNT');
}
</script>
@endpush