// Add some self-start things...
$(function () {
    $(window).scroll(gotopanywhere);
    $('.collapse.in').prev('.panel-heading').addClass('active');
    $('#accordion, #bs-collapse')
        .on('show.bs.collapse', function (a) {
            $(a.target).prev('.panel-heading').addClass('active');
        })
        .on('hide.bs.collapse', function (a) {
            $(a.target).prev('.panel-heading').removeClass('active');
        });
    $('input').iCheck({
        checkboxClass: 'icheckbox_flat-red',
        radioClass: 'iradio_flat-red'
    });
});

// User...
function login() {
    email = $("#ajax-email").val();
    password = $("#ajax-password").val();
    $.ajax({
        url: '/user',
        data: {
            email: email,
            password: password,
            forgetmenot: remember
        },
        type: 'post',
        cache: false,
        success: function (data) {
            if (data.auth) {
                side_avatar = $("#side-avatar");
                side_avatar.attr("src", data.avatar);
                side_avatar.attr("alt", "Avatar");
                $("#side-username").text("Hi, " + data.nickname);
                $("#side-userinfo").html('<a href="/logout"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span></a>');
            } else {
                //shake shake-horizontal shake-constant
            }
        },
        error: function () {
            alert("与服务器通信出现错误，请刷新页面重试。");
        }
    });
}
// Change the state of the menu
function toggleMenu() {
    $('#Yuki').toggleClass('Naving');
    $('nav').toggleClass('foo');
    $('.wrapper').toggleClass('foo');
    $('.openNav').toggleClass('foo');
    //$('.goback').toggleClass('foo');
}

// Go top
var goingtop = false;
function gotop() {
    goingtop = true;
    $("html,body").animate(
        {scrollTop: "0px"},
        200,
        function(){
            goingtop = false;
            gotopanywhere();
        }
    );
}
function gotopanywhere() {
    if (goingtop) {
        return;
    }
    var scrollt = $("body").scrollTop();
    if (scrollt > 200) {
        $(".gotop").addClass("gotop-show");
    } else if (scrollt < 200) {
        $(".gotop").removeClass("gotop-show");
    }
}

// Rotate a card
function toggleCard(frontID, backID) {
    var front = $('#' + frontID);
    var back = $('#' + backID);
    if (front.hasClass('front-hidden')) {
        back.addClass('back-hidden');
        setTimeout("$('#" + backID + "').hide()", 400);
        setTimeout("$('#" + frontID + "').show()", 400);
        setTimeout("$('#" + frontID + "').removeClass('front-hidden')", 450);
    } else {
        front.addClass('front-hidden');
        setTimeout("$('#" + frontID + "').hide()", 400);
        setTimeout("$('#" + backID + "').show()", 400);
        setTimeout("$('#" + backID + "').removeClass('back-hidden')", 450);
    }
}
// Side...
function side_card(show, hide) {
    $(hide).removeClass("user-op-card-foo");
    $(show).addClass("user-op-card-foo");
}
function close_side_card(card) {
    $(card).removeClass("user-op-card-foo");
}
// New solution
function post_code() {
    var editor = ace.edit("source-code");
    var code = editor.getValue();
    var pid = $("#pid").val();
    $.ajax({
        url: '/solution',
        data: {
            _method: 'PUT',
            code: code,
            pid: pid,
            language: 1
        },
        type: 'post',
        cache: false,
        success: function (data) {
            if (data.ok) {
                $("#post-info").html('<div class="alert alert-success alert-dismissible" role="alert">\<' +
                    'button type="button" class="close" data-dismiss="alert" aria-label="Close">\<' +
                    'span aria-hidden="true">&times;</span></button>提交成功！新的提交：<a href="/solution/' + data.sid +
                    '">#' + data.sid + '</a></div>');
            } else {
                $("#post-info").html('<div class="alert alert-danger alert-dismissible" role="alert">\<' +
                    'button type="button" class="close" data-dismiss="alert" aria-label="Close">\<' +
                    'span aria-hidden="true">&times;</span></button>提交失败，请确认你已登录。</div>');
            }
        },
        error: function () {
            $("#post-info").html('<div class="alert alert-danger alert-dismissible" role="alert">\<' +
                'button type="button" class="close" data-dismiss="alert" aria-label="Close">\<' +
                'span aria-hidden="true">&times;</span></button>与服务器通信错误！请确认你已登录，或刷新重试。</div>');
        }
    });
}