function validationInit(obj){

$('#form-user-profile').validate({
        rules:{
            'fos_user_registration[profile][PhoneNumber]' :
            {
                required:true,
            },

            'fos_user_registration[email]':
            {
                required:true,
                email:true
            }

        },
        messages: {
            'fos_user_registration[profile][PhoneNumber]':
            {
                required: "Please enter the Phone Number",
            },

            'fos_user_registration[email]':
            {
                required:"Please enter an Email Address",
                email:"Please enter a valid Email Address"
            },

        }
    ,
    submitHandler:function(form){
        obj.submit();
        return false;
    }
    });
}