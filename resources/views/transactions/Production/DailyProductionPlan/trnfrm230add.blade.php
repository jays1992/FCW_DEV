@extends('layouts.app')
@section('content')
    

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[230,'index'])}}" class="btn singlebt">Daily Production Plan (DPP)</a>
                </div><!--col-2-->
                <div class="col-lg-10 topnav-pd">
                    <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                    <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                    <button class="btn topnavbt" id="btnSave" ><i class="fa fa-save"></i> Save</button>
                    <button style="display:none" class="btn topnavbt buttonload"> <i class="fa fa-refresh fa-spin"></i> {{Session::get('save')}}</button>
                    <button class="btn topnavbt" id="btnView" disabled="disabled"><i class="fa fa-eye"></i> View</button>
                    <button class="btn topnavbt" id="btnPrint" disabled="disabled"><i class="fa fa-print"></i> Print</button>
                    <button class="btn topnavbt" id="btnUndo"  ><i class="fa fa-undo"></i> Undo</button>
                    <button class="btn topnavbt" id="btnCancel" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                    <button class="btn topnavbt" id="btnApprove" disabled="disabled"><i class="fa fa-lock"></i>  Approved</button>
                    <button class="btn topnavbt"  id="btnAttach" disabled="disabled"><i class="fa fa-link"></i> Attachment</button>
                    <button class="btn topnavbt" id="btnExit" ><i class="fa fa-power-off"></i> Exit</button>
                </div>
            </div><!--row-->
    </div>

    <form id="frm_trn_se" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >
    <div class="container-fluid filter">

	<div class="inner-form">
	
		<div class="row">
			<div class="col-lg-1 pl"><p>DPP No</p></div>
			<div class="col-lg-1 pl">  
        <input type="text" name="DPP_NO" id="DPP_NO" value="{{$docarray['DOC_NO']}}" {{$docarray['READONLY']}} class="form-control" maxlength="{{$docarray['MAXLENGTH']}}" autocomplete="off" style="text-transform:uppercase" >
          <script>docMissing(@json($docarray['FY_FLAG']));</script> 
        <span class="text-danger" id="ERROR_DPP_NO"></span> 
			</div>
			
			<div class="col-lg-1 pl col-md-offset-1"><p>DPP Date</p></div>
			<div class="col-lg-2 pl">
              
            <input type="hidden" name="hdnmaterial" id="hdnmaterial" class="form-control" autocomplete="off" />
            <input type="date" name="DPPDT" id="DPPDT"  onchange='checkPeriodClosing(230,this.value,1),getDocNoByEvent("DPP_NO",this,@json($doc_req))' class="form-control mandatory" autocomplete="off" placeholder="dd/mm/yyyy" >
           
       </div>
			
		</div>

		
	</div>

	<div class="container-fluid">
    
      
		<div class="row">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#Material">Material</a></li> 
			</ul>


      <div class="tab-content">
      <div id="Material" class="tab-pane fade in active">
          <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
              <table id="example2" class="display nowrap table table-striped table-bordered itemlist sorting-remove" width="100%" style="height:auto !important;">

                  <thead id="thead1"  style="position: sticky;top:">
                          <tr id="headingtr3" >
                              <th width="10%">Sub Leader	<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1" value="1"></th>
                              <th hidden>SLID_REF</th>
                              <th >SO No</th>
                              <th hidden>SOID_REF</th>
                              <th hidden>SQA_REFID</th>
                              <th hidden>SEQ_REFID</th>
                              <th width="10%">Item Code</th>
                              <th hidden>Item_ID</th>
                              <th  width="15%">Item Name</th>
                              <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
                              <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
                              <th {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
                              <th>Main UOM</th>
                              <th  hidden>MAIN_UOMID_REF</th>
                              <th style="width:100px !important;">SO Qty</th>
                              <th style="width:100px !important;">Balance SO Qty</th>
                              <th style="width:100px !important;">Production Plan Qty</th>
                              <th style="width:200px !important;">Priority</th>                          
                              <th  width="6%">Action</th>
                          </tr>
                  </thead>
<tbody id="tbody1">
<tr  class="participantRow">
    <td style="text-align:center;">
      <input type="text" name="SubGl_popup_0" id="txtsubgl_popup_0" class="form-control mandatory" style="width:140px" readonly />
    </td>
    <td hidden>
      <input type="text" name="SLID_REF_0" id="hdnSLIDREF_0" class="form-control"/> 
    </td> 
    <td > 
      <input type="text" name="SaleOrd_Popup_0" id="txtSORD_popup_0"  readonly data-dayno="1"  class="form-control"  style="width:140px;"  />
    </td>
    <td hidden>
      <input type="text" name="SOID_REF_0" id="hdnSOID_0" class="form-control" />
    </td>
    <td hidden>
      <input type="text" name="SQA_REFID_0" id="hdnSQAID_0" class="form-control" />
    </td>  
    <td hidden>
      <input type="text" name="SEQ_REFID_0" id="hdnSEQID_0" class="form-control" />
    </td>  
  
    <td><input type="text" name="popupITEMID_0" id="popupITEMID_0" class="form-control"  autocomplete="off" style="width:150px;"  readonly/></td>
    <td hidden><input type="text" name="ITEMID_REF_0" id="ITEMID_REF_0" class="form-control" autocomplete="off" /></td>

    
    <td><input type="text" name="ItemName_0" id="ItemName_0" class="form-control"  autocomplete="off"  readonly style="width: 100%;"/></td>

    <td {{$AlpsStatus['hidden']}}><input type="text" name="Alpspartno_0" id="Alpspartno_0" class="form-control"  autocomplete="off"  readonly/></td>
    <td {{$AlpsStatus['hidden']}}><input type="text" name="Custpartno_0" id="Custpartno_0" class="form-control"  autocomplete="off"  readonly/></td>
    <td {{$AlpsStatus['hidden']}}><input type="text" name="OEMpartno_0"  id="OEMpartno_0" class="form-control"  autocomplete="off"   readonly/></td>


    <td><input type="text" name="popupMUOM_0" id="popupMUOM_0" class="form-control"  autocomplete="off"  readonly/></td>
    <td hidden><input type="text" name="MAIN_UOMID_REF_0" id="MAIN_UOMID_REF_0" class="form-control"  autocomplete="off" /></td>
    <td>  <input type="text" name="SO_QTY_0" id="SO_QTY_0" class="form-control three-digits" maxlength="13"  autocomplete="off"  readonly style="width:130px;"/></td>

    <td><input type="text" name="BAL_SOQTY_0" id="BALSO_QTY_0" class="form-control" readonly style="width:130px;" autocomplete="off"  /></td>
    <td><input type="text" name="PRO_PLANQTY_0" id="PROPLAN_QTY_0" class="form-control three-digits" style="width:130px;" maxlength="13" autocomplete="off"  /></td>
    <td>
      <select id="dpp_priority_0" name="dpp_priority_0" class="form-control">
        <option value="" >-- Please Select --</option> 
        @foreach ($objPriority as $pindex=>$prow)
          <option value="{{ $prow->PRIORITYID }}" >{{ $prow->PRIORITYCODE }} - {{ $prow->DESCRIPTIONS }}</option> 
        @endforeach
      </select>
    </td>

    <td align="center" ><button class="btn add material" title="add" data-toggle="tooltip" type="button" ><i class="fa fa-plus"></i></button>
    <button class="btn remove dmaterial"  disabled title="Delete" data-toggle="tooltip" type="button" ><i class="fa fa-trash" ></i></button></td>

  </tr>

</tbody>
                </table>
                </div>	
            </div>
            
          

        </div>
    </div>
		</div>
		
	</div>
	
</div>

<!-- </div> -->
</form>
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
<!--dropdown begin-->
<!-- Sub GL Dropdown -->
<div id="subglpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='subgl_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sub GL Account</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="SubGLTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr>
            <th>Sub GLCode</th>
            <th>Sub GLName</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="subglcodesearch" class="form-control" onkeyup="SubGLCodeFunction()">
    </td>
    <td>
    <input type="text" id="subglnamesearch" class="form-control" onkeyup="SubGLNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="SubGLTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
          <tr id="none-select" class="searchalldata"  >            
            <td colspan="2" hidden>
              <input type="text" name="fieldid" id="hdn_subledid"/>
              <input type="text" name="fieldid2" id="hdn_subledid2"/>
            </td>
          </tr>          
        </thead>
        <tbody id="tbody_subglacct">  
          @foreach ($objSLList as $index=>$rowList)
          <tr id="subgl_{{ $rowList->SGLID }}" class="clssubgl">
            <td width="50%">{{ $rowList->SGLCODE }}
            <input type="hidden" id="txtsubgl_{{ $rowList->SGLID }}" data-desc="{{ $rowList->SGLCODE }}" data-descname="{{ $rowList->SLNAME }}" value="{{ $rowList->SGLID }}"/>
            </td>
            <td>{{ $rowList->SLNAME }}</td>
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
<!-- Sub GL Dropdown-->

<!-- Item Code Dropdown -->
<div id="ITEMIDpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md" style="width:90%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='ITEMID_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Item Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="ItemIDTable" class="display nowrap table  table-striped table-bordered" style="width: 100%">
    <thead>
      <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="text" name="fieldid" id="hdn_ItemID"/>
            <input type="text" name="fieldid2" id="hdn_ItemID2"/>
            <input type="text" name="fieldid3" id="hdn_ItemID3"/>
            <input type="text" name="fieldid4" id="hdn_ItemID4"/>
            <input type="text" name="fieldid5" id="hdn_ItemID5"/>
            <input type="text" name="fieldid6" id="hdn_ItemID6"/>
            <input type="text" name="fieldid7" id="hdn_ItemID7"/>
            <input type="text" name="fieldid8" id="hdn_ItemID8"/>
            <input type="text" name="fieldid9" id="hdn_ItemID9"/>
            <input type="text" name="fieldid10" id="hdn_ItemID10"/>
            <input type="text" name="fieldid11" id="hdn_ItemID11"/>
            <input type="text" name="fieldid12" id="hdn_ItemID12"/>
            <input type="text" name="fieldid13" id="hdn_ItemID13"/>
            <input type="text" name="fieldid14" id="hdn_ItemID14"/>
            <input type="text" name="fieldid15" id="hdn_ItemID15"/>
            <input type="text" name="fieldid16" id="hdn_ItemID16"/>
            <input type="text" name="fieldid17" id="hdn_ItemID17"/>
            <input type="text" name="fieldid18" id="hdn_ItemID18"/>
            <input type="text" name="fieldid19" id="hdn_ItemID19"/>
            <input type="text" name="fieldid20" id="hdn_ItemID20"/>
            <input type="text" name="hdn_ItemID21" id="hdn_ItemID21" value="0"/>
            <input type="text" name="fieldid22" id="hdn_ItemID22"/>
            <input type="text" name="fieldid23" id="hdn_ItemID23"/>
            <input type="text" name="fieldid24" id="hdn_ItemID24"/>
            <input type="text" name="fieldid25" id="hdn_ItemID25"/>
            </td>
      </tr>
      <!-- <tr class="topnav"><button class="btn topnavbt" id="btnOk"><i class="fa fa-check"></i> OK</button></tr> -->
      <tr>
            <th style="width:10%;text-align: center;" id="all-check" style="width:4%;" >Select</th>
            <th style="width:20%" >Item Code</th>
            <th style="width:20%" >Name</th>
            <th style="width:10%" >Main UOM</th>
            <th style="width:10%;">Business Unit</th>
            <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD8) && $TabSetting->FIELD8 !=''?$TabSetting->FIELD8:'Add. Info Part No'}}</th>
            <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD9) && $TabSetting->FIELD9 !=''?$TabSetting->FIELD9:'Add. Info Customer Part No'}}</th>
            <th style="width:10%;" {{$AlpsStatus['hidden']}} >{{isset($TabSetting->FIELD10) && $TabSetting->FIELD10 !=''?$TabSetting->FIELD10:'Add. Info OEM Part No.'}}</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td style="width:10%;text-align: center;"><input type="checkbox" class="js-selectall" data-target=".js-selectall1" /></td>
    <td style="width:20%">
    <input type="text" id="Itemcodesearch" class="form-control" onkeyup="ItemCodeFunction()">
    </td>
    <td style="width:20%">
    <input type="text" id="Itemnamesearch" class="form-control" onkeyup="ItemNameFunction()">
    </td>
    <td style="width:10%">
    <input type="text" id="ItemUOMsearch" class="form-control"  onkeyup="ItemUOMFunction()">
    </td>

    <td style="width:10%;"><input type="text" id="ItemBUsearch" class="form-control" onkeyup="ItemBUFunction()"></td>
    <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemAPNsearch" class="form-control" onkeyup="ItemAPNFunction()"></td>
    <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemCPNsearch" class="form-control" onkeyup="ItemCPNFunction()"></td>
    <td style="width:10%;" {{$AlpsStatus['hidden']}} ><input type="text" id="ItemOEMPNsearch" class="form-control" onkeyup="ItemOEMPNFunction()"></td>

    {{-- <td hidden>
    <input type="text" id="ItemQTYsearch" onkeyup="ItemQTYFunction()">
    </td>
    <td>
    <input type="text" id="ItemGroupsearch" onkeyup="ItemGroupFunction()">
    </td>
    <td>
    <input type="text" id="ItemCategorysearch" onkeyup="ItemCategoryFunction()">
    </td>
    <td>
    <input type="text" id="ItemStatussearch" onkeyup="ItemStatusFunction()">
    </td> --}}
    </tr>
    </tbody>
    </table>
      <table id="ItemIDTable2" class="display nowrap table  table-striped table-bordered" style="width: 100%;" >
        <thead id="thead2">

        </thead>
        <tbody id="tbody_ItemID">               
          
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Item Code Dropdown-->

<!-- FORMUP-->
<div id="FORMpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='FORM_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sale Order Details</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="FORMTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
          <tr id="none-select" class="searchalldata" hidden >            
            <td > <input type="text" name="fieldid" id="hdn_FORMid"/>
              <input type="text" name="fieldid2" id="hdn_FORMid2"/>
              <input type="text" name="fieldid3" id="hdn_FORMid3"/>
            </td>
          </tr>
          <tr>
                  <th>Code</th>
                  <th>Date</th>
          </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="FORMcodesearch" class="form-control" onkeyup="FORMCodeFunction()">
    </td>
    <td>
    <input type="text" id="FORMnamesearch" class="form-control" onkeyup="FORMNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="FORMTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">

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

<!-- POPUP END-->


@endsection


@push('bottom-css')
<style>
  

#ItemIDcodesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}
#ItemIDnamesearch {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  font-size: 11px;
  padding: 5px 5px 5px 5px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
}

#ItemIDTable {
  border-collapse: collapse;
  width: 950px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#ItemIDTable th {
    text-align: center;
    padding: 5px;
    font-size: 11px;

    font-weight: 600;
}

#ItemIDTable td {
    text-align: center;
    padding: 5px;
    font-size: 11px;
    font-weight: 600;
}

#ItemIDTable2 {
  border-collapse: collapse;
  width: 1050px;
  border: 1px solid #ddd;
  font-size: 11px;
}

#ItemIDTable2 th{
    text-align: left;
    padding: 5px;
    font-size: 11px;

    font-weight: 600;
}

#ItemIDTable2 td {
  text-align: left;
    padding: 5px;
    font-size: 11px;
    font-weight: 600;
    width: 16%;
}

</style>
@endpush
@push('bottom-scripts')
<script>

//------------------------
//------------------------
//Sub GL Account Starts
//------------------------

let sgltid = "#SubGLTable2";
      let sgltid2 = "#SubGLTable";
      let sglheaders = document.querySelectorAll(sgltid2 + " th");

      // Sort the table element when clicking on the table headers
      sglheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(sgltid, ".clssubgl", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function SubGLCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("subglcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SubGLTable2");
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

  function SubGLNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("subglnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("SubGLTable2");
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

 $('#example2').on('focus','[id*="txtsubgl_popup"]',function(event){

      var id = $(this).attr('id');
      var id2 = $("#"+id).parent().parent().find('[id*="hdnSLIDREF"]').attr('id');      
      
      $('#hdn_subledid').val(id);
      $('#hdn_subledid2').val(id2);
        // $('#hdn_FORMid3').val(id3);

      $("#subglpopup").show();
      event.preventDefault();
  });

$("#subgl_closePopup").on("click",function(event){ 
    $("#subglpopup").hide();
    event.preventDefault();
});

$('.clssubgl').dblclick(function(){
    

    var id = $(this).attr('id');
    var txtval =    $("#txt"+id+"").val();
    var texdesc =   $("#txt"+id+"").data("desc");
   //var texdescname =   $("#txt"+id+"").data("descname");


    var txtid= $('#hdn_subledid').val();
    var txt_id2= $('#hdn_subledid2').val();
    
    //clear row
    $('#'+txtid).parent().parent().find('input:text').val('');
    $('#'+txtid).parent().parent().find('input:hidden').val('');
    $('#'+txtid).parent().parent().find('[id*="dpp_priority"]').prop("selectedIndex",0);
    
    $('#'+txtid).val(texdesc);
    $('#'+txt_id2).val(txtval);
        
    $("#subglpopup").hide();
    $("#subglcodesearch").val(''); 
    $("#subglnamesearch").val(''); 
    SubGLCodeFunction();
   
    event.preventDefault();
});

//Sub GL Account Ends
//------------------------
 
//------------------------
  //Item ID Dropdown
  let itemtid = "#ItemIDTable2";
      let itemtid2 = "#ItemIDTable";
      let itemtidheaders = document.querySelectorAll(itemtid2 + " th");

      // Sort the table element when clicking on the table headers
      itemtidheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(itemtid, ".clsitemid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function ItemCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Itemcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
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

      function ItemNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Itemnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
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


      function ItemPartnoFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("Itempartsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
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

      function ItemUOMFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemUOMsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[3];
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
      function ItemQTYFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemQTYsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[4];
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

      function ItemGroupFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemGroupsearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[5];
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

      function ItemCategoryFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemCategorysearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[6];
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

      function ItemBUFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemBUsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("ItemIDTable2");
	tr = table.getElementsByTagName("tr");
	for (i = 0; i < tr.length; i++) {
	  td = tr[i].getElementsByTagName("td")[7];
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

function ItemAPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemAPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("ItemIDTable2");
	tr = table.getElementsByTagName("tr");
	for (i = 0; i < tr.length; i++) {
	  td = tr[i].getElementsByTagName("td")[8];
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

function ItemCPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemCPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("ItemIDTable2");
	tr = table.getElementsByTagName("tr");
	for (i = 0; i < tr.length; i++) {
	  td = tr[i].getElementsByTagName("td")[9];
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

function ItemOEMPNFunction() {
	var input, filter, table, tr, td, i, txtValue;
	input = document.getElementById("ItemOEMPNsearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("ItemIDTable2");
	tr = table.getElementsByTagName("tr");
	for (i = 0; i < tr.length; i++) {
	  td = tr[i].getElementsByTagName("td")[10];
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

      function ItemStatusFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ItemStatussearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("ItemIDTable2");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[7];
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

  $('#Material').on('focus','[id*="popupITEMID"]',function(event){
    var SLIDREF = $(this).parent().parent().find('[id*="hdnSLIDREF"]').val();

    var SOIDREF = $(this).parent().parent().find('[id*="hdnSOID"]').val();
    //var PIVQuotationNo = $(this).parent().parent().find('[id*="txtPIVQ_popup"]').val();
    if(SLIDREF ===""){
          showAlert('Please select Sub Leader.');
          return false;

    }
    else if(SOIDREF ===""){
          showAlert('Please select Sale Order.');
          return false;

    }
    else
    {
                
        $("#tbody_ItemID").html('Loading...');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
          $.ajax({
              url:'{{route("transaction",[230,"getItemDetails"])}}',
              type:'POST',
              data:{'SLIDREF':SLIDREF, 'SOIDREF':SOIDREF},

              success:function(data) {
                $("#tbody_ItemID").html(data);    
                bindItemEvents();   
                $('.js-selectall').prop('disabled', false);                     
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $("#tbody_ItemID").html('');                        
              },
          }); 
        

        $("#ITEMIDpopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
       // var id4 = $(this).parent().parent().find('[id*="ItemPartno"]').attr('id');
       // var id5 = $(this).parent().parent().find('[id*="Itemspec"]').attr('id');
        var id6 = $(this).parent().parent().find('[id*="itemuom"]').attr('id');
        var id7 = $(this).parent().parent().find('[id*="SLID_REF"]').attr('id');

        //----------------------
        $("#ITEMIDpopup").show();
        var id = $(this).attr('id');
        var id2 = $(this).parent().parent().find('[id*="ITEMID_REF"]').attr('id');
        var id3 = $(this).parent().parent().find('[id*="ItemName"]').attr('id');
        var id4 = $(this).parent().parent().find('[id*="popupMUOM"]').attr('id');
        var id5 = $(this).parent().parent().find('[id*="MAIN_UOMID_REF"]').attr('id');
        var id6 = $(this).parent().parent().find('[id*="SO_QTY"]').attr('id');
        var id7 = $(this).parent().parent().find('[id*="BALSO_QTY"]').attr('id');
       
        //var id4 = $(this).parent().parent().find('[id*="Itemspec"]').attr('id');
        //var id5 = $(this).parent().parent().find('[id*="PI_VQMUOM"]').attr('id');
        // var id6 = $(this).parent().parent().find('[id*="PI_VQMUOMQTY"]').attr('id');
        //var id7 = $(this).parent().parent().find('[id*="PI_VQAUOM"]').attr('id');
        //var id8 = $(this).parent().parent().find('[id*="PI_VQAUOMQTY"]').attr('id');
       
        //var id8 = $(this).parent().parent().find('[id*="PROPLAN_QTY"]').attr('id');
        //var id9 = $(this).parent().parent().find('[id*="dpp_priority"]').attr('id');
        //var id13 = $(this).parent().parent().find('[id*="ALT_UOMID_REF"]').attr('id');
        //var id14 = $(this).parent().parent().find('[id*="ALT_UOMID_QTY"]').attr('id');
        //var id15 = $(this).parent().parent().find('[id*="RATEPUOM"]').attr('id');
        //var id16 = $(this).parent().parent().find('[id*="PO_FQTY"]').attr('id');
        //var id22 = $(this).parent().parent().find('[id*="PENDING_QTY"]').attr('id');

        //var id23 = $(this).parent().parent().find('[id*="hdnSOID"]').attr('id');
        //var id24 = $(this).parent().parent().find('[id*="hdnSQAID"]').attr('id');
        //var id25 = $(this).parent().parent().find('[id*="hdnSEQID"]').attr('id');

        $('#hdn_ItemID').val(id);
        $('#hdn_ItemID2').val(id2);
        $('#hdn_ItemID3').val(id3);
        $('#hdn_ItemID4').val(id4);
        $('#hdn_ItemID5').val(id5);
        $('#hdn_ItemID6').val(id6);
        $('#hdn_ItemID7').val(id7);
        //$('#hdn_ItemID8').val(id8);
        //$('#hdn_ItemID9').val(id9);
        // $('#hdn_ItemID10').val(id10);
        // $('#hdn_ItemID11').val(id11);
        // $('#hdn_ItemID12').val(id12);
        // $('#hdn_ItemID13').val(id13);
        // $('#hdn_ItemID14').val(id14);
        // $('#hdn_ItemID15').val(id15);
        // $('#hdn_ItemID16').val(id16);
        // $('#hdn_ItemID17').val(PIVQuotationNo);
        // $('#hdn_ItemID22').val(id22);

        //$('#hdn_ItemID23').val(id23);
        //$('#hdn_ItemID24').val(id24);
       // $('#hdn_ItemID25').val(id25);

        var r_count = 0;

        var SubGLID = [];
        $('#example2').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            SubGLID.push($(this).find('[id*="hdnSLIDREF"]').val());
          }
        });
        $('#hdn_ItemID18').val(SubGLID.join(', '));
        
        
        var ItemID = [];
        $('#example2').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            ItemID.push($(this).find('[id*="ITEMID_REF"]').val());
          }
        });
        $('#hdn_ItemID19').val(ItemID.join(', '));

        var SORow = [];
        $('#example2').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            SORow.push($(this).find('[id*="hdnSOID"]').val());
            r_count = parseInt(r_count)+1;
            $('#hdn_ItemID20').val(r_count);
          }
        });
        $('#hdn_ItemID21').val(SORow.join(', '));

        var SQAID = [];
        $('#example2').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            SQAID.push($(this).find('[id*="hdnSQAID"]').val());
          }
        });
        $('#hdn_ItemID22').val(SQAID.join(', '));

        var SEQID = [];
        $('#example2').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            SEQID.push($(this).find('[id*="hdnSEQID"]').val());
          }
        });
        $('#hdn_ItemID23').val(SEQID.join(', '));
        event.preventDefault();

    }  //ELSE

        event.preventDefault();

  }); //item focus

  $("#ITEMID_closePopup").click(function(event){
    $("#ITEMIDpopup").hide();
  });





//-----------------------------------
function bindItemEvents()
    {

      $('#ItemIDTable2').off(); 

      $('.js-selectall').change(function()
      { 
        //select all checkbox
        var isChecked = $(this).prop("checked");
        var selector = $(this).data('target');
        $(selector).prop("checked", isChecked);
        
        
        $('#ItemIDTable2').find('.clsitemid').each(function(){
          var fieldid = $(this).attr('id');

          var txtval =   $("#txt"+fieldid+"").val();
          var texdesc =  $("#txt"+fieldid+"").data("desc");
          var fieldid2 = $(this).find('[id*="itemname"]').attr('id');
          var txtname =  $("#txt"+fieldid2+"").val();
          var txtspec =  $("#txt"+fieldid2+"").data("desc");
          var fieldid3 = $(this).find('[id*="itemuom"]').attr('id');
          var txtmuomid =  $("#txt"+fieldid3+"").val();
          var txtmuom =  $(this).find('[id*="itemuom"]').text();

          var apartno =  $("#addinfo"+fieldid+"").data("desc101");
          var cpartno =  $("#addinfo"+fieldid+"").data("desc102");
          var opartno =  $("#addinfo"+fieldid+"").data("desc103");
          
          var txtdynamicid_ref = ''
          var txtrfqid = '';

          var txtsubglid =  $("#txt"+fieldid+"").data("subledid");  //sub leader
          var txtsoqty =  $("#txt"+fieldid+"").data("soqty");
          var txttotppqty =  $("#txt"+fieldid+"").data("totalppqty");
          var txtsoid =  $("#txt"+fieldid+"").data("soid");
          var txtsqaid =  $("#txt"+fieldid+"").data("sqaid");
          var txtseqid =  $("#txt"+fieldid+"").data("seqid");

          var rcount1 = parseInt($(this).closest('table').find('.clsitemid').length);

          var rcount2 = $('#hdn_ItemID20').val();
          var r_count2 = 0;
          if(txtsubglid == undefined)
          {
            txtsubglid = '';
          }
          if(txtsoid == undefined)
          {
            txtsoid = '';
          }
          if(txtsqaid == undefined)
          {
            txtsqaid = '';
          }
          if(txtseqid == undefined)
          {
            txtseqid = '';
          }

         
        var GridRow2 = [];
        $('#example2').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            var   rowitem = $(this).find('[id*="hdnSLIDREF"]').val()+'-'+$(this).find('[id*="ITEMID_REF"]').val()+'-'+$(this).find('[id*="hdnSOID"]').val()+'-'+$(this).find('[id*="hdnSQAID"]').val()+'-'+$(this).find('[id*="hdnSEQID"]').val();
            GridRow2.push(rowitem);
            r_count2 = parseInt(r_count2) + 1;
          }
        });
        
        var subgl_ids =  $('#hdn_ItemID18').val();  
        var itemids =  $('#hdn_ItemID19').val();
        var so_ids =  $('#hdn_ItemID21').val();       
        var sqa_ids =  $('#hdn_ItemID22').val();
        var seq_ids =  $('#hdn_ItemID23').val();

        var rfqids = '';  //notvalid
    
            if($(this).find('[id*="chkId"]').is(":checked") == true) 
            {
              rcount1 = parseInt(rcount2)+parseInt(rcount1);
              if(parseInt(r_count2) >= parseInt(rcount1))
              {
                $('#hdn_ItemID').val('');
                    $('#hdn_ItemID2').val('');
                    $('#hdn_ItemID3').val('');
                    $('#hdn_ItemID4').val('');
                    $('#hdn_ItemID5').val('');
                    $('#hdn_ItemID6').val('');
                    $('#hdn_ItemID7').val('');
                    $('#hdn_ItemID8').val('');
                    $('#hdn_ItemID9').val('');
                    $('#hdn_ItemID10').val('');
                    $('#hdn_ItemID11').val('');
                    $('#hdn_ItemID12').val('');
                    $('#hdn_ItemID13').val('');
                    $('#hdn_ItemID14').val('');
                    $('#hdn_ItemID15').val('');
                    $('#hdn_ItemID16').val('');
                    $('#hdn_ItemID17').val('');
                    $('#hdn_ItemID18').val('');
                    $('#hdn_ItemID19').val('');
                    $('#hdn_ItemID20').val('');
                    $('#hdn_ItemID22').val('');
                    $('#hdn_ItemID23').val('');
                    txtval = '';
                    texdesc = '';
                    txtname = '';
                    txtmuom = '';
                    txtauom = '';
                    txtmuomid = '';

                    txtsubglid ='';
                    txtsoid = '';
                    txtsqaid='';
                    txtseqid='';

                    $('.js-selectall').prop("checked", false);
                    $("#ITEMIDpopup").hide();
                    return false;
              }
              var txtrow_item = txtsubglid+'-'+ txtval+'-'+txtsoid+'-'+txtsqaid+'-'+txtseqid;
              if(jQuery.inArray(txtrow_item, GridRow2) !== -1)
              {
                    $("#ITEMIDpopup").hide();
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('Item already exists.');
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    $('#hdn_ItemID').val('');
                    $('#hdn_ItemID2').val('');
                    $('#hdn_ItemID3').val('');
                    $('#hdn_ItemID4').val('');
                    $('#hdn_ItemID5').val('');
                    $('#hdn_ItemID6').val('');
                    $('#hdn_ItemID7').val('');
                    $('#hdn_ItemID8').val('');
                    $('#hdn_ItemID9').val('');
                    $('#hdn_ItemID10').val('');
                    $('#hdn_ItemID11').val('');
                    $('#hdn_ItemID12').val('');
                    $('#hdn_ItemID13').val('');
                    $('#hdn_ItemID14').val('');
                    $('#hdn_ItemID15').val('');
                    $('#hdn_ItemID16').val('');
                    $('#hdn_ItemID17').val('');
                    $('#hdn_ItemID18').val('');
                    $('#hdn_ItemID19').val('');
                    $('#hdn_ItemID20').val('');
                    $('#hdn_ItemID22').val('');
                    $('#hdn_ItemID23').val('');
                    txtval = '';
                    texdesc = '';
                    txtname = '';
                    txtmuom = '';
                    txtauom = '';
                    txtmuomid = '';

                    txtsubglid ='';
                    txtsoid = '';
                    txtsqaid='';
                    txtseqid='';

                    $('.js-selectall').prop("checked", false);
                    $("#ITEMIDpopup").hide();
                    return false;
              }

              if(subgl_ids.indexOf(txtsubglid) != -1 && itemids.indexOf(txtval) != -1 && so_ids.indexOf(txtsoid) != -1 && sqa_ids.indexOf(txtsqaid) != -1 && seq_ids.indexOf(txtseqid) != -1  )
              {
                            $("#ITEMIDpopup").hide();
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn").hide();
                            $("#OkBtn1").show();
                            $("#AlertMessage").text('Item already exists.');
                            $("#alert").modal('show');
                            $("#OkBtn1").focus();
                            highlighFocusBtn('activeOk1');
                            $('#hdn_ItemID').val('');
                            $('#hdn_ItemID2').val('');
                            $('#hdn_ItemID3').val('');
                            $('#hdn_ItemID4').val('');
                            $('#hdn_ItemID5').val('');
                            $('#hdn_ItemID6').val('');
                            $('#hdn_ItemID7').val('');
                            $('#hdn_ItemID8').val('');
                            $('#hdn_ItemID9').val('');
                            $('#hdn_ItemID10').val('');
                            $('#hdn_ItemID11').val('');
                            $('#hdn_ItemID12').val('');
                            $('#hdn_ItemID13').val('');
                            $('#hdn_ItemID14').val('');
                            $('#hdn_ItemID15').val('');
                            $('#hdn_ItemID16').val('');
                            $('#hdn_ItemID17').val('');
                            $('#hdn_ItemID18').val('');
                            $('#hdn_ItemID19').val('');
                            $('#hdn_ItemID20').val('');
                            $('#hdn_ItemID22').val('');
                            $('#hdn_ItemID23').val('');
                            txtval = '';
                            texdesc = '';
                            txtname = '';
                            txtmuom = '';
                            txtauom = '';
                            txtmuomid = '';

                            txtsubglid ='';
                            txtsoid = '';
                            txtsqaid='';
                            txtseqid='';

                            $('.js-selectall').prop("checked", false);
                            $("#ITEMIDpopup").hide();
                            return false;
              }
                  
                  if($('#hdn_ItemID').val() == "" && txtval != '')
                  {
                    

                    var $tr = $('.material').closest('table');
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
	var name = el.attr('name') || null;
	if(name){
		var nameLength = name.split('_').pop();
		var i = name.substr(name.length-nameLength.length);
		var prefix1 = name.substr(0, (name.length-nameLength.length));
		el.attr('name', prefix1+(+i+1));
	}
});

                        $clone.find('.remove').removeAttr('disabled'); 
                        $clone.find('[id*="popupITEMID"]').val(texdesc);
                        $clone.find('[id*="ITEMID_REF"]').val(txtval);
                        $clone.find('[id*="ItemName"]').val(txtname);
                        $clone.find('[id*="popupMUOM"]').val(txtmuom);
                        $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
                        $clone.find('[id*="SO_QTY"]').val(txtsoqty);
                        $clone.find('[id*="BALSO_QTY"]').val(txttotppqty);

                        $clone.find('[id*="hdnSQAID"]').val(txtsqaid);
                        $clone.find('[id*="hdnSEQID"]').val(txtseqid);

                        $clone.find('[id*="Alpspartno"]').val(apartno);
                        $clone.find('[id*="Custpartno"]').val(cpartno);
                        $clone.find('[id*="OEMpartno"]').val(opartno);
                       
                        $tr.closest('table').append($clone);   
                        var rowCount = $('#Row_Count1').val();
                        rowCount = parseInt(rowCount)+1;
                        $('#Row_Count1').val(rowCount);

                     

                      applyForceNum();
                      event.preventDefault();
                  }
                  else
                  {
                      var txtid= $('#hdn_ItemID').val();
                      var txt_id2= $('#hdn_ItemID2').val();
                      var txt_id3= $('#hdn_ItemID3').val();
                      var txt_id4= $('#hdn_ItemID4').val();
                      var txt_id5= $('#hdn_ItemID5').val();
                      var txt_id6= $('#hdn_ItemID6').val();
                      var txt_id7= $('#hdn_ItemID7').val();
                      

                      $('#'+txtid).val(texdesc);
                      $('#'+txt_id2).val(txtval);
                      $('#'+txt_id3).val(txtname);
                      $('#'+txt_id4).val(txtmuom);
                      $('#'+txt_id5).val(txtmuomid);
                      $('#'+txt_id6).val(txtsoqty);
                      $('#'+txt_id7).val(txttotppqty);
                     
                      $('#'+txtid).parent().parent().find('[id*="hdnSQAID"]').val(txtsqaid);
                      $('#'+txtid).parent().parent().find('[id*="hdnSEQID"]').val(txtseqid);

                      $('#'+txtid).parent().parent().find('[id*="Alpspartno"]').val(apartno);
                      $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
                      $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);
                      
                      
                        // $("#ITEMIDpopup").hide();
                        $('#hdn_ItemID').val('');
                        $('#hdn_ItemID2').val('');
                        $('#hdn_ItemID3').val('');
                        $('#hdn_ItemID4').val('');
                        $('#hdn_ItemID5').val('');
                        $('#hdn_ItemID6').val('');
                        $('#hdn_ItemID7').val('');
                        $('#hdn_ItemID8').val('');
                        $('#hdn_ItemID9').val('');
                        $('#hdn_ItemID10').val('');
                        $('#hdn_ItemID11').val('');
                        $('#hdn_ItemID12').val('');
                        $('#hdn_ItemID13').val('');
                        $('#hdn_ItemID14').val('');
                        $('#hdn_ItemID15').val('');
                        $('#hdn_ItemID16').val('');
                        $('#hdn_ItemID22').val('');
                        $('#hdn_ItemID23').val('');
                        $('#hdn_ItemID24').val('');
                        event.preventDefault();
                  }

                  $('.js-selectall').prop("checked", false);
                  // $("#ITEMIDpopup").reload();
                  $('#ITEMIDpopup').hide();
                  event.preventDefault();
                  
            }
            // else if($(this).is(":checked") == false) 
            // {
            //  UNCHECKED
            // }
          $("#Itemcodesearch").val(''); 
          $("#Itemnamesearch").val(''); 
          $("#ItemUOMsearch").val(''); 
          $("#ItemGroupsearch").val(''); 
          $("#ItemCategorysearch").val(''); 
          $("#ItemStatussearch").val(''); 
          $('.remove').removeAttr('disabled'); 
          ItemCodeFunction();
          event.preventDefault();
        });

        $('#ITEMIDpopup').hide();
        return false;
        event.preventDefault();


    }); //binditem event

    //single check box selected from item popup
    $('[id*="chkId"]').change(function()
    {
        var fieldid = $(this).parent().parent().attr('id');
        var txtval =   $("#txt"+fieldid+"").val();
        var texdesc =  $("#txt"+fieldid+"").data("desc");
        var fieldid2 = $(this).parent().parent().children('[id*="itemname"]').attr('id');
        var txtname =  $("#txt"+fieldid2+"").val();
        var txtspec =  $("#txt"+fieldid2+"").data("desc");
        var fieldid3 = $(this).parent().parent().children('[id*="itemuom"]').attr('id');
        var txtmuomid =  $("#txt"+fieldid3+"").val();
        var txtmuom =  $(this).parent().parent().children('[id*="itemuom"]').text();

        var apartno =  $("#addinfo"+fieldid+"").data("desc101");
        var cpartno =  $("#addinfo"+fieldid+"").data("desc102");
        var opartno =  $("#addinfo"+fieldid+"").data("desc103");
        
        var txtdynamicid_ref = '';
        var txtrfqid = '';

        var txtsubglid =  $("#txt"+fieldid+"").data("subledid");  //sub leader
        var txtsoqty =  $("#txt"+fieldid+"").data("soqty");
        var txttotppqty =  $("#txt"+fieldid+"").data("totalppqty");
        var txtsoid =  $("#txt"+fieldid+"").data("soid");
        var txtsqaid =  $("#txt"+fieldid+"").data("sqaid");
        var txtseqid =  $("#txt"+fieldid+"").data("seqid");

        if(txtsubglid == undefined)
        {
          txtsubglid = '';
        }
        if(txtsoid == undefined)
        {
          txtsoid = '';
        }
        if(txtsqaid == undefined)
        {
          txtsqaid = '';
        }
        if(txtseqid == undefined)
        {
          txtseqid = '';
        }
        
        var GridRow2 = [];
        $('#example2').find('.participantRow').each(function(){
          if($(this).find('[id*="ITEMID_REF"]').val() != '')
          {
            var rowitem = $(this).find('[id*="hdnSLIDREF"]').val()+'-'+$(this).find('[id*="ITEMID_REF"]').val()+'-'+$(this).find('[id*="hdnSOID"]').val()+'-'+$(this).find('[id*="hdnSQAID"]').val()+'-'+$(this).find('[id*="hdnSEQID"]').val();
            GridRow2.push(rowitem);
          }
        });
        
        var subgl_ids =  $('#hdn_ItemID18').val();  
        var itemids =  $('#hdn_ItemID19').val();
        var so_ids =  $('#hdn_ItemID21').val();       
        var sqa_ids =  $('#hdn_ItemID22').val();
        var seq_ids =  $('#hdn_ItemID23').val();

        var rfqids = '';  //notvalid
    
            if($(this).is(":checked") == true) 
            {
              var txtrow_item = txtsubglid+'-'+ txtval+'-'+txtsoid+'-'+txtsqaid+'-'+txtseqid;
              if(jQuery.inArray(txtrow_item, GridRow2) !== -1)
              {
                    $("#ITEMIDpopup").hide();
                    $("#YesBtn").hide();
                    $("#NoBtn").hide();
                    $("#OkBtn").hide();
                    $("#OkBtn1").show();
                    $("#AlertMessage").text('Item already exists.');
                    $("#alert").modal('show');
                    $("#OkBtn1").focus();
                    highlighFocusBtn('activeOk1');
                    $('#hdn_ItemID').val('');
                    $('#hdn_ItemID2').val('');
                    $('#hdn_ItemID3').val('');
                    $('#hdn_ItemID4').val('');
                    $('#hdn_ItemID5').val('');
                    $('#hdn_ItemID6').val('');
                    $('#hdn_ItemID7').val('');
                    $('#hdn_ItemID8').val('');
                    $('#hdn_ItemID9').val('');
                    $('#hdn_ItemID10').val('');
                    $('#hdn_ItemID11').val('');
                    $('#hdn_ItemID12').val('');
                    $('#hdn_ItemID13').val('');
                    $('#hdn_ItemID14').val('');
                    $('#hdn_ItemID15').val('');
                    $('#hdn_ItemID16').val('');
                    $('#hdn_ItemID17').val('');
                    $('#hdn_ItemID18').val('');
                    $('#hdn_ItemID19').val('');
                    $('#hdn_ItemID20').val('');
                    $('#hdn_ItemID22').val('');
                    txtval = '';
                    texdesc = '';
                    txtname = '';
                    txtmuom = '';
                    txtmuomid = '';
                   
                    txtsubglid ='';
                    txtsoid = '';
                    txtsqaid='';
                    txtseqid='';
                    $('.js-selectall').prop("checked", false);
                    $("#ITEMIDpopup").hide();
                    return false;
              }

                 
                      if($('#hdn_ItemID').val() == "" && txtval != '')
                      {
                        var txtid= $('#hdn_ItemID').val();
                        var txt_id2= $('#hdn_ItemID2').val();
                        var txt_id3= $('#hdn_ItemID3').val();
                        var txt_id4= $('#hdn_ItemID4').val();
                        var txt_id5= $('#hdn_ItemID5').val();
                        var txt_id6= $('#hdn_ItemID6').val();
                        var txt_id7= $('#hdn_ItemID7').val();
                        var txt_id8= $('#hdn_ItemID8').val();
                        var txt_id9= $('#hdn_ItemID9').val();
                        var txt_id10= $('#hdn_ItemID10').val();
                        var txt_id11= $('#hdn_ItemID11').val();
                        var txt_id12= $('#hdn_ItemID12').val();
                        var txt_id13= $('#hdn_ItemID13').val();
                        var txt_id14= $('#hdn_ItemID14').val();
                        var txt_id15= $('#hdn_ItemID15').val();
                        var txt_id16= $('#hdn_ItemID16').val();
                        var txt_id22= $('#hdn_ItemID22').val();

                        var $tr = $('.material').closest('table');
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
	var name = el.attr('name') || null;
	if(name){
		var nameLength = name.split('_').pop();
		var i = name.substr(name.length-nameLength.length);
		var prefix1 = name.substr(0, (name.length-nameLength.length));
		el.attr('name', prefix1+(+i+1));
	}
});

                        $clone.find('.remove').removeAttr('disabled'); 
                        
                        $clone.find('[id*="popupITEMID"]').val(texdesc);
                        $clone.find('[id*="ITEMID_REF"]').val(txtval);
                        $clone.find('[id*="ItemName"]').val(txtname);
                        $clone.find('[id*="popupMUOM"]').val(txtmuom);
                        $clone.find('[id*="MAIN_UOMID_REF"]').val(txtmuomid);
                        $clone.find('[id*="SO_QTY"]').val(txtsoqty);
                        $clone.find('[id*="BALSO_QTY"]').val(txttotppqty);

                        $clone.find('[id*="hdnSQAID"]').val(txtsqaid);
                        $clone.find('[id*="hdnSEQID"]').val(txtseqid);

                        $clone.find('[id*="Alpspartno"]').val(apartno);
                        $clone.find('[id*="Custpartno"]').val(cpartno);
                        $clone.find('[id*="OEMpartno"]').val(opartno);

                        $tr.closest('table').append($clone);   
                        var rowCount = $('#Row_Count1').val();
                            rowCount = parseInt(rowCount)+1;
                        $('#Row_Count1').val(rowCount);

                        
                        applyForceNum();
                        event.preventDefault();
                      }
                      else
                      {
                          var txtid= $('#hdn_ItemID').val();
                          var txt_id2= $('#hdn_ItemID2').val();
                          var txt_id3= $('#hdn_ItemID3').val();
                          var txt_id4= $('#hdn_ItemID4').val();
                          var txt_id5= $('#hdn_ItemID5').val();
                          var txt_id6= $('#hdn_ItemID6').val();
                          var txt_id7= $('#hdn_ItemID7').val();
                          

                          $('#'+txtid).val(texdesc);
                          $('#'+txt_id2).val(txtval);
                          $('#'+txt_id3).val(txtname);
                          $('#'+txt_id4).val(txtmuom);
                          $('#'+txt_id5).val(txtmuomid);
                          $('#'+txt_id6).val(txtsoqty);
                          $('#'+txt_id7).val(txttotppqty);
                        
                          $('#'+txtid).parent().parent().find('[id*="hdnSQAID"]').val(txtsqaid);
                          $('#'+txtid).parent().parent().find('[id*="hdnSEQID"]').val(txtseqid);

                          $('#'+txtid).parent().parent().find('[id*="Alpspartno"]').val(apartno);
                          $('#'+txtid).parent().parent().find('[id*="Custpartno"]').val(cpartno);
                          $('#'+txtid).parent().parent().find('[id*="OEMpartno"]').val(opartno);

                          // $("#ITEMIDpopup").hide();
                          $('#hdn_ItemID').val('');
                          $('#hdn_ItemID2').val('');
                          $('#hdn_ItemID3').val('');
                          $('#hdn_ItemID4').val('');
                          $('#hdn_ItemID5').val('');
                          $('#hdn_ItemID6').val('');
                          $('#hdn_ItemID7').val('');
                          $('#hdn_ItemID8').val('');
                          $('#hdn_ItemID9').val('');
                          $('#hdn_ItemID10').val('');
                          $('#hdn_ItemID11').val('');
                          $('#hdn_ItemID12').val('');
                          $('#hdn_ItemID13').val('');
                          $('#hdn_ItemID14').val('');
                          $('#hdn_ItemID15').val('');
                          $('#hdn_ItemID16').val('');
                          $('#hdn_ItemID22').val('');
                            
                      }
                      $('.js-selectall').prop("checked", false);
                      $("#ITEMIDpopup").hide();
                      return false;
                      //event.preventDefault();
            }
            else if($(this).is(":checked") == false) 
            {
                // CHECKBOX UNCHECKED
                
            }
        $("#Itemcodesearch").val(''); 
        $("#Itemnamesearch").val(''); 
        $("#ItemUOMsearch").val(''); 
        $("#ItemGroupsearch").val(''); 
        $("#ItemCategorysearch").val(''); 
        $("#ItemStatussearch").val(''); 
        $('.remove').removeAttr('disabled'); 
        ItemCodeFunction();
        event.preventDefault();
      });
    }




  //Item ID Dropdown Ends
//------------------------
//------------------------
  //SALE ORDER FORM Dropdown
  let frmid = "#FORMTable2";
      let frmid2 = "#FORMTable";
      let frmheaders = document.querySelectorAll(frmid2 + " th");

      // Sort the table element when clicking on the table headers
      frmheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(frmid, ".clssqid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function FORMCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("FORMcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("FORMTable2");
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

  function FORMNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("FORMnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("FORMTable2");
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

  $('#example2').on('focus','[id*="txtSORD_popup"]',function(event){

    var SLID = $.trim( $(this).parent().parent().find('[id*="hdnSLIDREF"]').val() );
    if(SLID ==""){
          showAlert('Please select Sub Leader.');
          return false;
    }

          var id = $(this).attr('id');
          var id2 = id.replace("txtSORD_popup","hdnSOID");
          //var id2 = $("#tdhdnmachine_"+dno).find('[id*="hdnSOID"]').attr('id');      
         // var id3 = $(this).parent().find('[id*="FORMNAME"]').attr('id');      

          $('#hdn_FORMid').val(id);
          $('#hdn_FORMid2').val(id2);
         // $('#hdn_FORMid3').val(id3);
        
          $("#FORMpopup").show();
          $("#tbody_FORM").html('Loading...');
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          })
          $.ajax({
              url:'{{route("transaction",[230,"getso"])}}',
              type:'POST',
              data:{'SLID':SLID},
              success:function(data) {
                $("#tbody_FORM").html(data);
                BindFORMEvents();
              },
              error:function(data){
                console.log("Error: Something went wrong.");
                $("#tbody_FORM").html('');
              },
          });

      });

      $("#FORM_closePopup").click(function(event){
        $("#FORMpopup").hide();
      });

      function BindFORMEvents()
      {
          $(".clsFORMid").dblclick(function(){
              var fieldid = $(this).attr('id');
              var txtval =    $("#txt"+fieldid+"").val();
              var texdesc =   $("#txt"+fieldid+"").data("desc");
              var texdescdate =   $("#txt"+fieldid+"").data("descdate");
              
              var txtid= $('#hdn_FORMid').val();
              var txt_id2= $('#hdn_FORMid2').val();
              //var txt_id3= $('#hdn_FORMid3').val();

              $('#'+txtid).val(texdesc);
              $('#'+txt_id2).val(txtval);
              //$('#'+txt_id3).val(texdescdate);
             
              //clear  CODE 
              $('#'+txtid).parent().parent().find('[id*="hdnSQAID"]').val('');
              $('#'+txtid).parent().parent().find('[id*="hdnSEQID"]').val('');
              $('#'+txtid).parent().parent().find('[id*="popupITEMID"]').val('');
              $('#'+txtid).parent().parent().find('[id*="ITEMID_REF"]').val('');
              $('#'+txtid).parent().parent().find('[id*="ItemName"]').val('');
              $('#'+txtid).parent().parent().find('[id*="popupMUOM"]').val('');
              $('#'+txtid).parent().parent().find('[id*="MAIN_UOMID_REF"]').val('');
              $('#'+txtid).parent().parent().find('[id*="SO_QTY"]').val('');
              $('#'+txtid).parent().parent().find('[id*="BALSO_QTY"]').val('');
              $('#'+txtid).parent().parent().find('[id*="PROPLAN_QTY"]').val('');
              $('#'+txtid).parent().parent().find('[id*="dpp_priority"]').prop('selectedIndex',0);


              $("#FORMpopup").hide();
              
              $("#FORMcodesearch").val(''); 
              $("#FORMnamesearch").val(''); 
              FORMCodeFunction();
              event.preventDefault();
          });
      }
//------------------------


      




//------------------------
     
$(document).ready(function(e) {


  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  
  var last_DT = <?php echo json_encode($objlast_DT[0]->DPP_DT); ?>;
  
  $('#DPPDT').attr("min",last_DT);
  $('#DPPDT').attr("max",today);
  $('#DPPDT').val(last_DT);

  //-------------------------

  var Material = $("#Material").html(); 
  $('#hdnmaterial').val(Material);



  $('#btnAdd').on('click', function() {
    var viewURL = '{{route("transaction",[230,"add"])}}';
                window.location.href=viewURL;
  });

  $('#btnExit').on('click', function() {
    var viewURL = '{{route('home')}}';
                window.location.href=viewURL;
  });




//delete row
$("#Material").on('click', '.remove', function() {
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

//add row
$("#Material").on('click', '.add', function() {

 

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
	var name = el.attr('name') || null;
	if(name){
		var nameLength = name.split('_').pop();
		var i = name.substr(name.length-nameLength.length);
		var prefix1 = name.substr(0, (name.length-nameLength.length));
		el.attr('name', prefix1+(+i+1));
	}
});

  $clone.find('input:text').val('');
  $clone.find('input:hidden').val('');

  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count1').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count1').val(rowCount1);
  $clone.find('.remove').removeAttr('disabled'); 

  applyForceNum();

  event.preventDefault();
});

$("#btnUndo").on("click", function() {
    $("#AlertMessage").text("Do you want to erase entered information in this record?");
    $("#alert").modal('show');

    $("#YesBtn").data("funcname","fnUndoYes");
    $("#YesBtn").show();

    $("#NoBtn").data("funcname","fnUndoNo");
    $("#NoBtn").show();
    
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#NoBtn").focus();
});

    

window.fnUndoYes = function (){
    //reload form
  window.location.href = "{{route('transaction',[230,'add'])}}";
}//fnUndoYes


window.fnUndoNo = function (){
    $("#DPP_NO").focus();
}//fnUndoNo

// growTextarea function: use for testing that the the javascript
// is also copied when row is cloned.  to confirm, 
// type several lines into Location, add a row, & repeat

    function growTextarea (i,elem) {
    var elem = $(elem);
    var resizeTextarea = function( elem ) {
        var scrollLeft = window.pageXOffset || (document.documentElement || document.body.parentNode || document.body).scrollLeft;
        var scrollTop  = window.pageYOffset || (document.documentElement || document.body.parentNode || document.body).scrollTop;  
        elem.css('height', 'auto').css('height', elem.prop('scrollHeight') );
        window.scrollTo(scrollLeft, scrollTop);
    };

    elem.on('input', function() {
        resizeTextarea( $(this) );
    });

    resizeTextarea( $(elem) );
    }

    $('.growTextarea').each(growTextarea);

    //hide rows
    //$("#headingtr2").hide();
   // $("#headingtr3").hide();
    //$(".participantRow").hide();

}); //ready
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {
  applyForceNum();

});



function validateForm(){
 
        $("#FocusId").val('');
        var DPP_NO     =   $.trim($("#DPP_NO").val());
        var DPPDT        =   $.trim($("#DPPDT").val());
       

        if(DPP_NO ===""){
            $("#FocusId").val($("#DPP_NO"));
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please enter value in DPP Number.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else if(DPPDT ===""){
            $("#FocusId").val($("#DPPDT"));
            $("#DPPDT").val();  
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please select DPP Date.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }  
        

        else{

                event.preventDefault();
                var allblank = [];
                var allblank2 = [];
                var allblank3 = [];
                var allblank4 = [];
                var allblank5 = [];

                var allblank10 = [];
                var allblank11 = [];
                var allblank12 = [];

                $('#example2').find('.participantRow').each(function(){
                  if($.trim($(this).find("[id*=hdnSLIDREF]").val())!=""){
                  allblank.push('true');
                  }
                  else{
                    allblank.push('false');
                  } 

                  if($.trim($(this).find("[id*=txtSORD_popup]").val())!=""){
                      allblank11.push('true');
                  }
                  else{
                      allblank11.push('false');
                  } 
                  
                  if($.trim($(this).find("[id*=popupITEMID]").val())!=""){
                      allblank2.push('true');
                  }
                  else{
                      allblank2.push('false');
                  } 

                  var prqty  = $.trim( $(this).find('[id*="PROPLAN_QTY"]').val() );
                  if(isNaN(prqty) || prqty=="" || parseFloat(prqty)<=0)
                  {
                    prqty = "";
                  }

                  if(prqty!=""){
                      allblank3.push('true');
                  }
                  else{
                      allblank3.push('false');
                  } 

                  if($.trim($(this).find("[id*=txtSORD_popup]").val())!=""){
                      allblank10.push('true');
                  }
                  else{
                      allblank10.push('false');
                  }

                  if($.trim($(this).find("[id*=dpp_priority]").val())!=""){
                      allblank12.push('true');
                  }
                  else{
                      allblank12.push('false');
                  }


                  
                }); 

                $('#example2').find('.participantRow').each(function(){
                    
                    
                    var balsqty  = $.trim( $(this).find('[id*="BALSO_QTY"]').val());
                    if(isNaN(balsqty) || balsqty=="" )
                    {
                      balsqty = 0;
                    }

                    var ppqty  = $.trim( $(this).find('[id*="PROPLAN_QTY"]').val());
                    if(isNaN(ppqty) || ppqty=="" )
                    {
                      ppqty = 0;
                    }

                    var soqty  = $.trim( $(this).find('[id*="SO_QTY"]').val() );
                    if(isNaN(soqty) || soqty=="" )
                    {
                      soqty = 0;
                    }

                    var totqty = parseFloat(balsqty) + parseFloat(ppqty);
                    if(parseFloat(totqty)<=parseFloat(soqty) ){
                        allblank5.push('true');
                    }else{
                      allblank5.push('false');
                    }
                        
                }); 
          
                //-------------
          }


        if(jQuery.inArray("false", allblank) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Sub Leader in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        else if(jQuery.inArray("false", allblank10) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select SO No. in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
        }  
        else if(jQuery.inArray("false", allblank2) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Item in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }  
        else if(jQuery.inArray("false", allblank3) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Production Plan Qty shoud be greater than zero in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }  
        else if(jQuery.inArray("false", allblank5) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please check. Sum of Balance SO Qty and Production Plan Qty must be less than or equeal to SO Qty.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }  
        else if(jQuery.inArray("false", allblank12) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Priority in Material Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          } 
          else if(checkPeriodClosing(230,$("#DPPDT").val(),0) ==0){
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
                $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                $("#YesBtn").focus();
                $("#OkBtn").hide();
                highlighFocusBtn('activeYes');
        }

}

$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

$("#btnSave" ).click(function() {
    var formReqData = $("#frm_trn_se");
    if(formReqData.valid()){
      validateForm();
    }
});

window.fnSaveData = function (){

//validate and save data
event.preventDefault();

     var trnseForm = $("#frm_trn_se");
    var formData = trnseForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$("#btnSave").hide(); 
$(".buttonload").show(); 
$("#btnApprove").prop("disabled", true);
$.ajax({
    url:'{{ route("transaction",[230,"save"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
      $(".buttonload").hide(); 
      $("#btnSave").show();   
      $("#btnApprove").prop("disabled", false);
       
        if(data.errors) {
            $(".text-danger").hide();
 
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
            
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").hide();
            $("#OkBtn").show();

            $("#AlertMessage").text(data.msg);
            $(".text-danger").hide();
            $("#alert").modal('show');
            $("#OkBtn").focus();
            
        }
        else 
        {                   
          
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text(data.msg);
            $(".text-danger").hide();
            $("#alert").modal('show');
            $("#OkBtn1").focus();
        }
        
    },
    error:function(data){
        $(".buttonload").hide(); 
        $("#btnSave").show();   
        $("#btnApprove").prop("disabled", false);
        console.log("Error: Something went wrong.");
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Error: Something went wrong.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk1');
    },
});

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
//no button
$("#NoBtn").click(function(){
    $("#alert").modal('hide');
    $("#LABEL").focus();
});

//ok button
$("#OkBtn").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $(".text-danger").hide();
    window.location.href = '{{route("transaction",[230,"index"]) }}';
});

$("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $("#"+$(this).data('focusname')).focus();
    // $("[id*=txtlabel]").focus();
    $(".text-danger").hide();
});

//
function showError(pId,pVal){
    $("#"+pId+"").text(pVal);
    $("#"+pId+"").show();
}
function getFocus(){
    var FocusId=$("#FocusId").val();
    if(focusid!="" && focus!=undefined){
      $("#"+focusid).focus();
    }
    $("#closePopup").click();
}
function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }

$(function(){
  //   var dtToday = new Date();
    
  //   var month = dtToday.getMonth() + 1;
  //   var day = dtToday.getDate();
  //   var year = dtToday.getFullYear();
  //   if(month < 10)
  //       month = '0' + month.toString();
  //   if(day < 10)
  //       day = '0' + day.toString();
    
  //   var minDate= year + '-' + month + '-' + day;
    
  //  $('.DPPDT').attr('max', minDate);

    
});

// item cat popup function





// begin
$('#Material').on('blur',"[id*='PROPLAN_QTY']",function()
{
    var qty2 = $.trim($(this).val());
    if(isNaN(qty2) || qty2=="" )
    {
      qty2 = 0;
    }  
    if(intRegex.test(qty2))
    {
      $(this).val((qty2 +'.000'));
    }
   
    event.preventDefault();
});  

function applyForceNum(){

  

}



//------------------------
 $('#DPP_NO').focusout(function(){
      var DOCNO   =   $.trim($(this).val());
      DOCNO = DOCNO.replace(" ","");
      $(this).val(DOCNO);

      if(DOCNO.indexOf(' ') !== -1){
        $("#FocusId").val('DPP_NO');
        $("#ProceedBtn").focus();
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn1").show();
        $("#AlertMessage").text('Space not allowed in DPP No. Please check.');
        $("#alert").modal('show');
        $("#OkBtn1").focus();
        highlighFocusBtn('activeOk1');
      }

      if(DOCNO ===""){
            $("#FocusId").val('DPP_NO');
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please enter value in DPP No.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            highlighFocusBtn('activeOk1');
      }else{
        checkDuplicateCode();
      }
});


function checkDuplicateCode(){
        
        //validate and save data
        var DPP_NO_VAL = $("#DPP_NO").val();
        //var formData = getDataForm.serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[230,"codeduplicate"])}}',
            type:'POST',
            data:{'DPP_NO':DPP_NO_VAL},
            success:function(data) {
                if(data.exists) {
                    $(".text-danger").hide();
                    showError('ERROR_DPP_NO',data.msg);
                    $("#DPP_NO").focus();
                } 
                if(data.notexists){
                  $(".text-danger").hide();
                }                               
            },
            error:function(data){
              console.log("Error: Something went wrong.");
            },
        });
    }

</script>


@endpush