<?php
/**
 * Class that handles pdf convertion
 **/

require_once("fpdf17/fpdf.php");

class PDF extends FPDF
{
	
	// Cabecera de página
	function Header()
	{
		// Logo
		//$this->Image('escudo_vasco.jpg',10,8,33);
		// Arial bold 15
		$this->SetFont('Arial','B',15);
		// Movernos a la derecha
		$this->Cell(80);
		// Título
		$this->Cell(30,10,'REQUISICIONES CBC - ID '.$this->requisition_id,0,0,'C');
		// Salto de línea
		$this->Ln(15);
	}
	
	public function set_requisition_id($id){
		$this->requisition_id = $id;
	}

	// Pie de página
	function Footer()
	{
		// Posición: a 1,5 cm del final
		//$this->SetY(20);
		// Arial italic 8
		$this->SetFont('Arial','I',6);
		$this->Ln(0);
		// Número de página
		$this->MultiCell(0,5,utf8_decode("Este Documento es una copia controlada, cualquier reproducción electrónica y/o física sea esta parcial o total del mismo está prohibida. Una vez impreso este documento Gente & Gestión Corporativo no se hace responsable por su actualización."));
	}
}
