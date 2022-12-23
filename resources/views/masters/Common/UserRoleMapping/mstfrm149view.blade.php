@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[149,'index'])}}" class="btn singlebt">User - Role Mapping</a>
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
                  <div class="col-lg-2 pl"><p>User</p></div>
                  <div class="col-lg-2 pl">
                  <label> {{isset($objUserName->UCODE) && $objUserName->UCODE !=''?$objUserName->UCODE:''}} </label>

                   
                  </div>

                  <div class="col-lg-3 pl">
                  <label> {{isset($objUserName->DESCRIPTIONS) && $objUserName->DESCRIPTIONS !=''?$objUserName->DESCRIPTIONS:''}} </label>
                  </div>

                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Role</p></div>
                  <div class="col-lg-2 pl">
                  <label> {{isset($objRoleName->RCODE) && $objRoleName->RCODE !=''?$objRoleName->RCODE:''}} </label>
           
                  </div>

                  <div class="col-lg-3 pl">
                    <label> {{isset($objRoleName->DESCRIPTIONS) && $objRoleName->DESCRIPTIONS !=''?$objRoleName->DESCRIPTIONS:''}} </label>
                    
                  </div>
                </div>

                
                <div class="row">

                <div class="col-lg-2 pl"><p>Effective Date</p></div>
                  <div class="col-lg-2 pl">
                  
                  <label> {{ (is_null($objResponse->EFDATE) || $objResponse->EFDATE=='1900-01-01' )?'':
                  \Carbon\Carbon::parse($objResponse->EFDATE)->format('d/m/Y')   }} </label>
                  </div>

                  <div class="col-lg-1 pl"><p>End Date</p></div>
                  <div class="col-lg-2 pl">
                  
                  <label> {{ (is_null($objResponse->ENDDATE) || $objResponse->ENDDATE=='1900-01-01' )?'':
                  \Carbon\Carbon::parse($objResponse->ENDDATE)->format('d/m/Y')   }} </label>
                  </div>

                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Reason of End Date</p></div>
                  <div class="col-lg-5 pl">
                    <label> {{$objResponse->REASONOED}} </label>
                   
                  </div>
                </div>



          </div>

    </div><!--purchase-order-view-->

    <script>
     $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[149,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });
    </script>

@endsection