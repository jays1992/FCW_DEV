@extends('layouts.app')
@section('content')

    <div class="container-fluid topnav">            
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('transaction',[418,'index'])}}" class="btn singlebt">Serial No & Barcode (IN)</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <button id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-edit"></i> Edit</button>
                        <button id="btnSave"   class="btn topnavbt" tabindex="7"><i class="fa fa-save"></i> Save</button>
                        <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-print"></i> Print</button>
                        <button class="btn topnavbt"  id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
                        <button class="btn topnavbt" disabled="disabled"><i class="fa fa-times"></i> Cancel</button>
                        <button   class="btn topnavbt" id="btnApproved" disabled="disabled" ><i class="fa fa-lock"></i> Approved</button>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</button>
                        <a href="{{route('home')}}" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
        <form id="frm_import" method="post" enctype="multipart/form-data" action='{{route("transactionmodify",[418,"importexcelindb"])}}' > 
          @CSRF
         
          <div class="inner-form">
            <div class="row">
            <div class="col-lg-1 pl"><p>Voucher Type</p></div>
            <div class="col-lg-2 pl">
              <label> {{$objMstVoucherType[0]->VCODE}} </label>
              <input type="hidden" name="VTID_REF" class="form-control" value='{{$objMstVoucherType[0]->VTID}}' />
            </div>
            <div class="col-lg-2 pl"><p>Excel Sample File</p></div>
            <div class="col-lg-4 pl">
              <a href="{{ route("transactionmodify",[418,"downloadExcelFormate"]) }}"  >download</a>              
            </div>            
          </div>

          @if(session('logerror'))
          <div class="row">
            <div class="col-lg-4 pl">
              <span style="font-weight: bold;color:#ff0000;">Data insertion error Please check log file : </span><a href="{{ asset(session("logerror")) }}" target="_blank" >View</a>
            </div>            
          </div>
          @endif
          @if(session('logsuccess'))
          <div class="row">
            <div class="col-lg-4 pl">
              <span style="font-weight: bold;color:#29dd47;">Data insreted successfully. : </span>
            </div>            
          </div>
          @endif
		
		<div class="row">
			<div class="col-lg-6 pl">
                <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar2" style="height:558px;" >
                <div style="font-weight:bold;margin-top:10px;">Note: Max size of the loaded file is 2 MB</div>           

                <input type="hidden" name="allow_max_size" id="allow_filesize" value='2'   />
                <input type="hidden" name="allow_extensions" id="allow_extensions" value='xls,xlsx' />

					<table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="400px" style="height:auto !important;">
						<thead id="thead1"  style="position: sticky;top: 0">
						  <tr>
							<th width="40%">File Name</th>
							</tr>
						</thead>
						<tbody>
							<tr  class="participantRow">
								<td>
                    <input type="file" name="FILENAME" id="FILENAME_0"  onchange="ValidateSize(this)"  class="form-control w-100" >
                </td>								
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
              <!--  end -->

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
            <button class="btn alertbt" name='OkBtn' id="OkBtn" data-focusname="FILENAME_1" style="display:none;margin-left: 90px;">
              <div id="alert-active" class="activeOk"></div>OK</button>
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

    function ValidateSize(file) {

                if(! ( $(file).val() ) )
                  return false;
                var configAllowSize   =  $("#allow_filesize").val();
                var allowSize = configAllowSize * 1024 * 1024; // in MB
        
                var configAllowExt    =  $("#allow_extensions").val();
                var validExtensions = configAllowExt.split(",");

                var ferror = "";   
                var fsize = file.files[0].size,
                    ftype = file.files[0].type,
                    fname = file.files[0].name,
                //fextension = fname.substring(fname.lastIndexOf('.')+1);
                fextension = fname.substring(fname.lastIndexOf('.')+1).toLowerCase();  

                    if ($.inArray(fextension, validExtensions) == -1){

                        $(file).val(''); 
                        $(file).blur(); 

                        $("#OkBtn").data('focusname',$(file).attr('id'));

                        $("#alert").modal('show');
                        $("#AlertMessage").text('This type of files are not allowed!');
                        $("#YesBtn").hide();
                        $("#NoBtn").hide();

                        $("#OkBtn").show();
                        $("#OkBtn").focus();

                        return false;
                    }else{
                        if(fsize > allowSize){/*1048576-1MB(You can change the size as you want)*/

                            // alert("File size too large! Please upload less than "+configAllowSize+"MB");
                            //this.value = "";
                            $(file).val(''); 
                            $(file).blur(); 

                            $("#OkBtn").data('focusname',$(file).attr('id'));

                            $("#alert").modal('show');
                            $("#AlertMessage").text("File size too large! Please upload less than "+configAllowSize+"MB");
                            $("#YesBtn").hide();
                            $("#NoBtn").hide();
                            $("#OkBtn").show();
                            $("#OkBtn").focus();

                            return false;
                        }
                        return true;
                    }
    }//validate
    //-------------

    
    var formCountryMst = $( "#frm_import" );
     formCountryMst.validate({
         errorPlacement: function(error, element) {
        }}
    );
    

    

    //validate
    $( "#btnSave" ).click(function() {

        if(formCountryMst.valid()){
            //set function nane of yes and no btn 
             var allblank = true;
             $('input[name^="FILENAME"]').each(function () {
                 if($(this).val()){
                    allblank = false;
                 }    
            });

            if(allblank){
            
                $("#alert").modal('show');
                $("#AlertMessage").text('Please select a file.');
                $("#YesBtn").hide(); 
                $("#NoBtn").hide();  

                $("#OkBtn").show();
                $("#OkBtn").focus();
                highlighFocusBtn('activeOk');
                
            }else{

                $("#alert").modal('show');
                $("#AlertMessage").text('Do you want to save to record.');
                $("#YesBtn").data("funcname","fnSaveData");  //set dynamic fucntion name
                $("#YesBtn").focus();

                $("#OkBtn").hide();
                highlighFocusBtn('activeYes');

            }

            

        }

    });//btnSave

    
    $("#YesBtn").click(function(){

      $("#alert").modal('hide');
      $("#OkBtn").hide();
      var customFnName = $("#YesBtn").data("funcname");
          window[customFnName]();

    }); //yes button

    
    window.fnSaveData = function (){
        
       event.preventDefault();
       $("#frm_import").submit();
       return true;

    };// fnSaveData


   

    //no button
    $("#NoBtn").click(function(){

      $("#alert").modal('hide');
      var custFnName = $("#NoBtn").data("funcname");
          window[custFnName]();

    }); //no button

   
    $("#OkBtn").click(function(){

        $("#alert").modal('hide');
        $("#YesBtn").show();  //reset
        $("#NoBtn").show();   //reset
        
       $("#"+$(this).data('focusname')).focus();
        
        //alert();
        $(".text-danger").hide();

        //window.location.href = '{{route("transaction",[4,"index"]) }}';

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

      $("#CID").focus();

   }//fnUndoNo


    //
    function showError(pId,pVal){

      alert("p=="+pId);  
      $("#"+pId+"").text(pVal);
      $("#"+pId+"").show();

    }  

    function highlighFocusBtn(pclass){
       $(".activeYes").hide();
       $(".activeNo").hide();
       
       $("."+pclass+"").show();
    }


$( document ).ready(function() {
@if (session('logsuccess'))    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").show();
    $("#AlertMessage").text('Data inserted successfully.');
    $("#alert").modal('show');
    $("#OkBtn").focus();    
@endif
@if (session('logerror'))    
    $("#YesBtn").hide();
    $("#NoBtn").hide();
    $("#OkBtn").show();
    $("#AlertMessage").text('Error in Import data. Please check log.');
    $("#alert").modal('show');
    $("#OkBtn").focus();    
@endif
 });
</script> 
@endpush