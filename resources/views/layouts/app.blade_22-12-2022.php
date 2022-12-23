<!-- <!DOCTYPE html> -->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name', 'Laravel') }}</title>
<meta name="keywords" content=" ">
<meta name="description" content=" ">
<link href="{{ asset('css/fontawesome.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('fonts/css/all.css') }}" rel="stylesheet">
<link href="{{ asset('css/font-style.css') }}" rel='stylesheet' type='text/css'>
<link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" >
<link href="{{ asset('css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/jquery-ui.css') }}" rel="Stylesheet" type="text/css" />

<link rel="stylesheet" href="{{ asset('css/bootstrapvalidator.min.css') }}">

<link href="{{ asset('css/nav.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/innerdesign.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css">


<link href="{{ asset('css/custom.css') }}" rel="stylesheet" type="text/css">

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>


<script type="text/javascript">
$(document).ready(function () {
	$('.nav-toggle').click(function(e) {		
		$("body").toggleClass("openNav");
		$(".nav-toggle").toggleClass("active");
		e.preventDefault();
	});
	 
      if ($(window).width() < 768) {
      		$("body").removeClass("openNav");
         }
 $(".adminlogindfs").click(function(){
  $(".menuright").toggle(100);
});
	// $('#vertical-menu > li > a').click(function(){
	// 	if ($(this).attr('class') != 'active'){
	// 		$('#vertical-menu li ul').slideUp();
	// 		$(this).next().slideToggle();
	// 		$('#vertical-menu li a').removeClass('active');
	// 		$(this).addClass('active');
	// 	}
	// });

	// $('#vertical-menu > li > ul > li > a').click(function(){
	// 	if ($(this).attr('class') != 'active'){
	// 		$('#vertical-menu li ul li ul').slideUp();
	// 		$(this).next().slideToggle();
	// 		$('#vertical-menu li ul li a').removeClass('active');
	// 		$(this).addClass('active');
	// 	}
	// });
	
	
});
</script>
<script type="text/javascript">
jQuery(document).ready(function () {
	jQuery("#jquery-accordion-menu").jqueryAccordionMenu();
	
});

$(function(){	
	$("#vertical-menu li").click(function(){
		$("#vertical-menu li.active").removeClass("active")
		$(this).addClass("active");
	})	
})	
</script>
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('js/bootstrapValidator.min.js') }}"></script>

<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/additional-methods.min.js') }}"></script>


<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/select-table.js') }}"></script>
<script src="{{ asset('js/menu.js') }}"></script>
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/common.js') }}"></script>
<script type="text/javascript">


jQuery.fn.ForceNumericOnly =
function()
{
    return this.each(function()
    {
        $(this).keydown(function(e)
        {
            var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
            // home, end, period, and numpad decimal
			if(e.altKey || e.ctrlKey || e.metaKey || e.shiftKey){
				key = 0;  //diable special charcters. i.e disbale keycode with shift, ctrl, alt keyand meta key, meta 
			}
            return (
                key == 8 || 
                key == 9 ||
                key == 13 ||
                key == 46 ||
                key == 110 ||
                key == 190 ||
                (key >= 35 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });
    });
};

var intRegex = /^\d+$/;
$( function() {
 $('#example2').on('keyup','.two-digits',function(){
   if($(this).val().indexOf('.')!=-1){         
       if($(this).val().split(".")[1].length > 2){                
		   $(this).val('');
		   $("#alert").modal('show');
			$("#AlertMessage").text('Enter value till two decimal only.');
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
});

$( function() {
 $('#example2').on('keyup','.three-digits',function(){
   if($(this).val().indexOf('.')!=-1){         
       if($(this).val().split(".")[1].length > 3){                
		   $(this).val('');
		   $("#alert").modal('show');
			$("#AlertMessage").text('Enter value till three decimal only.');
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
});

$( function() {
 $('#example2').on('keyup','.five-digits',function(){
   if($(this).val().indexOf('.')!=-1){         
       if($(this).val().split(".")[1].length > 5){                
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
});

$( function() {
 $('#example2').on('keyup','.four-digits',function(){
   if($(this).val().indexOf('.')!=-1){         
       if($(this).val().split(".")[1].length > 4){                
		   $(this).val('');
		   $("#alert").modal('show');
			$("#AlertMessage").text('Enter value till four decimal only.');
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
});






$(function () {
	
	$('#btnExit').on('click', function() {
      var viewURL = '{{route('home')}}';
                  window.location.href=viewURL;
    });


//    $('.datepicker').datepicker({
//     dateFormat: "dd/mm/yy",
//     changeMonth: true,
//     changeYear: true
//    });
});


/* function check_approval_level(REQUEST_DATA,RECORD_ID,editURL){

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
  
    var result = $.ajax({
                 url:'{{ route("transaction",[1001,"check_approval_level"])}}',
                type:'POST',
                async: false,
                dataType: 'json',
                data: {REQUEST_DATA:REQUEST_DATA,RECORD_ID:RECORD_ID},
                done: function(response) {return response;}
                }).responseText;
  
    if(result > 0){
      window.location.href=editURL;
    }
    else{
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('CANNOT EDIT AS THE RECORD IS ALREADY MOVED TO NEXT LEVEL.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
      return false;
    }
  }
  function getDocNoByEvent(docid,date,doc_req){
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.ajax({
		url:'{{ route("transaction",[1001,"getDocNoByEvent"])}}',
		type:'POST',
		dataType: 'json',
		data: {'REQUEST_DATA':date.value,doc_req:doc_req},
		success:function(result) {

			if(result.FLAG ==true){
				$("#"+docid).val(result.DOC_NO);
			}
			else{
				$("#"+docid).val('');
				$("#YesBtn").hide();
				$("#NoBtn").hide();
				$("#OkBtn1").show();
				$("#AlertMessage").text('Previous doc date not allow.');
				$("#alert").modal('show');
				$("#OkBtn1").focus();
			}
		}
		
	});
} */
</script>



@stack('head-scripts')
@stack('head-css')
</head>
<body class="openNav navclsd">
<div class="page">
	<aside class="app-sidebar" role="navigation">
		<div class="main-sidebar-header">
			<div class="desktop-logo">
				<?php
				$logo	=	Session::get('branch_logo');
				$logo	=	asset('http://bsquareappfordemo.com:8888/'.$logo);
				?>
				<a href="#" id="btnExit"><img src="{{$logo}}" class="main-logo" /></a>
			</div>
		</div>
			@include('partials.leftmenu')
      
		</div>
	</aside>
	<div class="main-content">
		<div class="main-header">
			<div class="container-fluid">
				<div class="row">
					<div class="col-sm-1">
					<button href="#" class="hamburger open-panel nav-toggle">
					<span class="screen-reader-text"></span>
					</button>
					</div>
					<img src="{{ asset('img/profile.png') }}" class="adminlogindfs" alt="">
			
					<div class="col-sm-11">
						<ul class="menuright">
							<li><b>Welcome :</b> {{ Auth::user()->DESCRIPTIONS }}</li>
							<!-- <li>,</li> -->
							<!--<li><b>Company:</b> @if(Session::get('company_name')) {{Session::get('company_name')}} @endif</li>-->
							<!-- <li>,</li> -->
							<li><b>Franchise:</b>  @if(Session::get('branch_name')) {{Session::get('branch_name')}} @endif</li>
							<!-- <li>,</li> -->
							<li><b>Login Time :</b> @if(Session::get('login_time')) {{Session::get('login_time')}} @endif</li> 

							@include('partials.notification')

							<li>
								<a class="dropdown-item" href="{{ route('logout') }}"  onclick="event.preventDefault();  document.getElementById('logout-form').submit();">
								<i class="fa fa-sign-out-alt" aria-hidden="true" style="color:#FFF;font-size:16px;" title="Logout"  ></i> Logout
								</a>
								<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
									@csrf
								</form>
							</li>
						</ul>
					</div>
					
				</div>
			</div>
		</div>
		<div class="maintextarea">
			<div class="container-fluid bgdesignsn">
				<div class="row">
					<div class="col-md-12">
						
					</div>
				</div>
				@yield('content')
			</div>
		</div>
	</div> <!-- new-wrapper -->

</div>
<!-- Alert -->
	@yield('alert')
<!-- Alert end-->


	@stack('bottom-scripts')
	@stack('bottom-css')
	<footer>
	<div class="ftrbx">
	<div class="pgmncntainr">
	<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><p>Copyright © 2022. Powered by B-Square Solutions Pvt. Ltd.</p></div>
	<!-- <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right"><p>Powered by B-Square Solutions Pvt. Ltd.</p></div> -->
	</div></div>
	</div>
	</footer>
</body>
</html>