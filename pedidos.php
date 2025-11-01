<?php 
    include_once 'templates/funciones.php';
    include("administrador/database/bd.php");
    include("templates/sesion.php");
    include("templates/header.php");
    
    $sentencia=$conexion->prepare("SELECT m.*, c.nombreCtgia FROM menus m LEFT JOIN categorias c ON m.categoriaMenu = c.idCtgia ORDER BY c.nombreCtgia, m.nombreMenu");
    $sentencia->execute();
    $listaMenus=$sentencia->fetchAll(PDO::FETCH_ASSOC);

    $sentenciaSQL=$conexion->prepare("SELECT * FROM categorias ORDER BY orden ASC, nombreCtgia ASC");
    $sentenciaSQL->execute();
    $listaCategorias=$sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);

    // Crea el array asociativo de categorías.
    $categoriasDisponibles=[];
    foreach ($listaMenus as $menu) {
        $categoria=trim($menu['nombreCtgia']);
        if (!isset($categoriasDisponibles[$categoria])) {
            $categoriasDisponibles[$categoria]=[];
        }
        $categoriasDisponibles[$categoria][]=$menu;
    }

    // Orden deseado de categorías.
    $categoriasOrdenadas=[];
    foreach ($listaCategorias as $categoriaDB) {
        $nombreCat=trim($categoriaDB['nombreCtgia']);

        if (isset($categoriasDisponibles[$nombreCat])) {
            $categoriasOrdenadas[$nombreCat]=$categoriasDisponibles[$nombreCat];
        } else {
            $categoriasOrdenadas[$nombreCat]=[];
        }
    }
?>

<article id="pedidosArt" aria-labelledby="pedidosHeader">
    <h1 class="display-5 text-center" id="pedidosHeader">¡ORDENE AHORA!</h1>
    <p class="text-center">
        Realizá tu pedido en línea con Sabor Urbano y recibí en tu puerta nuestros platos gourmet, frescos y listos para disfrutar. 
        Fácil, rápido y seguro: elegí tu menú favorito, personalizalo y disfrutá de la mejor cocina urbana sin moverte de casa.
    </p>

    <hr>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md m-0 p-0">
                <section id="menusSec" aria-labelledby="menusHeader">

                    <!--Menús destacados-->
                    <?php
                        //Filtrar solo los destacados
                        $menusDestacados = array_filter($listaMenus, function($menu) {
                            return $menu['destacado'] == "1";
                        });

                        //Paginación de destacados
                        $registrosPorPaginaDest=3;
                        $paginaDest=isset($_GET['pagina_destacados']) ? (int)$_GET['pagina_destacados'] : 1;
                        $inicioDest=($paginaDest>1) ? ($paginaDest-1) * $registrosPorPaginaDest : 0;
                        $totalDestacados=count($menusDestacados);
                        $totalPaginasDest=ceil($totalDestacados/$registrosPorPaginaDest);
                        $menusDestacados=array_slice($menusDestacados, $inicioDest, $registrosPorPaginaDest);
                    ?>

                    <div class="row justify-content-center" id="destacados">
                        <h4 id="menusHeader" class="text-center">Menús destacados</h4>

                        <?php if (!empty($menusDestacados)) { ?>
                            <?php foreach ($menusDestacados as $menu) { ?>
                                <div class="col-md-3 m-1 p-0">
                                    <div class="card pedidosCard">
                                        <div class="card-body text-center">
                                            <h5 class="card-title"><?php echo htmlspecialchars($menu['nombreMenu']); ?></h5>
                                            <p class="card-text"><?php echo htmlspecialchars($menu['descripcionMenu']); ?></p>
                                            <p class="card-text"><small class="text-body-secondary">$<?php echo htmlspecialchars($menu['precioMenu']); ?></small></p>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <form method="POST">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="text" hidden name="idMenu" value="<?php echo openssl_encrypt($menu['idMenu'], COD, KEY); ?>">
                                                <input type="text" hidden name="nombreMenu" value="<?php echo openssl_encrypt($menu['nombreMenu'], COD, KEY); ?>">
                                                <input type="text" hidden name="precioMenu" value="<?php echo openssl_encrypt($menu['precioMenu'], COD, KEY); ?>">
                                                <input type="text" hidden name="cantidadMenu" value="<?php echo openssl_encrypt(1, COD, KEY); ?>">
                                                <button class="btn btn-outline-primary btn-md" name="btnAccion" value="Agregar" type="submit" role="button">PIDA YA</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <p class="text-center text-muted">No hay menús destacados disponibles.</p>
                        <?php } ?>
                    </div>

                    <!-- Paginación de destacados -->
                    <?php if ($totalPaginasDest > 1) { ?>
                        <nav aria-label="Paginación de menús destacados">
                            <ul class="pagination justify-content-center mt-4">
                                <li class="page-item <?php echo ($paginaDest <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?pagina_destacados=<?php echo $paginaDest - 1; ?>#destacados" aria-label="Anterior">&laquo;</a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPaginasDest; $i++) { ?>
                                    <li class="page-item <?php echo ($paginaDest == $i) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?pagina_destacados=<?php echo $i; ?>#destacados"><?php echo $i; ?></a>
                                    </li>
                                <?php } ?>
                                <li class="page-item <?php echo ($paginaDest >= $totalPaginasDest) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?pagina_destacados=<?php echo $paginaDest + 1; ?>#destacados" aria-label="Siguiente">&raquo;</a>
                                </li>
                            </ul>
                        </nav>
                    <?php } ?>

                    
                    <!-- Menús por categoría -->
                    <ul class="nav nav-tabs nav-justified my-4" id="menuTab" role="tablist">
                        <?php $esPrimera=true; ?>
                        <?php foreach ($categoriasOrdenadas as $categoria => $menus) { ?>
                            <li class="nav-item m-0" role="presentation">
                                <button class="nav-link <?php echo $esPrimera ? 'active' : ''; ?>" 
                                        id="<?php echo htmlspecialchars(strtolower($categoria)); ?>Tab"
                                        data-bs-toggle="tab" 
                                        data-bs-target="#menu<?php echo htmlspecialchars($categoria); ?>" 
                                        type="button" role="tab" 
                                        aria-controls="menu<?php echo htmlspecialchars($categoria); ?>" 
                                        aria-selected="<?php echo $esPrimera ? 'true' : 'false'; ?>">
                                    <?php echo htmlspecialchars(strtoupper($categoria)); ?>
                                </button>
                            </li>
                            <?php $esPrimera=false; ?>
                        <?php } ?>
                    </ul>

                    <div class="tab-content" id="menuTabContenido">
                        <?php $esPrimera=true; ?>
                        <?php foreach ($categoriasOrdenadas as $categoria => $menus) { ?>
                            <?php 
                                // Paginación por categoría
                                $registrosPorPagina=3;
                                $paramPagina="pagina_".strtolower(str_replace(' ', '_', $categoria));
                                $pagina=isset($_GET[$paramPagina]) ? (int)$_GET[$paramPagina] : 1;
                                $inicio=($pagina>1) ? ($pagina - 1)*$registrosPorPagina : 0;
                                $totalMenus=count($menus);
                                $totalPaginas=ceil($totalMenus/$registrosPorPagina);
                                $menusPaginados=array_slice($menus, $inicio, $registrosPorPagina);
                            ?>

                            <div class="tab-pane fade <?php echo $esPrimera ? 'show active' : ''; ?>" id="menu<?php echo htmlspecialchars($categoria); ?>" role="tabpanel" aria-labelledby="<?php echo htmlspecialchars(strtolower($categoria)); ?>Tab">
                                
                                <div class="row justify-content-center mt-3">
                                    <?php foreach ($menusPaginados as $menu) { ?>
                                        <div class="col-md-3 m-1 p-0">
                                            <div class="card pedidosCard">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title"><?php echo htmlspecialchars($menu['nombreMenu']); ?></h5>
                                                    <p class="card-text"><?php echo htmlspecialchars($menu['descripcionMenu']); ?></p>
                                                    <p class="card-text"><small class="text-body-secondary">$<?php echo htmlspecialchars($menu['precioMenu']); ?></small></p>
                                                </div>
                                                <div class="card-footer bg-white">
                                                    <form method="POST">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                        <input type="text" hidden name="idMenu" value="<?php echo openssl_encrypt($menu['idMenu'], COD, KEY); ?>">
                                                        <input type="text" hidden name="nombreMenu" value="<?php echo  openssl_encrypt($menu['nombreMenu'], COD, KEY); ?>">
                                                        <input type="text" hidden name="precioMenu" value="<?php echo openssl_encrypt($menu['precioMenu'], COD, KEY); ?>">
                                                        <input type="text" hidden name="cantidadMenu" value="<?php echo openssl_encrypt(1, COD, KEY)?>">
                                                        <button class="btn btn-outline-primary btn-md" name="btnAccion" value="Agregar" type="submit" role="button">PIDA YA</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>

                                <!-- Paginación -->
                                <?php if ($totalPaginas>1) { ?>
                                    <nav aria-label="Paginación de <?php echo htmlspecialchars($categoria); ?>">
                                        <ul class="pagination justify-content-center mt-4">
                                            <li class="page-item <?php echo ($pagina<=1) ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="?<?php echo $paramPagina; ?>=<?php echo $pagina - 1; ?>#menu<?php echo htmlspecialchars($categoria); ?>" aria-label="Anterior">&laquo;</a>
                                            </li>
                                            <?php for ($i=1; $i<=$totalPaginas; $i++) { ?>
                                                <li class="page-item <?php echo ($pagina==$i) ? 'active' : ''; ?>">
                                                    <a class="page-link" href="?<?php echo $paramPagina; ?>=<?php echo $i; ?>#menu<?php echo htmlspecialchars($categoria); ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php } ?>
                                            <li class="page-item <?php echo ($pagina>=$totalPaginas) ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="?<?php echo $paramPagina; ?>=<?php echo $pagina + 1; ?>#menu<?php echo htmlspecialchars($categoria); ?>" aria-label="Siguiente">&raquo;</a>
                                            </li>
                                        </ul>
                                    </nav>
                                <?php } ?>

                            </div>
                        <?php $esPrimera=false; ?>
                        <?php } ?>
                    </div>

                </section>
            </div>
        </div>
    </div>
</article>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const tabs = document.querySelectorAll('#menuTab button[data-bs-toggle="tab"]');

    // Guarda la última pestaña seleccionada
    tabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (event) {
            localStorage.setItem('ultimaCategoriaActivaPedidos', event.target.id);
        });
    });

    // Restaura la última pestaña activa
    const ultima = localStorage.getItem('ultimaCategoriaActivaPedidos');
    if (ultima) {
        const tabGuardado = document.getElementById(ultima);
        if (tabGuardado) {
            const tab = new bootstrap.Tab(tabGuardado);
            tab.show();
        }
    }

    // Si hay hash en la URL, prioriza esa pestaña
    if (window.location.hash) {
        const tabLink = document.querySelector(`[data-bs-target="${window.location.hash}"]`);
        if (tabLink) {
            const tab = new bootstrap.Tab(tabLink);
            tab.show();
        }
    }
});
</script>

<?php include("templates/footer.php"); ?>
