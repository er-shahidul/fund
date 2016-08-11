 // Socialite.load();

    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        name: 'Facebook Dialogs';
        js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.7&appId=1107285459319764";

        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

document.getElementById('shareBtn').onclick = function() {
    FB.ui({
        method: 'share',
        mobile_iframe: true,
        href: shareUrl,
    }, function (response) {
    });
}