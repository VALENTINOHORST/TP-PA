/**
 * Gestión del ABM de Fechas y Eventos
 * - Integración con DataTables
 * - Validaciones en frontend con JavaScript
 * - Modales de Alta, Modificación y Baja
 */
document.addEventListener('DOMContentLoaded', () => {
    // 1. Inicialización de DataTables sobre la tabla de eventos
    if (window.jQuery && $('#tablaEventos').length) {
        $('#tablaEventos').DataTable({
            language: {
                decimal: "",
                emptyTable: "No hay fechas registradas.",
                info: "Mostrando _START_ a _END_ de _TOTAL_ fechas",
                infoEmpty: "Mostrando 0 a 0 de 0 fechas",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: "Mostrar _MENU_ registros",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                search: "Buscar:",
                zeroRecords: "No se encontraron fechas coincidentes",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                },
                aria: {
                    sortAscending: ": activar para ordenar columna ascendente",
                    sortDescending: ": activar para ordenar columna descendente"
                }
            },
            responsive: true,
            order: [[3, 'asc']], // Orden cronológico inicial por fecha
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: [6] } // Desactivar ordenamiento en la columna de Acciones
            ]
        });
    }

    // 2. Control del Modal de Alta y Modificación
    const modalEventoEl = document.getElementById('modalEvento');
    if (modalEventoEl) {
        const modalTitulo     = document.getElementById('modalEventoTitulo');
        const formEvento      = document.getElementById('formEvento');
        const inputAccion     = document.getElementById('eventoAccion');
        const inputId         = document.getElementById('eventoId');
        const inputTitulo     = document.getElementById('eventoTitulo');
        const inputMateria    = document.getElementById('eventoMateria');
        const inputTipo       = document.getElementById('eventoTipo');
        const inputFecha      = document.getElementById('eventoFecha');
        const inputDesc       = document.getElementById('eventoDescripcion');
        const checkGlobal     = document.getElementById('eventoGlobal');

        // Botón "Nuevo Evento" (Alta)
        const btnNuevo = document.getElementById('btnNuevoEvento');
        if (btnNuevo) {
            btnNuevo.addEventListener('click', () => {
                modalTitulo.textContent = 'Nueva Fecha / Evento';
                formEvento.reset();
                inputAccion.value = 'crear';
                inputId.value = '';
                if (checkGlobal) {
                    checkGlobal.checked = false;
                }
                limpiarValidaciones(formEvento);
            });
        }

        // Delegación de eventos para botones "Editar" (funciona en cualquier página de DataTables)
        document.addEventListener('click', (event) => {
            const btnEditar = event.target.closest('.btn-editar');
            if (!btnEditar) return;

            modalTitulo.textContent = 'Editar Fecha / Evento';
            formEvento.reset();
            limpiarValidaciones(formEvento);

            inputAccion.value  = 'editar';
            inputId.value      = btnEditar.dataset.id || '';
            inputTitulo.value  = btnEditar.dataset.titulo || '';
            inputMateria.value = btnEditar.dataset.materia || '';
            inputTipo.value    = btnEditar.dataset.tipo || 'Parcial';
            inputFecha.value   = btnEditar.dataset.fecha || '';
            inputDesc.value    = btnEditar.dataset.descripcion || '';

            if (checkGlobal) {
                checkGlobal.checked = (btnEditar.dataset.global === '1');
            }

            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEventoEl);
            modalInstance.show();
        });

        // Validaciones JavaScript en el envío del formulario
        formEvento.addEventListener('submit', (event) => {
            let valido = true;

            const obligatorios = [inputTitulo, inputMateria, inputTipo, inputFecha];
            obligatorios.forEach(campo => {
                if (!campo.value.trim()) {
                    campo.classList.add('is-invalid');
                    campo.classList.remove('is-valid');
                    valido = false;
                } else {
                    campo.classList.add('is-valid');
                    campo.classList.remove('is-invalid');
                }
            });

            if (!valido) {
                event.preventDefault();
                event.stopPropagation();
            }
        });

        // Validación interactiva mientras el usuario escribe
        [inputTitulo, inputMateria, inputFecha].forEach(campo => {
            campo.addEventListener('input', () => {
                if (campo.value.trim() !== '') {
                    campo.classList.add('is-valid');
                    campo.classList.remove('is-invalid');
                } else {
                    campo.classList.add('is-invalid');
                    campo.classList.remove('is-valid');
                }
            });
        });
        inputTipo.addEventListener('change', () => {
            if (inputTipo.value.trim() !== '') {
                inputTipo.classList.add('is-valid');
                inputTipo.classList.remove('is-invalid');
            }
        });
    }

    // 3. Control del Modal de Baja (Eliminación con confirmación)
    const modalEliminarEl = document.getElementById('modalEliminarEvento');
    if (modalEliminarEl) {
        const textoTituloEliminar = document.getElementById('eliminarTituloEvento');
        const inputIdEliminar     = document.getElementById('eliminarIdEvento');

        document.addEventListener('click', (event) => {
            const btnEliminar = event.target.closest('.btn-eliminar');
            if (!btnEliminar) return;

            const id     = btnEliminar.dataset.id || '';
            const titulo = btnEliminar.dataset.titulo || 'este evento';

            textoTituloEliminar.textContent = titulo;
            inputIdEliminar.value = id;

            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEliminarEl);
            modalInstance.show();
        });
    }

    function limpiarValidaciones(formulario) {
        formulario.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
            el.classList.remove('is-valid', 'is-invalid');
        });
    }
});

