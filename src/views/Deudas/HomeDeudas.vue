<template lang="">
	<h1>Panel de deudas</h1>
	<button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaDeuda"><i class="bi bi-plus-circle"></i> Crear nueva deuda</button>
	<div class="row">
		<div class="col-2">
			<label for="">Buscar deuda</label>
			<input type="text" class="form-control" placeholder="Búsqueda por asunto" v-model="texto" @keyup.enter="buscarPadre()">
		</div>
		<div class="col-2">
			<label for="">Nivel y Grado</label>
			<select class="form-select" id="sltAños">
				<option value="-1">Todos</option>
				<option v-for="grado in grados" :value="grado.id">
					<span v-if="grado.numero!=0">{{ grado.numero }}°</span>
					<span v-else>{{ grado.grado }}</span>
					 de {{grado.nivel}}
				</option>
			</select>
		</div>
		<div class="col-2">
			<label for="">Año</label>
			<select class="form-select" id="sltAños">
				<option value="-1">Todos</option>
				<option v-for="año in años" :value="año">{{ año }}</option>
			</select>
		</div>
		<div class="col-2">
			<label for="">Mes</label>
			<select class="form-select" id="sltAños">
				<option value="-1">Todos</option>
				<option v-for="mes in meses" :value="mes.id">{{ mes.mes }}</option>
			</select>
		</div>
		<div class="col-2 d-grid align-content-end">
			<button class="btn btn-outline-primary"><i class="bi bi-search"></i> Buscar</button>
		</div>
	</div>
	<hr>
	<div class="row">
		<p>Deudas registradas:</p>
		<table class="table table-hover">
			<thead>
				<tr>
					<th>N°</th>
					<th>Fecha</th>
					<th>Asunto</th>
					<th>Monto</th>
					<th>Nivel y grado</th>
					<th>@</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(deuda, index) in deudas">
					<td>{{ index+1 }}</td>
					<td>{{ fechaLatam(deuda.fecha) }}</td>
					<td>{{ deuda.motivo }}</td>
					<td>S/ {{ deuda.monto.toFixed(2) }}</td>
					<td>{{deuda.numero}}° {{ deuda.idNivel =='2' ? 'Primaria': deuda.idNivel=='3' ? 'Secundaria' : '' }}</td>
					<td>
						<button class="btn btn-outline-primary btn-sm" title="Ver pagos" @click="irAPagos(deuda.id)"><i class="bi bi-coin"></i></button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div>
		
	</div>
</template>
<script>
import moment from 'moment'
export default{
	data(){ return {
		años:[], reunion:{ asunto:'', detalles:'', fecha: moment().format('YYYY-MM-DD'), idGrado:1, seccion:''}, grados:[], deudas:[], meses: [ { "id": 1, "mes": "Enero" }, { "id": 2, "mes": "Febrero" }, { "id": 3, "mes": "Marzo" }, { "id": 4, "mes": "Abril" }, { "id": 5, "mes": "Mayo" }, { "id": 6, "mes": "Junio" }, { "id": 7, "mes": "Julio" }, { "id": 8, "mes": "Agosto" }, { "id": 9, "mes": "Septiembre" }, { "id": 10, "mes": "Octubre" }, { "id": 11, "mes": "Noviembre" }, { "id": 12, "mes": "Diciembre" } ]
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
			this.axios.post(this.servidor+'Deudas.php', datos)
			.then(resp => this.deudas = resp.data.deudas)
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
		irAPagos(id){
			this.$router.push({ name: 'DetalleDeudas', params:{idDeuda: id }});
		}
	}
}
</script>