@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Item Coding Definition</a>
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

          <input type="hidden" name="ITEMCODEDEFIID" id="ITEMCODEDEFIID" value="{{isset($objResponse->ITEMCODEDEFIID) && $objResponse->ITEMCODEDEFIID !=''?$objResponse->ITEMCODEDEFIID:''}}" />
      <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
                             
      <div class="row" >
          <div class="col-lg-2 pl"><p>Manual Series</p></div>
          <div class="col-lg-1 pl">
            <input type="checkbox" name="MANUAL_SR" id="MANUAL_SR" {{$objResponse->MANUAL_SR == 1 ? "checked" : ""}} value='1' onchange="SeriesType('MANUAL_SR');" disabled >
          </div>
          <div class="col-lg-1 pl"><p>OR</p></div>
          <div class="col-lg-2 pl"><p>System generated</p></div>
          <div class="col-lg-1 pl">
            <input type="checkbox" name="SYSTEM_GRSR" id="SYSTEM_GRSR" {{$objResponse->SYSTEM_GRSR == 1 ? "checked" : ""}} value='1' onchange="SeriesType('SYSTEM_GRSR');" disabled >
          </div>
      </div>

      <div class="row"><div class="col-lg-2 pl"><p style="text-decoration: underline;font-weight:bold;font-size:13px;">Pattern for Manual Code</p></div></div>

      <div class="row"  >
        <div class="col-lg-2 pl"><p>Maximum Length</p></div>
        <div class="col-lg-1 pl col-md-offset-1">
            <input type="text" name="MANUAL_MAXLENGTH" id="MANUAL_MAXLENGTH"  value="{{isset($objResponse->MANUAL_MAXLENGTH) && $objResponse->MANUAL_MAXLENGTH !=''?$objResponse->MANUAL_MAXLENGTH:''}}" class="form-control mandatory" autocomplete="off" onkeypress="return isNumberKey(event,this)"  maxlength="9" disabled tabindex="3" >
            <span class="text-danger error" id="ERROR_MANUAL_MAXLENGTH"></span>
        </div>
      </div>

      <div class="row"><div class="col-lg-2 pl"><p style="text-decoration: underline;font-weight:bold;font-size:13px;">Pattern for Auto Series</p></div></div>

      <div class="row">
          <div class="col-lg-2 pl"><p>Item Code max digit</p></div>
          
          <div class="col-lg-1 pl col-md-offset-1">
            <input type="text" name="MAX_DIGIT" id="MAX_DIGIT" value="{{isset($objResponse->MAX_DIGIT) && $objResponse->MAX_DIGIT !=''?$objResponse->MAX_DIGIT:''}}" class="form-control mandatory" autocomplete="off"  maxlength="8"  onkeypress="return isNumberKey(event,this)" tabindex="4" disabled >
          </div>

          <div class="col-lg-2 pl"><p>Number Series Start from</p> </div>
          <div class="col-lg-1 pl">
            <input type="text" name="NO_START" id="NO_START" value="{{isset($objResponse->NO_START) && $objResponse->NO_START !=''?$objResponse->NO_START:''}}" class="form-control mandatory" autocomplete="off"  maxlength="8" onkeypress="return isNumberKey(event,this)" tabindex="5" disabled >
            <span class="text-danger error" id="ERROR_NO_START"></span>
          </div>
          
          <div class="col-lg-2 pl"><p>Item Code (Prefix)</p></div>
          <div class="col-lg-1 pl">
            <input type="text" name="PREFIX" id="PREFIX" value="{{isset($objResponse->PREFIX) && $objResponse->PREFIX !=''?$objResponse->PREFIX:''}}" class="form-control mandatory" autocomplete="off"  maxlength="4" onkeypress="return AlphaNumaric(event,this)" style="text-transform:uppercase" tabindex="6" disabled >
            <span class="text-danger error" id="ERROR_PREFIX"></span>
          </div>
      </div>

		

             
              

              <div class="row">
                <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-1 pl pr">
                <input type="checkbox"   name="DEACTIVATE"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATE == 1 ? "checked" : ""}}
                 value='{{$objResponse->DEACTIVATE == 1 ? 1 : 0}}' tabindex="7" disabled >
                </div>
                
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                  <input type="date" name="DODEACTIVATE" class="form-control" id="DODEACTIVATE" {{$objResponse->DEACTIVATE == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATE) && $objResponse->DODEACTIVATE !="" && $objResponse->DODEACTIVATE !="1900-01-01" ? $objResponse->DODEACTIVATE:''}}" tabindex="8" placeholder="dd/mm/yyyy" disabled  />
                </div>
             </div>
          

          </div>

    </div><!--purchase-order-view-->

    <script>
     $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[$FormId,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });




  </script>

@endsection