@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[74,'index'])}}" class="btn singlebt">Item Group & Sub Group</a>
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
                  <div class="col-lg-2 pl"><p>Item Group Code</p></div>
                  <div class="col-lg-1 pl">
                    <label> {{$objResponse->GROUPCODE}} </label>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Item Group Name</p></div>
                  <div class="col-lg-4 pl">
                    <label> {{$objResponse->GROUPNAME}} </label>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>Purchase AC Set</p></div>
                  <div class="col-lg-3 pl">
                    <div class="col-lg-7 pl">
                        <input type="text" name="PUR_AC_SET_POPUP" id="PUR_AC_SET_POPUP" value="{{$PurAccountListName}}" class="form-control mandatory" readonly tabindex="3" required />
                        <input type="hidden" value="{{$PurAccountListID}}" name="PURCHASE_AC_SETID_REF" id="PURCHASE_AC_SETID_REF" />
                        <span class="text-danger" id="ERROR_PAC_SETID_REF"></span>
                    </div>
                  </div>
                  <div class="col-lg-2 pl"><p>Sale AC Set</p></div>
                  <div class="col-lg-3 pl">
                    <div class="col-lg-7 pl">
                        <input type="text" name="SALE_AC_SET_POPUP" id="SALE_AC_SET_POPUP" value="{{$salesAccountListName}}" class="form-control mandatory" readonly tabindex="4" required/>
                        <input type="hidden" name="SALES_AC_SETID_REF" value="{{$salesAccountListID}}" id="SALES_AC_SETID_REF" />
                        <span class="text-danger" id="ERROR_SAC_SETID_REF"></span>
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

          <div class="row">
                  <div class="col-lg-6 pl">
                    <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:300px;" >
                      <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                        <thead id="thead1"  style="position: sticky;top: 0">
                          <tr>
                          <th>
                              Item Sub Group Code 
                              <input class="form-control" type="hidden" name="Row_Count" id ="Row_Count"> 
                              <input type="hidden" id="focusid" >
                              <input type="hidden" id="errorid" >
                              
                          </th>
                          <th>Description</th>
                          <th width="5%">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                        @if(!empty($objDataResponse))
                        @php $n=1; @endphp
                        @foreach($objDataResponse as $key => $row)
                          <tr  class="participantRow">
                            <td><input disabled  class="form-control w-100"  type="text" name={{"ISGCODE_".$key}} id ={{"txtisgcode_".$key}}  value="{{ $row->ISGCODE }}" maxlength="20" autocomplete="off" style="text-transform:uppercase;width:100%;" ></td>
                              <td><input disabled class="form-control w-100" type="text" name={{"DESCRIPTIONS_".$key}} id ={{"txtdesc_".$key}} value="{{ $row->DESCRIPTIONS }}" maxlength="200" autocomplete="off" style="width:100%;" ></td>

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
      var viewURL = '{{route("master",[74,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });
    </script>

@endsection