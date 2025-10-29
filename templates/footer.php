    <footer>
        <div class="container-fluid bg-primary p-3">
            <div class="row justify-content-between">
                <div class="col-md-4 text-md-start">
                    <h2 id="contactoHeader" class="text-center text-md-start"><i>CONTACTO</i></h2>
                    <div id="contactoContenido" aria-labelledby="contactoHeader">
                        <div class="d-block" aria-label="Contáctanos a través de nuestro número de teléfono (03775-464602)">
                            <a href="tel:+"><i class="fa-solid fa-square-phone"></i> 03775-464602</a>
                        </div>
                        <div class="d-block" aria-label="Contáctanos a través de nuestro correo electrónico (SaborUrbano@restaurante.com)">
                            <a href="mailto:"><i class="fa-solid fa-square-envelope"></i> SaborUrbano@restaurante.com</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md text-md-start">
                    <h2 id="enlacesNavHeader" class="text-center text-md-start"><i>NAVEGACIÓN</i></h2>
                    <div id="enlacesNavContenido" aria-labelledby="enlacesNavHeader">
                        <ul>
                            <li><a href="index.php" aria-label="Enlace a la página de inicio"><i class="fa-solid fa-house"></i> Inicio</a></li>
                            <li><a href="menus.php" aria-label="Enlace a la página de menús"><i class="fa-solid fa-pizza-slice"></i> Menús</a></li>
                            <li><a href="reservas.php" aria-label="Enlace a la página de reservas"><i class="fa-solid fa-utensils"></i> Reservas</a></li>
                            <li><a href="ubicacion.php" aria-label="Enlace a la página de ubicación"><i class="fa-solid fa-shop"></i> Sucursales</a></li>
                            <li><a href="nosotros.php" aria-label="Enlace a la página sobre nosotros"><i class="fa-solid fa-people-group"></i> Sobre nosotros</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-4 text-md-start">
                    <h2 id="redesSocialesHeader" class="text-center text-md-start"><i>REDES SOCIALES</i></h2>
                    <div id="redesSocialesContenido" aria-labelledby="redesSocialesHeader">
                        <ul>
                            <li><a href="https://facebook.com" target="_blank" aria-label="Síguenos en Facebook"><i class="fab fa-facebook"></i> Facebook</a></li>
                            <li><a href="https://instagram.com" target="_blank" aria-label="Síguenos en Instagram"><i class="fab fa-instagram"></i> Instagram</a></li>
                            <li><a href="https://x.com" target="_blank" aria-label="Síguenos en Twitter (X)"><i class="fab fa-x-twitter"></i> Twitter (X)</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row align-items-center mt-2 mb-0">
                <div class="col-md text-center" aria-label="Derechos de autor">
                    <p>&copy; 2025 Restaurante. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/b85253dc75.js" crossorigin="anonymous"></script>
    <script> 
        document.querySelectorAll('#carritoOffcanvas form, #reservasOffcanvas form, #reservasArt form').forEach(form => {
            form.addEventListener('submit', function() {
                document.getElementById('barraDeCarga').style.display = 'block';
            });
        });

        document.querySelectorAll('form').forEach(form=> {

            form.addEventListener('submit', function(event) {
                const btn=event.submitter;
                const accion=btn.value;

                if (accion==='Guardar') {
                    if (!confirm('¿Seguro que querés agregar este menú a favoritos?')) {
                        event.preventDefault();
                        return;
                    }
                }

                if (accion==='Remover') {
                    if (!confirm('¿Seguro que querés eliminar este menú de favoritos?')) {
                        event.preventDefault();
                        return;
                    }
                }

                if (accion==='Eliminar') {
                    if (!confirm('¿Seguro que querés eliminar este menú del carrito?')) {
                        event.preventDefault();
                        return;
                    }
                }

                if (accion==='Pagar') {
                    if (!confirm('¿Deseas confirmar la compra?')) {
                        event.preventDefault();
                        return;
                    }
                }

                if (accion==='Vaciar') {
                    if (!confirm('¿Seguro que querés vaciar todo el carrito?')) {
                        event.preventDefault();
                        return;
                    }
                }

                if (accion==='Reservar' || accion==='Modificar') {
                    if (!confirm('¿Querés confirmar la reserva?')) {
                        event.preventDefault();
                        return;
                    }
                }

                if (accion==='Anular') {
                    if (!confirm('¿Seguro que querés anular esta reserva?')) {
                        event.preventDefault();
                        return;
                    }
                }

                document.getElementById('barraDeCarga').style.display = 'block';
            });
        });
    </script>
</body>
</html>