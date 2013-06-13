;(function($){

    $(document).ready(function(){

        $('#register_form').on('click', '#advanced_settings', function(){

            $('#register_form .advanced').toggleClass('hide');
            $('#register_form .advanced input').toggleDisabled();

        });

    });

    $.fn.toggleDisabled = function(){
        return this.each(function(){
            this.disabled = !this.disabled;
        });
    };

})(jQuery);