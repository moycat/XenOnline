// Change the state of the menu
function toggleMenu() {
    $('#Yuki').toggleClass('Naving');
    $('nav').toggleClass('foo');
    $('.wrapper').toggleClass('foo');
    $('.openNav').toggleClass('foo');
}
// Go top
function gotop() {
    $("html,body").animate({scrollTop:"0px"}, 200);
}
function gotopanywhere(){
    var scrollt = $("body").scrollTop();
    if (scrollt > 200) {
        $(".gotop").addClass("gotop-show");
    } else if (scrollt < 200){
        $(".gotop").removeClass("gotop-show");
    }
}
// Rotate a card
function toggleCard(frontID, backID) {
    var front = $('#'+frontID);
    var back = $('#'+backID);
    if (front.hasClass('front-hidden')) {
        back.addClass('back-hidden');
        setTimeout("$('#"+backID+"').hide()",400);
        setTimeout("$('#"+frontID+"').show()",400);
        setTimeout("$('#"+frontID+"').removeClass('front-hidden')",450);
    } else {
        front.addClass('front-hidden');
        setTimeout("$('#"+frontID+"').hide()",400);
        setTimeout("$('#"+backID+"').show()",400);
        setTimeout("$('#"+backID+"').removeClass('back-hidden')",450);
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
    $('input').iCheck({
        checkboxClass: 'icheckbox_flat-red',
        radioClass: 'iradio_flat-red'
    });
});