<template>
	<div>
		<h1>Detalle de faena</h1>
		<h3>Asunto: {{ faena.asunto }} </h3>
		<h5>Fecha: {{ fechaLatam(faena.fecha) }} </h5>
		<h5>Horario: {{ horaLatam(faena.inicio) }} - {{ horaLatam(faena.salida) }} </h5>
		
		<label for="">Ingrese DNI para registrarlo</label>
		<div class="input-group mb-3">
			<input type="text" class="form-control" v-model="dniNuevo" autocomplete="off" @keypress.enter="agregarDNI()">
			<button class="btn btn-outline-secondary" type="button" @click="agregarDNI()"><i class="bi bi-plus-lg"></i> Agregar </button>
		</div>
		<hr>
		<table class="table table-hover">
			<thead>
				<th>N°</th>
				<th>DNI</th>
				<th>Apellidos y Nombres</th>
				<th>Entrada</th>
				<th>Horas trabajadas</th>
				<th>Tardanza por</th>
			</thead>
			<tbody>
				<tr v-for="(trabajador, index) in trabajadores">
					<td>{{ index+1 }}</td>
					<td>{{ trabajador.dni }}</td>
					<td>{{ trabajador.apellidos }} {{ trabajador.nombres }}</td>
					<td>{{ horaLatam(trabajador.ingreso) }}</td>
					<td>{{ horasTrabajadas(trabajador.ingreso) }}</td>
					<td>{{ horasDeudas(trabajador.minutos) }}</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
import moment from 'moment'
export default{
	data(){ return {
		faena:[], dniNuevo:'', trabajadores:[]
	}},
	mounted(){
		this.cargarDatos();
	},
	methods:{
		cargarDatos(){
			let datos = new FormData()
			datos.append('pedir', 'detalles')
			datos.append('idFaena', this.$route.params.idFaena )
			this.axios.post(this.servidor+'Faenas.php', datos)
			.then(resp => {
				this.faena = resp.data.faena
				this.trabajadores = resp.data.trabajadores
			})
		},
		agregarDNI(){
			if(this.dniNuevo !='' && this.dniNuevo.length>=8){
				let encontrado = this.trabajadores.findIndex(x=> x.dni == this.dniNuevo)
				if(encontrado == -1){

					const marcado = moment()
					const entrada = moment(this.faena.inicio, 'HH:mm');

					const debeMinutos = marcado.diff(entrada, 'minutes');


					//Agregar DNI a la lista
					let datos = new FormData()
					datos.append('pedir', 'agregarTrabajador')
					datos.append('idFaena', this.$route.params.idFaena )
					datos.append('dni', this.dniNuevo )
					datos.append('entrada', marcado.format('YYYY-MM-DD HH:mm:ss') )
					datos.append('minutos', debeMinutos )
					this.axios.post(this.servidor+'Faenas.php', datos)
					.then(resp => {
						this.cargarDatos()
						this.dniNuevo = ''
						
					})
				}else
					alertify.message('Dni ya se encontraba en la lista',10)
			}else
				alertify.error('Dni con formato incorrecto',10)
		},
		fechaLatam(fecha){
			return moment(fecha).format('DD/MM/YYYY');
		},
		horaLatam(hora){
			return moment(hora, 'HH:mm:ss').format('h:mm a')
		},
		horasTrabajadas(hora){
			// Convertir a objetos Moment
			const entrada = moment(this.faena.inicio, 'HH:mm');
			const salida = moment(this.faena.salida, 'HH:mm');
			const marcado = moment(hora, 'HH:mm');

			// Calcular diferencia entre entrada y salida
			let horasTrabajadas = salida.diff(entrada, 'hours', true);

			// Ajustar horas trabajadas si la hora marcada es posterior a la entrada
			if (marcado.isAfter(entrada)) {
				horasTrabajadas = salida.diff(marcado, 'hours', true);
			}

			// Formatear resultado
			const horasEnteras = Math.floor(horasTrabajadas);
			const minutosDecimales = (horasTrabajadas - horasEnteras) * 60;
			const minutosEnteros = Math.round(minutosDecimales);

			return `${horasEnteras} horas y  ${minutosEnteros} minutos`
		},
		horasDeudas(minutos){
			if(minutos<0){
				return 'Puntual'
			}else{
				const hours = Math.floor(minutos / 60);
				const minutes = minutos % 60;
				return `${hours} horas y ${minutes} minutos`
			}


		}
	}
}
</script>