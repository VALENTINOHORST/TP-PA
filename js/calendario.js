document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.getElementById('calendario');
    if (!contenedor) return;

    fetch('index.php?opt=api_eventos')
    .then(response => {
        if (!response.ok) throw new Error('Error al consultar eventos.');
        return response.json();
    })
    .then(eventos => {
        let fechaActual = new Date();

        const nombresMeses = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
          'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        const nombresDias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

        const obtenerClaseBadge = (subtitulo) => {
            const texto = (subtitulo || '').toLowerCase();
            if (texto.includes('final')) return 'gcal-tipo-final';
            if (texto.includes('parcial')) return 'gcal-tipo-parcial';
            if (texto.includes('entrega')) return 'gcal-tipo-entrega';
            if (texto.includes('examen')) return 'gcal-tipo-examen';
            return 'gcal-tipo-default';
        };

        const renderizarCalendario = () => {
            contenedor.innerHTML = '';

            const anio = fechaActual.getFullYear();
            const mes = fechaActual.getMonth();

            // Primer y último día del mes
            const primerDiaMes = new Date(anio, mes, 1);
            const ultimoDiaMes = new Date(anio, mes + 1, 0);

            // Días del mes anterior para completar la primera semana
            const diaSemanaInicio = primerDiaMes.getDay(); // 0 = Domingo
            const ultimoDiaMesAnterior = new Date(anio, mes, 0).getDate();

            // Total de días del mes actual
            const totalDiasMes = ultimoDiaMes.getDate();

            // Estructura principal
            const wrapper = document.createElement('div');
            wrapper.className = 'gcal-container';

            // 1. Encabezado con título y botones de mes
            const header = document.createElement('div');
            header.className = 'gcal-header';
            header.innerHTML = `
            <div class="d-flex align-items-center gap-3">
            <h3 class="gcal-title">${nombresMeses[mes]} ${anio}</h3>
            <button class="btn btn-sm btn-outline-secondary" id="btnGcalHoy">Hoy</button>
            </div>
            <div class="btn-group">
            <button class="btn btn-outline-secondary btn-sm" id="btnGcalAnt" title="Mes anterior">‹</button>
            <button class="btn btn-outline-secondary btn-sm" id="btnGcalSig" title="Mes siguiente">›</button>
            </div>
            `;
            wrapper.appendChild(header);

            // 2. Fila con los nombres de la semana
            const weekdaysRow = document.createElement('div');
            weekdaysRow.className = 'gcal-weekdays';
            weekdaysRow.innerHTML = nombresDias.map(dia => `<div>${dia}</div>`).join('');
            wrapper.appendChild(weekdaysRow);

            // 3. Grilla de días
            const grid = document.createElement('div');
            grid.className = 'gcal-grid';

            const hoy = new Date();
            const hoyIso = `${hoy.getFullYear()}-${String(hoy.getMonth() + 1).padStart(2, '0')}-${String(hoy.getDate()).padStart(2, '0')}`;

            // Celdas del mes anterior
            for (let i = diaSemanaInicio - 1; i >= 0; i--) {
                const diaNum = ultimoDiaMesAnterior - i;
                grid.innerHTML += `
                <div class="gcal-day fuera-mes">
                <span class="gcal-day-num">${diaNum}</span>
                </div>
                `;
            }

            // Celdas del mes actual
            for (let d = 1; d <= totalDiasMes; d++) {
                const mesFormateado = String(mes + 1).padStart(2, '0');
                const diaFormateado = String(d).padStart(2, '0');
                const fechaIso = `${anio}-${mesFormateado}-${diaFormateado}`;
                const esHoy = fechaIso === hoyIso;

                // Filtrar eventos del día
                const eventosDia = (eventos || []).filter(ev => {
                    const f = (ev.date || ev.startDate || '').split(' ')[0];
                    return f === fechaIso;
                });

                let htmlEventos = '';
                eventosDia.forEach(ev => {
                    const titulo = ev.name || ev.title || 'Evento';
                    const subtitulo = ev.content || ev.subtitle || '';
                    const claseColor = obtenerClaseBadge(subtitulo);

                    htmlEventos += `
                    <div class="gcal-event-badge ${claseColor}" title="${titulo} - ${subtitulo}">
                    <strong>${titulo}</strong>
                    </div>
                    `;
                });

                grid.innerHTML += `
                <div class="gcal-day ${esHoy ? 'es-hoy' : ''}" data-fecha="${fechaIso}">
                <span class="gcal-day-num">${d}</span>
                <div class="gcal-events">${htmlEventos}</div>
                </div>
                `;
            }

            // Celdas del mes siguiente para cerrar cuadrícula (múltiplo de 7)
            const celdasHastaAhora = diaSemanaInicio + totalDiasMes;
            const celdasRestantes = (7 - (celdasHastaAhora % 7)) % 7;
            for (let j = 1; j <= celdasRestantes; j++) {
                grid.innerHTML += `
                <div class="gcal-day fuera-mes">
                <span class="gcal-day-num">${j}</span>
                </div>
                `;
            }

            wrapper.appendChild(grid);
            contenedor.appendChild(wrapper);

            // Listeners de los botones de navegación
            document.getElementById('btnGcalAnt').addEventListener('click', () => {
                fechaActual.setMonth(fechaActual.getMonth() - 1);
                renderizarCalendario();
            });

            document.getElementById('btnGcalSig').addEventListener('click', () => {
                fechaActual.setMonth(fechaActual.getMonth() + 1);
                renderizarCalendario();
            });

            document.getElementById('btnGcalHoy').addEventListener('click', () => {
                fechaActual = new Date();
                renderizarCalendario();
            });
        };

        renderizarCalendario();
    })
    .catch(error => {
        console.error('Error cargando eventos:', error);
        contenedor.innerHTML = '<div class="alert alert-warning text-center">No se pudieron cargar las fechas registradas.</div>';
    });
});
