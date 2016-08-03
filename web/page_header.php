<?php
if(!function_exists("check_priv")) require 'inc/privilege.php';
if(!function_exists("get_gravatar")) require 'inc/functions.php';
if(!isset($_SESSION['user'])){
	$_SESSION['login_redirect']=$_SERVER['PHP_SELF'];
	if(!empty($_SERVER['QUERY_STRING'])) $_SESSION['login_redirect']=$_SESSION['login_redirect'].'?'.$_SERVER['QUERY_STRING'];
}?>
<!--[if lt IE 9]>
  <script>window.fix_ie_pre=true;</script>
<![endif]-->
<header class="navbar navbar-default navbar-fixed-top">
  <div class="container">
   <div class="navbar-header">
	  <a class="navbar-brand" href="/" style="font-size:18px"><i class="fa fa-home"></i> CWOJ</a>
      <button type="button" class="navbar-toggle" data-toggle="collapse" 
         data-target="#nav_menus">
         <span class="icon-bar"></span>
         <span class="icon-bar"></span>
         <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand visible-xs"><?php echo $inTitle?></a>
   </div>
   <div class="collapse navbar-collapse" id="nav_menus">
      <ul class="nav navbar-nav navbar-left" id="nav_left">
		<li><a id="nav_cont" class="shortcut-hint" title="Alt+C" href="/contest.php"><i class="fa fa-fw fa-compass"></i><span id="nav_cont_text" class="hidden-sm"> 比赛</span></a></li>
		<li><a id="nav_set" href="/problemset.php" class="shortcut-hint" title="Alt+P"><i class="fa fa-fw fa-th-list"></i><span id="nav_set_text" class="hidden-sm"> 题库</span></a></li>
		<li><a id="nav_bbs" class="shortcut-hint" title="Alt+B" href="/board.php"><i class="fa fa-fw fa-comment"></i><span id="nav_bbs_text" class="hidden-sm"> 讨论</span></a></li>
		<li><a id="nav_record" class="shortcut-hint" title="Alt+R" href="/record.php"><i class="fa fa-fw fa-university"></i><span id="nav_record_text" class="hidden-sm"> 记录</span></a></li>
		<li><a id="nav_rank" href="/ranklist.php"><i class="fa fa-fw fa-leaf"></i><span id="nav_rank_text" class="hidden-sm"> 排名</span></a></li>
		<li><a id="nav_about" href="/about.php"><i class="fa fa-fw fa-magic"></i><span id="nav_about_text" class="hidden-sm"> 关于</span></a></li>
		<li><a id="nav_searchbtn" class="visible-xs visible-sm" href="#" title="Alt+I"><i class="fa fa-fw fa-search"></i><span class="hidden-sm"> 搜索</span></a></li>
      </ul>
      <ul class="nav navbar-nav hidden-xs hidden-sm hidden-md hidden-lg" id="nav_back">
        <li><a id="btn_clrsearch" class="visible-xs visible-sm" href="#"><i class="fa fa-fw fa-arrow-left"></i> 返回</a></li>
      </ul>
      <form class= "navbar-form visible-md-inline-block visible-lg-inline-block shortcut-hint" id="search_form" title="Alt+I" action="/search.php" method="get">
        <input type="hidden" name="t" id="search_type" value="0">
		  <div class="form-group">
            <div class="input-group">
              <span class="input-group-btn dropdown" id="search_span">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span id="search_select">题目</span></button>
                <ul class="dropdown-menu">
                  <li><a href="javascript:void(0)" onclick="return change_type(1)"><i class="fa fa-fw fa-coffee"></i> <span id="type1">题目</span></a></li>
                  <li><a href="javascript:void(0)" onclick="return change_type(2)"><i class="fa fa-fw fa-compass"></i> <span id="type2">比赛</span></a></li>
                  <li><a href="javascript:void(0)" onclick="return change_type(3)"><i class="fa fa-fw fa-user"></i> <span id="type3">用户</span></a></li>
                </ul>
              </span>
            <input id="search_input" name="q" type="text" class="form-control" autocomplete="off" placeholder="搜索...">
          </div>
        </div>
      </form>

	  <ul class="nav navbar-nav navbar-right">
	    <?php if(isset($_SESSION['user'])){?>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
			  <?php echo '<img src='.get_gravatar($_SESSION['email'],40).' class="img-circle navbar-img" width="40" height="40"> '.$_SESSION['user']?> <strong class="notifier"></strong> <span class="caret"></span>
			</a>  
			<ul class="dropdown-menu dropdown-menu-right" id="nav_right">
                <li><a href="/mail.php" id="nav_mail"><i class="fa fa-fw fa-envelope"></i> 私信 <strong class="notifier"></strong></a></li>
				<li><a href="/marked.php"><i class="fa fa-fw fa-star"></i> 收藏</a></li>
				<li><a href="/profile.php"><i class="fa fa-fw fa-user-secret"></i> 档案</a></li>
				<li><a href="/preferences.php"><i class="fa fa-fw fa-cog"></i> 设置</a></li>
				<?php if(check_priv(PRIV_PROBLEM) || check_priv(PRIV_SYSTEM)){?>
				<li class="divider hidden-xs"></li>
				<li><a href="/admin.php"><i class="fa fa-fw fa-bolt"></i> 管理</a></li>
				<?php }?>
				<li class="divider hidden-xs"></li>
				<li><a id="nav_logoff" href="#"><i class="fa fa-fw fa-sign-out"></i> 注销</a></li>
            </ul>
		  </li>
		  <?php }else{?>
		  <li><a id="nav_login" title="Alt+L" href="/login.php"><i class="fa fa-fw fa-sign-in"></i> 登录</a></li>
		  <?php }?>
	  </ul>  
    </div> 
  </div>
</header>