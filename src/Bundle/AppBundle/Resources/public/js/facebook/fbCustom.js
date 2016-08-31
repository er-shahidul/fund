 // Socialite.load();
 (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        name: 'Facebook Dialogs';
        js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.7&appId="+facebookClientId;

        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));


 (function(d, s, id) {
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) return;
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.7&appId="+facebookClientId;
     fjs.parentNode.insertBefore(js, fjs);
 }(document, 'script', 'facebook-jssdk'));

 window.fbAsyncInit = function(){
     FB.init({
         appId: facebookClientId, status: true, cookie: true, xfbml: true });
 };

 (function(d, debug){var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if(d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id;
     js.async = true;js.src = "//connect.facebook.net/en_US/all" + (debug ? "/debug" : "") + ".js";
     ref.parentNode.insertBefore(js, ref);}(document, /*debug*/ false));



 function postToFeed(title, desc, url, image){
     console.log(title);
         var obj = {
                        method: 'share',
                        href: url,
                        picture: image,
                        caption: title,
                        description: desc
                    };
     function callback(response){}
     FB.ui(obj, callback);

 }

 $('.btnShare').click(function(){
     elem = $(this);
     postToFeed($('#page-title').text(), $('#page-detail').text(), elem.prop('href'), $('#page-image').prop('src'));
     return false;
 });
