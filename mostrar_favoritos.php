<?php 
    include("administrador/database/bd.php");
    include("templates/sesion.php");
    include("favoritos.php");
    include("templates/header.php"); 
?>

<article id="favoritosArt" aria-labelledby="favoritosHeader">
    <h1 class="display-5 text-center" id="favoritosHeader"><i class="fa-solid fa-star"></i> MIS FAVORITOS <i class="fa-solid fa-star"></i></h1>

    <hr>

    <div class="container-fluid">
        <div class="row justify-content-center">
        <?php if(!empty($_SESSION['FAVORITOS'])) { ?>

            <?php foreach($_SESSION['FAVORITOS'] as $indice => $MENU) { ?>

                <div class="col-md-3 mb-4">
                    <!--Card de menús favoritos-->
                    <div id="favoritosCard" class="card mt-2 h-100">
                        <div class="row g-0 h-100">
                            <!--Imagen de la card-->
                            <div class="col-4 d-flex align-items-center">
                                <img src="img/menuImg/<?php echo htmlspecialchars($MENU['IMAGENMENU']); ?>" alt="<?php echo htmlspecialchars($MENU['NOMBREMENU']); ?>">
                            </div>
                            <!--Contenido de la card-->
                            <div class="col-8 d-flex flex-column">

                                <div class="card-body flex-grow-1">
                                    <h5 class="card-title">
                                        <?php echo htmlspecialchars($MENU['NOMBREMENU']); ?> - $<?php echo htmlspecialchars($MENU['PRECIOMENU']); ?>
                                    </h5>
                                    <p class="card-text">
                                        <?php echo htmlspecialchars($MENU['DESCRIPCIONMENU']); ?>
                                    </p>
                                </div>

                                <div class="card-footer text-center mt-auto">
                                    <form method="POST">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="idMenu" value="<?php echo openssl_encrypt($MENU['IDMENU'], COD, KEY); ?>">

                                        <div class="btn-group" role="group">
                                            <button class="btn btn-danger btn-md iconBtn" name="btnAccion" value="Remover" type="submit">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            <?php } ?>

        <?php } else { ?>
            
            <div class="alert alert-danger text-center m-2" role="alert" aria-label="Alerta: no hay menús favoritos">
                <b>Agrega tus menús favoritos para verlos aquí... <a href="menus.php">Ver menús</a></b>
            </div>
            
        <?php } ?>
            
        </div>
    </div>

</article>

<?php include("templates/footer.php"); ?>