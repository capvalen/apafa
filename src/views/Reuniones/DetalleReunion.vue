
<template>
	<h1>Reunión</h1>
	<h3>Asunto: {{ reunion.asunto }} </h3>
	<h5>Fecha: {{ fechaLatam(reunion.fecha) }} </h5>
	<h5>Grado y sección: {{ reunion.grado }} de {{queNivel(reunion.idNivel)  }} </h5>
	<p><strong>Detalles adicionales:</strong> {{ reunion.detalles }}</p>

	<div class="d-flex justify-content-between">
		<button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalAsistencias" @click="dniABuscar=''"><i class="bi bi-list-nested"></i> Registrar asistencia</button>
		<button class="btn btn-outline-info" @click="cargarDatos()" ><i class="bi bi-arrow-clockwise"></i> Actualizar lista</button>
	</div>
	<hr>
	<p>Apoderados asistentes:</p>
	<table class="table table-hover">
		<thead>
			<tr>
				<th>N°</th>
				<th>Apellidos y nombres</th>
				<th>Registro</th>
				<th>@</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="(asistente, index) in asistentes">
				<td>{{ index+1 }}</td>
				<td class="text-capitalize">{{ asistente.apellidos }} {{ asistente.nombres }}</td>
				<td>{{ asistente.registro }}</td>
				<td>
					<button class="btn btn-outline-danger btn-sm" title="Borrar registro" @click="borrar(asistente.id, index)"><i class="bi bi-eraser"></i></button>
				</td>
			</tr>
		</tbody>
	</table>

	<!-- Modal -->
<div class="modal fade" id="modalAsistencias" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Registrar asistencia</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
				<p class="fs-6 text-secondary"><small>Ojo: En caso no se ubique el DNI tiene la opción de registrarlo en una nueva ventana, luego regresar y buscar el DNI nuevamente para finalizar la asistnecia.</small></p>
        <p>Ingrese el DNI y presione <kbd @click="buscarDNI()">Enter <i class="bi bi-arrow-return-left"></i></kbd> o use el lector láser:</p>
				<input type="text" class="form-control w-100 mb-2" v-model="dniABuscar" @keyup.enter="buscarDNI()">
				<div class="alert alert-danger " v-if="noExiste" role="alert">El DNI que acaba de ingresar no existe. Puedes registrarlo <span data-bs-dismiss="modal" @click="cerrarModal()" >desde acá <i class="bi bi-box-arrow-up-right"></i></span> </div>
				<hr>
				<p>¿Indique por cuál hijo es el que viene?</p>
				<div v-if="hijos.length>1">
					<ol class="list-group list-group-numbered">
						<li v-for="(hijo, indice) in hijos" class="list-group-item list-group-item-action puntero" @click="seleccionarHijo(hijo.id, indice)">{{hijo.apellidos}} {{ hijo.nombres }}</li>
					</ol>
				</div>
      </div>
    </div>
  </div>
</div>
</template>
<script>
import moment from 'moment'
export default{
	data(){ return {
		reunion:[], dniABuscar:'', noExiste:false, asistentes:[], hijos:[]
	}},
	mounted(){
		this.cargarDatos()
	},
	methods:{
		cargarDatos(){
			let datos = new FormData()
			datos.append('pedir', 'detalles')
			datos.append('idReunion', this.$route.params.idReunion )
			this.axios.post(this.servidor+'Reuniones.php', datos)
			.then(resp => {
				this.reunion = resp.data.reunion
				this.asistentes = resp.data.asistentes
			})
		},
		fechaLatam(fecha){
			return moment(fecha).format('DD/MM/YYYY');
		},
		queNivel(nivel){
			if(nivel == '1') return 'Inicial'
			if(nivel == '2') return 'Primaria'
			if(nivel == '3') return 'Secundaria'
		},
		encontrarApoderado(id){
			const index = this.asistentes.findIndex(x=> x.id == id)
			if(index == -1) return true
			else return false
		},
		buscarDNI(){
			if(this.dniABuscar!=''){
				let datos = new FormData()
				datos.append('pedir', 'buscarDNI')
				datos.append('dni', this.dniABuscar)
				this.axios.post(this.servidor + 'Apoderado.php', datos)
				.then(res => {
					if( res.data.conteo ==0 ){
						this.noExiste=true;
						alertify.error('No existe el DNI registrado', 10)
						this.hijos = [];
					}
					if(res.data.conteo == 1 && res.data.hijos.length<=1 ){

						//si tiene 1 hijo
						if(res.data.hijos.length==1){
							let datoHijo = new FormData()
							datoHijo.append('pedir', 'matricular')
							datoHijo.append('idGrado', this.reunion.idGrado)
							datoHijo.append('idAlumno', res.data.hijos[0].id)
							datoHijo.append('año', moment().format('YYYY'))
							datoHijo.append('seccion', this.reunion.seccion)
							this.axios.post(this.servidor+'Reuniones.php', datoHijo)
						}

						this.hijos = [];
						this.asistentes.push({
							idPadre: res.data.apoderado.id,
							nombres: res.data.apoderado.nombres,
							apellidos: res.data.apoderado.apellidos,
							registro: moment().format()
						})
						
						if(this.encontrarApoderado(res.data.apoderado.id)){
							let datos = new FormData()
							datos.append('pedir', 'registrarAsistencia')
							datos.append('idPadre', res.data.apoderado.id)
							datos.append('idReunion', this.$route.params.idReunion)
							this.axios.post(this.servidor + 'Apoderado.php', datos)
							.then(resp => {
								if(resp.data.mensaje =='ok')
									alertify.success('Se agregó la asistencia', 10)
								else
									alertify.error('Hubo un error', 10)
							})
						}
						this.noExiste=false;
					}else if( res.data.hijos.length>1 ){
						this.temporal = res.data.apoderado
						this.hijos = res.data.hijos;
					}
					
				})
			}
		},
		seleccionarHijo(idAlumno, indice){
			let datos = new FormData()
			datos.append('pedir', 'matricular')
			datos.append('idGrado', this.reunion.idGrado)
			datos.append('idAlumno', idAlumno)
			datos.append('año', moment().format('YYYY'))
			datos.append('seccion', this.reunion.seccion)

			this.axios.post(this.servidor+'Reuniones.php', datos)
			
			if(this.encontrarApoderado(this.temporal.id)){
				let datos = new FormData()
				datos.append('pedir', 'registrarAsistencia')
				datos.append('idPadre', this.temporal.id)
				datos.append('idReunion', this.$route.params.idReunion)
				this.axios.post(this.servidor + 'Apoderado.php', datos)
				.then(resp => {
					if(resp.data.mensaje =='ok'){
						this.asistentes.push({
							idPadre: this.temporal.id,
							nombres: this.temporal.nombres,
							apellidos: this.temporal.apellidos,
							registro: moment().format()
						})
						alertify.success('Se agregó la asistencia', 10)
					}
					else
						alertify.error('Hubo un error', 10)
				})
			}
		},
		cerrarModal(){
			this.$router.push({ name: 'RelacionPadres', params:{dniPadre: this.dniABuscar }});
		},
		borrar(id, index){
			alertify.confirm('Eliminar apoderado', `¿Desea borrar la asistencia del Sr(a). ${this.asistentes[index].apellidos}?`, ()=>{
				let datos = new FormData()
				datos.append('pedir', 'borrar')
				datos.append('idPadre', id)
				this.axios.post(this.servidor+'Reuniones.php', datos)
				.then(resp => this.asistentes.splice(index,1) )
				alertify.success('Datos actualizados', 5);
			}, ()=>{});
		},
	}
}
</script>