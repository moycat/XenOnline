// Change the state of the menu
function toggleMenu() {
    $('#Yuki').toggleClass('Naving');
    $('nav').toggleClass('foo');
    $('.wrapper').toggleClass('foo');
    $('.openNav').toggleClass('foo');
}
// Go top
var display_gotop = false;
function gotop() {
    $("html,body").animate({scrollTop:"0px"}, 200);
}
function gotopanywhere(){
    var scrollt = document.documentElement.scrollTop + document.body.scrollTop;
    if (scrollt > 200 && !display_gotop) {
        $(".gotop").fadeIn(600);
        display_gotop = true;
    } else if (scrollt < 100 && display_gotop){
        $(".gotop").fadeOut(600);
        display_gotop = false;
    }
}
// Add some self-start things...
$(function() {
    $(window).scroll(gotopanywhere);
    $('.collapse.in').prev('.panel-heading').addClass('active');
    $('#accordion, #bs-collapse')
        .on('show.bs.collapse', function(a) {
            $(a.target).prev('.panel-heading').addClass('active');
        })
        .on('hide.bs.collapse', function(a) {
            $(a.target).prev('.panel-heading').removeClass('active');
        });
});