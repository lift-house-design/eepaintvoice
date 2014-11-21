// Mobile menu
var animate_duration=300,
    is_animating=false;

$('.show-menu').on('click',function(){
    if(!is_animating)
    {
        is_animating=true;

        var $menu=$('#layout-nav .nav'),
            $show_menu=$('#layout-nav .show-menu');

        // If menu is open
        if($show_menu.hasClass('open'))
        {
            // Close it
            $menu.animate({
                left: '105%'
            },{
                duration: animate_duration,
                complete: function(){
                    $show_menu.removeClass('open');
                    is_animating=false;
                }
            });
        }
        else
        {
            // Open it
            $menu.animate({
                left: '50%'
            },{
                duration: animate_duration,
                complete: function(){
                    $show_menu.addClass('open');
                    is_animating=false;
                }
            });
        }
    }
});