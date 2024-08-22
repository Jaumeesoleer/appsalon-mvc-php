let paso = 1;

const cita = {
    id: '',
    nombre : '',
    fecha : '',
    hora : '',
    servicios : []
};
document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
})

function iniciarApp(){
    tabs();
    botonesPaginador();
    paginaSiguiente();
    paginaAnterior();
    mostrarSeccion();
    consultarAPI(); 
    nombreCliente();
    idCliente();
    seleccionarFecha();
    seleccionarHora();
    mostrarResumen();

}

function mostrarSeccion(){
    //ocultar seccion
    const seccionAnterior = document.querySelector('.mostrar');
    if(seccionAnterior){
        seccionAnterior.classList.remove('mostrar');
    }

    //seleccionar la seccion
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);
    seccion.classList.add('mostrar');

    //quita clase actual al tab anterior
    const tabAnterior = document.querySelector('.actual');
    if(tabAnterior){
        tabAnterior.classList.remove('actual');
    }

    //resalta el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');
}

function tabs(){
    const botones = document.querySelectorAll('.tabs button');
    botones.forEach( boton => {
        boton.addEventListener('click', function(e){
            paso = parseInt(e.target.dataset.paso);

            mostrarSeccion();
            botonesPaginador();

        })
    })
}
function botonesPaginador(){
    const paginaAnterior = document.querySelector('#anterior');
    const paginaSiguiente = document.querySelector('#siguiente');

    const ocultar = document.querySelector('.ocultar'); 
    if(ocultar){
        ocultar.classList.remove('ocultar');
    }

    if(paso == 1){
        paginaAnterior.classList.add('ocultar');
    }
    if(paso == 3){
        paginaSiguiente.classList.add('ocultar');
        mostrarResumen();
    }
    mostrarSeccion();

}

function paginaAnterior(){
    const paginaAnterior = document.querySelector('#anterior'); 
    paginaAnterior.addEventListener('click', function(){
        if(paso <= 1) return;
        
        paso--;
        botonesPaginador();
    })

}
function paginaSiguiente(){
    const paginaSiguiente = document.querySelector('#siguiente');    
    paginaSiguiente.addEventListener('click', function(){
        if(paso >= 3) return;
        paso ++;
        botonesPaginador();
    }) 
}

async function consultarAPI(){
    try {
        const url = '/api/servicios';
        const resultado = await fetch(url);
        const servicios = await resultado.json();
        mostrarServicios(servicios);
    } catch (error) {
        console.log(error)
    }
}

function mostrarServicios(servicios){
    servicios.forEach( servicio =>{
        const {id, nombre, precio} = servicio;

        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$${precio}`;

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;

        servicioDiv.onclick = function(){
            seleccionarServicio(servicio)
        };

        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDiv);
    })
}

function seleccionarServicio(servicio){
    const {id} = servicio;
    const {servicios} = cita;

    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);


    //comprobar si un servicio estaba agregado
    if(servicios.some(agregado => agregado.id === id)){
        //eliminamos
        cita.servicios = servicios.filter(agregado => agregado.id !== id)
        divServicio.classList.remove('seleccionado');

    }else{
        //agregar
        cita.servicios = [...servicios, servicio];
        divServicio.classList.add('seleccionado');

    }
}

function nombreCliente(){
    cita.nombre = document.querySelector('#nombre').value;
}
function idCliente(){
    cita.id = document.querySelector('#id').value;
}

function seleccionarFecha(){
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e){
        const dia = new Date(e.target.value).getUTCDay();

        if([6,0].includes(dia)){
            e.target.value = '';
            mostrarAlerta('Fines de semana no permitidos', 'error', '.formulario');
        }else{
            cita.fecha = e.target.value;
        }

    });
}

function seleccionarHora(){
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(e){
        const horaCita = e.target.value;
        hora = horaCita.split(":");

        if(hora[0] < 10 || hora[0] > 18){
            e.target.value = '';
            mostrarAlerta('Hora no valida', 'error', '.formulario');
        }else{
            cita.hora = horaCita;
        }
    })
}


function mostrarAlerta(mensaje, tipo, elemento){

    const alertaPrevia = document.querySelector('.alerta');
    if(alertaPrevia) alertaPrevia.remove();

    const alerta = document.createElement('DIV');

    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);

    setTimeout (() =>{
        alerta.remove();
    },3000);

}

function mostrarResumen(){
    const resumen = document.querySelector('.contenido-resumen');
    
    if(Object.values(cita).includes('') || cita.servicios.length === 0){
        mostrarAlerta('Hacen falta datos de servicios, fecha u hora', 'error', '.contenido-resumen');
        return;
    }

    const {nombre, fecha, hora, servicios} = cita;

    servicios.forEach(servicio =>{
        const {id, precio, nombre} = servicio;
        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombre;
        
        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;
        
        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
    })

    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

    const fechaFormateada = new Date(fecha);
    const mes = fechaFormateada.getMonth();
    const dia = fechaFormateada.getDate() +2;
    const año = fechaFormateada.getFullYear();

    const fechaUTC = new Date(Date.UTC(año, mes, dia));
    const opciones = {weekday: 'long', year: 'numeric', month: 'long', day:'numeric'};

    const fechaFor = fechaUTC.toLocaleDateString('es-ES', opciones);

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFor}`;

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora}`;

    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent = 'Reservar Cita';
    botonReservar.onclick = reservarCita;

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);
    resumen.appendChild(botonReservar);

    
}

async function reservarCita(){
    const {id, nombre, fecha, hora, servicios} = cita;
    const idServicios = servicios.map(servicio => servicio.id);

    const datos = new FormData();
    
    datos.append('usuarioId', id);
    datos.append('nombre', nombre);
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('servicios', idServicios);
    try{
        const url = '/api/citas';

        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });
    
        const resultado = await respuesta.json();
        console.log(resultado);
    
        if(resultado.resultado){
            Swal.fire({
                icon: "success",
                title: "Cita creada",
                text: "Tu cita fue creada con éxito!",
                //button: "OK"
              }).then(()=>{
                window.location.reload();
              });
            }
 
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Hubo un error al guardar la cita",
            //button: "OK"
          });
          console.log(error);
    }
    //Para ver que info hay se le llama asiii!!!! 😄 console.log([...datos]); 
    
}