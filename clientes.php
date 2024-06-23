<?php 
session_start();
include("db_conn.php");

if (!isset($_SESSION['id']) || !isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

$title = "Clientes";

// Procesar eliminación de cliente si se ha enviado un request GET con parámetro dni
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['dni'])) {
    $dni = $_GET['dni'];
    $delete_query = "DELETE FROM cliente WHERE dni='$dni'";
    mysqli_query($conn, $delete_query);
    header("Location: clientes.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dni = $_POST['dni'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];
    $estado = $_POST['estado'];

    $sql = "INSERT INTO cliente (dni, nombre, telefono, email, direccion, estado) VALUES ('$dni', '$nombre', '$telefono', '$email', '$direccion', '$estado')";
    mysqli_query($conn, $sql);
}

$clientes = mysqli_query($conn, "SELECT * FROM cliente");
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
        <h2>Gestionar Clientes</h2>
        <form action="clientes.php" method="POST" id="add_client_form">
            <input type="text" name="dni" placeholder="DNI" required>
            <input type="text" name="nombre" placeholder="Nombre del cliente" required>
            <input type="text" name="telefono" placeholder="Teléfono" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="direccion" placeholder="Dirección" required>
            <select name="estado" required>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
            <button type="submit">Agregar Cliente</button>
        </form>

        <h2>Lista de Clientes</h2>
        <table>
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Dirección</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($clientes)) { ?>
                    <tr>
                        <td><?php echo $row['dni']; ?></td>
                        <td><?php echo $row['nombre']; ?></td>
                        <td><?php echo $row['telefono']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['direccion']; ?></td>
                        <td><?php echo $row['estado']; ?></td>
                        <td>
                            <a href="editar_cliente.php?dni=<?php echo $row['dni']; ?>">Editar</a>
                            <a href="clientes.php?action=delete&dni=<?php echo $row['dni']; ?>">Eliminar</a>
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