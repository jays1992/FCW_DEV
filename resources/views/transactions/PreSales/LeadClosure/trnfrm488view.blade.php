
@extends('layouts.app')
@section('content')
<!-- <form id="frm_mst_se" onsubmit="return validateForm()"  method="POST"  >     -->
 
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Lead Closure</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveSE" disabled="disabled"><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo" disabled="disabled" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" disabled="disabled" {{($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                        
                </div>
            </div><!--row-->
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->

    <div class="container-fluid purchase-order-view filter">     
      <form id="frm_mst_edit" method="POST"> 
        @CSRF
        {{isset($objResponse->LEAD_ID) ? method_field('PUT') : '' }}

        <div class="inner-form">
          <div class="row">
            <div class="col-lg-1 pl"><p>Lead No*</p></div>
            <div class="col-lg-3 pl">{{isset($objResponse->LEAD_NO) && $objResponse->LEAD_NO !=''?$objResponse->LEAD_NO:''}}
              <input type="hidden" name="LEAD_NO" id="LEAD_NO" value="{{isset($objResponse->LEAD_NO) && $objResponse->LEAD_NO !=''?$objResponse->LEAD_NO:''}}" class="form-control mandatory"  autocomplete="off" readonly >
              <input type="hidden" name="MAT_ROW_ID" id="MAT_ROW_ID" >
            </div>
            <div class="col-lg-1 pl"><p>Lead Date*</p></div>
              <div class="col-lg-3 pl">{{isset($objResponse->LEAD_DT) && $objResponse->LEAD_DT !=''?date('d-m-Y',strtotime($objResponse->LEAD_DT)):''}}
                <input type="hidden" name="LEAD_DT" id="LEAD_DT" value="{{isset($objResponse->LEAD_DT) && $objResponse->LEAD_DT !=''?$objResponse->LEAD_DT:''}}" class="form-control mandatory" >
            </div> 
              
            <div class="col-lg-1 pl"><p>Customer</p></div>
            <div class="col-lg-1 pl">
              <input type="radio" name="CUSTOMER" id="CUSTOMER" {{isset($objResponse->CUSTOMER_TYPE) && $objResponse->CUSTOMER_TYPE =='Customer'?'checked':''}} value="Customer" onclick="getCustomer(this.value)" disabled>
            </div>
    
            <div class="col-lg-1 pl"><p>Prospect</p></div>
            <div class="col-lg-1 pl">
              <input type="radio" name="CUSTOMER" id="PROSPECT" {{isset($objResponse->CUSTOMER_TYPE) && $objResponse->CUSTOMER_TYPE =='Prospect'?'checked':''}} value="Prospect" onclick="getCustomer(this.value)" disabled>
            </div>
          </div>
      
          <div class="row">
            <div class="col-lg-1 pl"><p id="CUSTOMER_TITLE">{{isset($objResponse->CUSTOMER_TYPE)?$objResponse->CUSTOMER_TYPE :''}}</p></div>
            <div class="col-lg-3 pl">{{isset($objResponse->CCODE) && $objResponse->CCODE !=''?$objResponse->CCODE:''}} {{isset($objResponse->CUSTNAME) && $objResponse->CUSTNAME !=''?'- '.$objResponse->CUSTNAME:''}} {{isset($objResponse->PCODE) && $objResponse->PCODE !=''?$objResponse->PCODE:''}} {{isset($objResponse->PROSNAME) && $objResponse->PROSNAME !=''?'- '.$objResponse->PROSNAME:''}}
              <input type="hidden" name="CUSTOMER_TYPE" id="CUSTOMER_TYPE" value="{{isset($objResponse->CUSTOMER_TYPE) && $objResponse->CUSTOMER_TYPE !=''?$objResponse->CUSTOMER_TYPE:''}}" class="form-control" autocomplete="off"/>
              <input type="hidden"  id="CUSTOMERPROSPECT_NAME" onclick="getCustProspect()" value="{{isset($objResponse->CCODE) && $objResponse->CCODE !=''?$objResponse->CCODE:''}}{{isset($objResponse->CUSTNAME) && $objResponse->CUSTNAME !=''?'- '.$objResponse->CUSTNAME:''}} {{isset($objResponse->PCODE) && $objResponse->PCODE !=''?$objResponse->PCODE:''}} {{isset($objResponse->PROSNAME) && $objResponse->PROSNAME !=''?'- '.$objResponse->PROSNAME:''}}" class="form-control mandatory"  autocomplete="off" readonly/>
              <input type="hidden" name="CUSTOMER_PROSPECT" id="CUSTOMER_PROSPECT" value="{{isset($objResponse->CUSTOMER_PROSPECT) && $objResponse->CUSTOMER_PROSPECT !=''?$objResponse->CUSTOMER_PROSPECT:''}}" class="form-control" autocomplete="off"/>
            </div>
    
            <select name="CONVERTSTATUS" id="CONVERTSTATUS" hidden>
              <option {{isset($HDR->objResponse) && $HDR->objResponse == 'Prospecting'?'selected="selected"':''}} value="Prospecting">Prospecting</option>
              <option {{isset($HDR->objResponse) && $HDR->objResponse == 'Qualifying Leads'?'selected="selected"':''}} value="Qualifying Leads">Qualifying Leads</option>
              <option {{isset($HDR->objResponse) && $HDR->objResponse == 'Opportunity'?'selected="selected"':''}}value="Opportunity">Opportunity</option>
            </select> 
            
              <div class="col-lg-1 pl"><p>Company</p></div>
                <div class="col-lg-3 pl">{{isset($objResponse->COMPANY_NAME) && $objResponse->COMPANY_NAME !=''?$objResponse->COMPANY_NAME:''}}
                  <input type="hidden" name="COMPANY_NAME" id="COMPANY_NAME" value="{{isset($objResponse->COMPANY_NAME) && $objResponse->COMPANY_NAME !=''?$objResponse->COMPANY_NAME:''}}" class="form-control mandatory" autocomplete="off">                            
                </div>
           
              <div class="col-lg-1 pl"><p>First Name*</p></div>
              <div class="col-lg-3 pl">{{isset($objResponse->FIRST_NAME) && $objResponse->FIRST_NAME !=''?$objResponse->FIRST_NAME:''}}
                <input type="hidden" name="FNAME" id="FNAME" value="{{isset($objResponse->FIRST_NAME) && $objResponse->FIRST_NAME !=''?$objResponse->FIRST_NAME:''}}" class="form-control mandatory" autocomplete="off">                            
              </div>
            </div>
    
            <div class="row">
              <div class="col-lg-1 pl"><p>Last Name</p></div>
              <div class="col-lg-3 pl">{{isset($objResponse->LAST_NAME) && $objResponse->LAST_NAME !=''?$objResponse->LAST_NAME:''}}
                <input type="hidden" name="LNAME" id="LNAME" value="{{isset($objResponse->LAST_NAME) && $objResponse->LAST_NAME !=''?$objResponse->LAST_NAME:''}}" class="form-control mandatory" autocomplete="off">                            
              </div>
    
              <div class="col-lg-2 pl">
                <textarea name="ADDRESS" id="ADDRESS" hidden>{{isset($objResponse->ADDRESS) && $objResponse->ADDRESS !=''?$objResponse->ADDRESS:''}}</textarea>
              </div>
    
              <div class="col-lg-2 pl">
                <input type="hidden" name="COUNTRY" id="COUNTRY" value="{{isset($objResponse->CONTRYNAME) && $objResponse->CONTRYNAME !=''?$objResponse->CONTRYNAME:''}}" class="form-control mandatory"  autocomplete="off" readonly/>
                <input type="hidden" name="COUNTRYID_REF" id="COUNTRYID_REF" value="{{isset($objResponse->CTRYID_REF) && $objResponse->CTRYID_REF !=''?$objResponse->CTRYID_REF:''}}" class="form-control" autocomplete="off" />
              </div>
            </div>
    
            <div class="row">
              <div class="col-lg-2 pl">
                <input type="hidden" name="STATE" id="STATE" value="{{isset($objResponse->STATENAME) && $objResponse->STATENAME !=''?$objResponse->STATENAME:''}}" class="form-control mandatory"  autocomplete="off" readonly/>
                <input type="hidden" name="STATEID_REF" id="STATEID_REF" value="{{isset($objResponse->STID_REF) && $objResponse->STID_REF !=''?$objResponse->STID_REF:''}}" class="form-control" autocomplete="off" />
              </div>
    
                <div class="col-lg-2 pl">
                  <input type="hidden" name="CITYID_REF_POPUP" id="CITYID_REF_POPUP" value="{{isset($objResponse->CITYNAME) && $objResponse->CITYNAME !=''?$objResponse->CITYNAME:''}}" class="form-control mandatory" readonly tabindex="1" />
                  <input type="hidden" name="CITYID_REF" id="CITYID_REF" value="{{isset($objResponse->CITYID_REF) && $objResponse->CITYID_REF !=''?$objResponse->CITYID_REF:''}}" />
                </div>
    
                <div class="col-lg-2 pl">
                  <input type="hidden" name="PINCODE" id="PINCODE" value="{{isset($objResponse->PINCODE) && $objResponse->PINCODE !=''?$objResponse->PINCODE:''}}" onkeypress="return onlyNumberKey(event)" maxlength="6" class="form-control mandatory" autocomplete="off">                             
                </div>
              </div>
              
              <div class="row">
              <div class="col-lg-2 pl">
                <input type="hidden" name="LOWNER" id="LOWNER" value="{{isset($objResponse->EMPCODE) && $objResponse->EMPCODE !=''?$objResponse->EMPCODE:''}}" class="form-control mandatory"  autocomplete="off" readonly/>
                <input type="hidden" name="LOWNERID_REF" id="LOWNERID_REF" value="{{isset($objResponse->LEADOWNERID_REF) && $objResponse->LEADOWNERID_REF !=''?$objResponse->LEADOWNERID_REF:''}}" class="form-control" autocomplete="off" />
              </div>
    
              <div class="col-lg-2 pl">
                <input type="hidden" name="INTYPE" id="INTYPE" value="{{isset($objResponse->INDSCODE) && $objResponse->INDSCODE !=''?$objResponse->INDSCODE:''}}" class="form-control mandatory"  autocomplete="off" readonly/>
                <input type="hidden" name="INTYPEID_REF" id="INTYPEID_REF" value="{{isset($objResponse->INDSID_REF) && $objResponse->INDSID_REF !=''?$objResponse->INDSID_REF:''}}" class="form-control" autocomplete="off" />
              </div>
              
              <div class="col-lg-2 pl">
                <select name="DESIGNID_REF" id="DESIGNID_REF" hidden>
                  <option value="">Select</option>
                  @foreach ($design as $val)
                  <option {{isset($objResponse->DESGINATION) && $objResponse->DESGINATION == $val-> DESGID ?'selected="selected"':''}} value="{{ $val-> DESGID }}">{{$val->DESGCODE}} - {{$val->DESCRIPTIONS}}</option> 
                  @endforeach
                  </select>                            
              </div>
            </div>
    
            <div class="row">
            <div class="col-lg-2 pl">
              <input type="hidden" name="CONTACT_PERSON" id="CONTACT_PERSON" value="{{isset($objResponse->CONTACT_PERSON) && $objResponse->CONTACT_PERSON !=''?$objResponse->CONTACT_PERSON:''}}" class="form-control mandatory" autocomplete="off">                            
            </div>
    
            <div class="col-lg-2 pl">
              <input type="hidden" name="LEAD_DETAILS" id="LEAD_DETAILS" value="{{isset($objResponse->LEAD_DETAILS) && $objResponse->LEAD_DETAILS !=''?$objResponse->LEAD_DETAILS:''}}" class="form-control mandatory" autocomplete="off">                            
            </div>
    
            <div class="col-lg-2 pl">
              <input type="hidden" name="WEBSITENAME" id="WEBSITENAME" value="{{isset($objResponse->WEBSITE) && $objResponse->WEBSITE !=''?$objResponse->WEBSITE:''}}" class="form-control">                            
            </div>
          </div>
      
          <div class="row">
            <div class="col-lg-2 pl">
              <input type="hidden" name="LANDNUMBER" id="LANDNUMBER" value="{{isset($objResponse->LANDLINE_NUMBER) && $objResponse->LANDLINE_NUMBER !=''?$objResponse->LANDLINE_NUMBER:''}}" onkeypress="return onlyNumberKey(event)" class="form-control mandatory" autocomplete="off">                            
            </div>
         
            <div class="col-lg-2 pl">
              <input type="hidden" name="MOBILENUMBER" id="MOBILENUMBER" value="{{isset($objResponse->MOBILE_NUMBER) && $objResponse->MOBILE_NUMBER !=''?$objResponse->MOBILE_NUMBER:''}}" onkeypress="return onlyNumberKey(event)" maxlength="12" class="form-control mandatory" autocomplete="off">                           
            </div>
          
            <div class="col-lg-2 pl">
              <input type="hidden" name="EMAIL" id="EMAIL" value="{{isset($objResponse->EMAIL) && $objResponse->EMAIL !=''?$objResponse->EMAIL:''}}" class="form-control mandatory" autocomplete="off"> 
              <input type="hidden" name="OPPORTY_REFID" id="OPPORTY_REFID" value="{{isset($objResponse->OPPORTUNITY_TYPE_ID) && $objResponse->OPPORTUNITY_TYPE_ID !=''?$objResponse->OPPORTUNITY_TYPE_ID:''}}" class="form-control mandatory" autocomplete="off"> 
              <input type="hidden" name="OPPORTYSTAGE_REFID" id="OPPORTYSTAGE_REFID" value="{{isset($objResponse->OPPORTUNITY_STAGE_ID) && $objResponse->OPPORTUNITY_STAGE_ID !=''?$objResponse->OPPORTUNITY_STAGE_ID:''}}" class="form-control mandatory" autocomplete="off">
              
              <input type="hidden" name="OPPORTY_DATE" id="OPPORTY_DATE" value="{{isset($objResponse->OPPORTUNITY_DATE) && $objResponse->OPPORTUNITY_DATE !=''?$objResponse->OPPORTUNITY_DATE:''}}" class="form-control mandatory" autocomplete="off"> 
              <input type="hidden" name="EXPECTED_DATE" id="EXPECTED_DATE" value="{{isset($objResponse->EXPECTED_DATE) && $objResponse->EXPECTED_DATE !=''?$objResponse->EXPECTED_DATE:''}}" class="form-control mandatory" autocomplete="off">                           
  
            </div>
          </div>
      
          <div class="row">
            <div class="col-lg-2 pl">
              <input type="hidden" name="LSOURCE" id="LSOURCE" value="{{isset($objResponse->LEAD_SOURCENAME) && $objResponse->LEAD_SOURCENAME !=''?$objResponse->LEAD_SOURCENAME:''}}" class="form-control mandatory"  autocomplete="off" readonly/>
              <input type="hidden" name="LSOURCEID_REF" id="LSOURCEID_REF" value="{{isset($objResponse->LEAD_SOURCE) && $objResponse->LEAD_SOURCE !=''?$objResponse->LEAD_SOURCE:''}}" class="form-control" autocomplete="off" />
            </div>
          
            <div class="col-lg-2 pl">
              <input type="hidden" name="LSTATUS" id="LSTATUS" value="{{isset($objResponse->LEAD_STATUSCODE) && $objResponse->LEAD_STATUSCODE !=''?$objResponse->LEAD_STATUSCODE:''}}" class="form-control mandatory"  autocomplete="off" readonly/>
              <input type="hidden" name="LSTATUSID_REF" id="LSTATUSID_REF" value="{{isset($objResponse->LEAD_STATUS) && $objResponse->LEAD_STATUS !=''?$objResponse->LEAD_STATUS:''}}" class="form-control" autocomplete="off" />
            </div>
      
            <div class="col-lg-2 pl">
              <input type="hidden" name="ASSIGTO" id="ASSIGTO" value="{{isset($objResp->ASSGNTOCODE) && $objResp->ASSGNTOCODE !=''?$objResp->ASSGNTOCODE:''}} - {{$objResp->ASSGNTOFNAME}}" class="form-control mandatory"  autocomplete="off" readonly/>
              <input type="hidden" name="ASSIGTOID_REF" id="ASSIGTOID_REF" value="{{isset($objResp->ASSIGNTO_REF) && $objResp->ASSIGNTO_REF !=''?$objResp->ASSIGNTO_REF:''}}" class="form-control" autocomplete="off" />
            </div>
          </div>
    
          <div class="row">
            <ul class="nav nav-tabs">
              <li class="active"><a data-toggle="tab" href="#LeadClosure" id="MAT_TAB" >Lead Closure</a></li>
            </ul>
    
          <div class="tab-content">
          <div id="LeadClosure" class="tab-pane fade in active" style="margin-left: 16px; margin-top: 10px; width: 97%;">
            <div class="row">
              <div class="col-lg-2 pl"><p>Lead Closure</p></div>
              <div class="col-lg-2 pl">
                <select {{$ActionStatus}} name="LEADCLOSURE" id="LCLOSUR" class="form-control">
                  <option {{isset($objResponse->LEAD_CLOSURE) && $objResponse->LEAD_CLOSURE == '1'?'selected="selected"':''}} value="1">Yes</option>
                  <option {{isset($objResponse->LEAD_CLOSURE) && $objResponse->LEAD_CLOSURE == '0'?'selected="selected"':''}} value="0">No</option>
                  </select>
              </div>
            
                <div class="col-lg-2 pl"><p>Remarks</p></div>
                <div class="col-lg-2 pl">
                  <textarea {{$ActionStatus}} name="REMARKSNAME" id="REMARKS" style="width: 357px; height: 40px;" class="form-control mandatory">{{isset($objResponse->REMARKS) && $objResponse->REMARKS !=''?$objResponse->REMARKS:''}}</textarea>
                </div>
              </div>
                    
                  {{-- <button class="btn topnavbt" id="Add"><i class="fa fa-floppy-o"></i> Add</button> --}}
                </div>
              </div>
            </div>
          </div>
          </div>
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
    
      function getData(path,msg,id){
    
          var listid = $("#"+id).val();
    
          $('#getData_tbody').html('Loading...'); 
    
          $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
    
          $.ajax({
              url:path,
              type:'POST',
              data:{listid:listid},
              success:function(data) {
              $('#getData_tbody').html(data);
              bindOpportunityTypeEvents()
              bindOpportunityStageEvents()
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
    
        function bindOpportunityTypeEvents(){
            $('.clsopptype').click(function(){
            var id = $(this).attr('id');
            var txtval =    $("#txt"+id+"").val();
            var texdesc =   $("#txt"+id+"").data("desc");
            $("#OPPORTY_TYPE").val(texdesc);
            $("#OPPORTY_REF").val(txtval);
            $("#modalpopup").hide();
            });
          }
    
        function bindOpportunityStageEvents(){
            $('.clsopstage').click(function(){
            var id = $(this).attr('id');
            var txtval =    $("#txt"+id+"").val();
            var texdesc =   $("#txt"+id+"").data("desc");
            var texdcompernt =   $("#txt"+id+"").data("compernt");
            $("#OPPORTY_STAGE").val(texdesc);
            $("#OPPORTYSTAGE_REF").val(txtval);
            $("#OPPORTY_STAGECOMPLTED").val(texdcompernt);
            $("#modalpopup").hide();
            });
          }
    
    
    /************************************* All Search Start  ************************** */
    
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
    
          var LCLOSUR           =   $.trim($("#LCLOSUR").val());
          var REMARKS             =   $.trim($("#REMARKS").val());
          
          $("#OkBtn1").hide();
    
          if(LCLOSUR ===""){
            alertMsg('LCLOSUR','Please Select Lead Closure.');
          }
          else if(REMARKS ===""){
            alertMsg('REMARKS','Please enter Remarks.');
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
                  checkDuplicateCode();
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
                      showError('ERROR',data.msg);
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
        
      </script>
      @endpush
    