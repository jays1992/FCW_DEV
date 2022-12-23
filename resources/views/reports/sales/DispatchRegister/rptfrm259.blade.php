
@extends('layouts.app')
@section('content')
<!-- <form id="frm_rpt_sir" onsubmit="return validateForm()"  method="POST" class="needs-validation"  >     -->

    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-4">
                <a href="{{route('report',[259,'index'])}}" class="btn singlebt">Dispatch Register</a>
                </div><!--col-2-->
                <div class="col-lg-10 topnav-pd">
                       
                </div>
            </div>
    </div><!--topnav-->	
    <!-- multiple table-responsive table-wrapper-scroll-y my-custom-scrollbar -->
    <div class="container-fluid purchase-order-view">
        <form id="frm_rpt_sir"  method="POST">   
            @csrf
            <!-- {{isset($objSO->SOID[0]) ? method_field('PUT') : '' }} -->
            <div class="container-fluid filter">

                    <div class="inner-form">
                        <div class="row">
                            <div class="col-lg-2 pl"><p>From Date</p></div>
                            <div class="col-lg-2 pl">
                                <input type="date" name="From_Date" id="From_Date" value="{{ old('From_Date') }}" class="form-control mandatory"  placeholder="dd/mm/yyyy" />
                            </div>
                            <div class="col-lg-2 pl"><p>To Date</p></div>
                            <div class="col-lg-2 pl">
                                <input type="date" name="To_Date" id="To_Date" value="{{ old('To_Date') }}" class="form-control mandatory"  placeholder="dd/mm/yyyy" />
                            </div>
                            <div class="col-lg-2 pl"><p>Branch Group</p></div>
                            <div class="col-lg-2 pl">
                                <select name="BranchGroup[]" id="BranchGroup" class="form-control selectpicker" multiple data-live-search="true" >
                                    @foreach($objBranchGroup as $bgindex=>$bgRow)
                                        <option value="{{$bgRow->BGID}}" selected>{{$bgRow->BG_CODE}}-{{$bgRow->BG_DESC}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                        </div>
                        <div class="row">                            
                            <div class="col-lg-2 pl"><p>Branch Name</p></div>
                            <div class="col-lg-2 pl">
                                <select name="BranchName[]" id="BranchName" class="form-control selectpicker" multiple data-live-search="true"  >
                                    @foreach($objBranch as $bindex=>$bRow)
                                        <option value="{{$bRow->BRID}}" selected>{{$bRow->BRCODE}}-{{$bRow->BRNAME}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 pl"><p>Financial Year</p></div>
                            <div class="col-lg-2 pl">
                                <select name="FinancialYear[]" id="FinancialYear" class="form-control selectpicker" multiple data-live-search="true"  >
                                    @foreach($objFYear as $findex=>$fRow)
                                        <option value="{{$fRow->FYID}}" selected>{{$fRow->FYCODE}}-{{$fRow->FYDESCRIPTION}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 pl"><p>Customer</p></div>
                            <div class="col-lg-2 pl">
                                <select name="CUSTOMER[]" id="CUSTOMER" class="form-control selectpicker" multiple data-live-search="true"  >
                                    @foreach($objCustomer as $index=>$Row)
                                        <option value="{{$Row->CID}}" selected>{{$Row->CCODE}}-{{$Row->NAME}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2 pl"><p>Item</p></div>
                            <div class="col-lg-2 pl">
                                <select name="ITEM[]" id="ITEM" class="form-control selectpicker" multiple data-live-search="true"  >
                                    @foreach($objItem as $iindex=>$iRow)
                                    <option value="{{$IRow->ITEMID}}" selected>
                                        {{$IRow->NAME}} ({{$IRow->ICODE}})                                          
                                        @if($company_check=='show')
                                        {{$IRow->ALPS_PART_NO!=''? '-'.$IRow->ALPS_PART_NO:''}}  {{$IRow->BUNAME!=''? '-'.$IRow->BUNAME:''}} 
                                        @endif                                    
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 pl"><p>Format</p></div>
                            <div class="col-lg-2 pl">
                                <select name="Format" id="Format" class="form-control mandatory">
                                    <option value="HTML" selected>HTML</option>
                                    <option value="PDF">PDF</option>
                                    <option value="EXCEL">EXCEL</option>
                                </select>
                            </div>
                            <div class="col-lg-4 pl text-center">
                                <button class="btn topnavbt" id="btnView" {{$objRights->VIEW != 1 ? 'disabled' : ''}}><i class="fa fa-eye"></i> View</button>
                            </div>
                        </div>
                    </div>

                </div>
        </form>
    </div><!--purchase-order-view-->

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

<!-- Alert -->



@endsection


@push('bottom-css')
<style>
.topnavbt {
    margin-left: 112px !important;
}
.dropdown-toggle{
    height: 30px;
    width: 200px !important;
    border: 2px !important;
    color: black !important;
    font-size: 14px;
    font-weight: 500;
}

</style>
@endpush
@push('bottom-scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<script>

$(document).ready(function(e) {
    $('select').selectpicker();
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
      window.location.reload();
   }//fnUndoYes

   window.fnUndoNo = function (){
      $("#From_Date").focus();
   }//fnUndoNo



$('#btnView').on('click', function() {
    var From_Date       = $('#From_Date').val();
        var To_Date         = $('#To_Date').val();
        var BranchGroup = [];
        $("select[name='BranchGroup[]']").each(function() {
            var value = $(this).val();
            if (value) {
                BranchGroup.push(value);
            }
        });

        var BranchName = [];
        $("select[name='BranchName[]']").each(function() {
            var value2 = $(this).val();
            if (value2) {
                BranchName.push(value2);
            }
        });
        
        var FinancialYear = [];
        $("select[name='FinancialYear[]']").each(function() {
            var value3 = $(this).val();
            if (value3) {
                FinancialYear.push(value3);
            }
        });

        var CUSTOMER  = [];
        $("select[name='CUSTOMER[]']").each(function() {
            var value4 = $(this).val();
            if (value4) {
                CUSTOMER.push(value4);
            }
        });

        var ITEM = [];
        $("select[name='ITEM[]']").each(function() {
            var value5 = $(this).val();
            if (value5) {
                ITEM.push(value5);
            }
        });
        
        
        if(From_Date ==="")
        {
            $("#FocusId").val($("#From_Date"));
            $("#From_Date").val('');
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please Select From Date.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else if(To_Date ==="")
        {
            $("#FocusId").val($("#To_Date"));
            $("#To_Date").val('');
            $("#ProceedBtn").focus();
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please Select To Date.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else if(BranchGroup  == '')
        {
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please Select Branch Group.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else if(BranchName  == '')
        {
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please Select Branch.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else if(FinancialYear  == '')
        {
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please Select Financial Year.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else if(CUSTOMER  == '')
        {
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please Select Customer.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else if(ITEM  == '')
        {
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please Select Item.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else{
            var trnsoForm = $("#frm_rpt_sir");
            var formData = trnsoForm.serialize();
            var consultURL = '{{route("report",[259,"ViewReport",":rcdId"]) }}';
            // var formdata = {'SONO': SONO};
            consultURL = consultURL.replace(":rcdId",formData);
            window.location.href=consultURL;
            event.preventDefault();
        }
  });
  $("#OkBtn1").click(function(){
    $("#alert").modal('hide');
    $("#YesBtn").show();
    $("#NoBtn").show();
    $("#OkBtn").hide();
    $("#OkBtn1").hide();
    $(".text-danger").hide();
});

});







</script>


@endpush