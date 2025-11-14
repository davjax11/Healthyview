<?php
/**
 * Vista para "Foro" (Paciente).
 *
 * Variables disponibles:
 * $listaPublicaciones (array): Publicaciones del foro.
 * $activePage (string): 'foro'.
 * $successMessage (string|null): Mensaje de éxito.
 * $errorMessage (string|null): Mensaje de error.
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Foro Motivacional</h1>
</div>

<?php if (isset($successMessage)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
<?php endif; ?>
<?php if (isset($errorMessage)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
            <div class="card-body p-4">
                <h5 class="card-title mb-3">Nueva Publicación</h5>
                <form action="index.php?action=verForo" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título (Opcional):</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Ej: ¡Mi logro de la semana!">
                    </div>
                    <div class="mb-3">
                        <label for="contenido" class="form-label">Mensaje:</label>
                        <textarea class="form-control" id="contenido" name="contenido" rows="5" placeholder="Comparte tu progreso, una receta o un mensaje de apoyo..." required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="imagen" class="form-label">Añadir Imagen (Opcional):</label>
                        <input class="form-control form-control-sm" type="file" id="imagen" name="imagen" accept="image/jpeg, image/png">
                    </div>

                    <div class="d-grid">
                        <button type="submit" name="publicarMensaje" class="btn btn-primary">Publicar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <?php if (empty($listaPublicaciones)): ?>
            <div class="alert alert-info text-center">
                Sé el primero en publicar. ¡Comparte algo con la comunidad!
            </div>
        <?php else: ?>
            <?php foreach ($listaPublicaciones as $pub): ?>
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">

                        <?php if (!empty($pub['imagenURL'])): ?>
                            <img src="<?php echo htmlspecialchars($pub['imagenURL']); ?>" class="img-fluid rounded mb-3" alt="Imagen del foro">
                        <?php endif; ?>

                        <h5 class="card-title"><?php echo htmlspecialchars($pub['titulo'] ? $pub['titulo'] : 'Publicación'); ?></h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($pub['contenido'])); ?></p>
                    </div>
                    <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <strong><?php echo htmlspecialchars($pub['pacienteNombre']); ?></strong>
                            el <?php echo htmlspecialchars(date("d/m/Y h:i A", strtotime($pub['fechaPublicacion']))); ?>
                        </small>
                        
                        <?php 
                            // Determinar el estilo del botón: 'btn-danger' si ya reaccionó, 'btn-outline-danger' si no
                            $btnClass = ($pub['usuarioYaReacciono'] > 0) ? 'btn-danger' : 'btn-outline-danger';
                        ?>
                        <a href="index.php?action=reaccionarForo&idPublicacion=<?php echo $pub['idPublicacion']; ?>" 
                           class="btn btn-sm <?php echo $btnClass; ?>"
                           title="Reaccionar">
                            <i class="bi bi-heart-fill"></i> 
                            <?php echo htmlspecialchars($pub['totalReacciones']); ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>