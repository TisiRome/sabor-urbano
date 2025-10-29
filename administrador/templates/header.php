<?php 
    header("X-Frame-Options: DENY");
    header("X-Content-Type-Options: nosniff");
    header("Referrer-Policy: no-referrer-when-downgrade");
    header("X-XSS-Protection: 1; mode=block");
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

    if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
        header('Location:../index.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">

                <a class="navbar-brand" href="index.php">
                    <img src="../img/icon/saborurbano-white.png" alt="Ícono del restaurante Sabor Urbano" class="d-inline-block align-text-top icono-sa">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#contenidoDelNav" aria-controls="contenidoDelNav" aria-expanded="false" aria-label="Botón colapsable de la barra de navegación">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="contenidoDelNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="inicio.php"><i class="fa-solid fa-house"></i> Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="menus.php"><i class="fa-solid fa-pizza-slice"></i> Menús</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../index.php"><i class="fa-solid fa-eye"></i> Ver sitio web</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="cerrar.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión</a>
                        </li>
                    </ul>
                </div>

            </div>
        </nav>
    </header>