<?php 
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

$title = "Comprobante de Abono";
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
        <h2>Bienvenido</h2>
    </div>

    <footer class="footer">
        &copy; 2024. Todos los derechos reservados.
    </footer>
</body>
</html>