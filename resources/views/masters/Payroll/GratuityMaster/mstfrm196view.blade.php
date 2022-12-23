@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[196,'index'])}}" class="btn singlebt">Gratuity Master</a>
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
                        <button class="btn topnavbt" id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">    
          <div class="inner-form">
          
              
          <div class="row">
          <div class="col-lg-2 pl"><p>Gratuity Code</p></div>
                  <div class="col-lg-2 pl">
                  <label> {{$objResponse->GRATUITY_CODE}} </label>
                    <input type="hidden" name="GRATUITYID" id="GRATUITYID" value="{{ $objResponse->GRATUITYID }}" />
                    <input type="hidden" name="GRATUITY_CODE" id="GRATUITY_CODE" value="{{ $objResponse->GRATUITY_CODE }}" autocomplete="off"  maxlength="20"   />
               
                  </div>
                  <div class="col-lg-2 pl"><p>Description</p></div>
                  <div class="col-lg-5 pl">
                    <input type="text" name="GRATUITY_DESC" id="GRATUITY_DESC" disabled class="form-control mandatory" value="{{ $objResponse->GRATUITY_DESC }}" maxlength="200" tabindex="2"  />
                    <span class="text-danger" id="ERROR_GRATUITY_DESC"></span> 
                  </div>

                </div>


                <div class="row">
                <div class="col-lg-2 pl"><p>Gratuity Type</p></div>
                <input type="radio" name="GratuityType" id="gratuity_type1" value="1" style="margin-right: 10;" disabled {{$objResponse->GRATUITY_TYPE == 1 ? "checked" : ""}} >
                <div class="col-lg-1 pl"><p>Gratuity Rate (%)</p></div>
                <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">
                        <input
                            type="text"
                            name="GRATUITY_RATE" disabled
                            id="GRATUITY_RATE"
                            value='{{$objResponse->GRATUITY_RATE == "null" ? "" : $objResponse->GRATUITY_RATE}}'
                            class="form-control mandatory"
                            autocomplete="off"
                            maxlength="20"
                            tabindex="1"
                        />
                        <span class="text-danger" id="ERROR_GRATUITY_RATE"></span>
                    </div>
                </div>
                <div class="col-lg-2 pl"><p>Minimum Tenure in years for eligibility</p></div>
                <div class="col-lg-2 pl">
                    <div class="col-lg-11 pl">
                        <input type="text" name="MIN_YEAR" id="MIN_YEAR" value='{{$objResponse->MIN_YEAR == "null" ? "" : $objResponse->MIN_YEAR}}' class="form-control mandatory" autocomplete="off" maxlength="20" tabindex="1" />
                        <span class="text-danger" id="ERROR_MIN_YEAR"></span>
                    </div>
                </div>
                <div class="col-lg-1 pl"><p>Max Gratuity</p></div>
                <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                        <input
                            type="text"
                            name="MAX_GRATUITY" disabled
                            id="MAX_GRATUITY"
                            value='{{$objResponse->MAX_GRATUITY == "null" ? "" : $objResponse->MAX_GRATUITY}}'
                            class="form-control mandatory"
                            autocomplete="off"
                            maxlength="20"
                            tabindex="1"
                        />
                        <span class="text-danger" id="ERROR_MAX_GRATUITY"></span>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-left: 244px;">
                <div class="col-lg-2 pl"><p>No of days of Gratuity per annum</p></div>
                <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                        <input
                            type="text"
                            name="GRATUITY_DAYS" disabled
                            id="GRATUITY_DAYS"
                            value='{{$objResponse->GRATUITY_DAYS == "null" ? "" : $objResponse->GRATUITY_DAYS}}'
                            class="form-control mandatory"
                            autocomplete="off"
                            maxlength="20"
                            tabindex="1"
                        />
                        <span class="text-danger" id="ERROR_GRATUITY_DAYS"></span>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-left: 221px;">
                <input type="radio" name="GratuityType" id="gratuity_type2" disabled value="2" style="margin-right: 10;" {{$objResponse->GRATUITY_TYPE == 2 ? "checked" : ""}} >
                <div class="col-lg-1 pl"><p>Fix Gratuity</p></div>
                <div class="col-lg-1 pl">
                    <div class="col-lg-11 pl">
                        <input
                            type="text" disabled
                            name="GRATUITY_FIX"
                            id="GRATUITY_FIX"
                            value='{{$objResponse->GRATUITY_FIX == "null" ? "" : $objResponse->GRATUITY_FIX}}'
                            class="form-control mandatory"
                            autocomplete="off"
                            maxlength="20"
                            tabindex="1"
                            style="width: 93px; margin-left: 21px;"
                        />
                        <span class="text-danger" id="ERROR_GRATUITY_FIX"></span>
                    </div>
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
      var viewURL = '{{route("master",[196,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });
    </script>

@endsection