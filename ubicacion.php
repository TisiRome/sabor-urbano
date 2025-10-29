<?php 
    include("administrador/database/bd.php");
    include("templates/sesion.php");
    include("templates/header.php");
?>

<main role="main" id="ubicacionArt" aria-label="Contenido principal de la página de ubicación">
    <div class="container-fluid">
        <div class="row">

            <div id="mapUbicacion">

                <!--Botón que despliega la información del mapa en vista móvil-->
                <button class="btn btn-primary d-block d-md-none position-absolute m-3" type="button" role="button" data-bs-toggle="collapse" data-bs-target="#mapInfoMobile" aria-expanded="false" aria-controls="mapInfoMobile">Ver información</button>

                <!--Información complementaria del mapa (vista escritorio)-->
                <aside class="bg-primary text-white position-absolute h-100 d-none d-md-block" id="mapInfo" role="complementary" aria-label="Texto complementario sobre la ubicación, horarios de atención y formas de contacto del restaurante Sabor Urbano">

                    <h2 class="text-center" aria-label="Información de ubicación y horarios">DÓNDE ENCONTRARNOS</h2>
                    <p><i class="fa-solid fa-location-dot"></i><strong> Nos ubicamos en: <br> Eva Duarte de Perón 1101</strong></p>
                    <p class="mb-0"><i class="fa-solid fa-clock"></i><strong> ABIERTO LOS 7 DÍAS</strong></p>
                    <ul style="color: white;">
                        <li>Por la mañana de 7:00 a.m. a 14 p.m.</li>
                        <li>Por la noche de 20:00 p.m. a 01:00 a.m.</li>
                    </ul>

                    <h2 class="text-center" aria-label="Información de contacto">CONTÁCTANOS A TRAVÉS DE</h2>
                    <p><i class="fa-solid fa-square-phone"></i></i><strong> Teléfono: <a href="tel:+" class="text-white">03775-464602</a></strong></p>
                    <p><i class="fa-solid fa-square-envelope"></i><strong> Correo electrónico: <a href="mailto:" class="text-white">SaborUrbano@restaurante.com</a></strong></p>

                </aside>

                <!--Información complementaria del mapa (vista móviles)-->
                <div class="collapse d-md-none" id="mapInfoMobile" role="complementary" aria-label="Texto complementario sobre la ubicación, horarios de atención y formas de contacto del restaurante Sabor Urbano">
                    <aside class="bg-primary text-white p-3 rounded shadow">
                    <h2 class="text-center" aria-label="Información de ubicación y horarios">DÓNDE ENCONTRARNOS</h2>
                        <p><i class="fa-solid fa-location-dot"></i><strong> Nos ubicamos en: <br> Eva Duarte de Perón 1101</strong></p>
                        <p class="mb-0"><i class="fa-solid fa-clock"></i><strong> ABIERTO LOS 7 DÍAS</strong></p>
                        <ul style="color: white;">
                        <li>Por la mañana de 7:00 a.m. a 14 p.m.</li>
                        <li>Por la noche de 20:00 p.m. a 01:00 a.m.</li>
                        </ul>
                    <h2 class="text-center" aria-label="Información de contacto">CONTÁCTANOS A TRAVÉS DE</h2>
                        <p><i class="fa-solid fa-square-phone"></i></i><strong> Teléfono: <a href="tel:+" class="text-white">Nro:03775-464602</a></strong></p>
                        <p><i class="fa-solid fa-square-envelope"></i><strong> Correo electrónico: <a href="mailto:" class="text-white">SaborUrbano@restaurante.com</a></strong></p>
                    </aside>
                </div>

                <!--Mapa de ubicación-->
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d13785.934421095475!2d-57.64247403264296!3d-30.251799215798872!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x95acd623edcc7fa3%3A0x77162afd065bbfff!2sVatel%20Rest%C3%B3Bar!5e0!3m2!1ses!2sar!4v1745433546595!5m2!1ses!2sar" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" aria-label="Mapa de ubicación del restaurante Sabor Urbano"></iframe>

            </div>

        </div>
    </div>
</main>

<?php 
    include("templates/footer.php");
?>