@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[125,'index'])}}" class="btn singlebt">City Master</a>
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
                  <div class="col-lg-2 pl"><p>Country Code</p></div>
                  <div class="col-lg-3 pl">
                    <label> {{isset($objCountryName->CTRYCODE)?$objCountryName->CTRYCODE:''}} </label>
                  </div>

                  <div class="col-lg-2 pl col-md-offset-1"><p>Country Name</p></div>
                  <div class="col-lg-3 pl">
                    <label> {{isset($objCountryName->NAME)?$objCountryName->NAME:''}} </label>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>State Code</p></div>
                  <div class="col-lg-3 pl">
                    <label> {{isset($objStateName->STCODE)?$objStateName->STCODE:''}} </label>
                  </div>

                  <div class="col-lg-2 pl col-md-offset-1"><p>State Name</p></div>
                  <div class="col-lg-3 pl">
                    <label> {{isset($objStateName->NAME)?$objStateName->NAME:''}} </label>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>City Code</p></div>
                  <div class="col-lg-3 pl">
                    <label> {{$objResponse->CITYCODE}} </label>
                  </div>

                  <div class="col-lg-2 pl col-md-offset-1"><p>City Name</p></div>
                  <div class="col-lg-3 pl">
                    <label> {{$objResponse->NAME}} </label>
                  </div>

                </div>

               

                
              <div class="row">
                <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-3 pl">
                <label> {{$objResponse->DEACTIVATED == 1 ? "Yes" : ""}} </label>
                
                </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Date of De-Activated</p></div>
                <div class="col-lg-3 pl">
                  <label> {{ (is_null($objResponse->DODEACTIVATED) || $objResponse->DODEACTIVATED=='1900-01-01' )?'':
                  \Carbon\Carbon::parse($objResponse->DODEACTIVATED)->format('d/m/Y')   }} </label>
                </div>
          </div>
          

          </div>

    </div><!--purchase-order-view-->

    <script>
     $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[125,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });
    </script>

@endsection