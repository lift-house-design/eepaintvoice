function window_resize()
{
    // If mobile
    if($(window).width()<=767)
    {
        // If skrollr is enabled
        if($('.skrollable').length)
        {
            // Disable skrollr
            s.destroy();
        }
    }
    // If desktop
    else
    {
        // If skrollr is disabled
        if(!$('.skrollable').length)
        {
            // Enable skrollr
            s=skrollr.init({
                smoothScrolling: true
            });
        }

        // Reset show menu button to closed
        $('#layout-nav .nav').css('left','');
        $('#layout-nav .show-menu').removeClass('open');
    }
}

$(window_resize);
$(window).resize(window_resize);