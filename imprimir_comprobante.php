<?php
require('fpdf186/fpdf.php');

// Incluir el archivo de conexión a la base de datos
include("db_conn.php");

// Configurar la codificación para la conexión a la base de datos
mysqli_set_charset($conn, "utf8");

// Obtener el id_comprobante desde la URL
if (!isset($_GET['id_comprobante'])) {
    exit('Error: No se ha especificado el comprobante.');
}
$id_comprobante = $_GET['id_comprobante'];

// Consulta para obtener los datos del comprobante
$query = "SELECT c.id_comprobante, c.fecha_emision, c.dni_cliente, cl.nombre AS nombre_cliente, cl.telefono, cl.email, a.nombre AS nombre_asesor, c.estado, b.nombre AS nombre_banco, o.descripcion AS descripcion_operacion, c.monto
          FROM comprobante c
          LEFT JOIN asesor a ON c.id_asesor = a.id_asesor
          LEFT JOIN banco b ON c.id_banco = b.id_banco
          LEFT JOIN operacion o ON c.id_operacion = o.id_operacion
          LEFT JOIN cliente cl ON c.dni_cliente = cl.dni
          WHERE c.id_comprobante = $id_comprobante";

$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    exit('Error: No se encontró el comprobante.');
}

$row = mysqli_fetch_assoc($result);

// Crear una nueva clase extendida para generar el PDF
class PDF extends FPDF
{
    // Cabecera del PDF
    function Header()
    {
        // Logo o título del comprobante
        $this->SetFont('Arial','B',15);
        $this->Cell(0,10,'Comprobante de Abono',0,1,'C');
        $this->Ln(5); // Salto de línea
    }

    // Cuerpo del PDF con los datos del comprobante
    function Body($data)
    {
        $this->SetFont('Arial','',12);
        
        // Mostrar los datos del comprobante
        $this->Cell(40,10,'ID Comprobante:',0,0);
        $this->Cell(0,10,$data['id_comprobante'],0,1);

        $this->Cell(40,10,'Fecha de Emision:',0,0);
        $this->Cell(0,10,$data['fecha_emision'],0,1);

        $this->Cell(40,10,'Cliente:',0,0);
        $this->Cell(0,10,$data['nombre_cliente'],0,1);

        $this->Cell(40,10,'DNI:',0,0);
        $this->Cell(0,10,$data['dni_cliente'],0,1);

        $this->Cell(40,10,'Telefono:',0,0);
        $this->Cell(0,10,$data['telefono'],0,1);

        $this->Cell(40,10,'Email:',0,0);
        $this->Cell(0,10,$data['email'],0,1);

        $this->Cell(40,10,'Asesor:',0,0);
        $this->Cell(0,10,$data['nombre_asesor'],0,1);

        $this->Cell(40,10,'Estado:',0,0);
        $this->Cell(0,10,$data['estado'],0,1);

        $this->Cell(40,10,'Banco:',0,0);
        $this->Cell(0,10,$data['nombre_banco'],0,1);

        $this->Cell(40,10,'Operacion:',0,0);
        $this->Cell(0,10,$data['descripcion_operacion'],0,1);

        $this->Cell(40,10,'Monto:',0,0);
        $this->Cell(0,10,'S/. ' . number_format($data['monto'], 2),0,1);
    }
}

// Crear instancia del PDF
$pdf = new PDF();
$pdf->AddPage();

// Llamar al método Body y pasar los datos del comprobante
$pdf->Body($row);

// Salida del documento
$pdf->Output();
?>