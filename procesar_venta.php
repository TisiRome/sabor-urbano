<?php 
    include("administrador/database/bd.php");
    include("templates/sesion.php");

    $total=0;
    $correoUsuario=$_SESSION['correoUsr'] ?? '';

    if (!empty($_SESSION['CARRITO'])) {
        foreach ($_SESSION['CARRITO'] as $MENU) {
            $total += $MENU['PRECIOMENU'] * $MENU['CANTIDADMENU'];
        }
    }

    if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['txtCorreo'])) {
        $correoUsuario=trim($_POST['txtCorreo']);
        if (!filter_var($correoUsuario, FILTER_VALIDATE_EMAIL)) {
            echo "<div class='alert alert-danger text-center'>El correo ingresado no es válido.</div>";
            include("templates/footer.php");
            exit;
        }
        $_SESSION['correoUsr']=$correoUsuario;
    }

    if (empty($correoUsuario)) {

    include("templates/header.php");
?>

<main class="container" id="procesarVentaForm">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card">
                <div class="card-body text-center">
                    <h4>Antes de continuar...</h4>
                    <p>Por favor, ingresá tu correo electrónico para registrar la compra.</p>
                    <hr>
                    <form method="POST">
                        <div class="form-group">
                            <label for="correo">Correo electrónico</label>
                            <input type="email" id="correo" name="txtCorreo" class="form-control" required>
                        </div>
                        <div class="d-flex flex-column flex-md-row justify-content-center">
                            <button type="submit" class="btn btn-success">Continuar</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

<?php
        include("templates/footer.php");
        exit;
    }

    $sentenciaSQL = $conexion->prepare("
        INSERT INTO ventas (fechaVnt, totalVnt, correoUsr)
        VALUES (NOW(), :totalVnt, :correoUsr)
    ");
    $sentenciaSQL->bindParam(':totalVnt', $total);
    $sentenciaSQL->bindParam(':correoUsr', $correoUsuario);
    $sentenciaSQL->execute();

    unset($_SESSION['CARRITO']);

    include("templates/header.php");
?>

<main role="main" id="procesarVentaComp">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class='alert alert-success text-center'>
                    <h4>¡Gracias por tu compra!</h4>
                    <p>Total a abonar: <strong>$<?php echo number_format($total, 2); ?></strong></p>
                    <p>Podés pagar al retirar o en el local.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const barra = document.getElementById('barraDeCarga');
            if (barra) {
                barra.style.display = 'block';
            }

            setTimeout(function() {
                window.location.href = 'pedidos.php';
            }, 500);
        }, 3000);
    });
</script>

<?php include("templates/footer.php"); ?>
