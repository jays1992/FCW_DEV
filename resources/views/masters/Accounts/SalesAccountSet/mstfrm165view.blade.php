@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[165,'index'])}}" class="btn singlebt">Sales Account Set</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <a href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</a>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button id="btnSaveCountry"   class="btn topnavbt" disabled="disabled" tabindex="7"><i class="fa fa-save"></i> Save</button>
                        <a class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</a>
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-print"></i> Print</a>
                        <a href="#" class="btn topnavbt" disabled="disabled" ><i class="fa fa-undo"></i> Undo</a>
                        <a href="#" class="btn topnavbt" disabled="disabled"><i class="fa fa-times"></i> Cancel</a>
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-lock"></i> Approved</a>
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</a>
                        <a href="{{route('home')}}" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	 
   
    <div class="container-fluid purchase-order-view filter">     
          <div class="inner-form">
          
              
   
          <div class="row">
          <div class="col-lg-2 pl"><p>Account set code</p></div>
                    <div class="col-lg-2 pl">
                      <div class="col-lg-7 pl">
                      <label> {{$objResponse->AC_SET_CODE}} </label>
                      <input type="hidden" name="SL_AC_SETID" id="SL_AC_SETID" value="{{ $objResponse->SL_AC_SETID }}" />
                    <input type="hidden" name="AC_SET_CODE" id="AC_SET_CODE" value="{{ $objResponse->AC_SET_CODE }}" autocomplete="off"  maxlength="20"   />
                    
                  
                          <span class="text-danger" id="ERROR_AC_SET_CODE"></span> 
                      </div>
                    </div>

                    <div class="col-lg-2 pl"><p>Account set code Description</p></div>
                    <div class="col-lg-3 pl">
                    <label> {{$objResponse->AC_SET_DESC}} </label>
                      
                    </div>
                </div>
                
              <div class="row">
                  <div class="col-lg-2 pl"><p>Sales Account Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-7 pl">
                    <label> {{$objSalesAccoutName != '' ? $objSalesAccoutName->GLCODE : ''}} </label>

                    </div>
                  </div>  

                  <div class="col-lg-2 pl"><p>Sales Account Description</p></div>
                  <div class="col-lg-3 pl">
                  <label> {{$objSalesAccoutName != '' ? $objSalesAccoutName->GLNAME : ''}} </label>
                      
                  </div>
              </div>

              <div class="row">
                  <div class="col-lg-2 pl"><p>Sales Return Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-7 pl">
                    <label> {{$objSalesReturnName != '' ? $objSalesReturnName->GLCODE : ''}}</label>
                 
                    </div>
                  </div>

                  <div class="col-lg-2 pl"><p>Sales Return Description</p></div>
                  <div class="col-lg-3 pl">
                  <label>  {{$objSalesReturnName != '' ? $objSalesReturnName->GLNAME : ''}} </label>
                  </div>
              </div>

              <div class="row">
                    <div class="col-lg-2 pl"><p>Shortage Code</p></div>
                    <div class="col-lg-2 pl">
                      <div class="col-lg-7 pl">
                      <label> {{$objShortageName != '' ? $objShortageName->GLCODE : ''}} </label>
             
                      </div>
                    </div>

                    <div class="col-lg-2 pl"><p>Shortage Description</p></div>
                    <div class="col-lg-3 pl">
                    <label> {{$objShortageName != '' ? $objShortageName->GLNAME : ''}}</label>  
                    </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Cost of Good Sold Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-7 pl">
                    <label> {{$objCostOfGoodSoldName != '' ? $objCostOfGoodSoldName->GLCODE : ''}} </label>  

                    </div>
                  </div>

                  <div class="col-lg-2 pl"><p> Cost of Good Sold Description</p></div>
                  <div class="col-lg-3 pl">
                  <label> {{$objCostOfGoodSoldName != '' ? $objCostOfGoodSoldName->GLNAME : ''}}</label>  
                     
                  </div>
              </div>
              
              <div class="row">
                  <div class="col-lg-2 pl"><p>Export Sale Account Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-7 pl">
                    <label> {{$objExportSalesAcctName != '' ? $objExportSalesAcctName->GLCODE : ''}} </label>  
                    </div>
                  </div>

                  <div class="col-lg-2 pl"><p>Export Sale Account Description</p></div>
                  <div class="col-lg-3 pl">
                  <label> {{$objExportSalesAcctName != '' ? $objExportSalesAcctName->GLNAME : ''}}</label> 
                  </div>
              </div>
			  <div class="row">
                  <div class="col-lg-2 pl"><p>Cost of Good Sold Transfer Code</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-7 pl">
                    <label> {{$objCostOfGoodSoldExportName != '' ? $objCostOfGoodSoldExportName->GLCODE : ''}} </label>  

                    </div>
                  </div>

                  <div class="col-lg-2 pl"><p> Cost of Good Sold Transfer Description</p></div>
                  <div class="col-lg-3 pl">
                  <label> {{$objCostOfGoodSoldExportName != '' ? $objCostOfGoodSoldExportName->GLNAME : ''}}</label>  
                     
                  </div>
              </div>
			  <div class="row">
                  <div class="col-lg-2 pl"><p>Sales Account Code(Inter State)</p></div>
                  <div class="col-lg-2 pl">
                    <div class="col-lg-7 pl">
                    <label> {{$objSalesISAccoutName != '' ? $objSalesISAccoutName->GLCODE : ''}} </label>

                    </div>
                  </div>  

                  <div class="col-lg-2 pl"><p>Sales Account Description(Inter State)</p></div>
                  <div class="col-lg-3 pl">
                  <label> {{$objSalesISAccoutName != '' ? $objSalesISAccoutName->GLNAME : ''}} </label>
                      
                  </div>
              </div>
			  
             
              

              <div class="row">
                <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-1 pl pr">
                <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
                 value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  >
                </div>
                
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  />
                </div>
             </div>


     




    </div>

@endsection