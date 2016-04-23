<!--[if lt IE 9]>
  <script>window.fix_ie_pre=true;</script>
<![endif]-->
<div class="navbar" id="navbar_pseude">
  <div class="navbar-inner" style="padding:0"></div>
</div>
<div class="navbar navbar-fixed-top <?php echo $nav_class?>" style="padding:0;width:100%;position:fixed;top:0px;margin:0px">
	<div class="navbar-inner" style="padding:0;top:0px;margin:0px">
		<div class="container-fluid navbar-padding-fix">
          <a class="btn btn-navbar <?php echo $button_class?> pull-right" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
		<a class="brand hidden-desktop pull-left" style="margin-left:3px"><?php echo"$inTitle";?></a>
          <div class="nav-collapse collapse" style="height:0px">
            <ul class="nav main_menu pull-left" style="margin:0px;padding:0">
              <li><a class="brand" href="/index.php"><i class="icon-home"></i> CWOJ</a></li>
              <li><a id="nav_bbs" class="shortcut-hint" title="Alt+B" href="/board.php"><i class="icon-comment"></i> 讨论</a></li>
              <li><a id="nav_set" href="/problemset.php"><i class="icon-tasks"></i> 题库</a></li>
              <li><a id="nav_prob" class="shortcut-hint" title="Alt+P" href="/problempage.php"><i class="icon-edit"></i> 题目</a></li>
              <li><a id="nav_record" class="shortcut-hint" title="Alt+R" href="/record.php"><i class="icon-hdd"></i> 记录</a></li>
              <li><a id="nav_rank" href="/ranklist.php"><i class="icon-bookmark"></i> 排名</a></li>
              <li><a id="nav_about" href="/about.php"><i class="icon-book"></i> 关于</a></li>
            </ul>
			<ul class="nav pull-right" style="margin:0px;padding:0">
			<?php if(isset($_SESSION['user'])){?>
				<li><a id="nav_user" href="javascript:menu_expand();"><i class="icon-user"></i> <?php echo $_SESSION['user']?><span class="badge badge-important notifier"style="height:12px;width:6px;margin-left:5px"></span></a></li>
				<li class="user_menu hide"><a href="/mail.php" id="nav_mail"><span><i class="icon-envelope"></i><span class="hidden-desktop"> 私信</span> <strong class="notifier"></strong></span></a></li>
				<li class="user_menu hide"><a href="/marked.php"><span><i class="icon-star"></i><span class="hidden-desktop"> 收藏</span></span></a></li>
				<li class="user_menu hide"><a href="/profile.php"><span><i class="icon-github"></i><span class="hidden-desktop"> 资料</span></span></a></li>
				<li class="user_menu hide"><a href="/control.php"><span><i class="icon-cog"></i><span class="hidden-desktop"> 设置</span></span></a></li>
				<?php if(isset($_SESSION['administrator'])){?>
				<li class="user_menu hide"><a href="/admin.php"><span><i class="icon-bolt"></i><span class="hidden-desktop"> 管理</span></span></a></li>
				<?php }?>
				<li class="user_menu hide"><a id='logoff_btn' href="#"><span><i class="icon-signout"></i><span class="hidden-desktop"> 注销</span></span></a></li>
				<li class="user_menu hide"><a id="nav_back" href="javascript:menu_back();"><span><i class="icon-chevron-left hidden-desktop"></i><i class="icon-chevron-left hidden-desktop"></i><span class="hidden-desktop"> 返回...</span><i class="icon-chevron-right visible-desktop"></i><i class="icon-chevron-right visible-desktop"></i></span></a></li>
			<?php }else{ ?>
			<li class="login_menu"><a id="login_btn" title="Alt+L" data-toggle="modal" href="#LoginModal">登录</a></li>
			<li class="login_menu"><a href="/reg.php">注册</a></li>
			<?php }?>
			</ul>
			<ul class="nav">
			<form class="navbar-search shortcut-hint" id="search_form" title="Alt+I" action="/search.php" method="get">
				<input type="text" name="q" id="search_input" class="search-query input-xlarge" style="margin-bottom:0px;width:135px" autocomplete="off" placeholder="搜索...">
			</form>
			</ul>
          </div>
		</div>
	</div>
</div>
<div class="hidden-desktop" style="margin-top:65px"></div>
<div class="modal fade hide" id="LoginModal">
  <form id="form_login" style="margin:0px" action="/login.php" method="post">
    <div class="modal-header">
      <a class="close" data-dismiss="modal">×</a>
        <h4>登录窗口</h4>
    </div>
    <div class="modal-body">
      <div id="uid_ctl" class="control-group">
        <label class="control-label" for="uid">用户名：</label>
        <div class="controls">
          <input type="text" id="uid" name="uid" placeholder="">
        </div>
      </div>
      <div id="pwd_ctl" class="control-group">
          <label class="control-label" for="pwd">密码：</label>
          <div class="controls">
            <input id="pwd" name="pwd" type="password" placeholder="">
          </div> </div>
      <div class="control-group">
        <div class="controls">
          <label class="checkbox">
            <input type="checkbox" name="remember">&nbsp;记住我英俊的面庞
          </label>
        </div>
      </div>
      <input id="ret_url" name="url" type="hidden"><!--value=""-->
    </div>
    <div class="modal-footer">
      <input id="signin" type="submit" value="登录" class="btn btn-primary">
      <a href="#" class="btn" data-dismiss="modal">取消</a>
    </div>
  </form>
</div>