<!--[if lt IE 9]>
  <script>window.fix_ie_pre=true;</script>
<![endif]-->
<div class="navbar" id="navbar_pseude">
  <div class="navbar-inner" style="position:fixed;width:100%;"></div>
</div>
<div class="navbar navbar-fixed-top <?php echo $nav_class?>" style="padding:0;width:100%;position:fixed;top:0px;margin:0px">
	<div class="navbar-inner" style="padding:0;top:0px;margin:0px">
		<div class="container-fluid navbar-padding-fix">
		  <a class="brand" href="/index.php" style="margin-left:0px"><i class="fa fa-home"></i> <?php echo $oj_name?></a>
          <a class="btn btn-navbar <?php echo $button_class?> pull-right" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
		<a class="brand hidden-desktop"><?php echo"$inTitle";?></a>
          <div class="nav-collapse collapse" style="height:0px">
            <ul class="nav main_menu pull-left" style="margin:0px;padding:0">
              <li><a id="nav_bbs" class="shortcut-hint" title="Alt+B" href="/board.php"><i class="fa fa-fw fa-comment"></i> 讨论</a></li>
              <li><a id="nav_set" class="shortcut-hint" title="Alt+S"href="/problemset.php"><i class="fa fa-fw fa-th-list"></i> 题库</a></li>
              <li><a id="nav_prob" class="shortcut-hint" title="Alt+P" href="/problempage.php"><i class="fa fa-fw fa-coffee"></i> 题目</a></li>
              <li><a id="nav_record" class="shortcut-hint" title="Alt+R" href="/record.php"><i class="fa fa-fw fa-university"></i> 记录</a></li>
              <li><a id="nav_rank" href="/ranklist.php"><i class="fa fa-fw fa-leaf"></i> 排名</a></li>
              <li><a id="nav_about" href="/about.php"><i class="fa fa-fw fa-magic"></i> 关于</a></li>
            </ul>
			<ul class="nav sec_menu pull-right" style="margin:0px;padding:0">
			<?php if(isset($_SESSION['user'])){?>
				<li><a class="nav_user" href="javascript:menu_expand();"><i class="fa fa-fw fa-user"></i> <?php echo $_SESSION['user']?><span class="badge badge-important notifier"style="height:12px;width:6px;margin-left:5px"></span></a></li>
				<li class="user_menu hide"><a href="/mail.php" id="nav_mail"><span><i class="fa fa-fw fa-envelope"></i><span class="hidden-desktop"> 私信</span> <strong class="notifier"></strong></span></a></li>
				<li class="user_menu hide"><a href="/marked.php"><span><i class="fa fa-fw fa-star"></i><span class="hidden-desktop"> 收藏</span></span></a></li>
				<li class="user_menu hide"><a href="/profile.php"><span><i class="fa fa-fw fa-user-secret"></i><span class="hidden-desktop"> 档案</span></span></a></li>
				<li class="user_menu hide"><a href="/settings.php"><span><i class="fa fa-fw fa-cog"></i><span class="hidden-desktop"> 设置</span></span></a></li>
				<?php if(isset($_SESSION['administrator'])){?>
				<li class="user_menu hide"><a href="/admin.php"><span><i class="fa fa-fw fa-bolt"></i><span class="hidden-desktop"> 管理</span></span></a></li>
				<?php }?>
				<li class="user_menu hide"><a id='logoff_btn' href="#"><span><i class="fa fa-fw fa-sign-out"></i><span class="hidden-desktop"> 注销</span></span></a></li>
				<li class="user_menu hide"><a id="nav_back" href="javascript:menu_back();"><span><i class="fa fa-fw fa-arrow-left hidden-desktop"></i><span class="hidden-desktop"> &nbsp;返回...</span><i class="fa fa-fw fa-arrow-right visible-desktop"></i></span></a></li>
			<?php }else{ ?>
			<li class="nav_user"><a id="login_btn" title="Alt+L" href="/login.php"><i class="fa fa-fw fa-sign-in"></i> 登录</a></li>
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
<div style="margin-top:65px"></div>
