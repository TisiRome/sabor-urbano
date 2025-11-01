<?php
    include_once 'templates/funciones.php';   
    include("administrador/database/bd.php");
    include("templates/sesion.php");
    include("favoritos.php");
    include("templates/header.php"); 

    $sentencia = $conexion->prepare("SELECT m.*, c.nombreCtgia FROM menus m LEFT JOIN categorias c ON m.categoriaMenu = c.idCtgia ORDER BY c.nombreCtgia, m.nombreMenu");
    $sentencia->execute();
    $listaMenus=$sentencia->fetchAll(PDO::FETCH_ASSOC);

    $sentenciaSQL=$conexion->prepare("SELECT * FROM categorias ORDER BY orden ASC, nombreCtgia ASC");
    $sentenciaSQL->execute();
    $listaCategorias=$sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);

    //Paginación de menús.
    $registrosPorPagina=3; 
    $pagina=isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $inicio=($pagina > 1) ? ($pagina - 1) * $registrosPorPagina : 0;

    $totalMenus=count($listaMenus);
    $totalPaginas=ceil($totalMenus / $registrosPorPagina);

    $menusPaginados=array_slice($listaMenus, $inicio, $registrosPorPagina);

    //Crea el array asociativo de categorías.
    $categoriasDisponibles=[];
    foreach ($listaMenus as $menu) {
        $categoria=trim($menu['nombreCtgia']);
        if (!isset($categoriasDisponibles[$categoria])) {
            $categoriasDisponibles[$categoria]=[];
        }
        $categoriasDisponibles[$categoria][]=$menu;
    }

    // Ordena según el "orden" de la base
    $categoriasOrdenadas=[];
    foreach ($listaCategorias as $categoriaDB) {
        $nombreCat=trim($categoriaDB['nombreCtgia']);

        if (isset($categoriasDisponibles[$nombreCat])) {
            $categoriasOrdenadas[$nombreCat]=$categoriasDisponibles[$nombreCat];
        } else {
            $categoriasOrdenadas[$nombreCat]=[]; // si querés ver categoría vacía
        }
    }

    //Crea el array asociativo de los textos de cada categoría.
    $textosCategorias=[];
    foreach ($listaCategorias as $categoria) {
        $textosCategorias[$categoria['nombreCtgia']]=$categoria['descripcionCtgia'];
    }

    //Las categorías no contempladas en el array de los textos, muestran un texto por defecto.
    function obtenerTextoPorDefecto($categoria) {
        $categoria=strtolower($categoria);
        $plural=(substr($categoria, -1)==='s') ? $categoria : $categoria . 's';

        return "Descubre nuestra deliciosa selección de ".$plural." preparados con los mejores ingredientes y mucho sabor.";
    }
?>

<?php if(!empty($_SESSION['FAVORITOS'])) { ?>

    <!--Botón que lleva a la página de favoritos (vista escritorio)-->
    <a id="btnDorado" class="btn btn-warning d-none d-md-block" type="button" href="mostrar_favoritos.php">
        <i class="fa-solid fa-star"></i> MIS FAVORITOS
    </a>

    <!--Botón que lleva a la página de favoritos (vista móviles)-->
    <a id="btnFavoritosMovil" class="btn btn-warning d-block d-md-none m-3" type="button" href="mostrar_favoritos.php">
        <i class="fa-solid fa-star"></i>
    </a>
<?php } ?>

<article id="menusArt" aria-labelledby="menusHeader">
    <h1 class="display-5 text-center" id="menusHeader"><i>¡BIENVENIDO AL MENÚ URBANO!</i></h1>
    <p class="text-center">Explora nuestra amplia selección de comida gourmet y déjate cautivar por los sabores únicos que tenemos para vos.</p>

    <!--Botón que lleva a la página del carrito de compras-->
    <div class="text-center m-2">
        <a href="pedidos.php" class="btn btn-primary btn-md" role="button">¡Ordena ya!</a>
    </div>

    <hr>

    <!--Barra de navegación de las categorías-->
    <ul class="nav nav-tabs nav-justified my-4" id="menuTab" role="tablist">
        <?php $esPrimera=true; ?>
        <?php foreach ($categoriasOrdenadas as $categoria => $menus) { ?>
            <li class="nav-item m-0" role="presentation">
                <button class="nav-link <?php echo $esPrimera ? 'active' : ''; ?>" id="<?php echo htmlspecialchars(strtolower($categoria)); ?>Tab" data-bs-toggle="tab" data-bs-target="#menu<?php echo htmlspecialchars($categoria); ?>" type="button" role="tab" aria-controls="menu<?php echo htmlspecialchars($categoria); ?>" aria-selected="<?php echo $esPrimera ? 'true' : 'false'; ?>"> <?php echo htmlspecialchars(strtoupper($categoria)); ?>
                </button>
            </li>
            <?php $esPrimera=false; ?>
        <?php } ?>
    </ul>
</article>

<!--Barra de búsqueda de menús-->
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <input type="text" id="buscador" class="form-control bg-light" placeholder="Buscar menús...">
        </div>
    </div>
</div>

<!--Listado de menús (filtrado por la barra de navegación)-->
<div class="tab-content" id="menuTabContenido">
    <?php $esPrimera=true; ?>
    <?php foreach ($categoriasOrdenadas as $categoria => $menus) { ?>
        <div class="tab-pane fade <?php echo $esPrimera ? 'show active' : ''; ?> p-3" id="menu<?php echo htmlspecialchars($categoria); ?>" role="tabpanel" aria-labelledby="<?php echo htmlspecialchars(strtolower($categoria)); ?>Tab">

            <article aria-labelledby="categoriasMenu">
                <h2 class="text-center" id="categoriasMenu"><?php echo htmlspecialchars($categoria); ?></h2>
                <p class="text-center">
                    <?php echo isset($textosCategorias[$categoria]) ? $textosCategorias[$categoria] : obtenerTextoPorDefecto($categoria); ?>
                </p>

                <?php
                    //Paginación por categoría.
                    $registrosPorPagina=3;
                    $paramPagina="pagina_".strtolower(str_replace(' ', '_', $categoria));
                    $pagina=isset($_GET[$paramPagina]) ? (int)$_GET[$paramPagina] : 1;
                    $inicio=($pagina>1) ? ($pagina - 1)*$registrosPorPagina : 0;
                    $totalMenus=count($menus);
                    $totalPaginas=ceil($totalMenus/$registrosPorPagina);
                    $menusPaginados=array_slice($menus, $inicio, $registrosPorPagina);
                ?>

                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <?php if (!empty($menusPaginados)) { ?>

                            <?php foreach ($menusPaginados as $menu) { 
                                //Crea un ID para cada modal que muestra los detalles del plato.
                                $idModal="detallesModal".$menu['idMenu'];
                                //Extrae de la sesión FAVORITOS el ID de cada menú.
                                $idMenusFavoritos=array_column($_SESSION['FAVORITOS'] ?? [], "IDMENU");
                                //Verifica si el ID del menú que viene de la base de datos, coincide con el ID del menú que está dentro de la sesión de FAVORITOS.
                                $estaEnFavoritos=in_array($menu['idMenu'], $idMenusFavoritos);
                                //Se verifica que si el menú está en favoritos, agregue un ícono estrella.
                                $menuFav=$estaEnFavoritos ? "<i class='fa-solid fa-star'></i> " : "";
                            ?>
                                <div class="col-md-3 mb-4">
                                    <!--Card de menú-->
                                    <div class="card menuCard bg-white border border-dark border-2 h-100" data-descripcion="<?php echo htmlspecialchars($menu['descripcionMenu']); ?>">
                                        <!--Imagen del menú-->
                                        <img src="img/menuImg/<?php echo htmlspecialchars($menu['imgMenu']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($menu['nombreMenu']); ?>" loading="lazy">
                                        <div class="card-body text-center pb-0">
                                            <h5 class="card-title"><i><?php echo $menuFav; ?><?php echo htmlspecialchars($menu['nombreMenu']); ?></i></h5>
                                        </div>
                                        <div class="card-footer text-center bg-light border-0">
                                            
                                            <form method="POST">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="text" hidden name="idMenu" value="<?php echo openssl_encrypt($menu['idMenu'], COD, KEY); ?>">
                                                <input type="text" hidden name="nombreMenu" value="<?php echo  openssl_encrypt($menu['nombreMenu'], COD, KEY); ?>">
                                                <input type="text" hidden name="precioMenu" value="<?php echo openssl_encrypt($menu['precioMenu'], COD, KEY); ?>">
                                                <input type="text" hidden name="descripcionMenu" value="<?php echo openssl_encrypt($menu['descripcionMenu'], COD, KEY); ?>">
                                                <input type="text" hidden name="imgMenu" value="<?php echo openssl_encrypt($menu['imgMenu'], COD, KEY); ?>">
                                                <div id="btnGroupMenu" class="btn-group" role="group">

                                                    <!--Botón que abre el modal con los detalles del plato-->
                                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#<?php echo $idModal; ?>">
                                                        VER DETALLES
                                                    </button>

                                                    <?php if ($estaEnFavoritos===false) { ?>
                                                        <!--Botón para agregar el menú a favoritos-->
                                                        <button class="btn btn-outline-success btn-md" name="btnAccion" value="Guardar" type="submit" role="button">
                                                            AGREGAR A FAVORITOS
                                                        </button>
                                                    <?php } else { ?>
                                                        <!--Botón de eliminar el menú de favoritos-->
                                                        <button class="btn btn-outline-danger btn-md" name="btnAccion" value="Remover" type="submit" role="button">
                                                            ELIMINAR DE FAVORITOS
                                                        </button>
                                                    <?php } ?>

                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                                
                                <!--Modal de detalles del menú-->
                                <div class="modal fade" id="<?php echo $idModal; ?>" tabindex="-1" aria-labelledby="modalTle" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalTle">
                                                    Detalles del plato: <?php echo htmlspecialchars($menu['nombreMenu']); ?>
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h5>
                                                    <strong>Precio: $<?php echo htmlspecialchars($menu['precioMenu']); ?></strong>
                                                </h5>
                                                <h6 class="card-subtitle mb-2 text-muted">
                                                    <?php echo htmlspecialchars($menu['descripcionMenu']); ?>
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            <?php } ?>

                        <?php } else { ?>

                            <div class="col-12 text-center">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Próximamente más <?php echo htmlspecialchars(strtolower($categoria)); ?>
                                </div>
                            </div>

                        <?php } ?>

                        <?php if ($totalPaginas>1) { ?>
                            <nav aria-label="Paginación de <?php echo htmlspecialchars($categoria); ?>">
                                <ul class="pagination justify-content-center mt-4">
                                    <li class="page-item <?php echo ($pagina<=1) ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?<?php echo $paramPagina; ?>=<?php echo $pagina - 1; ?>#menu<?php echo htmlspecialchars($categoria); ?>" aria-label="Anterior">
                                            &laquo;
                                        </a>
                                    </li>

                                    <?php for ($i=1; $i<=$totalPaginas; $i++) { ?>
                                        <li class="page-item <?php echo ($pagina==$i) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?<?php echo $paramPagina; ?>=<?php echo $i; ?>#menu<?php echo htmlspecialchars($categoria); ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php } ?>

                                    <li class="page-item <?php echo ($pagina>=$totalPaginas) ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?<?php echo $paramPagina; ?>=<?php echo $pagina + 1; ?>#menu<?php echo htmlspecialchars($categoria); ?>" aria-label="Siguiente">
                                            &raquo;
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        <?php } ?>

                    </div>
                </div>
            </article>
            
        </div>
    <?php $esPrimera=false; ?>
    <?php } ?>
</div>


<script>
    //Guarda y restaura la pestaña activa al cambiar de página o recargar
    document.addEventListener("DOMContentLoaded", function () {
        const tabs = document.querySelectorAll('#menuTab button[data-bs-toggle="tab"]');

        //Cuando el usuario cambia de pestaña, se guarda en localStorage
        tabs.forEach(tab => {
            tab.addEventListener('shown.bs.tab', function (event) {
                localStorage.setItem('ultimaCategoriaActiva', event.target.id);
            });
        });

        //Al recargar, si existe una pestaña guardada, la activa
        const ultima = localStorage.getItem('ultimaCategoriaActiva');
        if (ultima) {
            const tabGuardado = document.getElementById(ultima);
            if (tabGuardado) {
                const tab = new bootstrap.Tab(tabGuardado);
                tab.show();
            }
        }

        //Si hay hash en la URL (por ejemplo #menuPrincipales), prioriza eso
        if (window.location.hash) {
            const tabLink = document.querySelector(`[data-bs-target="${window.location.hash}"]`);
            if (tabLink) {
                const tab = new bootstrap.Tab(tabLink);
                tab.show();
            }
        }
    });

    document.getElementById("buscador").addEventListener("keyup", function() {
    //Se limpia el texto escrito en el buscador (a minúsculas y sin acentos)//
    const filtro = this.value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,"");

    //Se recorren todas las cards//
    document.querySelectorAll(".menuCard").forEach(function(card) {
        //Se limpian los nombres y descripciones de las tarjetas//
        const nombre = card.querySelector(".card-title").innerText.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,"");
        const descripcion = card.dataset.descripcion.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,"");

        //Se comparan los nombres y descripciones con el filtro de búsqueda y muestra los resultados (oculta las cards que no coinciden)//
        if (nombre.includes(filtro) || descripcion.includes(filtro)) {
            card.closest(".col-md-3").style.display = "block";
        } else {
            card.closest(".col-md-3").style.display = "none";
        }
    });
});
</script>


<?php include("templates/footer.php"); ?>