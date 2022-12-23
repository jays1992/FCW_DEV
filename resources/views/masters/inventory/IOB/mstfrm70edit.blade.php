@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2">
      <a href="{{route('master',[70,'index'])}}" class="btn singlebt">Item wise Opening Balance (IOB)</a>
    </div>
    <div class="col-lg-10 topnav-pd">
      <button href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
      <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
      <button <?php echo $OpeningStatus =='A'?'disabled':'';?> id="btnSave" {{isset($objRights->EDIT) && $objRights->EDIT != 1 ? 'disabled' : ''}}  class="btn topnavbt" tabindex="4"><i class="fa fa-save"></i> Save</button>
      <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
      <button class="btn topnavbt" disabled="disabled"><i class="fa fa-print"></i> Print</button>
      <button <?php echo $OpeningStatus =='A'?'disabled':'';?> class="btn topnavbt"  id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
      <button class="btn topnavbt" id="btnCancel" disabled="disabled" ><i class="fa fa-times"></i> Cancel</button>
      <button <?php echo $OpeningStatus =='A'?'disabled':'';?> class="btn topnavbt" id="btnApprove" {{ (isset($objRights->APPROVAL1) || isset($objRights->APPROVAL2) || isset($objRights->APPROVAL3) || isset($objRights->APPROVAL4) || isset($objRights->APPROVAL5)) &&  ($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}} ><i class="fa fa-lock"></i> Approved</button>
      <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</button>
      <button class="btn topnavbt" id="btnExit"><i class="fa fa-power-off"></i> Exit</button>
    </div>
  </div>
</div>	
   
<div class="container-fluid purchase-order-view filter">     
  <form id="frm_mst_edit" method="POST" onsubmit="return validateForm()" class="needs-validation"  > 
    @CSRF     
    <div class="inner-form">
      <div class="row">
        <div class="col-lg-2 pl"><p>Date of Opening Balance</p></div>
        <div class="col-lg-2 pl">
          <input type="date" name="DOOB" id="DOOB"  class="form-control mandatory" value="{{isset($OpeningDate) && $OpeningDate !=''?$OpeningDate:date('Y-m-d')}}" <?php echo isset($OpeningDate) && $OpeningDate !=''?'readonly':'';?> placeholder="dd-mm-yyyy"  />                      
          <input type="hidden" name="user_approval_level" id="user_approval_level" value="{{ $user_approval_level }}"  />
        </div>
      </div>   

      <div class="row">
        <div class="col-lg-12 pl">
          <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:400px;"  id="load_scroll" >
            <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
              <thead id="thead1"  style="position: sticky;top: 0">       
                <tr>
                  <th>
                    Item Code
                    <input type="hidden" id="focusid" >
                    <input type="hidden" id="errorid" >
                  </th>
                  <th>Item Name</th>
                  <th {{$AlpsStatus['hidden']}} >Alps Part Number</th>
                  <th {{$AlpsStatus['hidden']}} >Customer Part No</th>
                  <th {{$AlpsStatus['hidden']}} >OEM Part No</th>
                  <th >Main UOM</th>
                  <th>Store</th>
                  <th>Opening Balance Qty</th>
                  <th>Opening Rate</th>
                  <th>Opening Value</th>
                </tr>
              </thead>
                        
              <tbody id="load_data" >
                           
              </tbody>
            </table>       
          </div>
        </div>

        <div class="col-lg-12 pl">
          <div id="load_data_message"></div>
        </div>
      </div>

    </div>
  </form>
</div>
@endsection

@section('alert')
<div id="FORMpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width: 950px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='FORM_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="FORMTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
          <tr id="none-select" class="searchalldata"  hidden>            
            <td colspan="7"> <input type="text" name="fieldid" id="hdn_FORMid"/>
              <input type="text" name="fieldid2" id="hdn_FORMid2"/>
              <input type="text" name="fieldid3" id="hdn_FORMid3"/>
              <input type="text" name="fieldid4" id="hdn_FORMid4"/>
            </td>
          </tr>
    </thead>
    <tbody>
    {{-- <tr>
      <td>
        <input type="text" id="FORMcodesearch" onkeyup="FORMCodeFunction()">
      </td>
      <td>
        <input type="text" id="FORMnamesearch" onkeyup="FORMNameFunction()">
      </td>

    </tr> --}}
    </tbody>
    </table>
      <table id="FORMTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <input class="form-control" type="hidden" name="Row_Count9" id ="Row_Count9" value="1">
        </thead>
        <tbody id="tbody_FORM">     
        
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- FORMUP END-->
<!-- Stores Dropdown -->
<div id="SOpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md column3_modal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='SOclosePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Stores</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="StoresTable" class="display nowrap table  table-striped table-bordered" >
    <thead>
          <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="fieldid" id="hdn_soid"/>
            <input type="hidden" name="fieldid2" id="hdn_soid2"/></td>
          </tr>

    <tr>
      <th class="ROW1">Select</th> 
      <th class="ROW2">Code</th>
      <th class="ROW3">Description</th>
    </tr>
    </thead>
    <tbody>

      <tr>
        <th class="ROW1"><span class="check_th">&#10004;</span></th>
        <td class="ROW2"><input type="text" id="SalesOrdercodesearch" class="form-control" autocomplete="off" onkeyup="StoresCodeFunction()"></td>
        <td class="ROW3"><input type="text" id="SalesOrdernamesearch" class="form-control" autocomplete="off" onkeyup="StoresNameFunction()"></td>
      </tr>

    </tbody>
    </table>
      <table id="StoresTable2" class="display nowrap table  table-striped table-bordered" >
        <thead id="thead2">
        </thead>
        <tbody  id="tbody_SO">     
       
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Stores Dropdown-->
<!-- Alert -->
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog"  >
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
            <button onclick="setfocus();" class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;">
              <div id="alert-active" class="activeOk"></div>OK</button>

            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;">
                <div id="alert-active" class="activeOk1"></div>OK</button>

            <button class="btn alertbt" name='OkBtn2' id="OkBtn2" style="display:none;margin-left: 90px;">
                <div id="alert-active" class="activeOk2"></div>OK</button>
        </div><!--btdiv-->
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
var limit   = 10;
var start   = 0;
var indexno = 0;
var action  = 'inactive';

function load_country_data(limit, start,indexno){

  var DOOB    = $("#DOOB").val();

  $.ajaxSetup({
    headers:{
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    url:'{{route("master",[70,"listing"])}}',
    method:"POST",
    data:{limit:limit, start:start,indexno:indexno,DOOB:DOOB,'OpeningStatus':'<?php echo $OpeningStatus;?>'},
    cache:false,
    success:function(data){
      $('.dataTables_empty').remove();
      $('#load_data_message').html('');
      $('#load_data').append(data);
     
      if(data == ''){
        $('#load_data_message').html("<br/><div>No Data Found</div>");
        action = 'active';
      }
      else{
        action = 'inactive';
      } 
      
    }
  });
}

if(action == 'inactive'){
  action = 'active';
  
  <?php if($TotalData > 0){?> 
    load_country_data(<?php echo $TotalData;?>, start,indexno);
  <?php }else{?> 
    load_country_data(limit, start,indexno);
  <?php }?>
}

$("#load_scroll").scroll(function(){

  if($("#load_scroll").scrollTop() + $("#load_scroll").height() > $("#load_data").height() && action == 'inactive'){
    action  = 'active';
    start   = start + limit;
    indexno = $("#example2 .participantRow").length;

    setTimeout(function(){
      $('#load_data_message').html("<div>Please wait...</div>");
      load_country_data(limit, start,indexno);
    }, 1000);
  }
});


function setfocus(){
    var focusid=$("#focusid").val();
    $("#"+focusid).focus();
    $("#closePopup").click();
} 

function validateForm(UserAction){

  $("#focusid").val('');
    var DOOB  = $.trim($("#DOOB").val());
    var txtcostcode  =   $.trim($("[id*=txtcostcode]").val());
    var txtdesc     =   $.trim($("[id*=txtdesc]").val());



    if(DOOB ===""){
        $("#focusid").val('DOOB');
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text('Please select Date of Opening Balance.');
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
        return false;
    }
    else{
        event.preventDefault();
        var ExistArray = []; 
        var allblank1 = [];  
        var allblank2 = []; 
        var allblank3 = []; 
        var allblank4 = []; 

        var texid1    = "";
        var texid2    = ""; 
        var texid3    = "";
        var  foundBalQty = false;

        $("[id*=OPEINING_BAL_QTY]").each(function(){
 
            if($.trim($(this).val()) ==="" ){
              allblank1.push('true');
            //  texid1 = $(this).attr('id');
            }else{
              allblank1.push('false');
            }

            if($.trim($(this).val()) !=="" && parseFloat($(this).val())>0 ){
              foundBalQty=true;
            }
        

        });

        if(jQuery.inArray("true", allblank1) !== -1){
            
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Please enter Opening Balance Qty.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }
          else if(foundBalQty==false){          
           
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").show();
            $("#AlertMessage").text('Opening Balance Qty must be greater than 0 of any record.');
            $("#alert").modal('show');
            $("#OkBtn").focus();
            highlighFocusBtn('activeOk');
            return false;
          }         
          else{
              $("#alert").modal('show');
              $("#AlertMessage").text('Do you want to save to record.');
              $("#YesBtn").data("funcname",UserAction);  
              $("#YesBtn").focus();
              highlighFocusBtn('activeYes');
          }

    }
  
}


  $('#example2').on('click','[id*="FORMDTLBTN"]',function(event){

        
        
          var id = $(this).attr('id');
          var id2 = $(this).parent().parent().find('[id*="OPEINING_BAL_QTY"]').attr("id");      
          var id3 = $(this).parent().parent().find('[id*="BATCHNOA"]').val();      
          var id4 = $(this).parent().parent().find('[id*="SRNOA"]').val();      

          var iobid_ref = $(this).parent().parent().find('[id*="HDNIOBID_REF"]').val();      
          var itemid_ref = $(this).parent().parent().find('[id*="ITEMID_REF"]').val();      
          var mainuomid_ref = $(this).parent().parent().find('[id*="MAINUOMID_REF"]').val();      
          var open_bal_qty = $(this).parent().parent().find('[id*="OPEINING_BAL_QTY"]').val();      
          var storedtls = $(this).parent().parent().find('[id*="HiddenRowId"]').val();      

          $('#hdn_FORMid').val(id);
          $('#hdn_FORMid2').val(id2);
          $('#hdn_FORMid3').val(id3);
          $('#hdn_FORMid4').val(id4);
        
          $("#FORMpopup").show();
          $("#tbody_FORM").html('');
          $("#tbody_FORM").html('Loading...');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          })
          $.ajax({
              url:'{{route("master",[70,"getforms"])}}',
              data:{'iobid_ref':iobid_ref,'itemid_ref':itemid_ref,'mainuomid_ref':mainuomid_ref,'open_bal_qty':open_bal_qty,'batchnoa':id3,'serialnoa':id4 ,'storedtls':storedtls},
              type:'POST',
              success:function(data) {
                $("#tbody_FORM").html(data);
                BindFORMEvents();
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $("#tbody_FORM").html('');
              },
          });

          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          })
          $.ajax({
              url:'{{route("master",[70,"getformsCount"])}}',
              data:{'iobid_ref':iobid_ref,'itemid_ref':itemid_ref,'mainuomid_ref':mainuomid_ref,'storedtls':storedtls },
              type:'POST',
              success:function(data) {
                $("#Row_Count9").val(data);
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $("#Row_Count9").val('1');
              },
          });

  });

      $("#FORM_closePopup").click(function(event){

        var allblank30=[];
        var allblank31=[];
        var allblank32=[];
        var allblank33=[];
        var status ='';
       

        $('#FORMTable2').find('.participantRow9').each(function(){

            status = $.trim($(this).find('[id*="strStatus"]').val());

            if($.trim($(this).find('[id*="strMOPENING_QTY"]').val()) != "" && $.trim($(this).find('[id*="txtSO_popup"]').val()) == ""){
              allblank30.push('false'); 
            }
            else if($.trim($(this).find('[id*="strSERIAL_NO"]').val()) != "" && $.trim($(this).find('[id*="txtSO_popup"]').val()) == ""){
              allblank30.push('false'); 
            }
            else{
              allblank30.push('true'); 
            }  

            if($.trim($(this).find("[id*=SERIALNOismandatory]").val())=="1"){

                  if($.trim($(this).find('[id*="txtSO_popup"]').val()) != "" && $.trim($(this).find('[id*="strSERIAL_NO"]').val()) == ""){
                    allblank31.push('false'); 
                  }
                  else{
                    allblank31.push('true'); 
                  }  

            }  

            if($.trim($(this).find("[id*=BATCHNOismandatory]").val())=="1"){
         
                  if($.trim($(this).find('[id*="txtSO_popup"]').val()) != "" && $.trim($(this).find('[id*="strBATCH_NO"]').val()) == ""){
                    allblank32.push('false'); 
                  }
                  else{
                    allblank32.push('true'); 
                  }  

            }  


             if($.trim($(this).find('[id*="txtSO_popup"]').val()) != "" && $.trim($(this).find('[id*="strMOPENING_QTY"]').val()) == ""){
              allblank33.push('false'); 
              }
              else{
                allblank33.push('true'); 
              }    

           
        });

        if(status=='A'){
          $("#FORMpopup").hide();
          return false;
        }

        if(jQuery.inArray("false", allblank30) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please select store.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            return false;
        
        }else if(jQuery.inArray("false", allblank31) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please enter the value for	Serial No.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            return false;

        }else if(jQuery.inArray("false", allblank32) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please enter the value for	Batch / Lot No.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            return false;

        }else if(jQuery.inArray("false", allblank33) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please enter the value for	Opening Qty (MU).');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk');
            return false;
        }


       

        var totopenqty = 0;
        var rowcounter = 0;
        var detailsArr = [];
        var rowdata = "";
        var uniqStoreIds = [];
       

        $('#FORMTable2').find('.participantRow9').each(function(){
            //$.trim($(this).find('[id*="strMOPENING_QTY"]').val();

           
            var openqty = $(this).find('[id*="strMOPENING_QTY"]').val();
            if( !isNaN(openqty) && openqty.length !== 0) {
                 totopenqty += parseFloat(openqty);
              }   

              //make json
              var ItemId      = $.trim($(this).find("[id*=strITEMID_REF]").val());
              var StoreId      = $.trim($(this).find("[id*=STID_REF]").val());
              var BatchNo      = $.trim($(this).find("[id*=strBATCH_NO]").val());
              var SerialNo      = $.trim($(this).find("[id*=strSERIAL_NO]").val());
              var strMOPENING_QTY      = $(this).find('[id*="strMOPENING_QTY"]').val();

              var BATCHNOismandatory     = $.trim($(this).find("[id*=BATCHNOismandatory]").val());
              var SERIALNOismandatory     = $.trim($(this).find("[id*=SERIALNOismandatory]").val());
              var StoreDesc      = $.trim($(this).find("[id*=txtSO_popup]").val());
              var strMUOMID_REF      = $.trim($(this).find("[id*=strMUOMID_REF]").val());
              var MumId      = $.trim($(this).find("[id*=MUOM_REF]").val());

              if(jQuery.inArray(StoreId, uniqStoreIds) == -1){
                uniqStoreIds.push(StoreId);
              }


           

              detailsArr[rowcounter]=[
                                      {
                                        'ITEMID_REF':ItemId,
                                        'STID_REF':StoreId,
                                        'txtSO_popup':StoreDesc,
                                        'BATCH_NO':BatchNo,
                                        'BATCHNOismandatory':BATCHNOismandatory,
                                        'SERIAL_NO':SerialNo,
                                        'SERIALNOismandatory':SERIALNOismandatory,
                                        'MOPENING_QTY':strMOPENING_QTY,
                                        'MUOMID_REF':MumId,
                                        'strMUOMID_REF':strMUOMID_REF,
                                        'actiontype':'user',
                                      }
                                     ];
              rowcounter = parseFloat(rowcounter)+1;

        });


        var txtid = $.trim($('#hdn_FORMid2').val());

        totopenqty = parseFloat(totopenqty).toFixed(3); 

        $("#"+txtid).val(totopenqty) ;
        $("#"+txtid).parent().parent().find('[id*="HiddenRowId_"]').val(JSON.stringify(detailsArr));
        $("#"+txtid).parent().parent().find('[id*="UniqueStoreIds_"]').val(JSON.stringify(uniqStoreIds));

        var openrate = $("#"+txtid).parent().parent().find('[id*="OPEINING_RATE"]').val();

        //if( !isNaN(openrate) && openrate.length !== 0 && parseFloat(openrate)>0) {
                 var newopenval = parseFloat( (parseFloat(totopenqty) * parseFloat(openrate))).toFixed(3);
                 $("#"+txtid).parent().parent().find('[id*="OPEINING_VALUE_"]').val(newopenval);
        //}else{

            //$("#"+txtid).parent().parent().find('[id*="OPEINING_VALUE_"]').val(totopenqty);
        //}

        var duplicatearr = [];
        var hidepop = true;

        $('#FORMTable2').find('.participantRow9').each(function(){
              var StoreId2      = $.trim($(this).find("[id*=STID_REF]").val());
              var BatchNo2      = $.trim($(this).find("[id*=strBATCH_NO]").val());
              var SerialNo2      = $.trim($(this).find("[id*=strSERIAL_NO]").val());

              var stodata = StoreId2+'-'+BatchNo2+'-'+SerialNo2;
              if(jQuery.inArray(stodata, duplicatearr) !== -1){
                $("#alert").modal('show');
                $("#AlertMessage").text('Duplicate data. Please check.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  
                $("#OkBtn1").show();
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk');
                hidepop = false;
                return false;
                
              }else{
                duplicatearr.push(stodata);
              }   

        });

        if(hidepop){
          $("#FORMpopup").hide();
        }

      });

      function BindFORMEvents()
      {
          $("[id*='strMOPENING_QTY']").ForceNumericOnly();
          $('#FORMTable2').on('focusout','[id*="strMOPENING_QTY"]',function(event){
           
            if(intRegex.test($(this).val())){
              $(this).val(($(this).val() +'.000'));
            }
          });   


         
      }
//------------------------
//------------------------
  //Store Data Dropdown
  let sotid = "#StoresTable2";
      let sotid2 = "#StoresTable";
      let soheaders = document.querySelectorAll(sotid2 + " th");

      // Sort the table element when clicking on the table headers
      soheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sotid, ".clssoid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function StoresCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesOrdercodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("StoresTable2");
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

  function StoresNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("SalesOrdernamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("StoresTable2");
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

  $('#FORMTable2').on('click','[id*="txtSO_popup"]',function(event){
                 
                  $("#tbody_SO").html('');
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                  })
                  $.ajax({
                      url:'{{route("master",[70,"getstores"])}}',
                      type:'POST',
                      success:function(data) {
                        $("#tbody_SO").html(data);
                        BindSO();
                      },
                      error:function(data){
                        console.log("Error: Something went wrong.");
                        $("#tbody_SO").html('');
                      },
                  }); 
        $("#SOpopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="STID_REF"]').attr('id');

        $('#hdn_soid').val(id);
        $('#hdn_soid2').val(id2);
      });

      $("#SOclosePopup").click(function(event){
        $("#SOpopup").hide();
      });
      function BindSO(){
        $(".clssoid").click(function(){
          var fieldid = $(this).attr('id');
          var txtval =    $("#txt"+fieldid+"").val();
          var texdesc =   $("#txt"+fieldid+"").data("desc");
          
          var txtid= $('#hdn_soid').val();
          var txt_id2= $('#hdn_soid2').val();

          $('#'+txtid).val(texdesc);
          $('#'+txt_id2).val(txtval);
          $("#SOpopup").hide();
          
          $("#SalesOrdercodesearch").val(''); 
          $("#SalesOrdernamesearch").val(''); 
          event.preventDefault();
        });
      }

      

  //Store Data Dropdown Ends
//---------------  

$(document).ready(function(e) {

   
    $("[id*='OPEINING_RATE']").ForceNumericOnly();

    

});


  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[70,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

  $("#OkBtn2").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn2").hide();
    window.location.href = '{{route("master",[70,"index"]) }}';
});

 var formDataMst = $( "#frm_mst_edit" );
     formDataMst.validate();

   
    //validate
    $( "#btnSave" ).click(function() {

        if(formDataMst.valid()){
           
            validateForm('fnSaveData');

        }

    });//btnSave

    
    //validate and approve
    $("#btnApprove").click(function() {
        
        if(formDataMst.valid()){
           
            validateForm('fnApproveData');

        }

    });//btnSave


    $("#YesBtn").click(function(){

      $("#alert").modal('hide');
      var customFnName = $("#YesBtn").data("funcname");
          window[customFnName]();

    }); //yes button

    
    window.fnSaveData = function (){
        
      $("#OkBtn1").hide();
      $("#OkBtn2").hide();
        //validate and save data
        event.preventDefault();

        var getDataForm = $("#frm_mst_edit");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[70,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                   if(data.exist=='norecord') {
                      $("#errorid").val('1'); 
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

                   }
                   if(data.save=='invalid') {
                      $("#errorid").val('1'); 
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();

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

                    $("#AlertMessage").text(data.msg);
                    $(".text-danger").hide();
                    $("#alert").modal('show');
                    $("#OkBtn2").show();
                    $("#OkBtn2").focus();
                    
                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });

    };// fnSaveData


    // save and approve 
    window.fnApproveData = function (){
        
      $("#OkBtn2").hide();
      $("#OkBtn1").hide();
        //validate and save data
        event.preventDefault();

        var getDataForm = $("#frm_mst_edit");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("mastermodify",[70,"singleapprove"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                   if(data.exist=='norecord') {
                      $("#errorid").val('1'); 
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();
                      $("#OkBtn1").hide();
                      $("#OkBtn2").hide();

                   }
                   if(data.save=='invalid') {
                      $("#errorid").val('1'); 
                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();
                      $("#OkBtn1").hide();
                      $("#OkBtn2").hide();
                      

                   }
                }
                if(data.success) {                   
                    console.log("succes MSG="+data.msg);
                    
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").hide();

                    $("#AlertMessage").text(data.msg);

                    $(".text-danger").hide();
                    
                    $("#alert").modal('show');
                    $("#OkBtn2").show();
                    $("#OkBtn2").focus();

                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });

    };// fnApproveData

    //no button
    $("#NoBtn").click(function(){

      $("#alert").modal('hide');
      var custFnName = $("#NoBtn").data("funcname");
          window[custFnName]();

    }); //no button

   
    $("#OkBtn").click(function(){

        $("#alert").modal('hide');

        $("#YesBtn").show();
        $("#NoBtn").show();
        $("#OkBtn").hide();
        $("#OkBtn2").hide();

    }); ///ok button

    $("#OkBtn1").click(function(){

        $("#alert").modal('hide');

        $("#YesBtn").show();
        $("#NoBtn").show();
        $("#OkBtn").hide();
        $("#OkBtn1").hide();
        $("#OkBtn2").hide();

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

   


   window.fnUndoYes = function (){
      
      //reload form
      window.location.reload();

   }//fnUndoYes


   window.fnUndoNo = function (){

     // $("#GROUPCODE").focus();

   }//fnUndoNo


    //
    function showError(pId,pVal){

      $("#"+pId+"").text(pVal);
      $("#"+pId+"").show();

    }  

    function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }

    $('#example2').on('change',"[id*='CHKDEACTIVATED']",function()
    {
      
      if ($(this).is(":checked") == false){
            $(this).parent().parent().find('[id*="DODEACTIVATED"]').val('');
        }
      event.preventDefault();
    });

    $("#FORMTable2").on('click', '.add', function() {

     

        var $tr = $(this).closest('table');
        var allTrs = $tr.find('.participantRow9').last();
        var lastTr = allTrs[allTrs.length-1];
        var $clone = $(lastTr).clone();
        $clone.find('td').each(function(){
            var el = $(this).find(':first-child');
            var id = el.attr('id') || null;
            if(id) {
                var i = id.substr(id.length-1);
                var prefix = id.substr(0, (id.length-1));
                el.attr('id', prefix+(+i+1));
            }
            var name = el.attr('name') || null;
            if(name) {
                var i = name.substr(name.length-1);
                var prefix1 = name.substr(0, (name.length-1));
                el.attr('name', prefix1+(+i+1));
            }
        });

         $clone.find('[id*="strSERIAL_NO"]').val('');  
         $clone.find('[id*="strBATCH_NO"]').val('');  
         $clone.find('[id*="txtSO_popup"]').val('');  
         $clone.find('[id*="STID_REF"]').val('');  
         $clone.find('[id*="strMOPENING_QTY"]').val('');  
        

        $tr.closest('table').append($clone);         
        var rowCount9 = $('#Row_Count9').val();
		    rowCount9 = parseInt(rowCount9)+1;
        $('#Row_Count9').val(rowCount9);
        $clone.find('.remove').removeAttr('disabled'); 

        $("[id*='strMOPENING_QTY']").ForceNumericOnly();
        
        event.preventDefault();
    });
    $("#FORMTable2").on('click', '.remove', function() {
        var rowCount9 = $(this).closest('table').find('.participantRow9').length;
        if (rowCount9 > 1) {
          $(this).closest('.participantRow9').remove();   
          rowCount9 = parseInt(rowCount9)-1;
          $('#Row_Count9').val(rowCount9);  
        } 
        if (rowCount9 <= 1) {   
          var deletebtn = $(this).closest('table').find('[id*="delbtn"]').attr('disabled',true);       
          return false;
          event.preventDefault();
        }
        event.preventDefault();
    });

    
    $('#FORMTable2').on('keyup','.three-digits',function(){
      if($(this).val().indexOf('.')!=-1){         
          if($(this).val().split(".")[1].length > 3){                
          $(this).val('');
          $("#alert").modal('show');
          $("#AlertMessage").text('Enter value till three decimal only.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          $("#OkBtn").hide();
          highlighFocusBtn('activeOk1');
          }  
        }            
        return this;
    });

    $('#example2').on('keyup','.five-digits',function(){
      if($(this).val().indexOf('.')!=-1){         
          if($(this).val().split(".")[1].length > 5){                
          $(this).val('');
          $("#alert").modal('show');
          $("#AlertMessage").text('Enter value till five decimal only.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          $("#OkBtn").hide();
          highlighFocusBtn('activeOk1');
          }  
        }            
        return this;
    });

    $("[id*='strMOPENING_QTY']").ForceNumericOnly();
    $("[id*='OPEINING_RATE']").ForceNumericOnly();

$('#example2').on('focusout',"[id*='OPEINING_RATE']",function(){
    var orate = $(this).val();
    if(orate==""){
      $(this).val('0.00000');
    }

    if(intRegex.test($(this).val())){
      $(this).val($(this).val()+'.00000')
    }

    var open_balqty = $(this).parent().parent().find('[id*="OPEINING_BAL_QTY"]').val();

    if( !isNaN(open_balqty) && open_balqty.length !== 0 && parseFloat(open_balqty)>0) {

      var newobal2 = parseFloat( (parseFloat(open_balqty) * parseFloat($(this).val()))).toFixed(3);
      $(this).parent().parent().find('[id*="OPEINING_VALUE_"]').val(newobal2);
    }else{

        $(this).parent().parent().find('[id*="OPEINING_VALUE_"]').val(open_balqty);
    }
});


$(document).ready(function(e){
  var today   = new Date(); 
  var sodate  = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
  $('#DOOB').attr('max',sodate);
});

function getDebitBal(id,value){

  var ROW_ID = id.split('_').pop();

  if(value > 0){
    $("#HDN_GLCRBALANCE_"+ROW_ID).prop("disabled", true);
  }
  else{
    $("#HDN_GLCRBALANCE_"+ROW_ID).prop("disabled", false);
  }

}
</script>



@endpush