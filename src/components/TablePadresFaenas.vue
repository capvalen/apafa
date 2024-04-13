<template>
	<table class="table table-hover" v-if="respuestas.existe>0">
		<thead>
			<tr>
				<th colspan="2">Record de faenas</th>
			</tr>

		</thead>
		<tbody>
			<tr>
				<td colspan="2">DNI</td>
				<td colspan="2">{{respuestas.apoderado.dni}}</td>
			</tr>
			<tr>
				<td colspan="2">APELLIDOS Y NOMBRES</td>
				<td colspan="2">{{respuestas.apoderado.apellidos}} {{respuestas.apoderado.nombres}}</td>
			</tr>
			<tr >
				<th colspan="2">FAENAS</th>
			</tr>
			<tr>
				<th>Asunto</th>
				<th>Asistencia</th>
				<th>Fecha</th>
				<th>Inicia</th>
				<th>Salida</th>
				<th>Marcaje</th>
				<th>Horas trabajadas</th>
				<th>Horas pendientes</th>
			</tr>
			<tr v-for="(faena, index) in respuestas.faenas">
				<td class="text-capitalize">{{ index+1 }}. {{faena.asunto}}</td>
				<td v-if="faena.presente==1">
					<span class="text-success"><i class="bi bi-check-lg"></i> Asistió</span>
				</td>
				<td v-else><span class="text-danger"><i class="bi bi-exclamation-diamond"></i> Falta</span></td>
				<td v-if="faena.presente==1">{{fechaLatam(faena.fecha)}}</td><td v-else></td>
				<td v-if="faena.presente==1">{{horaLatam(faena.inicio)}}</td><td v-else></td>
				<td v-if="faena.presente==1">{{horaLatam(faena.salida)}}</td><td v-else></td>
				<td v-if="faena.presente==1">{{horaLatam(faena.ingreso)}}</td><td v-else></td>
				<td v-if="faena.presente==1">{{horasTrabajadas(faena.inicio,faena.salida,faena.ingreso)}}</td><td v-else></td>
				<td v-if="faena.presente==1">{{horasDeudas(faena.minutos)}}</td><td v-else></td>
				
			</tr>
			<tr v-if="respuestas.faenas.length==0">
			<td colspan="2">No hay registros</td></tr>
		</tbody>
	</table>
	<p v-else>No se encontraron registros del DNI <strong>{{ respuestas.dni }}</strong></p>
</template>

<script>
import moment from 'moment'
export default {
	props:['respuestas'],
	data(){ return {
		apoderado:[], faenas:[]
	}},
	methods:{
		fechaFormateada(fecha){
			if(fecha)
				return moment(fecha).format('DD/MM/YYYY h:mm a')
		},
		horasTrabajadas(inicio, termina, hora){
			// Convertir a objetos Moment
			const entrada = moment(inicio, 'HH:mm');
			const salida = moment(termina, 'HH:mm');
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
		},
		fechaLatam(fecha){
			return moment(fecha).format('DD/MM/YYYY');
		},
		horaLatam(hora){
			return moment(hora, 'HH:mm:ss').format('h:mm a')
		},
	}
}
</script>