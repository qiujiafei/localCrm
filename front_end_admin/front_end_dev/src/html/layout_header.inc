<!-- header start -->
<header class="business-frame-header">
    <nav class="navbar navbar-default">
        <div class="navbar-logo pull-left">
            <img alt="logo" src="/images/logo.png" >
            <div class='navbar-logo-title'>
                <p>CRM</p>
                <p>综合管理后台</p>
            </div>
        </div>
        <div class="navbar-title pull-left">
            管理员控制中心
        </div>
        <ul class="nav navbar-nav navbar-right">
            <li>
                <img src="/images/hummingbird/user_icon_20.png" alt="">
                <span data-id="username"></span>
            </li>
            <li>
                <a data-id="logout" href="./login.html">
                    退出
                </a>
            </li>
        </ul>
    </nav>
</header>
<!-- header end -->
<!-- aside nav start -->
<aside class="business-frame-aside">
    <div class="aside-list">
        <a href="/html/index.html" class="collapsed">
            <i class="glyphicon glyphicon-home"></i> 首页
        </a>
    </div>
    <div class="aside-list">
        <a data-toggle="collapse" href="#aside_panel_2" class="collapsed">
            <i class="glyphicon glyphicon-th-list"></i> 门店管理
            <i class="glyphicon glyphicon-chevron-down"></i>
        </a>
        <div class="collapse" id="aside_panel_2">
            <ul class="list-unstyled clearfix">
                <li>
                    <a href="/html/store_info.html">门店信息</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="aside-list">
        <a href="/html/role_manager.html" class="collapsed">
            <i class="glyphicon glyphicon-user"></i> 角色管理
        </a>
    </div>
</aside>
<!-- aside nav end -->
