<?php 
    include("database/bd.php"); 
    include("templates/sesion.php"); 
    include("templates/header.php");

    $token=htmlspecialchars($_POST['csrf_token'] ?? '');
    $txtIDCategoria=filter_input(INPUT_POST, 'txtIDCategoria', FILTER_SANITIZE_NUMBER_INT);
    $txtNombreCategoria=htmlspecialchars($_POST['txtNombreCategoria'] ?? '', ENT_QUOTES, 'UTF-8');
    $txtDescCategoria=htmlspecialchars($_POST['txtDescCategoria'] ?? '', ENT_QUOTES, 'UTF-8');
    $txtAccion=(isset($_POST['btnAccion'])) ? $_POST['btnAccion']:"";

    if (isset($_POST['btnAccion'])) {
    
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Error: token CSRF inválido.");
        }

        switch ($txtAccion) {
            case 'Agregar':
                $sentenciaSQL=$conexion->prepare("INSERT INTO categorias (nombreCtgia, descripcionCtgia) VALUES (:nombreCtgia, :descripcionCtgia);");
                $sentenciaSQL->bindParam(':nombreCtgia',$txtNombreCategoria);
                $sentenciaSQL->bindParam(':descripcionCtgia',$txtDescCategoria);
                $sentenciaSQL->execute();
                
                $_SESSION['MENSAJE']="Categoría agregada exitosamente.";
                header('Location:categorias.php');
                exit;

            case 'Modificar':
                $sentenciaSQL=$conexion->prepare("UPDATE categorias SET nombreCtgia=:nombreCtgia, descripcionCtgia=:descripcionCtgia WHERE idCtgia=:idCtgia");
                $sentenciaSQL->bindParam(':idCtgia',$txtIDCategoria);
                $sentenciaSQL->bindParam(':nombreCtgia',$txtNombreCategoria);
                $sentenciaSQL->bindParam(':descripcionCtgia',$txtDescCategoria);
                $sentenciaSQL->execute();

                $_SESSION['MENSAJE']="Categoría modificada con éxito.";
                header('Location:categorias.php');
                exit;

            case 'Cancelar':
                header('Location:categorias.php');
                break;

            case 'Seleccionar':
                $sentenciaSQL=$conexion->prepare("SELECT * FROM categorias WHERE idCtgia=:idCtgia");
                $sentenciaSQL->bindParam(':idCtgia',$txtIDCategoria);
                $sentenciaSQL->execute();
                $categoria=$sentenciaSQL->fetch(PDO::FETCH_LAZY);
                $txtNombreCategoria=$categoria['nombreCtgia'];
                $txtDescCategoria=$categoria['descripcionCtgia'];
                break;
            
            case 'Borrar':
                $sentenciaSQL=$conexion->prepare("DELETE FROM categorias WHERE idCtgia=:idCtgia");
                $sentenciaSQL->bindParam(':idCtgia',$txtIDCategoria); 
                $sentenciaSQL->execute();

                $_SESSION['MENSAJE']="Categoría eliminada exitosamente.";
                header("Location:categorias.php");
                exit;
        }
    }

    $sentenciaSQL=$conexion->prepare("SELECT * FROM categorias");
    $sentenciaSQL->execute();
    $listaCategorias=$sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="bgAdminMenus"></div>

<main id="formMenus">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-4">

                <form method="POST" id="formularioIngresoMenu" enctype="multipart/form-data">
                    <h4><b>Ingreso de categorías</b></h4>
                    <hr>

                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="form-group">
                        <input type="text" name="txtIDCategoria" readonly class="form-control inputForm" placeholder="ID de la categoría" value="<?php echo $txtIDCategoria; ?>">
                    </div>

                    <div class="form-group">
                        <input type="text" name="txtNombreCategoria" required class="form-control inputForm" placeholder="Nombre de la categoría" value="<?php echo $txtNombreCategoria; ?>">
                    </div>

                    <div class="form-group">
                        <input type="text" name="txtDescCategoria" class="form-control inputForm" placeholder="Descripción de la categoría" value="<?php echo $txtDescCategoria; ?>">
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
            <div class="col-md-8">
                <input type="text" id="buscador" class="form-control bg-light" placeholder="Buscar categorías...">
            </div>
        </div>

        <div class="row">
            <div class="col-md">
                <table id="tablaMenus" class="table text-center">
                <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre de la categoría</th>
                    <th scope="col">Descripción de la categoría</th>
                </tr>
                <?php foreach ($listaCategorias as $categoria){?>
                <tr class="filaMenus">
                    <td><?php echo htmlspecialchars($categoria['idCtgia'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="nombreMenus"><?php echo htmlspecialchars($categoria['nombreCtgia'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="descripcionMenus"><?php echo htmlspecialchars($categoria['descripcionCtgia'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <form method="POST">

                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="txtIDCategoria" value="<?php echo $categoria['idCtgia']; ?>">
                            
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
document.getElementById("buscador").addEventListener("keyup", function() {
    // Texto ingresado en el buscador, normalizado
    const filtro = this.value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,"");
    // Recorre cada fila de la tabla
    document.querySelectorAll(".filaMenus").forEach(function(fila) {

        const nombre = fila.querySelector(".nombreMenus").innerText.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,"");

        const descripcion = fila.querySelector(".descripcionMenus").innerText.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,"");

        // Si coincide con nombre o descripción → se muestra
        if (nombre.includes(filtro) || descripcion.includes(filtro)) {
            fila.style.display = "";
        } else {
            fila.style.display = "none";
        }
    });
});
</script>

<?php include("templates/footer.php"); ?>