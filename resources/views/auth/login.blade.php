<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ config('app.name', 'Laravel') }}</title>
<meta name="keywords" content=" ">
<meta name="description" content=" ">

 <!-- Custom fonts for this template-->
 <link href="{{asset('vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="{{asset('css/sb-admin-2.min.css')}}" rel="stylesheet">

    <!-- Bootstrap core JavaScript-->
    <script src="{{asset('vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

<style>
input:focus {
    border-bottom: 1px dashed #fff !important;
}
select:focus {
  border-bottom: 1px dashed #fff !important;
}

</style>
</head>
<body>

<!-- Alert -->
<div id="alert" class="modal"  role="dialog"  data-backdrop="static" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" id='closePopup' >&times;</button>
        <h4 class="modal-title">System Alert Message</h4>
      </div>
      <div class="modal-body">
	  <h5 id="AlertMessage" ></h5>
        <div class="btdiv">
    
        <button class="btn alertbt" name='ProceedBtn' id="ProceedBtn" onClick="getFocus()" style="margin-left: 77px;"  > Proceed</button>
        <input type="hidden" id="FocusId">
           
        </div><!--btdiv-->
		<div class="cl"></div>
      </div>
    </div>
  </div>
</div>
<!-- Alert -->

<script>

function validateForm(){
 
    $("#FocusId").val('');
    var UCODE          =   $.trim($("#UCODE").val());
    var PASSWORD       =   $.trim($("#PASSWORD").val());
    var CYID_REF       =   $.trim($("#CYID_REF").val());
    var BRID_REF       =   $.trim($("#BRID_REF").val());
    var FYID_REF       =   $.trim($("#FYID_REF").val());

    if(UCODE ===""){
        $("#FocusId").val('UCODE');
        $("[id=UCODE]").blur(); 
        $("#msg_alert_box").show();
        $("#msg_alert_box").text('Please enter user id.');
        return false;
    }
    if(existUser(UCODE) ==""){
        $("#FocusId").val('UCODE');
        $("[id=UCODE]").blur(); 
        $("#msg_alert_box").show();
        $("#msg_alert_box").text('User id is not correct. Please enter again.');
        return false;   
    }
    else if(PASSWORD ===""){
        $("#FocusId").val('PASSWORD');
        $("[id=PASSWORD]").blur(); 
        $("#msg_alert_box").show();
        $("#msg_alert_box").text('Password is not correct. Please enter again.');
        return false;
    }
    if(existPass(UCODE,PASSWORD) ==""){
        $("#FocusId").val('PASSWORD');
        $("[id=PASSWORD]").blur(); 
        $("#msg_alert_box").show();
        $("#msg_alert_box").text('Password is not correct. Please enter again.');
        return false;   
    }
    else if(FYID_REF ===""){
        $("#FocusId").val('FYID_REF');
        $("[id=FYID_REF]").blur(); 
        $("#msg_alert_box").show();
        $("#msg_alert_box").text('Please select financial year.');
        return false;
    }
    else if(existBranch(UCODE) <= 0){
        $("#FocusId").val('UCODE');
        $("[id=UCODE]").blur(); 
        $("#msg_alert_box").show();
        $("#msg_alert_box").text('Sorry! Franchise is not mapped with the user, Please contact administrator.');
        return false;
    }
    else{
        event.preventDefault();
        var loginForm = $("#loginForm");
        var formData = loginForm.serialize();
        $.ajax({
            url:'{{ route('login') }}',
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.errors) {
                    if(data.errors.UCODE){
                        $("#FocusId").val('UCODE');
                        $("[id=UCODE]").blur(); 
                        $("#msg_alert_box").show();
                        $("#msg_alert_box").text('Please enter correct user id.');
                    }
                    else if(data.errors.PASSWORD){
                        $("#FocusId").val('PASSWORD');
                        $("[id=PASSWORD]").blur(); 
                        $("#msg_alert_box").show();
                        $("#msg_alert_box").text('Password is not correct. Please enter again.');
                    }
                    else if(data.login=='invalid'){
                        $("#FocusId").val('BRID_REF');
                        $("[id=BRID_REF]").blur(); 
                        $("#msg_alert_box").show();
                        $("#msg_alert_box").text(data.msg);
                    }
                }
                if(data.success) {
                    window.location.href="{{ route('home') }}";
                }
            }
        });

    }

}

function getCompany(){

    $(".msg").remove();

    var UCODE       =   $.trim($("#UCODE").val());

    if(UCODE !=""){
        $.get('get-company',{UCODE:UCODE},function(data){

            if(data ==="Invalid"){
            getBranch('');
            }
            else{
                $("#CYID_REF").html(data);

                var CYID_REF = $('#CYID_REF option:selected').val();
                getBranch(CYID_REF);
               
            }
	    });
    }
    else{
       
        return false;
    }    
}

function getBranch(CYID_REF){

    if(CYID_REF !=""){

        var UCODE       =   $.trim($("#UCODE").val());

        $.get('get-branch',{UCODE:UCODE},function(data){
            $("#BRID_REF").html(data);

        });

        $.get('get-fyear',{UCODE:UCODE},function(data){
            $("#FYID_REF").html(data);
        });
    }
    else{
        $("#BRID_REF").html('');
        $("#FYID_REF").html('');
    }

}

function getFocus(){
    var FocusId=$("#FocusId").val();
    $("#"+FocusId).focus();
    $("#closePopup").click();
}

function existBranch(UCODE){
    var posts = $.ajax({type: 'GET',url:'exist-branch',async: false,dataType: 'json',data: {UCODE:UCODE},done: function(response) {return response;}}).responseText;
    return posts;
}

function existUser(UCODE){
    var posts = $.ajax({type: 'GET',url:'exist-user',async: false,dataType: 'json',data: {UCODE:UCODE},done: function(response) {return response;}}).responseText;
    return posts;
}

function existPass(UCODE,PASSWORD){
    var posts = $.ajax({type: 'GET',url:'exist-pass',async: false,dataType: 'json',data: {UCODE:UCODE,PASSWORD:PASSWORD},done: function(response) {return response;}}).responseText;
    return posts;
}

$(function() {
    setTimeout(function() {
        getCompany();
  }, 500);
});

</script>
<!-- Page Wrapper -->
<div id="wrapper">
    <div class="bodywrapper">
        <div class="container">
            <!-- Outer Row -->
            <div class="row justify-content-center">
                <div class="col-xl-5 col-lg-5 col-md-6">
                    <div class="card o-hidden border-0 shadow-lg">
                        <div class="card-body log_bg p-0">
                            <!-- Nested Row within Card Body -->
                            <div class="row">
                                <div class="col-lg-12"><img src="{{asset('img/white-logo1.png')}}" class="logo"></div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="p-5">

                                    
                                        <div class="text-center"><h1 class="h4 text-gray-900 mb-4">Login Here!</h1></div>

                                        <div class="alert alert-danger" role="alert" id="msg_alert_box" style="display:none;"></div>
                                
                                        <form class="user" name="login" method="POST" class="form-horizontal"  id="loginForm" onsubmit="return validateForm()"  >
                                            @csrf
                                            <div class="form-group">
                                                <input id="UCODE" type="text" class="form-control form-control-user" name="UCODE" value="{{ old('UCODE') }}" autocomplete="off" placeholder="User ID" onfocusout="getCompany()" tabindex="1" >
                                            </div>

                                            <div class="form-group">
                                                <input id="PASSWORD" type="PASSWORD" class="form-control form-control-user @error('PASSWORD') is-invalid @enderror" onfocusout="getCompany()" name="PASSWORD" autocomplete="current-PASSWORD" placeholder="Password"  tabindex="2"  >
                                            </div>

                                            <div class="form-group" hidden>
                                                <select class="form-control form-control-user" id="CYID_REF" name="CYID_REF"  tabindex="3"  >
                                                    <option value="" selected>Company</option>
                                                </select>
                                            </div>

                                            <div class="form-group" hidden >
                                                <select class="form-control form-control-user" id="BRID_REF" name="BRID_REF" tabindex="4" >
                                                    <option value="" selected>Branch</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <select class="form-control form-control-user"  id="FYID_REF" name="FYID_REF" tabindex="5" >
                                                    <option value="" selected>Financial Year</option>
                                                </select>
                                            </div>

                                            <button type="submit" name="submit" class="btn btn-primary btn-user btn-block" tabindex="6"> Login </button>
                                            <hr>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
