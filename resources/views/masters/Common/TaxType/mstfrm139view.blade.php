@extends('layouts.app')
@section('content')
    <div class="container-fluid topnav">
            <div class="row">
              <div class="col-lg-2">
                <a href="{{route('master',[139,'index'])}}" class="btn singlebt">Tax Type Master</a>
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
         <form id="frm_mst_edit" method="POST"  > 
          @CSRF
          {{isset($objResponse->TAXID) ? method_field('PUT') : '' }}
          <div class="inner-form">

              <div class="row">
                <div class="col-lg-2 pl"><p>Tax Type Code</p></div>
                <div class="col-lg-2 pl">
                  <div class="col-lg-12 pl">
                    <label>{{ $objResponse->TTCODE }}</label>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-lg-2 pl"><p>Tax Type Description</p></div>
                <div class="col-lg-3 pl">
                  <label>{{ $objResponse->TTDESCRIPTION }}</label>
                </div>
              </div>	

              <div class="row">
                <div class="col-lg-2 pl"><p>Type</p></div>
                  <div class="col-lg-1 pl"><p>IGST</p></div>
                  <div class="col-lg-1 pl pr">
                    <input type="radio" name="IGST_TYPE" value="IGST" id="IGST_TYPE"  tabindex="3" @if($objResponse->TAX_TYPE=="IGST") checked @endif disabled />
                    <input type="hidden" name="TAX_TYPE" id="TAX_TYPE" value="{{$objResponse->TAX_TYPE}}" />
                  </div>
                  <div class="col-lg-1 pl"><p>CGST</p></div>
                  <div class="col-lg-1 pl pr">
                    <input type="radio" name="CGST_TYPE" value="CGST" id="CGST_TYPE"  tabindex="4" @if($objResponse->TAX_TYPE=="CGST") checked @endif disabled />
                  </div>
                  <div class="col-lg-1 pl"><p>SGST</p></div>
                  <div class="col-lg-1 pl pr">
                    <input type="radio" name="SGST_TYPE" value="SGST" id="SGST_TYPE"  tabindex="5" @if($objResponse->TAX_TYPE=="SGST") checked @endif disabled />
                  </div>
              </div>
              
              <div class="row"><br/></div>
              <div class="row">
                <div class="col-lg-4 pl"><p>For Sale</p></div>
              </div>
          
              <div class="row">
                
                <div class="col-lg-1 pl"><p>Within State</p></div>
                <div class="col-lg-1 pl pr">
                  <input type="radio" name="TAX_FOR" value="SALE_WITHINSTATE" id="SALE_WITHINSTATE" @if($FOR_SALE==1 && $WITHINSTATE==1) checked @endif tabindex="3" disabled>
                </div>
                
                <div class="col-lg-1 pl"><p>Out of State</p></div>
                <div class="col-lg-1 pl pr">
                  <input type="radio" name="TAX_FOR" value="SALE_OUTOFSTATE" id="SALE_OUTOFSTATE" tabindex="4"  @if($FOR_SALE==1 && $OUTOFSTATE==1)  checked @endif disabled> 
                </div>
                
                <div class="col-lg-1 pl"><p>EXPORT</p></div>
                <div class="col-lg-1 pl pr">
                  <input type="radio" name="TAX_FOR" value="SALE_EXPORT" id="SALE_EXPORT" tabindex="5" @if($FOR_SALE==1 && $EXPORT ==1) checked @endif disabled>
                </div>
              </div>
              
              <div class="row"><br/></div>
              <div class="row">
                <div class="col-lg-4 pl"><p>For Purchase</p></div>
              </div>
              
              <div class="row">
                
                <div class="col-lg-1 pl "><p>Within State</p></div>
                <div class="col-lg-1 pl pr">
                  <input type="radio" name="TAX_FOR" value="PURCHASE_WITHINSTATE" id="PURCHASE_WITHINSTATE" tabindex="6" @if($FOR_PURCHASE==1 && $WITHINSTATE==1) checked @endif disabled>
                </div>
                
                <div class="col-lg-1 pl"><p>Out of State</p></div>
                <div class="col-lg-1 pl pr">
                  <input type="radio" name="TAX_FOR" value="PURCHASE_OUTOFSTATE" id="PURCHASE_OUTOFSTATE" tabindex="7" @if($FOR_PURCHASE==1 && $OUTOFSTATE==1) checked  @endif disabled>
                </div>
                
                <div class="col-lg-1 pl"><p>Import</p></div>
                <div class="col-lg-1 pl pr">
                  <input type="radio" name="TAX_FOR" value="PURCHASE_IMPORT" id="PURCHASE_IMPORT" tabindex="8" @if($FOR_PURCHASE==1 && $EXPORT==1) checked @endif disabled>
                </div>
              </div>
              
              
              <div class="row"><br/></div>
              <div class="row">
                <div class="col-lg-4 pl"><p>GST</p></div>
              </div>
              
              <div class="row">
                <div class="col-lg-1 pl"><p>GL</p></div>
                <div class="col-lg-2 pl">
                  <label>{{ $objOldGSTSLList->GLCODE }} - {{ $objOldGSTSLList->GLNAME }}</label>
                </div>
              </div>

              <div class="row"><br/></div>
              <div class="row">
                <div class="col-lg-4 pl"><p>REVERSE GST</p></div>
              </div>
              
              <div class="row">
                <div class="col-lg-1 pl"><p>GL</p></div>
                <div class="col-lg-2 pl">
                  <label>{{ $objOldREVSLList->GLCODE }} - {{ $objOldREVSLList->GLNAME }}</label>
                </div>
              </div>
            
              <div class="row"><br/></div>
              <!-- <div class="row">
                <div class="col-lg-4 pl"><p>Sale</p></div>
                <div class="col-lg-4 pl col-md-offset-1"><p>Sale Return</p></div>
              </div>
              
              <div class="row">
                <div class="col-lg-1 pl"><p>GL</p></div>
                <div class="col-lg-2 pl">
                  <label>{{ $SALE_GLID_REF_LABEL }}</label>
                </div>
                <div class="col-lg-1 pl col-md-offset-2"><p>GL</p></div>
                <div class="col-lg-2 pl">
                  <label>{{ $SALERETURN_GLID_REF_LABEL }}</label>
                </div>
              </div>
              
              <div class="row"><br/></div>
              <div class="row">
                <div class="col-lg-4 pl"><p>Purchase</p></div>
                <div class="col-lg-4 pl col-md-offset-1"><p>Purchase Return</p></div>
              </div>
              
              <div class="row">
                <div class="col-lg-1 pl"><p>GL</p></div>
                <div class="col-lg-2 pl">
                  <label>{{ $PURCHASE_GLID_REF_LABEL }}</label>
                </div>
                
                <div class="col-lg-1 pl col-md-offset-2"><p>GL</p></div>
                <div class="col-lg-2 pl">
                  <label>{{ $PURCHASERETURN_GLID_REF_LABEL }}</label>
                </div>
              </div> -->
              
              <div class="row"><br/></div>
              
              <div class="row">
                <div class="col-lg-2 pl"><p>De-Activated</p></div>
                <div class="col-lg-1 pl pr">
                <input type="checkbox" name="DEACTIVATED" name="DEACTIVATED"  id="deactive-checkbox_0" {{$objResponse->DEACTIVATED == 1 ? "checked" : ""}}   value='{{$objResponse->DEACTIVATED == 1 ? 1 : 0}}' tabindex="14"  disabled>
                </div>
                
                <div class="col-lg-2 pl"><p>Date of De-Activated</p></div>
                <div class="col-lg-2 pl">
                <div class="col-lg-8 pl">
                  <input type="date" name="DODEACTIVATED" class="form-control" id="DODEACTIVATED" placeholder="dd-mm-yyyy" 
                  {{$objResponse->DEACTIVATED == 1 ? "" : "disabled"}}
                     tabindex="8" value="{{ (!is_null($objResponse->DODEACTIVATED) && $objResponse->DODEACTIVATED!='1900-01-01')? 
                     \Carbon\Carbon::parse($objResponse->DODEACTIVATED)->format('Y-m-d') : ''   }}"  disabled/>
                </div>
                </div>
              </div>




             
          </div><!--inner-form -->
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
            <button class="btn alertbt" name='OkBtn1' id="OkBtn1" style="margin-left: 90px;display:none;">
                  <div id="alert-active" class="activeOk"></div>OK</button>    
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

{{-- GST GL POPUP --}}
<div id="gstglidref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='gstglidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>GST GL</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="GSTGL_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="GSTGL_codesearch" onkeyup='colSearch("GSTGL_tab2","GSTGL_codesearch",0)' >
          </td>
          <td><input type="text" id="GSTGL_namesearch" onkeyup='colSearch("GSTGL_tab2","GSTGL_namesearch",1)'></td>
        </tr>
        </tbody>
      </table>
      <table id="GSTGL_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="GSTGL_body">
        @foreach ($objGSTSLList as $index=>$GSTGLList)
        <tr id="gstglidref_{{ $GSTGLList->GLID }}" class="cls_gstglidref">
          <td width="50%">{{ $GSTGLList->GLCODE }}
          <input type="hidden" id="txtgstglidref_{{ $GSTGLList->GLID }}" data-desc="{{ $GSTGLList->GLCODE }}" data-descname="{{ $GSTGLList->GLNAME }}" value="{{ $GSTGLList->GLID }}"/>
          </td>
          <td>{{ $GSTGLList->GLNAME }}</td>
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

{{-- SALE GL POPUP --}}
<div id="saleglidref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='saleglidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>SALE GL</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="SALEGL_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="SALEGL_codesearch" onkeyup='colSearch("SALEGL_tab2","SALEGL_codesearch",0)' >
          </td>
          <td><input type="text" id="SALEGL_namesearch" onkeyup='colSearch("SALEGL_tab2","SALEGL_namesearch",1)'></td>
        </tr>
        </tbody>
      </table>
      <table id="SALEGL_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="SALEGL_body">
        @foreach ($objSALEGLList as $index=>$SALEGLList)
        <tr id="saleglidref_{{ $SALEGLList->GLID }}" class="cls_saleglidref">
          <td width="50%">{{ $SALEGLList->GLCODE }}
          <input type="hidden" id="txtsaleglidref_{{ $SALEGLList->GLID }}" data-desc="{{ $SALEGLList->GLCODE }}" data-descname="{{ $SALEGLList->GLNAME }}" value="{{ $SALEGLList->GLID }}"/>
          </td>
          <td>{{ $SALEGLList->GLNAME }}</td>
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

{{-- SALE RETURN GL POPUP --}}
<div id="saleretglidref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='saleretglidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>SALE RETURN GL</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="SALERETGL_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="SALERETGL_codesearch" onkeyup='colSearch("SALERETGL_tab2","SALERETGL_codesearch",0)' >
          </td>
          <td><input type="text" id="SALERETGL_namesearch" onkeyup='colSearch("SALERETGL_tab2","SALERETGL_namesearch",1)'></td>
        </tr>
        </tbody>
      </table>
      <table id="SALERETGL_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="SALERETGL_body">
        @foreach ($objSALEGLList as $index=>$SALEGLList)
        <tr id="saleretglidref_{{ $SALEGLList->GLID }}" class="cls_saleretglidref">
          <td width="50%">{{ $SALEGLList->GLCODE }}
          <input type="hidden" id="txtsaleretglidref_{{ $SALEGLList->GLID }}" data-desc="{{ $SALEGLList->GLCODE }}" data-descname="{{ $SALEGLList->GLNAME }}" value="{{ $SALEGLList->GLID }}"/>
          </td>
          <td>{{ $SALEGLList->GLNAME }}</td>
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

{{-- PURCHASE GL POPUP --}}
<div id="purglidref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='purglidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>PURCHASE GL</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">

      <table id="PURGL_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="PURGL_codesearch" onkeyup='colSearch("PURGL_tab2","PURGL_codesearch",0)' >
          </td>
          <td><input type="text" id="PURGL_namesearch" onkeyup='colSearch("PURGL_tab2","PURGL_namesearch",1)'></td>
        </tr>
        </tbody>
      </table>
      <table id="PURGL_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="PURGL_body">
        @foreach ($objPURGLList as $index=>$PurRow)
        <tr id="purglidref_{{ $PurRow->GLID }}" class="cls_purglidref">
          <td width="50%">{{ $PurRow->GLCODE }}
          <input type="hidden" id="txtpurglidref_{{ $PurRow->GLID }}" data-desc="{{ $PurRow->GLCODE }}" data-descname="{{ $PurRow->GLNAME }}" value="{{ $PurRow->GLID }}"/>
          </td>
          <td>{{ $PurRow->GLNAME }}</td>
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


{{-- PURCHASE RETURN GL POPUP --}}
<div id="purretglidref_popup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id='purretglidref_close' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>PURCHASE RETURN GL</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
      <table id="PURRETGL_tab1" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td ><input type="text" id="PURRETGL_codesearch" onkeyup='colSearch("PURRETGL_tab2","PURRETGL_codesearch",0)' >
          </td>
          <td><input type="text" id="PURRETGL_namesearch" onkeyup='colSearch("PURRETGL_tab2","PURRETGL_namesearch",1)'></td>
        </tr>
        </tbody>
      </table>
      <table id="PURRETGL_tab2" class="display nowrap table  table-striped table-bordered" width="100%">
        <tbody id="PURRETGL_body">
        @foreach ($objPURGLList as $index=>$PurRow)
        <tr id="purretglidref_{{ $PurRow->GLID }}" class="cls_purretglidref">
          <td width="50%">{{ $PurRow->GLCODE }}
          <input type="hidden" id="txtpurretglidref_{{ $PurRow->GLID }}" data-desc="{{ $PurRow->GLCODE }}" data-descname="{{ $PurRow->GLNAME }}" value="{{ $PurRow->GLID }}"/>
          </td>
          <td>{{ $PurRow->GLNAME }}</td>
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

<!-- Alert -->
@endsection


@push('bottom-scripts')
<script>

// GSTGLID_REF_POPUP 
$("#GSTGLID_REF_POPUP").on("click",function(event){ 
  $("#gstglidref_popup").show();
});

$("#GSTGLID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#gstglidref_popup").show();
  }
});

$("#gstglidref_close").on("click",function(event){ 
  $("#gstglidref_popup").hide();
});

$('.cls_gstglidref').dblclick(function(){
  var id          =   $(this).attr('id');
  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#GSTGLID_REF_POPUP").val(texdesc+' - '+texdescname);
  $("#GSTGLID_REF").val(txtval);  
  $("#GSTGLID_REF_POPUP").blur();
  
  $("#gstglidref_popup").hide();
  colSearchClear("GSTGL_tab1","cls_gstglidref");
  event.preventDefault();
});

//-----

// SALEGLID_REF_POPUP 
$("#SALEGLID_REF_POPUP").on("click",function(event){ 
  $("#saleglidref_popup").show();
});

$("#SALEGLID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#saleglidref_popup").show();
  }
});

$("#saleglidref_close").on("click",function(event){ 
  $("#saleglidref_popup").hide();
});

$('.cls_saleglidref').dblclick(function(){
  var id          =   $(this).attr('id');
  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#SALEGLID_REF_POPUP").val(texdesc+' - '+texdescname);
  $("#SALEGLID_REF").val(txtval);  
  $("#SALEGLID_REF_POPUP").blur();
  
  $("#saleglidref_popup").hide();
  colSearchClear("SALEGL_tab1","cls_saleglidref");
  event.preventDefault();
});


// SALE RETURN GL_POPUP 
$("#SALERETGLID_REF_POPUP").on("click",function(event){ 
  $("#saleretglidref_popup").show();
});

$("#SALERETGLID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#saleretglidref_popup").show();
  }
});

$("#saleretglidref_close").on("click",function(event){ 
  $("#saleretglidref_popup").hide();
});

$('.cls_saleretglidref').dblclick(function(){
  var id          =   $(this).attr('id');
  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#SALERETGLID_REF_POPUP").val(texdesc+' - '+texdescname);
  $("#SALERETGLID_REF").val(txtval);  
  $("#SALERETGLID_REF_POPUP").blur();
  
  $("#saleretglidref_popup").hide();
  colSearchClear("SALERETGL_tab1","cls_saleretglidref");
  event.preventDefault();
});


// PURCHASE GL POPUP 
$("#PURGLID_REF_POPUP").on("click",function(event){ 
  $("#purglidref_popup").show();
});

$("#PURGLID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#purglidref_popup").show();
  }
});

$("#purglidref_close").on("click",function(event){ 
  $("#purglidref_popup").hide();
});

$('.cls_purglidref').dblclick(function(){
  var id          =   $(this).attr('id');
  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#PURGLID_REF_POPUP").val(texdesc+' - '+texdescname);
  $("#PURGLID_REF").val(txtval);  
  $("#PURGLID_REF_POPUP").blur();
  
  $("#purglidref_popup").hide();
  colSearchClear("PURGL_tab1","cls_purglidref");
  event.preventDefault();
});


// PURCHASE RETURN GL POPUP 
$("#PURRETGLID_REF_POPUP").on("click",function(event){ 
  $("#purretglidref_popup").show();
});

$("#PURRETGLID_REF_POPUP").keyup(function(event){
  if(event.keyCode==13){
    $("#purretglidref_popup").show();
  }
});

$("#purretglidref_close").on("click",function(event){ 
  $("#purretglidref_popup").hide();
});

$('.cls_purretglidref').dblclick(function(){
  var id          =   $(this).attr('id');
  var txtval      =   $("#txt"+id+"").val();
  var texdesc     =   $("#txt"+id+"").data("desc");
  var texdescname =   $("#txt"+id+"").data("descname");

  $("#PURRETGLID_REF_POPUP").val(texdesc+' - '+texdescname);
  $("#PURRETGLID_REF").val(txtval);  
  $("#PURRETGLID_REF_POPUP").blur();
  
  $("#purretglidref_popup").hide();
  colSearchClear("PURGL_tab1","cls_purretglidref");
  event.preventDefault();
});



//-----------
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

  let GSTGL_tab1 = "#GSTGL_tab1";
  let GSTGL_tab2 = "#GSTGL_tab2";
  let gstgl_headers = document.querySelectorAll(GSTGL_tab1 + " th");

  gstgl_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(GSTGL_tab2, ".cls_gstglidref", "td:nth-child(" + (i + 1) + ")");
    });
  });

  //SALE GL
  let SALEGL_tab1 = "#SALEGL_tab1";
  let SALEGL_tab2 = "#SALEGL_tab2";
  let salegl_headers = document.querySelectorAll(SALEGL_tab1 + " th");

  salegl_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(SALEGL_tab2, ".cls_saleglidref", "td:nth-child(" + (i + 1) + ")");
    });
  });

  //SALE RETURN GL
  let SALERETGL_tab1 = "#SALERETGL_tab1";
  let SALERETGL_tab2 = "#SALERETGL_tab2";
  let saleretgl_headers = document.querySelectorAll(SALERETGL_tab1 + " th");

  saleretgl_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(SALERETGL_tab2, ".cls_saleretglidref", "td:nth-child(" + (i + 1) + ")");
    });
  });


  //PURCHASE GL
  let PURGL_tab1 = "#PURGL_tab1";
  let PURGL_tab2 = "#PURGL_tab2";
  let PURGL_headers = document.querySelectorAll(PURGL_tab1 + " th");

  PURGL_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(PURGL_tab2, ".cls_purglidref", "td:nth-child(" + (i + 1) + ")");
    });
  });


  //PURCHASE RETURN GL
  let PURRETGL_tab1 = "#PURRETGL_tab1";
  let PURRETGL_tab2 = "#PURRETGL_tab2";
  let PURRETGL_headers = document.querySelectorAll(PURRETGL_tab1 + " th");

  PURRETGL_headers.forEach(function(element, i) {
    element.addEventListener("click", function() {
      w3.sortHTML(PURRETGL_tab2, ".cls_purretglidref", "td:nth-child(" + (i + 1) + ")");
    });
  });



  //----------------global serach and sorting 
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
  //-------------------  
  var formDataMst = $( "#frm_mst_edit" );
     formDataMst.validate();

    $('input[type=radio][name=TAX_FOR]').on('change', function() {

      $('#SALEGLID_REF_POPUP').val('');
      $('#SALEGLID_REF').val('');
      $('#SALERETGLID_REF_POPUP').val('');
      $('#SALERETGLID_REF').val('');

      $('#PURGLID_REF_POPUP').val('');
      $('#PURGLID_REF').val('');
      $('#PURRETGLID_REF_POPUP').val('');
      $('#PURRETGLID_REF').val('');


      switch ($(this).val()) {
        case 'SALE_WITHINSTATE':
        case 'SALE_OUTOFSTATE':
        case 'SALE_EXPORT':

          $('#SALEGLID_REF_POPUP').attr('disabled',false);
          $('#SALERETGLID_REF_POPUP').attr('disabled',false);

          $('#PURGLID_REF_POPUP').attr('disabled',true);
          $('#PURRETGLID_REF_POPUP').attr('disabled',true);
          break;
        
        case 'PURCHASE_WITHINSTATE':
        case 'PURCHASE_OUTOFSTATE':
        case 'PURCHASE_IMPORT':
          $('#PURGLID_REF_POPUP').attr('disabled',false);
          $('#PURRETGLID_REF_POPUP').attr('disabled',false);

          $('#SALEGLID_REF_POPUP').attr('disabled',true);
          $('#SALERETGLID_REF_POPUP').attr('disabled',true);
          break;
      } //switch

    }); //radio button

  $("#IGST_TYPE").on("click",function(event){ 
    $(this).is(':checked') == true
    {
      $('#CGST_TYPE').attr('checked',false);
      $('#SGST_TYPE').attr('checked',false);
      $("#TAX_TYPE").val('IGST');
    }
  });
  $("#CGST_TYPE").on("click",function(event){ 
    $(this).is(':checked') == true
    {
      $('#IGST_TYPE').attr('checked',false);
      $('#SGST_TYPE').attr('checked',false);
      $("#TAX_TYPE").val('CGST');
    }
  });
  $("#SGST_TYPE").on("click",function(event){ 
    $(this).is(':checked') == true
    {
      $('#CGST_TYPE').attr('checked',false);
      $('#IGST_TYPE').attr('checked',false);
      $("#TAX_TYPE").val('SGST');
    }
  });
    
  $('#btnAdd').on('click', function() {
      var viewURL = '{{route("master",[139,"add"])}}';
      window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
    window.location.href=viewURL;
  });

 

     $("#TTCODE").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_TTCODE").hide();
        validateSingleElemnet("TTCODE");
    });

    $( "#TTCODE" ).rules( "add", {
        required: true,
        nowhitespace: true,
        StringNumberRegex: true,
        messages: {
            required: "Required field.",
        }
    });

    $("#TTDESCRIPTION").blur(function(){
        $(this).val($.trim( $(this).val() ));
        $("#ERROR_TTDESCRIPTION").hide();
        validateSingleElemnet("TTDESCRIPTION");
    });

    $( "#TTDESCRIPTION" ).rules( "add", {
        required: true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field."
        }
    });

    $("#DODEACTIVATED").blur(function(){
      $(this).val($.trim( $(this).val() ));
      $("#ERROR_DODEACTIVATED").hide();
      validateSingleElemnet("DODEACTIVATED");
    });

    $( "#DODEACTIVATED" ).rules( "add", {
        required: true,
        DateValidate:true,
        normalizer: function(value) {
            return $.trim(value);
        },
        messages: {
            required: "Required field"
        }
    });

    //validae single element
    function validateSingleElemnet(element_id){
      var validator =$("#frm_mst_edit" ).validate();
          validator.element( "#"+element_id+"" );
    }

    //validate
    $( "#btnSave" ).click(function() {

        if(formDataMst.valid()){
            //set function nane of yes and no btn 
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
            $("#YesBtn").focus();
            highlighFocusBtn('activeYes');

        }

    });//btnSave

    
    //validate and approve
    $("#btnApprove").click(function() {
        
        if(formDataMst.valid()){
            //set function nane of yes and no btn 
            $("#alert").modal('show');
            $("#AlertMessage").text('Do you want to save to record.');
            $("#YesBtn").data("funcname","fnApproveData");  //set dynamic fucntion name of approval
            $("#YesBtn").focus();
            highlighFocusBtn('activeYes');

        }

    });//btnSave


    $("#YesBtn").click(function(){

      $("#alert").modal('hide');
      var customFnName = $("#YesBtn").data("funcname");
          window[customFnName]();

    }); //yes button

    $("#OkBtn1").click(function(){
      $("#alert").modal('hide');
    });
    
    window.fnSaveData = function (){
        
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
            url:'{{route("mastermodify",[139,"update"])}}',
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
                   
                   $("#AlertMessage").text("Record updated successfully.");
                   $("#alert").modal('show');

                   $("#OkBtn").focus();
                   $(".text-danger").hide();
               }
               
           },
           error:function(data){
             console.log("Error: Something went wrong.");
           },
        });

    };// fnSaveData


    // save and approve 
    window.fnApproveData = function (){
        
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
            url:'{{route("mastermodify",[139,"singleapprove"])}}',
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

        $(".text-danger").hide();
        window.location.href = '{{route("master",[139,"index"]) }}';

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
      window.location.reload();

   }//fnUndoYes


   window.fnUndoNo = function (){

      $("#CITYCODE").focus();

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

    $(function() {
      
      $("#TTCODE").focus(); 
      var today = new Date(); 
      var dodeactived_date = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
          $('#DODEACTIVATED').attr('min',dodeactived_date);

      $('input[type=checkbox][name=DEACTIVATED]').change(function() {
          if ($(this).prop("checked")) {
            $(this).val('1');
            $('#DODEACTIVATED').removeAttr('disabled');
          }
          else {
            $(this).val('0');
            $('#DODEACTIVATED').prop('disabled', true);
            $('#DODEACTIVATED').val('');
            
          }
      });

       
       
       
    }); //ready
    

</script>

@endpush