!function ($) {
	"use strict";
	$(document).ready(function() {
		// Ajax Swich Layout
		$('#wpo-filter .display a').click(function(){
            var query = $(this).data('query');
            var type = $(this).data('type');
            var $this = $(this);

            if(!$(this).hasClass('active')){
	            $.ajax({
	                url: ajaxurl,
	                data:{action:'wpo_display_layout',query:query,type:type},
	                type: 'POST',
	                beforeSend:function(){
	                	$this.addClass('waiting').append('<span class="loading" style="background:url('+woocommerce_params.ajax_loader_url+') no-repeat center center;display:block;background-size:16px 16px;width:100%;height:100%;position:absolute;top:0;left:0"></span>');
	                },
	                success: function(response){
	                	$this.removeClass('waiting');
	                    $('.products-layout').html(response);
	                    $('#wpo-filter .display a .loading').remove();
	                }
	            });
	        }
	        $('#wpo-filter .display a').removeClass('active');
            $(this).addClass('active');
            return false;
        });
		$(document).ready(function(){
        // Ajax QuickView
			$('a.quickview').click(function (e) {
				e.preventDefault();
			    var productslug = $(this).data('productslug');
			    var url = ajaxurl + '?action=wpo_quickview&productslug=' + productslug;
			     $.get(url,function(data,status){
			     		$('#wpo_modal_quickview .modal-body').html(data);
			     });
			    //$("#quickview-carousel").carousel();
			    //$('#wpo_modal_quickview .modal-body').html('<iframe src="'+url+'"></iframe>' + '</div>');
			});
		})

		$('#wpo_modal_quickview').on('hidden.bs.modal',function(){
			$(this).find('.modal-body').empty().append('<span class="spinner"></span>');
		});

		//Show popup add to cart
		jQuery('body').bind('showNoty', function(){
            var text_success = woocommerce_localize.cart_success;
            var n = noty({
                text        : '<div class=""><i class="fa fa-shopping-cart"></i>&nbsp' + text_success + ' </div>',
                type        : 'success',
                dismissQueue: true,
                layout      : 'center',
                theme       : 'defaultTheme',
                timeout     : 2000,
            });
            console.log('html: ' + n.options.id);
       });
	});
}(jQuery);
