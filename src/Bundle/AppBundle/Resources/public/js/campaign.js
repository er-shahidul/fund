function onLoadUserProfile() {
  
    var $url = Routing.generate('campaign_user-profile-verify');

    $('.modal-body').load($url,function(result){

        $('.verify-profile-for-campaign').show();
        $('.verified-profile-for-campaign').hide();
        $('#verify-profile').show();
        $('#verified-profile').hide();
         verifyCheck();
         verifiedCheck();
         organizationAjaxValidation();
        $('#ajax').modal({show:true,backdrop: 'static', keyboard: false });
    });
}

if(typeof tokenPhoneVerify == 'undefined' || typeof tokenEmailVerify == 'undefined'){
    onLoadUserProfile();
}



    function verifyCheck() {
        var lease = $('#form-user-profile');
        var error = $('.alert-danger', lease);
        var success = $('.alert-success', lease);

        lease.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-inline', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: ".ignore",
            rules: {
                'fos_user_registration[profile][PhoneNumber]': {
                    required:true,
                    number:true
                },
                'fos_user_registration[email]': {
                    required:true,
                    email:true
                },
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

                success.show();
                error.hide();
                var datastring = $("#form-user-profile").serialize();

                $.ajax({
                    type: "post",
                    url: Routing.generate('campaign_user-verify'),
                    data: datastring,
                    dataType: 'json',
                    success: function (msg) {

                        if (msg.responseCode == 202) {

                            $('.verify-profile-for-campaign').hide();
                            $('.verified-profile-for-campaign').show();

                            $('#verify-profile').hide();
                            $('#verified-profile').show();
                        }
                    }
                });

            }

        });

    }

function verifiedCheck() {

    var lease = $('#form-user-profile-verified');
    var error = $('.alert-danger', lease);
    var success = $('.alert-success', lease);

    lease.validate({
        errorElement: 'span', //default input error message container
        errorClass: 'help-inline', // default input error message class
        focusInvalid: false, // do not focus the last invalid input
        ignore: ".ignore",
        rules: {
            'phoneVerificationCode': {
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

            success.show();
            error.hide();
            var phone = $('.phoneVerificationCode').val();
            var email = $('.emailVerificationCode').val();

            $.ajax({
                type: "post",
                url: Routing.generate('campaign_user-verified'),
                data: {phone:phone,email:email},
                dataType: 'json',
                success: function (msg) {
                    if(msg.responseCode == 202){
                        alert('successfully verified');
                        location.reload();
                        $('.emailVerificationCode').hide();
                        $('.verifiedEmail').hide();

                    } else{
                        alert(msg.massage);

                    }
                }
            });

        }

    });
    }

