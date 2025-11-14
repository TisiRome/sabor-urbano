<?php
    include("carrito.php");

    $correoUsuario=$_SESSION['correoUsr'] ?? '';

    if (!empty($correoUsuario)) {
        $sentenciaSQL=$conexion->prepare("SELECT * FROM reservas WHERE correoUsr=:correoUsr");
        $sentenciaSQL->bindParam(':correoUsr', $correoUsuario);
        $sentenciaSQL->execute();
        $listaReservas=$sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $listaReservas=[]; // Si no hay correo, no se muestran reservas
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sabor Urbano</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/icon/saborurbano-white.ico">
    <meta property="og:title" content="Sabor Urbano - Restaurante Gourmet">
    <meta property="og:description" content="Disfrutá de los mejores platos en un ambiente único y familiar.">
    <meta property="og:image" content="img/bgImg/presentacion.jpg">
    <meta property="og:url" content="">
    <meta name="twitter:card" content="summary_large_image">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">

                <a class="navbar-brand" href="index.php">
                    <img src="img/icon/saborurbano-white.png" alt="Ícono del restaurante Sabor Urbano" class="d-inline-block align-text-top icono-sa">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#contenidoDelNav" aria-controls="contenidoDelNav" aria-expanded="false" aria-label="Botón colapsable de la barra de navegación">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="contenidoDelNav">

                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link active" href="index.php"><i class="fa-solid fa-house"></i> Inicio</a></li>
                        <li class="nav-item"><a class="nav-link" href="menus.php"><i class="fa-solid fa-pizza-slice"></i> Menús</a></li>
                        <li class="nav-item"><a class="nav-link" href="reservas.php"><i class="fa-solid fa-utensils"></i> Reserve</a></li>
                        <li class="nav-item"><a class="nav-link" href="ubicacion.php"><i class="fa-solid fa-shop"></i> Sucursales</a></li>
                        <li class="nav-item"><a class="nav-link" href="nosotros.php"><i class="fa-solid fa-people-group"></i> Nosotros</a></li>
                    </ul>

                    <ul class="navbar-nav">
                        <?php if (isset($_SESSION['role'])): ?>
                            <?php if ($_SESSION['role']==='admin'): ?>
                                <!--Botón que lleva a la página de menus.php en administrador (vista escritorio)-->
                                <button id="btnAdministrador" class="btn btn-outline-dark nav-link" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminOffcanvas">
                                    <i class="fa-solid fa-screwdriver-wrench"></i> HERRAMIENTAS
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (!empty($_SESSION['CARRITO'])): ?>
                            <li class="nav-item ms-2">
                                <button id="btnCarrito" class="btn btn-outline-dark nav-link" type="button" data-bs-toggle="offcanvas" data-bs-target="#carritoOffcanvas">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                    (<?php echo count($_SESSION['CARRITO']); ?>)
                                </button>
                            </li>
                        <?php endif; ?>
                    </ul>

                </div>
            </div>
        </nav>
    </header>

    <?php if(!empty($_SESSION['CARRITO'])) { ?>
        <button id="btnCarritoMovil" class="btn btn-info d-block d-md-none m-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#carritoOffcanvas">
            <i class="fa-solid fa-cart-shopping"></i>
        </button>
    <?php } ?>

    <div id="carritoOffcanvas" class="offcanvas offcanvas-end estiloOffcanvas" data-bs-scroll="true" tabindex="-1">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Mis pedidos</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body">
            <?php if (!empty($_SESSION['CARRITO'])) { ?>
                <?php $total=0; ?>
                <?php foreach ($_SESSION['CARRITO'] as $indice => $MENU) { ?>
                    <div id="carritoCard" class="card mt-2">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($MENU['NOMBREMENU']); ?> - $<?php echo htmlspecialchars($MENU['PRECIOMENU']); ?></h5>
                            <p class="card-text">Cantidad: <?php echo htmlspecialchars($MENU['CANTIDADMENU']); ?></p>
                        </div>
                        <div class="text-center">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="idMenu" value="<?php echo openssl_encrypt($MENU['IDMENU'], COD, KEY); ?>">

                                <div class="btn-group" role="group">
                                    <button class="btn btn-danger btn-md iconBtn" name="btnAccion" value="Eliminar" type="submit"><i class="fa-solid fa-trash-can"></i></button>
                                    <button class="btn btn-success btn-md iconBtn" name="btnAccion" value="Aumentar" type="submit"><i class="fa-solid fa-plus"></i></button>
                                    <button class="btn btn-success btn-md iconBtn" name="btnAccion" value="Disminuir" type="submit"><i class="fa-solid fa-minus"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php $total += $MENU['PRECIOMENU'] * $MENU['CANTIDADMENU']; ?>
                <?php } ?>
            <?php } else { ?>
                <div class="alert alert-success">No hay menús seleccionados.</div>
            <?php } ?>
        </div>

        <div class="offcanvas-footer">
            <?php if (!empty($_SESSION['CARRITO'])) { ?>
                <div class="text-center">
                    <form action="pedidos.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button class="btn btn-outline-dark btn-block" name="btnAccion" value="Vaciar" type="submit">DEJAR DE COMPRAR</button>
                        <button class="btn btn-outline-info btn-block" name="btnAccion" value="Pagar" type="submit">PAGAR YA ($<?php echo number_format($total,2); ?>)</button>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>

    <div id="reservasOffcanvas" class="offcanvas offcanvas-end estiloOffcanvas" data-bs-scroll="true" tabindex="-1">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Mis reservas</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body">
            <?php if (!empty($listaReservas)) { ?>
                <?php foreach ($listaReservas as $reserva) { ?>
                    <div id="carritoCard" class="card mt-2">
                        <div class="card-body">
                            <?php
                                $sucursalMinus=strtolower($reserva['sucursal']);
                                $fechaBase=new DateTime($reserva['fechaRsrv']);
                                $formatter=new IntlDateFormatter(
                                    'es_ES', 
                                    IntlDateFormatter::FULL,
                                    IntlDateFormatter::NONE,
                                    'America/Argentina/Buenos_Aires',
                                    IntlDateFormatter::GREGORIAN,
                                    "EEEE d 'de' MMMM 'del' y"
                                );
                                $fechaFormateada=$formatter->format($fechaBase);
                                ?>
                            <h5 class="card-title">DATOS DE LA RESERVA</h5>
                            <p class="card-text">
                                Reserva para <?php echo htmlspecialchars($reserva['cantPersonas']); ?> personas, realizada en <?php echo htmlspecialchars($sucursalMinus); ?>, el día <?php echo htmlspecialchars($fechaFormateada); ?> a las <?php echo htmlspecialchars($reserva['horaRsrv']); ?>. A nombre de <?php echo htmlspecialchars($reserva['nombreUsr']); ?>.
                            </p>
                        </div>
                        <div class="text-center">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="txtIDReserva" value="<?php echo openssl_encrypt($reserva['idRsrv'], COD, KEY); ?>">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-warning btn-md iconBtn" name="btnAccion" value="Seleccionar" type="submit">SELECCIONAR</button>
                                    <button class="btn btn-danger btn-md iconBtn" name="btnAccion" value="Anular" type="submit">ANULAR</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="alert alert-success">No hay reservas.</div>
            <?php } ?>
        </div>

        <div class="offcanvas-footer">

        </div>
    </div>

    <div id="adminOffcanvas" class="offcanvas offcanvas-end estiloOffcanvas" data-bs-scroll="true" tabindex="-1">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">HERRAMIENTAS DE ADMINISTRADOR</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body">
            <div class="d-flex flex-column gap-2">
                <a class="btn btn-outline-primary d-none d-md-block" href="administrador/menus.php"><i class="fa-solid fa-pen-to-square"></i> EDITAR MENÚS</a>
                <a class="btn btn-outline-primary d-none d-md-block" href="administrador/categorias.php"><i class="fa-solid fa-pen-to-square"></i> EDITAR CATEGORÍAS</a>
            </div>
        </div>

        <div class="offcanvas-footer">
            <div class="d-flex flex-column gap-2 m-3">
                <a href="administrador/inicio.php" class="btn btn-warning btn-md w-100"><i class="fa-solid fa-arrow-left"></i> Volver al administrador</a>
                <a href="administrador/cerrar.php" class="btn btn-danger btn-md w-100"><i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión</a>
            </div>
        </div>
    </div>

<div id="barraDeCarga">Cargando...</div>