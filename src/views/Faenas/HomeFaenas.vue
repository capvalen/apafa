<template>
	<div>
		<h1>Faenas</h1>
		<button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFaena"><i class="bi bi-plus-circle"></i> Nueva faena</button>
	</div>

	<hr>
	<p>Faenas registradas:</p>
	<table class="table table-hover">
			<thead>
				<tr>
					<th>N°</th>
					<th>Fecha</th>
					<th>Asunto</th>
					<th>Inicia</th>
					<th>Salida</th>
					<th>@</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(faena, index) in faenas">
					<td>{{ index+1 }}</td>
					<td>{{ fechaLatam(faena.fecha) }}</td>
					<td class="text-capitalize">{{ faena.asunto }}</td>
					<td>{{ horaLatam(faena.inicio) }}</td>
					<td>{{ horaLatam(faena.salida) }}</td>					
					<td>
						<button class="btn btn-outline-primary btn-sm" title="Ir a detalles" @click="irAfaena(faena.id)"><i class="bi bi-list-ul"></i></button>
					</td>
				</tr>
			</tbody>
		</table>

	<!-- Modal -->
	<div class="modal fade" id="modalFaena" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="exampleModalLabel">Nueva faena</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p>Requerimos los datos:</p>
					<label for="">Fecha</label>
					<input type="date" class="form-control" v-model="faena.fecha">
					<div class="row">
						<div class="col-12 mt-3">
							<label for=""><small class="text-body-secondary fw-light fst-italic">(Formato 24 horas)</small></label>
						</div>
						<div class="col-6">
							<label for="">Hora de inicio </label>
						</div>
						<div class="col-6">
							<label for="">Hora de salida</label>
						</div>
						
						<div class="col-6">
							<input type="time" class="form-control" v-model="faena.inicio">
						</div>
						<div class="col-6">
							<input type="time" class="form-control" v-model="faena.salida">

						</div>
					</div>
					<label for="">Asunto</label>
					<input type="text" class="form-control" v-model="faena.asunto">
					<label for="">Detalles</label>
					<textarea name="" id="" rows="3" class="form-control" v-model="faena.detalles"></textarea>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" @click="crearFaena()" data-bs-dismiss="modal"><i class="bi bi-plus-circle"></i> Crear Faena</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import moment from 'moment'
export default{
	data(){ return{
		faena:{fecha:moment().format('YYYY-MM-DD'), asunto:'', detalles:'', inicio: moment().format('HH:00'), salida: moment().add(2, 'hour').format('HH:00')}, faenas:[]
	}},
	mounted(){
		this.cargarDatos();
	},
	methods: {		
		crearFaena(){
			let datos = new FormData()
			datos.append('pedir', 'crear')
			datos.append('faena', JSON.stringify(this.faena))
			this.axios.post(this.servidor+'Faenas.php', datos)
			.then(resp => this.cargarDatos() )
		},
		cargarDatos(){
			let datos = new FormData()
			datos.append('pedir', 'listar')
			this.axios.post(this.servidor+'Faenas.php', datos)
			.then(resp => this.faenas = resp.data.faenas)
		},

		fechaLatam(fecha){
			return moment(fecha).format('DD/MM/YYYY');
		},
		horaLatam(hora){
			return moment(hora, 'HH:mm:ss').format('h:mm a')
		},
		irAfaena(id){
			this.$router.push({ name: 'DetalleFaena', params:{idFaena: id }});
		}
	},
}
</script>