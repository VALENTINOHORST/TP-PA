<?php
$escapar = static function ($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
};
$esAdmin = $rol === 'admin';
?>
<div class="row align-items-center my-4">
    <div class="col-md-8">
        <h2 class="fw-bold mb-1">Panel de Gestión de Fechas</h2>
        <p class="text-muted mb-0">
            Usuario: <strong><?= $escapar($nombre) ?></strong> &nbsp;|&nbsp;
            Rol: <span class="badge <?= $esAdmin ? 'bg-danger' : 'bg-primary' ?>"><?= $escapar(ucfirst($rol)) ?></span>
        </p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <button type="button" class="btn btn-success shadow-sm" id="btnNuevoEvento" data-bs-toggle="modal" data-bs-target="#modalEvento">
            ➕ Nueva Fecha / Evento
        </button>
    </div>
</div>

<?php if ($errorDb !== null): ?>
    <div class="alert alert-danger text-center my-3" role="alert">
        <strong>Atención:</strong> <?= $escapar($errorDb) ?>
    </div>
<?php endif; ?>

<?php if ($flash !== null): ?>
    <div class="alert alert-<?= $escapar($flash['tipo'] ?? 'info') ?> alert-dismissible fade show my-3" role="alert">
        <?= $escapar($flash['mensaje'] ?? '') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm mb-5">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-secondary">Listado de Fechas y Exámenes Registrados</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaEventos" class="table table-striped table-hover table-bordered align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th style="width: 100px;">Tipo</th>
                        <th>Título</th>
                        <th>Materia</th>
                        <th style="width: 110px;">Fecha</th>
                        <th>Descripción</th>
                        <th style="width: 150px;">Visibilidad</th>
                        <th class="text-center" style="width: 170px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($eventos as $evento): ?>
                        <?php
                        $id = (int) $evento['id'];
                        $titulo = $escapar($evento['titulo']);
                        $materia = $escapar($evento['materia']);
                        $tipo = $escapar($evento['tipo']);
                        $fechaIso = $escapar($evento['fecha_evento']);
                        $fechaFormateada = date('d/m/Y', strtotime($evento['fecha_evento']));
                        $descripcion = $escapar($evento['descripcion'] ?? '');
                        $autor = $escapar($evento['autor'] ?? '');
                        $esGlobal = (int) $evento['es_global'];

                        $claseTipo = 'bg-secondary';
                        if ($tipo === 'Final') {
                            $claseTipo = 'bg-danger';
                        } elseif ($tipo === 'Parcial') {
                            $claseTipo = 'bg-warning text-dark';
                        } elseif ($tipo === 'Examen') {
                            $claseTipo = 'bg-primary';
                        } elseif ($tipo === 'Entrega') {
                            $claseTipo = 'bg-success';
                        }

                        // El admin gestiona todo; el alumno gestiona sus propios eventos (sean públicos o privados)
                        $puedeGestionar = $esAdmin || ((int) $evento['id_usuario'] === $idUsuario);
                        ?>
                        <tr>
                            <td><span class="badge <?= $claseTipo ?>"><?= $tipo ?></span></td>
                            <td class="fw-semibold"><?= $titulo ?></td>
                            <td><?= $materia ?></td>
                            <td data-order="<?= $fechaIso ?>"><?= $escapar($fechaFormateada) ?></td>
                            <td><small class="text-muted"><?= $descripcion !== '' ? $descripcion : '<em>Sin descripción</em>' ?></small></td>
                            <td>
                                <?php if ($esGlobal === 1): ?>
                                    <span class="badge bg-success" title="Visible para todos, incluso sin iniciar sesión">🌐 Pública</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary" title="Solo visible por vos">🔒 Privada</span>
                                <?php endif; ?>
                                <small class="text-muted d-block">(por <?= $autor ?>)</small>
                            </td>
                            <td>
                                <?php if ($puedeGestionar): ?>
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-editar"
                                            data-id="<?= $id ?>"
                                            data-titulo="<?= $titulo ?>"
                                            data-materia="<?= $materia ?>"
                                            data-tipo="<?= $tipo ?>"
                                            data-fecha="<?= $fechaIso ?>"
                                            data-descripcion="<?= $descripcion ?>"
                                            data-global="<?= $esGlobal ?>"
                                            title="Editar Fecha">
                                            ✏️ Editar
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar"
                                            data-id="<?= $id ?>"
                                            data-titulo="<?= $titulo ?>"
                                            title="Eliminar Fecha">
                                            🗑️ Borrar
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center"><span class="badge bg-light text-muted border">Solo Lectura</span></div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEvento" tabindex="-1" aria-labelledby="modalEventoTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEvento" method="POST" action="index.php?opt=evento" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalEventoTitulo">Nueva Fecha / Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="eventoAccion" value="crear">
                    <input type="hidden" name="id_evento" id="eventoId" value="">

                    <div class="mb-3">
                        <label for="eventoTitulo" class="form-label fw-semibold">Título del Evento *</label>
                        <input type="text" class="form-control" id="eventoTitulo" name="titulo" placeholder="Ej: Primer Parcial" required>
                        <div class="invalid-feedback">Por favor ingrese el título del evento.</div>
                    </div>

                    <div class="mb-3">
                        <label for="eventoMateria" class="form-label fw-semibold">Materia *</label>
                        <input type="text" class="form-control" id="eventoMateria" name="materia" placeholder="Ej: Programación Avanzada" required>
                        <div class="invalid-feedback">Por favor ingrese la materia correspondiente.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="eventoTipo" class="form-label fw-semibold">Tipo de Evento *</label>
                            <select class="form-select" id="eventoTipo" name="tipo" required>
                                <option value="" disabled selected>Seleccione...</option>
                                <option value="Parcial">Parcial</option>
                                <option value="Final">Final</option>
                                <option value="Examen">Examen</option>
                                <option value="Entrega">Entrega</option>
                            </select>
                            <div class="invalid-feedback">Seleccione un tipo de evento.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="eventoFecha" class="form-label fw-semibold">Fecha *</label>
                            <input type="date" class="form-control" id="eventoFecha" name="fecha_evento" required>
                            <div class="invalid-feedback">Seleccione una fecha válida.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="eventoDescripcion" class="form-label fw-semibold">Descripción / Observaciones</label>
                        <textarea class="form-control" id="eventoDescripcion" name="descripcion" rows="3" placeholder="Detalles de temas, aula, modalidad..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="eventoGlobal" class="form-label fw-semibold">Visibilidad de la Fecha *</label>
                        <select class="form-select" id="eventoGlobal" name="es_global" required>
                            <option value="1">🌐 Pública (Visible para todos, incluso sin iniciar sesión)</option>
                            <option value="0">🔒 Privada (Solo visible por mí en este panel)</option>
                        </select>
                        <div class="form-text">
                            Las fechas <strong>Públicas</strong> aparecen en la cartelera principal para cualquier visitante. Las <strong>Privadas</strong> solo las ves vos en tu panel.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarEvento">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminarEvento" tabindex="-1" aria-labelledby="modalEliminarTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEliminarEvento" method="POST" action="index.php?opt=evento">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id_evento" id="eliminarIdEvento" value="">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="modalEliminarTitulo">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">¿Está seguro que desea eliminar la fecha seleccionada?</p>
                    <p class="fw-bold text-danger fs-5 text-center mb-0" id="eliminarTituloEvento"></p>
                    <small class="text-muted d-block text-center mt-2">Esta acción no se puede deshacer.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Sí, eliminar definitivamente</button>
                </div>
            </form>
        </div>
    </div>
</div>
