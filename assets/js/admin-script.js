function _checkAndSave(val){
    let  cUrl = "https://combopos.co.uk/wp-json/posAdmin/v1/restaurants/check/"+val;
    Swal.fire({
        title: 'Checking !',
        html: 'We are checking you license.Please wait.',
        timerProgressBar: false,
        onBeforeOpen: () => {
            Swal.showLoading();
            jQuery.get(cUrl,function (response) {

                Swal.getTitle().textContent = 'Awesome !';
                Swal.getContent().textContent = 'Your license is valid. We are activating the restaurant.';

                let rData = response.data[0];

                let getData = {
                    action: 'save_option',
                    cPosSecurity: cPosSecurity,
                    rId: rData.restaurant_id,
                    rLc: rData.restaurant_lc,
                    rStatus: rData.restaurant_status,
                };
                jQuery.ajax({
                    method: "POST",
                    url: ajaxUrl,
                    data:getData,
                    dataType: "json",
                    success: function(data) {
                        if (data.success){
                            Swal.fire({
                                icon: 'success',
                                title: 'Success...',
                                text: 'Your licence has been saved ! ',
                                timer: 3000
                            });
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong or you license already been applied !',
                                timer: 3000
                            });
                        }
                    },
                    error: function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong !',
                            timer: 3000
                        });
                    },
                })
            }).fail(function(error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Your license is not valid ! ',
                    timer: 3000
                });
            });
        },
    });
}

jQuery(function ($) {

    // Cpos Tab Jquery
    $('.cpos-tab').on('click', function(evt) {
        evt.preventDefault();
        //$(this).toggleClass('active');
        let sel = this.getAttribute('data-toggle-target');
        let allA = $(".cpos-plugin-wrap .tab-header-wrap a");

        if ($(this).hasClass('active')){
            $(this).removeClass('active');
        }else{
            allA.removeClass('active');
            $(this).addClass('active');
        }
        $('.cpos-tab-content').removeClass('active').filter(sel).addClass('active');
    });

    // Cpos Ajax Save and check data
    $("#check-lc").submit(function (_e) {
        _e.preventDefault();
        let _flc  = jQuery("#cpos-lc").val();
        if (_flc !== ""){
            _checkAndSave(_flc);
        }else{
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Field should not be empty !',
                timer: 3000
            })
        }
    })

});