@extends('layouts.app')
@section('content')
<div class="container-fluid topnav">
  <div class="row">
    <div class="col-lg-2"><a href="{{route('master',[$FormId,'index'])}}" class="btn singlebt">Prospect Master</a></div>
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
  <form id="master_form" method="POST"> 
    @CSRF
    <div class="inner-form">
      <div class="row">
        <div class="col-lg-1 pl"><p>Prospect Code *</p></div>
        <div class="col-lg-2 pl">
          @if(isset($objDD->SYSTEM_GRSR) && $objDD->SYSTEM_GRSR == "1")
          <input type="text" name="PCODE" id="PCODE" value="{{ isset($objDOCNO)?$objDOCNO:'' }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
          @elseif(isset($objDD->MANUAL_SR) && $objDD->MANUAL_SR == "1")
          <input type="text" name="PCODE" id="PCODE" class="form-control mandatory" maxlength="{{isset($objDD->MANUAL_MAXLENGTH)?$objDD->MANUAL_MAXLENGTH:''}}" autocomplete="off" style="text-transform:uppercase"  >
          @else
          <input type="text" name="PCODE" id="PCODE"  class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  >
          @endif
        </div>

        <div class="col-lg-1 pl"><p>Name *</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="NAME" id="NAME" class="form-control mandatory" autocomplete="off">                            
        </div>

        <div class="col-lg-1 pl"><p>Register Address 1 *</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="REGADDL1" id="REGADDL1" class="form-control mandatory" autocomplete="off">                            
        </div>

        <div class="col-lg-1 pl"><p>Register Address 2</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="REGADDL2" id="REGADDL2"  class="form-control mandatory" autocomplete="off">                            
        </div>
		  </div>

      <div class="row">
        <div class="col-lg-1 pl"><p>Country *</p></div>
        <div class="col-lg-2 pl">
          <select name="REGCTRYID_REF" id="REGCTRYID_REF" onchange="getstate(this.value,'REGSTID_REF','REGCITYID_REF','')" class="form-control mandatory">
            <option value="">Select</option>
            @foreach ($country as $val)
            <option value="{{$val->CTRYID}}">{{$val->CTRYCODE}} - {{$val->NAME}}</option>
            @endforeach
          </select>                           
        </div>

        <div class="col-lg-1 pl"><p>State *</p></div>
        <div class="col-lg-2 pl">
          <select name="REGSTID_REF" id="REGSTID_REF" onchange="getcity(this.value,'REGCITYID_REF','')" class="form-control mandatory">
            <option value="">Select</option>
          </select>                            
        </div>

        <div class="col-lg-1 pl"><p>City *</p></div>
        <div class="col-lg-2 pl">
          <select name="REGCITYID_REF" id="REGCITYID_REF" class="form-control mandatory">
            <option value="">Select</option>
          </select> 
        </div>

        <div class="col-lg-1 pl"><p>Pin-Code *</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="REGPIN" id="REGPIN" onkeypress="return onlyNumberKey(event)" maxlength='6'  class="form-control mandatory" autocomplete="off">                             
        </div>
      </div>

		  <div class="row">
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#DetailBlock" id="MAT_TAB" >Contact</a></li>
          <li><a data-toggle="tab" href="#SiteDetails">Point of Contacts</a></li>
        </ul>

        <div class="tab-content">
          <div id="DetailBlock" class="tab-pane fade in active">
            <div class="inner-form">
              <div class="row">
                <div class="col-lg-1 pl"><p>Corporate Address 1</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="CORPADDL1" id="CORPADDL1"  class="form-control mandatory" autocomplete="off">                            
                </div>

                <div class="col-lg-1 pl"><p>Corporate Address 2</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="CORPADDL2" id="CORPADDL2"  class="form-control mandatory" autocomplete="off">                            
                </div>

                <div class="col-lg-1 pl"><p>Country</p></div>
                <div class="col-lg-2 pl">
                  <select name="CORPCTRYID_REF" id="CORPCTRYID_REF" onchange="getstate(this.value,'CORPSTID_REF','CORPCITYID_REF','')" class="form-control mandatory">
                    <option value="">Select</option>
                    @foreach ($country as $val)
                    <option value="{{$val->CTRYID}}">{{$val->CTRYCODE}} - {{$val->NAME}}</option>
                    @endforeach
                  </select>                           
                </div>

                <div class="col-lg-1 pl"><p>State</p></div>
                <div class="col-lg-2 pl">
                  <select name="CORPSTID_REF" id="CORPSTID_REF" onchange="getcity(this.value,'CORPCITYID_REF','')" class="form-control mandatory">
                    <option value="">Select</option>
                  </select>                            
                </div>
              </div>

              <div class="row">
                <div class="col-lg-1 pl"><p>City</p></div>
                <div class="col-lg-2 pl">
                  <select name="CORPCITYID_REF" id="CORPCITYID_REF" class="form-control mandatory">
                    <option value="">Select</option>
                  </select> 
                </div>

                <div class="col-lg-1 pl"><p>Pin-Code</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="CORPPIN" id="CORPPIN" onkeypress="return onlyNumberKey(event)" maxlength='6'  class="form-control mandatory" autocomplete="off">                             
                </div>

                <div class="col-lg-1 pl"><p>E-Mail</p></div>
                <div class="col-lg-2 pl">
                  <input type="email" name="EMAILID" id="EMAILID" class="form-control mandatory" autocomplete="off">                            
                </div>

                <div class="col-lg-1 pl"><p>Website</p></div>
                <div class="col-lg-2 pl">
                  <input type="email" name="WEBSITE" id="WEBSITE"  class="form-control mandatory" autocomplete="off">                            
                </div>
		          </div>

              <div class="row">
                <div class="col-lg-1 pl"><p>Phone No</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="PHNO" id="PHNO"  onkeypress="return onlyNumberKey(event)" class="form-control mandatory" autocomplete="off">                           
                </div>

                <div class="col-lg-1 pl"><p>Mobile No</p></div>
                <div class="col-lg-2 pl">
                  <input type="text" name="MONO" id="MONO"  onkeypress="return onlyNumberKey(event)" class="form-control mandatory" autocomplete="off">                           
                </div>

                <div class="col-lg-1 pl"><p>Contact Person</p></div>
                <div class="col-lg-2 pl">
                  <input type="email" name="CPNAME" id="CPNAME" class="form-control mandatory" autocomplete="off">                            
                </div>

                <div class="col-lg-1 pl"><p>Skype</p></div>
                <div class="col-lg-2 pl">
                  <input type="email" name="SKYPEID" id="SKYPEID" class="form-control mandatory" autocomplete="off">                            
                </div>
		          </div>
            </div>
          </div>

          <div id="SiteDetails" class="tab-pane fade in ">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
              <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0">                      
                  <tr>                                                
                    <th rowspan="2" >Person Name</th>
                    <th rowspan="2" >Designation</th>
                    <th rowspan="2" >Mobile</th>
                    <th rowspan="2" >Email</th>
                    <th rowspan="2" >LL No</th>
                    <th rowspan="2" >Authority Level</th>
                    <th rowspan="2" >Birthday</th>
                    <th rowspan="2" >Action</th>
                  </tr>                        
                </thead>
                <tbody>
                  @if(isset($POC))
                  @foreach($POC as $key=>$val)
                  <tr class="participantRow">
                    <td><input  class="form-control" type="text" name="PNAME[]"     id="PNAME_{{$key}}"     value="{{isset($val->NAME)?$val->NAME:''}}" autocomplete="off"></td>
                    <td><input  class="form-control" type="text" name="DESIG[]"     id="DESIG_{{$key}}"     value="{{isset($val->DESIG)?$val->DESIG:''}}" autocomplete="off"></td>
                    <td><input  class="form-control" type="text" name="PMONO[]"     id="PMONO_{{$key}}"     value="{{isset($val->MONO)?$val->MONO:''}}" onkeypress="return onlyNumberKey(event)" autocomplete="off"></td>
                    <td><input  class="form-control" type="text" name="EMAIL[]"     id="EMAIL_{{$key}}"     value="{{isset($val->EMAIL)?$val->EMAIL:''}}" autocomplete="off"></td>
                    <td><input  class="form-control" type="text" name="LLNO[]"      id="LLNO_{{$key}}"      value="{{isset($val->LLNO)?$val->LLNO:''}}" onkeypress="return onlyNumberKey(event)" autocomplete="off"></td>
                    <td><input  class="form-control" type="text" name="AUTHLEVEL[]" id="AUTHLEVEL_{{$key}}" value="{{isset($val->AUTHLEVEL)?$val->AUTHLEVEL:''}}" autocomplete="off"></td>
                    <td><input  class="form-control" type="date" name="DOB[]"       id="DOB_{{$key}}"       value="{{isset($val->DOB)?$val->DOB:''}}" autocomplete="off"></td>
                    <td align="center">
                      <button class="btn add"     title="add"     data-toggle="tooltip" ><i class="fa fa-plus"></i></button>
                      <button class="btn remove"  title="Delete"  data-toggle="tooltip" disabled ><i class="fa fa-trash" ></i></button>
                    </td>
                  </tr>
                  @endforeach
                  @endif
                </tbody>
              </table>
            </div>	
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
@section('alert')
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
          <button class="btn alertbt" name='YesBtn' id="YesBtn" data-funcname="fnSaveData"><div id="alert-active" class="activeYes"></div>Yes</button>
          <button class="btn alertbt" name='NoBtn' id="NoBtn"   data-funcname="fnUndoNo" ><div id="alert-active" class="activeNo"></div>No</button>
          <button onclick="setfocus()"  class="btn alertbt" name='OkBtn' id="OkBtn" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk"></div>OK</button>
          <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="display:none;margin-left: 90px;"><div id="alert-active" class="activeOk1"></div>OK</button>
          <input type="hidden" id="focusid" > 
        </div>
		    <div class="cl"></div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('bottom-scripts')
<script>
$('#btnAdd').on('click', function() {
  var viewURL = '{{route("master",[$FormId,"add"])}}';
  window.location.href=viewURL;
});
  
$('#btnExit').on('click', function(){
  var viewURL = '{{route('home')}}';
  window.location.href=viewURL;
});

$("#YesBtn").click(function(){
  $("#alert").modal('hide');
  var customFnName = $("#YesBtn").data("funcname");
  window[customFnName]();
});

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
  window.location.href = "{{route('master',[$FormId,'index'])}}";
});
  
$("#OkBtn").click(function(){
  $("#alert").modal('hide');
});
  
window.fnUndoYes = function (){
  window.location.href = "{{route('master',[$FormId,'add'])}}";
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

$("#btnSave").click(function() {
  validateForm("fnSaveData",'save');
});

function validateForm(actionType,msg){

  var PCODE           = $.trim($("#PCODE").val());
  var NAME            = $.trim($("#NAME").val());
  var REGADDL1        = $.trim($("#REGADDL1").val());
  var REGCTRYID_REF   = $.trim($("#REGCTRYID_REF").val());
  var REGSTID_REF     = $.trim($("#REGSTID_REF").val());
  var REGCITYID_REF   = $.trim($("#REGCITYID_REF").val());
  var REGPIN          = $.trim($("#REGPIN").val());
  var CORPADDL1       = $.trim($("#CORPADDL1").val());
  var CORPCTRYID_REF  = $.trim($("#CORPCTRYID_REF").val());
  var CORPSTID_REF    = $.trim($("#CORPSTID_REF").val());
  var CORPCITYID_REF  = $.trim($("#CORPCITYID_REF").val());
  var CORPPIN         = $.trim($("#CORPPIN").val());

  if(PCODE ===""){
    alertMsg('PCODE','Please enter prospect code');
  }
  else if(NAME ===""){
    alertMsg('NAME','Please enter name');
  }
  else if(REGADDL1 ===""){
    alertMsg('REGADDL1','Please enter register address 1');
  }
  else if(REGCTRYID_REF ===""){
    alertMsg('REGCTRYID_REF','Please select country');
  }
  else if(REGSTID_REF ===""){
    alertMsg('REGSTID_REF','Please select state');
  }
  else if(REGCITYID_REF ===""){
    alertMsg('REGCITYID_REF','Please select city');
  }
  else if(REGPIN ===""){
    alertMsg('REGPIN','Please enter pin code');
  }
  else if(REGPIN.length < 6){
    alertMsg('REGPIN','Please enter correct pin code');
  }
  else if(CORPADDL1 !="" && CORPCTRYID_REF ===""){
    alertMsg('CORPCTRYID_REF','Please select country');
  }
  else if(CORPADDL1 !="" && CORPSTID_REF ===""){
    alertMsg('CORPSTID_REF','Please select state');
  }
  else if(CORPADDL1 !="" && CORPCITYID_REF ===""){
    alertMsg('CORPCITYID_REF','Please select city');
  }
  else if(CORPADDL1 !="" && CORPPIN ===""){
    alertMsg('CORPPIN','Please enter pin code');
  }  
  else if(CORPADDL1 !="" && CORPPIN.length < 6){
    alertMsg('CORPPIN','Please enter correct pin code');
  }   
  else{

    if(checkDuplicate() > 0){
      alertMsg('PCODE','This record is already exist');
    }
    else{
      $("#alert").modal('show');
      $("#AlertMessage").text('Do you want to '+msg+' to record.');
      $("#YesBtn").data("funcname",actionType);  
      $("#YesBtn").focus();
      highlighFocusBtn('activeYes');
    }

  }
}
  
function checkDuplicate(){

  var formData  = $("#master_form");
  var formData  = formData.serialize();

  var count = $.ajax({
                url:'{{route("master",[$FormId,"checkDuplicate"])}}',
                type:'POST',
                async: false,
                dataType: 'json',
                data:formData,
                done: function(response) {return response;}
		          }).responseText;

  return parseFloat(count);
}
  
      
  
window.fnSaveData = function (){
    event.preventDefault();
    var getDataForm = $("#master_form");
    var formData = getDataForm.serialize();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("master",[$FormId,"save"])}}',
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
        error: function (request, status, error) {
          $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").show();
          $("#AlertMessage").text(request.responseText);
          $("#alert").modal('show');
          $("#OkBtn").focus();
          highlighFocusBtn('activeOk');
        }
    });
  
}
  
$("#SiteDetails").on('click', '.add', function() {
  var $tr = $(this).closest('table');
  var allTrs = $tr.find('.participantRow').last();
  var lastTr = allTrs[allTrs.length-1];
  var $clone = $(lastTr).clone();

  $clone.find('td').each(function(){
    var el = $(this).find(':first-child');
    var id = el.attr('id') || null;
      if(id){
          var idLength = id.split('_').pop();
          var i = id.substr(id.length-idLength.length);
          var prefix = id.substr(0, (id.length-idLength.length));
          el.attr('id', prefix+(+i+1));
      }
  });

  $clone.find('input:text').val('');
  $clone.find('input:hidden').val('');
  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count').val(rowCount1);
  $clone.find('.remove').removeAttr('disabled'); 
  event.preventDefault();
});
  
$("#SiteDetails").on('click', '.remove', function() {
  var rowCount = $(this).closest('table').find('.participantRow').length;
  if (rowCount > 1) {
  $(this).closest('.participantRow').remove();     
  } 
  if (rowCount <= 1) { 
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('There is only 1 row. So cannot be remove.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk1');
        return false;
  }
  event.preventDefault();
});
  
function getstate(id,stxtid,ctxtid,rowid){

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });	

  $.ajax({
      url:'{{route("master",[$FormId,"getstate"])}}',
      type:'POST',
      data:{id:id,rowid:rowid},
      success:function(data) {
          $("#"+stxtid).html(data); 
          $("#"+ctxtid).html('<option value="">Select</option>');                
      },
      error: function (request, status, error) {
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();
        $("#AlertMessage").text(request.responseText);
        $("#alert").modal('show');
        $("#OkBtn").focus();
        highlighFocusBtn('activeOk');
      }
  });	

}


function getcity(id,txtid,rowid){

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });	

  $.ajax({
    url:'{{route("master",[$FormId,"getcity"])}}',
    type:'POST',
    data:{id:id,rowid:rowid},
    success:function(data) {
      $("#"+txtid).html(data);                 
    },
    error:function(data){
      console.log("Error: Something went wrong.");
    },
    error: function (request, status, error) {
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").show();
      $("#AlertMessage").text(request.responseText);
      $("#alert").modal('show');
      $("#OkBtn").focus();
      highlighFocusBtn('activeOk');
    }
  });

}

function onlyNumberKey(evt){
  var ASCIICode = (evt.which) ? evt.which : evt.keyCode
  if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
  return false;
  return true;
}
</script>  
@endpush
