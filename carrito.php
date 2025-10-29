<?php 
$mensaje="";

if (isset($_POST['btnAccion'])) {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Error: token CSRF inválido.");
    }

    switch ($_POST['btnAccion']) {
        case 'Agregar':
            if (isset($_POST['idMenu'])) {
                $idMenu=openssl_decrypt($_POST['idMenu'], COD, KEY);
                $nombreMenu=openssl_decrypt($_POST['nombreMenu'], COD, KEY);
                $precioMenu=openssl_decrypt($_POST['precioMenu'], COD, KEY);
                $cantidadMenu=openssl_decrypt($_POST['cantidadMenu'], COD, KEY);

                try {
                    $idMenu=validarEntero($idMenu,'ID');
                    $precioMenu=validarEntero($precioMenu,'Precio');
                    $cantidadMenu=validarEntero($cantidadMenu,'Cantidad');

                    if ($idMenu===false || $nombreMenu===false || $precioMenu===false || $cantidadMenu===false) {
                        throw new Exception("Error: datos del formulario alterados o inválidos.");
                    }

                    if ($cantidadMenu < 1) {
                        throw new Exception("Error en Cantidad: debe ser mayor o igual a 1");
                    }

                } catch (Exception $e) {
                    $mensaje.=$e->getMessage()."<br/>";
                }

                if (!is_string($nombreMenu)) {
                    $mensaje.="Error en Nombre: valor inválido ($nombreMenu)<br/>";
                }


                if (!isset($_SESSION['CARRITO'])) {
                    $MENU=array(
                        'IDMENU' => $idMenu,
                        'NOMBREMENU' => $nombreMenu,
                        'PRECIOMENU' => $precioMenu,
                        'CANTIDADMENU' => $cantidadMenu,
                    );
                    $_SESSION['CARRITO'][0]=$MENU;
                    echo "<script>alert('Menú agregado al carrito.')</script>";
                } else {
                    $idMenus=array_column($_SESSION['CARRITO'], "IDMENU");
                    if (in_array($idMenu, $idMenus)) {
                        echo "<script>alert('Error. Este producto ya ha sido agregado al carrito. Si quieres agregar más, aumenta la cantidad dentro del carrito.')</script>";
                    } else {
                        $numeroMenus=count($_SESSION['CARRITO']);
                        $MENU=array(
                            'IDMENU' => $idMenu,
                            'NOMBREMENU' => $nombreMenu,
                            'PRECIOMENU' => $precioMenu,
                            'CANTIDADMENU' => $cantidadMenu,
                        );
                        $_SESSION['CARRITO'][$numeroMenus]=$MENU;
                        echo "<script>alert('Menú agregado al carrito.')</script>";
                    }
                }
            }
            header("Location:pedidos.php");
            break;
        
        case 'Eliminar':
            $idMenu=openssl_decrypt($_POST['idMenu'], COD, KEY);
            $idMenus=array_column($_SESSION['CARRITO'], "IDMENU");
            if (is_numeric($idMenu) && in_array($idMenu, $idMenus)) {
                foreach ($_SESSION['CARRITO'] as $indice => $MENU) {
                    if ($MENU['IDMENU']==$idMenu) {
                        unset($_SESSION['CARRITO'][$indice]);
                    }
                }
            } else {
                $mensaje.="Error. ID incorrecto o no pertenece al carrito.<br/>";
            }
            break;

        case 'Aumentar':
            $idMenu=openssl_decrypt($_POST['idMenu'], COD, KEY);
            $idMenus=array_column($_SESSION['CARRITO'], "IDMENU");
            if (is_numeric($idMenu) && in_array($idMenu, $idMenus)) {
                foreach($_SESSION['CARRITO'] as $indice=>$MENU) {
                    if ($MENU['IDMENU']==$idMenu) {
                        $_SESSION['CARRITO'][$indice]['CANTIDADMENU']++;
                    }
                }
            } else {
                $mensaje.="Error. ID incorrecto o no pertenece al carrito.<br/>";
            }
            break;

        case 'Disminuir':
            $idMenu=openssl_decrypt($_POST['idMenu'], COD, KEY);
            $idMenus=array_column($_SESSION['CARRITO'], "IDMENU");
            if (is_numeric($idMenu) && in_array($idMenu, $idMenus)) {
                foreach($_SESSION['CARRITO'] as $indice=>$MENU) {
                    if ($MENU['IDMENU']==$idMenu) {
                        if ($_SESSION['CARRITO'][$indice]['CANTIDADMENU']>1) {
                            $_SESSION['CARRITO'][$indice]['CANTIDADMENU']--;
                        }
                    }
                }
            } else {
                $mensaje.="Error. ID incorrecto o no pertenece al carrito.<br/>";
            }
            break;

        case 'Pagar':
            header("Location:procesar_venta.php");
            break;

        case 'Vaciar':
            unset($_SESSION['CARRITO']);
            break;
    }
}
?>
