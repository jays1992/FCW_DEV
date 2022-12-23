@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[388,'index'])}}" class="btn singlebt">Template Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
          <div class="inner-form">
          
            <div class="row">
              <div class="col-lg-2 pl"><p>Template Name</p></div>

              <div class="col-lg-2 pl">
                <div class="col-lg-11 pl">
                  <input type="hidden" name="TEMPLATEID" id="TEMPLATEID" value="{{ $objResponse->TEMPLATEID }}" />
                    <input type="text" name="TEMPLATE_NAME" id="TEMPLATE_NAME" value="{{ $objResponse->TEMPLATE_NAME }}" class="form-control mandatory" disabled autocomplete="off" maxlength="20" tabindex="1" />
                    <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
                    <span class="text-danger" id="ERROR_TEMPLATE_NAME"></span> 
                </div>
              </div>
              
            <div class="col-lg-2 pl"><p>Voucher Type</p></div>
              <div class="col-lg-2 pl">
                <div class="col-lg-11 pl">                        
                  <select name="TEMPLATE_FOR" id="TEMPLATE_FOR" class="form-control selectpicker" disabled autocomplete="off" data-live-search="true">
                    <option value="">Select</option>  
                    <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'VENDOR_QUOTATION'?'selected="selected"':''}}              value="VENDOR_QUOTATION">Vendor Quotation</option>
                    <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'VENDOR_QUOTATION_COMPARISION'?'selected="selected"':''}}  value="VENDOR_QUOTATION_COMPARISION">Vendor Quotation Comparision</option>
                    <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'PURCHASE_ORDER'?'selected="selected"':''}}                value="PURCHASE_ORDER">Purchase Order</option>
                    <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'BLANKET_PURCHASE_ORDER'?'selected="selected"':''}}        value="BLANKET_PURCHASE_ORDER">Blanket Purchase Order</option>
                    <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'SERVICE_PURCHASE_ORDER'?'selected="selected"':''}}        value="SERVICE_PURCHASE_ORDER">Service Purchase Order</option>
                    <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'PURCHASE_RETURN'?'selected="selected"':''}}               value="PURCHASE_RETURN">Purchase Return</option>
                    <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'PURCHASE_BILL/INVOICE'?'selected="selected"':''}}         value="PURCHASE_BILL/INVOICE">Purchase Bill/ Invoice</option>
                    <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'SERVICE_PURCHASE_INVOICE'?'selected="selected"':''}}      value="SERVICE_PURCHASE_INVOICE">Service Purchase Invoice</option>
                    <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'IMPORT_PURCHASE_ORDER'?'selected="selected"':''}}         value="IMPORT_PURCHASE_ORDER">Import Purchase Order</option>
                    <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'IMPORT_PURCHASE_INVOICE'?'selected="selected"':''}}       value="IMPORT_PURCHASE_INVOICE">Import Purchase Invoice</option>
                    <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'SALES_QUOTATION'?'selected="selected"':''}}               value="SALES_QUOTATION">Sales Quotation</option>
                    <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'SALES_ORDER'?'selected="selected"':''}}                   value="SALES_ORDER">Sales Order</option>
                    <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'OPEN_SALES_ORDER'?'selected="selected"':''}}              value="OPEN_SALES_ORDER">Open Sales Order</option>
                    <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'SALES_INVOICE'?'selected="selected"':''}}                 value="SALES_INVOICE">Sales Invoice</option>
                    <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'SALES_RETURN'?'selected="selected"':''}}                  value="SALES_RETURN">Sales Return</option>
                    <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'SALES_SERVICE_ORDER'?'selected="selected"':''}}           value="SALES_SERVICE_ORDER">Sales Service Order</option>
                    <option {{isset($objResponse->TEMPLATE_FOR) && $objResponse->TEMPLATE_FOR == 'SALES_SERVICE_INVOICE'?'selected="selected"':''}}         value="SALES_SERVICE_INVOICE">Sales Service Invoice</option>
                  </select>
                </div>
              </div>
            </div>     

          {{-- <div class="row">
            <div class="col-lg-2 pl"><p>De-Activated</p></div>
            <div class="col-lg-1 pl pr">
            <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
             value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
            </div>
            
            <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
            <div class="col-lg-2 pl">
              <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
            </div>
         </div> --}}

         <div class="row">
          <div class="col-lg-2 pl"><p>Template Description</p></div>
          <div class="col-lg-6 pl" id="temdes">
           <textarea id="editor1" name="TEMPLATE" disabled> {{$objResponse->TEMPLATE}} </textarea>
          </div>
      </div>

      
      
    {{-- <div class="row">
      <div class="col-lg-2 pl"><p>De-Activated</p></div>
      <div class="col-lg-1 pl">
      <label> {{$objResponse->DEACTIVATED == 1 ? "Yes" : ""}} </label>
      
      </div>
      
      <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
      <div class="col-lg-2 pl">
        <label> {{ (is_null($objResponse->DODEACTIVATED) || $objResponse->DODEACTIVATED=='1900-01-01' )?'':
        \Carbon\Carbon::parse($objResponse->DODEACTIVATED)->format('d/m/Y')   }} </label>
      </div>
</div> --}}
          

          </div>

    </div><!--purchase-order-view-->

    <script>
     $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[388,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });
    </script>

    
<script>
  $(document).ready(function() {
 $('#summernote').summernote();
});
</script>

<script>
  CKEDITOR.replace( 'editor1' );
  </script>

@endsection