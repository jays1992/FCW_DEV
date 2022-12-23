@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[34,'index'])}}" class="btn singlebt">Transporter Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  disabled="disabled"><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
          <div class="inner-form">
          
                <div class="row">
                <div class="col-lg-2 pl"><p>Transporter Code</p></div>
                <div class="col-lg-3 pl">
                  <div class="col-lg-10 pl">
                    <label> {{$objResponse->TRANSPORTER_CODE}} </label>
                  </div>
                </div>
                
                <div class="col-lg-2 pl"><p>Transporter Name</p></div>
                <div class="col-lg-5 pl">
                <label> {{$objResponse->TRANSPORTER_NAME}} </label>
                  
                </div>
              </div>
              
              <div class="row">
                <div class="col-lg-2 pl"><p>GL</p></div>
                <div class="col-lg-4 pl">
                
                    <label>{{isset($objGlName->GLCODE)?$objGlName->GLCODE.'-'.$objGlName->GLNAME:''}}</label>
                 
                </div>
              </div>
              
              <div class="row">
                <div class="col-lg-2 pl"><p>Transporter Registered Address Line 1</p></div>
                <div class="col-lg-5 pl">
                <label> {{$objResponse->REG_ADD1}} </label>
                  
                  
                  
                  
                </div>
              </div>
              
              <div class="row">
                <div class="col-lg-2 pl"><p>Transporter Registered Address Line 2</p></div>
                <div class="col-lg-5 pl">
                <label> {{$objResponse->REG_ADD2}} </label>
                  
                </div>
              </div>
              
              <div class="row">

              <div class="col-lg-2 pl"><p>Country</p></div>
                <div class="col-lg-2 pl">
                <label>{{isset($objCountryName->CTRYCODE)?$objCountryName->CTRYCODE.'-'.$objCountryName->NAME:''}}</label>
                 
                </div>

                <div class="col-lg-2 pl col-md-offset-1"><p>State</p></div>
                <div class="col-lg-2 pl">
                <label>{{isset($objStateName->STCODE)?$objStateName->STCODE.'-'.$objStateName->NAME:''}}</label>
                  
                </div>
                
                
                
              </div>
              
              <div class="row">
               
              <div class="col-lg-2 pl"><p>City</p></div>
                <div class="col-lg-2 pl">
                <label>{{isset($objCityName->CITYCODE)?$objCityName->CITYCODE.'-'.$objCityName->NAME:''}}</label>
                  
                </div>

                <div class="col-lg-2 pl col-md-offset-1"><p>District</p></div>
                <div class="col-lg-2 pl">   
                  <label>{{isset($objDisticName->DISTCODE)?$objDisticName->DISTCODE.'-'.$objDisticName->NAME:''}}</label>
                </div>


              </div>
              
              
              <div class="row">
                
              
              <div class="col-lg-2 pl "><p>Pincode</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-8 pl">
                  <label> {{$objResponse->PINCODE}} </label>
                 
                  </div>
                </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Landmark</p></div>
                <div class="col-lg-5 pl">
                <label> {{$objResponse->LANDMARK}} </label>
                  
                </div>
                
              </div>
              
              <div class="row">
                
                <div class="col-lg-2 pl"><p>Email ID</p></div>
                <div class="col-lg-3 pl">
                <label> {{$objResponse->EMAILID}} </label>
                  
                </div>
                
                <div class="col-lg-2 pl "><p>Cell No</p></div>
                <div class="col-lg-2 pl">
                <label> {{$objResponse->CELL_NO}} </label>
                 
                </div>
                
                <div class="col-lg-1 pl"><p>Website</p></div>
                <div class="col-lg-2 pl">
                <label> {{$objResponse->WEBSITE}} </label>
                 
                </div>
                
              </div>
              
              <div class="row">

                <div class="col-lg-2 pl"><p>Phone No</p></div>
                <div class="col-lg-2 pl">
                <label> {{$objResponse->PHONE_NO}} </label>
                 
                </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Whatsapp No</p></div>
                <div class="col-lg-2 pl">
                <label> {{$objResponse->WHATSAPP_NO}} </label>
                  
                </div>
                
              </div>
              
              <div class="row"><br/></div>
              
              <div class="row">

                <div class="col-lg-2 pl"><p>Contact Person Name</p></div>
                <div class="col-lg-2 pl">
                <label> {{$objResponse->CP_NAME}} </label>
                  
                </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Designation</p></div>
                <div class="col-lg-2 pl">
                <label> {{$objResponse->CP_DESIGNATION}} </label>
                  
                </div>

                <div class="col-lg-1 pl"><p>Email ID</p></div>
                <div class="col-lg-2 pl">
                <label> {{$objResponse->CP_EMAILID}} </label>
                 
                </div>
                
              </div>
              
              <div class="row">
                
                
                
                <div class="col-lg-2 pl"><p>Cell No</p></div>
                <div class="col-lg-2 pl">
                <label> {{$objResponse->CP_CELL_NO}} </label>
                 
                </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Phone No</p></div>
                <div class="col-lg-2 pl">
                <label> {{$objResponse->CP_PHONE_NO}} </label>
                
                </div>

                <div class="col-lg-1 pl"><p>GSTIN No</p></div>
                <div class="col-lg-2 pl">
                <label> {{$objResponse->GSTIN_NO}} </label>
                 
                </div>
                
              </div>
              
              
              <div class="row">
              
                <div class="col-lg-2 pl"><p>PAN No</p></div>
                <div class="col-lg-2 pl">
                <label> {{$objResponse->PAN_NO}} </label>
                  
                </div>

                <div class="col-lg-2 pl col-md-offset-1"><p>CIN </p></div>
                <div class="col-lg-2 pl">
                <label> {{$objResponse->CIN}} </label>

                </div>

                <div class="col-lg-1 pl"><p>Bank Name</p></div>
                <div class="col-lg-2 pl">
                <label> {{$objResponse->BANK_NAME}} </label>
                  
                </div>
              
                
              </div>
              

              
              <div class="row">

              <div class="col-lg-2 pl"><p>IFSC</p></div>
                <div class="col-lg-2 pl">
                  <label> {{$objResponse->IFSC}} </label>
                </div>

                <div class="col-lg-2 pl col-md-offset-1"><p>Account Type</p></div>
                <div class="col-lg-2 pl">
                <label> {{$objResponse->ACCOUNT_TYPE}} </label>
                </div>
                
                <div class="col-lg-1 pl"><p>A/c No</p></div>
                <div class="col-lg-2 pl">
                  <label> {{$objResponse->ACCOUNT_NO}} </label>
                 
                </div>
                
              </div>
          



                
              <div class="row">
                <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-1 pl">
                <label> {{$objResponse->DEACTIVATED == 1 ? "Yes" : ""}} </label>
                
                </div>
                
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <label> {{ (is_null($objResponse->DODEACTIVATED) || $objResponse->DODEACTIVATED=='1900-01-01' )?'':
                  \Carbon\Carbon::parse($objResponse->DODEACTIVATED)->format('d/m/Y')   }} </label>
                </div>
          </div>
          

          </div>

    </div><!--purchase-order-view-->

    <script>
     $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[34,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });
    </script>

@endsection