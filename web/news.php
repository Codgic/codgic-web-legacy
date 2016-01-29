<?php
require 'inc/ojsettings.php';
require('inc/checklogin.php');
$inTitle='新闻';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require('head.php'); ?>

  <!--<body>
    <?php require('page_header.php') ?>
    <div class="container-fluid">
      <div class="row-fluid">
      <div class="pagination pagination-centered">
      <ul>
        <li><a href="level.php?level=1">按等级分类 &raquo;</a></li>
      </ul>
      </div>
      </div>
      <div class="row-fluid">
        <div class="span10 offset1">
            <?php //require 'problemset_table.php';?>
        </div>  
      </div>
      <div class="row-fluid">
        <ul class="pager">
          <li>
            <a class="pager-pre-link shortcut-hint" title="Alt+A" href="#" id="btn-pre">&larr; 上一页</a>
          </li>
          <li>
            <a class="pager-next-link shortcut-hint" title="Alt+D" href="#" id="btn-next">下一页 &rarr;</a>
          </li>
        </ul>
      </div>
      <hr>
      <footer>
      <p>&copy; <?php echo"{$year} {$copyright}";?></p>
      </footer>
    </div>
    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/common.js"></script>

    <script type="text/javascript"> 
      $(document).ready(function(){
        var cur_page=<?php //echo $page_id ?>;
        $('#nav_set').parent().addClass('active');
        $('#ret_url').val("problemset.php?page_id="+cur_page);

        $('#problemset_table').click(function(E){
          var $target = $(E.target);
          if($target.is('i.save_problem')){
            var pid = $target.attr('data-pid');
            var op;
            if($target.hasClass('icon-star'))
              op='rm_saved';
            else
              op='add_saved';
            $.get('ajax_saveproblem.php?prob='+pid+'&op='+op,function(result){
              if(/__ok__/.test(result)){
                $target.toggleClass('icon-star-empty')
                $target.toggleClass('icon-star')
              }
            });
          }
        });
        $('#btn-next').click(function(){
          <?php
            if($page_id<$maxpage){
              $nextpage=$page_id+1;
              echo "location.href='problemset.php?page_id=$nextpage';";
            }
          ?>
          return false;
        });
        $('#btn-pre').click(function(){
          if(cur_page>10)
            location.href='problemset.php?page_id='+(cur_page-1);
          return false;
        });
      });
    </script>
  </body>-->
</html>
