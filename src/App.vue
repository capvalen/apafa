<template>
	<div class="container-fluid">
		<div class="row">
			<section class="col-2 p-4">
				<img src="./assets/logo.jpg" class=" w-75 mx-auto d-block">
				<ul class="list-group list-group-flush ">
					<router-link to="/" class="list-group-item list-group-item-action"><i class="bi bi-house"></i> Inicio</router-link>
					<router-link to="/padres" class="list-group-item list-group-item-action"><i class="bi bi-person-circle"></i> Padres</router-link>
					<router-link to="/alumnos" class="list-group-item list-group-item-action"><i class="bi bi-file-earmark-person"></i> Alumnos</router-link>
					<router-link to="/reuniones" class="list-group-item list-group-item-action"><i class="bi bi-person-workspace"></i> Reuniones</router-link>
					<router-link to="/deudas" class="list-group-item list-group-item-action"><i class="bi bi-cash"></i> Deudas y pagos</router-link>
					
					<li class="list-group-item list-group-item-action"><i class="bi bi-chat-dots"></i> Soporte</li>
				</ul>
			</section>
			<section class="col mt-4">
				<router-view/>
			</section>
		</div>
	</div>


</template>

<style>
.ajs-message {
	background-color: #00ad2d!important;
	color: aliceblue!important;
}
.ajs-error{
	background-color: #f51e01!important;
	color: aliceblue!important;
}
.puntero{ cursor:pointer}
</style>