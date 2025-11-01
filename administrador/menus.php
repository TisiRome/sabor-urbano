<?php 
    include("database/bd.php"); 
    include("templates/sesion.php"); 
    include("templates/header.php"); 

    $token=htmlspecialchars($_POST['csrf_token'] ?? '');
    $txtIDMenu=filter_input(INPUT_POST, 'txtIDMenu', FILTER_SANITIZE_NUMBER_INT);
    $txtNombreMenu=htmlspecialchars($_POST['txtNombreMenu'] ?? '', ENT_QUOTES, 'UTF-8');
    $txtDescMenu=htmlspecialchars($_POST['txtDescMenu'] ?? '', ENT_QUOTES, 'UTF-8');
    $txtCategoriaMenu=htmlspecialchars($_POST['txtCategoriaMenu'] ?? '', ENT_QUOTES, 'UTF-8');
    $txtPrecioMenu=filter_input(INPUT_POST, 'txtPrecioMenu', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $txtDestacado=isset($_POST['txtDestacadoMenu']) ? 1 : 0;
    $txtImgMenu=(isset($_FILES['txtImgMenu'] ['name'])) ? $_FILES['txtImgMenu'] ['name']:"";
    $txtAccion=(isset($_POST['btnAccion'])) ? $_POST['btnAccion']:"";

    if (isset($_POST['btnAccion'])) {
    
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Error: token CSRF inválido.");
        }

        switch ($txtAccion) {
            case 'Agregar':
                $sentenciaSQL=$conexion->prepare("INSERT INTO menus (nombreMenu, descripcionMenu, categoriaMenu, precioMenu, destacado, imgMenu) VALUES (:nombreMenu, :descripcionMenu, :categoriaMenu, :precioMenu, :destacado, :imgMenu);");
                $sentenciaSQL->bindParam(':nombreMenu',$txtNombreMenu);
                $sentenciaSQL->bindParam(':descripcionMenu',$txtDescMenu);
                $sentenciaSQL->bindParam(':categoriaMenu', $txtCategoriaMenu, PDO::PARAM_INT);
                $sentenciaSQL->bindParam(':precioMenu',$txtPrecioMenu);
                $sentenciaSQL->bindParam(':destacado', $txtDestacado);

                $fecha=new DateTime();
                $nombreArchivo=($txtImgMenu!="")?$fecha->getTimestamp()."_".$_FILES['txtImgMenu']['name']:"imagen.jpg";

                $tmpImagen=$_FILES['txtImgMenu']['tmp_name'];

                if ($tmpImagen!=""){
                    move_uploaded_file($tmpImagen,"../img/menuImg/".$nombreArchivo);
                }

                $sentenciaSQL->bindParam(':imgMenu',$nombreArchivo);
                $sentenciaSQL->execute();
                
                $_SESSION['MENSAJE']="Menú agregado exitosamente.";
                header('Location:menus.php');
                exit;

            case 'Modificar':
                $sentenciaSQL=$conexion->prepare("UPDATE menus SET nombreMenu=:nombreMenu, descripcionMenu=:descripcionMenu, categoriaMenu=:categoriaMenu, precioMenu=:precioMenu, destacado=:destacado WHERE idMenu=:idMenu");
                $sentenciaSQL->bindParam(':idMenu',$txtIDMenu); 
                $sentenciaSQL->bindParam(':nombreMenu',$txtNombreMenu);
                $sentenciaSQL->bindParam(':descripcionMenu',$txtDescMenu); 
                $sentenciaSQL->bindParam(':categoriaMenu', $txtCategoriaMenu, PDO::PARAM_INT);  
                $sentenciaSQL->bindParam(':precioMenu',$txtPrecioMenu);
                $sentenciaSQL->bindParam(':destacado', $txtDestacado);
                $sentenciaSQL->execute();

                if ($txtImgMenu!="") {
                    $fecha=new DateTime();
                    $nombreArchivo=($txtImgMenu!="")?
                    $fecha->getTimestamp()."_".$_FILES['txtImgMenu']['name']:"imagen.jpg";
                    $tmpImagen=$_FILES['txtImgMenu']['tmp_name'];
                    move_uploaded_file($tmpImgMenu,"../img/menuImg/".$nombreArchivo);

                    $sentenciaSQL=$conexion->prepare("SELECT imgMenu FROM menus WHERE idMenu=:idMenu");
                    $sentenciaSQL->bindParam(':idMenu',$txtIDMenu); 
                    $sentenciaSQL->execute();
                    $menu=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

                    if(isset($menu['imgMenu'])&&($menu['imgMenu']!="imagen.jpg")){
                        if(file_exists("../img/menuImg/".$menu['imgMenu'])){
                        unlink("../img/menuImg/".$menu['imgMenu']);
                        }
                    }
                    $sentenciaSQL=$conexion->prepare("UPDATE menus SET imgMenu=:imgMenu WHERE idMenu=:idMenu");
                    $sentenciaSQL->bindParam(':idMenu',$txtIDMenu); 
                    $sentenciaSQL->bindParam(':imgMenu',$nombreArchivo);
                    $sentenciaSQL->execute();
                }

                $_SESSION['MENSAJE']="Menú modificado con éxito.";
                header('Location:menus.php');
                exit;

            case 'Cancelar':
                header('Location:menus.php');
                break;

            case 'Seleccionar':
                $sentenciaSQL=$conexion->prepare("SELECT * FROM menus WHERE idMenu=:idMenu");
                $sentenciaSQL->bindParam(':idMenu',$txtIDMenu); 
                $sentenciaSQL->execute();
                $menu=$sentenciaSQL->fetch(PDO::FETCH_LAZY);
                $txtNombreMenu=$menu['nombreMenu'];
                $txtDescMenu=$menu['descripcionMenu'];
                $txtCategoriaMenu=$menu['categoriaMenu'];
                $txtPrecioMenu=$menu['precioMenu'];
                $txtImgMenu=$menu['imgMenu'];
                $txtDestacado=$menu['destacado'];
                break;

            case 'Borrar':
                $sentenciaSQL=$conexion->prepare("SELECT imgMenu FROM menus WHERE idMenu=:idMenu");
                $sentenciaSQL->bindParam(':idMenu',$txtIDMenu); 
                $sentenciaSQL->execute();
                $menu=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

                if(isset($menu['imgMenu'])&&($menu['imgMenu']!="imagen.jpg")){
                    if(file_exists("../img/menuImg/".$menu['imgMenu'])){
                    unlink("../img/menuImg/".$menu['imgMenu']);
                    }
                }

                $sentenciaSQL=$conexion->prepare("DELETE FROM menus WHERE idMenu=:idMenu");
                $sentenciaSQL->bindParam(':idMenu',$txtIDMenu); 
                $sentenciaSQL->execute();

                $_SESSION['MENSAJE']="Menú eliminado exitosamente.";
                header("Location:menus.php");
                exit;

        }
    }

    $sentenciaSQL=$conexion->prepare("SELECT m.*, c.nombreCtgia FROM menus m LEFT JOIN categorias c ON m.categoriaMenu = c.idCtgia ORDER BY m.idMenu DESC");
    $sentenciaSQL->execute();
    $listaMenus=$sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);
    
    $sentenciaSQL=$conexion->prepare("SELECT * FROM categorias ORDER BY orden ASC, nombreCtgia ASC");
    $sentenciaSQL->execute();
    $listaCategorias=$sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="bgAdminMenus"></div>

<main id="formMenus">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-4">

                <form method="POST" id="formularioIngresoMenu" enctype="multipart/form-data">
                    <h4><b>Ingreso de menús</b></h4>
                    <hr>

                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="form-group">
                        <input type="text" name="txtIDMenu" readonly class="form-control inputForm" placeholder="ID del menú" value="<?php echo $txtIDMenu; ?>">
                    </div>

                    <div class="form-group">
                        <input type="text" name="txtNombreMenu" required class="form-control inputForm" placeholder="Nombre del menú" value="<?php echo $txtNombreMenu; ?>">
                    </div>

                    <div class="form-group">
                        <input type="text" name="txtDescMenu" class="form-control inputForm" placeholder="Descripción del menú" value="<?php echo $txtDescMenu; ?>">
                    </div>

                    <div class="form-group">
                        <select class="form-select inputForm" required name="txtCategoriaMenu">
                            <option selected value="<?php echo $txtCategoriaMenu; ?>">
                                <?php 
                                    foreach ($listaCategorias as $catTemp) {
                                        if ($catTemp['idCtgia'] == $txtCategoriaMenu) {
                                            echo htmlspecialchars($catTemp['nombreCtgia'], ENT_QUOTES, 'UTF-8');
                                        }
                                    }
                                ?>
                            </option>
                            <?php foreach ($listaCategorias as $categoria) {?>
                                <option value="<?php echo $categoria['idCtgia']; ?>"><?php echo htmlspecialchars($categoria['nombreCtgia'], ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-text text-center"><i class="fa-solid fa-dollar-sign"></i></span>
                            <input type="number" name="txtPrecioMenu" class="form-control" min="100" max="9999999" required placeholder="Costo del menú" value="<?php echo $txtPrecioMenu; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group form-check-reverse text-start">
                        <label for="destacarMenu">¿Destacar menú?</label>
                        <?php  
                            $destacado=isset($txtDestacado)?$txtDestacado: 0;
                        ?>
                        <input type="checkbox" class="form-check-input" id="destacarMenu" name="txtDestacadoMenu" <?php echo ($txtDestacado==1) ? 'checked' : ''; ?>>
                    </div>

                    <div class="form-group">
                        <div class="text-center inputForm">
                            <?php echo $txtImgMenu; ?>
                            <?php if ($txtImgMenu!==""){?>
                                    <img src="../img/menuImg/<?php echo $menu['imgMenu']; ?>" width="50" class="rounded" alt="">
                            <?php } ?>
                        </div>
                        <input type="file" class="form-control inputForm" name="txtImgMenu">
                    </div>
                    
                    <?php if (!empty($_SESSION['MENSAJE'])): ?>
                        <div class="alert alert-success m-1" role="alert">
                            <?php echo $_SESSION['MENSAJE']; ?>
                        </div>
                        <?php unset($_SESSION['MENSAJE']); ?>
                    <?php endif; ?>


                    <div class="d-flex flex-column flex-md-row justify-content-center gap-2">
                        <button type="submit" name="btnAccion" value="Agregar" class="btn btn-info btn-md w-100 w-md-auto">Agregar</button>
                        <button type="submit" name="btnAccion" value="Modificar" class="btn btn-success btn-md w-100 w-md-auto">Modificar</button>
                        <button type="submit" name="btnAccion" value="Cancelar" class="btn btn-danger btn-md w-100 w-md-auto">Cancelar</button>
                    </div>
                </form>

            </div>
        </div>

        <div class="row justify-content-center my-4">
            <div class="col-md-5">
                <input type="text" id="buscador" class="form-control bg-light" placeholder="Buscar menús...">
            </div>

            <div class="col-md-3">
                <select id="filtroCategoria" class="form-select bg-light">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($listaCategorias as $categoria) { ?>
                        <option value="<?php echo $categoria['idCtgia']; ?>">
                            <?php echo htmlspecialchars($categoria['nombreCtgia'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md">
                <table id="tablaMenus" class="table text-center">
                <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre del menú</th>
                    <th scope="col">Descripción del menú</th>
                    <th scope="col">Categoria</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Imagen</th>
                </tr>
                <?php foreach ($listaMenus as $menu){?>
                <tr class="filaMenus" data-categoria="<?php echo $menu['categoriaMenu']; ?>">
                    <td><?php echo htmlspecialchars($menu['idMenu'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="nombreMenus">
                        <?php 
                            if ($menu['destacado']==1){
                                echo "<i class='fa-solid fa-star'></i> ".htmlspecialchars($menu['nombreMenu'], ENT_QUOTES, 'UTF-8');
                            } else {
                                echo htmlspecialchars($menu['nombreMenu'], ENT_QUOTES, 'UTF-8');
                            }
                        ?>
                    </td>
                    <td class="descripcionMenus"><?php echo htmlspecialchars($menu['descripcionMenu'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($menu['nombreCtgia'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>$<?php echo htmlspecialchars($menu['precioMenu'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($menu['imgMenu'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <form method="POST">

                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="txtIDMenu" value="<?php echo $menu['idMenu']; ?>">
                            
                            <input type="submit" name="btnAccion" value="Seleccionar" class="btn btn-primary btn-sm">
                            <input type="submit" name="btnAccion" value="Borrar" class="btn btn-danger btn-sm"> 
                        </form>
                    </td>
                </tr>
                <?php } ?>
                </thead>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
function filtrarMenu() {
    const texto = document.getElementById("buscador").value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,"");

    const categoriaSeleccionada = document.getElementById("filtroCategoria").value.toLowerCase();

    document.querySelectorAll(".filaMenus").forEach(function(fila) {

        const nombre = fila.querySelector(".nombreMenus").innerText.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,"");

        const descripcion = fila.querySelector(".descripcionMenus").innerText.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,"");

        const categoria = fila.dataset.categoria;

        // Condiciones del filtro
        const coincideTexto = nombre.includes(texto) || descripcion.includes(texto);
        const coincideCategoria = categoriaSeleccionada === "" || categoria === categoriaSeleccionada;

        // Mostrar/ocultar
        fila.style.display = (coincideTexto && coincideCategoria) ? "" : "none";
    });
}

// Escuchadores
document.getElementById("buscador").addEventListener("keyup", filtrarMenu);
document.getElementById("filtroCategoria").addEventListener("change", filtrarMenu);
</script>


<?php include("templates/footer.php"); ?>
