function loadImage(path) {
console.log(path);
    // $('.defaultLoadImage').hide();
    $('#loadImage').html('<img src="'+ path +'" width="338" height="185">'+'<span class="campaign-successful">Successful</span>');

}
$('.feature').click( function () {

    $.ajax({
        type: "post",
        url: Routing.generate('campaign_featured'),
        data: {campaign:$(this).attr("rel")},
        dataType: 'json',
        success: function (msg) {
            console.log(msg);
            if(msg == 'Featured'){
                $('.featurei').hide()
                $('.un-feature').show();
            } else {
                $('.un-feature').hide()
                $('.featurei').show();
            }
        }
    });

});