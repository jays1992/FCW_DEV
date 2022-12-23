@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[4,'index'])}}" class="btn singlebt">Country Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button id="btnSaveCountry"   class="btn topnavbt" disabled="disabled" tabindex="7"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" disabled="disabled" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</button>
                        <a href="{{route('home')}}" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
          <div class="inner-form">
          
              
                <div class="row">
                  <div class="col-lg-2 pl"><p>Country Code</p></div>
                  <div class="col-lg-3 pl">
                    <label> {{$objCountry->CTRYCODE}} </label>
                  </div>
                
                  <div class="col-lg-2 pl col-md-offset-1"><p>Country Name</p></div>
                  <div class="col-lg-3 pl">
                    <label> {{$objCountry->NAME}} </label>
                  </div>
                </div>
          
          
              <div class="row">
                <div class="col-lg-2 pl"><p>ISD Code</p></div>
                <div class="col-lg-3 pl">
                    <label> {{$objCountry->ISDCODE}} </label>
                </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Language</p></div>
                <div class="col-lg-3 pl ">
                  <label> {{$objCountry->LANG}} </label>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-2 pl"><p>Continental</p></div>
                <div class="col-lg-3 pl">
                  <label> {{$objCountry->CONTINENTAL}} </label>
                </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Capital</p></div>
                <div class="col-lg-3 pl">
                  <label> {{$objCountry->CAPITAL}} </label>
                </div>
              </div>

          </div>

    </div><!--purchase-order-view-->

@endsection