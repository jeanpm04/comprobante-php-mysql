<?php
session_start();
include("db_conn.php");

if (!isset($_SESSION['id']) || !isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

$title = "Editar Cliente";

// Obtener el DNI del cliente desde la URL
if (!isset($_GET['dni'])) {
    exit('Error: No se ha especificado el cliente.');
}
$dni = $_GET['dni'];

// Consulta para obtener los datos del cliente
$query = "SELECT * FROM cliente WHERE dni = '$dni'";
$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    exit('Error: No se encontró el cliente.');
}

$row = mysqli_fetch_assoc($result);

// Procesar la actualización del cliente si se ha enviado un formulario POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];
    $estado = $_POST['estado'];

    // Actualizar los datos del cliente en la base de datos
    $update_query = "UPDATE cliente SET nombre='$nombre', telefono='$telefono', email='$email', direccion='$direccion', estado='$estado' WHERE dni='$dni'";
    mysqli_query($conn, $update_query);

    // Redirigir a la página de clientes después de la actualización
    header("Location: clientes.php");
    exit();
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
            <h2>Editar Cliente</h2>
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
        <h2>Editar Cliente</h2>
        <form action="editar_cliente.php?dni=<?php echo $dni; ?>" method="POST" id="edit_client_form">
            <input type="text" name="nombre" value="<?php echo $row['nombre']; ?>" required>
            <input type="text" name="telefono" value="<?php echo $row['telefono']; ?>" required>
            <input type="email" name="email" value="<?php echo $row['email']; ?>" required>
            <input type="text" name="direccion" value="<?php echo $row['direccion']; ?>" required>
            <select name="estado" required>
                <option value="activo" <?php echo ($row['estado'] == 'activo') ? 'selected' : ''; ?>>Activo</option>
                <option value="inactivo" <?php echo ($row['estado'] == 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
            </select>
            <button type="submit">Guardar Cambios</button>
        </form>
    </div>

    <footer class="footer">
        &copy; 2024. Todos los derechos reservados.
    </footer>
</body>
</html>