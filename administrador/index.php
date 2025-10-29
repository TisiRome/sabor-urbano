<?php
    session_start();
    require 'database/bd.php';

    $nombreUsuario=trim($_POST['txtNombreUsuario'] ?? '');
    $contraseniaUsuario=$_POST['txtContrasenia'] ?? '';

    $MAX_ATTEMPTS=5;
    $TIEMPO_BLOQUEO=3600;
    $IP_USR=$_SERVER['REMOTE_ADDR'];
    $AHORA=date('Y-m-d H:i:s');

    $sentenciaSQL=$conexion->prepare("SELECT intento, ultIntento FROM intentoslogin WHERE ipUsr=:ipUsr");
    $sentenciaSQL->execute(['ipUsr'=>$IP_USR]);
    $registroUsr=$sentenciaSQL->fetch(PDO::FETCH_ASSOC);

    $bloqueado=false;

    if ($registroUsr) {
        $ultimo=strtotime($registroUsr['ultIntento']);
        $tiempoTranscurrido=time()-$ultimo;

        if ($registroUsr['intento']>=$MAX_ATTEMPTS && $tiempoTranscurrido<$TIEMPO_BLOQUEO) {
            $bloqueado=true;
            $segundosRestantes=$TIEMPO_BLOQUEO-$tiempoTranscurrido;

        } elseif ($tiempoTranscurrido>=$TIEMPO_BLOQUEO) {
            $sentenciaSQL=$conexion->prepare("UPDATE intentoslogin SET intento=0, ultIntento=:ahora WHERE ipUsr=:ipUsr");
            $sentenciaSQL->execute(['ipUsr'=>$IP_USR, 'ahora'=>$AHORA]);
            
        }
    }

    if (!$bloqueado) {
        $sentenciaSQL=$conexion->prepare("SELECT idAdm, nombreAdm, contraseniaAdm, role FROM usuarios WHERE nombreAdm=:u LIMIT 1");
        $sentenciaSQL->execute(['u'=>$nombreUsuario]);
        $usuario=$sentenciaSQL->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($contraseniaUsuario, $usuario['contraseniaAdm'])) {

            session_regenerate_id(true);
            
            $_SESSION['user_id']=$usuario['idAdm'];
            $_SESSION['username']=$usuario['nombreAdm'];
            $_SESSION['role']=$usuario['role'];

            $sentenciaSQL=$conexion->prepare("DELETE FROM intentoslogin WHERE ipUsr=:ipUsr");
            $sentenciaSQL->execute(['ipUsr'=>$IP_USR]);

            header("Location: " . ($usuario['role']==='admin' ? "inicio.php" : "../index.php"));
            exit;

        } else {
            if ($registroUsr) {
                $sentenciaSQL=$conexion->prepare("UPDATE intentoslogin SET intento=intento+1, ultIntento=:ahora WHERE ipUsr=:ipUsr");
                $sentenciaSQL->execute(['ipUsr'=>$IP_USR, 'ahora'=>$AHORA]);
                $intentos=$MAX_ATTEMPTS - ($registroUsr['intento'] ?? 0);

            } else {
                $sentenciaSQL=$conexion->prepare("INSERT INTO intentoslogin (ipUsr, intento, ultIntento) VALUES (:ipUsr, 1, :ahora)");
                $sentenciaSQL->execute(['ipUsr'=>$IP_USR, 'ahora'=>$AHORA]);
                $intentos=$MAX_ATTEMPTS - 1;
                
            }
            $mensaje="Usuario o contraseña incorrectos. Te quedan {$intentos} intentos.";
        }
    } else {
        $mensaje="Demasiados intentos. Esperá {$segundosRestantes} segundos.";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <main id="loginAdmin" role="main">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-4">

                    <form method="POST" aria-labelledby="loginHeader">
                        <h4 class="text-center" id="loginHeader"><b>Iniciar sesión</b></h4>
                        <hr>

                        <div class="form-group">
                            <label for="nombre">Nombre de usuario</label>
                            <input type="text" id="nombre" name="txtNombreUsuario" class="form-control" placeholder="Nombre de usuario">
                        </div>

                        <div class="form-group">
                            <label for="contrasenia">Contraseña</label>
                            <input type="password" id="contrasenia" name="txtContrasenia" class="form-control" placeholder="Contraseña">
                        </div>

                        <?php if (!empty($_POST) && !empty($mensaje)): ?>
                            <div class="alert alert-warning" role="alert">
                                <?php echo $mensaje; ?>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-outline-success btn-md">Iniciar sesión</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </main>

</body>
</html>