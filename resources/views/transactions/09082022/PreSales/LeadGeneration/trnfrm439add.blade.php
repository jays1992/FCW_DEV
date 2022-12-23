@extends('layouts.app')
@section('content')

  <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Lead Generation</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                  <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                  <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                  <button class="btn topnavbt" id="btnSave" ><i class="fa fa-floppy-o"></i> Save</button>
                  <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                  <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                  <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                  <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                  <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-thumbs-o-up"></i> Approved</button>
                  <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                  <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div>
        </div>
   
<div class="container-fluid purchase-order-view filter">     
    <form id="frm_mst_add" method="POST"> 
    @CSRF
    <div class="inner-form">
    <div class="row">
			<div class="col-lg-2 pl"><p>Lead No*</p></div>
			<div class="col-lg-2 pl">
        @if(isset($objDD->SYSTEM_GRSR) && $objDD->SYSTEM_GRSR == "1")
        <input type="text" name="LEAD_NO" id="LEAD_NO" value="{{ $objDOCNO }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
      @elseif(isset($objDD->MANUAL_SR) && $objDD->MANUAL_SR == "1")
        <input type="text" name="LEAD_NO" id="LEAD_NO" value="{{ old('LEAD_NO') }}" class="form-control mandatory" maxlength="{{isset($objDD->MANUAL_MAXLENGTH)?$objDD->MANUAL_MAXLENGTH:''}}" autocomplete="off" style="text-transform:uppercase"  >
      @else
        <input type="text" name="LEAD_NO" id="LEAD_NO" value="{{ old('BGCODE') }}"  class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
        @endif
      </div>
      <div class="col-lg-2 pl"><p>Lead Date*</p></div>
        <div class="col-lg-2 pl">
        <input type="date" name="LEAD_DT" id="LEAD_DT" onchange="checkPeriodClosing('{{$FormId}}',this.value,1)" value="{{ old('LEAD_DT') }}" class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
      </div> 
        
        <div class="col-lg-1 pl"><p>Customer</p></div>
        <div class="col-lg-1 pl">
          <input type="radio" name="CUSTOMER" id="CUSTOMER" value="Customer" onclick="getCustomer(this.value)" checked>
        </div>

        <div class="col-lg-1 pl"><p>Prospect</p></div>
        <div class="col-lg-1 pl">
          <input type="radio" name="CUSTOMER" id="PROSPECT" value="Prospect" onclick="getCustomer(this.value)">
        </div>

		</div>

    <div class="row">
      <div class="col-lg-2 pl"><p id="CUSTOMER_TITLE">Customer</p></div>
        <div class="col-lg-2 pl">
          <input type="hidden" name="CUSTOMER_TYPE" id="CUSTOMER_TYPE" value="Customer" class="form-control" autocomplete="off" />
          <input type="text"  id="CUSTOMERPROSPECT_NAME" onclick="getCustProspect()" class="form-control mandatory"  autocomplete="off" readonly/>
          <input type="hidden" name="CUSTOMER_PROSPECT" id="CUSTOMER_PROSPECT" class="form-control" autocomplete="off" />
        </div>

        <div class="col-lg-2 pl"><p>Dealer</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="DEALER" id="DEALER" onclick="getData('{{route('transaction',[$FormId,'getDealerCode'])}}','Dealer Details')" class="form-control mandatory"  autocomplete="off" readonly/>
          <input type="hidden" name="DEALERIDREF" id="DEALERID_REF" class="form-control" autocomplete="off" />
        </div>

        <div class="col-lg-2 pl"><p>Convert Status</p></div>
          <div class="col-lg-2 pl">
        <select name="CONVERTSTATUS" id="CONVERTSTATUS" class="form-control mandatory">
          <option value="">Select</option>
          <option value="Prospecting">Prospecting</option>
          <option value="Qualifying Leads">Qualifying Leads</option>
          <option value="Opportunity">Opportunity</option>  
          </select>  
        </div>
      </div>

      <div class="row">
        <div class="col-lg-2 pl"><p>Opportunity Type</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="OPPRTYPE" id="OPPRTYPE" class="form-control mandatory"  autocomplete="off" readonly/>
          <input type="hidden" name="OPPRTYPEID_REF" id="OPPRTYPEID_REF" class="form-control" autocomplete="off" />
        </div>
      
        <div class="col-lg-2 pl"><p>Opportunity Stage</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="OPPRSTAGE" id="OPPRSTAGE" class="form-control mandatory"  autocomplete="off" readonly/>
          <input type="hidden" name="OPPRSTAGEID_REF" id="OPPRSTAGEID_REF" class="form-control" autocomplete="off" />
        </div>

        <div class="col-lg-2 pl"><p>Opportuntity Stage Completed (%)</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="OPPRSTAGECOMP" id="OPPRSTAGECOMP" class="form-control mandatory"  autocomplete="off" readonly/>
        </div>
      </div>
      
      <div class="row">
        <div class="col-lg-2 pl"><p>Expected date</p></div>
        <div class="col-lg-2 pl">
        <input type="date" name="EXPECTED_DT" id="EXPECTED_DT" value="{{ old('EXPECTED_DT') }}" class="form-control mandatory" autocomplete="off" readonly >
      </div>
    
      <div class="col-lg-2 pl"><p>Opportunity Date</p></div>
        <div class="col-lg-2 pl">
        <input type="date" name="OPPORTUNITY_DT" id="OPPORTUNITY_DT" value="{{ old('OPPORTUNITY_DT') }}" class="form-control mandatory" autocomplete="off" readonly>
      </div>
    
        <div class="col-lg-2 pl"><p>Company Name*</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="COMPANY_NAME" id="COMPANY_NAME" value="{{ old('COMPANY_NAME') }}" class="form-control mandatory" autocomplete="off">                            
        </div>
      </div>
      
      <div class="row">
      <div class="col-lg-2 pl"><p>First Name*</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="FNAME" id="FNAME" value="{{ old('FNAME') }}" class="form-control mandatory" autocomplete="off">                            
      </div>
    
      <div class="col-lg-2 pl"><p>Last Name</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="LNAME" id="LNAME" value="{{ old('LNAME') }}" class="form-control mandatory" autocomplete="off">                            
      </div>

      <div class="col-lg-2 pl"><p>Address*</p></div>
      <div class="col-lg-2 pl">
        <textarea name="ADDRESS" id="ADDRESS" style="width: 192px;" class="form-control mandatory"></textarea>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-2 pl"><p>Country*</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="COUNTRY" id="COUNTRY" onclick="getData('{{route('transaction',[$FormId,'getCountryCode'])}}','Country Details')" class="form-control mandatory"  autocomplete="off" readonly/>
        <input type="hidden" name="COUNTRYID_REF" id="COUNTRYID_REF" class="form-control" autocomplete="off" />
      </div>
    
      <div class="col-lg-2 pl"><p>State*</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="STATE" id="STATE" class="form-control mandatory"  autocomplete="off" readonly/>
        <input type="hidden" name="STATEID_REF" id="STATEID_REF" class="form-control" autocomplete="off" />
      </div>
  
      <div class="col-lg-2 pl"><p>City*</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="CITYID_REF_POPUP" id="CITYID_REF_POPUP" class="form-control mandatory" readonly tabindex="1" />
        <input type="hidden" name="CITYID_REF" id="CITYID_REF" />
      </div>
    </div>

    <div class="row">
      <div class="col-lg-2 pl"><p>Pin-Code*</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="PINCODE" id="PINCODE" value="{{ old('PINCODE') }}" onkeypress="return onlyNumberKey(event)" maxlength="6" class="form-control mandatory" autocomplete="off">                             
      </div>
    
      <div class="col-lg-2 pl"><p>Lead Owner*</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="LOWNER" id="LOWNER" onclick="getData('{{route('transaction',[$FormId,'getLeadOwnerCode'])}}','Lead Owner Details')" class="form-control mandatory"  autocomplete="off" readonly/>
        <input type="hidden" name="LOWNERID_REF" id="LOWNERID_REF" value="@if(Session::get('branch_name')) {{Session::get('branch_name')}} @endif" class="form-control" autocomplete="off" />
      </div>

      <div class="col-lg-2 pl"><p>Industry Type*</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="INTYPE" id="INTYPE" onclick="getData('{{route('transaction',[$FormId,'getIndustryTypeCode'])}}','Industry Type Details')" class="form-control mandatory"  autocomplete="off" readonly/>
        <input type="hidden" name="INTYPEID_REF" id="INTYPEID_REF" class="form-control" autocomplete="off" />
      </div>
    </div>

    <div class="row">
      <div class="col-lg-2 pl"><p>Designation*</p></div>
      <div class="col-lg-2 pl">
        <select name="DESIGNID_REF" id="DESIGNID_REF" class="form-control mandatory">
          <option value="">Select</option>
          @foreach ($design as $val)
          <option value="{{$val->DESGID}}">{{$val->DESGCODE}} - {{$val->DESCRIPTIONS}}</option>  
          @endforeach
          </select>                            
      </div>
    
      <div class="col-lg-2 pl"><p>Contact Person</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="CONTACT_PERSON" id="CONTACT_PERSON" value="{{ old('CONTACT_PERSON') }}" class="form-control mandatory" autocomplete="off">                            
      </div>
    
      <div class="col-lg-2 pl"><p>Remarks</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="LEAD_DETAILS" id="LEAD_DETAILS" value="{{ old('LEAD_DETAILS') }}" class="form-control mandatory" autocomplete="off">                            
      </div>
    </div>
    
    <div class="row">
      <div class="col-lg-2 pl"><p>Website</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="WEBSITENAME" id="WEBSITENAME" value="{{ old('WEBSITENAME') }}" class="form-control">                            
      </div>
   
      <div class="col-lg-2 pl"><p>Landline Number</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="LANDNUMBER" id="LANDNUMBER" value="{{ old('LANDNUMBER') }}" onkeypress="return onlyNumberKey(event)" class="form-control mandatory" autocomplete="off">                            
      </div>
      
      <div class="col-lg-2 pl"><p>Mobile Number*</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="MOBILENUMBER" id="MOBILENUMBER" value="{{ old('MOBILENUMBER') }}" onkeypress="return onlyNumberKey(event)" maxlength="12" class="form-control mandatory" autocomplete="off">                           
      </div>
    </div>
    
    <div class="row">
      <div class="col-lg-2 pl"><p>E-Mail*</p></div>
      <div class="col-lg-2 pl">
        <input type="email" name="EMAIL" id="EMAIL" value="{{ old('EMAIL') }}" class="form-control mandatory" autocomplete="off">                            
      </div>
    
      <div class="col-lg-2 pl"><p>Lead Source*</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="LSOURCE" id="LSOURCE" onclick="getData('{{route('transaction',[$FormId,'getLeadSourceCode'])}}','Lead Source Details')" class="form-control mandatory"  autocomplete="off" readonly/>
        <input type="hidden" name="LSOURCEID_REF" id="LSOURCEID_REF" class="form-control" autocomplete="off" />
      </div>
    
      <div class="col-lg-2 pl"><p>Lead Stage*</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="LSTATUS" id="LSTATUS" onclick="getData('{{route('transaction',[$FormId,'getLeadStatusCode'])}}','Lead Status Details')" class="form-control mandatory"  autocomplete="off" readonly/>
        <input type="hidden" name="LSTATUSID_REF" id="LSTATUSID_REF" class="form-control" autocomplete="off" />
      </div>
    </div>
   
    <div class="row">
      <div class="col-lg-2 pl"><p>Transfer Leads*</p></div>
      <div class="col-lg-2 pl">
        <input type="text" name="ASSIGTO" id="ASSIGTO" onclick="getData('{{route('transaction',[$FormId,'getAssignedToHrd'])}}','Assigned To Details')" class="form-control mandatory"  autocomplete="off" readonly/>
        <input type="hidden" name="ASSIGTOID_REF" id="ASSIGTOID_REF" class="form-control" autocomplete="off" />
      </div>
    
      <div class="col-lg-2 pl"><p>Lead Closure</p></div>
      <div class="col-lg-2 pl">
        <select name="LCLOSUR" id="LCLOSUR" class="form-control">
          <option value="">Select</option>
          <option value="1">Yes</option>
          <option value="0">No</option>   
          </select>
      </div>
    
      <div class="col-lg-2 pl"><p>Remarks</p></div>
      <div class="col-lg-2 pl">
        <textarea name="REMARKS" id="REMARKS" style="width: 192px;" class="form-control"></textarea>
      </div>
    </div>

    
  </div>
</form>
</div>
@endsection
@section('alert')
<!-- Alert -->
<div id="alert" class="modal"  role="dialog"  data-backdrop="static">
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
            <button onclick="setfocus();"  class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">
            <div id="alert-active" class="activeOk"></div>OK</button>
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;">
              <div id="alert-active" class="activeOk1"></div>OK</button>
              <input type="hidden" id="focusid" >
            
        </div><!--btdiv-->
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<div id="modalpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='modalclosePopup' >&times;</button>
      </div>

      <div class="modal-body">

        <div class="tablename"><p id='tital_Name'></p></div>
        <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
          <table id="MachTable" class="display nowrap table  table-striped table-bordered">
            <thead>
              <tr>
                <th class="ROW1">Select</th> 
                <th class="ROW2">Code</th>
                <th class="ROW3">Description</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="ROW1"><span class="check_th">&#10004;</span></td>
                <td class="ROW2"><input type="text" autocomplete="off"  class="form-control" id="codesearch"  onkeyup='colSearch("tabletab2","codesearch",1)' /></td>
                <td class="ROW3"><input type="text" autocomplete="off"  class="form-control" id="namesearch"  onkeyup='colSearch("tabletab2","namesearch",2)' /></td>
              </tr>
            </tbody>
          </table>

          <table id="tabletab2" class="display nowrap table  table-striped table-bordered" >
            <thead id="thead2"></thead>
            <tbody id="getData_tbody"></tbody>
          </table>

        </div>

        <div class="cl"></div>

      </div>
    </div>
  </div>
</div>

<div id="stateidref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='stateidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>State Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="state_tab1" class="display nowrap table  table-striped table-bordered">
        <thead>
          <tr>
            <th class="ROW1">Select</th> 
            <th class="ROW2">Code</th>
            <th  class="ROW3">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1"><span class="check_th">&#10004;</span></td>
          <td  class="ROW2"><input type="text" class="form-control" autocomplete="off" id="statecodesearch"  onkeyup='colSearch("state_tab2","statecodesearch",1)'></td>
          <td  class="ROW3"><input type="text" class="form-control" autocomplete="off"  id="statenamesearch"  onkeyup='colSearch("state_tab2","statenamesearch",2)'></td>
        </tr>
        </tbody>
      </table>

      <table id="state_tab2" class="display nowrap table  table-striped table-bordered">
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
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='cityidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>City Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="city_tab1" class="display nowrap table  table-striped table-bordered">
        <thead>
          <tr>
            <th class="ROW1">Select</th> 
            <th class="ROW2">Code</th>
            <th  class="ROW3">Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1"><span class="check_th">&#10004;</span></td>
          <td  class="ROW2"><input type="text" class="form-control" autocomplete="off" id="citycodesearch"  onkeyup='colSearch("city_tab2","citycodesearch",1)'></td>
          <td  class="ROW3"><input type="text" class="form-control" autocomplete="off"  id="citynamesearch"  onkeyup='colSearch("city_tab2","citynamesearch",2)'></td>
        </tr>
        </tbody>
      </table>

      <table id="city_tab2" class="display nowrap table  table-striped table-bordered">
        <tbody id="city_body">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>


@endsection
@push('bottom-css')
@endpush
@push('bottom-scripts')
<script>

/*************************************   All Popup  ************************** */
function getCustomer(value){
  $("#CUSTOMER_TITLE").html(value);
  $("#CUSTOMER_TYPE").val(value);
  $("#CUSTOMERPROSPECT_NAME").val('');
  $("#CUSTOMER_PROSPECT").val('');
}

function getCustProspect(){

  var type  = $("input[name='CUSTOMER']:checked").val();
  var msg   = type;

  $('#getData_tbody').html('Loading...'); 
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("transaction",[$FormId,"getCustomerCode"])}}',
    type:'POST',
    data:{type:type},
    success:function(data) {
    $('#getData_tbody').html(data);
    bindCustPostEvents(type);
    },
    error:function(data){
      console.log("Error: Something went wrong.");
      $('#getData_tbody').html('');
    },
  });

  $("#tital_Name").text(msg);
  $("#modalpopup").show();
}


      function getData(path,msg){

      $('#getData_tbody').html('Loading...'); 

      $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      $.ajax({
          url:path,
          type:'POST',
          success:function(data) {
          $('#getData_tbody').html(data);
          bindOppTypeEvents()
          bindOppStageEvents()
          bindCountryEvents()
          bindLeadOwnerEvents()
          bindIndustryTypeEvents()
          bindLeadSourceEvents()
          bindLeadStatusEvents()
          bindAssignedToEvents()
          bindDealerEvents()
          },
          error:function(data){
            console.log("Error: Something went wrong.");
            $('#getData_tbody').html('');
          },
        });

          $("#tital_Name").text(msg);
          $("#modalpopup").show();
          event.preventDefault();
      }

      $("#modalclosePopup").on("click",function(event){ 
        $("#modalpopup").hide();
        event.preventDefault();
      });


/*************************************   All Popup bind  Start ************************** */
      function bindCustPostEvents(type){
        $('.cls'+type).click(function(){
          if($(this).is(':checked') == true){
          var id = $(this).attr('id');
          var txtval =    $("#txt"+id+"").val();
          var texdesc =   $("#txt"+id+"").data("desc");
          $("#CUSTOMERPROSPECT_NAME").val(texdesc);
          $("#CUSTOMER_PROSPECT").val(txtval);
          $("#modalpopup").hide();
          }
        });
      }


      function bindOppTypeEvents(){
        $('.clsopptype').click(function(){
        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc");
        $("#OPPRTYPE").val(texdesc);
        $("#OPPRTYPEID_REF").val(txtval);
        $("#modalpopup").hide();
        });
      }

      function bindOppStageEvents(){
        $('.clsoppstage').click(function(){
        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc");
        var texccpert =   $("#txt"+id+"").data("ccpert");
        $("#OPPRSTAGE").val(texdesc);
        $("#OPPRSTAGEID_REF").val(txtval);
        $("#OPPRSTAGECOMP").val(texccpert);
        $("#modalpopup").hide();
        });
      }

      function bindCountryEvents(){
        $('.clscontry').click(function(){
        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc");
        $("#COUNTRY").val(texdesc);
        $("#COUNTRYID_REF").val(txtval);
        getCountryWiseState(txtval);
        $("#modalpopup").hide();
        });
      }

      function bindLeadOwnerEvents(){
        $('.clsemp').click(function(){
        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc");
        $("#LOWNER").val(texdesc);
        $("#LOWNERID_REF").val(txtval);
        $("#modalpopup").hide();
        });
      }

      function bindIndustryTypeEvents(){
        $('.clsindtype').click(function(){
        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc");
        $("#INTYPE").val(texdesc);
        $("#INTYPEID_REF").val(txtval);
        $("#modalpopup").hide();
        });
      }

      function bindLeadSourceEvents(){
        $('.clsldsce').click(function(){
        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc");
        $("#LSOURCE").val(texdesc);
        $("#LSOURCEID_REF").val(txtval);
        $("#modalpopup").hide();
        });
      }

      function bindDealerEvents(){
        $('.clsldlr').click(function(){
        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc");
        $("#DEALER").val(texdesc);
        $("#DEALERID_REF").val(txtval);
        $("#modalpopup").hide();
        });
      }      
      
      function bindLeadStatusEvents(){
        $('.clsldst').click(function(){
        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc");
        $("#LSTATUS").val(texdesc);
        $("#LSTATUSID_REF").val(txtval);
        $("#modalpopup").hide();
        });
      }

      function bindAssignedToEvents(){
        $('.clsassigntohrd').click(function(){
        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc");
        $("#ASSIGTO").val(texdesc);
        $("#ASSIGTOID_REF").val(txtval);
        $("#modalpopup").hide();
        });
      }

/************************************* All Popup bind End ************************** */
 
/*************************************   State Start  ************************** */

function getCountryWiseState(CTRYID_REF){
    $("#state_body").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("transaction",[$FormId,"getCountryWiseState"])}}',
        type:'POST',
        data:{CTRYID_REF:CTRYID_REF},
        success:function(data) {
          $("#STATE").val('');
          $("#STATEID_REF").val('');
          $("#CITYID_REF_POPUP").val('');
          $("#CITYID_REF").val('');
          $("#State_Name").val('');
          $("#STID_REF_POPUP").val('');
          $("#STID_REF").val('');
          $("#City_Name").val('');
          $("#city_body").html('');
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
$("#STATE").on("click",function(event){
  var COUNTRY    =   $.trim($("#COUNTRY").val());  
  if(COUNTRY ===""){
    alertMsg('COUNTRY','Please Select Country.');
  }else{
    $("#title_name").text('State Details'); 
    $("#stateidref_popup").show();
  }
});

$("#stateidref_close").on("click",function(event){ 
  $("#stateidref_popup").hide();
});

function bindStateEvents(){
  $('.cls_stidref').click(function(){
    var id          =   $(this).attr('id');
    var txtval      =   $("#txt"+id+"").val();
    var texdesc     =   $("#txt"+id+"").data("desc");
    var texdescname =   $("#txt"+id+"").data("descname");
    $("#STATE").val(texdesc);
    $("#STATEID_REF").val(txtval);
    var CTRYID_REF	=	$("#COUNTRYID_REF").val();
	  getStateWiseCity(CTRYID_REF,txtval);
	  $("#STATE").blur(); 
    $("#stateidref_popup").hide();
    event.preventDefault();
  });
}

/*************************************   State End  ************************** */

/*************************************   City Start  ************************** */
// Citiy popup function
function getStateWiseCity(CTRYID_REF,STID_REF){
    $("#city_body").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("transaction",[$FormId,"getStateWiseCity"])}}',
        type:'POST',
        data:{CTRYID_REF:CTRYID_REF,STID_REF:STID_REF},
        success:function(data) {

            $("#City_Name").val('');
            $("#CITYID_REF_POPUP").val('');
            $("#CITYID_REF").val('');

            $("#city_body").html(data);
            bindCityEvents(); 
			
        },
        error:function(data){
          console.log("Error: Something went wrong.");
          $("#city_body").html('');
          
        },
    });	
  }

$("#CITYID_REF_POPUP").on("click",function(event){
  var STATEID_REF    =   $.trim($("#STATEID_REF").val());  
  if(STATEID_REF ===""){
    alertMsg('STATE','Please Select State.');
  }else{
  $("#cityidref_popup").show();
  }
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
		var texdesc =   $("#txt"+id+"").data("desc");
    var texdescname =   $("#txt"+id+"").data("descname");

    $("#City_Name").val(texdescname);
		$("#CITYID_REF_POPUP").val(texdesc);
    $("#CITYID_REF").val(txtval);
    $("#CITYID_REF_POPUP").blur(); 
	  $("#DISTCODE").focus(); 
		$("#cityidref_popup").hide();
		event.preventDefault();
	});
}

/*************************************   City End  ************************** */

/*************************************   All Search Start  ************************** */

let input, filter, table, tr, td, i, txtValue;
function colSearch(ptable,ptxtbox,pcolindex) {
  //var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById(ptxtbox);
  filter = input.value.toUpperCase();
  table = document.getElementById(ptable);
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[pcolindex];
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
    
/************************************* All Search End  ************************** */


function setfocus(){
  var focusid=$("#focusid").val();
  $("#"+focusid).focus();
  $("#closePopup").click();
}
  
  function alertMsg(id,msg){
    $("#focusid").val(id);
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").hide();  
    $("#OkBtn").show();              
    $("#AlertMessage").text(msg);
    $("#alert").modal('show');
    $("#OkBtn").focus();
    return false;
  }
  
  function validateForm(actionType){
      $("#focusid").val('');

      
      var LEAD_NO        =   $.trim($("#LEAD_NO").val());
      var LEAD_DT        =   $.trim($("#LEAD_DT").val());
      var CUSTOMER       =   $.trim($("#CUSTOMER").val());
      var PROSPECT       =   $.trim($("#PROSPECT").val());
      var COMPANY_NAME   =   $.trim($("#COMPANY_NAME").val());
      var FNAME          =   $.trim($("#FNAME").val());
      var ADDRESS        =   $.trim($("#ADDRESS").val());
      var COUNTRYID_REF  =   $.trim($("#COUNTRYID_REF").val());
      var STATEID_REF    =   $.trim($("#STATEID_REF").val());
      var CITYID_REF     =   $.trim($("#CITYID_REF").val());
      var LOWNERID_REF   =   $.trim($("#LOWNERID_REF").val());
      var INTYPEID_REF   =   $.trim($("#INTYPEID_REF").val());
      var DESIGNID_REF   =   $.trim($("#DESIGNID_REF").val());
      var MOBILENUMBER   =   $.trim($("#MOBILENUMBER").val());
      var EMAIL          =   $.trim($("#EMAIL").val());
      var LSOURCEID_REF  =   $.trim($("#LSOURCEID_REF").val());
      var LSTATUSID_REF  =   $.trim($("#LSTATUSID_REF").val());
      var ASSIGTOID_REF  =   $.trim($("#ASSIGTOID_REF").val());
      var PINCODE        =   $.trim($("#PINCODE").val());

      $("#OkBtn1").hide();
      if(LEAD_NO ===""){
        alertMsg('LEAD_NO','Please enter Lead No.');
      }
      else if(LEAD_DT ===""){
        alertMsg('LEAD_DT','Please enter Date.');
      }

      else if(CUSTOMER ===""){
        alertMsg('CUSTOMER','Please enter Customer.');
      }

      else if(PROSPECT ===""){
        alertMsg('PROSPECT','Please enter Prospect.');
      }

      else if(COMPANY_NAME ===""){
        alertMsg('COMPANY_NAME','Please enter Company Name.');
      }
      else if(FNAME ===""){
        alertMsg('FNAME','Please enter First Name.');
      }

      else if(ADDRESS ===""){
        alertMsg('ADDRESS','Please enter Address.');
      }

      else if(COUNTRYID_REF ===""){
        alertMsg('COUNTRY','Please Select Country.');
      }

      else if(STATEID_REF ===""){
        alertMsg('STATE','Please Select State.');
      }

      else if(CITYID_REF ===""){
        alertMsg('CITYID_REF_POPUP','Please Select City.');
      }
     
      else if(PINCODE.length < 6 ){
        alertMsg('PINCODE','Please enter Correct Pin-Code.');
      }
      
      else if(LOWNERID_REF ==="") {
        alertMsg('LOWNER','Please Select Lead Owner.');
      }
      else if(INTYPEID_REF ==="") {
        alertMsg('INTYPE','Please Select Industry Type.');
      }
      else if(DESIGNID_REF ==="") {
        alertMsg('DESIGNID_REF','Please Select Designation.');
      }
      else if(MOBILENUMBER ==="") {
        alertMsg('MOBILENUMBER','Please enter Mobile Number.');
      }
      
      else if(EMAIL ===""){
        alertMsg('EMAIL','Please enter E-Mail.');
      } 
      
      else if(LSOURCEID_REF ==="") {
        alertMsg('LSOURCE','Please Select Lead Source.');
      }

      else if(LSTATUSID_REF ===""){
        alertMsg('LSTATUS','Please Select Lead Status.');
      }

      else if(ASSIGTOID_REF ===""){
        alertMsg('ASSIGTO','Please Select Assigned To.');
      }
      else if(checkPeriodClosing('{{$FormId}}',$("#LEAD_DT").val(),0) ==0){
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text(period_closing_msg);
        $("#alert").modal('show');
        $("#OkBtn1").focus();
      }


         else{
          $("#alert").modal('show');
          $("#AlertMessage").text('Do you want to save to record.');
          $("#YesBtn").data("funcname",actionType);  
          $("#YesBtn").focus();
          highlighFocusBtn('activeYes');
      }
  }

  
    $('#btnAdd').on('click', function() {
        var viewURL = '{{route("transaction",[$FormId,"add"])}}';
        window.location.href=viewURL;
    });
  
    $('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
      window.location.href=viewURL;
    });
  
      var formResponseMst = $( "#frm_mst_add" );
          formResponseMst.validate();
      function validateSingleElemnet(element_id){
        var validator =$("#frm_mst_add" ).validate();
           if(validator.element( "#"+element_id+"" )){
            if(element_id=="LEAD_NO" || element_id=="LEAD_NO" ) {
              checkDuplicateCode();
            }
           }
        }
  
      function checkDuplicateCode(){
          var getDataForm = $("#frm_mst_add");
          var formData = getDataForm.serialize();
          $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
          $.ajax({
              url:'{{route("transaction",[$FormId,"codeduplicate"])}}',
              type:'POST',
              data:formData,
              success:function(data) {
                if(data.exists) {
                  $(".text-danger").hide();
                  showError('ERROR_LEAD_NO',data.msg);
                  $("#LEAD_NO").focus();
                  }                                
              },
              error:function(data){
                console.log("Error: Something went wrong.");
              },
          });
      }
  
      $( "#btnSave" ).click(function() {
          if(formResponseMst.valid()){
            validateForm("fnSaveData");
          }
        });
      
      $("#YesBtn").click(function(){
          $("#alert").modal('hide');
          var customFnName = $("#YesBtn").data("funcname");
          window[customFnName]();
        });
  
     window.fnSaveData = function (){
          event.preventDefault();
          var getDataForm = $("#frm_mst_add");
          var formData = getDataForm.serialize();
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
          $.ajax({
              url:'{{route("transaction",[$FormId,"save"])}}',
              type:'POST',
              data:formData,
              success:function(data) {
                if(data.success) {                   
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").show();
                  $("#OkBtn").hide();
                  $("#AlertMessage").text(data.msg);
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                }
                else{
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn1").hide();
                  $("#OkBtn").show();
                  $("#AlertMessage").text(data.msg);
                  $("#alert").modal('show');
                  $("#OkBtn").focus();
                }
                  
              },
              error:function(data){
              console.log("Error: Something went wrong.");
              },
          });
        
     }

      $("#NoBtn").click(function(){
        $("#alert").modal('hide');
        var custFnName = $("#NoBtn").data("funcname");
          window[custFnName]();
        });
     
      
      $("#OkBtn").click(function(){
          $("#alert").modal('hide');
          $("#YesBtn").show();
          $("#NoBtn").show();
          $("#OkBtn").hide();
          $("#OkBtn1").hide();
          $(".text-danger").hide(); 
      });
      
      
      $("#btnUndo").click(function(){
          $("#AlertMessage").text("Do you want to erase entered information in this record?");
          $("#alert").modal('show');
          $("#YesBtn").data("funcname","fnUndoYes");
          $("#YesBtn").show();
          $("#NoBtn").data("funcname","fnUndoNo");
          $("#NoBtn").show();
          $("#OkBtn").hide();
          $("#OkBtn1").hide();
          $("#NoBtn").focus();
          highlighFocusBtn('activeNo');
        });
  
      
          $("#OkBtn1").click(function(){
          $("#alert").modal('hide');
          $("#YesBtn").show();
          $("#NoBtn").show();
          $("#OkBtn").hide();
          $("#OkBtn1").hide();
          $(".text-danger").hide();
          window.location.href = "{{route('transaction',[$FormId,'index'])}}";
          });
  
          $("#OkBtn").click(function(){
            $("#alert").modal('hide');
          });
  
      window.fnUndoYes = function (){
        window.location.href = "{{route('transaction',[$FormId,'add'])}}";
      }
  
      function showError(pId,pVal){
        $("#"+pId+"").text(pVal);
        $("#"+pId+"").show();
        }
  
      function highlighFocusBtn(pclass){
         $(".activeYes").hide();
         $(".activeNo").hide();
         $("."+pclass+"").show();
      }  
  
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
  
      function getstate(id){
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });	
  
          $.ajax({
              url:'{{route("transaction",[$FormId,"getstate"])}}',
              type:'POST',
              data:{id:id},
              success:function(data) {
                 $("#STATEID_REF").html(data);                 
              },
              error:function(data){
                console.log("Error: Something went wrong.");
              },
          });	
    }


    function getcity(id){
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });	
  
          $.ajax({
              url:'{{route("transaction",[$FormId,"getcity"])}}',
              type:'POST',
              data:{id:id},
              success:function(data) {
                 $("#CITYID_REF").html(data);                 
              },
              error:function(data){
                console.log("Error: Something went wrong.");
              },
          });	
      }
  
  $(document).ready(function(e) {
  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  $('#LEAD_DT').val(today);
  });
      
  function onlyNumberKey(evt) {
      var ASCIICode = (evt.which) ? evt.which : evt.keyCode
      if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
          return false;
      return true;
  }

  </script>
  
  @endpush
