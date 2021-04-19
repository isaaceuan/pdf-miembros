

<?php
require ("clases.php");
require('fpdf/fpdf.php');





$membresia = 2019021008;


$perfil = new Miembros();
$array_perfil = $perfil->get_pdf($membresia);
foreach ($array_perfil as $valor) {
$theDate    = new DateTime($valor["fecha_fin"]);
$stringDate = $theDate->format('d/m/Y');
// echo $valor["nombres"];
    
$pdf=new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',12);
$pdf->SetTextColor(0,0,0);


$pdf->SetXY(-130, -310);
$pdf->Cell(500,60, strtoupper("Credencial Generada "), 500,100);
$pdf->SetTextColor(0,0,0);
$pdf->SetXY(-185, -200);
$pdf->SetFont('Arial','',10);
$pdf->Cell(500,60,'Imprime esta credencial para uso exclusivo de la Asociacion Nacional de Parques Recretivos(ANPR)', 500,100);


$pdf->SetFont('Arial','B',12);

$pdf->Image('fondo_credencial.png',55,30,100);
$pdf->SetTextColor(255,255,255);
$pdf->SetXY(65, 30);
$pdf->Cell(500,60, strtoupper( $valor["nombres"]." ".utf8_decode($valor["apellido_paterno"])), 500,100);
$pdf->SetXY(65, 40);
$pdf->Cell(500,70,'Numero de miembro:', 500,100);
$pdf->SetXY(118, 40);
$pdf->Cell(500,70,$valor["num_membresia"], 500,100);
$pdf->SetXY(65, 50);
$pdf->Cell(500,70,'Valido Hasta:', 500,100);
$pdf->SetXY(118, 50);
$pdf->Cell(500,70,$stringDate, 500,100);
$pdf->Output();


}


?>
