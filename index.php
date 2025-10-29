<?php
    include_once 'templates/funciones.php';    
    include("administrador/database/bd.php");
    include("templates/sesion.php");
    include("templates/header.php"); 

    //Cantidad de registros por página.
    $registrosPorPagina=3;
    $pagina=isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $inicio=($pagina > 1) ? ($pagina - 1) * $registrosPorPagina : 0;

    //Directorio donde se guarda el caché.
    $cache=__DIR__ . "/cache/menus_pagina_$pagina.json";
    $cacheTiempo=60;

    //Comprueba si existe el archivo del caché y si todavía no expiró.
    if (file_exists($cache) && (time() - filemtime($cache)) < $cacheTiempo) {
        //Se saltea la consulta a la base de datos y lo hace al archivo.
        $data=json_decode(file_get_contents($cache), true);

        // Seguridad por si el JSON no tiene lo esperado
        $listaMenus=isset($data['listaMenus']) ? $data['listaMenus'] : [];
        $totalPaginas=isset($data['totalPaginas']) ? (int)$data['totalPaginas'] : 0;

        // Evita el warning: determina si hay destacados a partir del cache
        if (isset($data['hayDestacados'])) {
            $hayDestacados=(bool)$data['hayDestacados'];
        } else {
            // compatibilidad con caches viejos: si no trae hayDestacados deducirlo
            $hayDestacados=!empty($listaMenus);
        }
    } else {
        //Si no existe el archivo caché o expiró, se hace una nueva consulta.
        $hayDestacados=(bool) $conexion->query("SELECT COUNT(*) FROM menus WHERE destacado=1")->fetchColumn();

        $sentencia=$conexion->prepare("SELECT * FROM menus WHERE destacado=1 LIMIT :inicio, :registros");
        $sentencia->bindParam(":inicio", $inicio, PDO::PARAM_INT);
        $sentencia->bindParam(":registros", $registrosPorPagina, PDO::PARAM_INT);
        $sentencia->execute();
        $listaMenus=$sentencia->fetchAll(PDO::FETCH_ASSOC);

        //Devuelve cuántos destacados existen y calcula cuántos menús se mostrarán por página.
        $totalRegistros=$conexion->query("SELECT COUNT(*) FROM menus WHERE destacado=1")->fetchColumn();
        $totalPaginas=ceil($totalRegistros / $registrosPorPagina);

        //Verifica si existe el directorio, si no, crea uno nuevo.
        if (!is_dir(__DIR__ . "/cache")) {
            mkdir(__DIR__ . "/cache", 0777, true);
        }

        //Se guardar el estado completo en cache, incluyendo hayDestacados.
        file_put_contents($cache, json_encode([
            'listaMenus'=>$listaMenus,
            'totalPaginas'=>$totalPaginas,
            'hayDestacados'=>$hayDestacados
        ]));
    }

?>
    
    <!--Contenedor principal-->
    <main role="main" aria-label="Contenido principal de la página de inicio">

        <!--Carrusel-->
        <div class="container-fluid">
            <div class="row">
                <div class="contenedor-main p-0 col-md border border-primary border-2">
                    <div id="carrusel" class="carousel slide carousel-fade">
                        <div class="carousel-inner" role="listbox">
                            <div class="carousel-item active">
                                <img class="carruselImagen" src="img/carouselImg/carousel-img-1.jpeg" alt="Plato principal del restaurante Sabor Urbano" loading="lazy">
                                <div class="carousel-caption d-block">
                                    <h1 class="display-5"><i>Bienvenido a Sabor Urbano</i></h1>
                                    <p class="text-center">
                                    Es un placer recibirte en nuestro hogar gastronómico, donde cada plato está preparado con pasión, ingredientes frescos y el deseo de brindarte una experiencia inolvidable.
                                    </p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <img class="carruselImagen" src="img/carouselImg/carousel-img-2.jpeg" alt="Local de Sabor Urbano con comensales" loading="lazy">
                                <div class="carousel-caption d-block">
                                    <h1 class="display-5"><i>Contamos con reservas</i></h1>
                                    <p class="text-center">
                                    En Sabor Urbano te invitamos a disfrutar de nuestra cocina gourmet en un ambiente moderno, elegante y acogedor, reserva tu mesa y déjate sorprender por cada sabor.
                                    </p>
                                    <a href="reservas.php" class="btn btn-primary btn-md p-3" role="button">¡Haga su reservación!</a>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <img class="carruselImagen" src="img/carouselImg/carousel-img-3.jpg" alt="Cliente consultando a nuestros chefs" loading="lazy">
                                <div class="carousel-caption d-block">
                                    <h1 class="display-5"><i>Conozca nuestros orígenes</i></h1>
                                    <p class="text-center">
                                        En Sabor Urbano, nos enorgullece compartir la historia y la pasión que dieron vida a nuestro restaurante. Descubra cómo nuestras raíces y tradiciones culinarias han dado forma a cada plato que servimos.
                                    </p>
                                    <a href="nosotros.php" class="btn btn-primary btn-md p-3" role="button">Descubra nuestra historia</a>
                                </div>
                            </div>
                        </div>
                        <a class="carousel-control-prev" href="#carrusel" role="button" data-bs-slide="prev" aria-label="Anterior">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </a>
                        <a class="carousel-control-next" href="#carrusel" role="button" data-bs-slide="next" aria-label="Siguiente">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <article aria-label="Artículo principal contenedor">


        <!--Sección de filosofía-->
        <section id="filosofiaSec" class="mt-1 border border-primary border-2" aria-labelledby="filosofiaHeader">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-5 bg-primary p-5">
                    <h1 class="display-5" id="filosofiaHeader"><i>Sobre nuestro menú</i></h1>
                    <p class="mb-4">
                    Celebramos la belleza de lo simple. Cada plato en nuestro menú nace del respeto por los ingredientes frescos y locales, combinados con técnicas culinarias de alto nivel que elevan lo cotidiano a lo extraordinario. 
                    Nuestra propuesta es clara: comida honesta, sin pretensiones, pero con un enfoque gourmet que cuida cada detalle.<br>
                    Desde entradas ligeras hasta platos fuertes reconfortantes, cada preparación destaca por su sabor auténtico, su presentación cuidada y una ejecución que honra lo artesanal. Ideal para quienes buscan disfrutar de una experiencia culinaria sofisticada en un ambiente relajado y urbano.
                    </p>
                    </div>
                    <div class="col-md-7 position-relative p-0">
                        <img id="imgFilosofia" src="img/bgImg/presentacion.jpg" alt="Chef sirviendo uno de nuestros platos" loading="lazy">
                        <a class="btn btn-outline-light btn-md position-absolute top-50 start-50 translate-middle" href="menus.php" role="button">Ver más</a>
                    </div>
                </div>
            </div>
        </section>

        
        <!--Sección de destacados-->
        <?php if ($hayDestacados): ?>
        <section id="destacadosSec" aria-labelledby="destacadosHeader">
            <div class="container-fluid">
                <h1 class="display-5" id="destacadosHeader"><i>Platos destacados</i></h1>
                <div class="row justify-content-center">
                
                    <?php foreach ($listaMenus as $menu) {
                        if ($menu['destacado']==1) { ?>
                            <div class="col-md-3 mx-3 mb-3">
                                <div class="card bg-white border border-dark border-2 menuCard">
                                    <img src="img/menuImg/<?php echo htmlspecialchars($menu['imgMenu']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($menu['nombreMenu']); ?>" loading="lazy">
                                    <div class="card-body">
                                        <div class="text-center">
                                            <h5 class="card-title"><i><?php echo htmlspecialchars($menu['nombreMenu']); ?></i></h5>
                                            <h6 class="card-subtitle mb-2 text-muted "><?php echo htmlspecialchars($menu['descripcionMenu']); ?></h6>
                                            <h5>$<?php echo htmlspecialchars($menu['precioMenu']); ?></h5>
                                        </div>
                                    </div>
                                    <div class="btnCard">
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
                        <?php } 
                    } ?>

                    <nav aria-label="Barra de paginación de la sección de platos destacados">
                        <ul class="pagination justify-content-center">

                            <li class="page-item <?php echo ($pagina<=1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?>#destacadosSec" aria-label="Anterior">
                                    &laquo;
                                </a>
                            </li>

                            <?php for ($i=1; $i<=$totalPaginas; $i++) { ?>
                            <li class="page-item <?php echo ($pagina == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $i; ?>#destacadosSec"><?php echo $i; ?></a>
                            </li>
                            <?php } ?>

                            <li class="page-item <?php echo ($pagina>=$totalPaginas) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>#destacadosSec" aria-label="Siguiente">
                                    &raquo;
                                </a>
                            </li>

                        </ul>
                    </nav>

                </div>
            </div>
        </section>
        <?php endif; ?>
    </article>

    <!--Artículo de ubicación-->
    <article id="mapaArt" class="mt-1 border border-primary border-2" aria-label="Artículo del mapa de ubicación">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-5 bg-primary p-3 m-0">
                    <div role="complementary" aria-labelledby="horariosHeader">
                        <h2 id="horariosHeader"><i>Horarios de atención</i></h2>
                        <p class="text-center txtBlanco py-4 m-0">
                        <b>ABIERTO LOS 7 DÍAS</b>
                        <br>
                        Por la mañana de 7:00 a.m. a 14 p.m.
                        <br>
                        Por la noche de 20:00 p.m. a 01:00 a.m.
                        </p>
                        <h2><i>Ubicación</i></h2>
                        <p class="text-center txtBlanco py-4 m-0">
                        <b>ÚNICO LOCAL</b>
                        <br>
                        Ubicado en Eva Duarte de Perón 1101
                        <br>
                        </p>
                    </div>
                    <div class="d-flex justify-content-center">
                        <a href="ubicacion.php" class="btn btn-outline-light btn-md m-3 p-2" role="button">Conoce más sobre nuestra sucursal</a>
                    </div>
                </div>
                <div id="mapIndex" class="col-md-7" aria-label="Mapa de la ubicación del restaurante Sabor Urbano">
                    <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d13785.934421095475!2d-57.64247403264296!3d-30.251799215798872!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x95acd623edcc7fa3%3A0x77162afd065bbfff!2sVatel%20Rest%C3%B3Bar!5e0!3m2!1ses!2sar!4v1745433546595!5m2!1ses!2sar" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </article>

<?php include("templates/footer.php"); ?>