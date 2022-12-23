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

    //--##--