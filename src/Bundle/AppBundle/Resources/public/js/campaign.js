function onLoadUserProfile() {
  
    var $url = Routing.generate('campaign_user-profile-verify');

    $('.modal-body').load($url,function(result){

        $('.verify-profile-for-campaign').show();
        $('.verified-profile-for-campaign').hide();
        $('#verify-profile').show();
        $('#verified-profile').hide();
         verifyCheck();
         verifiedCheck();
        $('#ajax').modal({show:true,backdrop: 'static', keyboard: false });
    });



}

if(typeof tokenPhoneVerify == 'undefined' || typeof tokenEmailVerify == 'undefined'){
    onLoadUserProfile();
}



    function verifyCheck() {


        $("#verify-profile").click( function (e) {

            e.preventDefault();
            var datastring = $("#form-user-profile").serialize();
          
            validationInit();
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
            

            return false;
        });
    }
function verifiedCheck() {

        $("#verified-profile").click( function () {

            // var datastring = $("#form-user-profile").serialize();
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
                        $('.emailVerificationCode').hide();
                        $('.verifiedEmail').hide();
                    } else{
                        alert(msg.massage);

                    }
                }
            });
        });
    }

