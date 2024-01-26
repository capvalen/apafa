<template>
	<h1>Panel de padres</h1>
	<router-link to="/padres/registro" class="btn btn-outline-primary mb-2" >Nuevo apoderado</router-link>
	<p>Los 30 últimos padres registrados</p>
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
	</table>
</template>