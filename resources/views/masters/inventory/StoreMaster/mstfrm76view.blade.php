@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[76,'index'])}}" class="btn singlebt">Store Master</a>
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
                  <div class="col-lg-2 pl"><p>Store Code</p></div>
                  <div class="col-lg-1 pl">
                    <label> {{$objResponse->STCODE}} </label>
                  </div>
                </div>                

                <div class="row">
                  <div class="col-lg-2 pl"><p>Store Name</p></div>
                  <div class="col-lg-5 pl">
                    <input type="text" name="NAME" id="NAME" class="form-control mandatory" value="{{$objResponse->NAME}}" maxlength="200" disabled tabindex="2"  />
                    <span class="text-danger" id="ERROR_NAME"></span> 
                  </div>
                </div>               


                <div class="row">
                  <div class="col-lg-2 pl"><p>Registered Address Line 1</p></div>
                  <div class="col-lg-4 pl">
                    <input type="text" name="Address1" id="Address1" class="form-control mandatory" value="{{ $objMstCust->Address1 }}"  maxlength="200" required disabled tabindex="7" >
                    <span class="text-danger" id="ERROR_REGADDL1"></span>
                  </div>
                  
                    <div class="col-lg-2 pl"><p>Registered Address Line 2</p></div>
                  <div class="col-lg-4 pl">
                    <input type="text" name="Address2" id="Address2" value="{{ $objMstCust->Address2 }}" class="form-control" disabled  maxlength="200" tabindex="8" >
                  </div>
                
                </div>
                
                <div class="row">            
                  <div class="col-lg-2 pl"><p>Country</p></div>
                  <div class="col-lg-2 pl">
                    <input type="text" name="CTRYID_REF" id="REGCTRYID_REF_POPUP" class="form-control mandatory" value="{{ $objRegCountry->CTRYCODE }} - {{ $objRegCountry->NAME }}" required disabled tabindex="9" />
                  </div>
                  
                  <div class="col-lg-1 pl"><p>State</p></div>
                  <div class="col-lg-2 pl">
                    <input type="text" name="STID_REF" id="REGSTID_REF_POPUP" class="form-control mandatory" value="{{ $objRegState->STCODE }} - {{ $objRegState->NAME }}" required disabled tabindex="10" />
                  </div>
                
                  <div class="col-lg-1 pl"><p>City</p></div>
                  <div class="col-lg-2 pl">
                    <input type="text" name="CITYID_REF" id="REGCITYID_REF_POPUP" class="form-control mandatory" value="{{ $objRegCity->CITYCODE }} - {{ $objRegCity->NAME }}"  required disabled tabindex="11" />
                  </div>                 

                  <div class="col-lg-1 pl"><p>Pincode</p></div>
                  <div class="col-lg-1 pl">
                    <input type="text" name="PINCODE" id="PINCODE" value="{{ $objMstCust->PINCODE }}" class="form-control "  maxlength="10" tabindex="12" disabled  autocomplete="off" >
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

          <div class="row">
                  <div class="col-lg-6 pl">
                    <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:300px;" >
                      <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                        <thead id="thead1"  style="position: sticky;top: 0">
                        <tr>
                          <th>
                              Rack No
                              <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count"> 
                              <input type="hidden" id="focusid" >
                              <input type="hidden" id="errorid" >
                              
                          </th>
                          <th>Rack Description</th>
                          <th>BIN No</th>
                          <th width="5%">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                        @if(!empty($objDataResponse))
                        @php $n=1; @endphp
                        @foreach($objDataResponse as $key => $row)
                          <tr  class="participantRow">
                              <td><input disabled  class="form-control w-100" type="text" name={{"RACKNO_".$key}} id ={{"txtrackno_".$key}}  value="{{ $row->RACKNO }}" maxlength="30" autocomplete="off" style="text-transform:uppercase" ></td>
                              <td><input disabled  class="form-control w-100" type="text" name={{"DESCRIPTIONS_".$key}} id ={{"txtdesc_".$key}} value="{{ $row->DESCRIPTIONS }}" maxlength="200" autocomplete="off" ></td>
                              <td><input disabled  class="form-control w-100" type="text" name={{"BINNO_".$key}} id ={{"txtbin_".$key}} value="{{ $row->BINNO }}" maxlength="9" autocomplete="off" ></td>
                              

                              <td align="center" >
                                  <button disabled class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                                  <button disabled class="btn remove" title="Delete" data-toggle="tooltip" disabled><i class="fa fa-trash" ></i></button>
                              </td>
                          </tr>
                          @php $n++; @endphp
                          @endforeach 
                          @endif     
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
          

          </div>

    </div><!--purchase-order-view-->

    <script>
     $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[76,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });
    </script>

@endsection