<template>
	<table class="table table-hover" v-if="respuestas.existe>0">
		<thead>
			<tr>
				<th colspan="2">Record de reuniones</th>
			</tr>

		</thead>
		<tbody>
			<tr>
				<td>DNI</td>
				<td>{{respuestas.apoderado.dni}}</td>
			</tr>
			<tr>
				<td>APELLIDOS Y NOMBRES</td>
				<td>{{respuestas.apoderado.apellidos}} {{respuestas.apoderado.nombres}}</td>
			</tr>
			<tr >
				<th colspan="2">REUNIONES</th>
			</tr>
			<tr v-for="(reunion, index) in respuestas.reuniones">
				<td class="text-capitalize">{{ index+1 }}. {{reunion.asunto}}</td>
				<td v-if="reunion.presente==1">
					<span class="text-success"><i class="bi bi-check-lg"></i>  Asistió: {{fechaFormateada(reunion.fecha)}}</span>
				</td>
				<td v-else><span class="text-danger"><i class="bi bi-exclamation-diamond"></i> Falta</span></td>
			</tr>
			<tr v-if="respuestas.reuniones.length==0">
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
		apoderado:[], reuniones:[]
	}},
	methods:{
		fechaFormateada(fecha){
			if(fecha)
				return moment(fecha).format('DD/MM/YYYY h:mm a')
		}
	}
}
</script>