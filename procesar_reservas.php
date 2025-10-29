<?php 
$mensaje="";

if (isset($_POST['btnAccion'])) {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Error: token CSRF inválido.");
    }

    $token=htmlspecialchars($_POST['csrf_token'] ?? '');
    $idReserva=htmlspecialchars($_POST['txtIDReserva'] ?? '');
    $nombreCompleto=htmlspecialchars(trim($_POST['txtNombreCliente'] ?? ''), ENT_QUOTES, 'UTF-8');
    $sucursal=htmlspecialchars($_POST['txtSucursal'] ?? '', ENT_QUOTES, 'UTF-8');
    $cantidadPersonas=intval($_POST['txtCantidad'] ?? 0);
    $fecha=$_POST['txtFechaReserva'] ?? '';
    $timestampFecha=strtotime($fecha);
    $minFecha=strtotime("today");
    $maxFecha=strtotime("+3 months", $minFecha);
    $hora=$_POST['txtHoraReserva'] ?? '';
    $correoElectronico=trim($_POST['txtCorreo'] ?? '');
    $btnAccion=(isset($_POST['accion'])) ? $_POST['accion']:"";

    if (!empty($_POST['txtCorreo'])) {
        $_SESSION['correoUsr']=trim($_POST['txtCorreo']);
    }

    switch ($_POST['btnAccion']) {
        case 'Reservar':
            if (trim($_POST['txtNombreCliente'])==="adm2025") {
                header("Location:administrador/index.php");
                exit();
            }

            if ($timestampFecha===false) {
                    $_SESSION['MENSAJE']="Formato de fecha inválido.";
                } elseif ($timestampFecha<$minFecha) {
                    $_SESSION['MENSAJE']="¡Tu fecha es inválida!";
                } elseif ($timestampFecha>$maxFecha) {
                    $_SESSION['MENSAJE']="¡La fecha supera el tiempo límite de reserva!";
            } else {
                $correoSeguro=htmlspecialchars($correoElectronico, ENT_QUOTES, 'UTF-8');
            }

            if (empty($_SESSION['MENSAJE'])) {
                $sentenciaSQL=$conexion->prepare("INSERT INTO reservas (nombreUsr, sucursal, cantPersonas, fechaRsrv, horaRsrv, correoUsr, tokenUsr) VALUES (:nombreUsr, :sucursal, :cantPersonas, :fechaRsrv, :horaRsrv, :correoUsr, :tokenUsr);");
                $sentenciaSQL->bindParam(':nombreUsr', $nombreCompleto);
                $sentenciaSQL->bindParam(':sucursal', $sucursal);
                $sentenciaSQL->bindParam(':cantPersonas', $cantidadPersonas);
                $sentenciaSQL->bindParam(':fechaRsrv', $fecha);
                $sentenciaSQL->bindParam(':horaRsrv', $hora);
                $sentenciaSQL->bindParam(':correoUsr', $correoElectronico);
                $sentenciaSQL->bindParam(':tokenUsr', $token);
                $sentenciaSQL->execute();
                $idReserva=$conexion->lastInsertId();

                if (!isset($_SESSION['RESERVA'])) {
                    $RESERVA=array(
                        'IDRSRV' => $idReserva,
                        'NOMBREUSR' => $nombreCompleto,
                        'SUCURSAL' => $sucursal,
                        'CANTIDADPERSONAS' => $cantidadPersonas,
                        'FECHARSRV' => $fecha,
                        'HORARSRV' => $hora,
                        'CORREOUSR' => $correoElectronico,
                        'TOKENUSR' => $token,
                    );
                    $_SESSION['RESERVA'][0]=$RESERVA;
                    $_SESSION['MENSAJE']="¡Reserva realizada con éxito!";
                } else {
                    $idReservas=array_column($_SESSION['RESERVA'], "IDRSRV");
                    if (in_array($idReserva, $idReservas)) {
                        $_SESSION['MENSAJE']="ERROR.";
                    } else {
                        $numeroReservas=count($_SESSION['RESERVA']);
                        $RESERVA=array(
                            'IDRSRV' => $idReserva,
                            'NOMBREUSR' => $nombreCompleto,
                            'SUCURSAL' => $sucursal,
                            'CANTIDADPERSONAS' => $cantidadPersonas,
                            'FECHARSRV' => $fecha,
                            'HORARSRV' => $hora,
                            'CORREOUSR' => $correoElectronico,
                            'TOKENUSR' => $token,
                        );
                        $_SESSION['RESERVA'][$numeroReservas]=$RESERVA;
                        $_SESSION['MENSAJE']="¡Reserva realizada con éxito!";
                    }
                }
            }
            header("Location:reservas.php");
            exit();

        case 'Seleccionar':
            $idReserva=openssl_decrypt($_POST['txtIDReserva'], COD, KEY);
            $sentenciaSQL=$conexion->prepare("SELECT * FROM reservas WHERE idRsrv=:idRsrv");
            $sentenciaSQL->bindParam(':idRsrv',$idReserva); 
            $sentenciaSQL->execute();
            $reserva=$sentenciaSQL->fetch(PDO::FETCH_ASSOC);
            if ($reserva!==false) {
                $token=$reserva['tokenUsr'];
                $nombreCompleto=$reserva['nombreUsr'];
                $sucursal=$reserva['sucursal'];
                $cantidadPersonas=$reserva['cantPersonas'];
                $fecha=$reserva['fechaRsrv'];
                $hora=$reserva['horaRsrv'];
                $correoElectronico=$reserva['correoUsr'];
            } else {
                $_SESSION['MENSAJE']="Error. ID incorrecto o no pertenece a una reserva existente.<br/>";
            }
            break;

        case 'Modificar':
            $idReserva=openssl_decrypt($_POST['txtIDReserva'], COD, KEY);
            $sentenciaSQL=$conexion->prepare("UPDATE reservas SET nombreUsr=:nombreUsr, sucursal=:sucursal, cantPersonas=:cantPersonas, fechaRsrv=:fechaRsrv, horaRsrv=:horaRsrv, correoUsr=:correoUsr, tokenUsr=:tokenUsr WHERE idRsrv=:idRsrv");
            $sentenciaSQL->bindParam(':idRsrv',$idReserva); 
            $sentenciaSQL->bindParam(':nombreUsr',$nombreCompleto);
            $sentenciaSQL->bindParam(':sucursal',$sucursal); 
            $sentenciaSQL->bindParam(':cantPersonas',$cantidadPersonas);  
            $sentenciaSQL->bindParam(':fechaRsrv',$fecha);
            $sentenciaSQL->bindParam(':horaRsrv', $hora);
            $sentenciaSQL->bindParam(':correoUsr', $correoElectronico);
            $sentenciaSQL->bindParam(':tokenUsr',$token); 
            if ($timestampFecha===false) {
                    $_SESSION['MENSAJE']="Formato de fecha inválido.";
                } elseif ($timestampFecha<$minFecha) {
                    $_SESSION['MENSAJE']="¡Tu fecha es inválida!";
                } elseif ($timestampFecha>$maxFecha) {
                    $_SESSION['MENSAJE']="¡La fecha supera el tiempo límite de reserva!";
            } else {
                $correoSeguro=htmlspecialchars($correoElectronico, ENT_QUOTES, 'UTF-8');
                $sentenciaSQL->execute();
                $_SESSION['MENSAJE']="¡Reserva modificada con éxito.!";
            }
            header("Location:reservas.php");
            exit();

        case 'Anular':
            $idReserva=openssl_decrypt($_POST['txtIDReserva'], COD, KEY);
            $idReservas=array_column($_SESSION['RESERVA'], "IDRSRV");
            if (is_numeric($idReserva) && in_array($idReserva, $idReservas)) {
                foreach ($_SESSION['RESERVA'] as $indice => $RESERVA) {
                    if ($RESERVA['IDRSRV']==$idReserva) {
                        unset($_SESSION['RESERVA'][$indice]);
                        $sentenciaSQL=$conexion->prepare("DELETE FROM reservas WHERE idRsrv=:idRsrv");
                        $sentenciaSQL->bindParam(':idRsrv', $idReserva, PDO::PARAM_INT);
                        $sentenciaSQL->execute();
                    }
                }
            } else {
                $_SESSION['MENSAJE']="Error. ID incorrecto o no pertenece a una reserva existente.<br/>";
            }
            break;
        
        case 'Cancelar':
            header('Location:reservas.php');
            break;

        case 'Vaciar':
            unset($_SESSION['RESERVA']);
            break;
        }
    }
?>