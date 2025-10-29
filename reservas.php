<?php 
    include("administrador/database/bd.php");
    include("templates/sesion.php");
    include("procesar_reservas.php");
    include("templates/header.php");

    $sentenciaSQL=$conexion->prepare("SELECT * FROM reservas");
    $sentenciaSQL->execute();
    $listaReservas=$sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);

?>

<!--Imagen de fondo de la página-->
<div id="bgReservas"></div>

<?php if(!empty($listaReservas)) { ?>

    <!--Botón que muestra el listado de reservas (vista escritorio)-->
    <button id="btnDorado" class="btn btn-warning d-none d-md-block" type="button" data-bs-toggle="offcanvas" data-bs-target="#reservasOffcanvas">
        <i class="fa-solid fa-utensils"></i> MIS RESERVAS
    </button>

    <!--Botón que muestra el listado de reservas (vista móviles)-->
    <button id="btnReservasMovil" class="btn btn-info d-block d-md-none m-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#reservasOffcanvas">
        <i class="fa-solid fa-utensils"></i>
    </button>
<?php } ?>

<main id="reservasArt" role="main">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <!--Formulario principal de reservas-->
                <form method="POST" action="reservas.php" aria-label="Formulario de reservas">
                    <h4 class="text-center"><b>Realice una reservación</b></h4>
                    <hr>

                    <input type="hidden" name="txtIDReserva" value="<?php echo htmlspecialchars(openssl_encrypt($idReserva, COD, KEY)); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="form-group">
                        <label for="nombre">Nombre completo</label>
                        <input type="text" id="nombre" name="txtNombreCliente" class="form-control" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ0-9\s]{3,50}" title="Solo letras y espacios, mínimo 3 caracteres." oninvalid="this.setCustomValidity('Por favor, ingresá tu nombre completo')" oninput="this.setCustomValidity('')" value="<?php echo htmlspecialchars($nombreCompleto ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="sucursal">Sucursal</label>
                        <select class="form-select" id="sucursal" name="txtSucursal" required title="Selecciona una sucursal.">
                            <option value="" disabled <?php echo empty($sucursal) ? 'selected' : ''; ?>>Seleccione una sucursal</option>
                            <option value="Sucursal principal" <?php echo ($sucursal ?? '')==='Sucursal principal' ? 'selected' : ''; ?>>
                                Sucursal principal - Eva Duarte de Perón 1101
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="cantidad">Cantidad de personas</label>
                        <select class="form-select" id="cantidad" name="txtCantidad" required title="Seleccione una cantidad de personas." min="1" max="4">
                            <option value="" disabled <?php echo empty($cantidadPersonas) ? 'selected' : ''; ?>>Seleccione cantidad</option>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo ($cantidadPersonas ?? 0)==$i ? 'selected' : ''; ?>>
                                    <?php echo $i; ?> persona<?php echo $i > 1 ? 's' : ''; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group row">
                        <label for="fecha">Fecha y hora</label>

                        <div class="col-md-6">
                            <input type="date" id="fecha" name="txtFechaReserva" class="form-control" required title="Seleccione una fecha para la reserva." min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d', strtotime('+3 months')); ?>" oninvalid="this.setCustomValidity('Seleccioná una fecha válida para tu reserva')" oninput="this.setCustomValidity('')" value="<?php echo htmlspecialchars($fecha ?? ''); ?>">
                        </div>

                        <div class="col-md-6">
                            <select class="form-select" id="horario" name="txtHoraReserva" required title="Seleccione un horario para la reserva.">
                                <option value="" disabled <?php echo empty($hora) ? 'selected' : ''; ?>>Seleccione un horario</option>
                                <?php 
                                $horarios=['8:00', '10:00', '12:00', '21:00', '23:00'];
                                foreach ($horarios as $h): ?>
                                    <option value="<?php echo $h; ?>" <?php echo ($hora ?? '')===$h ? 'selected' : ''; ?>>
                                        <?php echo $h; ?> hs
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="correo">Correo electrónico</label>
                        <input type="email" id="correo" name="txtCorreo" class="form-control" required title="Ingrese un correo, por ejemplo: 'nombre@correo.com'." oninvalid="this.setCustomValidity('Ingresá un correo válido, por ejemplo: nombre@correo.com')" oninput="this.setCustomValidity('')" value="<?php echo htmlspecialchars($correoElectronico ?? ''); ?>">
                    </div>

                    <!--Grupo de botones de acción (Reservar, modificar reserva y cancelar (volver atrás)-->
                    <div class="d-flex flex-column flex-md-row justify-content-center gap-2">
                        <button type="submit" name="btnAccion" value="Reservar" class="btn btn-info btn-md w-100 w-md-auto">Reservar</button>
                        <?php if (!empty($nombreCompleto)): ?>
                            <button type="submit" name="btnAccion" value="Modificar" class="btn btn-success btn-md w-100 w-md-auto">Modificar</button>
                            <button type="submit" name="btnAccion" value="Cancelar" class="btn btn-danger btn-md w-100 w-md-auto">Cancelar</button>
                        <?php endif; ?>
                    </div>

                    <!--Mensaje de alerta por errores en el formulario-->
                    <?php if (!empty($_SESSION['MENSAJE'])): ?>
                        <div class="alert alert-success m-1" role="alert">
                            <?php echo $_SESSION['MENSAJE']; ?>
                        </div>
                        <?php unset($_SESSION['MENSAJE']); ?>
                    <?php endif; ?>
                </form>
                
            </div>
        </div>
    </div>
</main>

<?php include("templates/footer.php"); ?>
