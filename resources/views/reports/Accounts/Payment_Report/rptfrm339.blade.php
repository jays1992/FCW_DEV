
@extends('layouts.app')
@section('content')
    <div class="container-fluid topnav">
            <div class="row">
                <div class="col-lg-4">
                <a href="{{route('report',[339,'index'])}}" class="btn singlebt">Payment Report</a>
                </div><!--col-2-->
                <div class="col-lg-10 topnav-pd">
                </div>
            </div>
    </div><!--topnav-->	
    <div class="container-fluid purchase-order-view">
        <form id="frm_rpt_payment_register"  method="POST">   
            @csrf
            <div class="container-fluid filter">

                    <div class="inner-form">
                        <div class="row">
                            <div class="col-lg-3 pl"><p>From Date</p></div>
                            <div class="col-lg-3 pl">
                                <input type="date" name="From_Date" id="From_Date" value="{{ old('From_Date') }}" class="form-control mandatory"  placeholder="dd/mm/yyyy" />
                            </div>
                            <div class="col-lg-3 pl"><p>To Date</p></div>
                            <div class="col-lg-3 pl">
                                <input type="date" name="To_Date" id="To_Date" value="{{ old('To_Date') }}" class="form-control mandatory"  placeholder="dd/mm/yyyy" />
                            </div>                                                       
                        </div>
                        <div class="row"> 
                            <div class="col-lg-3 pl"><p>Branch Group</p></div>
                            <div class="col-lg-3 pl">
                                <select name="BranchGroup[]" data-hide-disabled="hide" multiple data-actions-box="true" id="BranchGroup"  class="form-control selectpicker" multiple data-live-search="true"  >
                                    @foreach($objBranchGroup as $bgindex=>$bgRow)
                                        <option value="{{$bgRow->BGID}}" selected>{{$bgRow->BG_CODE}}-{{$bgRow->BG_DESC}}</option>
                                    @endforeach
                                </select>
                            </div>                            
                            <div class="col-lg-3 pl"><p>Branch Name</p></div>
                            <div class="col-lg-3 pl">
                                <select name="BranchName[]" data-hide-disabled="hide" multiple data-actions-box="true" id="BranchName"  class="form-control selectpicker" multiple data-live-search="true"  >
                                    @foreach($objBranch as $bindex=>$bRow)
                                        <option value="{{$bRow->BRID}}" selected>{{$bRow->BRCODE}}-{{$bRow->BRNAME}}</option>
                                    @endforeach
                                </select>
                            </div> 
                        </div>
                        <div class="row"> 
                            <div class="col-lg-3 pl"><p>Bank / Cash</p></div>
                            <div class="col-lg-3 pl" id="div_cust">
                                <select name="BID[]" data-hide-disabled="hide" multiple data-actions-box="true" id="BID" class="form-control selectpicker" multiple data-live-search="true"  >
                                @foreach($objCashBank as $cindex=>$cRow)
                                        <option value="{{$cRow->BID}}" selected>{{$cRow->BCODE}}-{{$cRow->NAME}}</option>
                                @endforeach
                                </select>
                            </div>                            
                            <div class="col-lg-3 pl"><p>General Ledger</p></div>
                            <div class="col-lg-3 pl" id="">
                                <select name="GLID[]" data-hide-disabled="hide"  multiple data-actions-box="true" id="GLID" class="form-control selectpicker" multiple data-live-search="true"  >
									@foreach($objGL as $gindex=>$gRow)
                                        <option value="{{$gRow->GLID}}" selected>{{$gRow->GLCODE}}-{{$gRow->GLNAME}}</option>
									@endforeach
                                </select>
                            </div>
                        </div> 
						<div class="row">               
                            <div class="col-lg-3 pl"><p>Payment For</p></div>
                            <div class="col-lg-3 pl" id="">
                                <select name="PAYMENTFOR[]" data-hide-disabled="hide"  data-actions-box="true" id="PAYMENTFOR" class="form-control selectpicker"  data-live-search="true"  >
                                        <option value="Account" selected>Account</option>
                                        <option value="Customer">Customer</option>
                                        <option value="Vendor">Vendor</option>
                                </select>
                            </div>
							<div class="col-lg-3 pl"><p>Type Of Account</p></div>
                            <div class="col-lg-3 pl" id="">
                                <select name="TYPE[]" data-hide-disabled="hide"  multiple data-actions-box="true" id="TYPE" class="form-control selectpicker" multiple data-live-search="true"  >
                                          <option value="Advance" selected>Advance</option>
										  <option value="Loan" selected>Loan</option>                            
										  <option value="Medical" selected>Medical</option>
										  <option value="Conveyance" selected>Conveyance</option>
										  <option value="Others" selected>Others</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">                           
                            <div class="col-lg-3 pl"><p>Payment No</p></div>
                            <div class="col-lg-3 pl" id="">
                                <select name="PAYMENTID[]" data-hide-disabled="hide" multiple data-actions-box="true" id="PAYMENTID" class="form-control selectpicker" multiple data-live-search="true"  >
                                @foreach($objPaymentNo as $Pindex=>$PRow)
                                        <option value="{{$PRow->PAYMENTID}}" selected>{{$PRow->PAYMENT_NO}}</option>
                                @endforeach
                                </select>
                            </div>
                            

							<div class="col-lg-3 pl"><p>Status</p></div>
                            <div class="col-lg-3 pl">
                                <select name="STATUS" id="STATUS" class="form-control selectpicker">
                                    <option value="A" selected>Approved</option>
                                    <option value="N" >Not Approved</option>
                                    <option value="C" >Cancelled</option>
                                    <option value="R" >Closed</option>
                                </select>
                            </div> 
                        </div> 
                                                                     
                    </div>
                    <div class="inner-form">
                        <div class="row"> </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-4 pl text-center">
                            <button style="display:none"  class="btn topnavbt buttonload" disabled> <i class="fa fa-refresh fa-spin"></i>{{Session::get('report_button')}}</button>
                                <button class="btn topnavbt" id="btnView" {{$objRights->VIEW != 1 ? 'disabled' : ''}}><i class="fa fa-eye"></i> View</button>
                                <input type="hidden" id="Flag" name="Flag" />
                            </div>
                            <div class="col-lg-3"></div>
                        </div>
                    </div>
                    <div class="inner-form">
                        <div class="row">
                            <div class="frame-container col-lg-12 pl text-center" >
                                <button class="iframe-button3" id="btnPrint">
                                    Print
                                </button>
                                <button class="iframe-button" id="btnPdf">
                                    Export to PDF
                                </button>
                                <button class="iframe-button2" id="btnExcel">
                                    Export to Excel
                                </button>
                                <iframe id="iframe_rpt" width="100%" height="1000" >
                                </iframe>
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
    margin-left: 312px !important;
}
.dropdown-toggle{
    height: 30px;
    width: 320px !important;
    border: 2px !important;
    color: black !important;
    font-size: 14px;
    font-weight: 500;
}

.frame-container {
  position: relative;
}
.iframe-button {
  display: none;
  position: absolute;
  top: 15px;
  left: 950px;
  width:150px;
}
.iframe-button2 {
  display: none;
  position: absolute;
  top: 15px;
  left: 1125px;
  width:150px;
}
.iframe-button3 {
  display: none;
  position: absolute;
  top: 15px;
  left: 875px;
  width:50px;
}


</style>
@endpush
@push('bottom-scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<script>


$(document).ready(function(e) {
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

// 



$('#btnPdf').on('click', function() {
    $('#Flag').val('P');
    var Flag = $('#Flag').val();
    var formData = 'Flag='+ Flag;
    var consultURL = '{{route("report",[339,"ViewReport",":rcdId"]) }}';
    consultURL = consultURL.replace(":rcdId",formData);
    window.location.href=consultURL;
    event.preventDefault();
}); 

$('#btnExcel').on('click', function() {
    $('#Flag').val('E');
    var Flag = $('#Flag').val();
    var formData = 'Flag='+ Flag;
    var consultURL = '{{route("report",[339,"ViewReport",":rcdId"]) }}';
    consultURL = consultURL.replace(":rcdId",formData);
    window.location.href=consultURL;
    event.preventDefault();
});

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
        
        var BID = [];
        $("select[name='BID[]']").each(function() {
            var value3 = $(this).val();
            if (value3) {
                BID.push(value3);
            }
        });

        var PAYMENTFOR = [];
        $("select[name='PAYMENTFOR[]']").each(function() {
            var value6 = $(this).val();
            if (value6) {
                PAYMENTFOR.push(value6);
            }
        });
        
        var PAYMENTID = [];
        $("select[name='PAYMENTID[]']").each(function() {
            var value5 = $(this).val();
            if (value5) {
                PAYMENTID.push(value5);
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
        else if(BID  == '')
        {
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please Select Bank / Cash.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else if(PAYMENTFOR  == '')
        {
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please Select Payment For.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else if(PAYMENTID  == '')
        {
            $("#YesBtn").hide();
            $("#NoBtn").hide();
            $("#OkBtn").hide();
            $("#OkBtn1").show();
            $("#AlertMessage").text('Please Select Payment No.');
            $("#alert").modal('show');
            $("#OkBtn1").focus();
            return false;
        }
        else{
            $('#Flag').val('H');
            var trnsoForm = $("#frm_rpt_payment_register");
            var formData = trnsoForm.serialize();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $("#btnView").hide();               
                $(".buttonload").show();
                $.ajax({
                    url:'{{route("report",[339,"ViewReport"])}}',
                    type:'POST',
                    data:formData,
                    success:function(data) {
                        $("#btnView").show();               
                        $(".buttonload").hide();
                        var localS = data;
                        document.getElementById('iframe_rpt').src = "data:text/html;charset=utf-8," + escape(localS);
                        $('#btnPdf').show();
                        $('#btnExcel').show();
                        $('#btnPrint').show();
                    },
                    error:function(data){
                        $("#btnView").show();               
                        $(".buttonload").hide();
                        console.log("Error: Something went wrong.");
                        var localS = "";
                        document.getElementById('iframe_rpt').src = "data:text/html;charset=utf-8," + escape(localS);
                        $('#btnPdf').hide();
                        $('#btnExcel').hide();
                        $('#btnPrint').hide();
                    },
                });
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