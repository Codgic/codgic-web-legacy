<?php
require 'inc/ojsettings.php';
require('inc/checklogin.php');
$inTitle='关于';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require('head.php'); ?>
  <body>
    <?php require('page_header.php') ?>  
          
    <div class="container-fluid about-page">
      <div class="row-fluid">
        <div class="offset2 span8" style="font-size:16px">
          <div class="page-header">
            <h2>简介</h2>
          </div>
		  <div>
          <p>这是一个充斥着辣鸡代码的BugOJ，同时是成外第一个内部OJ。</p>
          <p>本OJ基于GitHub上的开源项目Bashu OnlineJudge二次开发，原本优秀的代码风格被改得面目全非。&#x1F602;</p>
		  <p>然而，对于成(shui)外(wang)并没有什么卵办法，还是得用这套OJ...</p>
		  </div>
        </div>
	  </div>
	  <div class="row-fluid">
        <div class="offset2 span8" style="font-size:16px">
          <div class="page-header">
            <h2>关于</h2>
          </div>
		  <div>
		    <p><b>版本信息:</b><p>
		    <p>网页端: <?php echo"{$web_ver}"?></p>
		    <p>评测端: <?php echo"{$daemon_ver}"?></p>
			<hr>
            <p><b>CWOJ Team：</b></p>
		    <p>jimmy19990： 前端和后端。</p>
		    <p>Void： 题库管理。</p>
		    <p>dreamfly： 题库管理。</p>
          </div>
		</div>
	  </div>
      <div class="row-fluid">
        <div class="offset2 span8" style="font-size:16px">
          <div class="page-header">
            <h2>问&答</h2>
          </div>
          <div>
            <h3>问：本OJ采用什么编译器进行编译？编译语句是什么？</h3>
            <p>答：管那么多干嘛！</p>
            <p>我们使用GNU GCC/G++来编译C/C++程序，用Free Pascal来编译Pascal程序，环境为<?php echo $os_name?>。</p>
            <p>其中，编译语句为：</p>
            <table class="table table-striped table-bordered table-condensed table-last-left-aligned" id="tab_options">
              <tbody>
                <tr>
                  <td>C++</td>
                  <td>g++ -static -fno-asm -s -w -lm -O -DONLINE_JUDGE</td>
                </tr>
                <tr>
                  <td>C</td>
                  <td>gcc -static -fno-asm -s -w -lm -O -DONLINE_JUDGE</td>
                </tr>
                <tr>
                  <td>Pascal</td>
                  <td>fpc -Xs -Sgic -dONLINE_JUDGE</td>
                </tr>
                <tr>
                  <td>C++ 11</td>
                  <td>g++ -static --std=gnu++0x -lm -O -DONLINE_JUDGE</td>
                </tr>
              </tbody>
            </table>
          </div>
          <hr>
          <div>
            <h3>问：有必要写文件操作吗？</h3>
            <p>答：使用标准的输入输出就行了。</p>
			<p>用户的程序中不允许出现任何文件操作，如果进行的话祝您RE愉快（Runtime Error）。</p>
          </div>
          <hr>
          <div>
            <h3>问：评测程序的返回信息都是什么意思？</h3>
            <p>答：这里有一个列表：</p>
            <table class="table table-condensed table-last-left-aligned" id="about_msg">
              <tbody>
                <tr>
                  <td><span class="label label-success">Accepted</span></td>
                  <td><p>AC了！恭喜你的程序通过了所有的测试点！</p></td>
                </tr>
                <tr>
                  <td><span class="label">Compile Error</span></td>
                  <td><p>你的程序连编译都没通过，再去本地测试一下吧。当然，在线编译过程中Warning是被忽略掉了的。注意一下Linux保留字问题！</p></td>
                </tr>
                <tr>
                  <td><span class="label label-warning">Memory Exceeded</span></td>
                  <td><p>你的程序爆内存限制啦，去优化一下你的算法吧。</p></td>
                </tr>
                <tr>
                  <td><span class="label label-info">Runtime Error</span></td>
                  <td><p>你的程序在运行过程中出现了错误（如：不合法的文件操作、爆栈、指针溢出、浮点数异常、傻逼地除以0等）。</p></td>
                </tr>
                <tr>
                  <td><span class="label label-warning">Time Out</span></td>
                  <td><p>你的程序运行慢如乌龟，去优化一下吧。</p></td>
                </tr>
                <tr>
                  <td><span class="label label-important">Wrong Answer</span></td>
                  <td><p>你的程序输出了错误的答案。</p></td>
                </tr>
                <tr>
                  <td><span class="label label-inverse">Validator Error</span></td>
                  <td><p>检查程序在验证你的答案时的输出时表现出异常行为，若非你图谋不轨，请向我们报告。</p></td>
                </tr>
              </tbody>
            </table>
			<hr>
          <p><h4>如果您还有其他任何问题，建议或是错误报告，请发布到： <a href="board.php">讨论中心</a>。</h4></p>
          </div>
        </div>
      </div>
      <hr>
      <footer>
        <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>
    </div>
    </div>
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/common.js"></script>
    <script type="text/javascript"> 
      $(document).ready(function(){
        $('#ret_url').val("about.php");
        $('#nav_about').parent().addClass('active');
      });
    </script>
  </body>
</html>
