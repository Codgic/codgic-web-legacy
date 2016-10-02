function htmlEncode(str) {
	var s = "";
	if (str.length == 0) return "";
	s = str.replace(/&/g, "&amp;");
	s = s.replace(/ /g, "&nbsp;");
	s = s.replace(/</g, "&lt;");
	s = s.replace(/>/g, "&gt;");  
	s = s.replace(/\'/g, "&#39;");
	s = s.replace(/\"/g, "&quot;");
	return s;
}
function encode_space(str) {
	var s="";
	if(str.length == 0) return "";
	s=str.replace(/\r?\n/g, "<br>");
	s=s.replace(/ /g, "&nbsp;");
	s=s.replace(/\t/g, "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
	return s;
}
function LoadCSS(url) {
  	var head = document.getElementsByTagName('head')[0];
  	var link = document.createElement('link');
  	link.type = 'text/css';
  	link.rel = 'stylesheet';
 	link.href = url;
 	head.appendChild(link);
  	return link;
}
function InsertString(tb, str){
    //var tb = document.getElementById(tbid);
    tb.focus();
    if (document.all){
	var r = document.selection.createRange();
	document.selection.empty();
	r.text = str;
	r.collapse();
	r.select();
    }
    else{
        var newstart = tb.selectionStart+str.length;
        tb.value=tb.value.substr(0,tb.selectionStart)+str+tb.value.substring(tb.selectionEnd);
        tb.selectionStart = newstart;
        tb.selectionEnd = newstart;
    }
}
function GetSelection(tb){
    var sel = '';
    if (document.all){
        var r = document.selection.createRange();
        document.selection.empty();
        sel = r.text;
    }
    else{
    	//var tb = document.getElementById(tbid);
    	// tb.focus();
        var start = tb.selectionStart;
        var end = tb.selectionEnd;
        sel = tb.value.substring(start, end);
    }
    return sel;
}
function GetUrlParms()    
{
    var args=new Object();
    var query=location.search.substring(1);
    var pairs=query.split("&");
    for(var i=0;i<pairs.length;i++)
    {
        var pos=pairs[i].indexOf('=');
        if(pos==-1) 
        	continue;
        var argname=pairs[i].substring(0,pos);
        var value=pairs[i].substring(pos+1);
        args[argname]=decodeURIComponent(value);
    }
    return args;
}
function BuildUrlParms(obj) {
	var arr = [];
	for (var name in obj){
		arr.push(name+'='+encodeURIComponent(obj[name]));
	}
	return '?'+arr.join('&');
}
shortcuts={
	"65": function(){
			try{
				$('ul.pager>li>a.pager-pre-link').get(0).click();
			}catch(exp){}
		} , //alt+A
	"68": function(){
			try{
				$('ul.pager>li>a.pager-next-link').get(0).click();
			}catch(exp){}
		} , //alt+D
	"66": function(){location.href=$('#nav_bbs').attr('href');} , //alt+B
	"67": function(){location.href=$('#nav_cont').attr('href');} , //alt+C
	"80": function(){location.href=$('#nav_set').attr('href');} , //alt+P
	"82": function(){location.href=$('#nav_record').attr('href');} , //alt+R
	"73": function(){$('#nav_searchbtn').click();} , //alt+I
	"76": function(){$("#nav_login").click();} , //alt+L
	"77": function(){
			var obj=$('#nav_mail');
			if(obj.length) //if logged in
				location.href=obj.attr('href');
		}   //Alt+M

};
shortcuts[49]=shortcuts[67]; //Alt+1
shortcuts[50]=function(){location.href=$('#nav_set').attr('href');} //Alt+2
shortcuts[51]=shortcuts[66]; //Alt+3
shortcuts[52]=shortcuts[82]; //Alt+4
shortcuts[53]=function(){location.href=$('#nav_rank').attr('href');} //Alt+5
shortcuts[54]=function(){location.href=$('#nav_about').attr('href');} //Alt+6
shortcuts[55]=shortcuts[73]; //Alt+7

function hotkey_hint_show () {
	$('.shortcut-hint').addClass('shortcut-hint-active');
}
function hotkey_hint_dismiss (E) {
	if(E.keyCode==18){ //alt key
		$('.shortcut-hint').removeClass('shortcut-hint-active');
	}
}
function reg_hotkey (key, fun) {
	shortcuts[key] = fun; }
function change_type(e){
	$('#search_type').val(e);
	$('#search_select').html($('#type'+e).html());
	$('#search_input').focus();
}
$(document).ready(function(){
	var $notifier=$('.notifier'),msgnum=0;
	$('#nav_logoff').click(function(){$.ajax({url:"/api/ajax_logoff.php",dataType:"html",success:function(){location.reload();}});});
	$('#search_span').hover(function(){ 
            $(this).addClass('open'); 
       },function(){
            $(this).removeClass('open'); 
        });
	$('#search_form').submit(function(){
		if($.trim($('#search_input').val()).length==0)
			return false;
		return true;
	});
	function checkMail(){
		$.get("/api/ajax_mailfunc.php?op=check",function(data){
			if(data=='-1')
				window.location.reload();
			else if(isNaN(data)||data=='0')
				return;
			$notifier.html(data);
			if(data>msgnum){
				msgnum=data;
				$('#alert_newmsg').fadeIn();  
				setTimeout(function(){$('#alert_newmsg').fadeOut()},2000);  
			}           
		});
	}
        checkMail();
        setInterval(checkMail,120000);

}).keydown(function(E){
	if(window.hidehotkey)
		return;
	if(E.altKey && !E.metaKey){
		var key=E.keyCode;
		if(key>=97 && key<=122)
			key-=32;
		else if(key==18){ //alt key
			hotkey_hint_show(E);
			return;
		}
		if(shortcuts.hasOwnProperty(key))
			(shortcuts[key])(E);
		return false;
	}
}).keyup(hotkey_hint_dismiss);
$('#search_input').keyup(hotkey_hint_dismiss);
$('#nav_searchbtn').click(function(){
	$('#search_form').addClass("visible-inline-block");
        $('#nav_back').show();
	$('#nav_left').addClass('hidden-xs hidden-sm hidden-md');
	$('#search_input').focus();
});
$('#nav_clrsearch').click(function(){
	$('#search_input').val('');
       	$('#nav_back').hide();
	$('#search_form').removeClass("visible-inline-block");
	$('#nav_left').removeClass('hidden-xs hidden-sm hidden-md');
});
