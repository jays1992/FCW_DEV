@extends('layouts.app')
@section('content')

    <div class="container-fluid topnav">
            
            <div class="row">
                <div class="col-lg-2">
                <a href="{{route('master',[124,'index'])}}" class="btn singlebt">Voucher Type Master</a>
                </div><!--col-2-->

                <div class="col-lg-10 topnav-pd">
                        <a href="#" id="btnSelectedRows" class="btn topnavbt" disabled="disabled"><i class="fa fa-plus"></i> Add</a>
                        <button class="btn topnavbt"  disabled="disabled"><i class="fa fa-pencil-square-o"></i> Edit</button>
                        <button id="btnSaveAttachment"   class="btn topnavbt" tabindex="7"><i class="fa fa-floppy-o"></i> Save</button>
                        <button class="btn topnavbt" id="btnView"  disabled="disabled"><i class="fa fa-eye"></i> View</button>
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-print"></i> Print</a>
                        <button class="btn topnavbt"  id="btnUndo" ><i class="fa fa-undo"></i> Undo</button>
                        <a href="#" class="btn topnavbt" disabled="disabled"><i class="fa fa-times"></i> Cancel</a>
                        <button   class="btn topnavbt" id="btnApproved" disabled="disabled" ><i class="fa fa-thumbs-o-up"></i> Approved</button>
                        <a href="#" class="btn topnavbt"  disabled="disabled"><i class="fa fa-link" ></i> Attachment</a>
                        <a href="{{route('home')}}" class="btn topnavbt"><i class="fa fa-power-off"></i> Exit</a>
                </div><!--col-10-->

            </div><!--row-->
    </div><!--topnav-->	
   
    <div class="container-fluid purchase-order-view filter">     
        <form id="frm_data_attachment" method="post" enctype="multipart/form-data" action='{{route("mastermodify",[124,"docuploads"])}}' > 
          @CSRF
         
          <div class="inner-form">
              <!-- attachment begin -->
            <div class="row">
			<div class="col-lg-1 pl"><p>Voucher Type</p></div>
			<div class="col-lg-2 pl">
        <label> {{$objMstVoucherType[0]->VCODE}} </label>
        <input type="hidden" name="VTID_REF" class="form-control" value='{{$objMstVoucherType[0]->VTID}}' />
       </div>
			
			<div class="col-lg-1 pl"><p>Document No</p></div>
			<div class="col-lg-2 pl">
         <label> {{$objResponse->VCODE}} </label>
        <input type="hidden" name="ATTACH_DOCNO" id="ATTACH_DOCNO" value="{{$objResponse->VTID}}" class="form-control" maxlength="50" >
			</div>
			
			<div class="col-lg-1 pl"><p>Document Date</p></div>
			<div class="col-lg-2 pl">
      <label> {{ date("d/m/Y", strtotime($objResponse->INDATE) )}} </label> 
        <input type="hidden" name="ATTACH_DOCDT" id="ATTACH_DOCDT" value='{{date("Y-m-d", strtotime($objResponse->INDATE))}}'   />

        
			</div>
		</div>
		
		<div class="row">
			<div class="col-lg-6 pl">
                <div class=" table-responsive table-wrapper-scroll-y my-custom-scrollbar2" style="height:350px;" >

                @if(!empty($objAttachments))    
                <table class="display table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
						<thead id="thead1"  style="position: sticky;top: 0">
						  <tr>
							<th width="40%">File Name</th>
							<th>Remarks</th>
              <th width="15%">View / Download</th>
						  </tr>
						</thead>
						<tbody>
                            @foreach($objAttachments as $row)
                            @php
                              $docpath="";
                              $custFileName="";
                              $viewfile = false;
                              if(isset($row->FILESNAME) && !empty($row->FILESNAME)){
                                $fileExt = explode(".",$row->FILESNAME);
                                
                                $viewArr = explode(",",config("erpconst.attachments.allow_view_file"));
                                if(in_array(strtolower($fileExt[1]), $viewArr) ){
                                  $viewfile = true;
                                }else{
                                  $viewfile = false;
                                }
                                  $data = $row->FILESNAME;    
                                  $custFileName = substr($data, strpos($data, "_") + 1);    
                                  $docpath = str_replace('//', '/', $row->LOCATION);
                                  $docpath = $docpath.$row->FILESNAME;                                  
                              }
                            @endphp
                            <tr  class="participantRow">
                              <td>{{isset($custFileName)?$custFileName:''}}</td>
                              <td >{{$row->REMARKS}}</td>
                              @if($viewfile==true)
                              <td align="center" ><a class="btn" title="view" data-toggle="tooltip" data-docpath="{{asset($docpath)}}" onclick="showfile( $(this).data('docpath') )"><i class="fa fa-eye"></i></a></td>
                              @else     
                              <td align="center" ><a class="btn" title="download" data-toggle="tooltip" data-docpath="{{asset($docpath)}}" onclick="downloadfile($(this).data('docpath'))"><i class="fa fa-download"></i></a></td>
                              @endif
                            </tr>
                            @endforeach
                        </tbody>    
                </table>
                @endif
                
                            
                <div style="font-weight:bold;margin-top:10px;">Note: Max size of the loaded file is 2 MB</div>           

                <input type="hidden" name="allow_max_size" id="allow_filesize" value='{{Config("erpconst.attachments.max_size")}}'   />
                <input type="hidden" name="allow_extensions" id="allow_extensions" value='{{Config("erpconst.attachments.allow_extensions")}}' />

					<table id="example2" class="display nowrap table table-striped table-bordered itemlist" width="100%" style="height:auto !important;">
						<thead id="thead1"  style="position: sticky;top: 0">
						  <tr>
							<th width="40%">File Name</th>
							<th>Remarks</th>
							<th width="5%">Action</th>
						  </tr>
						</thead>
						<tbody>
							<tr  class="participantRow">
								<td>
                                    <input type="file" name="FILENAME[]" id="FILENAME_0"  onchange="ValidateSize(this)"  class="form-control w-100" >
                                </td>
								<td><input style="width: 100%"  type="text" name="REMARKS[]" id="REMARKS_0" 	class="form-control w-100"  maxlength="200" ></td>
								<td align="center" >
                <a class="btn add" title="add" data-toggle="tooltip"><i class="fa fa-plus"></i></a>
                <a class="btn remove" title="Delete" data-toggle="tooltip" type="button" ><i class="fa fa-trash" ></i></a>
                </td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
              <!-- attachment end -->

          </div>
        </form>
    </div><!--purchase-order-view-->


@endsection
@section('alert')
<!-- Alert -->
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
            <button class="btn alertbt" name='OkBtn' id="OkBtn" data-focusname="FILENAME_1" style="display:none;margin-left: 90px;">
              <div id="alert-active" class="activeOk"></div>OK</button>
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Alert -->
{{-- file popup begin --}}
<div id="filePopup" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog modal-md"  style="width:850px;height:550px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='closefilePopup' >&times;</button>
        <h4 class="modal-title"> View</h4>
      </div>
      <div class="modal-body">
        <div >
          <iframe id="fileloader" src="" style="border: none;width:100%; height:100%"></iframe>
        </div>         
	    </div>
    </div>
  </div>
</div>
{{-- file popup end --}}
@endsection
<!-- btnSaveAttachment -->

@push('bottom-scripts')
<script>
    function showfile(path){
      $("#fileloader").attr('src','');
      $("#fileloader").attr('src',path);
      $("#filePopup").modal('show');
    }; 

    function downloadfile(path){
      $("#fileloader").attr('src','');
      $("#fileloader").attr('src',path);
    };

    $('#closefilePopup').click(function(){
      $("#fileloader").attr('src','');
      $("#filePopup").modal('hide');
    });	  

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

    $("#example2").on('click', '.add', function() {
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
               // el.attr('name', prefix+(+i+1));
            }
        });
        $clone.find('input:file').val('');
        $tr.closest('table').append($clone);
        event.preventDefault();
    });

   
            
    $("#example2").on('click', '.remove', function() {
    
    var rowCount = $(this).closest('table').find('tbody').length;
    
    if (rowCount > 1) {
        $(this).closest('tbody').remove();
    }
    rowCount --;
        if (rowCount <= 1) {
        $(document).find('.remove').prop('disabled', true);
    }
    event.preventDefault();
    });



 var formDataMst = $( "#frm_data_attachment" );
     formDataMst.validate({
         errorPlacement: function(error, element) {
        }}
    );

     

    

    //validate
    $( "#btnSaveAttachment" ).click(function() {

        if(formDataMst.valid()){
            //set function nane of yes and no btn 
             var allblank = true;
             $('input[name^="FILENAME[]"]').each(function () {
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

    });//btnSaveAttachment

    
    $("#YesBtn").click(function(){

      $("#alert").modal('hide');
      $("#OkBtn").hide();
      var customFnName = $("#YesBtn").data("funcname");
          window[customFnName]();

    }); //yes button

    
    window.fnSaveData = function (){
        
       event.preventDefault();
       $("#frm_data_attachment").submit();
       return true;

    };// fnSaveData


    // save and approve 
    window.fnApproveData = function (){
        
       

    };// fnApproveData

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

        //window.location.href = '{{route("master",[124,"index"]) }}';

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

      $("#VCODE").focus();

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
@if (session('success'))
    
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();

        $("#AlertMessage").text('{{session("success")}}');

        $("#alert").modal('show');
        $("#OkBtn").focus();
    
@endif
@if (session('error'))
    
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();

        $("#AlertMessage").text('{{session("error")}}');

        $("#alert").modal('show');
        $("#OkBtn").focus();
    
@endif
@if (session('duplicate'))
    
        $("#YesBtn").hide();
        $("#NoBtn").hide();
        $("#OkBtn").show();

        $("#AlertMessage").text('{{session("duplicate")}}');

        $("#alert").modal('show');
        $("#OkBtn").focus();
    
@endif
 });
</script> 
@endpush