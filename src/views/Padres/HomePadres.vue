<template>
	<h1>Panel de padres</h1>
	<router-link to="/padres/registro" class="btn btn-outline-primary mb-2" ><i class="bi bi-plus-circle"></i> Nuevo apoderado</router-link>
	<div class="col-4">
		<label for="">Buscar padre</label>
		<input type="text" class="form-control" placeholder="Búsqueda por DNI, Nombres o Celular" v-model="texto" @keyup.enter="buscarPadre()">
	</div>
	<p class="mt-4">Los 30 últimos padres registrados</p>
	<table class="table table-hover">
		<thead>
			<tr>
				<th>N°</th>
				<th>Apellidos</th>
				<th>Nombres</th>
				<th>DNI</th>
				<th>Celular</th>
				<th>@</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="(padre, index) in apoderados" :key="padre.id">
				<td>{{ index+1 }}</td>
				<td class="text-capitalize">{{ padre.apellidos }}</td>
				<td class="text-capitalize">{{ padre.nombres }}</td>
				<td>{{ padre.dni }}</td>
				<td>{{ padre.celular }}</td>
				<td>
					<div class="">
						<button class="btn btn-sm btn-outline-primary mx-1" title="Ver alumnos asignados" @click="verAlumnosAsignados(padre.dni)"><i class="bi bi-people-fill"></i></button>
						<button class="btn btn-sm btn-outline-danger mx-1" title="Borrar apoderado" @click="eliminarPadre(padre.id, index)"><i class="bi bi-eraser"></i></button>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</template>

<script>

export default {
	name: 'HomePadres',
	data(){ return {
		apoderados:[], texto:''
	}},
	methods:{
		cargarDatos(){
			let datos = new FormData()
			datos.append('pedir', 'listar30Apoderados')
			this.axios.post(this.servidor+'Apoderado.php', datos)
			.then(resp => this.apoderados = resp.data.apoderados)
		},
		buscarPadre(){
			if( this.texto=='') this.cargarDatos()
			else{
				let datos = new FormData()
				datos.append('pedir', 'buscarApoderados')
				datos.append('texto', this.texto)
				this.axios.post(this.servidor+'Apoderado.php', datos)
				.then(resp => this.apoderados = resp.data.apoderados)
			}
		},
		eliminarPadre(id, index){
			alertify.confirm('Eliminar apoderado', `¿Desea borrar al Sr(a). ${this.apoderados[index].apellidos}?`, ()=>{
				let datos = new FormData()
				datos.append('pedir', 'eliminarApoderado')
				datos.append('id', id)
				this.axios.post(this.servidor+'Apoderado.php', datos)
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