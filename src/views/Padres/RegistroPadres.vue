<template>
	<h1>Registro de padres</h1>
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
				<router-link to="/padres" class="btn btn-outline-secondary">Volver</router-link>
				<button class="btn btn-outline-success" @click="registrar()">Registrar</button>
			</div>
		</div>
		<div class="col-5">
			<p class="fw-bold">Registro de hijos</p>
			<button class="btn btn-outline-warning" @click="panelNuevoHijo()">Agregar nuevo hijo</button>

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
					<input type="text" class="form-control" autocomplete="off" v-model="hijo.dni">
					<label for="">Apellidos *</label>
					<input type="text" class="form-control" autocomplete="off" v-model="hijo.nombres">
					<label for="">Nombres *</label>
					<input type="text" class="form-control" autocomplete="off" v-model="hijo.apellidos">
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
		apoderado:{dni:'', nombres:'', apellidos:'', idRelacion:1, id:-1}, hijos:{}, hijo:{ id:-1, dni:'', nombres:'', apellidos:''}
	}},
	methods:{
		panelNuevoHijo(){
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
				}
			})
		},
		addHijo(){
			if(this.hijo.nombres !='' && this.hijo.apellidos!='' ){
				//guardar
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
				this.axios.post(this.servidor + 'Apoderado.php', datos)
				.then(res => console.log(res.data))
				
			}
		}
	}
}
</script>