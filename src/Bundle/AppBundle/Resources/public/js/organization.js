// $(".createOrganization").click(function () {
//
// var data = $('form').serialize();
//     $.ajax({
//         type: "post",
//         url: Routing.generate('organization_create_ajax'),
//         data: data,
//         success: function (response) {
//
//         }
//     });
//
// });
 function organizationAjaxValidation() {
    

var lease= $('#form-organization');
var error = $('.alert-danger', lease);
var success = $('.alert-success', lease);

lease.validate({
    errorElement: 'span', //default input error message container
    errorClass: 'help-inline', // default input error message class
    focusInvalid: false, // do not focus the last invalid input
    ignore: ".ignore",
    rules: {
        'appbundle_organization[name]': {
            required:true
        }
    },
    invalidHandler: function (event, validator) { //display error alert on form submit
        success.hide();
        error.show();
        Metronic.scrollTo(error, -200);
    },

    highlight: function (element) { // hightlight error inputs
        $(element)
            .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
    },

    unhighlight: function (element) { // revert the change done by hightlight
        $(element)
            .closest('.form-group').removeClass('has-error'); // set error class to the control group
    },

    success: function (label) {
        label
            .addClass('valid') // mark the current input as valid and display OK icon
            .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
    },
    submitHandler: function (form) {
         var data = $('form').serialize();
    $.ajax({
        type: "post",
        url: Routing.generate('organization_create_ajax'),
        data: data,
        success: function (response) {

        }
    });
    }

});

 }