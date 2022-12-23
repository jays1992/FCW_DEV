    //---##-- jQuery Validator method for requirement.

    //string , space
    $.validator.addMethod("StringRegex", function(value, element) {
        return this.optional(element) || /^[a-z\s]+$/i.test(value);
    }, "Only letters");

    //string, number, space, dash, space
    $.validator.addMethod("StringNumberDashedRegex", function(value, element) {
        return this.optional(element) || /^[a-z0-9\-\s]+$/i.test(value);
    }, "Only letters, numbers, or dashes.");

    //string, number, space
    $.validator.addMethod("StringNumberRegex", function(value, element) {
        return this.optional(element) || /^[a-z0-9\s]+$/i.test(value);
    }, "Only letters and numbers allowed");

    //numbers, space
    $.validator.addMethod("OnlyNumberRegex", function(value, element) {
        return this.optional(element) || /^[0-9\s]+$/i.test(value);
    }, "Only no.");

    //numbers, dot
    $.validator.addMethod("OnlyNumberDotRegex", function(value, element) {
        return this.optional(element) || /^[0-9.]+$/i.test(value);
    }, "Only no.");
	
	//email validate
    $.validator.addMethod("EmailValidate", function(value, element) {
        return this.optional(element) || /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/i.test(value);
    }, "Only Email.");


    jQuery.extend(jQuery.validator.messages, {
        required: "Required field",
        remote: "Please fix this field.",
        email: "Please enter a valid email address.",
        url: "Please enter a valid URL.",
        date: "Invalid date.",
        dateISO: "Invalid valid date (ISO).",
        number: "Invalid number.",
        digits: "Only digits.",
        creditcard: "Please enter a valid credit card number.",
        equalTo: "Please enter the same value again.",
        accept: "Please enter a value with a valid extension.",
        maxlength: jQuery.validator.format("Max {0} char"),
        minlength: jQuery.validator.format("Min {0} char"),
        rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
        range: jQuery.validator.format("Please enter a value between {0} and {1}."),
        max: jQuery.validator.format("Must be >= {0}"),
        min: jQuery.validator.format("Must be >= {0}")
    });

    jQuery.fn.ForceNumericOnly =
        function() {
            return this.each(function() {
                $(this).keydown(function(e) {
                    var key = e.charCode || e.keyCode || 0;
                    // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
                    // home, end, period, and numpad decimal
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
		
		//date validate
    $.validator.addMethod("DateValidate", function(value, element) {

        var today = new Date(); 
        var d = new Date(value); 
        today.setHours(0, 0, 0, 0) ;
        d.setHours(0, 0, 0, 0) ;

        if(this.optional(element) || d < today){
            return false;
        }
        else {
            return true;
        }
    }, "Less date not allow");
    //--##--
	
	$.validator.addMethod("OnlyNumberDec", function(value, element) {
        return this.optional(element) || /^[0-9.\s]+$/i.test(value);
    }, "Only no.");
	
	function isNumberKey(e,t){
try {
if (window.event) {
var charCode = window.event.keyCode;
}
else if (e) {
var charCode = e.which;
}
else { return true; }
if (charCode > 31 && (charCode < 48 || charCode > 57)) {
return false;
}
return true;

}
catch (err) {
alert(err.Description);
}
}

function AlphaNumaric(e, t) {
try {
if (window.event) {
var charCode = window.event.keyCode;
}
else if (e) {
var charCode = e.which;
}
else { return true; }
if ((charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122))
return true;
else
return false;
}
catch (err) {
alert(err.Description);
}
}

function AlphaNumaricSpace(e, t) {
try {
if (window.event) {
var charCode = window.event.keyCode;
}
else if (e) {
var charCode = e.which;
}
else { return true; }
if ((charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122) || ( charCode <= 32) )
return true;
else
return false;
}
catch (err) {
alert(err.Description);
}
}


	