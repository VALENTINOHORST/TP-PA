document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.getElementById('calendario');

    // Solo se ejecuta si el elemento existe en la vista actual
    if (!contenedor) {
        return;
    }

    fetch('api_eventos.php')
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al consultar las fechas del servidor.');
        }
        return response.json();
    })
    .then(eventos => {
        if (!eventos || eventos.length === 0) {
            contenedor.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <p class="fs-5 mb-1">📅 No hay fechas oficiales registradas actualmente.</p>
                    <small>Las fechas se mostrarán aquí una vez que sean cargadas desde el sistema.</small>
                </div>
            `;
            return;
        }

        if (typeof calendarjs !== 'undefined' && calendarjs.Timeline) {
            const { Timeline } = calendarjs;

            // Obtenemos la fecha de hoy en formato YYYY-MM-DD para inicializar la vista
            const hoy = new Date().toISOString().split('T')[0];

            Timeline(contenedor, {
                type: 'monthly', // Agrupación mensual
                value: hoy,      // Mes inicial de referencia
                data: eventos    // Fechas provenientes de MariaDB
            });
        } else {
            console.error('El componente Timeline de CalendarJS no se encuentra disponible.');
            contenedor.innerHTML = '<div class="alert alert-danger text-center">No se pudo cargar la librería del calendario.</div>';
        }
    })
    .catch(error => {
        console.error('Error cargando las fechas:', error);
        contenedor.innerHTML = '<div class="alert alert-warning text-center">No se pudieron cargar las fechas registradas.</div>';
    });
});
