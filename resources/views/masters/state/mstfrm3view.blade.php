@extends('layouts.app')
@section('content')

<script>
function getCountryName(CTRYID){
		$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
		
        $.ajax({
            url:'{{route("master",[3,"getCountryName"])}}',
            type:'POST',
            data:{CTRYID:CTRYID},
            success:function(data) {
               $("#COUNTRYNAME").text(data);                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
		
		$.ajax({
            url:'{{route("master",[3,"getCountryCode"])}}',
            type:'POST',
            data:{CTRYID:CTRYID},
            success:function(data) {
               $("#COUNTRYCODE").text(data);                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });	
		
  }
  
  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[3,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });


$(function() { 
	$("#CTRYID_REF").focus(); 
	getCountryName('{{$objCountry->CTRYID_REF}}')
});

</script>
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[3,'index'])}}" class="btn singlebt">State Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <a href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</a>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button id="btnSaveCountry"   class="btn topnavbt" disabled="disabled" tabindex="7"><i class="fa fa-floppy-o"></i> Save</button>
                        <a class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</a>
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-print"></i> Print</a>
                        <a href="#" class="btn topnavbt" disabled="disabled" ><i class="fa fa-undo"></i> Undo</a>
                        <a href="#" class="btn topnavbt" disabled="disabled"><i class="fa fa-times"></i> Cancel</a>
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</a>
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</a>
                        <a href="{{route('home')}}" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
          <div class="inner-form">
          
              
			<div class="row">
                  <div class="col-lg-1 pl"><p>Country Code</p></div>
                  <div class="col-lg-1 pl">
                    <label id="COUNTRYCODE"></label>
                  </div>
                
                  <div class="col-lg-1 pl col-md-offset-3"><p>Country Name</p></div>
                  <div class="col-lg-4 pl">
                     <label id="COUNTRYNAME"></label>
                  </div>
                </div>
				
                <div class="row">
                  <div class="col-lg-1 pl"><p>State Code</p></div>
                  <div class="col-lg-1 pl">
                    <label> {{$objCountry->STCODE}} </label>
                  </div>
                
                  <div class="col-lg-1 pl col-md-offset-3"><p>State Name</p></div>
                  <div class="col-lg-4 pl">
                    <label> {{$objCountry->NAME}} </label>
                  </div>
                </div>
          
          
              <div class="row">
                <div class="col-lg-1 pl"><p>STD Code</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-8 pl">
                    <label> {{$objCountry->STDCODE}} </label>
                  </div>
                </div>
                
                <div class="col-lg-1 pl col-md-offset-2"><p>Main Language</p></div>
                <div class="col-lg-2 pl ">
                  <label> {{$objCountry->LANG}} </label>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-1 pl"><p>NEWSC India</p></div>
                <div class="col-lg-3 pl">
                  <label> {{$objCountry->NEWSC}} </label>
                </div>
                
                <div class="col-lg-1 pl col-md-offset-1"><p>Capital</p></div>
                <div class="col-lg-3 pl">
                  <label> {{$objCountry->CAPITAL}} </label>
                </div>
              </div>
			  
              <div class="row">
                      <div class="col-lg-1 pl"><p>State / UT</p></div>
                      <div class="col-lg-3 pl">
                        <label> {{$objCountry->STTYPE}} </label>
                      </div>
                    
              </div>


              <div class="row">
                <div class="col-lg-1 pl"><p>De-Activated</p></div>
                <div class="col-lg-3 pl">
                <label> {{$objCountry->DEACTIVATED == 1 ? "Yes" : ""}} </label>
                
                </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Date of De-Activated</p></div>
                <div class="col-lg-3 pl">
                  <label> {{ (is_null($objCountry->DODEACTIVATED) || $objCountry->DODEACTIVATED=='1900-01-01' )?'':
                  \Carbon\Carbon::parse($objCountry->DODEACTIVATED)->format('d/m/Y')   }} </label>
                </div>
          </div>

			  


              </div>

     

            <!--
              <div class="row">
                <div class="col-lg-1 pl"><p>De-Activated</p></div>
                <div class="col-lg-3 pl">
                <input type="checkbox"   name="DEACTIVATED"  {{$objCountry->DEACTIVATED == 1 ? "checked" : ""}}
                 value='{{$objCountry->DEACTIVATED == 1 ? 1 : 0}}' tabindex="8"  >
                </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Date of De-Activated</p></div>
                <div class="col-lg-3 pl">
                  <label> {{ (is_null($objCountry->DODEACTIVATED) || $objCountry->DODEACTIVATED=='1900-01-01' )?'':
                  \Carbon\Carbon::parse($objCountry->DODEACTIVATED)->format('d/m/Y')   }} </label>
                </div>
          </div>

              -->



    </div><!--purchase-order-view-->

@endsection