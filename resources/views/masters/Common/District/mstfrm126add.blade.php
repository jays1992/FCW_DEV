@extends('layouts.app')
@section('content')


    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[126,'index'])}}" class="btn singlebt">District Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                <button class="btn topnavbt" id="btnAdd" disabled="disabled" ><i class="fa fa-plus"></i> Add</button>
                <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                <button class="btn topnavbt" id="btnSave"  tabindex="5"  ><i class="fa fa-save"></i> Save</button>
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
         <form id="frm_mst_add" method="POST"  > 
          @CSRF
          <div class="inner-form">

              <div class="row">
                  <div class="col-lg-2 pl"><p>Country Code</p></div>
                  <div class="col-lg-3 pl">
                        <input type="text" name="CTRYID_REF_POPUP" id="CTRYID_REF_POPUP" class="form-control mandatory" readonly tabindex="1" />
                        <input type="hidden" name="CTRYID_REF" id="CTRYID_REF" />
                        <span class="text-danger" id="ERROR_CTRYID_REF"></span>
                  </div>

                  <div class="col-lg-2 pl col-md-offset-1"><p>Country Name</p></div>
                  <div class="col-lg-3 pl">
                      <input type="text" name="Couontry_Name" id="Couontry_Name" class="form-control" readonly  />
                  </div>
              </div>

              <div class="row">
                    <div class="col-lg-2 pl"><p>State Code</p></div>
                    <div class="col-lg-3 pl">
                          <input type="text" name="STID_REF_POPUP" id="STID_REF_POPUP" class="form-control mandatory" readonly tabindex="2" />
                          <input type="hidden" name="STID_REF" id="STID_REF" />
                          <span class="text-danger" id="ERROR_STID_REF"></span>
                    </div>

                    <div class="col-lg-2 pl col-md-offset-1"><p>State Name</p></div>
                    <div class="col-lg-3 pl">
                        <input type="text" name="State_Name" id="State_Name" class="form-control" readonly  />  
                    </div>
                </div>

                <div class="row">
                  <div class="col-lg-2 pl"><p>City Code</p></div>
                  <div class="col-lg-3 pl">
                        <input type="text" name="CITYID_REF_POPUP" id="CITYID_REF_POPUP" class="form-control mandatory" readonly tabindex="1" />
                        <input type="hidden" name="CITYID_REF" id="CITYID_REF" />
                        <span class="text-danger" id="ERROR_CITYID_REF"></span>
                  </div>

                  <div class="col-lg-2 pl col-md-offset-1"><p>City Name</p></div>
                  <div class="col-lg-3 pl">
                      <input type="text" name="City_Name" id="City_Name" class="form-control" readonly  />
                  </div>
              </div>
              
                <div class="row">
                    <div class="col-lg-2 pl"><p>District Code</p></div>
                    <div class="col-lg-3 pl">
                          @if(!empty($objDD))
                            @if($objDD->SYSTEM_GRSR == "1")
                                <input type="text" name="DISTCODE" id="DISTCODE" value="{{ $objDOCNO }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase"  autofocus >
                            @endif
                            @if($objDD->MANUAL_SR == "1")
                                <input type="text" name="DISTCODE" id="DISTCODE" value="{{ old('DISTCODE') }}" class="form-control mandatory" minlength="3" maxlength="{{$objDD->MANUAL_MAXLENGTH}}" tabindex="3" autocomplete="off" style="text-transform:uppercase" autofocus >
                            @endif
                          @endif  
                          {{-- <input type="text" name="DISTCODE" id="DISTCODE" value="{{ old('DISTCODE') }}" class="form-control mandatory" autocomplete="off" maxlength="10" tabindex="3" style="text-transform:uppercase" /> --}}
                          <span class="text-danger" id="ERROR_DISTCODE"></span>
                    </div>

                    <div class="col-lg-2 pl col-md-offset-1"><p>District Name</p></div>
                    <div class="col-lg-3 pl">
                      <input type="text" name="NAME" id="NAME" class="form-control mandatory" value="{{ old('NAME') }}" maxlength="50" tabindex="4"  />
                      <span class="text-danger" id="ERROR_NAME"></span> 
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
            <td  class="ROW2"  style="width: 40%"><input type="text" id="country_codesearch" autocomplete="off"  class="form-control" onkeyup="searchCountryCode()" ></td>
            <td  class="ROW3"  style="width: 40%"><input type="text" id="country_namesearch"   class="form-control" onkeyup="searchCountryName()" autocomplete="off"></td>
          </tr>
        </tbody>
      </table>


      <table id="country_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="country_body">
        @foreach ($objCountryList as $index=>$CountryList)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_CTRYID_REF[]"  id="ctryidref_{{$index}}" class="cls_ctryidref" value="{{ $CountryList->CTRYID }}" ></td>
          <td  class="ROW2" style="width: 39%">{{ $CountryList->CTRYCODE }}
          <input type="hidden" id="txtctryidref_{{$index}}" data-desc="{{ $CountryList->CTRYCODE }}" data-descname="{{ $CountryList->NAME }}" value="{{ $CountryList-> CTRYID }}"/>
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
            <td class="ROW1" style="width: 12%"><span class="check_th">&#10004;</span></td>
            <td  class="ROW2" style="width: 39%"><input type="text" clsss="form-control" id="state_codesearch" onkeyup="searchStateCode()" /></td>
            <td  class="ROW3"  style="width: 39%"><input type="text" clsss="form-control" id="state_namesearch" onkeyup="searchStateName()" /></td>
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
          <td  class="ROW2" style="width: 40%" ><input type="text" class="form-control" autocomplete="off" id="city_codesearch" onkeyup="searchCityCode()"></td>
          <td  class="ROW3" style="width: 40%"><input type="text" class="form-control" autocomplete="off" id="city_namesearch" onkeyup="searchCityName()"></td>
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


<!-- Alert -->
@endsection
<!-- btnSave -->

@push('bottom-scripts')
<script>

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
  var id          =   $(this).attr('id');
  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#Couontry_Name").val(texdescname);
  $("#CTRYID_REF_POPUP").val(texdesc);
  $("#CTRYID_REF").val(txtval);

  getCountryWiseState(txtval);
  
  $("#CTRYID_REF_POPUP").blur(); 
  $("#STID_REF_POPUP").focus(); 
  
  $("#ctryidref_popup").hide();
  searchCountryCode();
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
    $("#state_body").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[126,"getCountryWiseState"])}}',
        type:'POST',
        data:{CTRYID_REF:CTRYID_REF},
        success:function(data) {
          
          $("#State_Name").val('');
          $("#STID_REF_POPUP").val('');
          $("#STID_REF").val('');

          $("#City_Name").val('');
          $("#CITYID_REF_POPUP").val('');
          $("#CITYID_REF").val('');
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

    var id          =   $(this).attr('id');
    var txtval      =   $("#txt"+id+"").val();
    var texdesc     =   $("#txt"+id+"").data("desc");
    var texdescname =   $("#txt"+id+"").data("descname");

    $("#State_Name").val(texdescname);
    $("#STID_REF_POPUP").val(texdesc);
    $("#STID_REF").val(txtval);

    var CTRYID_REF	=	$("#CTRYID_REF").val();
	  getStateWiseCity(CTRYID_REF,txtval);
	
	  $("#STID_REF_POPUP").blur(); 
	  $("#DISTCODE").focus(); 
	
    $("#stidref_popup").hide();
    searchStateCode();
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

// Citiy popup function

function getStateWiseCity(CTRYID_REF,STID_REF){
    $("#city_body").html('');
		$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
		
    $.ajax({
        url:'{{route("master",[126,"getStateWiseCity"])}}',
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
		var texdesc =   $("#txt"+id+"").data("desc");
    var texdescname =   $("#txt"+id+"").data("descname");

    $("#City_Name").val(texdescname);
		$("#CITYID_REF_POPUP").val(texdesc);
    $("#CITYID_REF").val(txtval);
    	
    $("#CITYID_REF_POPUP").blur(); 
	  $("#DISTCODE").focus(); 

		$("#cityidref_popup").hide();
		
		searchCityCode();
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


  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[126,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 var formResponseMst = $( "#frm_mst_add" );
     formResponseMst.validate();

    $("#CTRYID_REF_POPUP").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_CTRYID_REF").hide();
      validateSingleElemnet("CTRYID_REF_POPUP");
         
    });

    $( "#CTRYID_REF_POPUP" ).rules( "add", {
        required: true,
        messages: {
            required: "Required field.",
        }
    });

    $("#STID_REF_POPUP").blur(function(){
      $(this).val($.trim( $(this).val() ));

      $("#ERROR_STID_REF").hide();
      validateSingleElemnet("STID_REF_POPUP");
         
    });

    $( "#STID_REF_POPUP" ).rules( "add", {
        required: true,
        messages: {
            required: "Required field.",
        }
    });

    $("#CITYID_REF_POPUP").blur(function(){
      $(this).val($.trim( $(this).val() ));

      $("#ERROR_CITYID_REF").hide();
      validateSingleElemnet("CITYID_REF_POPUP");
         
    });

    $( "#CITYID_REF_POPUP" ).rules( "add", {
        required: true,
        messages: {
            required: "Required field.",
        }
    });

    $("#DISTCODE").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_DISTCODE").hide();
        validateSingleElemnet("DISTCODE");
    });

    $( "#DISTCODE" ).rules( "add", {
        required: true,
        nowhitespace: true,
        //StringNumberRegex: true,
        messages: {
            required: "Required field.",
        }
    });

    $("#NAME").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_NAME").hide();
        validateSingleElemnet("NAME");
    });

    $("#NAME").keydown(function(){
        $("#ERROR_NAME").hide();
        validateSingleElemnet("NAME");
    });

    $( "#NAME" ).rules( "add", {
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
      var validator =$("#frm_mst_add" ).validate();
         if(validator.element( "#"+element_id+"" )){
            //check duplicate code
          if(element_id=="DISTCODE" || element_id=="distcode" ) {
            checkDuplicateCode();


          }

         }
    }

    // //check duplicate exist code
    function checkDuplicateCode(){
        
        //validate and save data
        var getDataForm = $("#frm_mst_add");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[126,"codeduplicate"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_DISTCODE',data.msg);
                    $("#DISTCODE").focus();
                }                                
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
    }

    //validate
    $( "#btnSave" ).click(function() {
        if(formResponseMst.valid()){

          var DISTCODE          =   $.trim($("#DISTCODE").val());
          if(DISTCODE ===""){
              $("#ProceedBtn").focus();
              $("#YesBtn").hide();
              $("#NoBtn").hide();
              $("#OkBtn").show();
              $("#AlertMessage").text('Please enter Disctrict Code.');
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

        $("#OkBtn1").hide();
        //validate and save data
        event.preventDefault();

        var getDataForm = $("#frm_mst_add");
        var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[126,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();

                    if(data.errors.DISTCODE){
                        //showError('ERROR_DISTCODE',data.errors.DISTCODE);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").hide();
                        $("#OkBtn").show();
                        $("#AlertMessage").text("Disctict code is ".data.errors.DISTCODE);
                        $("#alert").modal('show');
                        $("#OkBtn").focus();
                    }
                    if(data.errors.NAME){
                        //showError('ERROR_NAME',data.errors.NAME);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").hide();
                        $("#OkBtn").show();
                        $("#AlertMessage").text("Disctrict name is required.");
                        $("#alert").modal('show');
                        $("#OkBtn").focus();
                    }
                   if(data.exist=='duplicate') {

                      $("#YesBtn").hide();
                      $("#NoBtn").hide();
                      $("#OkBtn").show();

                      $("#AlertMessage").text(data.msg);

                      $("#alert").modal('show');
                      $("#OkBtn").focus();

                   }
                   if(data.save=='invalid') {

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
                    $("#OkBtn1").show();
                    $("#AlertMessage").text(data.msg);
                    $(".text-danger").hide();
                    $("#frm_mst_add").trigger("reset");
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();

                  //  window.location.href='{{ route("master",[126,"index"])}}';
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

        $(".text-danger").hide();
        $("#DISTCODE").focus();
        
    }); ///ok button

    $("#OkBtn1").click(function(){

      $("#alert").modal('hide');
      $("#YesBtn").show();  //reset
      $("#NoBtn").show();   //reset
      $("#OkBtn").show();
      $("#OkBtn1").hide();
      $(".text-danger").hide();
      window.location.href = "{{route('master',[126,'index'])}}";

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


    
    $("#OkBtn").click(function(){
      $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('master',[126,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){
      $("#DISTCODE").focus();
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



    $(function() { $("#CTRYID_REF_POPUP").focus(); });

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