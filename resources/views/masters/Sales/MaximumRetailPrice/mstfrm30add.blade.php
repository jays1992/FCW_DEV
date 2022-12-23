@extends('layouts.app')
@section('content')

<div class="container-fluid topnav">
	<div class="row">
		<div class="col-lg-2"><a href="{{route('master',[30,'index'])}}" class="btn singlebt">Maximum Retail Price Master</a></div>
		<div class="col-lg-10 topnav-pd">
		  <button  id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
		  <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
		  <button id="btnSaveFormData"   class="btn topnavbt" tabindex="7"><i class="fa fa-save"></i> Save</button>
		  <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
		  <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-print"></i> Print</button>
		  <button class="btn topnavbt"  id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
		  <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
		  <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-lock"></i> Approved</button>
		  <button  class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</button>
		  <a href="{{route('home')}}" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
		</div>
	</div>
</div>

<div class="container-fluid filter">
	<form id="form_data" method="POST"  > 
		@CSRF  
		<div class="inner-form">
      
      <div class="row">
        <div class="col-lg-2 pl"><p>MRP No</p></div>
        <div class="col-lg-2 pl">
          @if(!empty($objDD))
              @if($objDD->SYSTEM_GRSR == "1")
                  <input type="text" name="MRP_NO" id="MRP_NO" value="{{ $objDOCNO }}" class="form-control mandatory" tabindex="1" maxlength="10" autocomplete="off" readonly style="text-transform:uppercase" autofocus >
              @endif
              @if($objDD->MANUAL_SR == "1")
                  <input type="text" name="MRP_NO" id="MRP_NO" value="{{ old('MRP_NO') }}" class="form-control mandatory"  maxlength="{{$objDD->MANUAL_MAXLENGTH}}" tabindex="1" autocomplete="off" style="text-transform:uppercase" autofocus >
              @endif
          @endif  
          {{-- <input type="text" name="MRP_NO" id="MRP_NO"  class="form-control mandatory" style="text-transform:uppercase"  maxlength="15" tabindex="1" autocomplete="off" required> --}}
          <span class="text-danger" id="ERROR_MRP_NO"></span>
        </div>			
        <div class="col-lg-2 pl"><p>MRP Date</p></div>
        <div class="col-lg-2 pl">
          <input type="text" name="MRP_DT" id="MRP_DT" class="form-control mandatory"  value="{{ \Carbon\Carbon::now()->format('d/m/Y') }}"  tabindex="2" required readonly> 
        </div>			
        <div class="col-lg-2 pl"><p>With Effective Date</p></div>
        <div class="col-lg-2 pl">
          <input type="date" name="EFFECTIVE_DT" id="EFFECTIVE_DT" class="form-control  mandatory" placeholder="dd/mm/yyyy"  tabindex="3" required >  
        </div>
      </div>
      
      <div class="row">			
        <div class="col-lg-2 pl"><p>MRP Title</p></div>
        <div class="col-lg-4 pl">
          <input type="text" name="MRP_TITLE" id="MRP_TITLE" class="form-control" maxlength="200" autocomplete="off"  tabindex="4">
        </div>
      </div>
      
      <div class="row">
        <div class="col-lg-2 pl"><p>De-Activated</p></div>
        <div class="col-lg-1 pl pr">
        <input type="checkbox"   name="DEACTIVATED"  id="deactive-checkbox_0"  disabled tabindex="3">
        </div>
        
        <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
        <div class="col-lg-2 pl">
        <div class="col-lg-8 pl">
        <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" placeholder="dd/mm/yyyy"   tabindex="4" disabled/>
        </div>
        </div>
      </div>    
	</div>
		
		
	<div class="container-fluid">
		<div class="row">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#tab1" tabindex="6">Material</a></li>
			</ul>
			<div class="tab-content">
        <div id="tab1" class="tab-pane fade in active">
            <div class="table-responsive table-wrapper-scroll-y " style="height:260px;margin-top:10px;">					
              <table id="table3" class="display nowrap table table-striped table-bordered itemlist itemlistscroll w-200" style="height:auto !important;">
                <thead id="thead1"  style="position: sticky;top: 0">
                  <tr >
                    <th style="width: 120px">Item Code<input class="form-control" type="hidden" name="Row_Count3" id ="Row_Count3"></th>
                    <th>Item Name</th>
                    <th style="width: 70px">UoM Code</th>
                    <th>Item Specifications (If Any)</th>
                    <th style="width: 130px">MRP</th>
                    <th>Remarks</th>
                    <th style="text-align: center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr  class="participantRow">
                    <td>
                      <input  class="form-control w-100" type="text" name="ITEMID_REF_0" id="TXT_ITEMID_REF_POPUP_0" maxlength="100" readonly>
                    </td>
                    <td hidden>
                      <input  class="form-control w-100" type="text" name="HDN_ITEMID_REF_0" id="HDN_ITEMID_REF_POPUP_0" maxlength="100" >
                    </td>
                    <td>
                      <input  class="form-control w-100" type="text" name="ITEMNAME_0" id="ITEMNAME_0" autocomplete="off" readonly >
                    </td>
                    <td hidden>
                      <input  class="form-control w-100" type="text" name="HDN_UOMID_REF_0" id="HDN_UOMID_REF_POPUP_0" maxlength="100" >
                    </td>
                    <td >
                      <input  class="form-control" style="width: 70px" type="text" name="UOM_0" id="UOM_0" autocomplete="off" readonly >
                    </td>
                    <td>
                      <input  class="form-control w-100" type="text" name="ITEM_SPEC_0" id="ITEM_SPEC_0" maxlength="200" autocomplete="off"  >
                    </td>
                    <td>
                      <input  class="form-control rightalign five-digits" type="text" name="MRP_0" id="IDMRP_0" maxlength="13" autocomplete="off"  >
                    </td>
                    <td>
                      <input  class="form-control " type="text" name="REMARKS_0" id="REMARKS_0" maxlength="200" autocomplete="off"  >
                    </td>
                    <td align="center" >
                      <a class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></a>
                      <button class="btn remove" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button>
                    </td>
                  </tr>
                </tbody>
              </table>          
            </div>
        </div><!-- tab1 -->

      </div><!-- tab-content -->
		</div><!-- row -->			
	</div><!-- container-fluid -->
						
	</form>
  </div>
@endsection

@section('alert')
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog" style="position:relative;top:82px;left:273px;"  >
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
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="margin-left: 90px;display:none;">
              <div id="alert-active" class="activeOk1"></div>OK</button>
        </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

{{-- POPUP1 popup  --}}
<div id="popup1" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='popup1_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Items</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="popup1_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th class="ROW1" style="width: 10%" align="center">Select</th> 
            <th  class="ROW2" style="width: 35%">Code</th>
            <th  class="ROW3" style="width: 35%" >Description</th>
            <th  class="ROW4" style="width: 20%" >UOM</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td class="ROW1" style="width: 10%" align="center"><span class="check_th">&#10004;</span></td>
          <td class="ROW2" style="width: 35%" ><input type="text" autocomplete="off"  class="form-control"  id="popup1_codesearch"  onkeyup='colSearch("popup1_tab2","popup1_codesearch",1)' /></td>
          <td class="ROW3" style="width: 35%"><input  type="text" autocomplete="off"  class="form-control"  id="popup1_namesearch"  onkeyup='colSearch("popup1_tab2","popup1_namesearch",2)' /></td>
          <td class="ROW4" style="width: 20%"><input  type="text" autocomplete="off"  class="form-control"  id="popup1_code2search"  onkeyup='colSearch("popup1_tab2","popup1_code2search",3)' /></td>
        </tr>
        </tbody>
      </table>
      <table id="popup1_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <td colspan="2" hidden> 
            <input  type="text" name="fieldid" id="hdn_popup1fieldid"/>
            <input  type="text" name="fieldid2" id="hdn_popup1fieldid2"/>
            <input  type="text" name="fieldid3" id="hdn_popup1fieldid3"/>
            <input  type="text" name="fieldid4" id="hdn_popup1fieldid4"/>
            <input  type="text" name="fieldid5" id="hdn_popup1fieldid5"/>
            <input  type="text" name="fieldid6" id="hdn_popup1fieldid6"/>
           </td>
        </thead>
        <tbody id="popup1_tbody">
        @foreach ($objPopup1List as $index=>$listRow1)
        <tr >
          <td class="ROW1" style="width: 12%" align="center"> <input type="checkbox" name="SELECT_ITEMID_REF[]"  id="record_idref_{{ $listRow1->ITEMID }}" class="cls_popup1_idref" value="{{ $listRow1->ITEMID }}" /></td>
          <td class="ROW2" style="width: 34%">{{ $listRow1->ICODE }}
          <input type="hidden" id="txtrecord_idref_{{ $listRow1->ITEMID }}" data-desc="{{ $listRow1->ICODE }}" data-desc2="{{ $listRow1->NAME }}" data-desc6="{{ $listRow1->ITEM_SPECI }}" data-id4='{{ $listRow1->MAIN_UOMID_REF }}' data-desc4="{{ $listRow1->UOMCODE }}"    value="{{ $listRow1->ITEMID }}"/>
          </td>
          <td class="ROW3" style="width: 34%">{{ $listRow1->NAME }}</td>
          <td class="ROW2" style="width: 20%">{{ $listRow1->UOMCODE }}</td>
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
{{-- POPUP1 popup end  --}}
@endsection

@push('bottom-css')
@endpush

@push('bottom-scripts')
<script>
$(function() { 
    //ready
    $("#MRP_NO").focus(); 

    $("#Row_Count3").val(1);
    
     $("[id*='IDMRP_']").ForceNumericOnly();
    

    // $( "#MRP_DT" ).rules( "add", {
    //     required: true,
    //     DateValidate:true,
    //     normalizer: function(value) {
    //         return $.trim(value);
    //     },
    //     messages: {
    //         required: "Required field"
    //     }
    // });
    $( "#EFFECTIVE_DT" ).rules( "add", {
        required: true,
        DateValidate:true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field"
        }
    });

    //---------------------------  
}); //ready
       
         $('#table3').on('keyup', '.five-digits', function() {
            if ($(this).val().indexOf('.') != -1) {
                if ($(this).val().split(".")[1].length > 5) {
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
            return this; //for chaining
        });

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
function colSearchClear(ptable1,pclsname) {
  //clear text box value
  $('#'+ptable1+' input[type="text"]').each(function () {
      $(this).val('');
   });
  
  //clear row 
  $('.'+pclsname).each(function () {
      $(this).removeAttr("style");
   });
}

$('#table3').on('blur','[id*="IDMRP"]',function(event){
  if(intRegex.test($(this).val())){
    $(this).val($(this).val() + '.00000') ;
  }
});

          

$("#table3").on('click', '.add', function() {
    
    var $tr = $(this).closest('table');
    var allTrs = $tr.find('tbody').last();
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
    $clone.find('input:text').val('');
    $clone.find('.remove').removeAttr('disabled'); 

    $clone.find('input:checkbox').prop('checked',false);;

    $tr.closest('table').append($clone);   
    var rowCount = $('#Row_Count3').val();
      rowCount = parseInt(rowCount)+1;
      $('#Row_Count3').val(rowCount);

}); 

$("#table3").on('click', '.remove', function() {
    var rowCount = $(this).closest('table').find('tbody').length;
    if (rowCount > 1) {
      $(this).closest('tbody').remove();
    }
    if (rowCount <= 1) {
      $(document).find('.remove').prop('disabled', false);
    }
});


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
       

      let tidp1 = '';
      let tidp2 = '';
      let clsname = '';          
      let p_headers = '';
      function doSorting(ptable1,ptable2,pclass){


           tidp1 = "#"+ptable1;
           tidp2 = "#"+ptable2;
           clsname = "."+pclass;          
           p_headers = document.querySelectorAll(tidp1 + " th");

          // Sort the table element when clicking on the table headers
          p_headers.forEach(function(element, i) {
            element.addEventListener("click", function() {
              w3.sortHTML(tidp2, clsname, "td:nth-child(" + (i + 1) + ")");
            });
          });

      }

      //------------
      

  //nor tax code
  $('#table3').on ("focus",'[id*="TXT_ITEMID_REF_POPUP"]',function(event){
        $("#popup1").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="HDN_ITEMID_REF_POPUP"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="ITEMNAME"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="HDN_UOMID_REF_POPUP"]').attr('id');
        var id5 = $(this).parent().parent().find('[id*="UOM_"]').attr('id');
        var id6 = $(this).parent().parent().find('[id*="ITEM_SPEC_"]').attr('id');

        $('#hdn_popup1fieldid').val(id);
        $('#hdn_popup1fieldid2').val(id2);        
        $('#hdn_popup1fieldid3').val(id3);        
        $('#hdn_popup1fieldid4').val(id4);        
        $('#hdn_popup1fieldid5').val(id5);        
        $('#hdn_popup1fieldid6').val(id6);        

  });

  $("#popup1_close").on("click",function(event){
        $("#popup1").hide();
  });

  $('#popup1_tab2').on("click",".cls_popup1_idref",function(){
        var fieldid = $(this).attr('id');
        var txtval =    $("#txt"+fieldid+"").val();
        var texdesc =   $("#txt"+fieldid+"").data("desc")
        var texdesc2 =   $("#txt"+fieldid+"").data("desc2")

        var id4 =   $("#txt"+fieldid+"").data("id4")
        var texdesc4 =   $("#txt"+fieldid+"").data("desc4")

        var texdesc6 =   $("#txt"+fieldid+"").data("desc6")
        
        var txtid= $('#hdn_popup1fieldid').val();
        var txt_id2= $('#hdn_popup1fieldid2').val();
        var txt_id3= $('#hdn_popup1fieldid3').val();
        var txt_id4= $('#hdn_popup1fieldid4').val();
        var txt_id5= $('#hdn_popup1fieldid5').val();
        var txt_id6= $('#hdn_popup1fieldid6').val();

        //------------------
        var selected_data  = [];
        $("[id*=HDN_ITEMID_REF]").each(function(){
            if( $.trim( $(this).val() ) !== "" )
            {
              selected_data.push($(this).val());
            }
        });

        if(jQuery.inArray(txtval, selected_data) !== -1){
            $("#alert").modal('show');
            $("#AlertMessage").text('Already selected. Please select another field.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
            $("#popup1").hide();
            //clear
            //colSearchClear("popup1_tab1","cls_popup1_idref");
            $(this).prop("checked",false);
            event.preventDefault();
            return false;
        }                 
        //-------------------
        
        $('#'+txtid).val(texdesc);
        $('#'+txt_id2).val(txtval);
        $('#'+txt_id3).val(texdesc2);
        $('#'+txt_id4).val(id4);
        $('#'+txt_id5).val(texdesc4);
        $('#'+txt_id6).val(texdesc6);

        $('#'+txtid).blur();  
        //colSearchClear("popup1_tab1","cls_popup1_idref");       
        $(this).prop("checked",false); 
        $("#popup1").hide();
   
});
//nor tax end  



/* form validation */

var formItemMst = $( "#form_data" );
  formItemMst.validate();

$("#MRP_NO").blur(function(){
	$(this).val($.trim( $(this).val() ));
	$("#ERROR_MRP_NO").hide();
	validateSingleElemnet("MRP_NO"); 
});

$("#MRP_NO").rules( "add",{
	required: true,
	nowhitespace: true,
	//StringNumberRegex: true,
	messages: {
		required: "Required field.",
	}
});


function validateSingleElemnet(element_id){
	var validator =$("#form_data" ).validate();
	
	if(validator.element( "#"+element_id+"" )){
		
		if(element_id=="MRP_NO" || element_id=="MRP_NO" ) {
			checkDuplicateCode();
		}
		
	 }
}

function checkDuplicateCode(){
	var codedata = $("#MRP_NO").val(); 
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
	$.ajax({
		url:'{{route("master",[30,"codeduplicate"])}}',
		type:'POST',
		data:{'MRP_NO': codedata},
		success:function(data) {
			if(data.exists) {
				$(".text-danger").hide();
				showError('ERROR_MRP_NO',data.msg);
				$("#MRP_NO").focus();
			}                                
		},
		error:function(data){
		  console.log("Error: Something went wrong.");
		},
	});
}

$( "#btnSaveFormData" ).click(function() {

	if(formItemMst.valid()){
    event.preventDefault();

        var MRP_NO          =   $.trim($("#MRP_NO").val());
          if(MRP_NO ===""){
            $("#alert").modal('show');
            $("#AlertMessage").text('Please enter MRP No.');
            $("#YesBtn").hide(); 
            $("#NoBtn").hide();  
            $("#OkBtn1").show();
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
            return false;
          }
    
                var allblank1 = [];  
                var allblank2 = [];  
                var allblank3 = [];  
                var allblank4 = [];  
                

                $("[id*=HDN_ITEMID_REF_POPUP]").each(function(){
                  var strid = $(this).attr("id")
                  if (strid.toLowerCase().indexOf("error") == -1){
                    if( $.trim( $(this).val()) == "" )
                    {
                        allblank1.push('true');
                    }else
                    {
                      allblank1.push('false');
                    }
                  }
                });

                $("[id*=IDMRP]").each(function(){
                  var strid = $(this).attr("id")
                  if (strid.toLowerCase().indexOf("error") == -1){

                      if( $.trim( $(this).val()) == "" )
                      {
                          allblank2.push('true');
                      }else
                      {
                        allblank2.push('false');
                      }

                      if( $.trim($(this).val()) != "" && !$.isNumeric($(this).val()) ){
                        allblank3.push('true');
                      }else
                      {
                        allblank3.push('false');
                      }

                      if( $.trim($(this).val()) != "" && $.isNumeric($(this).val()) ){
                        if($(this).val()<0.00001){
                          allblank4.push('true');
                        }else
                        {
                          allblank4.push('false');
                        }
                      }


                  }
                   
                });

                if(jQuery.inArray("true", allblank1) !== -1){
                      $("#alert").modal('show');
                      $("#AlertMessage").text('Please select Item Code in Material Tab.');
                      $("#YesBtn").hide(); 
                      $("#NoBtn").hide();  
                      $("#OkBtn1").show();
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;

                  }else if(jQuery.inArray("true", allblank2) !== -1){
                      $("#alert").modal('show');
                      $("#AlertMessage").text('Please enter MRP in Material Tab.');
                      $("#YesBtn").hide(); 
                      $("#NoBtn").hide();  
                      $("#OkBtn1").show();
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;
                  
                  }else if(jQuery.inArray("true", allblank3) !== -1){
                      $("#alert").modal('show');
                      $("#AlertMessage").text('Please enter valid MRP in Material Tab.');
                      $("#YesBtn").hide(); 
                      $("#NoBtn").hide();  
                      $("#OkBtn1").show();
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;

                  }else if(jQuery.inArray("true", allblank4) !== -1){
                      $("#alert").modal('show');
                      $("#AlertMessage").text('MRP value must be greater than 0.00000 in Material Tab.');
                      $("#YesBtn").hide(); 
                      $("#NoBtn").hide();  
                      $("#OkBtn1").show();
                      $("#OkBtn1").focus();
                      highlighFocusBtn('activeOk1');
                      return false;

                  }// blank if    


              $("#alert").modal('show');
              $("#AlertMessage").text('Do you want to save to record.');
              $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
              $("#OkBtn1").hide();
              $("#OkBtn").hide();
              $("#YesBtn").show();
              $("#NoBtn").show();
              $("#YesBtn").focus();
              highlighFocusBtn('activeYes');

            return false;

  }            
//----------------------------
});//btnSaveFormData

    
  $("#YesBtn").click(function(){

        $("#alert").modal('hide');
        var customFnName = $("#YesBtn").data("funcname");
            window[customFnName]();

  }); //yes button
   
  $("#OkBtn1").click(function(){

        $("#alert").modal('hide');
  }); //yes button


  window.fnSaveData = function (){

        //validate and save data
        event.preventDefault();

        var currentForm = $("#form_data");
        var formData = currentForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("master",[30,"save"])}}',
            type:'POST',
            data:formData,
            success:function(data) {
               
                if(data.errors) {
                    $(".text-danger").hide();


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
                    $("#OkBtn").show();  // ok button for reload the page
                    highlighFocusBtn('activeOk1');
                    
                    $("#AlertMessage").text("Recore saved successfully.");
                    $("#alert").modal('show');

                    $("#OkBtn").focus();
                    $(".text-danger").hide();
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
         //reload form
         window.location.href = "{{route('master',[30,'index'])}}";
        
    }); ///ok button

    
    
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
        
    }); ////Undo button

    $("#OkBtn").click(function(){
      $("#alert").modal('hide');

    });////ok button


   window.fnUndoYes = function (){
      
      //reload form
      window.location.href = "{{route('master',[30,'add'])}}";

   }//fnUndoYes


   window.fnUndoNo = function (){
      //$("#MRP_NO").focus();
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

    window.onload = function(){
      var strdd = <?php echo json_encode($objDD); ?>;
      if($.trim(strdd)==""){     
        $("#YesBtn").hide();
          $("#NoBtn").hide();
          $("#OkBtn").hide();
          $("#OkBtn1").show();
          $("#AlertMessage").text('Please contact to administrator for creating document numbering.');
          $("#alert").modal('show');
          $("#OkBtn").focus();
          highlighFocusBtn('activeOk');
      } 
    };


</script>
@endpush