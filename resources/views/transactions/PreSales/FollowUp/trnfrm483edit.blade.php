
@extends('layouts.app')
@section('content')
    <div class="container-fluid topnav">
      <div class="row">
          <div class="col-lg-2">
          <a href="{{route('transaction',[$FormId,'index'])}}" class="btn singlebt">Follow-Up</a>
          </div><!--col-2-->
          <div class="col-lg-10 topnav-pd">
            <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
            <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
            <button class="btn topnavbt" id="btnSave" onclick="submitData('fnSaveData')"><i class="fa fa-floppy-o"></i> Update To Grid</button>
            <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
            <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
            <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
            <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
            <button class="btn topnavbt" onclick="submitDataAp('fnApproveData')"  disabled="disabled" id="btnApprove" {{($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-thumbs-o-up"></i> Approved</button>
            <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
            <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
          </div>
      </div><!--row-->
    </div><!--topnav-->	

    <div class="container-fluid purchase-order-view filter">     
      <form id="frm_mst_edit" method="POST"> 
        @CSRF
        {{isset($objResponse->LEAD_ID) ? method_field('PUT') : '' }}

        <div class="inner-form">
          <div class="row">
            <div class="col-lg-1 pl"><p>Lead No</p></div>
            <div class="col-lg-3 pl">{{isset($objResponse->LEAD_NO) && $objResponse->LEAD_NO !=''?$objResponse->LEAD_NO:''}}
              <input type="hidden" name="LEAD_NO" id="LEAD_NO" value="{{isset($objResponse->LEAD_NO) && $objResponse->LEAD_NO !=''?$objResponse->LEAD_NO:''}}" class="form-control mandatory"  autocomplete="off" readonly  disabled>
              <input type="hidden" name="LEAD_ID" id="LEAD_ID" value="{{isset($objResponse->LEAD_ID) && $objResponse->LEAD_ID !=''?$objResponse->LEAD_ID:''}}"  class="form-control mandatory">
          </div>
            
            <div class="col-lg-1 pl"><p>Lead Date</p></div>
              <div class="col-lg-3 pl">{{isset($objResponse->LEAD_DT) && $objResponse->LEAD_DT !=''?date('d-m-Y',strtotime($objResponse->LEAD_DT)):''}}
                <input type="hidden" name="LEAD_DT" id="LEAD_DT" onchange="checkPeriodClosing('{{$FormId}}',this.value,1)" value="{{isset($objResponse->LEAD_DT) && $objResponse->LEAD_DT !=''?$objResponse->LEAD_DT:''}}" class="form-control mandatory" disabled >
            </div> 
              
            <div class="col-lg-1 pl"><p>Customer</p></div>
            <div class="col-lg-1 pl">
              <input type="radio" name="CUSTOMER" id="CUSTOMER" {{isset($objResponse->CUSTOMER_TYPE) && $objResponse->CUSTOMER_TYPE =='Customer'?'checked':''}} value="{{isset($objResponse->CUSTOMER_TYPE) && $objResponse->CUSTOMER_TYPE =='Customer'?$objResponse->CUSTOMER_TYPE:''}}" onclick="getCustomer(this.value)" disabled>
            </div>
    
            <div class="col-lg-1 pl"><p>Prospect</p></div>
            <div class="col-lg-1 pl">
              <input type="radio" name="CUSTOMER" id="PROSPECT" {{isset($objResponse->CUSTOMER_TYPE) && $objResponse->CUSTOMER_TYPE =='Prospect'?'checked':''}} value="{{isset($objResponse->CUSTOMER_TYPE) && $objResponse->CUSTOMER_TYPE =='Prospect'?$objResponse->CUSTOMER_TYPE:''}}" onclick="getCustomer(this.value)" disabled>
            </div>
          </div>
      
          <div class="row">
            <div class="col-lg-1 pl"><p id="CUSTOMER_TITLE">{{isset($objResponse->CUSTOMER_TYPE)?$objResponse->CUSTOMER_TYPE :''}}</p></div>
            <div class="col-lg-3 pl">{{isset($objResponse->CCODE) && $objResponse->CCODE !=''?$objResponse->CCODE:''}} {{isset($objResponse->CUSTNAME) && $objResponse->CUSTNAME !=''?'- '.$objResponse->CUSTNAME:''}} {{isset($objResponse->PCODE) && $objResponse->PCODE !=''?$objResponse->PCODE:''}} {{isset($objResponse->PROSNAME) && $objResponse->PROSNAME !=''?'- '.$objResponse->PROSNAME:''}}
              <input type="hidden" name="CUSTOMER_TYPE" id="CUSTOMER_TYPE" value="{{isset($objResponse->CUSTOMER_TYPE) && $objResponse->CUSTOMER_TYPE !=''?$objResponse->CUSTOMER_TYPE:''}}" class="form-control" autocomplete="off" disabled/>
              <input type="hidden"  id="CUSTOMERPROSPECT_NAME" onclick="getCustProspect()" value="{{isset($objResponse->CCODE) && $objResponse->CCODE !=''?$objResponse->CCODE:''}}{{isset($objResponse->CUSTNAME) && $objResponse->CUSTNAME !=''?'- '.$objResponse->CUSTNAME:''}} {{isset($objResponse->PCODE) && $objResponse->PCODE !=''?$objResponse->PCODE:''}} {{isset($objResponse->PROSNAME) && $objResponse->PROSNAME !=''?'- '.$objResponse->PROSNAME:''}}" class="form-control mandatory"  autocomplete="off" readonly disabled/>
              <input type="hidden" name="CUSTOMER_PROSPECT" id="CUSTOMER_PROSPECT" value="{{isset($objResponse->CUSTOMER_PROSPECT) && $objResponse->CUSTOMER_PROSPECT !=''?$objResponse->CUSTOMER_PROSPECT:''}}" class="form-control" autocomplete="off" disabled/>
            </div>
    
              <div class="col-lg-1 pl"><p>Company</p></div>
              <div class="col-lg-3 pl">{{isset($objResponse->COMPANY_NAME) && $objResponse->COMPANY_NAME !=''?$objResponse->COMPANY_NAME:''}}
                <input type="hidden" name="COMPANY_NAME" id="COMPANY_NAME" value="{{isset($objResponse->COMPANY_NAME) && $objResponse->COMPANY_NAME !=''?$objResponse->COMPANY_NAME:''}}" class="form-control mandatory" autocomplete="off" disabled> 
              </div>
             
              <div class="col-lg-1 pl"><p>First Name</p></div>
              <div class="col-lg-3 pl">{{isset($objResponse->FIRST_NAME) && $objResponse->FIRST_NAME !=''?$objResponse->FIRST_NAME:''}}
                <input type="hidden" name="FNAME" id="FNAME" value="{{isset($objResponse->FIRST_NAME) && $objResponse->FIRST_NAME !=''?$objResponse->FIRST_NAME:''}}" class="form-control mandatory" autocomplete="off" disabled> 
              </div>
            </div>
            
            <div class="row">
            <div class="col-lg-1 pl"><p>Last Name</p></div>
            <div class="col-lg-3 pl">{{isset($objResponse->LAST_NAME) && $objResponse->LAST_NAME !=''?$objResponse->LAST_NAME:''}}
              <input type="hidden" name="LNAME" id="LNAME" value="{{isset($objResponse->LAST_NAME) && $objResponse->LAST_NAME !=''?$objResponse->LAST_NAME:''}}" class="form-control mandatory" autocomplete="off" disabled>                         
            </div>
          </div>
        <div class="row">
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#FollowUp" id="MAT_TAB" >Follow-Up</a></li>
          </ul>

        <div class="tab-content">
        <div id="FollowUp" class="tab-pane fade in active" style="margin-left: 16px; margin-top: 10px; width: 97%;">
          <div class="row">
            <div class="col-lg-2 pl"><p>Activity Type*</p></div>
            <div class="col-lg-2 pl">
              <input type="text" name="ACTIVITY_TYPE" id="ACTIVITY_TYPE" onclick="getData('{{route('transaction',[$FormId,'getActivityType'])}}','Activity Type Details','ACTIVITY_REF')" class="form-control mandatory" autocomplete="off" readonly>
              <input type="hidden" name="ACTIVITY_REF" id="ACTIVITY_REF" class="form-control mandatory" autocomplete="off" readonly>
            </div>
    
            <div class="col-lg-2 pl"><p>Activity Date*</p></div>
              <div class="col-lg-2 pl">
                <input {{$ActionStatus}} type="date" name="FLOUP_DT" id="ACTIVITY_DT" class="form-control mandatory" autocomplete="off">                            
              </div>
      
              <div class="col-lg-2 pl"><p>Contact Person*</p></div>
              <div class="col-lg-2 pl">
                <input {{$ActionStatus}} type="text" name="CONTACTPERSON" id="CONTACTPERSON"  class="form-control">                            
              </div>
            </div>
  
            <div class="row">
              <div class="col-lg-2 pl"><p>Reminder Detail*</p></div>
              <div class="col-lg-2 pl">
                <select {{$ActionStatus}} name="REMNDETAILID_REF" id="REMNDETAILID_REF" class="form-control">
                  <option value="">Select</option>
                  <option value="Meeting">Meeting</option>
                  <option value="Mail">Mail</option>
                  <option value="Call">Call</option>
                  </select>
              </div>
      
              <div class="col-lg-2 pl"><p>Additonal Member Visit*</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="ADDMEMBER_VISIT" id="ADDMEMBER_VISIT" onclick="getData('{{route('transaction',[$FormId,'getAddMemberVisitCode'])}}','Additonal Member Visit','ADDMEMBER_VISIT_REF')" class="form-control mandatory" autocomplete="off" readonly>
                  <input type="hidden" name="ADDMEMBERVISIT_REF" id="ADDMEMBER_VISIT_REF" class="form-control mandatory">                              
                </div>
        
                <div class="col-lg-2 pl"><p>Response*</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="RESPONSE_NAME" id="RESPONSE_NAME" onclick="getData('{{route('transaction',[$FormId,'getResponseCode'])}}','Response','RESPONSE_REF')" class="form-control mandatory" autocomplete="off" readonly>
                  <input type="hidden" name="RESPONSE_REF" id="RESPONSE_REF" class="form-control mandatory">                              
                </div>
              </div>
  
              <div class="row">
                <div class="col-lg-2 pl"><p>Activity Detail*</p></div>
                <div class="col-lg-2 pl">
                  <textarea {{$ActionStatus}} class="form-control" name="ACTYDETAIL" id="ACTYDETAIL" style="width: 398px; height: 45px;"></textarea>
                </div>

                <div class="col-lg-2 pl" style="margin-left: 206px;"><p>Action Plan*</p></div>
                <div class="col-lg-2 pl">
                  <textarea {{$ActionStatus}} class="form-control" name="ACTYPLAN" id="ACTYPLAN" style="width: 403px;height: 45px;"></textarea>
                </div>
                </div>
                {{-- <button class="btn topnavbt" id="Add"><i class="fa fa-floppy-o"></i> Add</button> --}}
            </div>
          </div>
        </div>
      </div>

      <div id="FollowUp" class="container-fluid purchase-order-view">	
        <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
         <table id="example3" class="display nowrap table table-striped table-bordered">
           <thead id="thead1"  style="position: sticky;top: 0">                      
             <tr>                          
             <th rowspan="2">Activity Type</th>
             <th rowspan="2">Activity Date</th>
             <th rowspan="2">Contact Person</th>
             <th rowspan="2">Reminder Detail</th>
             <th rowspan="2">Additonal Member Visit</th>
             <th rowspan="2">Response</th>
             <th rowspan="2">Activity Detail</th>
             <th rowspan="2">Action Plan</th>
             <th rowspan="2">Action</th>
           </tr>                      
             
         </thead>
           <tbody>
             @if(!empty($MAT))
             @php $n=1; @endphp
             @foreach($MAT as $key => $row)
             <tr  class="participantRow">
              <input type="hidden" name="ACTIVITY_ID" id ="ACTIVITY_ID_{{$key}}" value="{{isset($row->ACTIVITY_ID) && $row->ACTIVITY_ID !=''?$row->ACTIVITY_ID:''}}" class="form-control mandatory" autocomplete="off">
               <td>{{isset($row->ACTIVITYCODE) && $row->ACTIVITYCODE !=''?$row->ACTIVITYCODE:''}}<input type="hidden" name="ACTIVITY_TYPE"                  id ="ACTIVITY_TYPE_{{$key}}"            value="{{isset($row->ACTIVITYCODE) && $row->ACTIVITYCODE !=''?$row->ACTIVITYCODE:''}}" class="form-control mandatory" autocomplete="off"></td>
               <td>{{isset($row->ACTIVITY_DATE) && $row->ACTIVITY_DATE !=''?$row->ACTIVITY_DATE:''}}<input type="hidden" name="ACTYDATE"                    id ="ACTYDATE_{{$key}}"                 value="{{isset($row->ACTIVITY_DATE) && $row->ACTIVITY_DATE !=''?$row->ACTIVITY_DATE:''}}" class="form-control mandatory" autocomplete="off"></td>
               <td>{{isset($row->CONTACT_PERSON) && $row->CONTACT_PERSON !=''?$row->CONTACT_PERSON:''}}<input type="hidden" name="CONTACT_PERSON"           id ="CONTACT_PERSON_{{$key}}"           value="{{isset($row->CONTACT_PERSON) && $row->CONTACT_PERSON !=''?$row->CONTACT_PERSON:''}}" class="form-control mandatory" autocomplete="off"></td>
               <td>{{isset($row->REMINDER_DETAIL) && $row->REMINDER_DETAIL !=''?$row->REMINDER_DETAIL:''}}<input type="hidden" name="REMAINDER_DETAILS"     id ="REMAINDER_DETAILS_{{$key}}"        value="{{isset($row->REMINDER_DETAIL) && $row->REMINDER_DETAIL !=''?$row->REMINDER_DETAIL:''}}" class="form-control mandatory" autocomplete="off"></td>
               <td>{{isset($row->CODE_NAME) && $row->CODE_NAME !=''?$row->CODE_NAME:''}}<input type="hidden" name="ADDITONL_VISIT"                          id ="ADDITONL_VISIT_{{$key}}"           value="{{isset($row->CODE_NAME) && $row->CODE_NAME !=''?$row->CODE_NAME:''}}"                     class="form-control mandatory" autocomplete="off"></td>
               <td>{{isset($row->RESPONSECODE) && $row->RESPONSECODE !=''?$row->RESPONSECODE:''}}<input type="hidden" name="RESPNCE"                        id ="RESPNCE_{{$key}}"                  value="{{isset($row->RESPONSECODE) && $row->RESPONSECODE !=''?$row->RESPONSECODE:''}}"              class="form-control mandatory" autocomplete="off"></td>
               <td>{{isset($row->ACTIVITY_DETAILS) && $row->ACTIVITY_DETAILS !=''?$row->ACTIVITY_DETAILS:''}}<input type="hidden" name="ACTIVITY_DETAILS"   id ="ACTIVITY_DETAILS_{{$key}}"         value="{{isset($row->ACTIVITY_DETAILS) && $row->ACTIVITY_DETAILS !=''?$row->ACTIVITY_DETAILS:''}}"    class="form-control mandatory" autocomplete="off"></td>
               <td>{{isset($row->ACTION_PLAN) && $row->ACTION_PLAN !=''?$row->ACTION_PLAN:''}}<input type="hidden" name="ACTION_PLAN"                       id ="ACTION_PLAN_{{$key}}"              value="{{isset($row->ACTION_PLAN) && $row->ACTION_PLAN !=''?$row->ACTION_PLAN:''}}"                     class="form-control mandatory" autocomplete="off"></td>
               <td hidden><input type="hidden" name="ACTIVITY_REF"          id ="ACTIVITY_REF_{{$key}}"         value="{{isset($row->ACTIVITYID_REF) && $row->ACTIVITYID_REF !=''?$row->ACTIVITYID_REF:''}}" class="form-control mandatory" autocomplete="off"></td>
               <td hidden><input type="hidden" name="ADDMEMBER_VISIT_REF"   id ="ADDMEMBER_VISIT_REF_{{$key}}"  value="{{isset($row->ADDITIONAL_EMPLOYEE_ID) && $row->ADDITIONAL_EMPLOYEE_ID !=''?$row->ADDITIONAL_EMPLOYEE_ID:''}}" class="form-control mandatory" autocomplete="off"></td>
               <td hidden><input type="hidden" name="RESPONSE_REF"          id ="RESPONSE_REF_{{$key}}"         value="{{isset($row->RESPONSEID_REF) && $row->RESPONSEID_REF !=''?$row->RESPONSEID_REF:''}}" class="form-control mandatory" autocomplete="off"></td>
               {{-- <td style="width: 110px;"><input type="radio" name="selectRow" onchange="getDataVal('{{$key}}')"></td> --}}
               <td><a {{$key ==0?'':'disabled'}} class="btn add" title="Edit" id="editrow_{{$key}}" data-toggle="tooltip" style="color: rgb(43, 41, 41);"><i class="fa fa-edit" onclick="getDataVal('{{$key}}')" style="cursor: pointer;"></i></a></td>
             </tr>
             @php $n++; @endphp
           @endforeach 
         @endif
         </tbody>
       </table>
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
  
  
/*************************************   All Search Start  ************************** */

      function getDataVal(id){
          if(id==0){
          $('#ACTIVITY_TYPE').val($('#ACTIVITY_TYPE_'+id).val());
          $('#FLOUP_DT').val($('#ACTYDATE_'+id).val());
          $('#CONTACTPERSON').val($('#CONTACT_PERSON_'+id).val());
          $('#REMNDETAILID_REF').val($('#REMAINDER_DETAILS_'+id).val());
          $('#ADDMEMBER_VISIT').val($('#ADDITONL_VISIT_'+id).val());
          $('#RESPONSE_NAME').val($('#RESPNCE_'+id).val());
          $('#ACTYDETAIL').val($('#ACTIVITY_DETAILS_'+id).val());
          $('#ACTYPLAN').val($('#ACTION_PLAN_'+id).val());
          $('#ACTIVITY_REF').val($('#ACTIVITY_REF_'+id).val());
          $('#ADDMEMBER_VISIT_REF').val($('#ADDMEMBER_VISIT_REF_'+id).val());
          $('#RESPONSE_REF').val($('#RESPONSE_REF_'+id).val());
          }else{
            $clone.find($('#editrow_'+id)).prop('disabled','true');
          }
        }

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
            bindActivityTypeEvents()
            bindAditnalMbrVisitEvents()
            bindResponseEvents()
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

      function bindActivityTypeEvents(){
        $('.clsacttype').click(function(){
        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc");
        $("#ACTIVITY_TYPE").val(texdesc);
        $("#ACTIVITY_REF").val(txtval);
        $("#modalpopup").hide();
        });
      }
      
      function bindAditnalMbrVisitEvents(){
        $('.clsaddmeb').click(function(){
        var addId_Ref = []
        var addCode = []
        $('.clsaddmeb:checked').each(function() {     
          addId_Ref.push($(this).val())
        });
        $('.clsaddmeb:checked').each(function() {       
          addCode.push($(this).data("desc1"))
        });

        $("#ADDMEMBER_VISIT").val(addCode);
        $("#ADDMEMBER_VISIT_REF").val(addId_Ref);
        //$("#modalpopup").hide();
        });
      }

      function bindResponseEvents(){
        $('.clsres').click(function(){
        var id = $(this).attr('id');
        var txtval =    $("#txt"+id+"").val();
        var texdesc =   $("#txt"+id+"").data("desc");
        $("#RESPONSE_NAME").val(texdesc);
        $("#RESPONSE_REF").val(txtval);
        $("#modalpopup").hide();
        });
      }

      $(document).ready(function(e) {
        $('#ACTIVITY_TYPE').val($('#ACTIVITY_TYPE_0').val());
        $('#ACTIVITY_DT').val($('#ACTYDATE_0').val());
        $('#CONTACTPERSON').val($('#CONTACT_PERSON_0').val());
        $('#REMNDETAILID_REF').val($('#REMAINDER_DETAILS_0').val());
        $('#ADDMEMBER_VISIT').val($('#ADDITONL_VISIT_0').val());
        $('#RESPONSE_NAME').val($('#RESPNCE_0').val());
        $('#ACTYDETAIL').val($('#ACTIVITY_DETAILS_0').val());
        $('#ACTYPLAN').val($('#ACTION_PLAN_0').val());
        $('#ACTIVITY_REF').val($('#ACTIVITY_REF_0').val());
        $('#ADDMEMBER_VISIT_REF').val($('#ADDMEMBER_VISIT_REF_0').val());
        $('#RESPONSE_REF').val($('#RESPONSE_REF_0').val());
      });
      

    
/************************************* All Search Start  ************************** */

    let input, filter, table, tr, td, i, txtValue;
      function colSearch(ptable,ptxtbox,pcolindex) {
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
  
      var ACTIVITY_REF         =   $.trim($("#ACTIVITY_REF").val());
      var ACTIVITY_DT             =   $.trim($("#ACTIVITY_DT").val());
      var CONTACT_PERSON       =   $.trim($("#CONTACTPERSON").val());
      var REMNDETAILID_REF     =   $.trim($("#REMNDETAILID_REF").val());
      var ADDMEMBER_VISIT_REF  =   $.trim($("#ADDMEMBER_VISIT_REF").val());
      var RESPONSE_REF         =   $.trim($("#RESPONSE_REF").val());
      var ACTYDETAIL           =   $.trim($("#ACTYDETAIL").val());
      var ACTYPLAN             =   $.trim($("#ACTYPLAN").val());
      
      $("#OkBtn1").hide();

      if(ACTIVITY_REF ===""){
        alertMsg('ACTIVITY_TYPE','Please Select Activity Type.');
      }
      else if(ACTIVITY_DT ===""){
        alertMsg('ACTIVITY_DT','Please Select Date.');
      }
      else if(CONTACT_PERSON ===""){
        alertMsg('CONTACT_PERSON','Please enter Contact Person.');
      }
      else if(REMNDETAILID_REF ===""){
        alertMsg('REMNDETAILID_REF','Please Select Reminder Detail.');
      }
      else if(ADDMEMBER_VISIT_REF ===""){
        alertMsg('ADDMEMBER_VISIT','Please Select Additonal Member Visit.');
      }
      else if(RESPONSE_REF ===""){
        alertMsg('RESPONSE_NAME','Please Select Response.');
      }
      else if(ACTYDETAIL ===""){
        alertMsg('ACTYDETAIL','Please enter Activity Detail.');
      }
      else if(ACTYPLAN ===""){
        alertMsg('ACTYPLAN','Please enter Action Plan.');
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
    
     var formResponseMst = $( "#frm_mst_edit" );
         formResponseMst.validate();
    
        function validateSingleElemnet(element_id){
          var validator =$("#frm_mst_edit" ).validate();
             if(validator.element( "#"+element_id+"" )){
             }
          }
    
        function checkDuplicateCode(){
            var getDataForm = $("#frm_mst_edit");
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
                    showError('ERROR_ATTCODE',data.msg);
                    $("#ATTCODE").focus();
                    }                                
                },
                error:function(data){
                  console.log("Error: Something went wrong.");
                },
            });
        }
    
          function submitData(type){
              if(formResponseMst.valid()){
                validateForm("fnSaveData");
              }
          }

          function submitDataAp(type){
              if(formResponseMst.valid()){
                validateForm("fnApproveData");
              }
          }

          window.fnSaveData = function (){
              submitForm('update');
          };

          window.fnApproveData = function (){
              submitForm('approve');
          }

                        
        function submitForm(requestType){
            var getDataForm = $("#frm_mst_edit");
            var formData = getDataForm.serialize() + "&requestType=" + requestType ;
            //var formData = getDataForm.append(requestType);

            $.ajaxSetup({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'{{route("transactionmodify",[$FormId,"update"])}}',
                type:'POST',
                data:formData,
                success:function(data) {
                  if(data.success) {                   
                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").show();
                  $("#AlertMessage").text(data.msg);
                  $(".text-danger").hide();
                  $("#alert").modal('show');
                  $("#OkBtn").focus();
                  }     
                  if(data.errors) {
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text(data.msg);
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                  }
                },
                error:function(data){
                console.log("Error: Something went wrong.");
                },
              });
          }

        $("#YesBtn").click(function(){
            $("#alert").modal('hide');
            var customFnName = $("#YesBtn").data("funcname");
            window[customFnName]();
        });

        $("#NoBtn").click(function(){
          $("#alert").modal('hide');
          var custFnName = $("#NoBtn").data("funcname");
          window[custFnName]();
        });

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
            location.reload(); 
            //window.location.href = "{{route('transaction',[$FormId,'index'])}}";
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
          //window.location.href = "{{route('transaction',[$FormId,'index'])}}";
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

    $(document).ready(function(e) {
      var d = new Date(); 
      var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
      $('#FLOUP_DT').val(today);
    });
        
    function onlyNumberKey(evt) {
      var ASCIICode = (evt.which) ? evt.which : evt.keyCode
      if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
          return false;
      return true;
    }
  
    </script>
    @endpush
  