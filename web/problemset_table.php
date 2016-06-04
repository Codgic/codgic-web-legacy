<table class="table table-striped table-bordered" id="problemset_table">
  <thead><tr>
    <th style="width:6%">ID</th>
    <?php 
      if(isset($_SESSION['user']))
        echo '<th colspan="3">标题</th>';
      else
        echo '<th>标题</th>';
    ?>
    <th style="width:10%">AC比例</th>
    <th style="width:6%">通过率</th>
    <th style="width:25%">题目标签</th>
  </tr></thead>
  <tbody>
    <?php 
      while($row=mysqli_fetch_row($result)){
        echo '<tr>';
        echo '<td>',$row[0],'</td>';
        if(isset($_SESSION['user'])){
          echo '<td class="width-for-2x-icon"><i class=', is_null($row[6]) ? '"fa fa-remove fa-2x" style="visibility:hidden"' : ($row[6]? '"fa fa-remove fa-2x" style="color:red"' : '"fa fa-2x fa-check" style="color:green"'), '></i>', '</td>';
          echo '<td style="text-align:left;border-left:0;">';
        }else{
          echo '<td style="text-align:left">';
        }
        echo '<a href="problempage.php?problem_id=',$row[0],'">',$row[1];
        if($row[5]=='Y')echo '&nbsp;&nbsp;<span class="label label-important">已删除</span>';
        echo '</a>';
        if(isset($_SESSION['user'])){
          echo '<td class="width-for-2x-icon" style="border-left:0;"><i data-pid="',$row[0],'" class="', is_null($row[7]) ? 'fa fa-star-o' : 'fa fa-star', ' fa-2x text-warning save_problem" style="cursor:pointer;"></i></td>';
        }
        echo '</td>';
        echo '<td><a href="record.php?result=0&amp;problem_id=',$row[0],'">',$row[2],'</a>/';
        echo '<a href="record.php?problem_id=',$row[0],'">',$row[3],'</a></td>';
        echo '<td>',$row[3] ? intval($row[2]/$row[3]*100) : 0,'%</td>';
        echo '<td style="text-align:left;">',$row[4],'</td>';
        echo "</tr>\n";
      }
    ?>
  </tbody>
</table>
