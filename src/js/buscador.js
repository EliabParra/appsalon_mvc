const $ = $ => {return document.querySelector($)}
const $$ = $$ => {return document.querySelectorAll($$)}

document.addEventListener('DOMContentLoaded', () => {
    iniciarApp()
})

function iniciarApp() {
    buscarPorFecha()
}

function buscarPorFecha() {
    const fechaInput = $('#fecha')
    fechaInput.addEventListener('input', (e) => {
        const fechaSeleccionada = e.target.value

        window.location = `?fecha=${fechaSeleccionada}`
    })
}