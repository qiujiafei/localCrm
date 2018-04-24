$(function () {
    var username = $('#username').val();
    var password = $('#password').val();

    //校验
    $('#username,#password').on("input", function (e) {
        $('.help-msg').css('display', 'none');
        if (e.currentTarget.id === 'username') {
            username = e.currentTarget.value;
        }
        if (e.currentTarget.id === 'password') {
            password = e.currentTarget.value;
        }
        if (username && password) {
            $('#J_login_btn').attr('disabled', false);
        }
    })

    // 登录
    var doLogin = function () {
        var reg = /^\s*$/
        var usernameIsPass = !reg.test(username)
        var pwdIsPass = !reg.test(password)

        if (usernameIsPass && pwdIsPass) {
            window.localStorage.removeItem("BUSINESS_TOKEN");
            window.localStorage.removeItem("BUSINESS_USERNAME");
            requestUrl({
                url: '/authentication/account/login.do',
                type: 'POST',
                data: {
                    username: username,
                    passwd: password
                },
                success: function (data) {
                    if (data.status == "200") {
                        window.localStorage.setItem('BUSINESS_TOKEN', data.data.token);
                        window.localStorage.setItem('BUSINESS_USERNAME', data.data.username);
                        location.href = "./index.html";
                    } else {
                        console.log(data)
                        $('.help-msg').css('display', 'block');
                        $('.help-msg>em').text(data.data.errMsg);
                    }

                }
            })

        } else if (!usernameIsPass) {
            $('.help-msg').css('display', 'block');
            $('.help-msg>em').text("请输入用户名");
        } else if (!pwdIsPass) {
            $('.help-msg').css('display', 'block');
            $('.help-msg>em').text("请输入密码");
        }
    }

    $('#J_login_btn').on('click', function () {
        doLogin();
    })

    $(document).keydown(function (e) {
        if (e.keyCode == 13) {
            doLogin();
        }
    });

})
