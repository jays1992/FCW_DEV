@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[176,'index'])}}" class="btn singlebt">Project Master</a>
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
            <div class="col-lg-2 pl"><p>Project Code</p></div>
            <div class="col-lg-2 pl">
              <label> {{$objResponse->PCODE}} </label>
              <input type="hidden" name="PID" id="PID" value="{{ $objResponse->PID }}" />
          </div>

          <div class="col-lg-2 pl"><p>Project Name</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="DESCRIPTIONS" id="DESCRIPTIONS" class="form-control mandatory" value="{{ old('DESCRIPTIONS',$objResponse->DESCRIPTIONS) }}" maxlength="200" tabindex="1" disabled />
              <span class="text-danger" id="ERROR_DESCRIPTIONS"></span> 
            </div>

            <div class="col-lg-2 pl"><p>Project Start Date</p></div>
              <div class="col-lg-2 pl">
                <input type="date" name="PROJECT_START_DATE" id="PROJECT_START_DATE" class="form-control mandatory" value="{{ old('PROJECT_START_DATE',$objResponse->PROJECT_START_DATE) }}" disabled />
                <span class="text-danger" id="ERROR_PROJECT_START_DATE"></span> 
              </div>
          </div>

          <div class="row">
          
            <div class="col-lg-2 pl"><p>Project End Date</p></div>
              <div class="col-lg-2 pl">
                <input type="date" name="PROJECT_END_DATE" id="PROJECT_END_DATE" class="form-control mandatory" value="{{ old('PROJECT_END_DATE',$objResponse->PROJECT_END_DATE) }}" disabled />
                <span class="text-danger" id="ERROR_PROJECT_END_DATE"></span> 
              </div>

              <div class="col-lg-2 pl"><p>Project location</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="PROJECT_LOCATION" id="PROJECT_LOCATION" class="form-control mandatory" value="{{ old('PROJECT_LOCATION',$objResponse->PROJECT_LOCATION) }}" disabled />
                  <span class="text-danger" id="ERROR_PROJECT_LOCATION"></span> 
                </div>

                <div class="col-lg-2 pl"><p>Project Status</p></div>
                  <div class="col-lg-2 pl">
                    <select name="PROJECT_STATUS" id="PROJECT_STATUS" class="form-control"  autocomplete="off" disabled>
                      <option value="">Select</option>
                      <option value="Start" {{isset($objResponse->PROJECT_STATUS) && $objResponse->PROJECT_STATUS =="Start" ? "selected":""}}>Yet To Start</option>
                      <option value="Started" {{isset($objResponse->PROJECT_STATUS) && $objResponse->PROJECT_STATUS =="Started" ? "selected":""}}>Started</option>
                      <option value="Closed" {{isset($objResponse->PROJECT_STATUS) && $objResponse->PROJECT_STATUS =="Closed" ? "selected":""}}>Closed</option>
                    </select>
                    <span class="text-danger" id="ERROR_PROJECT_STATUS"></span> 
                  </div>
              </div>       

            <div class="row">
              <div class="col-lg-2 pl"><p>De-Activated</p></div>
              <div class="col-lg-1 pl pr">
              <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}
              value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="2"  disabled>
              </div>
              
              <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
              <div class="col-lg-2 pl">
                <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}} value="{{isset($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED !="" && $objResponse->DODEACTIVATED !="1900-01-01" ? $objResponse->DODEACTIVATED:''}}" tabindex="3" placeholder="dd/mm/yyyy"  disabled />
              </div>
          </div>

        </div>

    </div><!--purchase-order-view-->

    <script>
     $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[176,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });
    </script>

@endsection