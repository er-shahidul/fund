function onLoadUserProfile() {
  
    var $url = Routing.generate('campaign_user-profile-verify');
    
    $('.modal-body').load($url,function(result){
        $('#ajax').modal({show:true,backdrop: 'static', keyboard: false});
    });
}
onLoadUserProfile();