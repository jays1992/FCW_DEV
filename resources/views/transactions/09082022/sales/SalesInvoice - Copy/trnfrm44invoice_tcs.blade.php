@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2">
      <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">E-Invoice {{isset($InvoiceDetails->docdtl->docno) ? '('.$InvoiceDetails->docdtl->docno.')':""}}</a>
    </div>
    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt"  onclick="clickEvent('Generate')"><i class="fa fa-plus"></i> Generate IRN</button>
      <button class="btn topnavbt"  onclick="return  window.location.reload()" ><i class="fa fa-eye"></i> Get Invoice</button>
      <button class="btn topnavbt"  onclick="clickEvent('Cancel')" {{empty($InvoiceDetails)?'disabled':''}} ><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt"  onclick="clickEvent('Download')" {{empty($InvoiceDetails)?'disabled':''}}><i class="fa fa-download"></i> Download</button>      
      <!--<button class="btn topnavbt"  onclick="clickEvent('Send')" {{empty($InvoiceDetails)?'disabled':''}} ><i class="fa fa-envelope"></i> Send Invoice</button>-->
      <button class="btn topnavbt"  onclick="return  window.location.reload()" {{empty($InvoiceDetails)?'disabled':''}} ><i class="fa fa-undo"></i> Reload</button>
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
            <span> {{isset($InvoiceDetails->docdtl->document_type) ? $InvoiceDetails->docdtl->document_type :""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>Document Number *</p></div>
            <div class="col-lg-6 pl">
            <span> {{isset($InvoiceDetails->docdtl->docno) ? $InvoiceDetails->docdtl->docno :""}} </span>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-2 pl"><p>Document Date *</p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->docdtl->docdt) ? $InvoiceDetails->docdtl->docdt :""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>IRN Status</p></div>
            <div class="col-lg-6 pl">
            <span> {{isset($InvoiceDetails->document_status) ? $InvoiceDetails->document_status:""}} </span>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-2 pl"><p>Seller GSTIN</p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->self_gstin) ? $InvoiceDetails->self_gstin :""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>IRN</p></div>
            <div class="col-lg-6 pl">
            <span> {{isset($InvoiceDetails->irnno) ? $InvoiceDetails->irnno :""}}</span>
            </div>
          </div>
                               
          <div class="row">
            <div class="col-lg-2 pl"><p>IRN Generation Date</p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->docdtl->docdt) ? $InvoiceDetails->docdtl->docdt :""}}  </span>
            
            </div>
            <div class="col-lg-2 pl"><p>Acknowledgement Number</p></div>
            <div class="col-lg-6 pl">
            <span> {{isset($AckNo) ? $AckNo:""}} </span>
            </div>
          </div>              
        </div>
      </div>

      <div id="Transaction" class="tab-pane fade in">
        <div class="inner-form" style="margin-top:10px;">
            <div class="row">
              <div class="col-lg-2 pl"><p>Tax Scheme</p></div>
              <div class="col-lg-2 pl">
              <span> {{isset($InvoiceDetails->self_gstin) ? "GST":""}}</span>
              </div>
              <div class="col-lg-2 pl"><p>REG REV</p></div>
              <div class="col-lg-2 pl">
              <span> {{isset($InvoiceDetails->trandtl->TranDtls->reversecharge) && $InvoiceDetails->trandtl->TranDtls->reversecharge =="Y" ? "Yes":"No"}} </span>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-2 pl"><p>Supply Type *</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->self_gstin) ? "B2B":""}}</span>
              </div>
              <div class="col-lg-2 pl"><p>IGST ON Infra</p></div>
              <div class="col-lg-2 pl">
              <span> {{isset($InvoiceDetails->trandtl->igstonintra) && $InvoiceDetails->trandtl->igstonintra =="Y" ? "Yes":"No"}} </span>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-2 pl"><p>Ecommerce GSTIN</p></div>
              <div class="col-lg-2 pl">
              <span>{{isset($InvoiceDetails->self_gstin) ? $InvoiceDetails->self_gstin:""}}  </span>
              </div>
            </div>     
          </div>
        </div>


        <div id="Seller" class="tab-pane fade in">
          <div class="inner-form" style="margin-top:10px;">
            <div class="row">
              <div class="col-lg-2 pl"><p>Seller GSTIN *</p></div>
              <div class="col-lg-3 pl">
              <span>  {{isset($InvoiceDetails->supplierdtl->supplier_gstin) ? $InvoiceDetails->supplierdtl->supplier_gstin:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>Legal Name *</p></div>
              <div class="col-lg-5 pl">
              <span>   {{isset($InvoiceDetails->supplierdtl->supplier_lglnm) ? $InvoiceDetails->supplierdtl->supplier_lglnm:""}} </span>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-2 pl"><p>Trade Name</p></div>
              <div class="col-lg-3 pl">
              <span>   {{isset($InvoiceDetails->supplierdtl->supplier_lglnm) ? $InvoiceDetails->supplierdtl->supplier_lglnm:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>Address Line 1</p></div>
              <div class="col-lg-5 pl">
              <span>   {{isset($InvoiceDetails->supplierdtl->supplier_loc) ? $InvoiceDetails->supplierdtl->supplier_loc:""}} </span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Address Line 2</p></div>
              <div class="col-lg-3 pl">
              <span>   {{isset($InvoiceDetails->supplierdtl->supplier_loc) ? $InvoiceDetails->supplierdtl->supplier_loc:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>Location</p></div>
              <div class="col-lg-5 pl">
              <span>   {{isset($InvoiceDetails->supplierdtl->supplier_loc) ? $InvoiceDetails->supplierdtl->supplier_loc:""}} </span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Pincode</p></div>
              <div class="col-lg-3 pl">
              <span>   {{isset($InvoiceDetails->supplierdtl->supplier_pin) ? $InvoiceDetails->supplierdtl->supplier_pin:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>State Code</p></div>
              <div class="col-lg-5 pl">
              <span>   {{isset($InvoiceDetails->supplierdtl->supplier_state) ? $InvoiceDetails->supplierdtl->supplier_state:""}} </span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Phone</p></div>
              <div class="col-lg-3 pl">
              <span>   {{isset($InvoiceDetails->transaction->SellerDtls->supplier_phone) ? $InvoiceDetails->transaction->SellerDtls->supplier_phone:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>Email</p></div>
              <div class="col-lg-5 pl">
              <span>   {{isset($InvoiceDetails->transaction->SellerDtls->supplier_email) ? $InvoiceDetails->transaction->SellerDtls->supplier_email:""}} </span>
              </div>
            </div>
            
          </div>
        </div>

        <div id="Buyer" class="tab-pane fade in">
          <div class="inner-form" style="margin-top:10px;">
            <div class="row">
              <div class="col-lg-2 pl"><p>Buyer GSTIN </p></div>
              <div class="col-lg-4 pl">
              <span> {{isset($InvoiceDetails->buyerdtl->buyer_gstin) ? $InvoiceDetails->buyerdtl->buyer_gstin:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>Legal Name</p></div>
              <div class="col-lg-4 pl">
              <span> {{isset($InvoiceDetails->buyerdtl->buyer_lglnm) ? $InvoiceDetails->buyerdtl->buyer_lglnm:""}} </span>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-2 pl"><p>Trade Name</p></div>
              <div class="col-lg-4 pl">
              <span> {{isset($InvoiceDetails->buyerdtl->buyer_lglnm) ? $InvoiceDetails->buyerdtl->buyer_lglnm:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>Place of Supply</p></div>
              <div class="col-lg-4 pl">
              <span> {{isset($InvoiceDetails->buyerdtl->buyer_loc) ? $InvoiceDetails->buyerdtl->buyer_loc:""}} </span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Address Line 1</p></div>
              <div class="col-lg-4 pl">
              <span> {{isset($InvoiceDetails->buyerdtl->buyer_loc) ? $InvoiceDetails->buyerdtl->buyer_loc:""}} </span>
              </div>

              <div class="col-lg-2 pl"><p>Address Line 2</p></div>
              <div class="col-lg-4 pl">
                <span>  </span>
              </div>
            </div>               
            
            <div class="row">
              <div class="col-lg-2 pl"><p>Location</p></div>
              <div class="col-lg-4 pl">
                <span> {{isset($InvoiceDetails->buyerdtl->buyer_loc)? $InvoiceDetails->buyerdtl->buyer_loc:""}} </span>
              </div>
           
              <div class="col-lg-2 pl"><p>Pincode</p></div>
              <div class="col-lg-4 pl">
                <span> {{isset($InvoiceDetails->buyerdtl->buyer_pin)? $InvoiceDetails->buyerdtl->buyer_pin:""}} </span>
              </div>
            </div>  
            
            <div class="row">
              <div class="col-lg-2 pl"><p>State Code</p></div>
              <div class="col-lg-4 pl">
                <span> {{isset($InvoiceDetails->buyerdtl->buyer_state) ? $InvoiceDetails->buyerdtl->buyer_state:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>Phone</p></div>
              <div class="col-lg-4 pl">
                <span> {{isset($InvoiceDetails->buyerdtl->BuyerDtls->supplier_phone) ? $InvoiceDetails->buyerdtl->BuyerDtls->supplier_phone:""}} </span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-2 pl"><p>Email</p></div>
              <div class="col-lg-4 pl">
              <span> {{isset($InvoiceDetails->buyerdtl->BuyerDtls->supplier_email) ? $InvoiceDetails->buyerdtl->BuyerDtls->supplier_email:""}} </span>
              </div>
              
            </div>  

          </div>
        </div>

        <div id="Value" class="tab-pane fade in">
          <div class="inner-form" style="margin-top:10px;">
            <div class="row">
              <div class="col-lg-2 pl"><p>Total Taxable Value</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->valdtl->ttlassval) ? $InvoiceDetails->valdtl->ttlassval:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>CGST Value</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->valdtl->cgstval) ? $InvoiceDetails->valdtl->cgstval:""}} </span>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-2 pl"><p>SGST Value</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->valdtl->sgstval) ? $InvoiceDetails->valdtl->sgstval:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>IGST Value</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->valdtl->igstval) ? $InvoiceDetails->valdtl->igstval:""}} </span>
              </div>
            </div>
            <div class="row">
              <!-- <div class="col-lg-2 pl"><p>State Cess Value</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->valdtl->ValDtls->TaxSch) ? $InvoiceDetails->valdtl->ValDtls->TaxSch:""}} </span>
              </div>-->
              <div class="col-lg-2 pl"><p>Discount</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->valdtl->discval) ? $InvoiceDetails->valdtl->discval:""}} </span>
              </div>
              <div class="col-lg-2 pl"><p>Other Charges</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->valdtl->othchrg) ? $InvoiceDetails->valdtl->othchrg:""}} </span>
              </div>           
            </div>

          <div class="row"> 
              <div class="col-lg-2 pl"><p>Total Invoice Value</p></div>
              <div class="col-lg-2 pl">
              <span>  {{isset($InvoiceDetails->valdtl->totinvval) ? $InvoiceDetails->valdtl->totinvval:""}} </span>
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
            <span>  {{isset($InvoiceDetails->shipdtl->ship_gstin) ? $InvoiceDetails->shipdtl->ship_gstin:""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>Legal Name</p></div>
            <div class="col-lg-4 pl">
            <span>  {{isset($InvoiceDetails->shipdtl->ship_lglnm) ? $InvoiceDetails->shipdtl->ship_lglnm:""}} </span>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-2 pl"><p>Trade Name</p></div>
            <div class="col-lg-2 pl">
            <span>  {{isset($InvoiceDetails->shipdtl->ship_lglnm) ? $InvoiceDetails->shipdtl->ship_lglnm:""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>Address Line 1</p></div>
            <div class="col-lg-4 pl">
            <span>  {{isset($InvoiceDetails->shipdtl->ship_loc) ? $InvoiceDetails->shipdtl->ship_loc:""}} </span>
            </div>
          </div>


          <div class="row">
            <div class="col-lg-2 pl"><p>Pincode</p></div>
            <div class="col-lg-2 pl">
            <span>  {{isset($InvoiceDetails->shipdtl->ship_pin) ? $InvoiceDetails->shipdtl->ship_pin:""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>State Code</p></div>
            <div class="col-lg-4 pl">
            <span>  {{isset($InvoiceDetails->shipdtl->ship_state) ? $InvoiceDetails->shipdtl->ship_state:""}} </span>
            </div>
          </div>   
    
        </div>
      </div>

      <div id="Dispatch" class="tab-pane fade in">
        <div class="inner-form" style="margin-top:10px;">
          <div class="row">
          <div class="col-lg-2 pl"><p>Dispatch GSTIN</p></div>
            <div class="col-lg-2 pl">
            <span>  {{isset($InvoiceDetails->dispdtl->disp_gstin) ? $InvoiceDetails->dispdtl->disp_gstin:""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>Legal Name</p></div>
            <div class="col-lg-4 pl">
            <span>  {{isset($InvoiceDetails->dispdtl->disp_trdnm) ? $InvoiceDetails->dispdtl->disp_trdnm:""}} </span>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-2 pl"><p>Address Line </p></div>
            <div class="col-lg-2 pl">
            <span>  {{isset($InvoiceDetails->dispdtl->disp_loc) ? $InvoiceDetails->dispdtl->disp_loc:""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>Pincode</p></div>
            <div class="col-lg-2 pl">
            <span>  {{isset($InvoiceDetails->dispdtl->disp_pin) ? $InvoiceDetails->dispdtl->disp_pin:""}} </span>
            </div>
          </div>
          <div class="row">
              <div class="col-lg-2 pl"><p>State Code</p></div>
            <div class="col-lg-4 pl">
            <span>  {{isset($InvoiceDetails->dispdtl->disp_state) ? $InvoiceDetails->dispdtl->disp_state:""}} </span>
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

              <th>HSN Code</th>
              <th>Quantity</th>
              <th>Unit</th>
              <th>Unit Price</th>
              <th>Gross Amount</th>
          
              <th>Item Taxable Value</th>
  
              <th>IGST Amount</th>
              <th>CGST Amount</th>
              <th>SGST Amount</th>           
              <th>Total Item Value</th>
              </tr>
            </thead>
            <tbody>               
          @if(isset($InvoiceDetails->itemdtls))
          @foreach($InvoiceDetails->itemdtls as $key=>$ItemLists)
  
            <tr class="participantRow">
              <td>
                <input value="{{$key+1}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input value="{{isset($ItemLists->prdnm) ? $ItemLists->prdnm:''}}" class="form-control" autocomplete="off" maxlength="100" style="width: 159px;" disabled/>
              </td>

              <td>
                <input value="{{isset($ItemLists->hsncd) ? $ItemLists->hsncd:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input style="text-align:center;" value="{{isset($ItemLists->qty) ? $ItemLists->qty:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input style="text-align:left;" value="{{isset($ItemLists->uqc) ? $ItemLists->uqc:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input style="text-align:right;" value="{{isset($ItemLists->unitrate) ? $ItemLists->unitrate:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input style="text-align:right;" value="{{isset($ItemLists->grossamt) ? $ItemLists->grossamt:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
    
       
              <td>
                <input style="text-align:right;" value="{{isset($ItemLists->assamt) ? $ItemLists->assamt:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
   
              <td>
                <input style="text-align:right;" value="{{isset($ItemLists->igstamt) ? $ItemLists->igstamt:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input style="text-align:right;" value="{{isset($ItemLists->cgstamt) ? $ItemLists->cgstamt:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
              <td>
                <input style="text-align:right;" value="{{isset($ItemLists->sgstamt) ? $ItemLists->sgstamt:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
    
              <td>
                <input style="text-align:right;" value="{{isset($ItemLists->totitemval) ? $ItemLists->totitemval:''}}" class="form-control" autocomplete="off" maxlength="100" disabled/>
              </td>
                      

              
            </tr>

      

            @endforeach
            
            <tr>
              <td colspan="10"></td>
              <td style="font-size: 11px;"> Other Charges</td>
              <td style="text-align:right;">{{isset($InvoiceDetails->valdtl->othchrg) ? $InvoiceDetails->valdtl->othchrg:''}}</td>
            </tr>
            <tr>
              <td colspan="10"></td>
              <td style="font-size: 11px;">Discount </td>
              <td style="text-align:right;">{{isset($InvoiceDetails->valdtl->discval) ? $InvoiceDetails->valdtl->discval:''}}</td>
            </tr>

            @else
          <td>
              <input value="" class="form-control" autocomplete="off" maxlength="100" disabled/>
            </td>
            @endif


          
            </tbody>
            <tr  class="participantRowFooter">
                                          <td colspan="7" style="text-align:center;font-weight:bold; font-size: 12px;">TOTAL </td>    
                                        
                                          <td style="text-align:right;font-weight:bold; font-size: 12px;">{{isset($InvoiceDetails->valdtl->ttlassval) ? $InvoiceDetails->valdtl->ttlassval:''}}</td>
                                    

                                          <td style="text-align:right;font-weight:bold; font-size: 12px;">{{isset($InvoiceDetails->valdtl->igstval) ? $InvoiceDetails->valdtl->igstval:''}}</td>
                                          <td style="text-align:right;font-weight:bold; font-size: 12px;">{{isset($InvoiceDetails->valdtl->cgstval) ? $InvoiceDetails->valdtl->cgstval:''}}</td>
                                          <td style="text-align:right;font-weight:bold; font-size: 12px;">{{isset($InvoiceDetails->valdtl->sgstval) ? $InvoiceDetails->valdtl->sgstval:''}}</td>
                                          
                                          <td style="text-align:right;font-weight:bold; font-size: 12px;">{{isset($InvoiceDetails->valdtl->totinvval) ? $InvoiceDetails->valdtl->totinvval:''}}</td>                                          
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
    PrintIrn(id)
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
    url:'{{ route("transaction",[$FormId,"GenerateIrn_tcs"])}}',
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
    url:'{{ route("transaction",[$FormId,"cancelIRN_tcs"])}}',
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
  var path              = '{{route("transaction",[$FormId,"PrintIrn_tcs",":irn"]) }}';
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