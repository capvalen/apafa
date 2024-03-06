<template>
	<table class="table table-hover">
		<thead>
			<tr>
				<th>N°</th>
				<th>Datos de apoderado</th>
				<th>Alumno</th>
				<th>Relación</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="(respuesta, index) in respuestas">
			<td>{{ index+1 }}</td>
			<td class="text-capitalize">{{ respuesta.apellidos }} {{ respuesta.nombres }}</td>
			<td>{{ respuesta.nombreAlumno }}</td>
			<td>{{ respuesta.relacion }}</td>
		</tr>
		</tbody>
		
	</table>
</template>
<script>
export default{
	props:['respuestas']
}
</script>