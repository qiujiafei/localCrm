$(function () {
    var tpl_box = $('#tpl_role').html();
    var tpl_checkbox = $('#tpl_role_checkbox').html();
    var tpl_radio = $('#tpl_role_radio').html();
    var tpl_power = $("#tpl_power").html();
    var tpl_rol_dropdown = $('#tpl_role_dropdown_menu').html();
    var tpl_resource_list = $('#tpl_resource_list').html();

    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        if (e.target.id == "account") {
            $('#power-seach-input').val("");
            getUserList();
            getRoleList()
                .then(function (data) {
                    $('.dropdown-role-menu').html(juicer(tpl_rol_dropdown, data));
                });
        }
        if (e.target.id == "role") {
            $('.set_checkbox').removeClass('show');
            getRoleList()
                .then(function (data) {
                    $('#role_box').html(juicer(tpl_box, data));
                });
        }
    })

    var getUserList = function (params) {
        var _default = {
            count_per_page: 10,
            page_num: 1,
            keyword: "",
            role_id: ''
        }
        $.extend(_default, params)
        requestUrl({
            url: '/adminuser/get/getall.do',
            data: _default,
            success: function (data) {
                if (data.status == 200) {
                    var num = data.data.total_count;
                    if (data.data.list.length > 0) {
                        $('#power_box').html(juicer(tpl_power, data.data.list));
                    } else {
                        $('#power_box').html("<tr><td>暂无数据</td></tr>");
                    }
                    pagingBuilder.build($('#power_box_page'), _default.page_num, _default.count_per_page, num)
                    pagingBuilder.click($('#power_box_page'), function (page, keyword, role_id) {
                        var keyword = $('#power-seach-input').val();
                        var role_id = $(".dropdown-role-menu").find("option:selected").attr('data-serviceId');
                        getUserList({
                            page_num: page,
                            keyword: keyword,
                            role_id: role_id
                        })
                    })
                }
            }
        })
    }

    // 添加新角色
    $('#creatRole').click(function () {
        $('#creatRoleModal').modal('show');
    });

    // 保存角色
    $('#saveRole').click(function () {
        createRole()
    })

    $('.role-name-input').on("input", function (e) {
        $('.role-help-msg').hide();
    });

    function createRole() {
        var name = $('#role-name').val();
        var reg = /^\s*$/
        var nameIsPass = !reg.test(name)
        if (!nameIsPass) {
            $('.role-help-msg').show();
            $('.role-help-msg').children('em').text('名称不能为空');
            return;
        }
        requestUrl({
            type: 'POST',
            url: '/authorization/role/add.do',
            data: {
                name: name
            },
            success: function (data) {
                if (data.status == 200) {
                    getRoleList().then(function (data) {
                        if (data.length > 0) {
                            $('#role_box').html(juicer(tpl_box, data));
                        } else {
                            $('#role_box').html("<tr><td>暂无数据</td></tr>");
                        }

                    });
                    $('#creatRoleModal').modal('hide');
                } else {
                    $('.role-help-msg').show();
                    $('.role-help-msg').children('em').text(data.data.errMsg);
                }
            }
        })
    }

    // 新建角色模态框shown
    $('#creatRoleModal').on('show.bs.modal', function () {
        var input = $('.role-name-input');
        $('.role-help-msg').hide();
        input.val("");
        //focus
        setTimeout(function () {
            input.focus();
        }, 500);
    });

    // 获取角色列表
    var getRoleList = function () {
        return new Promise(function (resove, reject) {
            requestUrl({
                url: '/authorization/role/getall.do',
                success: function (data) {
                    if (data.status == 200) {
                        resove(data.data);
                    } else {
                        salert(data.data.errMsg, "", 'warning', false);
                    }
                }
            })
        })

    }

    // 删除角色
    $('#role_box').on('click', '.del', function () {
        var role_id = $(this).attr('data-serviceId');
        salert("", "是否删除角色", 'warning', true, function (isConfirm) {
            if (isConfirm) {
                delRole(role_id);
            } else {
            }
        });
    });

    function delRole(role_id) {
        requestUrl({
            url: '/authorization/role/delete.do',
            type: 'post',
            data: {
                role_id: role_id
            },
            success: function (data) {
                if (data.status == 200) {
                    getRoleList().then(function (data) {
                        $('.set_checkbox').removeClass('show');
                        if (data.length > 0) {
                            $('#role_box').html(juicer(tpl_box, data));
                        } else {
                            $('#role_box').html("<tr><td>暂无数据</td></tr>");
                        }


                    })

                    salert("操作成功！", "", 'success', false);
                } else {
                    setTimeout(function () {
                        salert(data.data.errMsg, "", 'warning', false);
                     },1000)

                }

            }
        })
    }

    // 获取角色权限
    $('#role_box').on('click', 'tr', function () {
        var role_id = $(this).attr('data-serviceId');
        $(this).addClass('selected').siblings().removeClass('selected');
        getResource(role_id, function (data) {
            if (data.status == 200) {
                $('#role_stroe_manager').html(juicer(tpl_checkbox, data.data["门店管理"]));
                $('#role_power_set').html(juicer(tpl_checkbox, data.data["角色管理"]));
            } else {
                salert(data.data.errMsg, "", 'warning', false);
            }
        });
        $('.set_checkbox').removeClass('show');
        setTimeout(function () {
            $('.set_checkbox').addClass('show');
        }, 200);

    });

    function getResource(role_id, callback) {
        requestUrl({
            url: '/authorization/resource/get-all.do',
            data: {
                role_id: role_id
            },
            success: callback
        })
    }

    //权限设置
    $('#save_role_power').click(function () {
        var _data = {
            role_id: '',
            allow: []
        }

        _data.role_id = $('.role_table_left').find('tr.selected').attr('data-serviceid');

        $('.set_checkbox').find(':checked').each(function () {
            _data.allow.push($(this).attr('id'));
        });

        requestUrl({
            url: '/authorization/resource/assign-resource.do',
            type: 'post',
            data: _data,
            success: function (data) {
                if (data.status == 200) {
                    salert("操作成功！", "", 'success', false);
                } else {
                    salert(data.data.errMsg, "", 'warning', false);
                }

            }
        })
    })

    // 添加新账户
    $('#creatAccount').click(function () {
        $('#creatOrEditAccountModal').modal('show');
        $('#creatAccountModelLabel').text("添加新账号");
        // 记录状态
        sessionStorage.setItem('TYPE', 'add');
    })

    // 添加新账户
    $('#saveAccount').click(function () {
        createOrEditAccount();
    })

    function createOrEditAccount() {
        var creatAccountModelForm = $('.creatAccountModelForm');
        var name = creatAccountModelForm.find('.name').val();
        var mobile = creatAccountModelForm.find('.mobile').val();
        var email = creatAccountModelForm.find('.email').val();
        var passwd = creatAccountModelForm.find('.passwd').val();
        var verify_passwd = creatAccountModelForm.find('.verify_passwd').val();
        var role_id = $('#creatOrEidtAccount').find('input:checked').attr('id');
        if (!/^[A-Za-z0-9\u4e00-\u9fa5]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/.test(email)) {
            salert('请正确填写邮箱', '', 'error', false);
            return;
        }
        if (!role_id) {
            salert('请选择角色', '', 'error', false);
            return;
        }
        if (!/^[1][345789][0-9]{9}$/.test(mobile)) {
            salert('手机号格式错误', '', 'error', false);
            return;
        }

        var _default = {
            name: name,
            mobile: mobile,
            email: email,
            passwd: passwd,
            verify_passwd: verify_passwd,
            role_id: role_id
        }
        if (accountData && accountData.id) {
            _default.id = accountData.id;
            if (passwd !== verify_passwd) {
                salert('密码填写不一致！', '', 'error', false);
                return;
            }
            requestUrl({
                type: 'POST',
                url: '/adminuser/modify/update.do',
                data: _default,
                success: function (data) {
                    if (data.status == 200) {
                        var page = $('.pagination li.active').attr('data-page');
                        var keyword = $('#power-seach-input').val();
                        var role_id = $(".dropdown-role-menu").find("option:selected").attr('data-serviceId');
                        getUserList({
                            page_num: page,
                            keyword: keyword,
                            role_id: role_id
                        });
                        $('#creatOrEditAccountModal').modal('hide');
                        salert("操作成功！", "", 'success', false);
                        accountData = undefined;
                    } else {
                        salert(data.data.errMsg, "", 'warning', false);
                    }
                }
            })
        } else {

            if (passwd == "" && verify_passwd == "") {
                salert('密码不能为空！', '', 'error', false);
                return;
            } else {
                if (passwd !== verify_passwd) {
                    salert('密码填写不一致！', '', 'error', false);
                    return;
                }
            }
            requestUrl({
                type: 'POST',
                url: '/adminuser/put/user.do',
                data: _default,
                success: function (data) {
                    if (data.status == 200) {
                        getUserList();
                        $('#creatOrEditAccountModal').modal('hide');
                        setTimeout(() => {
                            $('.userInfo>p>em').text(data.data.account);
                            $('#userInfoModal').modal('show');
                        }, 500);
                    } else {
                        salert(data.data.errMsg, "", 'warning', false);
                    }

                }
            })
        }
    }

    // 修改
    var accountData;
    $('#power_box').on('click', '.edit', function () {
        // 记录状态
        sessionStorage.setItem('TYPE', 'edit');
        $('#creatAccountModelLabel').text("修改账号");

        var userId = $(this).attr('data-serviceId');
        getUserDetail(userId, function (userDetail) {
            $('#creatOrEditAccountModal').modal('show');
            accountData = userDetail.data;
        }, function (error) {
            salert("", error.responseJSON.data.errMsg, "warning", false);
        });

    })

    $('#creatOrEditAccountModal').on('show.bs.modal', function () {
        getRoleList().then(function (data) {
            $('#creatOrEidtAccount').html(juicer(tpl_radio, data));
            if (accountData) {
                var checkedRadio = $('#creatOrEidtAccount input[id="' + accountData.role_id + '"]');

                checkedRadio.prop("checked", true);
                checkedRadio.siblings('label').children('i').addClass('checked');
            }
        });

        var _form = $('.creatAccountModelForm');
        _form.find('.passwd').val("");
        _form.find('.verify_passwd').val("");
    })

    $('#creatOrEditAccountModal').on('hide.bs.modal', function () {
        accountData = undefined;
    })
    // 新建修改账户弹窗shown
    $('#creatOrEditAccountModal').on('shown.bs.modal', function () {
        if (sessionStorage.getItem('TYPE') == 'edit') {
            if (accountData) {
                var _form = $('.creatAccountModelForm');

                _form.find('.name').val(accountData.name);
                _form.find('.mobile').val(accountData.mobile);
                _form.find('.email').val(accountData.email);

                $('#account_stroe_manager').html(juicer(tpl_resource_list, accountData.resource_list["门店管理"]));
                $('#account_power_set').html(juicer(tpl_resource_list, accountData.resource_list["角色管理"]));
            }

        } else {

            var creatAccountModelForm = $('.creatAccountModelForm');
            reast(creatAccountModelForm, ['.name', '.mobile', '.email', '.passwd', '.verify_passwd']);
            $('#account_stroe_manager').html(juicer(tpl_resource_list, []));
            $('#account_power_set').html(juicer(tpl_resource_list, []));
        }

        //focus
        setTimeout(function () {
            $('.creatAccountModelForm .name').focus();
        }, 0);

    });
    // 删除账号
    $('#power_box').on('click', '.del', function () {
        var userId = $(this).attr('data-serviceId');
        salert("", "是否删除账号", 'warning', true, function (isConfirm) {
            if (isConfirm) {
                delUser(userId);
                getUserList();
            }
        });
    })

    function delUser(userId) {
        requestUrl({
            type: 'POST',
            url: '/adminuser/delete/one.do',
            data: {
                id: userId
            },
            success: function (data) {
                if (data.status == 200) {
                    salert("操作成功！", "", 'success', false);
                } else {
                    salert(data.data.errMsg, "", 'warning', false);
                }

            }
        })
    }
    // 账户详情
    function getUserDetail(userId, callback, errCallback) {
        requestUrl({
            url: '/adminuser/get/getone.do',
            data: {
                id: userId
            }, success: callback, error: errCallback
        })
    }
    // 查询
    $('#power-seach').click(function () {
        var input = $('#power-seach-input').val();
        var role_id = $(".dropdown-role-menu").find("option:selected").attr('data-serviceId');
        getUserList({
            keyword: input,
            role_id: role_id
        });
    })

    // 账户选择角色
    $('#creatOrEidtAccount').on('click', '.radio-group', function () {
        $(this).children('input').attr('checked', 'checked');
        $(this).children('label').children('i').addClass('checked');
        $(this).siblings().children('input').removeAttr('checked');
        $(this).siblings().children('label').children('i').removeClass('checked');
        getResource($(this).children('input').attr('id'), function (data) {
            if (data.status == 200) {
                $('#account_stroe_manager').html(juicer(tpl_resource_list, data.data["门店管理"]));
                $('#account_power_set').html(juicer(tpl_resource_list, data.data["角色管理"]));
            } else {
                salert(data.data.errMsg, "", 'warning', false);
            }
        })
    })

    function reast(form, classNameArray) {
        for (let index = 0; index < classNameArray.length; index++) {
            form.find(classNameArray[index]).val("");
        }

    }

    // 密码显隐
    $('.set-power-password div[data-state]').click(function () {
        var state = $(this).attr("data-state");
        if (state == "0") {
            $(this).siblings("input").attr("type", "text");
            $(this).attr("data-state", "1");
            $(this).removeClass("icon-passwd-display").addClass("icon-passwd-conceal");
        } else {
            $(this).siblings("input").attr("type", "password");
            $(this).attr("data-state", "0");
            $(this).removeClass("icon-passwd-conceal").addClass("icon-passwd-display");

        }
    })

    getRoleList()
        .then(function (data) {
            if (data.length > 0) {
                $('#role_box').html(juicer(tpl_box, data));
            } else {
                $('#role_box').html("<tr><td>暂无数据</td></tr>");
            }

            $('.dropdown-role-menu').html(juicer(tpl_rol_dropdown, data));
        });


})
