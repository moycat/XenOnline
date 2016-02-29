// Change the state of the menu
function toggleMenu() {
    $('#Yuki').toggleClass('Naving');
    $('nav').toggleClass('foo');
    $('.wrapper').toggleClass('foo');
    $('.openNav').toggleClass('foo');
}
// Add some self-start things...
$(function(){
    var display_gotop = false;
    $(window).scroll(function(){
        var scrollt = document.documentElement.scrollTop + document.body.scrollTop;
        if (scrollt > 200 && !display_gotop) {
            $(".gotop").fadeIn(600);
            display_gotop = true;
        } else if (scrollt < 100 && display_gotop){
            $(".gotop").fadeOut(600);
            display_gotop = false;
        }
    });
});
function gotop() {
    $("html,body").animate({scrollTop:"0px"}, 200);
}