<template>
	<h1>Panel de reuniones</h1>
	<button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaReunion"><i class="bi bi-plus-circle"></i> Nueva reunión</button>
	<div class="row">
		<div class="col-4">
			<label for="">Ubicar reunión</label>
			<input type="text" class="form-control" placeholder="Búsqueda por asunto de reunión" v-model="texto" @keyup.enter="buscarPadre()">
		</div>
		<div class="col-3">
			<label for="">Año</label>
			<select class="form-select" id="sltAños">
				<option v-for="año in años" :value="año">{{ año }}</option>
			</select>
		</div>
		<div class="col-3 d-grid align-content-end">
			<button class="btn btn-outline-primary"><i class="bi bi-search"></i> Buscar</button>
		</div>
	</div>
	<hr>
	<div class="row">
		<p>Reuniones registradas:</p>
		<table class="table table-hover">
			<thead>
				<tr>
					<th>N°</th>
					<th>Fecha</th>
					<th>Asunto</th>
					<th>@</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(reunion, index) in reuniones">
					<td>{{ index+1 }}</td>
					<td>{{ fechaLatam(reunion.fecha) }}</td>
					<td>{{ reunion.asunto }}</td>
					<td>
						<button class="btn btn-outline-primary btn-sm" title="Ver asistentes" @click="irAReunion(reunion.id)"><i class="bi bi-list-ul"></i></button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="modalNuevaReunion" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="exampleModalLabel">Nueva reunión</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p>Requerimos los datos:</p>
					<label for="">Fecha</label>
					<input type="date" class="form-control" v-model="reunion.fecha">
					<label for="">Asunto</label>
					<input type="text" class="form-control" v-model="reunion.asunto">
					<label for="">Detalles</label>
					<textarea name="" id="" rows="3" class="form-control" v-model="reunion.detalles"></textarea>
					<label for="">Nivel</label>
					<select class="form-select" id="sltGrados" v-model="reunion.idGrado">
						<option v-for="grado in grados" :value="grado.id">{{ grado.grado }} de {{ grado.nivel }}</option>
					</select>
					<label for="">Seccion</label>
					<input type="text" class="form-control text-capitalize" v-model="reunion.seccion">

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" @click="crearReunion()" data-bs-dismiss="modal"><i class="bi bi-plus-circle"></i> Crear reunión</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import moment from 'moment'
export default{
	data(){ return {
		años:[], reunion:{ asunto:'', detalles:'', fecha: moment().format('YYYY-MM-DD'), idGrado:1, seccion:''}, grados:[], reuniones:[]
	}},
	mounted(){
		this.cargarDatos()
		this.pedirGrados()
		const actual = moment().format('Y')
		console.log(actual)
		for(let i= actual; i>=2024; i--)
			this.años.push(i)
	},
	methods:{
		pedirGrados(){
			let datos = new FormData()
			datos.append('pedir', 'listar')
			this.axios.post(this.servidor+'Grados.php', datos)
			.then(resp => this.grados = resp.data.grados )
		},
		cargarDatos(){
			let datos = new FormData()
			datos.append('pedir', 'listar')
			this.axios.post(this.servidor+'Reuniones.php', datos)
			.then(resp => this.reuniones = resp.data.reuniones)
		},
		crearReunion(){
			let datos = new FormData()
			datos.append('pedir', 'crear')
			datos.append('reunion', JSON.stringify(this.reunion))
			this.axios.post(this.servidor+'Reuniones.php', datos)
			.then(resp => this.cargarDatos() )
		},
		fechaLatam(fecha){
			return moment(fecha).format('DD/MM/YYYY');
		},
		irAReunion(id){
			this.$router.push({ name: 'DetalleReunion', params:{idReunion: id }});
		}
	}
}
</script>