<?php
require 'inc/ojsettings.php';
require 'inc/checklogin.php';
require 'inc/functions.php';

if(isset($_GET['start_id']))
  $query_id=intval($_GET['start_id']);
else
  $query_id=2100000000;

if(isset($_GET['problem_id'])){
  $cond_prob='and problem_id='.intval($_GET['problem_id']);
  $query_prob=substr($cond_prob, 4);
}else{
  $query_prob=$cond_prob='';
}
require 'inc/database.php';
$subquery="select thread_id from message where thread_id<$query_id $cond_prob order by thread_id desc limit 20";
$res=mysqli_query($con,"select min(thread_id) from ($subquery) as tmptab");
if(!$res)
  die('Wrong Argument.');
$row=mysqli_fetch_row($res);
$range=$row[0];

function get_pre_link($top)
{
  require ('inc/database.php');
  global $cond_prob;
  $res=mysqli_query($con,"select max(thread_id) from (select thread_id from message where thread_id>=$top $cond_prob order by thread_id limit 20) as tmptab");
  $row=mysqli_fetch_row($res);
  if($row[0])
    $pre=$row[0]+1;
  else
    $pre=2100000000;
  return $pre;
}

$inTitle='讨论';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require 'head.php'; ?>
  <body>
    <script type="text/x-mathjax-config">
    MathJax.Hub.Config({
      skipStartupTypeset:true
    });
    </script>
    <?php require 'inc/mathjax_head.php';
	require 'page_header.php'; ?>
      <div class="replypanel collapse panel panel-default well-replypanel" style="min-width:40%" id="replypanel">
        <div class="panel-heading">
          <h4 class="panel-title"></h4>
        </div>
		<div class="panel-body">
        <form class="form-horizontal" method="post" action="#" id="form_submit">
            <input type="text" style="display:none" id="msg_op" name="op" value="msg_create">
            <div class="form-group">
              <label class="col-xs-2" for="msg_input">标题</label>
			  <div class="col-xs-10">
				<input class="form-control" id="msg_input" name="message" placeholder="请输入消息标题...">
			  </div>
		   </div>
            <div class="form-group">
              <label class="col-xs-2" for="detail_input">内容</label>
			  <div class="col-xs-10">
				<textarea class="form-control" id="detail_input" rows="7" name="detail" placeholder="请输入消息内容..."></textarea>
			  </div>
			</div>
            <div id="PreviewPopover" class="popover left" >
              <div class="arrow"></div>
              <div class="popover-inner">
                <h3 class="popover-title">预览<a class="close" style="line-height: inherit">×</a></h3>
                <div class="popover-content">
                  <pre><div id="preview_content"></div></pre>
                </div>
              </div>
            </div>
            <div style="float:left">
              <span id="post_preview" class="btn btn-default">预览</span>
            </div>
            <div style="float:right">
              <button type="submit" id="post_submit" style="margin-left:20px" class="btn btn-primary shortcut-hint" title="Alt+S">发表</button>
              <button id="cancel_input" class="btn btn-default">取消</button>
            </div>
            <div class="text-center text-error"><strong id="post_status"></strong></div>
          <input type="hidden" name="message_id" id="msgid_input">
          <?php if(isset($_GET['problem_id'])){
            echo '<input type="hidden" name="problem_id" value="',$_GET['problem_id'],'">';
          }?>
        </form>
		</div>
      </div>
    <div class="alert collapse text-center alert-popup" id="alert_error"></div>
    <div class="container">
      <div class="row">
        <div class="col-xs-12" id="board">
		    <a href="#" title="Alt+N" class="btn btn-primary shortcut-hint" id="new_msg"><i class="fa fa-fw fa-commenting"></i> 新建讨论...</a>
		  <?php
            $top=$query_id;
            if($range){
              $res=mysqli_query($con,"select title,depth,user_id,message_id,in_date,thread_id,problem_id,ASCII(content),usremail from message LEFT JOIN (select user_id as uid,email as usremail from users) as fuckzk on (uid=user_id) where thread_id<$query_id and thread_id>=$range $cond_prob order by thread_id desc,orderNum");
              $deep=-1;
              $top=0;
              while($row=mysqli_fetch_row($res)){
                if($row[1]>$deep){
                  if($deep>-1)
                    echo '<ul class="list-unstyled msg-group">';
                }else{
                  echo '</li>';
                  while($deep>$row[1]){
                    $deep--;
                    echo '</ul></li>';
                  }
                  if($row[1]==0)
                    echo '</ul>';
                }
                $deep=$row[1];
                if($row[5]>$top)
                  $top=$row[5];
                if($deep==0)
                  echo '<hr><ul class="list-unstyled">';
                echo '<li class="msg-item">';
                  echo '<div class="msg msg-box">';
                echo '<div class="msg-container"><div class="media"><a class="msg-avatar pull-left" href="javascript:void(0)" onclick="return show_user(\''.$row[2].'\')"><img src="'.get_gravatar($row[8],40).'" class="img-circle media-object"></a><div class="media-body"><strong><a href="javascript:void(0)" onclick="return show_user(\''.$row[2].'\')">',$row[2],'</a></strong> ',$row[4];
                if($row[3]==$row[5] && $deep>0)
                  echo '&nbsp;<span class="label label-warning" style="font-size:12px">最新消息</span>';
                if($deep==0 && $row[6])
                    echo '&nbsp;&nbsp;<a class="prob_link" href="problempage.php?problem_id=',$row[6],'">题目#',$row[6],'</a>';
					echo '<div class="btn-group"><button onclick="open_replypanel(',$row[3],')" class="btn btn-default btn-sm"><i class="fa fa-fw fa-reply"></i> 回复</button>';
					if(isset($_SESSION['user'])&&$row[2]==$_SESSION['user']) 
					  echo ' <button onclick="open_editpanel(',$row[3],')" class="btn btn-default btn-sm"><i class="fa fa-fw fa-pencil"></i> 编辑</button>';
                    echo '</div></div>';
                if($row[7])
                  echo '<p class="msg-content msg-detailed">';
                else
                  echo '<p class="msg-content">';
                echo '<a class="msg-link msg-title" href="#" id="msg',$row[3],'">',htmlspecialchars($row[0]),'</a>';
                echo '</p></div></div></div>';
              }
              echo '</li>';
              while($deep>0){
                $deep--;
                echo '</ul></li>';
              }
              echo '</ul>';
              $top++;
            }else{?>
            <div class="text-center none-text none-center">
              <p><i class="fa fa-meh-o fa-4x"></i></p>
              <p><b>Whoops</b><br>
              看起来这里什么也没有</p>
            </div>
          <?php }?> 
        </div> 
      </div>
      <div class="row">
        <ul class="pager">
          <li>
            <a class="pager-pre-link shortcut-hint" title="Alt+A" <?php if(get_pre_link($top)!='2100000000') echo 'href="board.php?'.$query_prob.'&amp;start_id='.get_pre_link($top).'"' ?>> <i class="fa fa-angle-left"></i> 较新的</a>
          </li>
          <li>
            <a class="pager-next-link shortcut-hint" title="Alt+D" <?php if($range&&$query_id>1020) echo 'href="board.php?',$query_prob,'&amp;start_id=',$range.'"'; ?>>较旧的 <i class="fa fa-angle-right"></i></a>
          </li>
        </ul>
      </div>
       <div class="modal fade" id="UserModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">用户信息</h4>
            </div>
            <div class="modal-body" id="user_status">
              <p>信息不可用……</p>
            </div>
            <div class="modal-footer">
              <form action="mail.php" method="post">
                <input type="hidden" name="touser" id="input_touser">
                <?php if(isset($_SESSION['user'])){?>
                <button type="submit" class="btn btn-default pull-left"><i class="fa fa-fw fa-envelope-o"></i> 发私信</button>
                <?php }?>
              </form>
              <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <footer>
        <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>
    </div>
    <script src="/assets/js/bbcode.js"></script>
    <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
    <script type="text/javascript"> 
      function dealwithlinks($jqobj)
        {
          $jqobj.find('a').each(function(){
            var Href = this.getAttribute("href",2);
            Href=Href.replace(/^([ \t\n\r]*javascript:)+/i,'');
            if(!(/(ht|f)tps?:\/\//i.test(Href)))
              Href = "http://"+Href;
            this.href=Href;
          });
        }
      function open_replypanel(msg_id){
          <?php if(isset($_SESSION['user'])){?>
			var title = ((msg_id=='0')?'<i class="fa fa-fw fa-commenting"></i> 新建消息':'<i class="fa fa-fw fa-reply"></i> 新建回复: #'+msg_id);
			$('#msg_op').val('msg_create');
			$('#msgid_input').val(msg_id);
			$('#replypanel h4').html(title);
			$('#post_status').html('');
			$('#PreviewPopover').hide();
			$('#msg_input').val('');
			$('#detail_input').val('');
			$('#replypanel').fadeIn(300);
			$('#msg_input').focus();
          <?php }else{ ?>
		    $('#alert_error').removeClass('alert-info');
			$('#alert_error').addClass('alert-danger');
			$('#alert_error').html('<i class="fa fa-fw fa-remove"></i> 您尚未登录...').fadeIn();
            setTimeout(function(){$('#alert_error').fadeOut();},2000);
		  <?php }?>
          return false;
        }
      function open_editpanel(msg_id){
          <?php if(isset($_SESSION['user'])){?>
          var title = '<i class="fa fa-fw fa-pencil"></i> 编辑消息';
          $('#msg_op').val('msg_edit');
          $('#post_status').html('');
          $('#msg_input').val('');
          $('#detail_input').val('');
          $.ajax({
                  type:"POST",
                  url:"ajax_message.php",
                  data:{"op":'get_message', "message_id":msg_id},
                  success:function(data){
                      $('#msg_input').val($('#msg'+msg_id).html());
                      $('#detail_input').val(data);
                   }
              });
          $('#msgid_input').val(msg_id);
          $('#replypanel h4').html(title);
          $('#replypanel').fadeIn(300);
		  $('#PreviewPopover').hide();
          $('#msg_input').focus();
          <?php }else{ ?>
		  $('#alert_error').removeClass('alert-info');
		  $('#alert_error').addClass('alert-danger');
		  $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> 您尚未登录...').fadeIn();
		  <?php }?>
          return false;
        }
        function show_user(usr){
            $('#user_status').html("<p>正在加载...</p>").load("ajax_user.php?user_id="+usr);
            $('#input_touser').val(usr);
            $('#UserModal').modal('show');
            return false;
        };
      $(document).ready(function(){
        $('#nav_bbs').parent().addClass('active');
		$('#nav_bbs_text').removeClass('hidden-sm');
        $('#board').click(function(E){
          if(! $(E.target).is("a.msg-link"))
            return;
          var ID=E.target.id+'_detail';
          var node=document.getElementById(ID);
          var p=$(E.target).parent();
          if(node){
            $(node).remove();
            p.removeClass("expanded");
          }else{
            if(p.hasClass("msg-detailed")){
              p.addClass("expanded");
              p.after('<pre id="'+ID+'"><div id="'+ID+'_div"></div></pre>');
              $.ajax({
                  type:"POST",
                  url:"ajax_message.php",
                  data:{"op":'get_message'," message_id":E.target.id.substring(3)},
                  success:function(data){
                      dealwithlinks( $('#'+ID+'_div').html(parseBBCode(data)) );
                MathJax.Hub.Queue(["Typeset",MathJax.Hub,(ID+'_div')]);
                   }
              });
            }else{
			  $('#alert_error').removeClass('alert-danger');
			  $('#alert_error').addClass('alert-info');
              $('#alert_error').html('<i class="fa fa-fw fa-info"></i> 本条消息内容为空...').fadeIn();
              setTimeout(function(){$('#alert_error').fadeOut();},2000);
            }
          }
          return false;
        });
        var detail_ele=document.getElementById('detail_input');
        var minW=260,minH=100;
        
        $('#new_msg').click(function(){open_replypanel('0')});
        reg_hotkey(78,function(){$('#new_msg').click()}); //Alt+N

        $('#replypanel form').submit(function(){
          var msg=$.trim($('#msg_input').val());
          if(msg.length==0){
            $('#post_status').html('<i class="fa fa-fw fa-remove"></i> 消息不可为空！');
            return false;
          }
          if(msg.length>150){
            $('#post_status').html('<i class="fa fa-fw fa-remove"></i> 消息太长了！');
            return false;
          }
          post_submit.setAttribute("disabled",true);
          $('#post_status').html('<i class="fa fa-fw fa-spinner fa-spin"></i> 正在发表...');
		  $.ajax({
            type:"POST",
            url:"ajax_message.php",
            data:$('#form_submit').serialize(),
            success:function(msg){
              if(/success/.test(msg))
                location.reload();
              else{
                post_submit.removeAttribute("disabled");
                $('#post_status').html('<i class="fa fa-fw fa-remove"></i> 发生错误:'+msg);
               }
            }
          });

          return false;
        });
        reg_hotkey(83,function(){$('#replypanel form').submit()}); //Alt+S

        $('#cancel_input').click(function(){
          $('#replypanel').fadeOut(300);
          return false;
        });
        $('#replypanel').keyup(function(E){
          E.which==27 && $('#replypanel').fadeOut(300);
        });
        $('#post_preview').click(function(){
          var data=$('#detail_input').val();
          data=$('<div/>').text(data).html();
          dealwithlinks( $('#preview_content').html(parseBBCode(data)));
          $('#PreviewPopover').fadeIn(300);
          MathJax.Hub.Queue(["Typeset",MathJax.Hub,('preview_content')]);
        });
        $('#PreviewPopover a.close').click(function(){
          $('#PreviewPopover').fadeOut(300);
        });
        function move_handle(E){
          var w=origX-E.clientX+origW;
          var h=E.clientY-origY+origH;
          if(w>=minW){
            $(detail_ele).width(w);
            $('#msg_input').width(w);
          }
          if(h>=minH)
            $(detail_ele).height(h);
        }
        $('body').mouseup(function(){
          $('body').unbind('mousemove');
        });
        $('body').mouseleave(function(){$('body').unbind('mousemove');});
      }); 
    </script>
  </body>
</html>
