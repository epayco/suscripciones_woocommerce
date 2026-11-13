(function($){

    $('.subscription_epayco_se_enable').click(function(e){
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url:  ajaxurl,
            data: 'action=subscription_epayco_se',
            dataType: 'json',
            beforeSend: () =>{
                swal.fire({
                    title: '',
                    onOpen: () => {
                        swal.showLoading()
                    },
                    allowOutsideClick: false
                });
            },
            success: (r) =>{
                if (r.status){
                    swal.fire({
                        title: 'Successfully activated',
                        text: 'redirecting to settings...',
                        type: 'success',
                        showConfirmButton: false
                    });
                    window.location.replace(subscriptionepayco.urlConfig);
                }else{
                    swal.fire({
                        title: 'Installation error',
                        text: 'The "WooCommerce Subscriptions" plugin could not be downloaded and activated, please try again or do it manually',
                        type: 'warning'
                    });
                }
            }
        });
    });
})(jQuery);