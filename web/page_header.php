<?php 
if(!function_exists('check_priv')) 
    require 'inc/privilege.php';
if(!function_exists('get_gravatar'))
    require 'inc/functions.php';
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
		<li><a id="nav_cont" class="shortcut-hint" title="Alt+C" href="/contest.php"><i class="fa fa-fw fa-compass"></i><span id="nav_cont_text" class="hidden-sm"> <?php echo _('Contests');?></span></a></li>
		<li><a id="nav_set" href="/problemset.php" class="shortcut-hint" title="Alt+P"><i class="fa fa-fw fa-coffee"></i><span id="nav_set_text" class="hidden-sm"> <?php echo _('Problems');?></span></a></li>
		<li><a id="nav_bbs" class="shortcut-hint" title="Alt+B" href="/board.php"><i class="fa fa-fw fa-comment"></i><span id="nav_bbs_text" class="hidden-sm"> <?php echo _('Board');?></span></a></li>
		<li><a id="nav_record" class="shortcut-hint" title="Alt+R" href="/record.php"><i class="fa fa-fw fa-university"></i><span id="nav_record_text" class="hidden-sm"> <?php echo _('Record');?></span></a></li>
		<li><a id="nav_rank" href="/ranklist.php"><i class="fa fa-fw fa-pie-chart"></i><span id="nav_rank_text" class="hidden-sm"> <?php echo _('Rank');?></span></a></li>
		<li><a id="nav_about" href="/about.php"><i class="fa fa-fw fa-magic"></i><span id="nav_about_text" class="hidden-sm"> <?php echo _('About');?></span></a></li>
		<li><a id="nav_searchbtn" class="visible-xs visible-sm visible-md" href="javascript:void(0)" title="Alt+I"><i class="fa fa-fw fa-search"></i><span class="hidden-sm"> <?php echo _('Search...');?></span></a></li>
      </ul>
      <ul class="nav navbar-nav hidden-xs hidden-sm hidden-md hidden-lg" id="nav_back">
        <li><a id="btn_clrsearch" class="visible-xs visible-sm visible-md" href="javascript:void(0)"><i class="fa fa-fw fa-arrow-left"></i> <?php echo _('Go Back...');?></a></li>
      </ul>
      
      <form class= "navbar-form visible-lg-inline-block shortcut-hint" id="search_form" title="Alt+I" action="/search.php" method="get">
        <input type="hidden" name="t" id="search_type" value="0">
		  <div class="form-group">
            <div class="input-group">
              <div class="input-group-btn dropdown" id="search_span">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span id="search_select"><i class="fa fa-fw fa-coffee"></i></span></button>
                <ul class="dropdown-menu">
                  <li><a href="javascript:void(0)" onclick="return change_type(1)"><span id="type1"><i class="fa fa-fw fa-coffee"></i></span> <?php echo _('Problems');?></a></li>
                  <li><a href="javascript:void(0)" onclick="return change_type(2)"><span id="type2"><i class="fa fa-fw fa-compass"></i></span> <?php echo _('Contests');?></a></li>
                  <li><a href="javascript:void(0)" onclick="return change_type(3)"><span id="type3"><i class="fa fa-fw fa-user"></i></span> <?php echo _('Users');?></a></li>
                </ul>
              </div>
            <input id="search_input" name="q" type="text" class="form-control" autocomplete="off" placeholder="<?php echo _('Search...');?>">
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
                <li><a href="/mail.php" id="nav_mail"><i class="fa fa-fw fa-envelope"></i> <?php echo _('Mails');?> <strong class="notifier"></strong></a></li>
				<li><a href="/marked.php"><i class="fa fa-fw fa-star"></i> <?php echo _('Marked');?></a></li>
				<li><a href="/profile.php"><i class="fa fa-fw fa-github-alt"></i> <?php echo _('Profile');?></a></li>
				<li><a href="/preferences.php"><i class="fa fa-fw fa-cog"></i> <?php echo _('Preferences');?></a></li>
				<?php if(check_priv(PRIV_PROBLEM) || check_priv(PRIV_SYSTEM)){?>
				<li class="divider hidden-xs"></li>
				<li><a href="/admin.php"><i class="fa fa-fw fa-bolt"></i> <?php echo _('Administration');?></a></li>
				<?php }?>
				<li class="divider hidden-xs"></li>
				<li><a id="nav_logoff" href="#"><i class="fa fa-fw fa-sign-out"></i> <?php echo _('Log off');?></a></li>
            </ul>
		  </li>
		  <?php }else{?>
		  <li><a id="nav_login" title="Alt+L" href="/login.php"><i class="fa fa-fw fa-sign-in"></i> <?php echo _('Log in');?></a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-fw fa-globe"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right" id="nav_lang">
              <li><a href="javascript:void(0)" onclick="return change_i18n('en_US')">English</a></li>
              <li><a href="javascript:void(0)" onclick="return change_i18n('zh_CN')">简体中文</a></li>
            </ul>
          </li>
		  <?php }?>
	  </ul>  
    </div> 
  </div>
  <div class="alert alert-success text-center alert-popup collapse" id="alert_newmsg"><i class="fa fa-fw fa-info"></i><?php echo _('You have unread mails...')?></div>
</header>