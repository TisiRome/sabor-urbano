<?php 
$mensaje="";

if (isset($_POST['btnAccion'])) {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Error: token CSRF inválido.");
    }

    switch ($_POST['btnAccion']) {
        case 'Guardar':
            if (isset($_POST['idMenu'])) {
                $idMenu=openssl_decrypt($_POST['idMenu'], COD, KEY);
                $nombreMenu=openssl_decrypt($_POST['nombreMenu'], COD, KEY);
                $precioMenu=openssl_decrypt($_POST['precioMenu'], COD, KEY);
                $descripcionMenu=openssl_decrypt($_POST['descripcionMenu'], COD, KEY);
                $imgMenu=openssl_decrypt($_POST['imgMenu'], COD, KEY);


                try {
                    $idMenu=validarEntero($idMenu,'ID');
                    $nombreMenu=validarTexto($nombreMenu, 'Nombre');
                    $precioMenu=validarEntero($precioMenu,'Precio');
                    $descripcionMenu=validarTexto($descripcionMenu, 'Descripcion');
                    $imgMenu=validarTexto($imgMenu, 'Imagen');

                    if ($idMenu===false || $nombreMenu===false || $precioMenu===false || $descripcionMenu===false || $imgMenu===false) {
                        throw new Exception("Error: datos del formulario alterados o inválidos.");
                    }

                } catch (Exception $e) {
                    $mensaje.=$e->getMessage()."<br/>";
                }


                if (!isset($_SESSION['FAVORITOS'])) {
                    $MENU=array(
                        'IDMENU' => $idMenu,
                        'NOMBREMENU' => $nombreMenu,
                        'PRECIOMENU' => $precioMenu,
                        'DESCRIPCIONMENU' => $descripcionMenu,
                        'IMAGENMENU' => $imgMenu,
                    );
                    $_SESSION['FAVORITOS'][0]=$MENU;
                    echo "<script>alert('Menú agregado a favoritos.')</script>";
                } else {
                    $idMenus=array_column($_SESSION['FAVORITOS'], "IDMENU");
                    if (in_array($idMenu, $idMenus)) {
                        echo "<script>alert('Error. Este producto ya ha sido agregado a favoritos.')</script>";
                    } else {
                        $numeroMenus=count($_SESSION['FAVORITOS']);
                        $MENU=array(
                            'IDMENU' => $idMenu,
                            'NOMBREMENU' => $nombreMenu,
                            'PRECIOMENU' => $precioMenu,
                            'DESCRIPCIONMENU' => $descripcionMenu,
                            'IMAGENMENU' => $imgMenu,
                        );
                        $_SESSION['FAVORITOS'][$numeroMenus]=$MENU;
                    }
                }
            }
            header("Location:menus.php");
            break;
        
        case 'Remover':
            $idMenu=openssl_decrypt($_POST['idMenu'], COD, KEY);
            $idMenus=array_column($_SESSION['FAVORITOS'], "IDMENU");
            if (is_numeric($idMenu) && in_array($idMenu, $idMenus)) {
                foreach ($_SESSION['FAVORITOS'] as $indice => $MENU) {
                    if ($MENU['IDMENU']==$idMenu) {
                        unset($_SESSION['FAVORITOS'][$indice]);
                    }
                }
            } else {
                $mensaje.="Error. ID incorrecto o no pertenece a favoritos.<br/>";
            }
            break;
    }
}
?>
