<!--[if lt IE 9]>
  <script>window.fix_ie_pre=true;</script>
<![endif]-->
<div class="navbar" id="navbar_pseude">
  <div class="navbar-inner" style="padding:0"></div>
</div>
<center><?php echo "<div class=\"navbar navbar-fixed-top {$nav_class}\" id=\"navbar_top\" style=\"text-align:center\">";?>
<div class="navbar navbar-inner" style="padding:0;text-align:center">
    <div class="navbar navbar-inner" style="padding:0;width:100%;position:fixed;z-index:3;top:0px;left:0px;margin:0 auto"></div>
      <div class="container-fluid navbar-padding-fix hidden-phone" style="width:100%;position:fixed;z-index:3;top:0px;left:0px;margin:0 auto">
      <a class="brand" href="index.php"><i class="icon-home"></i><span> CWOJ</span></a>
        <ul class="nav">
          <li><a id="nav_bbs" class="shortcut-hint" title="Alt+B" href="board.php"><i class="icon-comment"></i><span class="navbar-hide-text"> 讨论</span></a></li>
          <li><a id="nav_set" href="problemset.php"><i class="icon-tasks"></i><span class="navbar-hide-text"> 题库</span></a></li>
          <li><a id="nav_prob" class="shortcut-hint" title="Alt+P" href="problempage.php"><i class="icon-edit"></i><span class="navbar-hide-text"> 题目</span></a></li>
          <li><a id="nav_record" class="shortcut-hint" title="Alt+R" href="record.php"><i class="icon-hdd"></i><span class="navbar-hide-text"> 记录</span></a></li>
          <li><a id="nav_rank" href="ranklist.php"><i class="icon-bookmark"></i><span class="navbar-hide-text"> 排名</span></a></li>
          <li><a id="nav_about" href="about.php"><i class="icon-book"></i><span class="navbar-hide-text"> 关于</span></a></li>
        </ul>
        <form class="navbar-search shortcut-hint" id="search_form" title="Alt+I" action="search.php" method="get">
          <input type="text" name="q" id="search_input" class="search-query input-medium" style="margin-bottom:0px;width:95px;margin-left:10px" autocomplete="off" placeholder="搜索...">
        </form>
		<ul class="nav pull-right" style="margin-right:30px">
		<?php if(isset($_SESSION['user'])){?>
		  <li class="dropdown">
		    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-user"></i>
			<?php echo $_SESSION['user']?>&nbsp;<b class="caret"></b><strong class="notifier"></strong></a>
          <ul class="dropdown-menu" style="text-align:left">
          <li><a href="mail.php" id="nav_mail"><i class="icon-envelope"></i> 私信<?php echo '<strong class="notifier"></strong>'?></a></li>
          <li><a href="marked.php"><i class="icon-star"></i> 收藏</a></li>
          <li><a href="profile.php"><i class="icon-github"></i> 资料</a></li>
          <li><a href="control.php"><i class="icon-cog"></i> 设置</a></li>
		  <?php if(isset($_SESSION['administrator'])){?>
          <li class="divider"></li><li><a href="admin.php"><i class="icon-bolt"></i> 管理</a></li>
		  <?php }?>
          <li class="divider"></li>
          <li><a id='logoff_btn' href="#"><i class="icon-signout"></i> 注销</a></li>
        </ul>
		</li>
		<?php }else{ ?>
		<li><a id="login_btn" title="Alt+L" data-toggle="modal" href="#LoginModal">登录</a></li>
        <li><a href="reg.php">注册</a></li>
		<?php }?>
		</ul>
    </div>
	<div class="navbar navbar-inner visible-phone" style="text-align:center;padding:0;width:100%;position:fixed;z-index:3;top:0px;margin:0px">
        <div class="container-fluid navbar-padding-fix">
		<?php echo"<a class=\"btn {$button_class} visible-phone pull-left\" href=\"javascript:history.back(-1);\" style=\"margin-left:10px\">";?>
            <i class="icon-chevron-left"></i></a>
          <div class="brand" style="float:none;display:inline-block;margin-left:auto;margin-right:auto"><?php echo"$inTitle";?></div>
          <?php echo"<a class=\"btn {$button_class} pull-right\" style=\"margin-right:10px\" data-toggle=\"collapse\" data-target=\".nav-collapse\">";?>
            <i class="icon-th-list"></i><?php echo '<strong class="notifier"></strong>';?></a>
          <div class="nav-collapse"><p></p>
            <ul class="nav" style="text-align:left">
              <li><a style="color:#FFF" class="brand" href="index.php"><i class="icon-home"></i> CWOJ</a></li>
              <li><a style="color:#FFF" id="nav_bbs" class="shortcut-hint" title="Alt+B" href="board.php"><i class="icon-comment"></i> 讨论</a></li>
              <li><a style="color:#FFF" id="nav_set" href="problemset.php"><i class="icon-tasks"></i> 题库</a></li>
              <li><a style="color:#FFF" id="nav_prob" class="shortcut-hint" title="Alt+P" href="problempage.php"><i class="icon-edit"></i> 题目</a></li>
              <li><a style="color:#FFF" id="nav_record" class="shortcut-hint" title="Alt+R" href="record.php"><i class="icon-hdd"></i> 记录</a></li>
              <li><a style="color:#FFF" id="nav_rank" href="ranklist.php"><i class="icon-bookmark"></i> 排名</a></li>
              <li><a style="color:#FFF" id="nav_about" href="about.php"><i class="icon-book"></i> 关于</a></li>
			  <?php if(isset($_SESSION['user'])) {
		      echo "<div style=\"color:#FFF\">&nbsp;&nbsp;<div class=\"btn {$button_class}\"><i class=\"icon-user\"></i> {$_SESSION['user']} </div>
			  <a class=\"btn {$button_class}\" href=\"mail.php\" id=\"nav_mail\"><i class=\"icon-envelope\"></i><strong class=\"notifier\"></strong></a>
              <a class=\"btn {$button_class}\" href=\"marked.php\"><i class=\"icon-star\"></i></a>
              <a class=\"btn {$button_class}\" href=\"profile.php\"><i class=\"icon-github\"></i></a>
              <a class=\"btn {$button_class}\" href=\"control.php\"><i class=\"icon-cog\"></i></a>";?>
		      <?php if(isset($_SESSION['administrator']))
              echo "<a class=\"btn {$button_class}\" href=\"admin.php\"><i class=\"icon-bolt\"></i> </a>&nbsp;
			  <a class=\"btn {$button_class}\" id=\"logoff_btn1\" href=\"#\"><i class=\"icon-signout\"></i></a>";
		      }else{  
		      echo "&nbsp;&nbsp;<a class=\"btn {$button_class}\" id=\"login_btn1\" title=\"Alt+L\" data-toggle=\"modal\" href=\"#LoginModal\">登录</a>
              <a class=\"btn {$button_class}\" href=\"reg.php#\">注册</a>";}?></div>
              <li><form class="navbar-search pull-left shortcut-hint" id="search_form" title="Alt+I" action="search.php" method="get">
              <input type="text" name="q" id="search_input" class="search-query input-xlarge" style="margin-bottom:0px" autocomplete="off" placeholder="搜索...">
              </form></li>
			</ul>
		  </div>
          </div>
        </div>
      </div>
	 </div>
</center>
<div class="modal fade hide" id="LoginModal">
  <form id="form_login" style="margin:0px" action="login.php" method="post">
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