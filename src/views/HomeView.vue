<template>
  <div class="home">
    <!-- <img alt="Vue logo" src="../assets/logo.png"> -->
		<h1>Sistema de APAFA</h1>
		<p>Bienvenido, aquí tiene unas cifras relacionadas con este año:</p>

		<div class="row row-cols-2 row-cols-md-4">
			<div class="col">
				<div class="card my-2">
					<div class="card-body text-center">
						<h2>{{respuestas.alumnosConPadres}}</h2>
						<span>Alumnos con padres</span>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="card my-2">
					<div class="card-body text-center">
						<h2>{{respuestas.padresTotal}}</h2>
						<span>Padres registrados</span>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="card my-2">
					<div class="card-body text-center">
						<h2>{{respuestas.faenasTotal}}</h2>
						<span>Faenas activas</span>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="card my-2">
					<div class="card-body text-center">
						<h2>{{respuestas.deudasTotal}}</h2>
						<span>Recaudaciones activas</span>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="card my-2">
					<div class="card-body text-center">
						<h2>{{respuestas.reunionesTotal}}</h2>
						<span>Reuniones activas</span>
					</div>
				</div>
			</div>
			
		</div>
  </div>
</template>

<script>
export default {
  name: 'HomeView',
	data(){ return {
		respuestas:[]
	}},
	mounted(){
		this.cargarDatos();
	},
  methods:{
		cargarDatos(){
			let datos = new FormData()
			datos.append('queReporte', 4)
			this.axios.post(this.servidor+'Reportes.php', datos)
			.then(resp => this.respuestas = resp.data )
		}
	}
}
</script>
