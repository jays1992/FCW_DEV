@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2">
      <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">E-Invoice {{isset($InvoiceDetails->transaction->DocDtls->No) ? '('.$InvoiceDetails->transaction->DocDtls->No.')':""}}</a>
    </div>
    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt"  onclick="clickEvent('Generate')"><i class="fa fa-plus"></i> Generate IRN</button>
      <button class="btn topnavbt"  onclick="return  window.location.reload()" ><i class="fa fa-eye"></i> Get Invoice</button>
      <button class="btn topnavbt"  onclick="clickEvent('Cancel')" {{empty($InvoiceDetails)?'disabled':''}} ><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt"  onclick="clickEvent('Download')" {{empty($InvoiceDetails)?'disabled':''}} ><i class="fa fa-download"></i> Download</button>      
      <button class="btn topnavbt"  onclick="clickEvent('Send')" {{empty($InvoiceDetails)?'disabled':''}} ><i class="fa fa-envelope"></i> Send Invoice</button>
      <button class="btn topnavbt"  onclick="return  window.location.reload()" {{empty($InvoiceDetails)?'disabled':''}} ><i class="fa fa-undo"></i> Reload</button>
      <button class="btn topnavbt" id="btnEwaybill" ><i class="fa fa-thumbs-o-up"></i> Eway Bill </button>
      <button class="btn topnavbt"  onclick="return  window.location.href='{{route('transaction',[$FormId,'index'])}}'" ><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>

<div class="container-fluid purchase-order-view filter" id='invoice_details'>
  
  @if(!empty($InvoiceDetails))
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#DocumentSummary">Document Summary</a></li>
    <li><a data-toggle="tab" href="#Transaction">Transaction</a></li>
    <li><a data-toggle="tab" href="#Seller">Seller</a></li>
    <li><a data-toggle="tab" href="#Buyer">Buyer</a></li>
    <li><a data-toggle="tab" href="#Value">Value</a></li>
    <li><a data-toggle="tab" href="#Shipping">Shipping</a></li>
    <li><a data-toggle="tab" href="#Dispatch">Dispatch</a></li>            
    <li><a data-toggle="tab" href="#LineItems">Line Items</a></li>              
  </ul>
              
  <div class="tab-content">
 
    <div id="DocumentSummary" class="tab-pane fade in active">
        <div class="inner-form" style="margin-top:10px;">

          <div class="row">
            <div class="col-lg-2 pl"><p>Document Type *</p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->transaction->DocDtls->Typ) ? $InvoiceDetails->transaction->DocDtls->Typ:""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>Document Number *</p></div>
            <div class="col-lg-6 pl">
            <span> {{isset($InvoiceDetails->transaction->DocDtls->No) ? $InvoiceDetails->transaction->DocDtls->No:""}} </span>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-2 pl"><p>Document Date *</p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->transaction->DocDtls->Dt) ? $InvoiceDetails->transaction->DocDtls->Dt:""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>IRN Status</p></div>
            <div class="col-lg-6 pl">
            <span> {{isset($InvoiceDetails->document_status) ? $InvoiceDetails->document_status:""}} </span>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-2 pl"><p>Seller GSTIN</p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->transaction->SellerDtls->Gstin) ? $InvoiceDetails->transaction->SellerDtls->Gstin:""}}  </span>
            </div>
            <div class="col-lg-2 pl"><p>IRN</p></div>
            <div class="col-lg-6 pl">
            <span> {{isset($InvoiceDetails->govt_response->Irn) ? $InvoiceDetails->govt_response->Irn:""}} </span>
            </div>
          </div>
                               
          <div class="row">
            <div class="col-lg-2 pl"><p>IRN Generation Date</p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->govt_response->AckDt) && $InvoiceDetails->govt_response->AckDt !='' && $InvoiceDetails->govt_response->AckDt !='1900-01-01' ? date('d-m-Y',strtotime($InvoiceDetails->govt_response->AckDt)):''}} </span>
            
            </div>
            <div class="col-lg-2 pl"><p>Acknowledgement Number</p></div>
            <div class="col-lg-6 pl">
            <span> {{isset($InvoiceDetails->govt_response->AckNo) ? $InvoiceDetails->govt_response->AckNo:""}} </span>
            </div>
          </div>
                 
        </div>
      </div>

      <div id="Transaction" class="tab-pane fade in">
        <div class="inner-form" style="margin-top:10px;">
            <div class="row">
              <div class="col-lg-2 pl"><p>Tax Scheme</p></div>
              <div class="col-lg-2 pl">
              <span> {{isset($InvoiceDetails->transaction->TranDtls->TaxSch) ? $InvoiceDetails->transaction->TranDtls->TaxSch:""}}</span>
              </div>
              <div class="col-lg-2 pl"><p>REG REV</p></div>
              <div class="col-lg-2 pl">
              <span> {{isset($InvoiceDetails->transaction->TranDtls->RegRev) && $InvoiceDetails->transaction->TranDtls->RegRev =="Y" ? "Yes":"No"}} </span>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-2 pl"><p>Supply Type *</p></div>
              <div class="col-lg-2 pl">
              <span> {{isset($InvoiceDetails->transaction->TranDtls->SupTyp) ? $InvoiceDetails->transaction->TranDtls->SupTyp:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>IGST ON Infra</p></div>
              <div class="col-lg-2 pl">
              <span> {{isset($InvoiceDetails->transaction->TranDtls->IgstOnIntra) && $InvoiceDetails->transaction->TranDtls->IgstOnIntra =="Y" ? "Yes":"No"}} </span>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-2 pl"><p>Ecommerce GSTIN</p></div>
              <div class="col-lg-2 pl">
              <span>{{isset($InvoiceDetails->gstin) ? $InvoiceDetails->gstin:""}}  </span>
              </div>
            </div>     
          </div>
        </div>


        <div id="Seller" class="tab-pane fade in">
          <div class="inner-form" style="margin-top:10px;">
            <div class="row">
              <div class="col-lg-2 pl"><p>Seller GSTIN *</p></div>
              <div class="col-lg-3 pl">
              <span>  {{isset($InvoiceDetails->transaction->SellerDtls->Gstin) ? $InvoiceDetails->transaction->SellerDtls->Gstin:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>Legal Name *</p></div>
              <div class="col-lg-5 pl">
              <span>   {{isset($InvoiceDetails->transaction->SellerDtls->LglNm) ? $InvoiceDetails->transaction->SellerDtls->LglNm:""}} </span>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-2 pl"><p>Trade Name</p></div>
              <div class="col-lg-3 pl">
              <span>   {{isset($InvoiceDetails->transaction->SellerDtls->TrdNm) ? $InvoiceDetails->transaction->SellerDtls->TrdNm:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>Address Line 1</p></div>
              <div class="col-lg-5 pl">
              <span>   {{isset($InvoiceDetails->transaction->SellerDtls->Addr1) ? $InvoiceDetails->transaction->SellerDtls->Addr1:""}} </span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Address Line 2</p></div>
              <div class="col-lg-3 pl">
              <span>   {{isset($InvoiceDetails->transaction->SellerDtls->Addr2) ? $InvoiceDetails->transaction->SellerDtls->Addr2:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>Location</p></div>
              <div class="col-lg-5 pl">
              <span>   {{isset($InvoiceDetails->transaction->SellerDtls->Loc) ? $InvoiceDetails->transaction->SellerDtls->Loc:""}} </span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Pincode</p></div>
              <div class="col-lg-3 pl">
              <span>   {{isset($InvoiceDetails->transaction->SellerDtls->Pin) ? $InvoiceDetails->transaction->SellerDtls->Pin:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>State Code</p></div>
              <div class="col-lg-5 pl">
              <span>   {{isset($InvoiceDetails->transaction->SellerDtls->Stcd) ? $InvoiceDetails->transaction->SellerDtls->Stcd:""}} </span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Phone</p></div>
              <div class="col-lg-3 pl">
              <span>   {{isset($InvoiceDetails->transaction->SellerDtls->Gstin) ? $InvoiceDetails->transaction->SellerDtls->Gstin:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>Email</p></div>
              <div class="col-lg-5 pl">
              <span>   {{isset($InvoiceDetails->transaction->SellerDtls->Gstin) ? $InvoiceDetails->transaction->SellerDtls->Gstin:""}} </span>
              </div>
            </div>
            
          </div>
        </div>

        <div id="Buyer" class="tab-pane fade in">
          <div class="inner-form" style="margin-top:10px;">
            <div class="row">
              <div class="col-lg-2 pl"><p>Buyer GSTIN </p></div>
              <div class="col-lg-4 pl">
              <span> {{isset($InvoiceDetails->transaction->BuyerDtls->Gstin) ? $InvoiceDetails->transaction->BuyerDtls->Gstin:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>Legal Name</p></div>
              <div class="col-lg-4 pl">
              <span> {{isset($InvoiceDetails->transaction->BuyerDtls->LglNm) ? $InvoiceDetails->transaction->BuyerDtls->LglNm:""}} </span>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-2 pl"><p>Trade Name</p></div>
              <div class="col-lg-4 pl">
              <span> {{isset($InvoiceDetails->transaction->BuyerDtls->TrdNm) ? $InvoiceDetails->transaction->BuyerDtls->TrdNm:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>Place of Supply</p></div>
              <div class="col-lg-4 pl">
              <span> {{isset($InvoiceDetails->transaction->BuyerDtls->Loc) ? $InvoiceDetails->transaction->BuyerDtls->Loc:""}} </span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Address Line 1</p></div>
              <div class="col-lg-4 pl">
              <span> {{isset($InvoiceDetails->transaction->BuyerDtls->Addr1) ? $InvoiceDetails->transaction->BuyerDtls->Addr1:""}} </span>
              </div>

              <div class="col-lg-2 pl"><p>Address Line 2</p></div>
              <div class="col-lg-4 pl">
                <span> {{isset($InvoiceDetails->transaction->BuyerDtls->Addr2) ? $InvoiceDetails->transaction->BuyerDtls->Addr2:""}} </span>
              </div>
            </div>   
            
            
            <div class="row">
              <div class="col-lg-2 pl"><p>Location</p></div>
              <div class="col-lg-4 pl">
                <span> {{isset($InvoiceDetails->transaction->BuyerDtls->Loc) ? $InvoiceDetails->transaction->BuyerDtls->Loc:""}} </span>
              </div>
           
              <div class="col-lg-2 pl"><p>Pincode</p></div>
              <div class="col-lg-4 pl">
                <span> {{isset($InvoiceDetails->transaction->BuyerDtls->Pin) ? $InvoiceDetails->transaction->BuyerDtls->Pin:""}} </span>
              </div>
            </div>  
            
            <div class="row">
              <div class="col-lg-2 pl"><p>State Code</p></div>
              <div class="col-lg-4 pl">
                <span> {{isset($InvoiceDetails->transaction->BuyerDtls->Stcd) ? $InvoiceDetails->transaction->BuyerDtls->Stcd:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>Phone</p></div>
              <div class="col-lg-4 pl">
                <span> {{isset($InvoiceDetails->transaction->BuyerDtls->Ph) ? $InvoiceDetails->transaction->BuyerDtls->Ph:""}} </span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Email</p></div>
              <div class="col-lg-4 pl">
              <span> {{isset($InvoiceDetails->transaction->BuyerDtls->Em) ? $InvoiceDetails->transaction->BuyerDtls->Em:""}} </span>
              </div>
            </div>  

          </div>
        </div>

        <div id="Value" class="tab-pane fade in">
          <div class="inner-form" style="margin-top:10px;">
            <div class="row">
              <div class="col-lg-2 pl"><p>Total Taxable Value</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->transaction->ValDtls->AssVal) ? $InvoiceDetails->transaction->ValDtls->AssVal:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>CGST Value</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->transaction->ValDtls->CgstVal) ? $InvoiceDetails->transaction->ValDtls->CgstVal:""}} </span>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-2 pl"><p>SGST Value</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->transaction->ValDtls->SgstVal) ? $InvoiceDetails->transaction->ValDtls->SgstVal:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>IGST Value</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->transaction->ValDtls->IgstVal) ? $InvoiceDetails->transaction->ValDtls->IgstVal:""}} </span>
              </div>
            </div>
            <div class="row">
              <!-- <div class="col-lg-2 pl"><p>State Cess Value</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->transaction->ValDtls->TaxSch) ? $InvoiceDetails->transaction->ValDtls->TaxSch:""}} </span>
              </div>-->
              <div class="col-lg-2 pl"><p>Discount</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->transaction->ValDtls->Discount) ? $InvoiceDetails->transaction->ValDtls->Discount:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>Other Charges</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->transaction->ValDtls->OthChrg) ? $InvoiceDetails->transaction->ValDtls->OthChrg:""}} </span>
              </div>
            
          </div>

          <div class="row"> 
              <div class="col-lg-2 pl"><p>Total Invoice Value</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->transaction->ValDtls->TotInvVal) ? $InvoiceDetails->transaction->ValDtls->TotInvVal:""}} </span>
              </div>
              <!--<div class="col-lg-2 pl"><p>Round Off Amount</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->transaction->ValDtls->TaxSch) ? $InvoiceDetails->transaction->ValDtls->TaxSch:""}} </span>
              </div>-->
            </div>   

              <div class="row">
          
              <!--<div class="col-lg-2 pl"><p>Final Invoice Value in Add Currency</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->transaction->ValDtls->TaxSch) ? $InvoiceDetails->transaction->ValDtls->TaxSch:""}} </span>
              </div>-->
  
          </div>
        </div>
      </div>

      <div id="Shipping" class="tab-pane fade in">
        <div class="inner-form" style="margin-top:10px;">
          <div class="row">
            <div class="col-lg-2 pl"><p>Shipping GSTIN</p></div>
            <div class="col-lg-2 pl">
            <span>  {{isset($InvoiceDetails->transaction->ShipDtls->Gstin) ? $InvoiceDetails->transaction->ShipDtls->Gstin:""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>Legal Name</p></div>
            <div class="col-lg-4 pl">
            <span>  {{isset($InvoiceDetails->transaction->ShipDtls->LglNm) ? $InvoiceDetails->transaction->ShipDtls->LglNm:""}} </span>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-2 pl"><p>Trade Name</p></div>
            <div class="col-lg-2 pl">
            <span>  {{isset($InvoiceDetails->transaction->ShipDtls->TrdNm) ? $InvoiceDetails->transaction->ShipDtls->TrdNm:""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>Address Line 1</p></div>
            <div class="col-lg-4 pl">
            <span>  {{isset($InvoiceDetails->transaction->ShipDtls->Addr1) ? $InvoiceDetails->transaction->ShipDtls->Addr1:""}} </span>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-2 pl"><p>Address Line 2</p></div>
            <div class="col-lg-2 pl">
            <span>  {{isset($InvoiceDetails->transaction->ShipDtls->Addr2) ? $InvoiceDetails->transaction->ShipDtls->Addr2:""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>Location</p></div>
            <div class="col-lg-4 pl">
            <span>  {{isset($InvoiceDetails->transaction->ShipDtls->Loc) ? $InvoiceDetails->transaction->ShipDtls->Loc:""}} </span>
            </div>
          </div>   

          <div class="row">
            <div class="col-lg-2 pl"><p>Pincode</p></div>
            <div class="col-lg-2 pl">
            <span>  {{isset($InvoiceDetails->transaction->ShipDtls->Pin) ? $InvoiceDetails->transaction->ShipDtls->Pin:""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>State Code</p></div>
            <div class="col-lg-4 pl">
            <span>  {{isset($InvoiceDetails->transaction->ShipDtls->Stcd) ? $InvoiceDetails->transaction->ShipDtls->Stcd:""}} </span>
            </div>
          </div>   
    
        </div>
      </div>

      <div id="Dispatch" class="tab-pane fade in">
        <div class="inner-form" style="margin-top:10px;">
          <div class="row">
            <div class="col-lg-2 pl"><p>Name</p></div>
            <div class="col-lg-2 pl">
            <span>  {{isset($InvoiceDetails->transaction->DispDtls->Nm) ? $InvoiceDetails->transaction->DispDtls->Nm:""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>Address Line 1</p></div>
            <div class="col-lg-4 pl">
            <span>  {{isset($InvoiceDetails->transaction->DispDtls->Addr1) ? $InvoiceDetails->transaction->DispDtls->Addr1:""}} </span>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-2 pl"><p>Address Line 2</p></div>
            <div class="col-lg-2 pl">
            <span>  {{isset($InvoiceDetails->transaction->DispDtls->Addr2) ? $InvoiceDetails->transaction->DispDtls->Addr2:""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>Location</p></div>
            <div class="col-lg-4 pl">
            <span>  {{isset($InvoiceDetails->transaction->DispDtls->OKHLA) ? $InvoiceDetails->transaction->DispDtls->OKHLA:""}} </span>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-2 pl"><p>Pincode</p></div>
            <div class="col-lg-2 pl">
            <span>  {{isset($InvoiceDetails->transaction->DispDtls->Pin) ? $InvoiceDetails->transaction->DispDtls->Pin:""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>State Code</p></div>
            <div class="col-lg-4 pl">
            <span>  {{isset($InvoiceDetails->transaction->DispDtls->Stcd) ? $InvoiceDetails->transaction->DispDtls->Stcd:""}} </span>
            </div>
          </div>     
        </div>
      </div>

      <div id="LineItems" class="tab-pane fade in">

        <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:500px;margin-top:10px;" >
          <table id="example2" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
            <thead id="thead1"  style="position: sticky;top: 0">
              <tr >
              <th>S.No.</th>
              <th>Item Name</th>
              <th>Is Service?</th>
              <th>HSN Code</th>
              <th>Quantity</th>
              <th>Unit</th>
              <th>Unit Price</th>
              <th>Gross Amount</th>
              <th>Discount</th>
              <th>Item Taxable Value</th>
              <th>GST Rate</th>
              <th>IGST Amount</th>
              <th>CGST Amount</th>
              <th>SGST Amount</th>           
              <th>Total Item Value</th>
              </tr>
            </thead>
            <tbody>               
          @if(isset($InvoiceDetails->transaction->ItemList))
          @foreach($InvoiceDetails->transaction->ItemList as $key=>$ItemLists)  
            <tr class="participantRow">
              <td>
                <input value="{{isset($ItemLists->SlNo) ? $ItemLists->SlNo:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input value="{{isset($ItemLists->PrdDesc) ? $ItemLists->PrdDesc:''}}" class="form-control" autocomplete="off" maxlength="100" style="width: 159px;" disabled/>
              </td>
              <td>
                <input value="{{isset($ItemLists->IsServc) ? $ItemLists->IsServc:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input value="{{isset($ItemLists->HsnCd) ? $ItemLists->HsnCd:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input style="text-align:center;" value="{{isset($ItemLists->Qty) ? $ItemLists->Qty:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input style="text-align:left;" value="{{isset($ItemLists->Unit) ? $ItemLists->Unit:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input style="text-align:right;" value="{{isset($ItemLists->UnitPrice) ? $ItemLists->UnitPrice:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input style="text-align:right;" value="{{isset($ItemLists->TotAmt) ? $ItemLists->TotAmt:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input style="text-align:right;" value="{{isset($ItemLists->Discount) ? $ItemLists->Discount:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input style="text-align:right;" value="{{isset($ItemLists->AssAmt) ? $ItemLists->AssAmt:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input  style="text-align:right;" value="{{isset($ItemLists->GstRt) ? $ItemLists->GstRt:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input style="text-align:right;" value="{{isset($ItemLists->IgstAmt) ? $ItemLists->IgstAmt:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input style="text-align:right;" value="{{isset($ItemLists->CgstAmt) ? $ItemLists->CgstAmt:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input style="text-align:right;" value="{{isset($ItemLists->SgstAmt) ? $ItemLists->SgstAmt:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>    
              <td>
                <input style="text-align:right;" value="{{isset($ItemLists->TotItemVal) ? $ItemLists->TotItemVal:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
            </tr>
            @endforeach
            
            <tr>
              <td colspan="13"></td>
              <td style="font-size: 11px;"> Other Charges</td>
              <td style="text-align:right;">{{isset($InvoiceDetails->transaction->ValDtls->OthChrg) ? $InvoiceDetails->transaction->ValDtls->OthChrg:''}}</td>
            </tr>

            @else
          <td>
              <input value="" class="form-control" autocomplete="off" maxlength="100" disabled/>
            </td>
            @endif          
            </tbody>
            <tr  class="participantRowFooter">
              <td colspan="8" style="text-align:center;font-weight:bold; font-size: 12px;">TOTAL </td>    
            
              <td id="" style="text-align:right;font-weight:bold; font-size: 12px;">{{isset($InvoiceDetails->transaction->ValDtls->Discount) ? $InvoiceDetails->transaction->ValDtls->Discount:''}}</td>
              <td id="" style="text-align:right;font-weight:bold; font-size: 12px;">{{isset($InvoiceDetails->transaction->ValDtls->AssVal) ? $InvoiceDetails->transaction->ValDtls->AssVal:''}}</td>
              <td id="" style="text-align:right;font-weight:bold; font-size: 12px;"></td>
              <td id="" style="text-align:right;font-weight:bold; font-size: 12px;">{{isset($InvoiceDetails->transaction->ValDtls->IgstVal) ? $InvoiceDetails->transaction->ValDtls->IgstVal:''}}</td>
              <td id="" style="text-align:right;font-weight:bold; font-size: 12px;">{{isset($InvoiceDetails->transaction->ValDtls->CgstVal) ? $InvoiceDetails->transaction->ValDtls->CgstVal:''}}</td>
              <td id="" style="text-align:right;font-weight:bold; font-size: 12px;">{{isset($InvoiceDetails->transaction->ValDtls->SgstVal) ? $InvoiceDetails->transaction->ValDtls->SgstVal:''}}</td>              
              <td id="" style="text-align:right;font-weight:bold; font-size: 12px;">{{isset($InvoiceDetails->transaction->ValDtls->TotInvVal) ? $InvoiceDetails->transaction->ValDtls->TotInvVal:''}}</td>                                          
        </tr>
          </table>
        </div>
      </div>            
    </div>
    @endif

</div>
@endsection
@section('alert')
<div id="alert" class="modal" role="dialog" data-backdrop="static" >
  <div class="modal-dialog"  >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">System Alert Message</h4>
      </div>
      <div class="modal-body">
        <h5 id="AlertMessage" ></h5>
        <div class="btdiv"> 

          <button class="btn alertbt" onclick="ActionType('{{$id}}','{{isset($InvoiceDetails->govt_response->Irn) ? $InvoiceDetails->govt_response->Irn:''}}')" style="margin-left: 90px;"><div id="alert-active" class="activeOk"></div>OK</button>  
          <input type='hidden' id='hdn_action_type' >
        </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
@endsection



@push('bottom-scripts')
<script>

function clickEvent(type){
  $("#hdn_action_type").val(type);
  $("#AlertMessage").text('Do you want to '+type+' E-Invoice ?');
  $("#alert").modal('show');
}

function ActionType(id,irn){
  if($("#hdn_action_type").val() =='Generate'){
    $('#invoice_details').html('<div class="modal-backdrop in"></div><div class="loading"></div>');
    GenerateIrn(id);
  }
  else if($("#hdn_action_type").val() =='Cancel'){
    CancelIrn(id);
  }
  else if($("#hdn_action_type").val() =='Download'){
    PrintIrn(irn)
  }
  else if($("#hdn_action_type").val() =='Reload'){
    window.location.reload();
  }
  else if($("#hdn_action_type").val() =='Send'){
    $('#invoice_details').html('<div class="modal-backdrop in"></div><div class="loading"></div>');
    SendInvoice();
  }

  $("#hdn_action_type").val('');
  $("#alert").modal('hide');
}

function GenerateIrn(id){

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{ route("transaction",[$FormId,"GenerateIrn"])}}',
    type:'POST',
    data:{id:id},
    success:function(data){ 
      $('#invoice_details').html(data);
    }
  });
}

function CancelIrn(id){

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{ route("transaction",[$FormId,"cancelIRN"])}}',
    type:'POST',
    data:{id:id},
    success:function(data) {               
      if(data.errors) {
        $("#AlertMessage").text(data.msg);
        $("#alert").modal('show');
        return false; 
      }
        
      if(data.cancel) {   
        $("#AlertMessage").text(data.msg);
        $("#hdn_action_type").val('Reload');
        $("#alert").modal('show');
        return false;
      }  
                  
    },
    error:function(data){
        $("#AlertMessage").text('Error: Something went wrong.');
        $("#alert").modal('show');
    },
  });
}

function PrintIrn(id){
  var path              = '{{route("transaction",[$FormId,"PrintIrn",":irn"]) }}';
  window.location.href  = path.replace(":irn",id);
}

function SendInvoice(){

  $("#hdn_action_type").val('');
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{ route("transaction",[$FormId,"SendInvoice"])}}',
    type:'POST',
    data:{
      DOC_NO:'{{isset($InvoiceDetails->transaction->DocDtls->No) ? $InvoiceDetails->transaction->DocDtls->No:""}}',
      DOC_DT:'{{isset($InvoiceDetails->transaction->DocDtls->Dt) ? $InvoiceDetails->transaction->DocDtls->Dt:""}}',
      BUYER_NAME:'{{isset($InvoiceDetails->transaction->BuyerDtls->LglNm) ? $InvoiceDetails->transaction->BuyerDtls->LglNm:""}}',
      EMAIL:'{{isset($InvoiceDetails->transaction->BuyerDtls->Em) ? $InvoiceDetails->transaction->BuyerDtls->Em:""}}',
      IRN:'{{isset($InvoiceDetails->govt_response->Irn) ? $InvoiceDetails->govt_response->Irn:""}}',
  
    },
    success:function(data) {  
      $('#invoice_details').html('');    
      if(data.errors) {
        $("#AlertMessage").text(data.msg);
        $("#alert").modal('show');
        return false; 
      }
        
      if(data.sent) {   
        $("#AlertMessage").text(data.msg);
        $("#alert").modal('show');
        return false;
      }  
                  
    },
    error:function(data){
        $("#AlertMessage").text('Error: Something went wrong.');
        $("#alert").modal('show');
    },
  });
}
  
    $('#btnEwaybill').on('click', function(){
    var recordId = '{{$id}}';
    var editURL = '{{route("transaction",[44,"ewaybill",":rcdId"]) }}';
    editURL = editURL.replace(":rcdId",window.btoa(recordId));
    window.location.href=editURL;
  });





</script>
@endpush

<style>
.loading {
  border: 16px solid #f3f3f3 !important;
  border-radius: 50% !important;
  margin-top:10% !important;
  margin-left:40% !important;
  width:200px !important;
  height:200px !important;
  z-index:9999999;
  border-top: 16px solid #b7b7b7 !important;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
}

@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>