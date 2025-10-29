<?php 
    include("administrador/database/bd.php");
    include("templates/sesion.php");
    include("templates/header.php"); 
?>

<a href="#nosotrosArt" class="visually-hidden-focusable">Ir al contenido principal</a>

<main id="nosotrosArt" class="container" role="main" aria-label="Contenido principal sobre nosotros">
    <div class="container-fluid">
        <div class="row">
            <section class="intro" aria-labelledby="nosotrosHeader">
                <h1 id="nosotrosHeader">Nuestra Historia</h1>
                <p>
                Hay historias que nacen en oficinas, otras en grandes inversiones… la nuestra nació en una cocina pequeña, 
                con el sonido de una sartén chisporroteando y el aroma del pan recién horneado. 
                Así comenzó <strong>Sabor Urbano</strong> en 2010: con recetas heredadas, manos apasionadas y el deseo 
                de tres amigos de llenar la ciudad con sabores que despertaran recuerdos.
                </p>
                <p>
                Todo empezó como reuniones improvisadas después del trabajo, donde <em>Lucas</em>, <em>Martín</em> y <em>Clara</em> 
                cocinaban para amigos y vecinos. Entre risas, anécdotas y ollas humeantes, nació la idea de crear un lugar 
                donde cada plato se sintiera como un abrazo.
                </p>
                <p>
                Con el tiempo, sus mesas se convirtieron en testigos de cientos de momentos: 
                cumpleaños llenos de risas, primeras citas con nervios y brindis por nuevos comienzos.  
                La ciudad les enseñó a innovar, y ellos le devolvieron lo que mejor saben hacer: 
                comida honesta, hecha con cariño y un toque creativo que la hace única.
                </p>

                <section class="historia-gallery" aria-label="Galería histórica de Sabor Urbano">
                    <figure class="historia-img">
                        <img src="img/nosotrosImg/Cocina_SU01.jpg" alt="Nuestra cocina en los primeros años">
                        <figcaption>Primeros pasos de Sabor Urbano en 2010</figcaption>
                    </figure>
                    <figure class="historia-img">
                        <img src="img/nosotrosImg/Evento_SU02.jpg" alt="Clientes disfrutando en el local">
                        <figcaption>Primeras reuniones con clientes y amigos</figcaption>
                    </figure>
                    <figure class="historia-img">
                        <img src="img/nosotrosImg/Restaurante_Actual_SU03.jpeg" alt="Restaurante actual en 2024">
                        <figcaption>Nuestro restaurante en la actualidad</figcaption>
                    </figure>
                </section>

                <p>
                Hoy, más de una década después, seguimos con la misma energía que el primer día:  
                <strong>cocinando con pasión, cuidando cada detalle y asegurándonos de que
                cada visita sea mucho más que solo comer… sea vivir una experiencia</strong>.
                </p> 
            </section>

            <section aria-labelledby="valoresHeader">
                <h2 id="valoresHeader">Nuestros Valores</h2>
                <ul id="listValores" role="list" aria-label="Nuestros valores fundamentales">
                <li><strong>Pasión por la cocina:</strong> cada plato es preparado con dedicación.</li>
                <li><strong>Atención cálida y personalizada:</strong> tratamos a cada cliente como un invitado especial.</li>
                <li><strong>Ingredientes frescos y de calidad:</strong> seleccionamos lo mejor para cada bocado.</li>
                <li><strong>Compromiso con cada cliente:</strong> nos esforzamos para que tu experiencia sea inolvidable.</li>
                <li><strong>Innovación constante:</strong> combinamos tradición y creatividad para sorprender siempre.</li>
                </ul>
            </section>
        </div>
    </div>
</main>

<?php include("templates/footer.php"); ?>