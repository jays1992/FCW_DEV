
@extends('layouts.app')
@section('content')

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[526,'index'])}}" class="btn singlebt">Price List Standard Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveFormData" ><i class="fa fa-floppy-o"></i> Save</button>
                        <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
                        <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button class="btn topnavbt" id="btnApprove" {{($objRights->APPROVAL1||$objRights->APPROVAL2||$objRights->APPROVAL3||$objRights->APPROVAL4||$objRights->APPROVAL5) == 1 ? '' : 'disabled'}}><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                        <!-- <button  class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button> -->
                        <a href="{{route('home')}}" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
                </div>
            </div>
    </div><!--topnav--> 
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->

    <div class="container-fluid filter">     
         <form id="form_data" method="POST" onsubmit="return false;" > 
          @CSRF  
          {{isset($objMstResponse->PLID) ? method_field('PUT') : '' }}
                    <div class="inner-form">
                        <div class="row">                            
                            <div class="col-lg-2 pl"><p>Price Level Code </p></div>

                                <div class="col-lg-2 pl">
                                  <input type="text" name="PLCODE" id="PLCODE" value="{{ isset($objMstResponse->PLCODE)?$objMstResponse->PLCODE:'' }}" class="form-control mandatory" autocomplete="off" maxlength="20" tabindex="1" style="text-transform:uppercase" onkeypress="return AlphaNumaric(event,this)" />
                                      <span class="text-danger" id="ERROR_PLCODE"></span> 
                                </div>
                            <div class="col-lg-2 pl"><p>Price Level Name</p></div>
                                <div class="col-lg-2 pl">
                                  <input type="text" name="PLNAME" id="PLNAME" class="form-control mandatory" value="{{ isset($objMstResponse->PLNAME)?$objMstResponse->PLNAME:'' }}" maxlength="200" tabindex="2"  />
                                    <span class="text-danger" id="ERROR_PLNAME"></span> 
                                </div>
                            <div class="col-lg-2 pl"><p>Remarks</p></div>
                            <div class="col-lg-2 pl">
                                <input type="text" name="REMARKS" id="REMARKS" value="{{ isset($objMstResponse->REMARKS)?$objMstResponse->REMARKS:'' }}" autocomplete="off" class="form-control" maxlength="200"  >
                            </div>                            
                        </div>

                        <div class="row">
                            <div class="col-lg-2 pl"><p>De-Activated</p></div>
                              <div class="col-lg-2 pl pr">
                              <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0" value='{{$objMstResponse->DEACTIVATED == 1 ? 1 : 0}}'  tabindex="3" >
                              </div>
                              
                              <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                              <div class="col-lg-2 pl">
                              <div class="col-lg-8 pl">
                              <input type="date" name="DODEACTIVATED" class="form-control datepicker" id="DODEACTIVATED" placeholder="dd/mm/yyyy" 
                              {{$objMstResponse->DEACTIVATED == 1 ? "" : "disabled"}}
                                value="{{ (!is_null($objMstResponse->DODEACTIVATED) && $objMstResponse->DODEACTIVATED!='1900-01-01')? \Carbon\Carbon::parse($objMstResponse->DODEACTIVATED)->format('Y-m-d') : ''   }}" 
                                tabindex="4" disabled/>
                              </div>
                              </div>
                        </div>

                    </div>

                      <div class="container-fluid purchase-order-view">
                        <div class="row">
                            <ul class="nav nav-tabs">
                                <li><a data-toggle="tab" href="#udf">UDF</a></li>
                            </ul> 
                            <div class="tab-content">

                                <div id="udf" class="tab-pane fade active in">
                                    <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
                                        <table id="example4" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                                            <thead id="thead1"  style="position: sticky;top: 0">
                                            <tr >
                                                <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3" value="{{ $objCountUDF }}"></th>
                                                <th>Value / Comments</th>
                                            </tr>
                                            </thead>
                                            
                                            <tbody>
                                              @if(isset($objUdf) && !empty($objUdf))
                                              @foreach($objUdf as $udfkey => $udfrow)
                                              <tr  class="participantRow4">
                                                <td>
                                                  <input {{$ActionStatus}} name={{"udffie_popup_".$udfkey}} id={{"txtudffie_popup_".$udfkey}} value="{{$udfrow->LABEL}}" class="form-control @if ($udfrow->ISMANDATORY==1) mandatory @endif" autocomplete="off" maxlength="100" disabled/>
                                                </td>
                                
                                                <td hidden>
                                                  <input type="text" name='{{"udffie_".$udfkey}}' id='{{"hdnudffie_popup_".$udfkey}}' value="{{$udfrow->UDFID}}" class="form-control" maxlength="100" />
                                                </td>
                                
                                                <td hidden>
                                                  <input type="text" name={{"udffieismandatory_".$udfkey}} id={{"udffieismandatory_".$udfkey}} class="form-control" maxlength="100" value="{{$udfrow->ISMANDATORY}}" />
                                                </td>
                                
                                                <td id="{{"tdinputid_".$udfkey}}">
                                                  @php
                              
                                                      $dynamicid = "udfvalue_".$udfkey;
                                                      $chkvaltype = strtolower($udfrow->VALUETYPE); 

                                                    if($chkvaltype=='date'){

                                                      $strinp = '<input '.$ActionStatus.' type="date" placeholder="dd/mm/yyyy" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'" /> ';       

                                                    }else if($chkvaltype=='time'){

                                                        $strinp= '<input '.$ActionStatus.' type="time" placeholder="h:i" name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control"  value="'.$udfrow->UDF_VALUE.'"/> ';

                                                    }else if($chkvaltype=='numeric'){
                                                    $strinp = '<input'.$ActionStatus.' type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'"/> ';

                                                    }else if($chkvaltype=='text'){

                                                    $strinp = '<input '.$ActionStatus.' type="text" name="'.$dynamicid. '" id="'.$dynamicid.'" class="form-control" value="'.$udfrow->UDF_VALUE.'"/> ';

                                                    }else if($chkvaltype=='boolean'){
                                                        $boolval = ''; 
                                                        if($udfrow->UDF_VALUE=='on' || $udfrow->UDF_VALUE=='1' ){
                                                          $boolval="checked";
                                                        }
                                                        $strinp = '<input '.$ActionStatus.' type="checkbox" name="'.$dynamicid. '" id="'.$dynamicid.'" class=""  '.$boolval.' /> ';

                                                    }else if($chkvaltype=='combobox'){
                                                      $strinp='';
                                                    $txtoptscombo =   strtolower($udfrow->DESCRIPTIONS); ;
                                                    $strarray =  explode(',',$txtoptscombo);
                                                    $opts = '';
                                                    $chked='';
                                                      for ($i = 0; $i < count($strarray); $i++) {
                                                        $chked='';
                                                        if($strarray[$i]==$udfrow->UDF_VALUE){
                                                          $chked='selected="selected"';
                                                        }
                                                        $opts = $opts.'<option value="'.$strarray[$i].'"'.$chked.'  >'.$strarray[$i].'</option> ';
                                                      }

                                                      $strinp = '<select '.$ActionStatus.' name="'.$dynamicid.'" id="'.$dynamicid.'" class="form-control" >'.$opts.'</select>' ;


                                                    }
                                                    echo $strinp;
                                                    @endphp
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
<!-- Alert -->
@endsection

@push('bottom-scripts')
<script>

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

//UDF Tab Starts
//------------------------

let udftid = "#UDFSOIDTable2";
      let udftid2 = "#UDFSOIDTable";
      let udfheaders = document.querySelectorAll(udftid2 + " th");

      // Sort the table element when clicking on the table headers
      udfheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(udftid, ".clsudfsoid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function UDFSOIDCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("UDFSOIDcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("UDFSOIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[0];
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

  function UDFSOIDNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("UDFSOIDnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("UDFSOIDTable2");
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

$("#udfsoid_closePopup").on("click",function(event){ 
     $("#udfsoidpopup").hide();
});

$('.clsudfsoid').dblclick(function(){
    
        var id = $(this).attr('id');
        var txtid =    $("#txt"+id+"").val();
        var txtname =   $("#txt"+id+"").data("desc");
        var fieldid2 = $(this).find('[id*="udfvalue"]').attr('id');
        var txtvaluetype = $.trim($(this).find('[id*="udfvalue"]').text().trim());
        var txtismandatory =  $("#txt"+fieldid2+"").val();
        var txtdescription =  $("#txt"+fieldid2+"").data("desc");
        
        var txtcol = $('#hdn_UDFSOID').val();
        $("#"+txtcol).val(txtname);
        $("#"+txtcol).parent().parent().find("[id*='UDFSOID_REF']").val(txtid);
        $("#"+txtcol).parent().parent().find("[id*='UDFismandatory']").val(txtismandatory);
        
        var txt_id4 = $("#"+txtcol).parent().parent().find("[id*='udfinputid']").attr('id');  //<td> id 

        var strdyn = txt_id4.split('_');
        var lastele =   strdyn[strdyn.length-1];

        var dynamicid = "udfvalue_"+lastele;

        var chkvaltype2 =  txtvaluetype.toLowerCase();
        var strinp = '';

        if(chkvaltype2=='date'){

          strinp = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';       

        }else if(chkvaltype2=='time'){
          strinp= '<input type="time" placeholder="h:i" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';

        }else if(chkvaltype2=='numeric'){
          strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';

        }else if(chkvaltype2=='text'){

          strinp = '<input type="text" name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" /> ';
        
        }else if(chkvaltype2=='boolean'){

          strinp = '<input type="checkbox" name="'+dynamicid+ '" id="'+dynamicid+'" class="" /> ';
        
        }else if(chkvaltype2=='combobox'){
          if(txtdescription !== undefined)
              {
                var strarray = txtdescription.split(',');
                
                var opts = '';

                for (var i = 0; i < strarray.length; i++) {
                  opts = opts + '<option value="'+strarray[i]+'">'+strarray[i]+'</option> ';
                }

                strinp = '<select name="'+dynamicid+ '" id="'+dynamicid+'" class="form-control" required>'+opts+'</select>' ;
              }
        }

        $('#'+txt_id4).html('');  
        $('#'+txt_id4).html(strinp);   //set dynamic input

        $("#udfsoidpopup").hide();
        $("#UDFSOIDcodesearch").val(''); 
        $("#UDFSOIDnamesearch").val(''); 
        UDFSOIDCodeFunction();
        event.preventDefault();
            
 });
 
//UDF Tab Ends
//------------------------

    $("#example6").on('click', '.add', function() {
        var $tr = $(this).closest('table');
        var allTrs = $tr.find('.participantRow6').last();
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
            var name = el.attr('name') || null;
          if(name){
            var nameLength = name.split('_').pop();
            var i = name.substr(name.length-nameLength.length);
            var prefix1 = name.substr(0, (name.length-nameLength.length));
            el.attr('name', prefix1+(+i+1));
          }
        });

        $clone.find('input:text').val('');
        $tr.closest('table').append($clone);         
        var rowCount5 = $('#Row_Count5').val();
        rowCount5 = parseInt(rowCount5)+1;
        $('#Row_Count5').val(rowCount5);
        $clone.find('.remove').removeAttr('disabled'); 
        
        event.preventDefault();
    });
    $("#example6").on('click', '.remove', function() {
        var rowCount5 = $(this).closest('table').find('.participantRow6').length;
        if (rowCount5 > 1) {
          $(this).closest('.participantRow6').remove();     
          var rowCount5 = $('#Row_Count5').val();
          rowCount5 = parseInt(rowCount5)-1;
          $('#Row_Count5').val(rowCount5);
        } 
        if (rowCount5 <= 1) {          
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").hide();
              $("#OkBtn1").show();
              $("#AlertMessage").text('There is only 1 row. So cannot be remove.');
              $("#alert").modal('show');
              $("#OkBtn1").focus();
              highlighFocusBtn('activeOk1');
              return false;
              event.preventDefault();
        }
        event.preventDefault();
    });

$(document).ready(function(e) {
    

    $('#btnAdd').on('click', function() {
        var viewURL = '{{route("master",[526,"add"])}}';
                  window.location.href=viewURL;
    });
    $('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });

//SO Validity to Date Check

    $("#btnUndo").on("click", function() {
        $("#AlertMessage").text("Do you want to erase entered information in this record?");
        $("#alert").modal('show');

        $("#YesBtn").data("funcname","fnUndoYes");
        $("#YesBtn").show();

        $("#NoBtn").data("funcname","fnUndoNo");
        $("#NoBtn").show();
        
        $("#OkBtn").hide();
        $("#NoBtn").focus();
    });

    window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('master',[526,'add'])}}";

   }//fnUndoYes

   window.fnUndoNo = function (){

   }//fnUndoNo

   window.fnUndoNo = function (){
      $("#PLCODE").focus();
   }//fnUndoNo

   $("#SOFC").change(function() {
      if ($(this).is(":checked") == true){
          $(this).parent().parent().find('#txtCRID_popup').removeAttr('disabled');
          $(this).parent().parent().find('#txtCRID_popup').prop('readonly','true');
          event.preventDefault();
      }
      else
      {
          $(this).parent().parent().find('#txtCRID_popup').prop('disabled','true');
          $(this).parent().parent().find('#txtCRID_popup').removeAttr('readonly');
          $(this).parent().parent().find('#txtCRID_popup').val('');
          $(this).parent().parent().find('#CRID_REF').val('');
          $(this).parent().parent().find('#CONVFACT').val('');
          event.preventDefault();
      }
  });

});
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

});

var formTrans = $("#form_data");
formTrans.validate();

$( "#btnSaveFormData" ).click(function() {
  //var formTrans = $("#form_data");
  if(formTrans.valid()){
    validateForm();
  }
});

$(document).ready(function(){
  $("#btnSaveFormData").click(function(){
    $("#PLNAME-error").fadeOut(1500);
  });
});


$(function() { 
    //ready

    var last_dactdt =  <?php echo json_encode($objMstResponse->DODEACTIVATED); ?>;

    var today = new Date(); 
    var curdate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
    // $('#PERIOD_FRDT').attr('min',last_pfromdt);
    $('#PERIOD_TODT').attr('min',curdate);
    $('#DODEACTIVATED').attr('min',last_dactdt);

    $('input[type=checkbox][name=DEACTIVATED]').change(function() {
        if ($(this).prop("checked")) {
          $(this).val('1');
          $('#DODEACTIVATED').removeAttr('disabled');
        }
        else {
          $(this).val('0');
          $('#DODEACTIVATED').prop('disabled', true);
          $('#DODEACTIVATED').val('dd/mm/yyyy');
          
        }
    });


    $('input').keypress(function(e) {
      if (e.which == '13') {
        e.preventDefault();        
      }
    });

    //---------------------------  
});

function commonvalidation(){
    event.preventDefault();
    if( $("#deactive-checkbox_0").is(":checked")  == true && $.trim( $("#DODEACTIVATED").val()) == '' ) {
      
      $("#alert").modal('show');
      $("#AlertMessage").text('Please select Date of De-Activated.');
      $("#YesBtn").hide(); 
      $("#NoBtn").hide();  
      $("#OkBtn1").show();
      $("#OkBtn1").focus();
      highlighFocusBtn('activeOk1'); 
      return false;
}
return true;
}

function validateForm(){
 
    $("#FocusId").val('');
    // var BILLTO          =   $.trim($("#BILLTO").val());
    // var SHIPTO          =   $.trim($("#SHIPTO").val());
    
   
    // if(PLCODE ===""){
    //     $("#YesBtn").hide();
    //     $("#NoBtn").hide();
    //     $("#OkBtn1").show();
    //     $("#AlertMessage").text('Please select PLCODE.');
    //     $("#alert").modal('show');
    //     $("#OkBtn1").focus();
    //     return false;
    // }    
    if(PLNAME ===""){
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Please select PLNAME.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        return false;
    }    
    // else{
    //     event.preventDefault();

    //     var RackArray = []; 
    //     var allblank = [];
    //     var allblank2 = [];
    //     var allblank3 = [];
    //     var allblank4 = [];
    //     var allblank5 = [];
    //     var allblank6 = [];
    //     var allblank7 = [];
    //     var allblank8 = [];
    //     var allblank9 = [];
    //     var allblank10 = [];
    //     var allblank11 = [];
    //     var allblank12 = [];
    //     var allblank13 = [];
    //     var allblank15 = [];
    //     var allblank16 = [];
    //     var allblank17 = [];

      
    //           $("[id*=txtudffie_popup]").each(function(){
    //               if($.trim($(this).val())!="")
    //               {
    //                   if($.trim($(this).parent().parent().find('[id*="udffieismandatory"]').val()) == "1")
    //                     {
    //                       if($.trim($(this).parent().parent().find('[id*="udfvalue"]').val()) != "")
    //                         {
    //                           allblank9.push('true');
    //                         }
    //                       else
    //                         {
    //                           allblank9.push('false');
    //                         }
    //                     }
                      
    //               }
                  
    //           });

    //         $('#example6').find('.participantRow6').each(function(){
    //               if($.trim($(this).find("[id*=PAY_DAYS]").val())!="")
    //                 {
    //                   if($.trim($(this).find('[id*="DUE"]').val()) != "")
    //                   {
    //                     allblank12.push('true');
    //                   }
    //                   else
    //                   {
    //                     allblank12.push('false');
    //                   }       
    //                 }                
    //         });
            
            // else if(jQuery.inArray("true", allblank17) !== -1){
            //     $("#alert").modal('show');
            //     $("#AlertMessage").text('VQ Qty should be greater then zero.');
            //     $("#YesBtn").hide(); 
            //     $("#NoBtn").hide();  
            //     $("#OkBtn1").show();
            //     $("#OkBtn1").focus();
            //     highlighFocusBtn('activeOk');
            //     return false;
            // }
            // else if(jQuery.inArray("false", allblank16) !== -1){
            //     $("#alert").modal('show');
            //     $("#AlertMessage").text('VQ Qty should not greater then RFQ Qty.');
            //     $("#YesBtn").hide(); 
            //     $("#NoBtn").hide();  
            //     $("#OkBtn1").show();
            //     $("#OkBtn1").focus();
            //     highlighFocusBtn('activeOk');
            //     return false;
            // }                    
           
            // else if(jQuery.inArray("false", allblank6) !== -1){
            //     $("#alert").modal('show');
            //     $("#AlertMessage").text('Please select Terms & Condition Description in T&C Tab.');
            //     $("#YesBtn").hide(); 
            //     $("#NoBtn").hide();  
            //     $("#OkBtn1").show();
            //     $("#OkBtn1").focus();
            //     highlighFocusBtn('activeOk');
            //     return false;
            // }
            // else if(jQuery.inArray("false", allblank7) !== -1){
            //     $("#alert").modal('show');
            //     $("#AlertMessage").text('Please enter Value / Comment in T&C Tab.');
            //     $("#YesBtn").hide(); 
            //     $("#NoBtn").hide();  
            //     $("#OkBtn1").show();
            //     $("#OkBtn1").focus();
            //     highlighFocusBtn('activeOk');
            //     return false;
            // }
            // else if(jQuery.inArray("false", allblank9) !== -1){
            //     $("#alert").modal('show');
            //     $("#AlertMessage").text('Please enter  Value / Comment in UDF Tab.');
            //     $("#YesBtn").hide(); 
            //     $("#NoBtn").hide();  
            //     $("#OkBtn1").show();
            //     $("#OkBtn1").focus();
            //     highlighFocusBtn('activeOk');
            //     return false;
            // }
            // else if(jQuery.inArray("false", allblank10) !== -1){
            //     $("#alert").modal('show');
            //     $("#AlertMessage").text('Please select Calculation Component in Calculation Template Tab.');
            //     $("#YesBtn").hide(); 
            //     $("#NoBtn").hide();  
            //     $("#OkBtn1").show();
            //     $("#OkBtn1").focus();
            //     highlighFocusBtn('activeOk');
            //     return false;
            // }
            // else if(jQuery.inArray("false", allblank11) !== -1){
            //     $("#alert").modal('show');
            //     $("#AlertMessage").text('Please Enter GST Rate / Value in Calculation Template Tab.');
            //     $("#YesBtn").hide(); 
            //     $("#NoBtn").hide();  
            //     $("#OkBtn1").show();
            //     $("#OkBtn1").focus();
            //     highlighFocusBtn('activeOk');
            //     return false;
            // }
            // else if(jQuery.inArray("false", allblank12) !== -1){
            //     $("#alert").modal('show');
            //     $("#AlertMessage").text('Please Enter Due % in Payment Slabs Tab.');
            //     $("#YesBtn").hide(); 
            //     $("#NoBtn").hide();  
            //     $("#OkBtn1").show();
            //     $("#OkBtn1").focus();
            //     highlighFocusBtn('activeOk');
            //     return false;
            // }
            else{

                $("#alert").modal('show');
                $("#AlertMessage").text('Do you want to save to record.');
                $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                $("#OkBtn1").hide();
                $("#OkBtn").hide();
                $("#YesBtn").show();
                $("#NoBtn").show();
                $("#YesBtn").focus();
                highlighFocusBtn('activeYes');
            }
    }


$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button


/* form validation */
var formMst = $( "#form_data" );
  formMst.validate();
  
//btnApprove
$( "#btnApprove" ).click(function() {

  if(formMst.valid()){
      if(commonvalidation()){   
          $("#alert").modal('show');
          $("#AlertMessage").text('Do you want to save and approve this record.');
          $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name
          $("#OkBtn1").hide();
          $("#OkBtn").hide();
          $("#YesBtn").show();
          $("#NoBtn").show();
          $("#YesBtn").focus();
          highlighFocusBtn('activeYes');

          return false;
      }    
  } //if          
});//btnApprove


//********-----
window.fnApproveData = function (){

    //validate and save data
    var currentForm = $("#form_data");
    var formData = currentForm.serialize();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url:'{{route("master",[526,"singleapprove"])}}',
        type:'POST',
        data:formData,
        success:function(data) {
          
            if(data.errors) {
                $(".text-danger").hide();

                console.log("error MSG="+data.msg);

                if(data.resp=='duplicate') {

                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text(data.msg);
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  highlighFocusBtn('activeOk1');
                  return false;

                }

              if(data.save=='invalid') {

                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text(data.msg);
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
              }

              if(data.form=='invalid') {

                  $("#YesBtn").hide();
                  $("#NoBtn").hide();
                  $("#OkBtn").hide();
                  $("#OkBtn1").show();
                  $("#AlertMessage").text("Invalid form data please required fields.");
                  $("#alert").modal('show');
                  $("#OkBtn1").focus();
                  return false;
              }
              
            }
            
            if(data.success) {                   

                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn1").hide();
                $("#OkBtn").show();  
                highlighFocusBtn('activeOk');
                
                $("#AlertMessage").text(data.msg);
                $("#alert").modal('show');

                $("#OkBtn").focus();
                $(".text-danger").hide();
            }
            
        },
        error:function(data){
          console.log("Error: Something went wrong.");
        },
    });


}// fnApproveata

//********-----

// fnUpdateDataStart
  window.fnSaveData = function (){
    
        event.preventDefault();

        var currentForm = $("#form_data");
        var formData = currentForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[526,"update"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
              
                if(data.errors) {
                    $(".text-danger").hide();

                    console.log("error MSG="+data.msg);

                    if(data.resp=='duplicate') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").hide();
                      $("#OkBtn1").show();
                      $("#AlertMessage").text(data.msg);
                      $("#alert").modal('show');
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;

                    }

                  if(data.save=='invalid') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").hide();
                      $("#OkBtn1").show();
                      $("#AlertMessage").text(data.msg);
                      $("#alert").modal('show');
                      $("#OkBtn1").focus();
                      return false;
                  }

                  if(data.form=='invalid') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").hide();
                      $("#OkBtn1").show();
                      $("#AlertMessage").text("Invalid form data please required fields.");
                      $("#alert").modal('show');
                      $("#OkBtn1").focus();
                      return false;
                  }
                  
                }
                
                if(data.success) {                   

                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn1").hide();
                    $("#OkBtn").show();  
                    highlighFocusBtn('activeOk');
                    
                    $("#AlertMessage").text("Record successfully updated.");
                    $("#alert").modal('show');

                    $("#OkBtn").focus();
                    $(".text-danger").hide();
                }
                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
        
      
  } // fnUpdateDataEnd

//no button
$("#NoBtn").click(function(){
    $("#alert").modal('hide');
});

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("master",[526,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
   
    $(".text-danger").hide();
});


    $("#PLNAME").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_PLNAME").hide();
        validateSingleElemnet("PLNAME");
    });

    $( "#PLNAME" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

//
function showError(pId,pVal){
    $("#"+pId+"").text(pVal);
    $("#"+pId+"").show();
}
function getFocus(){
    var FocusId=$("#FocusId").val();
    $("#"+FocusId).focus();
    $("#closePopup").click();
}
function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }

function showAlert(msg){
  $("#alert").modal('show');
  $("#AlertMessage").text(msg);
  $("#YesBtn").hide(); 
  $("#NoBtn").hide();  
  $("#OkBtn1").show();
  $("#OkBtn1").focus();
  highlighFocusBtn('activeOk');
}

function isNumberDecimalKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
}

  function doCalculation(){
    $(".blurRate").blur();

  }

  function showAlert(msg){
    $("#alert").modal('show');
    $("#AlertMessage").text(msg);
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn1").show();
    $("#OkBtn1").focus();
    highlighFocusBtn('activeOk');
    return false;
  }

function showSelectedCheck(hidden_value,selectAll){

  var divid ="";

  if(hidden_value !=""){

      var all_location_id = document.querySelectorAll('input[name="'+selectAll+'[]"]');
      
      for(var x = 0, l = all_location_id.length; x < l;  x++){
      
          var checkid=all_location_id[x].id;
          var checkval=all_location_id[x].value;
      
          if(hidden_value == checkval){
          divid = checkid;
          }

          $("#"+checkid).prop('checked', false);
          
      }
  }

  if(divid !=""){
      $("#"+divid).prop('checked', true);
  }
}
</script>

@endpush