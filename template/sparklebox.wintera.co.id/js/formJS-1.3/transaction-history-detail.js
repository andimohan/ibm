function TransactionHistoryDetail(opt) {
    var thisObj = this;
    var errMsg = opt.errMsg || {};
    var lang = opt.lang || {};
    
    this.loadOnReady = function loadOnReady() {
        jQuery(document).ready(function () {
            var currentRating = 0;
            
            $('#order-done .review-box').each(function () {
                var parent = $(this);
                var stars = parent.find('.stars');
                var rating = parseInt(stars.data('rating')) || 0;
                updateStars(stars, rating);
            });

            $('#order-done .stars i').on('click', function () {
                var parent = $(this).closest('.review-box');
                var inputRating = parent.find('input[name="hidRating"]');
                currentRating = $(this).data('value');
                inputRating.val(currentRating);

                var button = $(this);
                var tabObj = button.closest('.stars');
                var pkey = tabObj.find('[name=hidItemKey]').val();
                tabObj.find('[name=hidRating]').val(currentRating);
                updateStars(tabObj, currentRating);
            });

            $('#order-done .stars i').on('mouseover', function () {
                var container = $(this).closest('.stars');
                var rating = $(this).data('value');
                updateStars(container, rating, true);
            });

            $('#order-done .stars i').on('mouseout', function () {
                var container = $(this).closest('.stars');
                var currentRating = parseInt(container.find('input[name="hidRating"]').val()) || 0;
                updateStars(container, currentRating);
            });

            function updateStars(container, rating, isHover) {
                container.find('i').each(function () {
                    var value = $(this).data('value');
                    if (value <= rating) {
                        $(this)
                            .removeClass('fa-regular outline')
                            .addClass('fa-solid filled');
                    } else {
                        $(this)
                            .removeClass('fa-solid filled')
                            .addClass('fa-regular');
            
                        if (!isHover && rating > 0) {
                            $(this).addClass('outline');
                        } else {
                            $(this).removeClass('outline');
                        }
                    }
                });
            }

            $('.form-review').each(function () {
                var $formReview = $(this);

                $formReview.bootstrapValidator({
                        feedbackIcons: {
                            valid: 'glyphicon glyphicon-ok',
                            invalid: 'glyphicon glyphicon-remove',
                            validating: 'glyphicon glyphicon-refresh'
                        }
                    })
                    .on('success.form.bv', function (e) {
                        e.preventDefault();
                        var $form = $(e.target);
                        var bv = $form.data('bootstrapValidator');
 
                        $.post($form.attr('action'), $form.serialize(), function (result) {  
                            //alert(result[0]['message']); 
                            window.location.reload();
                            if (!result[0].valid) {
                                $form.data('bootstrapValidator').resetForm();
                            }
                        }, 'json');
                    });
            });

        });
    };
}