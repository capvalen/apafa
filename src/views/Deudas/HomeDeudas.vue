<template lang="">
	<h1>Panel de deudas</h1>
	<button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaDeuda"><i class="bi bi-plus-circle"></i> Crear nueva deuda</button>
	<div class="row row-cols-5">
		<div class="col">
			<label for="">Buscar deuda</label>
			<input type="text" class="form-control" placeholder="Búsqueda por asunto" v-model="filtro.texto" @keyup.enter="buscarDeudas()">
		</div>
		<div class="col">
			<label for="">Nivel y Grado</label>
			<select class="form-select" id="sltGrados" v-model="filtro.idGrado">
				<option value="-1">Todos</option>
				<option v-for="grado in grados" :value="grado.idGrado">
					<span v-if="grado.numero!=0">{{ grado.numero }}°</span>
					<span v-else>{{ grado.grado }}</span>
					 {{grado.nivel}}
				</option>
			</select>
		</div>
		<div class="col">
			<label for="">Año</label>
			<select class="form-select" id="sltAños" v-model="filtro.año">
				<option value="-1">Todos</option>
				<option v-for="año in años" :value="año">{{ año }}</option>
			</select>
		</div>
		<div class="col">
			<label for="">Mes</label>
			<select class="form-select" id="sltMeses" v-model="filtro.mes">
				<option value="-1">Todos</option>
				<option v-for="mes in meses" :value="mes.id">{{ mes.mes }}</option>
			</select>
		</div>
		<div class="col d-grid align-content-end">
			<button class="btn btn-outline-primary" @click="buscarDeudas()"><i class="bi bi-search"></i> Buscar</button>
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
					<td v-if="deuda.numero!=0">{{deuda.numero}}° {{ deuda.idNivel =='2' ? 'Primaria': deuda.idNivel=='3' ? 'Secundaria' : '' }}</td>
					<td v-else>
						<span v-if="deuda.grado =='General'">General para todos</span>
						<span v-else>{{grado.numero}} {{grado.nivel}}</span>
					</td>
					<td>
						<button class="btn btn-outline-primary btn-sm" title="Ver pagos" @click="irAPagos(deuda.id)"><i class="bi bi-coin"></i></button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div>
		
	</div>
	<div class="modal fade" id="modalNuevaDeuda" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" >Nueva deuda</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p>Rellene los campos para crear una nueva deuda</p>
					<label for="">Motivo</label>
					<input type="text" class="form-control" v-model="deuda.motivo">
					<label for="">Detalle</label>
					<textarea class="form-control" id="sltArea" cols="3" rows="3" v-model="deuda.detalle">
					</textarea>
					<label for="">Grado</label>
					<select class="form-select" id="sltGrado2" v-if="grados" v-model="deuda.idGrado">
						<option v-for="grado in grados" :value="grado.idGrado">
							<span v-if="grado.numero!=0">{{ grado.numero }}°</span>
							<span v-else>{{ grado.grado }}</span>
							de {{grado.nivel}}
						</option>
					</select>
					<label for="">Monto</label>
					<input type="number" class="form-control" v-model="deuda.monto" min=0 step=1>
					<label for="">Fecha</label>
					<input type="date" class="form-control" v-model="deuda.fecha">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal" @click="crear()"><i class="bi bi-floppy"></i> Crear</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import moment from 'moment'
export default{
	data(){ return {
		años:[], grados:[], deudas:[], meses: [ { "id": 1, "mes": "Enero" }, { "id": 2, "mes": "Febrero" }, { "id": 3, "mes": "Marzo" }, { "id": 4, "mes": "Abril" }, { "id": 5, "mes": "Mayo" }, { "id": 6, "mes": "Junio" }, { "id": 7, "mes": "Julio" }, { "id": 8, "mes": "Agosto" }, { "id": 9, "mes": "Septiembre" }, { "id": 10, "mes": "Octubre" }, { "id": 11, "mes": "Noviembre" }, { "id": 12, "mes": "Diciembre" } ], deuda:{motivo:'', detalle:'', idGrado:18, monto:0, fecha: moment().format('YYYY-MM-DD') }, filtro:{mes:-1, año:-1, idGrado:-1, texto:''}
	}},
	mounted(){
		this.cargarDatos()
		this.pedirGrados()
		const actual = moment().format('Y')
		for(let i= actual; i>=2023; i--)
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
		fechaLatam(fecha){
			return moment(fecha).format('DD/MM/YYYY');
		},
		irAPagos(id){
			this.$router.push({ name: 'DetalleDeudas', params:{idDeuda: id }});
		},
		nombreGrado(grado){
			if(grado.numero == 0){
				return grado.grado;
			}else
			return grado.numero + ' '+grado.nivel
		},
		crear(){
			if(this.deuda.motivo == '') alertify.error('<i class="bi bi-bug"></i> Debe rellenarse el motivo de la deuda', 5)
			else if(this.deuda.monto == 0) alertify.error('<i class="bi bi-bug"></i> El monto no puede ser 0', 5)
			else if(!this.deuda.fecha ) alertify.error('<i class="bi bi-bug"></i> Seleccione una fecha correcta', 5)
			else{
				let datos = new FormData()
				datos.append('pedir', 'crear')
				datos.append('deuda', JSON.stringify(this.deuda))
				this.axios.post(this.servidor+'Deudas.php', datos)
				.then(resp =>{
					alertify.message('<i class="bi bi-check"></i> Guardado correcto', 5)
					this.cargarDatos()
				})
			}
		},
		buscarDeudas(){
			let datos = new FormData()
			datos.append('pedir', 'buscar')
			datos.append('texto', this.filtro.texto)
			datos.append('año', this.filtro.año)
			datos.append('mes', this.filtro.mes)
			datos.append('idGrado', this.filtro.idGrado)
			this.axios.post(this.servidor+'Deudas.php', datos)
			.then(resp => this.deudas = resp.data.deudas)
		}
	}
}
</script>