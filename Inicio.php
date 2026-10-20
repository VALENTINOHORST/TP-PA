<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Proteger acceso: sólo usuarios autenticados
if (!isset($_SESSION['id_usuario'])) {
    header('Location: formLogin.php');
    exit;
}

require_once __DIR__ . '/includes/PageClass.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Evento.php';

$idUsuario = (int)$_SESSION['id_usuario'];
$rol       = $_SESSION['rol'] ?? 'alumno';
$nombre    = htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['usuario'], ENT_QUOTES, 'UTF-8');

// 2. Validar conexión a la Base de Datos con mensaje amigable
$db = Database::getConexion();
$alertaDb = '';
if (!$db) {
    $alertaDb = '<div class="alert alert-danger text-center my-3" role="alert">
        <strong>Atención:</strong> ' . htmlspecialchars(Database::getErrorConexion(), ENT_QUOTES, 'UTF-8') . '
    </div>';
}

// 3. Capturar mensaje flash de operaciones del ABM
$alertaFlash = '';
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    $tipo = htmlspecialchars($flash['tipo'], ENT_QUOTES, 'UTF-8');
    $mensaje = htmlspecialchars($flash['mensaje'], ENT_QUOTES, 'UTF-8');
    $alertaFlash = '<div class="alert alert-' . $tipo . ' alert-dismissible fade show my-3" role="alert">'
        . $mensaje .
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>';
    unset($_SESSION['flash']);
}

// 4. Obtener las fechas registradas
$eventos = $db ? Evento::obtenerParaUsuario($idUsuario, $rol) : [];

// 5. Construcción de filas de la tabla
$filasHtml = '';
foreach ($eventos as $e) {
    $id              = (int)$e['id'];
    $titulo          = htmlspecialchars($e['titulo'], ENT_QUOTES, 'UTF-8');
    $materia         = htmlspecialchars($e['materia'], ENT_QUOTES, 'UTF-8');
    $tipo            = htmlspecialchars($e['tipo'], ENT_QUOTES, 'UTF-8');
    $fechaIso        = htmlspecialchars($e['fecha_evento'], ENT_QUOTES, 'UTF-8');
    $fechaFormateada = date('d/m/Y', strtotime($e['fecha_evento']));
    $desc            = htmlspecialchars($e['descripcion'] ?? '', ENT_QUOTES, 'UTF-8');
    $autor           = htmlspecialchars($e['autor'] ?? '', ENT_QUOTES, 'UTF-8');
    $esGlobal        = (int)$e['es_global'];

    // Badge visual según el tipo de fecha
    $badgeTipo = 'bg-secondary';
    if ($tipo === 'Final') {
        $badgeTipo = 'bg-danger';
    } elseif ($tipo === 'Parcial') {
        $badgeTipo = 'bg-warning text-dark';
    } elseif ($tipo === 'Examen') {
        $badgeTipo = 'bg-primary';
    } elseif ($tipo === 'Entrega') {
        $badgeTipo = 'bg-success';
    }

    // Badge según el ámbito (Oficial/Global vs Personal)
    if ($esGlobal === 1) {
        $ambitoBadge = '<span class="badge bg-info text-dark" title="Visible para todos">Oficial</span>';
    } else {
        $ambitoBadge = '<span class="badge bg-secondary" title="Fecha personal">Personal</span>';
    }

    // Permisos: el admin gestiona todo; el alumno solo sus fechas personales
    $puedeGestionar = ($rol === 'admin') || ((int)$e['id_usuario'] === $idUsuario && $esGlobal === 0);

    if ($puedeGestionar) {
        $accionesHtml = '
        <div class="d-flex justify-content-center gap-1">
            <button type="button" class="btn btn-sm btn-outline-primary btn-editar"
                data-id="' . $id . '"
                data-titulo="' . $titulo . '"
                data-materia="' . $materia . '"
                data-tipo="' . $tipo . '"
                data-fecha="' . $fechaIso . '"
                data-descripcion="' . $desc . '"
                data-global="' . $esGlobal . '"
                title="Editar Fecha">
                ✏️ Editar
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar"
                data-id="' . $id . '"
                data-titulo="' . $titulo . '"
                title="Eliminar Fecha">
                🗑️ Borrar
            </button>
        </div>';
    } else {
        $accionesHtml = '<div class="text-center"><span class="badge bg-light text-muted border">Solo Lectura</span></div>';
    }

    $filasHtml .= '<tr>
        <td><span class="badge ' . $badgeTipo . '">' . $tipo . '</span></td>
        <td class="fw-semibold">' . $titulo . '</td>
        <td>' . $materia . '</td>
        <td data-order="' . $fechaIso . '">' . $fechaFormateada . '</td>
        <td><small class="text-muted">' . ($desc !== '' ? $desc : '<em>Sin descripción</em>') . '</small></td>
        <td>' . $ambitoBadge . ' <small class="text-muted">(' . $autor . ')</small></td>
        <td>' . $accionesHtml . '</td>
    </tr>';
}

// 6. Opción para admin de marcar fechas globales
$campoGlobalAdmin = '';
if ($rol === 'admin') {
    $campoGlobalAdmin = '
    <div class="form-check form-switch mb-3 p-2 bg-light border rounded">
        <input class="form-check-input ms-0 me-2" type="checkbox" id="eventoGlobal" name="es_global" value="1">
        <label class="form-check-label fw-semibold" for="eventoGlobal">
            Fecha Oficial / Global <small class="text-muted fw-normal">(visible en la cartelera pública para todos los alumnos)</small>
        </label>
    </div>';
}

// 7. Ensamble de la Vista principal
$body = '
<div class="row align-items-center my-4">
    <div class="col-md-8">
        <h2 class="fw-bold mb-1">Panel de Gestión de Fechas</h2>
        <p class="text-muted mb-0">
            Usuario: <strong>' . $nombre . '</strong> &nbsp;|&nbsp;
            Rol: <span class="badge ' . ($rol === 'admin' ? 'bg-danger' : 'bg-primary') . '">' . ucfirst($rol) . '</span>
        </p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <button type="button" class="btn btn-success shadow-sm" id="btnNuevoEvento" data-bs-toggle="modal" data-bs-target="#modalEvento">
            ➕ Nueva Fecha / Evento
        </button>
    </div>
</div>

' . $alertaDb . '
' . $alertaFlash . '

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
                        <th style="width: 150px;">Ámbito</th>
                        <th class="text-center" style="width: 170px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $filasHtml . '
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Alta y Modificación (ABM) -->
<div class="modal fade" id="modalEvento" tabindex="-1" aria-labelledby="modalEventoTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEvento" method="POST" action="controladorEvento.php" novalidate>
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

                    ' . $campoGlobalAdmin . '
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarEvento">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Baja (Eliminación) -->
<div class="modal fade" id="modalEliminarEvento" tabindex="-1" aria-labelledby="modalEliminarTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEliminarEvento" method="POST" action="controladorEvento.php">
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
';

$oPage = new PageClass();
$oPage->setBody($body);

echo $oPage->getHtml();
