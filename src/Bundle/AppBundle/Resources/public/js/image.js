function loadImage(path) {
        
        $('.defaultLoadImage').hide();
        $('#loadImage').html('<img src="'+ path +'" width="300" height="350">'+'<span class="campaign-successful"></span>');
}