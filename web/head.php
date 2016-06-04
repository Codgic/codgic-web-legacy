<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="renderer" content="webkit">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?php echo $oj_name?> - 一个开源且毫无特色并且时刻受到水王洪水威胁的信息竞赛刷题系统, built for you to code your future. 愿你在<?php echo $oj_name?>玩(bei)耍(nve)愉快! NOIP NOI 信息竞赛 刷题 水王">
  <?php header("Content-Type: text/html; charset=utf-8");?>
  <link rel="shortcut icon" href="/assets/res/favicon.ico" type="image/x-icon" />
  <title><?php echo $Title?></title>
  <?php 
  $hour = date('H',time());
  if($hour>=$daystart && $hour<$nightstart)
	    	$t_night='off';
   	else
    		$t_night='on';
  if(isset($_SESSION['user'])){
    require_once 'inc/preferences.php';
    if(isset($_SESSION['pref']))
	    $pref=unserialize($_SESSION['pref']);
    else
	    $pref=new preferences();
    if($pref->hidehotkey=='on')
	  echo "<script>window.hidehotkey=true;</script>";
    if($pref->autonight=='off'){
	  if($pref->night=='off') $t_night='off';
	  else $t_night='on';
    }
  };
	if($t_night=='on') {
		$loginimg='/assets/res/loginbg_dark.png';
		$css1='/assets/css/cerulean_dark.min.css?v=0';
		$css2='/assets/css/docs_dark.css?v=1';
		$nav_class='navbar-inverse';
     $button_class='btn-inverse';
   }else{
		$loginimg='/assets/res/loginbg.png';
		$css1='/assets/css/cerulean.min.css?v=0';
		$css2='/assets/css/docs.css?v=1';
		$nav_class='';
	   	$button_class='btn-primary';
	};
  ?>
  <link href="<?php echo $css1?>" rel="stylesheet" type="text/css">
  <link href="<?php echo $css2?>" rel="stylesheet" type="text/css">
  <link href="/assets/css/bootstrap-responsive.min.css" rel="stylesheet">
  <link href="/assets/css/font-awesome.min.css" rel="stylesheet">
  <link href="/assets/js/google-code-prettify/prettify.css" rel="stylesheet">
  <script src="/assets/js/jquery.min.js"></script>
  <script src="/assets/js/bootstrap.min.js"></script>
  <!--[if lt IE 8]>
  <script type="text/javascript">
  window.location = "fuckie.php"; 
  </script>
  <![endif]-->
  <!--[if lt IE 9]>
        <script src="/assets/js/html5.min.js"></script>
   	<script src="/assets/js/respond.min.js"></script>
	<script type="text/javascript" src="/assets/js/jquery.placeholder.min.js">
  <![endif]-->
</head>