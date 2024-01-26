<template>
	<div class="container-fluid">
		<div class="row">
			<section class="col-2 p-4">
				<img src="./assets/logo.png" class="img-fluid">
				<ul class="list-group list-group-flush ">
					<router-link to="/" class="list-group-item list-group-item-action">Inicio</router-link>
					<router-link to="/padres" class="list-group-item list-group-item-action">Padres</router-link>
					<li class="list-group-item list-group-item-action">Alumnos</li>
					<li class="list-group-item list-group-item-action">Reuniones</li>
					<li class="list-group-item list-group-item-action">Asistencias</li>
					<li class="list-group-item list-group-item-action">Deudas</li>
					<li class="list-group-item list-group-item-action">Pagos</li>
				</ul>
			</section>
			<section class="col mt-5">
				<router-view/>
			</section>
		</div>
	</div>


</template>