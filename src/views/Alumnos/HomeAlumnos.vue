<template>
	<h1>Panel de alumnos</h1>
	<router-link to="/padres/registro" class="btn btn-outline-primary mb-2" ><i class="bi bi-plus-circle"></i> Nuevo alumno</router-link>
	<div class="col-4">
		<label for="">Buscar alumno</label>
		<input type="text" class="form-control" placeholder="Búsqueda por DNI o Nombres" v-model="texto" @keyup.enter="buscarAlumno()">
	</div>
	<p class="mt-4">Los 30 últimos alumnos registrados</p>
	<table class="table table-hover">
		<thead>
			<tr>
				<th>N°</th>
				<th>Apellidos</th>
				<th>Nombres</th>
				<th>DNI</th>
				<th>@</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="(alumno, index) in alumnos" :key="alumno.id">
				<td>{{ index+1 }}</td>
				<td>{{ alumno.apellidos }}</td>
				<td>{{ alumno.nombres }}</td>
				<td>{{ alumno.dni }}</td>	
				<td>
					<div class="">
						<button class="btn btn-sm btn-outline-primary mx-1" title="Ver alumnos asignados" @click="verAlumnosAsignados(alumno.dni)"><i class="bi bi-people-fill"></i></button>
						<button class="btn btn-sm btn-outline-danger mx-1" title="Borrar alumno" @click="eliminar(alumno.id, index)"><i class="bi bi-eraser"></i></button>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</template>

<script>

export default {
	name: 'HomeAlumnos',
	data(){ return {
		alumnos:[], texto:''
	}},
	methods:{
		cargarDatos(){
			let datos = new FormData()
			datos.append('pedir', 'listar30Alumnos')
			this.axios.post(this.servidor+'Alumno.php', datos)
			.then(resp => this.alumnos = resp.data.alumnos)
		},
		buscarAlumno(){
			if( this.texto=='') this.cargarDatos()
			else{
				let datos = new FormData()
				datos.append('pedir', 'buscarAlumno')
				datos.append('texto', this.texto)
				this.axios.post(this.servidor+'Alumno.php', datos)
				.then(resp => this.alumnos = resp.data.alumnos)
			}
		},
		eliminar(id, index){
			alertify.confirm('Eliminar alumno', `¿Desea borrar al alumno: ${this.alumnos[index].apellidos}?`, ()=>{
				let datos = new FormData()
				datos.append('pedir', 'eliminarAlumno')
				datos.append('id', id)
				this.axios.post(this.servidor+'Alumno.php', datos)
				.then(resp => this.cargarDatos() )
				alertify.success('Datos actualizados', 5);
			}, ()=>{});
		},
		verAlumnosAsignados(dni){
			this.$router.push({ name: 'RelacionPadres', params:{dniPadre: dni }}); 
		}
	},
	mounted(){
		this.cargarDatos()
	}
}
</script>