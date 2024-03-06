<template>
	<h1>Reportes</h1>
	<div class="row">
		<div class="col-3">
			<label for="">Tipo de reporte</label>
			<select class="form-select" id="sltAños" v-model="filtro.queReporte">
				<option v-for="reporte in reportes" :value="reporte.id">{{ reporte.clase }}</option>
			</select>
		</div>
		<div class="col-3">
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
		<div class="col-2">
			<label for="">Año</label>
			<select class="form-select" id="sltAños" v-model="filtro.año">
				<option value="-1">Todos</option>
				<option v-for="año in años" :value="año">{{ año }}</option>
			</select>
		</div>
		<div class="col-3 d-grid align-content-end">
			<button class="btn btn-outline-primary" @click="buscarReporte()"><i class="bi bi-search"></i> Buscar</button>
		</div>
	</div>
	<hr>
	<div class="row">
		<div class="col">
			<tablePadresAptos :respuestas="respuestas"></tablePadresAptos>
		</div>
	</div>
	
</template>
<script>
import moment from 'moment'
import TablePadresAptos from '@/components/TablePadresAptos.vue'
export default{
	name: 'HomeReportes',
	components:{ TablePadresAptos },
	data() {
		return {
			respuestas:[], grados:[], filtro:{queReporte:1, idGrado:-1, año:moment().format('YYYY')}, años:[], reportes:[
				{id:1, clase: 'Padres aptos para votar'}
			]
		}
	},
	mounted(){
		this.cargarDatos()
	},
	methods:{
		cargarDatos(){
			let datos = new FormData()
			datos.append('pedir', 'listar')
			this.axios.post(this.servidor+'Grados.php', datos)
			.then(resp => this.grados = resp.data.grados )

			const actual = moment().format('Y')
			for(let i= actual; i>=2023; i--)
				this.años.push(i)
		},
		buscarReporte(){
			let datos = new FormData()
			datos.append('pedir', 'buscar')
			datos.append('idGrado', this.filtro.idGrado)
			datos.append('año', this.filtro.año)
			datos.append('queReporte', this.filtro.queReporte)
			this.axios.post(this.servidor+'Reportes.php', datos)
			.then(resp => this.respuestas = resp.data)
		}
	}
}
</script>