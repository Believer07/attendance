<?php
session_start();


if(!isset($_SESSION["userid"]) || !isset($_POST['classdetail']))
  header('Location: index.php');

require_once 'conn_iet.php';
$domain_name="Attendance System";
$class = $_POST['classdetail'];
$subject = $_POST['subjectdetail'];
$batch = $_POST['batch'];
$_SESSION["batch"]=$batch;
$limit=$_POST['limit'];
$session="";
$result=mysqli_query($conn,"select id from schedule_table where class_id=$class and subject_id=$subject and batch=$batch");

$count=mysqli_num_rows($result);
        if($count==1)
          {
            $row=mysqli_fetch_array($result,MYSQLI_ASSOC);
            $_SESSION['schedule']=$row['id'];
            $S_id=$row['id'];
        //echo $S_id;
           }

$result=mysqli_query($conn,"SELECT * FROM schedule_table WHERE schedule_table.class_id=$class AND schedule_table.subject_id=$subject AND schedule_table.batch=$batch;");
        //setting up relative date
        // end of changes
$T_present= array();#creates array
if($result->num_rows > 0){
      while($row =$result->fetch_assoc())
        {
       $lecture_no= $row["last_lecture_no"];
      // print_r($row);
       for($i=1;$i<=$lecture_no;$i++)
          {    $l='l'.$i;
               $dl=$row[$l];
               $sdate[$l]=$dl;
               //print_r($sdate);
          }
             asort($sdate);
             $session = substr($dl,0,4);
            // echo $_POST['relative'];
  #relative feature// not needed for now
        if(($_POST['relative']==1))
          {
              $date_val=$_POST['date1'];
              $date_val = array_search($date_val, array_keys($sdate));//$date_val m selected date ka lecture number aa rha h
              $start=$date_val;
          }
       else
       {
         $start=0;
      }
            if($limit>0 && $limit<=$lecture_no)
             {
               $lecture_no=$limit+$start;
             }
           else
            {
              $lecture_no= $row["last_lecture_no"];
            }
            $date="";

          foreach($sdate as $key => $x_value) {
           // print_r($sdate.$key.":".$x_value."<br>");

            $P_Query=mysqli_query($conn,"SELECT COUNT($key) as Pno FROM attendance_table WHERE schedule_id=$S_id;");
            if ($P_Query->num_rows==1)
            {
               while($val = $P_Query->fetch_assoc())
                {
                  array_push($T_present, $val["Pno"]);
                }
            }
         }
      }
  }

  $col="";
$str_count=0;
//echo $start."*".$lecture_no;
//if relative is not set than start is 0,otherwise start=$date_val
foreach($sdate as $key => $x_value) {

 if ($str_count >= $start && $str_count<=$lecture_no)
 {
          $col.="attendance_table.".$key.",";
          $dl=substr($x_value,8,2)."-".substr($x_value,5,2);
          $date.='<td>'.$dl.' <b>('.$T_present[$str_count].')</b>'.'<div class="radio"><input type="radio" id="date" name="date1" value='.$key.'></div></td>';
                      }
   $str_count++;
                 }
                 $str_count++;
 //echo $col;
  $col = substr($col,0,strlen($col)-1);
//echo $col;
//print_r($_POST['view_type']);
switch($_POST['view_type'])
{

    case 0:
        if($batch>0)
        $query="SELECT $col,schedule_id,student_id,present_no from attendance_table,student_table.roll_no,student_table.name FROM schedule_table INNER JOIN attendance_table ON schedule_table.id=attendance_table.schedule_id INNER JOIN student_table ON student_table.id=attendance_table.student_id WHERE schedule_table.class_id=$class AND schedule_table.subject_id=$subject AND schedule_table.batch=$batch and student_table.batch=$batch;";
        else
        $query="SELECT $col,schedule_id,student_id,present_no,student_table.roll_no,student_table.name FROM schedule_table INNER JOIN attendance_table ON schedule_table.id=attendance_table.schedule_id INNER JOIN student_table ON student_table.id=attendance_table.student_id WHERE schedule_table.class_id=$class AND schedule_table.subject_id=$subject AND schedule_table.batch=$batch;";

    break;
}

$result=mysqli_query($conn,$query);

if($_POST['less_than'])
{
    echo $_POST['less_than'];
}

$list="";

//echo $result->num_rows;
if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                    foreach($sdate as $x => $x_value) {                                                        $li=$x;
                            if($row["$li"]==1){
                                     $row["$li"]='P';
                            }
                           else if($row["$li"]==0)
                             {
                              $row["$li"]='A';
                            }
                                      }
                                   if($row["present_no"]==NULL)
                                            {
                                              $n=1;
                                               $m=0;
                                              }
                                    else
                                               $m=$lecture_no;
                                                //setting up division factor
                                                if($_POST['relative']==1)
                                                {
                                                   $divider=$lecture_no-$start+1;
                                                }
                                                else{
                                                   $divider=$lecture_no;
                                                  }
                                                //end of changes

                                         $num="";$count=0;$str_count=0;
                                              foreach($sdate as $x => $x_value) {
                                               if ($str_count >= $start && $str_count<=$lecture_no){
                                                    $li=$x;
                                                    $num.='<td id="status">'.$row["$li"].'</td>';
                                                    if($row["$li"]=="P")
                                                      $count++;
                                                    }
                                          $str_count++;
                                      } $str_count++;
                $row["present_no"]=$count;
                $count=0;
                $list.='<tr><td>'.'<input type="checkbox" name="foo" class="get_values"'.'value='.$row["student_id"].'>'.'</td><td>'.$row["roll_no"].'</td><td>'.$row["name"].'</td>'.$num.' <td>'.$row["present_no"].'</td> <td>'.(int)($row["present_no"]*100/$divider).'%</td></tr>';
            }
}else {
  }
?>
<!-- ************end of php backend*************** -->
<!-- yha se wha code copy kia h -->
<head><title>View attendance</title>
<link rel="stylesheet" type="text/css" href="admin/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="admin/css/buttons.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="admin/css/fixedColumns.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.css"/>

<style>
 th, td { white-space: nowrap; }
    div.dataTables_wrapper {
        width: 100%;
        margin: 0 auto;
    }
</style>

<!-- ********************************************test boundar*********************************************************************-->
<!-- *******************************yaha se js start hua hai*********************************** -->
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<?php include("header.php"); ?>

<script src="admin/js/jquery.dataTables.min.js"></script>
<script src="admin/js/dataTables.buttons.min.js"></script>
<script src="admin/js/buttons.flash.min.js"></script>
<script src="admin/js/jszip.min.js"></script>
<script src="admin/js/pdfmake.min.js"></script>
<script src="admin/js/vfs_fonts.js"></script>
<script src="admin/js/buttons.html5.min.js"></script>
<script src="admin/js/buttons.print.min.js"></script>
<script src="admin/js/dataTables.fixedColumns.min.js"></script>
<script>
$(document).ready(function(){
  $('#submit').click(function(){
    var languages = [];
    var mstval = $('#mstdrp').val();
    //alert(mstval);
    $('.get_values').each(function(){
      if($(this).is(":checked")){
        languages.push($(this).val());
      }
    });
languages = languages.toString();
//console.log(languages);
$.ajax({
  url:"insert.php",
  method:"POST",
  data:{languages:languages,
       subject_id:<?php echo $subject ?>,
      class_id:<?php echo $class ?>,
      mst:mstval},
  success:function(data){
    alert(data);
    location.reload();
  }
 // alert("please select students");
});
  });
});
</script>
<script>
function newDoc() {
   var mst = document.getElementById('mstdrp').value;
  if (mst =="" || mst==0) {
    alert("Please select mst");
}else{
  //alert(mst);
  window.location.assign("view_detained_students_pdf.php?id=<?php echo $subject;?>"+ "&mst=" + mst);
  // window.location.assign("view_detained_students_pdf.php");
}}
</script>

<!-- <script>
function newDoc1() {
   var mst = document.getElementById('mstdrp').value;
  if (mst =="" || mst==0) {
    alert("Please select mst");
}else{
  //alert(mst);
  window.location.assign("edit_detain_student_panel.php?id=<?php echo $subject;?>"+ "&mst=" + mst);
  // window.location.assign("view_detained_students_pdf.php");
}}
</script> -->

<!-- <script>
//var mstval;
$(document).ready(function(){
  $('#v_d_s').click(function(){
    //var languages = [];
    var mstval = $('#mstdrp').val();
    //alert(mstval);
    if(mstval==null)
    alert("please select mst");
    //return false;
    });
//languages = languages.toString();
//console.log(languages);

  });
</script> -->
<script >
function toggle1(source) {
  checkboxes = document.getElementsByName('detain1[]');
  console.log(checkboxes);
  for(var checkbox in checkboxes)
    checkbox.checked = source.checked;
  return true;
  }
  </script>
<script>
$(document).ready(function() {

    var table = $('#scroll').DataTable( {
        scrollY:        "500px",
        scrollX:        true,
        scrollCollapse: true,
        paging:         false,
        columnDefs: [ { orderable: false,  width: '20%',targets: [<?php for ($i=2;$i<=($divider+1);$i++){echo $i.',' ;}?>] } ],
        <?php if($divider>6){
        echo"
        fixedColumns:   true,
        fixedColumns:   {
            leftColumns: 2,
            rightColumns: 1
        },";}?>
        responsive: true,
        ordering : true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'excel',
        ],
    });
  });
</script>
<script>
    $.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = parseInt( $('#min').val(), 10 );
        var max = parseInt( $('#max').val(), 10 );
        var per = parseFloat( data[<?php echo $divider+4;?>] ) || 0;
        // use data for the percentage column
        if ( ( isNaN( min ) && isNaN( max ) ) ||
             ( isNaN( min ) && per <= max ) ||
             ( min <= per   && isNaN( max ) ) ||
             ( min <= per   && per <= max ) )
        {
            return true;
        }
        return false;
    }
);

$(document).ready(function() {
    var table = $('#scroll').DataTable();
     // Event listener to the two range filtering inputs to redraw on input
    $('#min, #max').keyup( function() {
        table.draw();
    });
});

</script>

<!-- <script>
    function dateCheck(){
        if(!document.edit.date1.value)
        {
            alert("Please select a date");
            return false;
        }
        return true;
    }
 $( document ).ready(function() {
    $('td.status').each(function( index ) {
          if($( this ).text()== 'A'){
             $(this).css("background-color","#FF6947");
              }
          else if($( this ).text()== 'P'){
             $(this).css("background-color","#66ED44");
          }
    });
});
</script>

<script>
    function mstCheck(){
        if(!document.v_d_s.mst.value)
        {
            alert("Please select a mst");
            return false;
        }
        return true;
    }
    $( document ).ready(function() {
       $('td.status').each(function( index ) {
             if($( this ).text()== 'A'){
                $(this).css("background-color","#FF6947");
                 }
             else if($( this ).text()== 'P'){
                $(this).css("background-color","#66ED44");
             }
       });
   });
</script> -->
<!-- <script>
function mstCheck() {
if(document.v_d_s.value) {
if($('#mstdrp option:selected').val('not supplied')) {
    alert("Please select a mst");
    $('#mstdrp').focus();
    return false;
}}
return true;
}</script> -->
</head>
<!-- ********************************************test boundary*********************************************************************-->

<!-- ****************************************yaha js khatam************************************************** -->

<script>
    function dateCheck(){
        if(!document.edit.date1.value)
        {
            alert("Please select a date");
            return false;
        }
        return true;
    }
 $( document ).ready(function() {
    $('td.status').each(function( index ) {
          if($( this ).text()== 'A'){
             $(this).css("background-color","#FF6947");
              }
          else if($( this ).text()== 'P'){
             $(this).css("background-color","#66ED44");
          }
    });
});
</script>
<!-- <script>
function newDoc1() {
   var mst = document.getElementById('mstdrp').value;
  if (mst =="" || mst==0) {
    alert("Please select mst");
}else{
  //alert(mst);
  window.location.assign("edit_detain_student_panel.php?id=<?php echo $subject;?>"+ "&mst=" + mst);
  // window.location.assign("view_detained_students_pdf.php");
}}
</script> -->

<!-- <script>
$('#select-all').click(function(event) {
if(this.checked) {
    // Iterate each checkbox
    $(':checkbox').each(function() {
        this.checked = true;
    });
} else {
    $(':checkbox').each(function() {
        this.checked = false;
    });
}
});
</script> -->
<!-- <script>
    function dateCheck1(){
        if(!document.edit.date1.value)
        {
            alert("date");
            return false;
        }
        return true;
    }
 $( document ).ready(function() {
    $('td.status').each(function( index ) {
          if($( this ).text()== 'A'){
             $(this).css("background-color","#FF6947");
              }
          else if($( this ).text()== 'P'){
             $(this).css("background-color","#66ED44");
          }
    });
});
</script> -->
<script language="JavaScript">
function toggle(source) {
  checkboxes = document.getElementsByName('foo');
  for(var i=0, n=checkboxes.length;i<n;i++) {
    checkboxes[i].checked = source.checked;
  }
}
</script>
<body>
<center>
<?php if(!$list=="") { ?>
<br>
<h4>Total Lectures: <?php echo $divider;?></h4>

<span style="float: right; position: relative; right: 30px; bottom: 30px">
<?php echo 'Academic Session - '.$session;?>
</span>
<!-- </div> -->
        <tbody>
          <table border="0" cellspacing="5" cellpadding="10" style="position: relative; left: 80px">
        <tr id="percentage">
            <td><b>Percentage <=</b></td>
            <td><input  type="text" placeholder="Please enter % of attendance"  class="col-md-12"  id="max" name="detain" style="width: 250px"></td>
        </tr>
</tbody>
</table>
<!-- <form class="btn-sm" method="post" name="v_d_s" action="view_detained_students_pdf.php">
<input type="submit" name="view_detain_std" id="v_d_s" style="width: 15%; margin-top: 10px; float: left; position: relative; left: 110px" class="btn-sm btn-info" value="View Detain Students">
</form> -->

<form class="btn-sm" action="edit_panel.php" method="post" name="edit"
 onsubmit="return dateCheck(this);">
 <!-- <p id="p1"><b>Select Date to see Relative Attendance</b></p> -->
 <div style="width: 100%; height: 55px;">
<button type="submit" name='relative' id="relative" class="btn-sm btn-primary" value="1" formaction="view.php" style="width: 15%; float: left; margin-top:10px; position: relative; left: 20px">Relative</button>
<!-- <input class="col-md-2" type="number" id="relbox" name="limit">     -->
<!-- <div class="col-sm-3 col-md-11 mx-auto"> -->
<input type="submit" id="edit" title="First check a button on any date to edit" value="Edit Attendance" class="btn-sm btn-success btn-block" style="width: 15%; float: left; position: relative; left: 50px; margin-top:10px;"
>
<!-- </div> -->
<!-- style="float: left; width: 15%; margin-right: 5px;margin-top:10px;" -->
<button type="button" name="detain_std" class="btn-sm btn-danger" id="submit" style="width: 15%; margin-top: 10px; float: left; position: relative; left: 80px">Detain Students</button>

<!-- <a href="view_detained_students_pdf.php?id=<?php //echo $subject;?>;mst=mstval"> -->
<input type="button" name="viw_detain_std" class="btn-sm btn-info" id="view_d_s" style="width: 15%; margin-top: 10px; float: left; position: relative; left: 110px" value="View Detain Students" onclick="newDoc()">

<select name="mst" id="mstdrp" style="width: 15%; margin-top: 15px; position: relative; right: 80px;">
<option  value="0"selected disabled hidden>Select MST</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">endsem</option>
</select>
</div>
<a href="admin/generate_pdf.php?schedule=<?php echo $S_id;?>">
<input type="button"  id="pdf" name="schedule" style="width: 15%; float: left; position: relative; left: 20px; margin-bottom: 10px;"  class="btn-sm btn-warning" value="Generate PDF">
</a>

<!-- <input type="button"  id="edit_d_s" name="edit_d_s" style="width: 15%; float: left; position: relative; left: 50px; margin-bottom: 10px;"  class="btn-sm btn-disabled" value="Edit Detain Students" onclick="newDoc1()"> -->
<!-- float: left; position: relative; left: 148px; top: 34px; -->
<!-- <input type="checkbox" onClick="toggle1(this)" />
<div id="check">Check All</div><br/> -->
<!-- self form submission-->
<form  method="post">
<input type="hidden" name="classdetail" value="<?php echo $class;?>">
<input type="hidden" name="subjectdetail" value="<?php echo $subject;?>">
<input type="hidden" name="batch" value="<?php echo $batch;?>">
<div id="tabshow">
<div  style="margin-left: 20px;margin-right: 20px;" >
  <table id="scroll" class="table table-bordered hover stripe row-border order-column">
  <thead class="thead-dark">
       <tr>
      <th><input type ="checkbox" onClick="toggle(this)" ></th>
      <th>Roll No.</th>
      <th>Name</th>
      <?php echo $date;?>
      <th>Present No.</th>
      <th>Percentage</th>
    </tr>
  </thead>
</form>
<!-- <form class="btn-sm" name="v_d_s" method="post" action="view_detained_students_pdf.php" onsubmit="return mstCheck(this);">
<input type="submit" name="view_detain_std" id="v_d_s" style="width: 15%; margin-top: 10px; float: left; position: relative; left: 110px" class="btn-sm btn-info" value="View Detain Students">
</form> -->
</form>

  <tbody id="changeOrder">
<?php echo $list; ?>
<div id="result"></div>
  </tbody>
</table>
</section>
</div>
</div>
  <div class="card-block">
    <blockquote class="card-blockquote">
    <div class="col-4">
  </div>
<div class="row">
</div>
</blockquote>
  </div>

<?php }
else
echo "<br><br><br><br><div class=\"col-xs-12\" style=\"height:200px;\"><h4>No Attendance To Show!!</h4></div>"; ?>
</center>
<?php include("footer.php"); ?>
</body>
