class FormPersonas {
   
    constructor() {
        this.form = document.getElementById('formPersonas');
    }

    validoInputsIndividual() {
        let formValido = true;
        const nombreyape = document.getElementById('nombreyape');
            if (nombreyape.value.trim() === '') {
                nombreyape.classList.add('is-invalid');
                nombreyape.classList.remove('is-valid');
                formValido = false;
                //this.cambiarEstiloInput(nombreyape, false);
            } else {
                nombreyape.classList.add('is-valid');
                nombreyape.classList.remove('is-invalid');
                //this.cambiarEstiloInput(nombreyape, true);
            }

        const correo = document.getElementById('correo');
        const correoValido = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test(correo.value.trim());
        
        if (correo.value.trim() === '' || !correoValido) {
            correo.classList.add('is-invalid');
            correo.classList.remove('is-valid');
            formValido = false;
        } else {
            correo.classList.add('is-valid');
            correo.classList.remove('is-invalid');
        }

        const documento = document.getElementById('documento');
        if (documento.value.trim() === '') {
            documento.classList.add('is-invalid');
            documento.classList.remove('is-valid');
            formValido = false;
        } else {
            documento.classList.add('is-valid');
            documento.classList.remove('is-invalid');
        }
        return formValido;
    }

    validoInputsArray(){
        this.inputs = Array.from(this.form.elements).filter(element => element.tagName === 'INPUT');
        let formValido = true;
        this.inputs.forEach(input => {
            let esValido = true;
            if (input.id === 'correo') {
                const correoValido = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test(input.value.trim());
                if (input.value.trim() === '' || !correoValido) {
                    esValido = false;
                }
            } else if (input.id === 'nombreyape') {
                if (input.value.trim() === '' || input.value.trim().length < 3) {
                    esValido = false;
                }
            } else if (input.id === 'documento') {
                if (input.value.trim() === '' || input.value.trim().length < 6) {
                    esValido = false;
                }
            } else {
                if (input.value.trim() === '') {
                    esValido = false;
                }
            }
            this.cambiarEstiloInput(input, esValido);
            if (!esValido) {
                formValido = false;
            }
        });
        return formValido;
    }


    cambiarEstiloInput(elemento, esValido) {
        if (esValido) {
            elemento.classList.add('is-valid');
            elemento.classList.remove('is-invalid');
        } else {
            elemento.classList.add('is-invalid');
            elemento.classList.remove('is-valid');
        }
    }


    init() {

        this.form.addEventListener('submit', (event) => {
            event.preventDefault();
            if ( this.validoInputsIndividual()==true ) {
                
                this.form.submit();
            }
        });
    }

}

// Inicialización automática si el formulario existe
window.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('formPersonas')) {
        new FormPersonas().init();
    }
});
