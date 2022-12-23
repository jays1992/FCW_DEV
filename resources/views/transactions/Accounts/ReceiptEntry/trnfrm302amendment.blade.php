
@extends('layouts.app')
@section('content')
    

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[169,'index'])}}" class="btn singlebt">Journal Voucher(JV)</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button class="btn topnavbt" id="btnAdd" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt" id="btnEdit" disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button class="btn topnavbt" id="btnSaveJV" ><i class="fa fa-save"></i> Save</button>
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

    <!-- <form id="frm_trn_jv" onsubmit="return validateForm()"  method="POST" class="needs-validation"  > -->
    <div class="container-fluid filter">
      <form id="frm_trn_jv"  method="POST"> 
      @csrf
      {{isset($objJV->JVID[0]) ? method_field('PUT') : '' }}
	<div class="inner-form">
	
		<div class="row">
        <div class="col-lg-1 pl"><p>JV No</p></div>
        <div class="col-lg-2 pl">
              <input type="text" name="JV_NO" id="JV_NO" value="{{ $objJV->JV_NO }}" class="form-control mandatory"  autocomplete="off" readonly style="text-transform:uppercase" autofocus >
         </div>
        <div class="col-lg-1 pl col-md-offset-1"><p>JV Date</p></div>
        <div class="col-lg-2 pl">
                  <input type="date" name="JV_DT" id="JV_DT" value="{{ $objJV->JV_DT}}" class="form-control mandatory"  placeholder="dd/mm/yyyy" />
        </div>
        <div class="col-lg-1 pl"><p> Reverse	</p></div> 
        <div class="col-lg-1 pl">
                  <input type="checkbox" name="REVERSE" id="REVERSE" {{$objJV->REVERSE == 1 ? 'checked' : ''}} />
        </div>
        <div class="col-lg-1 pl"><p>Reverse Date</p></div>
        <div class="col-lg-2 pl">
                  <input type="date" name="REVERSE_DT" id="REVERSE_DT" value="{{ $objJV->REVERSE_DT }}" class="form-control"  placeholder="dd/mm/yyyy" disabled >
        </div>
		</div>
    <div class="row">
        <div class="col-lg-1 pl"><p>Remarks</p></div>
        <div class="col-lg-5 pl">
            <input type="text" name="REMARKS" id="REMARKS" class="form-control" value="{{ $objJV->REMARKS }}"  autocomplete="off"   />
        </div>
        <div class="col-lg-1 pl"><p>Source Doc No</p></div>
        <div class="col-lg-2 pl">
            <input type="text" name="SOURCE_DOCNO" id="SOURCE_DOCNO" class="form-control"  value="{{ $objJV->SOURCE_DOCNO }}"   autocomplete="off" disabled  />
        </div>
        
        <div class="col-lg-1 pl"><p>Source Doc Date</p></div>
        <div class="col-lg-2 pl">
            <input type="date" name="SOURCE_DOCDT" id="SOURCE_DOCDT" autocomplete="off"  value="{{ $objJV->SOURCE_DOCDT }}"  class="form-control"disabled  >
        </div>
    </div>
    <div class="row">
          <div class="col-lg-1 pl"><p>Common Narration</p></div>
          <div class="col-lg-5 pl">
              <input type="text" name="NARRATION" id="NARRATION" class="form-control"  value="{{ $objJV->NARRATION }}"   autocomplete="off"  />                          
          </div>                            
          <div class="col-lg-1 pl"><p>Sub Ledger</p></div>
          <div class="col-lg-1 pl">
              <input type="checkbox" name="SubGL" id="SubGL" disabled  />
              <input type="hidden" name="hdnAccounting" id="hdnAccounting" class="form-control"  autocomplete="off"  />
          </div>
    </div>
	</div>

	<div class="container-fluid">
		<div class="row">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#Accounting">Accounting</a></li> 
        <li><a data-toggle="tab" href="#udf">UDF</a></li>
			</ul>
      <div class="tab-content">
        <div id="Accounting" class="tab-pane fade in active">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="height:280px;margin-top:10px;" >
                <table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                            
                            <tr>
                                <th width="15%">GL/SL<input class="form-control" type="hidden" name="Row_Count1" id ="Row_Count1"></th>
                                <th width="20%">Description</th>
                                <th width="10%">Debit Amount</th>
                                <th width="10%">Credit Amount</th>
                                <th width="20%">Narration</th>
                                <th width="15%">CC Code</th>
                                <th width="10%">Action</th>
                            </tr>
                    </thead>
                    <tbody>
                    @if(!empty($objJVACC))
                      @foreach($objJVACC as $key => $row)
                        <tr  class="participantRow">
                            <td style="text-align:center;" >
                            <input type="text" name={{"txtGLID_".$key}} id={{"txtGLID_".$key}} class="form-control" value="{{$row->GLCODE}}"  autocomplete="off"  readonly/></td>
                            <td hidden><input type="hidden" name={{"GLID_REF_".$key}} id={{"GLID_REF_".$key}}class="form-control" value="{{$row->GLID_REF}}" autocomplete="off" /></td>
                            <td hidden><input type="hidden" name={{"txtflag_".$key}} id={{"txtflag_".$key}} class="form-control" value="{{$row->SGLID_REF}}" autocomplete="off" /></td>
                            <td><input type="text" name={{"Description_".$key}} id={{"Description_".$key}} class="form-control" value="{{$row->NAME}}"  autocomplete="off"  readonly/></td>
                            <td><input type="text" name={{"DR_AMT_".$key}} id={{"DR_AMT_".$key}} class="form-control two-digits" value="{{$row->DR_AMT}}" maxlength="15"  autocomplete="off"  /></td>
                            <td><input type="text" name={{"CR_AMT_".$key}} id={{"CR_AMT_".$key}} class="form-control two-digits" value="{{$row->CR_AMT}}" maxlength="15"  autocomplete="off"  /></td>
                            <td><input type="text" name={{"NARRATION_".$key}} id={{"NARRATION_".$key}} class="form-control" value="{{$row->NARRATION}}"  autocomplete="off"  /></td>
                            <td><input type="text" name={{"txtCostCenter_".$key}} id={{"txtCostCenter_".$key}} class="form-control" value="{{$row->CostCenter}}" autocomplete="off"  readonly/></td>
                            <td hidden><input type="text" name={{"CCID_REF_".$key}} id={{"CCID_REF_".$key}} class="form-control" value="{{$row->CCID_REF}}"  autocomplete="off"  readonly/></td>
                            <td align="center" ><button class="btn add" title="add" data-toggle="tooltip" type="button"><i class="fa fa-plus"></i></button>
                            <button class="btn remove" title="Delete" data-toggle="tooltip" type="button"><i class="fa fa-trash" ></i></button></td>
                        </tr>
                        <tr></tr>
                      @endforeach 
                    @endif
                    </tbody>
            </table>
            </div>	
        </div> 
        <div id="udf" class="tab-pane fade">
            <div class="table-responsive table-wrapper-scroll-y my-custom-scrollbar" style="margin-top:10px;height:280px;width:50%;">
                <table id="example4" class="display nowrap table table-striped table-bordered itemlist" style="height:auto !important;">
                    <thead id="thead1"  style="position: sticky;top: 0">
                    <tr >
                        <th>UDF Fields<input class="form-control" type="hidden" name="Row_Count2" id ="Row_Count2"></th>
                        <th>Value / Comments</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($objJVUDF))
                        @foreach($objJVUDF as $Ukey => $Urow)
                            <tr  class="participantRow4">
                                <td><input type="text" name={{"popupUDFJVID_".$Ukey}} id={{"popupUDFJVID_".$Ukey}}  class="form-control"  autocomplete="off"  readonly/></td>
                                <td hidden><input type="hidden" name={{"UDFJVID_REF_".$Ukey}}  id={{"UDFJVID_REF_".$Ukey}} class="form-control" value="{{$Urow->UDFJVID_REF}}" autocomplete="off" /></td>
                                <td hidden><input type="hidden" name={{"UDFismandatory_".$Ukey}} id={{"UDFismandatory_".$Ukey}} class="form-control" autocomplete="off" /></td>
                                <td id={{"udfinputid_".$Ukey}}>
                                {{-- dynamic input --}} 
                                </td>
                                <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                            </tr>
                            <tr></tr>
                        @endforeach 
                    @else
                        @foreach($objUdfJVData as $uindex=>$uRow)
                          <tr  class="participantRow4">
                              <td><input type="text" name={{"popupUDFJVID_".$uindex}} id={{"popupUDFJVID_".$uindex}} class="form-control" value="{{$uRow->LABEL}}" autocomplete="off"  readonly/></td>
                              <td hidden><input type="hidden" name={{"UDFJVID_REF_".$uindex}} id={{"UDFJVID_REF_".$uindex}} class="form-control" value="{{$uRow->UDFJVID}}" autocomplete="off"   /></td>
                              <td hidden><input type="hidden" name={{"UDFismandatory_".$uindex}} id={{"UDFismandatory_".$uindex}} value="{{$uRow->ISMANDATORY}}" class="form-control"   autocomplete="off" /></td>
                              <td id={{"udfinputid_".$uindex}} >
                                
                              </td>
                              <td align="center" ><button class="btn add UDF" title="add" data-toggle="tooltip" type="button" disabled><i class="fa fa-plus"></i></button><button class="btn remove DUDF" title="Delete" data-toggle="tooltip" type="button" disabled><i class="fa fa-trash" ></i></button></td>
                              
                          </tr>
                          <tr></tr>
                        @endforeach
                    @endif 
                    </tbody>
                </table>
            </div>
        </div> 
      </div>
    </div>
  </div>
		
	</div>
  </form>	
</div>

<!-- </div> -->

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

<!--GL/SL dropdown-->

<div id="glsl_popup" class="modal" role="dialog"  data-backdrop="static">
<div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='gl_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>GL Account</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="GlCodeTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr id="none-select" class="searchalldata" hidden>
            <td> <input type="hidden" name="hdn_GLID" id="hdn_GLID"/>
            <input type="hidden" name="hdn_GLID2" id="hdn_GLID2"/>
            <input type="hidden" name="hdn_GLID3" id="hdn_GLID3"/>
            <input type="hidden" name="hdn_GLID4" id="hdn_GLID4"/></td>
    </tr>
    <tr>
            <th>Code</th>
            <th>Name</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>
    <input type="text" id="glcodesearch" onkeyup="GLCodeFunction()">
    </td>
    <td>
    <input type="text" id="glnamesearch" onkeyup="GLNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="GlCodeTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
        </thead>
        <tbody id="tbody_glsl">
        </tbody>
      </table>
    </div>
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>

<!--Cost Centre dropdown-->

<div id="costpopup" class="modal" role="dialog"  data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='cc_closePopup' >&times;</button>
      </div>
    <div class="modal-body">
	  <div class="tablename"><p>Sub GL Account</p></div>
	  <div class="single single-select table-responsive  table-wrapper-scroll-y my-custom-scrollbar">
    <table id="CostTable" class="display nowrap table  table-striped table-bordered" width="100%">
    <thead>
    <tr id="none-select" class="searchalldata" hidden>
            
            <td> <input type="hidden" name="hdn_CCID" id="hdn_CCID"/>
            <input type="hidden" name="hdn_CCID2" id="hdn_CCID2"/>
            <input type="hidden" name="hdn_CCID3" id="hdn_CCID3"/>
            <input type="hidden" name="hdn_CCID4" id="hdn_CCID4"/>
            </td>
    </tr>
    <tr>
            <th id="all-check" style="width:6.5%; text-align: center;">Select</th>
            <th>Cost Centre Code</th>
            <th>Cost Centre Name</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td></td>
    <td>
    <input type="text" id="cccodesearch" onkeyup="CCCodeFunction()">
    </td>
    <td>
    <input type="text" id="ccnamesearch" onkeyup="CCNameFunction()">
    </td>
    </tr>
    </tbody>
    </table>
      <table id="CostTable2" class="display nowrap table  table-striped table-bordered" width="100%">
        <thead id="thead2">
        </thead>
        <tbody id="tbody_cc">       
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
<style>
#custom_dropdown, #udfforsemst_filter {
    display: inline-table;
    margin-left: 15px;
  }
.dataTables_wrapper .row:nth-child(1) .col-sm-6:nth-child(2){text-align:right;}
#filtercolumn{color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
  }

</style>
@endpush
@push('bottom-scripts')
<script>

//------------------------
  //GL/SL Account
    let tid = "#GlCodeTable2";
    let tid2 = "#GlCodeTable";
    let headers = document.querySelectorAll(tid2 + " th");

      // Sort the table element when clicking on the table headers
      headers.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(tid, ".clsglid", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function GLCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("glcodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("GlCodeTable2");
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

  function GLNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("glnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("GlCodeTable2");
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

  $('#Accounting').on('focus','[id*="txtGLID"]',function(event){
    var SL = $('#GLID_REF').is(':checked');
      $("#tbody_glsl").html('');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[169,"getglsl"])}}',
            type:'POST',
            data:{'SL':SL},
            success:function(data) {
              $("#tbody_glsl").html(data);    
              bindGeneralLedger();                    
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              $("#tbody_glsl").html('');                        
            },
        });
    $("#glsl_popup").show();
    var id = $(this).attr('id');
    var id2 = $(this).parent().parent().find('[id*="GLID_REF"]').attr('id');
    var id3 = $(this).parent().parent().find('[id*="Description"]').attr('id');
    var id4 = $(this).parent().parent().find('[id*="txtflag_"]').attr('id');
    $('#hdn_GLID').val(id);
    $('#hdn_GLID2').val(id2);
    $('#hdn_GLID3').val(id3);
    $('#hdn_GLID4').val(id4);
    event.preventDefault();
  });

  $("#gl_closePopup").click(function(event){
    $("#glsl_popup").hide();
    event.preventDefault();
  });

  function bindGeneralLedger()
  {
    $('#GlCodeTable2').off(); 
    $(".clsglid").dblclick(function(){
      var fieldid = $(this).attr('id');
      var txtid =    $("#txt"+fieldid+"").val();
      var txtval =   $("#txt"+fieldid+"").data("desc");
      var txtdesc =   $("#txt"+fieldid+"").data("desc2");
      var txtflag =   $("#txt"+fieldid+"").data("desc3");

      var txt_id1= $('#hdn_GLID').val();
      var txt_id2= $('#hdn_GLID2').val();
      var txt_id3= $('#hdn_GLID3').val();
      var txt_id4= $('#hdn_GLID4').val();
      
      $('#'+txt_id1).val(txtval);
      $('#'+txt_id2).val(txtid);
      $('#'+txt_id3).val(txtdesc);
      $('#'+txt_id4).val(txtflag);
      $("#glsl_popup").hide();
      $("#glcodesearch").val(''); 
      $("#glnamesearch").val(''); 
      GLCodeFunction();
      
      var customid = txtid;
      
      event.preventDefault();
    });
  }

      

  //GL/SL Account Ends
//------------------------


//------------------------
  //Cost Center Dropdown
    let itemtid = "#CostTable2";
    let itemtid2 = "#CostTable";
    let itemtidheaders = document.querySelectorAll(itemtid2 + " th");

      // Sort the table element when clicking on the table headers
      itemtidheaders.forEach(function(element, i) {
        element.addEventListener("click", function() {
          w3.sortHTML(itemtid, ".clscccd", "td:nth-child(" + (i + 1) + ")");
        });
      });

      function CCCodeFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("cccodesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("CostTable2");
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

      function CCNameFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("ccnamesearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("CostTable2");
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


  $('#Accounting').on('focus','[id*="txtCostCenter"]',function(event){
    var customid = $(this).parent().parent().find('[id*="GLID_REF"]').val();
    if(customid!='')
    {
      $('#tbody_cc').html('<tr><td colspan="2">Please wait..</td></tr>');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:'{{route("transaction",[169,"getCostCenter"])}}',
            type:'POST',
            data:{'customid':customid},
            success:function(data) {
                $('#tbody_cc').html(data);
                bindCostCenter();
            },
            error:function(data){
              console.log("Error: Something went wrong.");
              $('#tbody_cc').html('');
            },
        });        
    }
    var id = $(this).attr('id');
    var id2 = $(this).parent().parent().find('[id*="CCID_REF"]').attr('id');
        $('#hdn_CCID').val(id);
        $('#hdn_CCID2').val(id2);
        $("#costpopup").show();
        event.preventDefault();
  });

      $("#cc_closePopup").click(function(event){
        var txt_id1= $('#hdn_CCID').val();
        var txt_id2= $('#hdn_CCID2').val();
        var txtid= $('#hdn_CCID3').val();
        var txtcode= $('#hdn_CCID4').val();
        
        $('#'+txt_id1).val(txtcode);
        $('#'+txt_id2).val(txtid);
        $("#costpopup").hide();
        $("#cccodesearch").val(''); 
        $("#ccnamesearch").val(''); 
        CCCodeFunction();
      });

    function bindCostCenter(){

      $('#CostTable2').off(); 

      $('[id*="chkId"]').change(function(){
        var fieldid = $(this).parent().parent().attr('id');
        var txtid =   $("#txt"+fieldid+"").val();
        var txtcode =  $("#txt"+fieldid+"").data("desc");
        var txtname =  $("#txt"+fieldid+"").data("desc2");

        var txt_id1= $('#hdn_CCID').val();
        var txt_id2= $('#hdn_CCID2').val();
        var existcode =  $('#'+txt_id1).val();
        var existid =  $('#'+txt_id2).val();
        $('#hdn_CCID4').val(existcode);
        $('#hdn_CCID3').val(existid);

        var txtcost = txtcode;
        
        if($(this).is(":checked") == true) 
        {
          var prid =  $('#hdn_CCID3').val();
          var prcode =  $('#hdn_CCID4').val();
          if(prcode.indexOf(txtcost) != -1)
          {
                $("#costpopup").hide();
                $("#YesBtn").hide();
                $("#NoBtn").hide();
                $("#OkBtn").hide();
                $("#OkBtn1").show();
                $("#AlertMessage").text('Cost Center already exists.');
                $("#alert").modal('show');
                $("#OkBtn1").focus();
                highlighFocusBtn('activeOk1');
                $('.js-selectall').prop("checked", false);
                return false;
          }
          else
          {
            if(prid != '')
            {
              prid = prid+','+txtid;
              prcode = prcode+','+txtcost;
              prid = prid.replace(",,", ",");
              prcode = prcode.replace(",,", ",");
            }
            else
            {
              prid = txtid;
              prcode = txtcost;
              prid = prid.replace(",,", ",");
              prcode = prcode.replace(",,", ",");
            }
            $('#hdn_CCID3').val(prid);
            $('#hdn_CCID4').val(prcode);
          }
            event.preventDefault();
        }
        else if($(this).is(":checked") == false) 
        {
          var prid =  $('#hdn_CCID3').val();
          var prcode =  $('#hdn_CCID4').val();
          if(prcode.indexOf(txtcost) != -1)
          {
            prid = prid.replace(txtid, "");
            prid = prid.replace(",,", ",");
            prcode = prcode.replace(txtcost, "");
            prcode = prcode.replace(",,", ",");
            $('#hdn_CCID3').val(prid);
            $('#hdn_CCID4').val(prcode);
          }
          
            event.preventDefault();
        }
      });
    }

      

  //Item ID Dropdown Ends
//------------------------



      




//------------------------
     
$(document).ready(function(e) {
var Accounting = $("#Accounting").html(); 
$('#hdnAccounting').val(Accounting);

var objlastJVDT = <?php echo json_encode($objlastJVDT[0]->JV_DT); ?>;
var today = new Date(); 
var jvdate = today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + today.getDate()).slice(-2) ;
$('#JV_DT').attr('min',objlastJVDT);
$('#JV_DT').attr('max',jvdate);
$('#JV_DT').val(jvdate);

var jvudf = <?php echo json_encode($objUdfJVData); ?>;
var count2 = <?php echo json_encode($objCountUDF); ?>;
$("#Row_Count1").val(1);
$("#Row_Count2").val(count2);
$('#example4').find('.participantRow4').each(function(){
  var txt_id4 = $(this).find('[id*="udfinputid"]').attr('id');
  var udfid = $(this).find('[id*="UDFJVID_REF"]').val();
  $.each( jvudf, function( jvukey, jvuvalue ) {
    if(jvuvalue.UDFJVID == udfid)
    {
      var txtvaltype2 =   jvuvalue.VALUETYPE;
      var strdyn2 = txt_id4.split('_');
      var lastele2 =   strdyn2[strdyn2.length-1];
      var dynamicid2 = "udfvalue_"+lastele2;
      
      var chkvaltype2 =  txtvaltype2.toLowerCase();
      var strinp2 = '';

      if(chkvaltype2=='date'){
      strinp2 = '<input type="date" placeholder="dd/mm/yyyy" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';       
      }
      else if(chkvaltype2=='time'){
      strinp2= '<input type="time" placeholder="h:i" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';
      }
      else if(chkvaltype2=='numeric'){
      strinp2 = '<input type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"   > ';
      }
      else if(chkvaltype2=='text'){
      strinp2 = '<input type="text" name="'+dynamicid2+ '" id="'+dynamicid2+'" autocomplete="off" class="form-control"  > ';          
      }
      else if(chkvaltype2=='boolean'){            
          strinp2 = '<input type="checkbox" name="'+dynamicid2+ '" id="'+dynamicid2+'" class="" > ';
      }
      else if(chkvaltype2=='combobox'){
      var txtoptscombo2 =   jvuvalue.DESCRIPTIONS;
      var strarray2 = txtoptscombo2.split(',');
      var opts2 = '';
      for (var i = 0; i < strarray2.length; i++) {
          opts2 = opts2 + '<option value="'+strarray2[i]+'">'+strarray2[i]+'</option> ';
      }
      strinp2 = '<select name="'+dynamicid2+ '" id="'+dynamicid2+'" class="form-control" required>'+opts2+'</select>' ;          
      }
      $('#'+txt_id4).html('');  
      $('#'+txt_id4).html(strinp2);
    }
  });
});

$('#btnAdd').on('click', function() {
  var viewURL = '{{route("transaction",[169,"add"])}}';
              window.location.href=viewURL;
});

$('#btnExit').on('click', function() {
  var viewURL = '{{route('home')}}';
              window.location.href=viewURL;
});


$('#REVERSE').on('change', function() {
  if($(this).is(':checked') == true)
  {
    $('#REVERSE_DT').removeAttr('disabled');
  }
  else
  {
    $('#REVERSE_DT').prop('disabled','true');
  }
});
$("#Accounting").on('focusout', "[id*='CR_AMT']", function() 
{
  if($(this).val() != '')
  {
    var amt = $(this).val();
    amt = amt + '.00';
    $(this).val(amt);
    $(this).parent().parent().find("[id*='DR_AMT']").prop('disabled','true');
    $(this).parent().parent().find("[id*='DR_AMT']").val('');
  }
  else
  {
    $(this).parent().parent().find("[id*='DR_AMT']").removeAttr('disabled');
  }
});
$("#Accounting").on('focusout', "[id*='DR_AMT']", function() 
{
  if($(this).val() != '')
  {
    var amt = $(this).val();
    amt = amt + '.00';
    $(this).val(amt);
    $(this).parent().parent().find("[id*='CR_AMT']").prop('disabled','true');
    $(this).parent().parent().find("[id*='CR_AMT']").val('');
  }
  else
  {
    $(this).parent().parent().find("[id*='CR_AMT']").removeAttr('disabled');
  }
});


//delete row
$("#Accounting").on('click', '.remove', function() {
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
$("#Accounting").on('click', '.add', function() {
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
  var d = new Date(); 
  var today = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) ;
  $clone.find('[id*="EDD"]').val(today);
  $tr.closest('table').append($clone);         
  var rowCount1 = $('#Row_Count1').val();
  rowCount1 = parseInt(rowCount1)+1;
  $('#Row_Count1').val(rowCount1);
  $clone.find('.remove').removeAttr('disabled'); 
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
    $("#NoBtn").focus();
});

    

window.fnUndoYes = function (){
    //reload form
  window.location.href = "{{route('transaction',[169,'add'])}}";
}//fnUndoYes


window.fnUndoNo = function (){
    $("#AFSNO").focus();
}//fnUndoNo

});
</script>

@endpush

@push('bottom-scripts')
<script>

$(document).ready(function() {

//   $("#btnSaveJV").on("submit", function( event ) {

//     if ($("#frm_trn_jv").valid()) {
//         // Do something
//         alert( "Handler for .submit() called." );
//         event.preventDefault();
//     }
// });


    $('#frm_trn_jv1').bootstrapValidator({       
        fields: {
            txtlabel: {
                validators: {
                    notEmpty: {
                        message: 'The Enquiry Number is required'
                    }
                }
            },            
        },
        submitHandler: function(validator, form, submitButton) {
            alert( "Handler for .submit() called." );
             event.preventDefault();
             $("#frm_trn_jv").submit();
        }
    });
});



$( "#btnSaveJV" ).click(function() {
  var formJournalVoucher = $("#frm_trn_jv");
  if(formJournalVoucher.valid()){
 
 $("#FocusId").val('');
 var JV_NO          =   $.trim($("#JV_NO").val());
 var JV_DT          =   $.trim($("#JV_DT").val());
 var REVERSE        =   $("#REVERSE").is(':checked');
 var REVERSE_DT     =   $("#REVERSE_DT").val();


 if(JV_NO ===""){
     $("#FocusId").val($("#JV_NO"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please enter value in JV Number.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else if(JV_DT ===""){
     $("#FocusId").val($("#JV_DT"));
     $("#AFSDT").val();  
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select JV Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }  

 else if(REVERSE === true && REVERSE_DT ===""){
  $("#FocusId").val($("#REVERSE_DT"));
     $("#ProceedBtn").focus();
     $("#YesBtn").hide();
     $("#NoBtn").hide();
     $("#OkBtn1").show();
     $("#AlertMessage").text('Please select Reversal Date.');
     $("#alert").modal('show');
     $("#OkBtn1").focus();
     return false;
 }
 else{
    event.preventDefault();
    var allblank = [];
    var allblank2 = [];
    var allblank3 = [];
    

        // $('#udfforsebody').find('.form-control').each(function () {
          $('#example2').find('.participantRow').each(function(){
          if($.trim($(this).find("[id*=txtGLID]").val())!="")
          {
                allblank.push('true');
          }
          else
          {
                allblank.push('false');
          } 
          if($.trim($(this).find("[id*=txtCostCenter]").val())!=""){

                allblank2.push('true');
          }
          else
          {
                allblank2.push('false');
          } 
          if($.trim($(this).find("[id*=CID_REF]").val())!=""){

                allblank3.push('true');

          }
          else
          {
                allblank3.push('false');
          } 

        }); 
    }


        if(jQuery.inArray("false", allblank) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select GL / SL in Accounting Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }
        else if(jQuery.inArray("false", allblank3) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please value of Debit / Credit in Accounting Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
          }  
        else if(jQuery.inArray("false", allblank2) !== -1){
          $("#alert").modal('show');
          $("#AlertMessage").text('Please select Cost Center in Accounting Tab.');
          $("#YesBtn").hide(); 
          $("#NoBtn").hide();  
          $("#OkBtn1").show();
          $("#OkBtn1").focus();
          highlighFocusBtn('activeOk');
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
});

$("#YesBtn").click(function(){

$("#alert").modal('hide');
var customFnName = $("#YesBtn").data("funcname");
    window[customFnName]();

}); //yes button

// $("#btnSaveJV" ).click(function() {
//     var formReqData = $("#frm_trn_jv");
//     if(formReqData.valid()){
//       validateForm();
//     }
// });

window.fnSaveData = function (){

//validate and save data
event.preventDefault();

     var trnseForm = $("#frm_trn_jv");
    var formData = trnseForm.serialize();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$.ajax({
    url:'{{ route("transaction",[169,"update"])}}',
    type:'POST',
    data:formData,
    success:function(data) {
       
        if(data.errors) {
            $(".text-danger").hide();

            if(data.errors.LABEL){
                showError('ERROR_LABEL',data.errors.LABEL);
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();
                        $("#OkBtn1").show();
                        $("#AlertMessage").text('Please enter correct value in Label.');
                        $("#alert").modal('show');
                        $("#OkBtn1").focus();
            }
           if(data.country=='norecord') {

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
            $("#OkBtn").show();

            $("#AlertMessage").text(data.msg);

            $(".text-danger").hide();
            // $("#frm_mst_country").trigger("reset");

            $("#alert").modal('show');
            $("#OkBtn").focus();
            // window.location.href="{{ route('transaction',[90,'index']) }}";
        }
        else if(data.cancel) {                   
            console.log("cancel MSG="+data.msg);
            
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn1").show();

            $("#AlertMessage").text(data.msg);

            $(".text-danger").hide();
            // $("#frm_mst_country").trigger("reset");

            $("#alert").modal('show');
            $("#OkBtn1").focus();
            // window.location.href="{{ route('transaction',[90,'index']) }}";
        }
        else 
        {                   
            console.log("succes MSG="+data.msg);
            
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
    window.location.href = '{{route("transaction",[169,"index"]) }}';
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
    $("#"+FocusId).focus();
    $("#closePopup").click();
}
function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }

    





</script>


@endpush