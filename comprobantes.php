<?php 
session_start();
include("db_conn.php");

if (!isset($_SESSION['id']) || !isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

$title = "Comprobantes";

// Procesar eliminación de comprobante si se ha enviado un request GET con parámetro id_comprobante
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id_comprobante'])) {
    $id_comprobante = $_GET['id_comprobante'];
    $delete_query = "DELETE FROM comprobante WHERE id_comprobante='$id_comprobante'";
    mysqli_query($conn, $delete_query);
    header("Location: comprobantes.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fecha_emision = $_POST['fecha_emision'];
    $dni_cliente = $_POST['dni_cliente'];
    $id_asesor = $_POST['id_asesor'];
    $estado = $_POST['estado'];
    $id_banco = $_POST['id_banco'];
    $id_operacion = $_POST['id_operacion'];
    $monto = $_POST['monto'];

    $sql = "INSERT INTO comprobante (fecha_emision, dni_cliente, id_asesor, estado, id_banco, id_operacion, monto) 
            VALUES ('$fecha_emision', '$dni_cliente', '$id_asesor', '$estado', '$id_banco', '$id_operacion', '$monto')";
    mysqli_query($conn, $sql);
}

// Modificar la consulta para obtener los nombres de asesor, banco y operacion
$comprobantes_query = "SELECT c.id_comprobante, c.fecha_emision, c.dni_cliente, a.nombre AS nombre_asesor, c.estado, b.nombre AS nombre_banco, o.descripcion AS descripcion_operacion, c.monto
                      FROM comprobante c
                      LEFT JOIN asesor a ON c.id_asesor = a.id_asesor
                      LEFT JOIN banco b ON c.id_banco = b.id_banco
                      LEFT JOIN operacion o ON c.id_operacion = o.id_operacion";
$comprobantes = mysqli_query($conn, $comprobantes_query);
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
            <h2>Comprobante de Abono</h2>
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
        <h2>Gestionar Comprobantes</h2>
        <form action="comprobantes.php" method="POST" id="add_comprobante_form">
            <input type="date" name="fecha_emision" required>
            <select name="dni_cliente" required>
                <option value="">Seleccionar Cliente</option>
                <?php 
                    $clientes = mysqli_query($conn, "SELECT dni, nombre FROM cliente");
                    while ($row = mysqli_fetch_assoc($clientes)) {
                        echo "<option value='" . $row['dni'] . "'>" . $row['nombre'] . "</option>";
                    }
                ?>
            </select>
            <select name="id_asesor" required>
                <option value="">Seleccionar Asesor</option>
                <?php 
                    $asesores = mysqli_query($conn, "SELECT id_asesor, nombre FROM asesor");
                    while ($row = mysqli_fetch_assoc($asesores)) {
                        echo "<option value='" . $row['id_asesor'] . "'>" . $row['nombre'] . "</option>";
                    }
                ?>
            </select>
            <select name="estado" required>
                <option value="pendiente">Pendiente</option>
                <option value="pagado">Pagado</option>
            </select>
            <select name="id_banco" required>
                <option value="">Seleccionar Banco</option>
                <?php 
                    $bancos = mysqli_query($conn, "SELECT id_banco, nombre FROM banco");
                    while ($row = mysqli_fetch_assoc($bancos)) {
                        echo "<option value='" . $row['id_banco'] . "'>" . $row['nombre'] . "</option>";
                    }
                ?>
            </select>
            <select name="id_operacion" required>
                <option value="">Seleccionar Operación</option>
                <?php 
                    $operaciones = mysqli_query($conn, "SELECT id_operacion, descripcion FROM operacion");
                    while ($row = mysqli_fetch_assoc($operaciones)) {
                        echo "<option value='" . $row['id_operacion'] . "'>" . $row['descripcion'] . "</option>";
                    }
                ?>
            </select>
            <input type="number" step="0.01" name="monto" placeholder="Monto" required>
            <button type="submit">Generar Comprobante</button>
        </form>

        <h2>Lista de Comprobantes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Comprobante</th>
                    <th>Fecha de Emisión</th>
                    <th>Cliente</th>
                    <th>Asesor</th>
                    <th>Estado</th>
                    <th>Banco</th>
                    <th>Operación</th>
                    <th>Monto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($comprobantes)) { ?>
                    <tr>
                        <td><?php echo $row['id_comprobante']; ?></td>
                        <td><?php echo $row['fecha_emision']; ?></td>
                        <td><?php echo $row['dni_cliente']; ?></td>
                        <td><?php echo $row['nombre_asesor']; ?></td>
                        <td><?php echo $row['estado']; ?></td>
                        <td><?php echo $row['nombre_banco']; ?></td>
                        <td><?php echo $row['descripcion_operacion']; ?></td>
                        <td>S/. <?php echo number_format($row['monto'], 2); ?></td>
                        <td>
                            <a href="editar_comprobante.php?id_comprobante=<?php echo $row['id_comprobante']; ?>">Editar</a>
                            <a href="comprobantes.php?action=delete&id_comprobante=<?php echo $row['id_comprobante']; ?>">Eliminar</a>
                            <a href="imprimir_comprobante.php?id_comprobante=<?php echo $row['id_comprobante']; ?>">Imprimir</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <footer class="footer">
        &copy; 2024. Todos los derechos reservados.
    </footer>
</body>
</html>