import Cookie from 'js-cookie';

$(document).ready(function () {
    const body = $('body');
    let sidebarToggled = (Cookie.get('sidebar-toggled') === ('true' || undefined)) && $(window).width() >= 768;
    const all = $('.transition');

    function toggleSidebar(sidebarToggled) {
        if (sidebarToggled) {
            body.addClass('sidebar-toggled');
        } else {
            body.removeClass('sidebar-toggled');
        }
    }

    toggleSidebar(sidebarToggled);

    $('.sidebar-toggle').click(()=>{
        sidebarToggled = !sidebarToggled;

        toggleSidebar(sidebarToggled);
        Cookie.set('sidebar-toggled', sidebarToggled);
    });

    setTimeout(()=>{
        all.css('transition-duration', '0.2s');
    }, 500);
});
