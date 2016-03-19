<!DOCTYPE html>
<head>
  <? require('head.php');?>
  <title>请升级你的浏览器</title>
  <base target="_blank" />
  <style type="text/css">
  body,h1,h2,h3,h4,h5,h6,hr,p,blockquote,dl,dt,dd,ul,ol,li,pre,form,fieldset,legend,button,input,textarea,th,td{margin:0;padding:0}
  a{text-decoration:none;color:#0072c6;}a:hover{text-decoration:none;color:#004d8c;}
  body{width:960px;margin:0 auto;padding:10px;font-size: 18px;line-height:24px;color:#454545;font-family:'Microsoft YaHei UI','Microsoft YaHei',DengXian,SimSun,'Segoe UI',Tahoma,Helvetica,sans-serif;}
  h1{font-size:45px;line-height:80px;font-weight:200;margin-bottom:10px;}
  h2{font-size:30px;line-height:45px;font-weight:200;margin:10px 0;}
  p{margin-bottom:10px;}
  .line{clear:both;width:100%;height:1px;overflow:hidden;line-height:1px;border:0;background:#ccc;margin:10px 0 30px;}
  img{width:34px;height:34px;border:0;float:left;margin-right:10px;}
  span{display:block;font-size:18px;line-height:24px;}
  .clean{clear:both;}
  .browser{padding:10px 0;}
  .browser li{width:220px;float:left;list-style:none;}
  </style>
</head>
<body>
  <h1>是时候升级你的浏览器了</h1>
  <p>你正在使用不支持CWOJ的浏览器。这意味着在升级浏览器前，你将无法访问CWOJ。</p>
  <ul class="browser">
    <li><a href="http://windows.microsoft.com/zh-cn/internet-explorer/download-ie"><img src="http://www.goody.com.cn/2014/updatebrowser/images/ie.jpg" alt="最新IE浏览器" /> 最新IE浏览器<span>Internet Explorer</span></a></li>
	<div class="clean"></div>
  </ul>
  <div class="line"></div>
  <h2>更先进的浏览器</h2>
  <p>推荐使用以下浏览器的最新版本。如果你的电脑已有以下浏览器的最新版本则直接使用该浏览器访问<b id="referrer"></b>即可。</p>
  <ul class="browser">
    <li><a href="https://www.microsoft.com/en-us/windows/microsoft-edge"><img src="assets/res/edge.png" alt="Edge浏览器" /> Edge浏览器<span>Microsoft Edge</span></a></li>
	<li><a href="https://www.google.com/intl/zh-CN/chrome/browser/index.html"><img src="assets/res/chrome.png" alt="谷歌浏览器" /> 谷歌浏览器<span>Google Chrome</span></a></li>
	<li><a href="http://www.firefox.com.cn/download"><img src="assets/res/firefox.png" alt="火狐浏览器" /> 火狐浏览器<span>Mozilla Firefox</span></a></li>
	<li><a href="http://www.opera.com/download/get"><img src="assets/res/opera.png" alt="Opera浏览器" /> Opera浏览器<span>Opera Explorer</span></a></li>
	<div class="clean"></div>
	</ul>
   <div class="line"></div>
   <h2>为什么会出现这个页面？</h2>
   <p>如果你不知道升级浏览器是什么意思，请你关闭这个页面，因为CWOJ极可能不适用于你。</p>
</body>
<script type="text/javascript">
var browser=navigator.appName 
var b_version=navigator.appVersion 
var version=b_version.split(";"); 
var trim_Version=version[1].replace(/[ ]/g,""); 
if(browser!="Microsoft Internet Explorer") { 
window.location = "index.php"; 
} else {
	if(browser=="Microsoft Internet Explorer" && trim_Version=="MSIE11.0" || trim_Version=="MSIE10.0" || trim_Version=="MSIE9.0" || trim_Version=="MSIE8.0")
		window.location = "index.php"; 
}
function Request(name){
     new RegExp("(^|&)"+name+"=([^&]*)").exec(window.location.search.substr(1));
     return RegExp.$2
}
var referrer = Request("referrer");
var url = document.referrer;
if(!url==""){
x=document.getElementById("referrer")
x.innerHTML='&nbsp;<a href="'+url+'">'+url+'</a>&nbsp;';
}else if(!referrer==""){
x=document.getElementById("referrer")
x.innerHTML='&nbsp;<a href="'+referrer+'">'+referrer+'</a>&nbsp;';
}
</script>
</body>
