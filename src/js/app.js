let paso = 1

const $ = $ => {return document.querySelector($)}
const $$ = $$ => {return document.querySelectorAll($$)}

const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
}

document.addEventListener('DOMContentLoaded', () => {
    iniciarApp()
})

function iniciarApp() {
    mostrarSeccion()
    tabs()
    botonesPaginador()
    paginaSiguiente()
    paginaAnterior()
    consultarAPI()
    idCliente()
    nombreCliente()
    seleccionarFecha()
    seleccionarHora()
    mostrarResumen()
}

function tabs() {
    const botones = $$('.tabs button')

    botones.forEach(boton => {
        boton.addEventListener('click', (e) => {
            paso = parseInt(e.target.dataset.paso)
            mostrarSeccion()
            botonesPaginador()
        })
    })
}

function mostrarSeccion() {
    const seccionAnterior = $('.mostrar')
    if (seccionAnterior) {
        seccionAnterior.classList.remove('mostrar')
    }
    
    const seccion = $(`#paso-${paso}`)
    seccion.classList.add('mostrar')

    const tabAnterior = $('.actual')
    if (tabAnterior) {
        tabAnterior.classList.remove('actual')
    }

    const tab = $(`[data-paso="${paso}"]`)
    tab.classList.add('actual')
}

function botonesPaginador() {
    const paginaAnterior = $('#anterior')
    const paginaSiguiente = $('#siguiente')

    if (paso === 1) {
        paginaSiguiente.classList.remove('ocultar')
        paginaAnterior.classList.add('ocultar')
    } else if (paso === 3) {
        paginaAnterior.classList.remove('ocultar')
        paginaSiguiente.classList.add('ocultar')
        mostrarResumen()
    } else {
        paginaAnterior.classList.remove('ocultar')
        paginaSiguiente.classList.remove('ocultar')
    }

    mostrarSeccion()
}

function paginaAnterior() {
    const paginaAnterior = $('#anterior')

    paginaAnterior.addEventListener('click', () => {
        paso--
        botonesPaginador()
    })
}

function paginaSiguiente() {
    const paginaSiguiente = $('#siguiente')

    paginaSiguiente.addEventListener('click', () => {
        paso++
        botonesPaginador()
    })
}

async function consultarAPI() {
    try {
        const url = `${location.origin}/api/servicios`
        const resultado = await fetch(url)
        const servicios = await resultado.json()
        mostrarServicios(servicios)

    } catch (error) {
        console.log(error)
    }
}

function mostrarServicios(servicios) {
    servicios.forEach(servicio => {
        const { id, nombre, precio } = servicio

        const nombreServicio = document.createElement('P')
        nombreServicio.classList.add('nombre-servicio')
        nombreServicio.textContent = nombre

        const precioServicio = document.createElement('P')
        precioServicio.classList.add('precio-servicio')
        precioServicio.textContent = `$${precio}`

        const servicioDiv = document.createElement('DIV')
        servicioDiv.classList.add('servicio')
        servicioDiv.dataset.idServicio = id;
        servicioDiv.onclick = () => {
            seleccionarServicio(servicio)
        }

        servicioDiv.appendChild(nombreServicio)
        servicioDiv.appendChild(precioServicio)

        $('#servicios').appendChild(servicioDiv)
    })
}

function seleccionarServicio(servicio) {
    const { id } = servicio
    const { servicios } = cita

    const divServicio = $(`[data-id-servicio="${id}"]`)

    if (servicios.some(agregado => agregado.id === id)) {
        cita.servicios = servicios.filter(agregado => agregado.id !== id)
    } else {
        cita.servicios = [...servicios, servicio]
    }

    divServicio.classList.toggle('seleccionado')
}

function idCliente() {
    cita.id = $('#id').value
}

function nombreCliente() {
    cita.nombre = $('#nombre').value
}

function seleccionarFecha() {
    const inputFecha = $('#fecha')
    inputFecha.addEventListener('input', (e) => {
        const dia = new Date(e.target.value).getUTCDay()
        
        if ([6, 0].includes(dia)) {
            e.target.value = ''
            mostrarAlerta('error', 'Fines de semana no permitidos', '.alertas1')
        } else {
            cita.fecha = e.target.value
        }
    })
}

function seleccionarHora() {
    const inputHora = $('#hora')
    inputHora.addEventListener('input', (e) => {
        const horaCita = e.target.value
        const hora = horaCita.split(':')[0]
        if (hora < 10 || hora > 18) {
            e.target.value = ''
            mostrarAlerta('error', 'Horario laboral entre las 10 y 18 horas', '.alertas1')
        } else {
            cita.hora = e.target.value
        }
    })
}

function mostrarAlerta(tipo, mensaje, elemento, desaparece = true) {
    const alertaPrevia = $('.alerta')
    if (alertaPrevia) {
        alertaPrevia.remove()
    }

    const alerta = document.createElement('DIV')
    alerta.textContent = mensaje
    alerta.classList.add('alerta')
    alerta.classList.add(tipo)

    const divAlerta = $(elemento)
    divAlerta.appendChild(alerta)

    if (desaparece) {
        setTimeout(() => {
            alerta.remove()
        }, 3000)
    }
}

function mostrarResumen() {
    const resumen = $('.contenido-resumen')

    if (Object.values(cita).includes('') || cita.servicios.length === 0) {
        mostrarAlerta('error', 'Hacen falta datos o seleccionar servicios', '.alertas2', false)
        return
    }

    while(resumen.firstChild) {
        resumen.removeChild(resumen.firstChild)
    }
    
    //formatear el div de resumen
    const { nombre, fecha, hora, servicios } = cita

    const headingServicios = document.createElement('H3')
    headingServicios.textContent = 'Resumen de Servicios'
    resumen.appendChild(headingServicios)

    servicios.forEach(servicio => {
        const { precio, nombre } = servicio 
        const contenedorServicio = document.createElement('DIV')
        contenedorServicio.classList.add('contenedor-servicio')

        const textoServicio = document.createElement('P')
        textoServicio.textContent = nombre

        const precioServicio = document.createElement('P')
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`

        contenedorServicio.appendChild(textoServicio)
        contenedorServicio.appendChild(precioServicio)

        resumen.appendChild(contenedorServicio)
    })

    const headingCitas = document.createElement('H3')
    headingCitas.textContent = 'Resumen de Cita'
    resumen.appendChild(headingCitas)

    const nombreCliente = document.createElement('P')
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`

    //formatear fecha
    const fechaObj = new Date(fecha)
    const mes = fechaObj.getMonth()
    const dia = fechaObj.getDate() + 2
    const year = fechaObj.getFullYear()

    const fechaUTC = new Date(Date.UTC(year, mes, dia))

    const opciones = {
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    }

    const fechaFormateada = fechaUTC.toLocaleDateString('es-MX', opciones)

    const fechaCita = document.createElement('P')
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`

    const horaCita = document.createElement('P')
    horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas`

    const botonReservar = document.createElement('BUTTON')
    botonReservar.classList.add('boton')
    botonReservar.textContent = 'Reservar Cita'
    botonReservar.onclick = reservarCita

    resumen.appendChild(nombreCliente)
    resumen.appendChild(fechaCita)
    resumen.appendChild(horaCita)
    resumen.appendChild(botonReservar)
}

async function reservarCita() {
    const { id, fecha, hora, servicios } = cita

    const idServicios = servicios.map(servicio => servicio.id)

    const datos = new FormData()
    datos.append('usuarioId', id)
    datos.append('fecha', fecha)
    datos.append('hora', hora)
    datos.append('servicios', idServicios)

    try {
        const url = `${location.origin}/api/citas`
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        })

        const resultado = await respuesta.json()
        
        if (resultado.resultado) {
            Swal.fire({
                icon: "success",
                title: "Cita Creada",
                text: "Tu cita ha sido procesada correctamente",
                button: "OK",
                width: '400px'
            }).then(() => {
                setTimeout(() => {
                    window.location.reload()
                }, 1000)
            })
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Hubo un error al guardar la cita",
            button: "OK",
            width: '400px'
        })
    }
}