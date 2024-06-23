<?php
session_start();
include("db_conn.php");

if (!isset($_SESSION['id']) || !isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

$title = "Editar Comprobante";

// Obtener el id_comprobante desde la URL
if (!isset($_GET['id_comprobante'])) {
    exit('Error: No se ha especificado el comprobante.');
}
$id_comprobante = $_GET['id_comprobante'];

// Consulta para obtener los datos del comprobante, incluyendo nombres en lugar de IDs
$query = "SELECT c.id_comprobante, c.fecha_emision, c.dni_cliente, cl.nombre AS nombre_cliente, c.id_asesor, a.nombre AS nombre_asesor, c.estado, c.id_banco, b.nombre AS nombre_banco, c.id_operacion, o.descripcion AS descripcion_operacion, c.monto
          FROM comprobante c
          LEFT JOIN cliente cl ON c.dni_cliente = cl.dni
          LEFT JOIN asesor a ON c.id_asesor = a.id_asesor
          LEFT JOIN banco b ON c.id_banco = b.id_banco
          LEFT JOIN operacion o ON c.id_operacion = o.id_operacion
          WHERE c.id_comprobante = $id_comprobante";

$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    exit('Error: No se encontró el comprobante.');
}

$row = mysqli_fetch_assoc($result);

// Procesar el formulario cuando se envíe
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fecha_emision = $_POST['fecha_emision'];
    $dni_cliente = $_POST['dni_cliente'];
    $id_asesor = $_POST['id_asesor'];
    $estado = $_POST['estado'];
    $id_banco = $_POST['id_banco'];
    $id_operacion = $_POST['id_operacion'];
    $monto = $_POST['monto'];

    // Actualizar los datos del comprobante en la base de datos
    $update_query = "UPDATE comprobante 
                     SET fecha_emision='$fecha_emision', dni_cliente='$dni_cliente', id_asesor='$id_asesor', estado='$estado', 
                         id_banco='$id_banco', id_operacion='$id_operacion', monto='$monto'
                     WHERE id_comprobante='$id_comprobante'";
    
    if (mysqli_query($conn, $update_query)) {
        header("Location: comprobantes.php");
        exit();
    } else {
        echo "Error al actualizar el comprobante: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <div class="left-section">
            <h2>Editar Comprobante</h2>
        </div>
        <nav class="nav">
            <ul>
                <li><a href="comprobantes.php">Comprobantes</a></li>
                <li><a href="clientes.php">Clientes</a></li>
            </ul>
        </nav>
        <div class="right-section">
            <div class="user-info">
                <span>Hello, <?php echo $_SESSION['name']; ?></span>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <h2>Modificar Comprobante</h2>
        <form action="editar_comprobante.php?id_comprobante=<?php echo $id_comprobante; ?>" method="POST" id="edit_comprobante_form">
            <input type="date" name="fecha_emision" value="<?php echo $row['fecha_emision']; ?>" required>
            <select name="dni_cliente" required>
                <option value="">Seleccionar Cliente</option>
                <?php 
                    $clientes = mysqli_query($conn, "SELECT dni, nombre FROM cliente");
                    while ($cliente = mysqli_fetch_assoc($clientes)) {
                        $selected = ($cliente['dni'] == $row['dni_cliente']) ? 'selected' : '';
                        echo "<option value='" . $cliente['dni'] . "' $selected>" . $cliente['nombre'] . "</option>";
                    }
                ?>
            </select>
            <select name="id_asesor" required>
                <option value="">Seleccionar Asesor</option>
                <?php 
                    $asesores = mysqli_query($conn, "SELECT id_asesor, nombre FROM asesor");
                    while ($asesor = mysqli_fetch_assoc($asesores)) {
                        $selected = ($asesor['id_asesor'] == $row['id_asesor']) ? 'selected' : '';
                        echo "<option value='" . $asesor['id_asesor'] . "' $selected>" . $asesor['nombre'] . "</option>";
                    }
                ?>
            </select>
            <select name="estado" required>
                <option value="pendiente" <?php echo ($row['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                <option value="pagado" <?php echo ($row['estado'] == 'pagado') ? 'selected' : ''; ?>>Pagado</option>
            </select>
            <select name="id_banco" required>
                <option value="">Seleccionar Banco</option>
                <?php 
                    $bancos = mysqli_query($conn, "SELECT id_banco, nombre FROM banco");
                    while ($banco = mysqli_fetch_assoc($bancos)) {
                        $selected = ($banco['id_banco'] == $row['id_banco']) ? 'selected' : '';
                        echo "<option value='" . $banco['id_banco'] . "' $selected>" . $banco['nombre'] . "</option>";
                    }
                ?>
            </select>
            <select name="id_operacion" required>
                <option value="">Seleccionar Operación</option>
                <?php 
                    $operaciones = mysqli_query($conn, "SELECT id_operacion, descripcion FROM operacion");
                    while ($operacion = mysqli_fetch_assoc($operaciones)) {
                        $selected = ($operacion['id_operacion'] == $row['id_operacion']) ? 'selected' : '';
                        echo "<option value='" . $operacion['id_operacion'] . "' $selected>" . $operacion['descripcion'] . "</option>";
                    }
                ?>
            </select>
            <input type="number" step="0.01" name="monto" value="<?php echo $row['monto']; ?>" placeholder="Monto" required>
            <button type="submit">Guardar Cambios</button>
        </form>
    </div>

    <footer class="footer">
        &copy; 2024. Todos los derechos reservados.
    </footer>
</body>
</html>