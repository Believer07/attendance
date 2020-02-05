<?php
require_once "conn_iet.php";
require('admin/fpdf.php');
$subject_id = $_GET['id'];
$mst=$_GET["mst"];
// echo $subject_id;
// echo $mst;

$query1="SELECT GROUP_CONCAT(Distinct B.name SEPARATOR ',')
as data,GROUP_CONCAT(Distinct B.roll_no SEPARATOR ',')
AS data1,C.subject_code,C.subject_name,B.name,B.roll_no,A.mst
FROM detained_student AS A LEFT JOIN student_table AS B
ON A.student_id=B.id LEFT JOIN subject_table AS C
ON A.subject_id=C.id WHERE A.subject_id='$subject_id' and A.mst='$mst'";
$result2 = mysqli_query($conn,$query1);
$row2 =$result2->fetch_assoc();
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','U',25);
$pdf->Cell(200,10,'List of Detained Students',0,1,'C',false);
$pdf->SetFont('Arial','B',10);
$pdf->ln(5);
$pdf->Cell(50,10,'Class:',0,0,'C',false);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(50,10,'Year:',0,0,'C',false);
$pdf->SetFont('Arial','B',10);
if($row2['mst']!=4){
$pdf->Cell(50,10,'MST:'.$row2['mst'],0,0,'C',false);}
else {
  $pdf->Cell(50,10,'MST:'."End Sem",0,0,'C',false);
}
$pdf->SetFont('Arial','B',10);
$pdf->Cell(50,10,'Co-ordinator:',0,1,'C',false);
$pdf->ln(2);

$width_cell=array(10,50,20,30);
$pdf->SetFillColor(193,229,252); // Background color of header

// First row of data
$result1 = mysqli_query($conn,$query1);
if($result1->num_rows > 0){
    // Header starts ///
$pdf->SetFont('Arial','U',15);
 $pdf->Cell(200,10,'Subject Code & Subject Name',1,1,'C',true);
 $pdf->MultiCell(200,10,'Roll No.',1,'C',true);
 $pdf->MultiCell(200,10,'Names',1,'C',true);
 $pdf->ln();

 while($row1 =$result1->fetch_assoc()){
    $pdf->SetFont('Times','BU',15);
$pdf->Cell(200,10,'('.$row1['subject_code'].')'.' '.'-'.' '.$row1['subject_name'],1,0,'C',false);
$pdf->ln();
$pdf->SetFont('Times','B',12);
$pdf->MultiCell(200,8,$row1['data1'],1);
$pdf->SetFont('Times','',10);
$pdf->MultiCell(200,5,$row1['data'],1);
$pdf->ln(5);
}
}else{
    $pdf->SetFont('Times','B',50);
    $pdf->ln(25);
    $pdf->cell(45);
    $pdf->Cell(120,10,'No data available',0,0,'C',false);
}

$pdf->Output();

?>
