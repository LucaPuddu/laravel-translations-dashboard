$(document).ready(function () {
    const body = $('body');
    let sidebarToggled = $(window).width() >= 768;
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
    });

    setTimeout(()=>{
        all.css('transition-duration', '0.2s');
    }, 500);
});
