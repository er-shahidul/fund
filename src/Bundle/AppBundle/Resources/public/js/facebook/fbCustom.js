 // Socialite.load();

 (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        name: 'Facebook Dialogs';
        js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.7&appId=1107285459319764";

        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));


 (function(d, s, id) {
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) return;
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.7&appId=1107285459319764";
     fjs.parentNode.insertBefore(js, fjs);
 }(document, 'script', 'facebook-jssdk'));

 window.fbAsyncInit = function(){
     FB.init({
         appId: '1107285459319764', status: true, cookie: true, xfbml: true });
 };
 (function(d, debug){var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if(d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id;
     js.async = true;js.src = "//connect.facebook.net/en_US/all" + (debug ? "/debug" : "") + ".js";
     ref.parentNode.insertBefore(js, ref);}(document, /*debug*/ false));
 function postToFeed(title, desc, url, image){
     var obj = {method: 'feed',link: url, picture: 'http://www.url.com/images/'+image,name: title,description: desc};
     function callback(response){}
     FB.ui(obj, callback);
 }
 $('.btnShare').click(function(){
     elem = $(this);
     postToFeed(elem.data('title'), elem.data('desc'), elem.prop('href'), elem.data('image'));

     return false;
 });