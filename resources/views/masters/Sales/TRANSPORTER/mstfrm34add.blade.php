@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[34,'index'])}}" class="btn singlebt">Transporter Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled" ><i class="fa fa-plus"></i> Add</button>
                <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                <button class="btn topnavbt" id="btnSave"  tabindex="29"  ><i class="fa fa-save"></i> Save</button>
                <button class="btn topnavbt" id="btnView" disabled="disabled" ><i class="fa fa-eye"></i> View</button>
                <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                <button class="btn topnavbt" id='btnUndo' ><i class="fa fa-undo"></i> Undo</button>
                <button class="btn topnavbt" id="btnCancel"  disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
                <button class="btn topnavbt" id="btnApprove"  disabled="disabled" ><i class="fa fa-lock"></i> Approved</button>
                <button class="btn topnavbt"  id="btnAttach"  disabled="disabled" ><i class="fa fa-link"></i> Attachment</button>
                <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>

              </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
         <form id="frm_mst_transporter" method="POST"  > 
          @CSRF
          <div class="inner-form">
              <div class="row">
                  <div class="col-lg-2 pl"><p>Transporter Code</p></div>
                  <div class="col-lg-2 pl">
                  @if(!empty($objDD))
                    @if($objDD->SYSTEM_GRSR == "1")
                        <input type="text" name="TRANSPORTER_CODE" id="TRANSPORTER_CODE" value="{{ $objDOCNO }}" class="form-control mandatory" tabindex="1" maxlength="10" autocomplete="off" readonly style="text-transform:uppercase" autofocus >
                    @endif
                    @if($objDD->MANUAL_SR == "1")
                        <input type="text" name="TRANSPORTER_CODE" id="TRANSPORTER_CODE" value="{{ old('TRANSPORTER_CODE') }}" class="form-control mandatory"  maxlength="{{$objDD->MANUAL_MAXLENGTH}}" tabindex="1" autocomplete="off" style="text-transform:uppercase" autofocus >
                    @endif
                  @endif 
                      {{-- <input type="text" name="TRANSPORTER_CODE" id="TRANSPORTER_CODE" value="{{ old('TRANSPORTER_CODE') }}" class="form-control mandatory" autocomplete="off" maxlength="50" tabindex="1" style="text-transform:uppercase" /> --}}
                      <span class="text-danger" id="ERROR_TRANSPORTER_CODE"></span>
                  </div>
                  
                
                  <div class="col-lg-2 pl col-md-offset-1"><p>Transporter Name</p></div>
                  <div class="col-lg-5 pl">
                      <input type="text" name="TRANSPORTER_NAME" id="TRANSPORTER_NAME" class="form-control mandatory" value="{{ old('TRANSPORTER_NAME') }}" maxlength="100" tabindex="2"  />
                      <span class="text-danger" id="ERROR_TRANSPORTER_NAME"></span>
                  </div>

              </div>
       
              <div class="row">
                  <div class="col-lg-2 pl"><p>GL</p></div>
                  <div class="col-lg-2 pl">
                      <input type="text" name="GLID_REF_POPUP" id="GLID_REF_POPUP" class="form-control mandatory" required readonly tabindex="3" />
                      <input type="hidden" name="GLID_REF" id="GLID_REF" />
                      <span class="text-danger" id="ERROR_GLID_REF"></span>  
                  </div>
              </div>
              
              <div class="row">
                  <div class="col-lg-2 pl"><p>Transporter Registered Address Line 1</p></div>
                  <div class="col-lg-5 pl">
                      <textarea  name="REG_ADD1" id="REG_ADD1" class="form-control"  maxlength="200"   tabindex="4" ></textarea>
                  </div>
              </div>

              <div class="row">
                  <div class="col-lg-2 pl"><p>Transporter Registered Address Line 2</p></div>
                  <div class="col-lg-5 pl">
                      <textarea  name="REG_ADD2" id="REG_ADD2" class="form-control"  maxlength="200"  tabindex="5" ></textarea>
                  </div>
              </div>
              
             
              
              <div class="row">
                  <div class="col-lg-2 pl"><p>Country</p></div>
                  <div class="col-lg-2 pl">
                      <input type="text" name="CTRYID_REF_POPUP" id="CTRYID_REF_POPUP" class="form-control mandatory" readonly tabindex="6" />
                      <input type="hidden" name="CTRYID_REF" id="CTRYID_REF" />
                      <span class="text-danger" id="ERROR_CTRYID_REF"></span>
                  </div>

                  <div class="col-lg-2 pl col-md-offset-1"><p>State</p></div>
                  <div class="col-lg-2 pl">
                      <input type="text" name="STID_REF_POPUP" id="STID_REF_POPUP" class="form-control mandatory" required readonly tabindex="7" />
                      <input type="hidden" name="STID_REF" id="STID_REF" />
                      <span class="text-danger" id="ERROR_STID_REF"></span>
                  </div>

                  

              </div>

              <div class="row">

              <div class="col-lg-2 pl"><p>City</p></div>
                  <div class="col-lg-2 pl">
                      <input type="text" name="CITYID_REF_POPUP" id="CITYID_REF_POPUP" class="form-control" readonly tabindex="8" />
                      <input type="hidden" name="CITYID_REF" id="CITYID_REF" />
                      <span class="text-danger" id="ERROR_CITYID_REF"></span>
                  </div>

              <div class="col-lg-2 pl col-md-offset-1"><p>District</p></div>
                <div class="col-lg-2 pl">
                <input type="text" name="DISTID_REF_POPUP" id="DISTID_REF_POPUP" class="form-control" readonly tabindex="9" />
                <input type="hidden" name="DISTID_REF" id="DISTID_REF" />
                <span class="text-danger" id="ERROR_DISTID_REF"></span>
                </div>
              </div>
              
              <div class="row">
                

                <div class="col-lg-2 pl"><p>Pincode</p></div>
                <div class="col-lg-2 pl">
                    <input type="text" name="PINCODE" id="PINCODE" class="form-control"  maxlength="20" tabindex="10" >
                </div>

                <div class="col-lg-2 pl col-md-offset-1"><p>Landmark</p></div>
                <div class="col-lg-5 pl">
                  <input type="text" name="LANDMARK" id="LANDMARK" class="form-control"  maxlength="200" tabindex="11"  >
                </div>

              </div>
              
              
              
              
              <div class="row">
                
                <div class="col-lg-2 pl"><p>Email ID</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="EMAILID" id="EMAILID" class="form-control"  maxlength="100" tabindex="12" >
                </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Cell No</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="CELL_NO" id="CELL_NO" class="form-control"  maxlength="30" tabindex="13"  >
                </div>
                
                <div class="col-lg-1 pl"><p>Website</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="WEBSITE" id="WEBSITE" class="form-control"  maxlength="100" tabindex="14" >
                </div>
                
              </div>
              
              <div class="row">

                <div class="col-lg-2 pl"><p>Phone No</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="PHONE_NO" id="PHONE_NO" class="form-control"  maxlength="20" tabindex="15" >
                </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Whatsapp No</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="WHATSAPP_NO" id="WHATSAPP_NO" class="form-control"  maxlength="20" tabindex="16" >
                </div>
                
              </div>
              
              <div class="row"><br/></div>
              
              <div class="row">

                <div class="col-lg-2 pl"><p>Contact Person Name</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="CP_NAME" id="CP_NAME" class="form-control"  maxlength="100" tabindex="17" >
                </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Designation</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="CP_DESIGNATION" id="CP_DESIGNATION" class="form-control"  maxlength="100" tabindex="18" >
                </div>

                <div class="col-lg-1 pl"><p>Email ID</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="CP_EMAILID" id="CP_EMAILID" class="form-control"  maxlength="100" tabindex="19" >
                </div>
                
              </div>
              
              <div class="row">
                <div class="col-lg-2 pl"><p>Cell No</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="CP_CELL_NO" id="CP_CELL_NO" class="form-control"  maxlength="30" tabindex="20"  >
                </div>
                
                <div class="col-lg-2 pl col-md-offset-1"><p>Phone No</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="CP_PHONE_NO" id="CP_PHONE_NO" class="form-control"  maxlength="20" tabindex="21"  >
                </div>

                <div class="col-lg-1 pl"><p>GSTIN No</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="GSTIN_NO" id="GSTIN_NO" class="form-control mandatory"  maxlength="50" tabindex="22" >
                </div>
                
              </div>
              
              <div class="row">

                
                
                <div class="col-lg-2 pl"><p>PAN No</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="PAN_NO" id="PAN_NO" class="form-control"  maxlength="20" tabindex="23"  >
                </div>

                <div class="col-lg-2 pl col-md-offset-1"><p>CIN </p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="CIN" id="CIN" class="form-control"  maxlength="30" tabindex="24"  >
                </div>

                <div class="col-lg-1 pl"><p>Bank Name</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="BANK_NAME" id="BANK_NAME" class="form-control"  maxlength="100" tabindex="25"  >
                </div>
                
              </div>
              
         
              
              <div class="row">

                
                
                <div class="col-lg-2 pl "><p>IFSC</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="IFSC" id="IFSC" class="form-control"  maxlength="50" tabindex="26" >
                </div>

                <div class="col-lg-2 pl col-md-offset-1"><p>Account Type</p></div>
                <div class="col-lg-2 pl">
                  <select name="ACCOUNT_TYPE" id="ACCOUNT_TYPE" class="form-control" tabindex="27" >
                    <option value="" selected >Select</option>
                    <option value='SAVING ACCOUNT'>SAVING ACCOUNT</option>
                    <option value='CURRENT ACCOUNT'>CURRENT ACCOUNT</option>
                    <option value='OD'>OD</option>
                    <option value='OTHERS'>OTHERS</option>
                  </select>
                 
                </div>

                <div class="col-lg-1 pl"><p>A/c No</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="ACCOUNT_NO" id="ACCOUNT_NO" class="form-control"  maxlength="50" tabindex="28"  >
                </div>
                
              </div>
              
             
          </div>
        </form>
    </div><!--purchase-order-view-->
@endsection
@section('alert')
<!-- Alert -->
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='closePopup' >&times;</button>
        <h4 class="modal-title">System Alert Message</h4>
      </div>
      <div class="modal-body">
	  <h5 id="AlertMessage" ></h5>
        <div class="btdiv">    
            <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData">
              <div id="alert-active" class="activeYes"></div>Yes
            </button>
            <button class="btn alertbt" name='NoBtn' id="NoBtn"   data-funcname="fnUndoNo" >
              <div id="alert-active" class="activeNo"></div>No
            </button>
            <button class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">
                <div id="alert-active" class="activeOk"></div>OK</button>
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;">
              <div id="alert-active" class="activeOk1"></div>OK</button>
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!-- GL Alert -->
<div id="glrefpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='glrefpopup_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>GL</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="gl_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
            <td class="ROW2" style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="gl_codesearch" onkeyup="searchGLCode()"></td>
            <td class="ROW3" style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="gl_namesearch" onkeyup="searchGLName()"></td>
          </tr>
        </tbody>
      </table>
      
      <table id="gl_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody>
        @foreach ($objGlList as $index=>$GlList)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_GLID_REF[]" id="glref_{{ $GlList->GLID }}" class="clsglref" value="{{ $GlList->GLID }}" ></td>
          <td  class="ROW2" style="width: 39%">{{ $GlList->GLCODE }}
          <input type="hidden" id="txtglref_{{ $GlList->GLID }}" data-desc="{{ $GlList->GLCODE }} - {{ $GlList->GLNAME }}" value="{{ $GlList-> GLID }}"/>
          </td>
          <td class="ROW3" style="width: 39%">{{ $GlList->GLNAME }}</td>
        </tr>
        @endforeach
        </tbody>
      </table>

    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="ctryidref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='ctryidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Country</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="country_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
            <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="country_codesearch" onkeyup="searchCountryCode()"></td>
            <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control"  id="country_namesearch" onkeyup="searchCountryName()"></td>
          </tr>
        </tbody>
      </table>


      <table id="country_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="country_body">
        @foreach ($objCountryList as $index=>$CountryList)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_CTRYID_REF[]"  id="ctryidref_{{ $CountryList->CTRYID }}" class="cls_ctryidref" value="{{ $CountryList->CTRYID }}" ></td>
          <td class="ROW2" style="width: 39%">{{ $CountryList->CTRYCODE }}
          <input type="hidden" id="txtctryidref_{{ $CountryList->CTRYID }}" data-desc="{{ $CountryList->CTRYCODE }} - {{ $CountryList->NAME }}" value="{{ $CountryList-> CTRYID }}"/>
          </td>
          <td  class="ROW3" style="width: 39%">{{ $CountryList->NAME }}</td>
        </tr>        
        @endforeach
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="stidref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='stidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>State</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="state_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="state_codesearch" onkeyup="searchStateCode()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="state_namesearch" onkeyup="searchStateName()" /></td>
        </tr>
        </tbody>
      </table>

      <table id="state_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="state_body">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="cityidref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='cityidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>City</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="city_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="city_codesearch" onkeyup="searchCityCode()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="city_namesearch" onkeyup="searchCityName()" /></td>
        </tr>
        </tbody>
      </table>

      <table id="city_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="city_body">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="distidref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='distidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>District</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="dist_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th class="ROW2" style="width: 40%">Code</th>
            <th  class="ROW3"style="width: 40%">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="dist_codesearch" onkeyup="searchDistCode()" /></td>
          <td class="ROW3"  style="width: 40%"><input type="text" autocomplete="off"  class="form-control" id="dist_namesearch" onkeyup="searchDistName()" /></td>
        </tr>
        </tbody>
      </table>

      <table id="dist_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="dist_body">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>





<!-- Alert -->
@endsection
<!-- btnSave -->

@push('bottom-scripts')
<script>

/* GL popup function */
$("#GLID_REF_POPUP").on("click",function(event){ 
  $("#glrefpopup").show();
});

$("#GLID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#glrefpopup").show();
  }
});

$("#glrefpopup_close").on("click",function(event){ 
  $("#glrefpopup").hide();
});

$('.clsglref').click(function(){
    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texdesc =   $("#txt"+id+"").data("desc")

    $("#GLID_REF_POPUP").val(texdesc);
    $("#GLID_REF").val(txtval);
    $("#GLID_REF_POPUP").blur(); 
    $("#REG_ADD1").focus(); 
    $("#glrefpopup").hide();

    $("#gl_codesearch").val(''); 
    $("#gl_namesearch").val(''); 
    searchGLCode();
    searchGLName()
    $(this).prop("checked",false);
    event.preventDefault();

});

function searchGLCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("gl_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("gl_tab2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}

function searchGLName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("gl_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("gl_tab2");
      tr = table.getElementsByTagName("tr");
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[2];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }       
  }
}

// Country popup function

$("#CTRYID_REF_POPUP").on("click",function(event){ 
  $("#ctryidref_popup").show();
});

$("#CTRYID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#ctryidref_popup").show();
  }
});

$("#ctryidref_close").on("click",function(event){ 
  $("#ctryidref_popup").hide();
});

$('.cls_ctryidref').click(function(){
  var id = $(this).attr('id');
  var txtval =    $("#txt"+id+"").val();
  var texdesc =   $("#txt"+id+"").data("desc")

  $("#CTRYID_REF_POPUP").val(texdesc);
  $("#CTRYID_REF").val(txtval);

  getCountryWiseState(txtval);
  
  $("#CTRYID_REF_POPUP").blur(); 
  $("#STID_REF_POPUP").focus(); 
  
  $("#ctryidref_popup").hide();
  
  $(this).prop("checked",false);
  event.preventDefault();
});

function searchCountryCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("country_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("country_tab2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}

function searchCountryName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("country_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("country_tab2");
      tr = table.getElementsByTagName("tr");
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[2];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }       
  }
}

function getCountryWiseState(CTRYID_REF){
    $("#state_body").html('loading...');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[34,"getCountryWiseState"])}}',
        type:'POST',
        data:{CTRYID_REF:CTRYID_REF},
        success:function(data) {
          
          $("#STID_REF_POPUP").val('');
          $("#STID_REF").val('');
			    $("#CITYID_REF_POPUP").val('');
          $("#CITYID_REF").val('');
          $("#city_body").html(''); 
          $("#DISTID_REF_POPUP").val('');
          $("#DISTID_REF").val('');
          $("#dist_body").html(''); 
          
          $("#state_body").html(data);
          bindStateEvents(); 

        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#state_body").html('');
          
        },
    });	
  }

// State popup function

$("#STID_REF_POPUP").on("click",function(event){ 
  $("#stidref_popup").show();
});

$("#STID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#stidref_popup").show();
  }
});

$("#stidref_close").on("click",function(event){ 
  $("#stidref_popup").hide();
});

function bindStateEvents(){
  $('.cls_stidref').click(function(){

    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texdesc =   $("#txt"+id+"").data("desc")

    $("#STID_REF_POPUP").val(texdesc);
    $("#STID_REF").val(txtval);
	
	var CTRYID_REF	=	$("#CTRYID_REF").val();
	
	getStateWiseCity(CTRYID_REF,txtval);
	
	$("#STID_REF_POPUP").blur(); 
	$("#CITYID_REF_POPUP").focus(); 
	
    $("#stidref_popup").hide();
    //searchStateCode();
    $(this).prop("checked",false);
    event.preventDefault();
  });
}

function searchStateCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("state_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("state_tab2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}

function searchStateName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("state_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("state_tab2");
      tr = table.getElementsByTagName("tr");
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[2];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }       
  }
}

function getStateWiseCity(CTRYID_REF,STID_REF){
    $("#city_body").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[34,"getStateWiseCity"])}}',
        type:'POST',
        data:{CTRYID_REF:CTRYID_REF,STID_REF:STID_REF},
        success:function(data) {
          
            $("#CITYID_REF_POPUP").val('');
            $("#CITYID_REF").val('');

            $("#DISTID_REF_POPUP").val('');
            $("#DISTID_REF").val('');
            $("#dist_body").html('');
            
            $("#city_body").html(data);
            bindCityEvents(); 
			
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#city_body").html('');
          
        },
    });	
  }

// Citiy popup function

$("#CITYID_REF_POPUP").on("click",function(event){ 
  $("#cityidref_popup").show();
});

$("#CITYID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#cityidref_popup").show();
  }
});

$("#cityidref_close").on("click",function(event){ 
  $("#cityidref_popup").hide();
});

function bindCityEvents(){
	$('.cls_cityidref').click(function(){

		var id = $(this).attr('id');
		var txtval =    $("#txt"+id+"").val();
		var texdesc =   $("#txt"+id+"").data("desc")

		$("#CITYID_REF_POPUP").val(texdesc);
    $("#CITYID_REF").val(txtval);
    
    var CTRYID_REF	=	$("#CTRYID_REF").val();
    var STID_REF	=	$("#STID_REF").val();

	  getCityWiseDist(CTRYID_REF,STID_REF,txtval);
	
    $("#CITYID_REF_POPUP").blur(); 
	  $("#DISTID_REF_POPUP").focus(); 

		$("#cityidref_popup").hide();
		$(this).prop("checked",false);
		//searchCityCode();
		event.preventDefault();
	});
}


function searchCityCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("city_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("city_tab2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}

function searchCityName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("city_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("city_tab2");
      tr = table.getElementsByTagName("tr");
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[2];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }       
  }
}

function getCityWiseDist(CTRYID_REF,STID_REF,CITYID_REF){
    $("#dist_body").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[34,"getCityWiseDist"])}}',
        type:'POST',
        data:{CTRYID_REF:CTRYID_REF,STID_REF:STID_REF,CITYID_REF:CITYID_REF},
        success:function(data) {
          
            $("#DISTID_REF_POPUP").val('');
            $("#DISTID_REF").val('');            
            $("#dist_body").html(data);
            bindDistEvents(); 
			
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#dist_body").html('');
          
        },
    });	
  }


// Dist popup function

$("#DISTID_REF_POPUP").on("click",function(event){ 
  $("#distidref_popup").show();
});

$("#DISTID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#distidref_popup").show();
  }
});

$("#distidref_close").on("click",function(event){ 
  $("#distidref_popup").hide();
});

function bindDistEvents(){
	$('.cls_distidref').click(function(){

		var id = $(this).attr('id');
		var txtval =    $("#txt"+id+"").val();
		var texdesc =   $("#txt"+id+"").data("desc")

		$("#DISTID_REF_POPUP").val(texdesc);
		$("#DISTID_REF").val(txtval);

		$("#distidref_popup").hide();
		
		$(this).prop("checked",false);
		event.preventDefault();
	});
}


function searchDistCode() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("dist_codesearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("dist_tab2");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}

function searchDistName() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("dist_namesearch");
      filter = input.value.toUpperCase();
      table = document.getElementById("dist_tab2");
      tr = table.getElementsByTagName("tr");
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[2];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }       
  }
}

  "use strict";
	var w3 = {};
  w3.getElements = function (id) {
    if (typeof id == "object") {
      return [id];
    } else {
      return document.querySelectorAll(id);
    }
  };
	w3.sortHTML = function(id, sel, sortvalue) {
    var a, b, i, ii, y, bytt, v1, v2, cc, j;
    a = w3.getElements(id);
    for (i = 0; i < a.length; i++) {
      for (j = 0; j < 2; j++) {
        cc = 0;
        y = 1;
        while (y == 1) {
          y = 0;
          b = a[i].querySelectorAll(sel);
          for (ii = 0; ii < (b.length - 1); ii++) {
            bytt = 0;
            if (sortvalue) {
              v1 = b[ii].querySelector(sortvalue).innerText;
              v2 = b[ii + 1].querySelector(sortvalue).innerText;
            } else {
              v1 = b[ii].innerText;
              v2 = b[ii + 1].innerText;
            }
            v1 = v1.toLowerCase();
            v2 = v2.toLowerCase();
            if ((j == 0 && (v1 > v2)) || (j == 1 && (v1 < v2))) {
              bytt = 1;
              break;
            }
          }
          if (bytt == 1) {
            b[ii].parentNode.insertBefore(b[ii + 1], b[ii]);
            y = 1;
            cc++;
          }
        }
        if (cc > 0) {break;}
      }
    }
  };

  
  let gl_tab1 = "#gl_tab1";
  let gl_tab2 = "#gl_tab2";
  let gl_headers = document.querySelectorAll(gl_tab1 + " th");

  gl_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(gl_tab2, ".clsglref", "td:nth-child(" + (i + 1) + ")");
    });
  });

  let country_tab1 = "#country_tab1";
  let country_tab2 = "#country_tab2";
  let country_headers = document.querySelectorAll(country_tab1 + " th");

  country_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(country_tab2, ".cls_ctryidref", "td:nth-child(" + (i + 1) + ")");
    });
  });

  
  let state_tab1 = "#state_tab1";
  let state_tab2 = "#state_tab2";
  let state_headers = document.querySelectorAll(state_tab1 + " th");

  state_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(state_tab2, ".cls_stidref", "td:nth-child(" + (i + 1) + ")");
    });
  });

  let city_tab1 = "#city_tab1";
  let city_tab2 = "#city_tab2";
  let city_headers = document.querySelectorAll(city_tab1 + " th");

  city_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(city_tab2, ".cls_cityidref", "td:nth-child(" + (i + 1) + ")");
    });
  });

  let dist_tab1 = "#dist_tab1";
  let dist_tab2 = "#dist_tab2";
  let dist_headers = document.querySelectorAll(dist_tab1 + " th");

  dist_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(dist_tab2, ".cls_distidref", "td:nth-child(" + (i + 1) + ")");
    });
  });




  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[34,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formResponseMst = $( "#frm_mst_transporter" );
     formResponseMst.validate();

    $("#TRANSPORTER_CODE").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_TRANSPORTER_CODE").hide();
      validateSingleElemnet("TRANSPORTER_CODE");
         
    });

    $( "#TRANSPORTER_CODE" ).rules( "add", {
        required: true,
        nowhitespace: true,
        //StringNumberRegex: true, //from custom.js
        messages: {
            required: "Required field.",
        }
    });

    $("#TRANSPORTER_NAME").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_TRANSPORTER_NAME").hide();
        validateSingleElemnet("TRANSPORTER_NAME");
    });
    $("#TRANSPORTER_NAME").keydown(function(){
        $("#ERROR_TRANSPORTER_NAME").hide();
        validateSingleElemnet("TRANSPORTER_NAME");
    });

    $( "#TRANSPORTER_NAME" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#GLID_REF").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_GLID_REF").hide();
        validateSingleElemnet("GLID_REF");
    });

    $( "#GLID_REF" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#STID_REF").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_STID_REF").hide();
        validateSingleElemnet("STID_REF");
    });

    $( "#STID_REF" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#CTRYID_REF").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_CTRYID_REF").hide();
        validateSingleElemnet("CTRYID_REF");
    });

    $( "#CTRYID_REF" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#EMAILID").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_EMAILID").hide();
        validateSingleElemnet("EMAILID");
    });

    $( "#EMAILID" ).rules( "add", {
        EmailValidate: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#CP_EMAILID").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_EMAILID").hide();
        validateSingleElemnet("EMAILID");
    });

    $("#CP_EMAILID" ).rules( "add", {
        EmailValidate: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });
    

    $("#GSTIN_NO").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_GSTIN_NO").hide();
        validateSingleElemnet("GSTIN_NO");
    });

    $( "#GSTIN_NO" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });


    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_transporter" ).validate();
         if(validator.element( "#"+element_id+"" )){
            //check duplicate code
          if(element_id=="TRANSPORTER_CODE" || element_id=="transporter_code" ) {
            checkDuplicateCode();
          }

         }
    }

    // //check duplicate country code
    function checkDuplicateCode(){
        
        //validate and save data
        var getDataForm = $("#frm_mst_transporter");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[34,"codeduplicate"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_TRANSPORTER_CODE',data.msg);
                    $("#TRANSPORTER_CODE").focus();
                }                                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
    }

    //validate
    $( "#btnSave" ).click(function() {

          $("#OkBtn1").hide(); 

          if(formResponseMst.valid()){
            var TRANSPORTER_CODE          =   $.trim($("#TRANSPORTER_CODE").val());
            if(TRANSPORTER_CODE ===""){
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").show();     
              $("#AlertMessage").text('Please enter Transporter Code.');
              $("#alert").modal('show');
              $("#OkBtn").focus();
              return false;
            }

            //set function nane of yes and no btn 
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
            $("#YesBtn").focus();
            highlighFocusBtn('activeYes');
       
        }
    });//btnSave

  
    
    $("#YesBtn").click(function(){

        $("#alert").modal('hide');
        var customFnName = $("#YesBtn").data("funcname");
            window[customFnName]();

    }); //yes button


   window.fnSaveData = function (){

        //validate and save data
        event.preventDefault();
        $("#OkBtn1").hide();

        var getDataForm = $("#frm_mst_transporter");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[34,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.TRANSPORTER_CODE){
                       // showError('ERROR_TRANSPORTER_CODE',data.errors.TRANSPORTER_CODE);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").hide();
                        $("#OkBtn").show();
                        $("#AlertMessage").text("Transporter Code is "+data.errors.TRANSPORTER_CODE);
                        $("#alert").modal('show');
                        $("#OkBtn").focus();
                    }
                    if(data.errors.TRANSPORTER_NAME){
                        //showError('ERROR_TRANSPORTER_NAME',data.errors.TRANSPORTER_NAME);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").hide();
                        $("#OkBtn").show();
                        $("#AlertMessage").text("Transporter Name is "+data.errors.TRANSPORTER_NAME);
                        $("#alert").modal('show');
                        $("#OkBtn").focus();
                    }
                   if(data.exist=='duplicate') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();
                      $("#OkBtn1").hide();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

                   }
                   if(data.save=='invalid') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();
                      $("#OkBtn1").hide();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

                   }
                }
                if(data.success) {                   
                    console.log("succes MSG="+data.msg);
                    
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();

                    $("#AlertMessage").text(data.msg);

                    $(".text-danger").hide();
                    $("#frm_mst_transporter").trigger("reset");

                    $("#alert").modal('show');
                    $("#OkBtn1").focus();

                  //  window.location.href='{{ route("master",[34,"index"])}}';
                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
      
   } // fnSaveData



    
    $("#NoBtn").click(function(){
    
      $("#alert").modal('hide');
      var custFnName = $("#NoBtn").data("funcname");
          window[custFnName]();

    }); //no button
   
    
    $("#OkBtn").click(function(){

        $("#alert").modal('hide');

        $("#YesBtn").show();  //reset
        $("#NoBtn").show();   //reset
        $("#OkBtn").hide();
        $("#OkBtn1").hide();

        $(".text-danger").hide();
        $("#TRANSPORTER_CODE").focus();
        
    }); ///ok button

    
    
    $("#btnUndo").click(function(){

        $("#AlertMessage").text("Do you want to erase entered information in this record?");
        $("#alert").modal('show');

        $("#YesBtn").data("funcname","fnUndoYes");
        $("#YesBtn").show();

        $("#NoBtn").data("funcname","fnUndoNo");
        $("#NoBtn").show();
        
        $("#OkBtn").hide();
        $("#NoBtn").focus();
        highlighFocusBtn('activeNo');
        
    }); ////Undo button

    $("#OkBtn1").click(function(){

        $("#alert").modal('hide');
        $("#YesBtn").show();  //reset
        $("#NoBtn").show();   //reset
        $("#OkBtn").hide();
        $("#OkBtn1").hide();
        $(".text-danger").hide();
        window.location.href = "{{route('master',[34,'index'])}}";

      }); 


    
    $("#OkBtn").click(function(){
      $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('master',[34,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#TRANSPORTER_CODE").focus();
   }//fnUndoNo


    function showError(pId,pVal){

      $("#"+pId+"").text(pVal);
      $("#"+pId+"").show();

    }//showError

    function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }

  $(function() { 
    
    $("#TRANSPORTER_CODE").focus(); 
  
  });

  window.onload = function(){
      var strdd = <?php echo json_encode($objDD); ?>;
      if($.trim(strdd)==""){     
        $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").show();
          $("#AlertMessage").text('Please contact to administrator for creating document numbering.');
          $("#alert").modal('show');
          $("#OkBtn").focus();
          highlighFocusBtn('activeOk');
      } 
    };
    

</script>

@endpush