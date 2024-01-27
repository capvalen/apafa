<template>
	<h1>Registro de padres e hijos</h1>
	<p>Rellene los campos:</p>

	<div class="row">
		<div class="col-5">
			<p class="fw-bold">Registro de apoderado</p>
			<label for="">DNI</label>
			<input type="text" class="form-control" autocomplete="off" v-model="apoderado.dni" @blur="buscarDNI()">
			<label for="">Apellidos</label>
			<input type="text" class="form-control" autocomplete="off" v-model="apoderado.apellidos">
			<label for="">Nombres</label>
			<input type="text" class="form-control" autocomplete="off" v-model="apoderado.nombres">
			<label for="">Celular</label>
			<input type="text" class="form-control" autocomplete="off" v-model="apoderado.celular">
			<label for="">Relación</label>
			<select class="form-select" id="sltRelacion" v-model="apoderado.idRelacion">
				<option value="1" select>Padre</option>
				<option value="2">Madre</option>
				<option value="3">Familiar</option>
				<option value="4">Hermandad</option>
				<option value="5">Apoderado</option>
			</select>

			<div class="d-flex justify-content-between mt-2">
				<router-link to="/padres" class="btn btn-outline-secondary" @click="$emit('actualizarPadres')"><i class="bi bi-arrow-bar-left"></i> Volver</router-link>
				<button v-if="!editar" class="btn btn-outline-success" @click="registrar()"><i class="bi bi-floppy"></i> Registrar datos</button>
				<button v-if="editar" class="btn btn-outline-warning" @click="registrar()"><i class="bi bi-floppy"></i> Actualizar datos</button>
			</div>
		</div>
		<div class="col-5">
			<p class="fw-bold">Registro de hijos</p>
			<button class="btn btn-outline-warning" @click="panelNuevoHijo()"><i class="bi bi-plus"></i> Agregar nuevo hijo</button>
			<div class="mt-2" v-if="hijos.length>0" >
				<ol class="list-group list-group-numbered">
					<li class="list-group-item d-flex justify-content-between align-items-start" v-for="(hija, index) in hijos">
						<div class="ms-2 me-auto">
							<div class="fw-bold"> {{ hija.apellidos }} {{hija.nombres}}</div>
							<span>{{hija.dni}}</span>
						</div>
						<button class="btn btn-danger btn-sm rounded-5" @click="quitarAlumno(index)"><i class="bi bi-x"></i></button>
					</li>
				</ol>
			</div>

		</div>
	</div>

	<div class="modal fade" id="modalNuevoHijo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="exampleModalLabel">Nuevo hijo</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p>Complete los siguientes datos</p>
					<label for="">DNI</label>
					<input type="text" class="form-control" autocomplete="off" v-model="hijo.dni" @blur="buscarDNIHijo()">
					<label for="">Apellidos *</label>
					<input type="text" class="form-control" autocomplete="off" v-model="hijo.apellidos">
					<label for="">Nombres *</label>
					<input type="text" class="form-control" autocomplete="off" v-model="hijo.nombres">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal" @click="addHijo()">Agregar</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
export default{
	name: 'RegistroPadres',
	data(){ return {
		apoderado:{dni:'', nombres:'', apellidos:'', celular:'', idRelacion:1, id:-1}, hijos:[], hijo:{ id:-1, dni:'', nombres:'', apellidos:''}, editar:false
	}},
	mounted(){
		if( this.$route.params.dniPadre ){
			this.editar = true;
			this.apoderado.dni = this.$route.params.dniPadre
			this.buscarDNI();
		}
	},
	methods:{
		panelNuevoHijo(){
			this.hijo.id = -1
			this.hijo.nombres = ''
			this.hijo.apellidos = ''
			this.hijo.dni = ''
			const myModalAlternative = new bootstrap.Modal('#modalNuevoHijo')
			myModalAlternative.show()
		},
		buscarDNI(){
			let datos = new FormData()
			datos.append('pedir', 'buscarDNI')
			datos.append('dni', this.apoderado.dni)
			this.axios.post(this.servidor + 'Apoderado.php', datos)
			.then(res => {
				if( res.data.conteo>0 ){
					this.apoderado.id = res.data.apoderado.id;
					this.apoderado.nombres = res.data.apoderado.nombres
					this.apoderado.apellidos = res.data.apoderado.apellidos
					this.apoderado.celular = res.data.apoderado.celular
					this.apoderado.idRelacion = res.data.apoderado.idRelacion
					this.hijos = res.data.hijos
				}
			})
		},
		buscarDNIHijo(){
			let datos = new FormData()
			datos.append('pedir', 'buscarDNI')
			datos.append('dni', this.hijo.dni)
			this.axios.post(this.servidor + 'Alumno.php', datos)
			.then(res => {
				if( res.data.conteo>0 ){
					this.hijo.id = res.data.alumno.id
					this.hijo.nombres = res.data.alumno.nombres
					this.hijo.apellidos = res.data.alumno.apellidos
					this.hijo.dni = res.data.alumno.dni
				}
			})
		},
		addHijo(){
			if(this.hijo.nombres !='' && this.hijo.apellidos!='' ){
				this.hijos.push({
					id: this.hijo.id,
					nombres: this.hijo.nombres,
					apellidos: this.hijo.apellidos,
					dni: this.hijo.dni
				})
			}else{
				alert('faltan datos por rellenar')
			}
		},
		registrar(){
			if( this.apoderado.dni =='' )
				alert('No se puede crear un apoderado con DNI vacío')
			else if( this.apoderado.nombre =='' || this.apoderado.apellidos == '' )
				alert('Falta rellenar datos de nommbres o apellidos')
			else{
				let datos = new FormData()
				datos.append('pedir', 'crearApoderado')
				datos.append('apoderado', JSON.stringify(this.apoderado))
				datos.append('hijos', JSON.stringify(this.hijos))
				this.axios.post(this.servidor + 'Apoderado.php', datos)
				.then(res =>{
					if (res.data.idPadre){
						alertify.message('Guardado exitoso', 10);
						this.limpiar()
					}
				})
				
			}
		},
		quitarAlumno(index){
			this.hijos.splice(index, 1)
		},
		limpiar(){
			this.hijos = [];
			this.hijo.nombres = ''
			this.hijo.apellidos = ''
			this.hijo.dni = ''
			this.hijo.id = -1
			this.apoderado.id = -1
			this.apoderado.dni = ''
			this.apoderado.nombres = ''
			this.apoderado.apellidos = ''
			this.apoderado.celular = ''
			this.apoderado.idRelacion = 1
		}
	}
}
</script>