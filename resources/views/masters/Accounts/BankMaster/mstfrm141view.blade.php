@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[141,'index'])}}" class="btn singlebt">Bank / Cash Master</a>
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
                    <div class="col-lg-2 pl"><p>Bank Code</p></div>
                    <div class="col-lg-2 pl">
                    <label> {{$objResponse->BCODE}} </label>
                    </div>
                    
                    <div class="col-lg-2 pl col-md-offset-2"><p>Name</p></div>
                    <div class="col-lg-4 pl">
                    <label> {{$objResponse->NAME}} </label>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-lg-2 pl"><p>Branch</p></div>
                    <div class="col-lg-3 pl">
                    <label> {{$objResponse->BRANCH}} </label>
                     
                    </div>
                    
                    <div class="col-lg-2 pl col-md-offset-1"><p>IFSC</p></div>
                    <div class="col-lg-2 pl">
                    <label> {{$objResponse->IFSC}} </label>
                      
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-2 pl"><p>Type (Bank/Cash)</p></div>
                    <div class="col-lg-2 pl">
                      <div class="col-lg-12 pl">
                        <label> 
                          {{isset($objResponse->BANK_CASH) && strtoupper($objResponse->BANK_CASH) =="B"?'Bank':''}}
                          {{isset($objResponse->BANK_CASH) && strtoupper($objResponse->BANK_CASH) =="C"?'Cash':''}}                        
                        </label>
                      </div>
                    </div>            
                  </div>

                  
                  <div class="row">
                    <div class="col-lg-2 pl"><p>Account Type</p></div>
                    <div class="col-lg-2 pl">
                    <label> {{$objResponse->ACTYPE}} </label>

           
                    </div>
                    
                    <div class="col-lg-2 pl col-md-offset-2"><p>Account No</p></div>
                    <div class="col-lg-2 pl">
                    <label> {{$objResponse->ACNO}} </label>
                      
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-lg-2 pl"><p>OD Limit</p></div>
                    <div class="col-lg-1 pl">
                    <label> {{$objResponse->ODLIMIT}} </label>
                      <div class="col-lg-12 pl">
                     
                      </div>
                    </div>
                    
                    <div class="col-lg-2 pl col-md-offset-3"><p>Branch Timing</p></div>
                    <div class="col-lg-1 pl">
                    @php
                      $starr = explode(".",$objResponse->BRANCHTIME);
                    @endphp
                    <input type="time" name="BRANCHTIME" id="BRANCHTIME" class="form-control " value="{{isset($starr[0]) ? $starr[0] : '' }}" disabled  >
                      
                    </div>
                  </div>
                  
                  
                  <div class="row">
                    <div class="col-lg-2 pl"><p>Address 1</p></div>
                    <div class="col-lg-4 pl">
                    <label> {{$objResponse->ADD1}} </label>
                     
                    </div>
                    
                    <div class="col-lg-2 pl "><p>Address 2</p></div>
                    <div class="col-lg-4 pl">
                    <label> {{$objResponse->ADD2}} </label>
                     
                    </div>
                  </div>
                  
                  <div class="row">
                  <div class="col-lg-2 pl"><p>Country</p></div>
                    <div class="col-lg-2 pl">
                    <label> {{isset($objCountryName->CTRYCODE)?$objCountryName->CTRYCODE. ' - ':''}}  {{isset($objCountryName->NAME)?$objCountryName->NAME:''}} </label>
                     
                    
                    </div>

                    <div class="col-lg-2 pl col-md-offset-2"><p>State</p></div>
                    <div class="col-lg-2 pl">
                    <label> {{isset($objStateName->STCODE)?$objStateName->STCODE. ' - ':''}}  {{isset($objStateName->NAME)?$objStateName->NAME:''}} </label>
                     
                    </div>

                  </div>
                  
                  <div class="row">
                    
                  <div class="col-lg-2 pl"><p>City</p></div>
                    <div class="col-lg-2 pl">
                    <label> {{isset($objCityName->CITYCODE)?$objCityName->CITYCODE. ' - ':''}}  {{isset($objCityName->NAME)?$objCityName->NAME:''}} </label>
                    </div>
                    
                    <div class="col-lg-2 pl col-md-offset-2"><p>PinCode</p></div>
                    <div class="col-lg-2 pl">
                    <label> {{$objResponse->PIN}} </label>
                    </div>
                    
                    
                  </div>

                    
                  
                  <div class="row">
                  
                    <div class="col-lg-2 pl "><p>Landmark</p></div>
                    <div class="col-lg-4 pl">
                    <label> {{$objResponse->LANDMARK}} </label>
                     
                    </div>
                    
                    <div class="col-lg-2 pl"><p>Date of Opening</p></div>
                    <div class="col-lg-2 pl">
                  
                    <label> {{ (is_null($objResponse->DOO) || $objResponse->DOO=='1900-01-01' )?'':
                  \Carbon\Carbon::parse($objResponse->DOO)->format('d/m/Y')   }} </label>
                   
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-lg-2 pl"><p>Signing Authority</p></div>
                    <div class="col-lg-4 pl">
                    <label> {{$objResponse->SIGNAUTH}} </label>
                     
                    </div>

                    <div class="col-lg-2 pl"><p>GL</p></div>
                    <div class="col-lg-4 pl">
                      <label> {{isset($objGlName->GLCODE) && $objGlName->GLCODE !=''?$objGlName->GLCODE:''}} {{isset($objGlName->GLNAME) && $objGlName->GLNAME !=''?' - '.$objGlName->GLNAME:''}}</label>
                    </div>
                  </div>
				  <div class="row">
                    <div class="col-lg-2 pl"><p>Bank Charges GL</p></div>
                    <div class="col-lg-4 pl">
                        <label> {{isset($objBankGlName->GLCODE) && $objBankGlName->GLCODE !=''?$objBankGlName->GLCODE:''}} {{isset($objBankGlName->GLNAME) && $objBankGlName->GLNAME !=''?' - '.$objBankGlName->GLNAME:''}}</label>  
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
      var viewURL = '{{route("master",[141,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });
    </script>

@endsection