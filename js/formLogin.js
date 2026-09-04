class FormLogin {

    constructor() {
        this.form = document.getElementById('formLogin');
        this.usuario = document.getElementById('usuario');
        this.contrasena = document.getElementById('contrasena');
        this.btnLogin = document.getElementById('btnLogin');
        this.mensajeDiv = document.getElementById('loginMensaje');
    }

    validarCampo(elemento) {
        const esValido = elemento.value.trim() !== '';
        if (esValido) {
            elemento.classList.add('is-valid');
            elemento.classList.remove('is-invalid');
        } else {
            elemento.classList.add('is-invalid');
            elemento.classList.remove('is-valid');
        }
        return esValido;
    }

    validarFormulario() {
        const usuarioValido = this.validarCampo(this.usuario);
        const contrasenaValida = this.validarCampo(this.contrasena);
        const formValido = usuarioValido && contrasenaValida;

        this.btnLogin.disabled = !formValido;

        return formValido;
    }

    mostrarMensaje(texto, tipo) {
        this.mensajeDiv.innerHTML = `<div class="alert alert-${tipo} text-center" role="alert">${texto}</div>`;
    }

    verificarLogin() {
        this.btnLogin.disabled = true;
        this.mensajeDiv.innerHTML = '';

        const datos = new FormData(this.form);

        fetch(this.form.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: datos
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                // Credenciales correctas: navegamos de verdad a procesoLogin.php
                this.form.submit();
            } else {
                // Credenciales incorrectas: nos quedamos en el form con el mensaje
                this.mostrarMensaje(data.mensaje, 'danger');
                this.validarFormulario();
            }
        })
        .catch(() => {
            this.mostrarMensaje('No se pudo conectar con el servidor. Probá de nuevo.', 'warning');
            this.validarFormulario();
        });
    }

    init() {
        this.usuario.addEventListener('input', () => this.validarFormulario());
        this.contrasena.addEventListener('input', () => this.validarFormulario());

        this.form.addEventListener('submit', (event) => {
            event.preventDefault();
            if (this.validarFormulario()) {
                this.verificarLogin();
            }
        });
    }

}

window.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('formLogin')) {
        new FormLogin().init();
    }
});