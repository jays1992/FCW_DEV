const { ajaxPrefilter } = require("jquery");

var period_closing_msg = "Period already close";

function check_approval_level(REQUEST_DATA,RECORD_ID,editURL){

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var result = $.ajax({
               url:'/check_approval_level',
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

function checkPeriodClosing(form_id,doc_date,flag){

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
    
  var result  = $.ajax({
              url:'/checkPeriodClosing',
              type:'POST',
              async: false,
              dataType: 'json',
              data: {form_id:form_id,doc_date:doc_date},
              done: function(response) {return response;}
          }).responseText;

  if(flag ==0){
    return result;
  }
  else if(flag ==1){
    if(result ==0){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text(period_closing_msg);
      $("#alert").modal('show');
      $("#OkBtn1").focus();
    }
  }
}  

function getDocNoByEvent(docid,date,doc_req){

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.ajax({
    url:'/getDocNoByEvent',
		type:'POST',
		dataType: 'json',
		data: {'REQUEST_DATA':date.value,doc_req:doc_req},
		success:function(result) {

      if(result.FY_FLAG ==false){
				$("#"+docid).val('');
				$("#YesBtn").hide();
				$("#NoBtn").hide();
				$("#OkBtn1").show();
				$("#AlertMessage").text('Please select correct financial year.');
				$("#alert").modal('show');
				$("#OkBtn1").focus();
			}
      else if(result.FLAG ==false){
				$("#"+docid).val('');
				$("#YesBtn").hide();
				$("#NoBtn").hide();
				$("#OkBtn1").show();
				$("#AlertMessage").text('Previous doc date not allow.');
				$("#alert").modal('show');
				$("#OkBtn1").focus();
			}
			else{
        $("#"+docid).val(result.DOC_NO);
			}
		}
		
	});
}

function GetConvFector(ToCurrency){
	

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
    
  var result  = $.ajax({
              url:'/GetConvFector',
              type:'POST',
              async: false,
              dataType: 'json',
              data: {ToCurrency:ToCurrency},
              done: function(response) {return response;}
          }).responseText;


    return result;  

}

function docMissing(FY_FLAG){
  window.onload = function() {
    if(FY_FLAG ==false){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('The document number/financial year does not exist kindly contact to administrator.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
    }
  };
}

function MultiCurrency_Conversion(TotalVal){
  var CurrencyType	=	$("#CRID_REF").val();
  var ConvFact		=	$("#CONVFACT").val();
  var DefaultCurrencyTotal = parseFloat($("#"+TotalVal).val());
  if(CurrencyType != ""){
    $("#multi_currency_section").show(); 
    var CurrencySymbol=    ($("#txtCRID_popup").val()).split("-")[0]; 
    $("#currency_section").html("Total Value in "+CurrencySymbol);  
    if(DefaultCurrencyTotal > 0 && ConvFact > 0){
      TotalValue_Conversion=parseFloat(DefaultCurrencyTotal/ConvFact).toFixed(2); 
      $("#TotalValue_Conversion").val(TotalValue_Conversion);
    }else{
      $("#TotalValue_Conversion").val("0.00");
    }
  }else{
    $("#multi_currency_section").hide(); 
  }
}

function getItemCost(ITEMID_REF,DOC_DATE,TYPE){
	
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
    
  var result  = $.ajax({
              url:'/getItemCost',
              type:'POST',
              async: false,
              dataType: 'json',
              data: {ITEMID_REF:ITEMID_REF,DOC_DATE:DOC_DATE,TYPE:TYPE},
              done: function(response) {return response;}
          }).responseText;


    return result;  

}

$(document).ready(function() {
  $('#CONVFACT').change(function( event ) 
  {    
    if(intRegex.test($(this).val())){
      $(this).val($(this).val()+'.00')
    }
    event.preventDefault();
  });      
});

function check_exist_docno(FY_FLAG){
  window.onload = function() {
    if(FY_FLAG ==false){
      $("#YesBtn").hide();
      $("#NoBtn").hide();
      $("#OkBtn").hide();
      $("#OkBtn1").show();
      $("#AlertMessage").text('The document number does not exist kindly contact to administrator.');
      $("#alert").modal('show');
      $("#OkBtn1").focus();
    }
  };
}

function exist_customer_by_mobile_no(MOBILE_NO,SLID_REF){

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
    
  var result  = $.ajax({
                url:'/exist_customer_by_mobile_no',
                type:'POST',
                async: false,
                dataType: 'json',
                data: {MOBILE_NO:MOBILE_NO,SLID_REF:SLID_REF},
                done: function(response) {return response;}
                }).responseText;

    return result;
}

function read_notification(TABLE_NAME,COLUMN_NAME,DOC_ID,KEY){

  $("#notify_"+KEY).switchClass("bg-primary", "bg-success"); 

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
    
  var result  = $.ajax({
                url:'/read_notification',
                type:'POST',
                async: false,
                dataType: 'json',
                data: {TABLE_NAME:TABLE_NAME,COLUMN_NAME:COLUMN_NAME,DOC_ID:DOC_ID},
                done: function(response) {return response;}
                }).responseText;

  $("#total_notify_count").text(result+'+');

}

function IsEmail(email) {
  var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if(!regex.test(email)) {
    return false;
  }else{
    return true;
  }
}
