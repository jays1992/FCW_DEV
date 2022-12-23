@extends('layouts.app')
@section('content')

<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2">
   <a href="javascript:void(0)" onclick="return  window.location.reload()" class="btn singlebt">Eway Bill{{isset($EwayBillDetails["EWAY_NO"]) ? '- '.$EwayBillDetails["EWAY_NO"]:""}} </a>

    </div>
    <div class="col-lg-10 topnav-pd">
      <button class="btn topnavbt"  id="btnSave" {{!empty($InvoiceDetails)?'disabled':''}}><i class="fa fa-plus"></i> Generate Eway Bill</button>
      <button class="btn topnavbt"  onclick="return  window.location.reload()" ><i class="fa fa-eye"></i> Get Eway Bill</button>
      <button class="btn topnavbt"  onclick="clickEvent('Cancel')" {{empty($InvoiceDetails)?'disabled':''}} ><i class="fa fa-times"></i> Cancel</button>
      <button class="btn topnavbt"  onclick="clickEvent('Download')" {{empty($InvoiceDetails)?'disabled':''}} ><i class="fa fa-download"></i> Download</button>   
      <button class="btn topnavbt"  onclick="return  window.location.reload()" {{empty($InvoiceDetails)?'disabled':''}} ><i class="fa fa-undo"></i> Reload</button>
      <button class="btn topnavbt"  onclick="return  window.location.href='{{route('transaction',[$FormId,'index'])}}'" ><i class="fa fa-power-off"></i> Exit</button>
      <button class="btn topnavbt" id="btnEInvoice" ><i class="fa fa-thumbs-o-up"></i>Back To E-Invoice Section</button>
    </div>
  </div>
</div>



<div class="container-fluid purchase-order-view filter" id="invoice_details">
  
  @if(!empty($InvoiceDetails))
  <div class="tab-content"> 
    <div id="DocumentSummary" class="tab-pane fade in active">
        <div class="inner-form" style="margin-top:10px;">
          <div class="row">
            <div class="col-lg-2 pl"><p>Transaction Id</p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->TransId) ? $InvoiceDetails->TransId:""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>Transaction Name</p></div>
            <div class="col-lg-6 pl">
            <span> {{isset($InvoiceDetails->TransName) ? $InvoiceDetails->TransName:""}} </span>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-2 pl"><p>Transport Doc No</p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->TransDocNo) ? $InvoiceDetails->TransDocNo:""}}  </span>
            </div>
            <div class="col-lg-2 pl"><p>Tranport Doc Date</p></div>
            <div class="col-lg-6 pl">
            <span> {{isset($InvoiceDetails->TransDocDt) && $InvoiceDetails->TransDocDt !='' && $InvoiceDetails->TransDocDt !='1900-01-01' ? date('d-m-Y',strtotime($InvoiceDetails->TransDocDt)):''}}</span>
            </div>
          </div>
                               
          <div class="row">
            <div class="col-lg-2 pl"><p>Vehicle No</p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->VehNo) ? $InvoiceDetails->VehNo:""}}  </span>
            
            </div>
            <div class="col-lg-2 pl"><p>Vehicle Type</p></div>
            <div class="col-lg-6 pl">
            <span>  {{$TRANSPORT_MODE}} </span>

            </div>
          </div>

          <div class="row">                    
            <div class="col-lg-2 pl"><p>Eway Bill No</p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($EwayBillDetails["EWAY_NO"]) ? $EwayBillDetails["EWAY_NO"]:""}}   </span>
            </div>
            <div class="col-lg-2 pl"><p> Irn</p></div>
            <div class="col-lg-6 pl">
            <span> {{isset($InvoiceDetails->Irn) ? $InvoiceDetails->Irn:""}} </span>
            </div> 
          </div>    

          <div class="row">                    
            <div class="col-lg-2 pl"><p>Eway Bill Date</p></div>
            <div class="col-lg-2 pl">
            <span>{{isset($EwayBillDetails["EWAYBILLDT"]) ? $EwayBillDetails["EWAYBILLDT"]:""}} </span>
            </div>
            <div class="col-lg-2 pl"><p>Eway Bill Status</p></div>
            <div class="col-lg-6 pl">
            <span> {{isset($EwayBillDetails["STATUS"]) ? $EwayBillDetails["STATUS"]:""}}  </span>
            </div> 
          </div>  

          <div class="row">                    
            <div class="col-lg-2 pl"><p>Eway Bill Valid Till</p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($EwayBillDetails["EWAYBILL_VALIDTO"]) ? $EwayBillDetails["EWAYBILL_VALIDTO"]:""}}  </span>
            </div>
           <div class="col-lg-2 pl"><p>Distance</p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->Distance) ? $InvoiceDetails->Distance:""}} </span>
            </div>
          </div>
    

          <div class="row">                    
            <div class="col-lg-2 pl"><p>Shipping Address </p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->ExpShipDtls->Addr1) ? $InvoiceDetails->ExpShipDtls->Addr1:""}} </span>
            </div>
           <div class="col-lg-2 pl"><p>Dispatch Address</p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->DispDtls->Addr1) ? $InvoiceDetails->DispDtls->Addr1:""}}</span>
            </div>
          </div>
  

          <div class="row">                    
            <div class="col-lg-2 pl"><p>Shipping Location </p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->ExpShipDtls->Loc) ? $InvoiceDetails->ExpShipDtls->Loc:""}} </span>
            </div>
           <div class="col-lg-2 pl"><p>Dispatch Location</p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->DispDtls->Loc) ? $InvoiceDetails->DispDtls->Loc:""}}</span>
            </div>
          </div>
      

          <div class="row">                    
            <div class="col-lg-2 pl"><p>Shipping State Code </p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->ExpShipDtls->Stcd) ? $InvoiceDetails->ExpShipDtls->Stcd:""}} </span>
            </div>
           <div class="col-lg-2 pl"><p>Dispatch State Code</p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->DispDtls->Stcd) ? $InvoiceDetails->DispDtls->Stcd:""}}</span>
            </div>
          </div>        

          <div class="row">                    
            <div class="col-lg-2 pl"><p>Shipping Pincode </p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->ExpShipDtls->Pin) ? $InvoiceDetails->ExpShipDtls->Pin:""}} </span>
            </div>
           <div class="col-lg-2 pl"><p>Dispatch Pincode</p></div>
            <div class="col-lg-2 pl">
            <span> {{isset($InvoiceDetails->DispDtls->Pin) ? $InvoiceDetails->DispDtls->Pin:""}}</span>
            </div>
          </div>
          </div>  



        </div>
      </div>
              
    </div>
    @endif


</div>
@if(empty($InvoiceDetails))
<div class="container-fluid purchase-order-view filter" >
<form id="frm_mst_add" method="POST" onsubmit="return validateForm()" class="needs-validation"  >          
         @csrf
         <div class="inner-form">
                   <div class="row">
                      <div class="col-lg-2 pl"><p>Transport ID</p></div>
                      <div class="col-lg-3 pl">
                        <input type="text" name="TRANSPORTID" id="TRANSPORTID" class="form-control mandatory" value="{{ old('TRANSPORTID') }}" required maxlength="200"   />
                      </div>
                      <div class="col-lg-2 pl"><p>Transport Name</p></div>
                      <div class="col-lg-3 pl">
                        <input type="text" name="TRANSPORT_NAME" id="TRANSPORT_NAME" class="form-control mandatory" value="{{ old('TRANSPORT_NAME') }}" required maxlength="200"   />
                      </div>   
                   </div>    
                   <div class="row">
                    <div class="col-lg-2 pl"><p>Transport Doc Date</p></div>
                      <div class="col-lg-3 pl">
                        <input type="date" name="TRANSPORT_DOCDT" id="TRANSPORT_DOCDT" class="form-control mandatory" value="{{ old('TRANSPORT_DOCDT') }}" required maxlength="200"   />
                      </div>  
                      <div class="col-lg-2 pl"><p>Transport Doc No</p></div>
                      <div class="col-lg-3 pl">
                        <input type="text" name="TRANSPORT_DOCNO" id="TRANSPORT_DOCNO" class="form-control mandatory" value="{{ old('TRANSPORT_DOCNO') }}" maxlength="200" required  />
                      </div>
                   </div>    
                   <div class="row">
                      <div class="col-lg-2 pl"><p>Vehicle No</p></div>
                        <div class="col-lg-3 pl">
                          <input type="text" name="VEHICLENO" id="VEHICLENO" class="form-control mandatory" value="{{ old('VEHICLENO') }}" required maxlength="200"   />
                        </div>
                      <div class="col-lg-2 pl"><p>VehType</p></div>
                        <div class="col-lg-3 pl">
                          <input type="hidden" NAME="IRN" value="{{$IRN_NO}}">
                          <input type="hidden" NAME="id" value="{{$id}}">
                          <select name="VEHICLE_TYPE" id="VEHICLE_TYPE" class="form-control mandatory" required>
                            <option value="">Select Vehicle Type</option>
                            <option value="R">Road</option>
                            <option value="T">Train</option>
                            <option value="A">Air</option>
                          </select>
                        </div>     
                   </div>   
                   <div class="row">
                        <div class="col-lg-2 pl"><p>Shipping Address 1</p></div>
                        <div class="col-lg-3 pl">
                          <input type="text" name="SHIPPING_ADDRESS_1" id="SHIPPING_ADDRESS_1" class="form-control mandatory" required maxlength="200"   />
                        </div>
                        <div class="col-lg-2 pl"><p>Dispatch Legal Name</p></div>
                        <div class="col-lg-3 pl">
                          <input type="text" name="DISPATCH_LEGALNAME" id="DISPATCH_LEGALNAME" class="form-control mandatory" required />
                        </div>
                     </div>
                     <div class="row">
                     <div class="col-lg-2 pl"><p>Shipping Address 2</p></div>
                     <div class="col-lg-3 pl">
                       <input type="text" name="SHIPPING_ADDRESS_2" id="SHIPPING_ADDRESS_2" class="form-control mandatory" required maxlength="200"/>
                     </div>
                     <div class="col-lg-2 pl"><p>Dispatch Address 1 </p></div>
                     <div class="col-lg-3 pl">
                       <input type="text" name="DISPATCH_ADDRESS1" id="DISPATCH_ADDRESS1" class="form-control mandatory" required />
                     </div>  
                     </div>
                     <div class="row">
                      <div class="col-lg-2 pl"><p>Shipping Country *</p></div>
                      <div class="col-lg-3 pl">
                        <select name="SHIPCTRYID_REF" id="SHIPCTRYID_REF" required onchange="getstate(this.value,'SHIPSTID_REF','SHIPCITYID_REF','')" class="form-control mandatory">
                          <option value="">Select</option>
                          @foreach ($country as $val)
                          <option value="{{$val->CTRYID}}">{{$val->CTRYCODE}} - {{$val->NAME}}</option>
                          @endforeach
                        </select>                           
                      </div>
                      <div class="col-lg-2 pl"><p>Dispatch Country</p></div>
                    <div class="col-lg-3 pl">
                      <select name="DISCTRYID_REF" id="DISCTRYID_REF" required onchange="getstate(this.value,'DISSTID_REF','DISCITYID_REF','')" class="form-control mandatory">
                        <option value="">Select</option>
                        @foreach ($country as $val)
                        <option value="{{$val->CTRYID}}">{{$val->CTRYCODE}} - {{$val->NAME}}</option>
                        @endforeach
                      </select>                           
                    </div>
                     </div>    
                     <div class="row">
                      <div class="col-lg-2 pl"><p>Shipping State *</p></div>
                        <div class="col-lg-3 pl">
                          <select name="SHIPSTID_REF" id="SHIPSTID_REF" required onchange="getcity(this.value,'SHIPCITYID_REF','')" class="form-control mandatory">
                            <option value="">Select</option>
                          </select>                            
                        </div>
                        <div class="col-lg-2 pl"><p>Dispatch State</p></div>
                      <div class="col-lg-3 pl">
                        <select name="DISSTID_REF" id="DISSTID_REF" required onchange="getcity(this.value,'DISCITYID_REF','')" class="form-control mandatory">
                          <option value="">Select</option>
                        </select>                            
                      </div>
                  </div>     
                     <div class="row">
                     <div class="col-lg-2 pl"><p>Shipping City *</p></div>
                      <div class="col-lg-3 pl">
                        <select name="SHIPCITYID_REF" id="SHIPCITYID_REF" required class="form-control mandatory">
                          <option value="">Select</option>
                        </select> 
                      </div>
                      <div class="col-lg-2 pl"><p>Dispatch City</p></div>
                    <div class="col-lg-3 pl">
                      <select name="DISCITYID_REF" id="DISCITYID_REF" required class="form-control mandatory">
                        <option value="">Select</option>
                      </select> 
                    </div>
                     </div>  
                     <div class="row">
                     <div class="col-lg-2 pl"><p>Shipping Pincode</p></div>
                      <div class="col-lg-3 pl">
                        <input type="text" name="SHPPING_PINCODE" id="SHPPING_PINCODE" class="form-control mandatory" required />
                      </div>
                      <div class="col-lg-2 pl"><p>Dispatch Pincode</p></div>
                      <div class="col-lg-3 pl">
                        <input type="text" name="DISPATCH_PINCODE" id="DISPATCH_PINCODE" class="form-control mandatory" required />
                      </div>
                     </div>    
                     <div class="row">
                      <div class="col-lg-2 pl"><p>Distance</p></div>
                      <div class="col-lg-3 pl">
                        <input type="text" name="DISTANCE" id="DISTANCE" class="form-control mandatory" />
                      </div>
                     </div>
             </div>
</form>
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
  $("#AlertMessage").text('Do you want to '+type+' E-Way Bill ?');
  $("#alert").modal('show');
}

function ActionType(id,irn){
  if($("#hdn_action_type").val() =='Generate'){
    //$('#invoice_details').html('<div class="modal-backdrop in"></div><div class="loading"></div>');
    GenerateEway(id);
  }
  else if($("#hdn_action_type").val() =='Cancel'){
    CancelEway(id);
  }
  else if($("#hdn_action_type").val() =='Download'){
    PrintEway()
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

function GenerateEway(id){
  var trnForm = $("#frm_mst_add");
  var formData = trnForm.serialize();
  //$('#invoice_details').html('<div class="modal-backdrop in"></div><div class="loading"></div>');
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{ route("transaction",[$FormId,"GenerateEway"])}}',
    type:'POST',
    data:formData,
    success:function(data){ 
       if(typeof data.status != "undefined" && data.status==200){
         $('#invoice_details').html(data.message);      
         $("#frm_mst_add")[0].reset();
         setTimeout(function(){
            $('#invoice_details').html("");   
          },5000); 
      }else{
        $('#invoice_details').html(data);
      }  
    }
  });
}

function CancelEway(id){
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{ route("transaction",[$FormId,"cancelEway"])}}',
    type:'POST',
    data:{ewaybillno:'{{isset($EwayBillDetails["EWAY_NO"]) ? $EwayBillDetails["EWAY_NO"]:""}}',id:'{{$id}}'},
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

function PrintEway(){
  var EwayBillNo        = '{{isset($EwayBillDetails["EWAY_NO"]) ? $EwayBillDetails["EWAY_NO"]:""}} ';
  var path              = '{{route("transaction",[$FormId,"PrintEway",":ewayno"]) }}';
  window.location.href  = path.replace(":ewayno",EwayBillNo);
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

//===========================================================EWAYBILL SECTION CLEARTAX================================================================

function getstate(id,stxtid,ctxtid,rowid){

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});	

$.ajax({
    url:'{{route("transaction",[$FormId,"getstate"])}}',
    type:'POST',
    data:{id:id,rowid:rowid},
    success:function(data) {
        $("#"+stxtid).html(data); 
        $("#"+ctxtid).html('<option value="">Select</option>');                
    },
    error: function (request, status, error) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
    }
});	

}


function getcity(id,txtid,rowid){

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});	

$.ajax({
  url:'{{route("transaction",[$FormId,"getcity"])}}',
  type:'POST',
  data:{id:id,rowid:rowid},
  success:function(data) {
    $("#"+txtid).html(data);                 
  },
  error:function(data){
    console.log("Error: Something went wrong.");
  },
  error: function (request, status, error) {
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").show();
    $("#AlertMessage").text(request.responseText);
    $("#alert").modal('show');
    $("#OkBtn").focus();
    highlighFocusBtn('activeOk');
  }
});

}

var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();
    
    $( "#btnSave" ).click(function() {
        if(formResponseMst.valid()){
            clickEvent('Generate');

        }
    });


    $('#btnEInvoice').on('click', function(){
    var recordId = '{{$id}}';
    var editURL = '{{route("transaction",[44,"invoice",":rcdId"]) }}';
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