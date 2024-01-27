
<template>
	<h1>Deudas y pagos</h1>
	<h3>Asunto: {{ deuda.motivo }} </h3>
	<div class="row row-cols-2">
		<div class="col">
			<h5>Monto a recaudar: S/ {{ parseFloat(deuda.monto).toFixed(2) }} </h5>
			<p><strong>Detalles adicionales:</strong> <br><span v-html="textoDivido(deuda.detalle)"></span></p>
		</div>
		<div class="col">
			<h5>Fecha: {{ fechaLatam(deuda.fecha) }} </h5>
			<h5>Grado y sección: {{ deuda.grado }} de {{queNivel(deuda.idNivel)  }} </h5>
		</div>
	</div>

	<div class="d-flex justify-content-between">
		<button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalAddPago" @click="dniABuscar=''"><i class="bi bi-plus-circle"></i> Agregar pago de apoderado</button>
		<button class="btn btn-outline-info" @click="cargarDatos()" ><i class="bi bi-arrow-clockwise"></i> Actualizar lista</button>
	</div>
	<hr>
	<p>Aportes de apoderados:</p>
	<table class="table table-hover">
		<thead>
			<tr>
				<th>N°</th>
				<th>Apellidos y nombres</th>
				<th>Fecha</th>
				<th>Monto</th>
				<th>Obs</th>
				<th>@</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="(asistente, index) in asistentes">
				<td>{{ index+1 }}</td>
				<td class="text-capitalize">{{ asistente.apellidos }} {{ asistente.nombres }}</td>
				<td>{{ fechaLatam(asistente.fecha) }}</td>
				<td>S/ {{ asistente.monto.toFixed(2) }}</td>
				<td>{{ asistente.observacion }}</td>
				<td>
					<button class="btn btn-outline-primary btn-sm mx-1" title="Imprimir ticket" @click="imprimir(index)"><i class="bi bi-file-earmark-break"></i></button>
					<button class="btn btn-outline-danger btn-sm mx-1" title="Borrar registro" @click="borrar(asistente.idPago, index)"><i class="bi bi-eraser"></i></button>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td class="text-end" colspan="3">Recaudación Total</td>
				<td>S/ {{ sumaMontos }}</td>
			</tr>
		</tfoot>
	</table>

	<!-- Modal -->
<div class="modal fade" id="modalAddPago" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Registrar nuevo pago</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
				<p>Ingrese el DNI o Nombres del apoderado y presione <kbd @click="buscarDNI()">Enter <i class="bi bi-arrow-return-left"></i></kbd> o use el lector láser:</p>
				<input type="text" class="form-control w-100 mb-2" v-model="dniABuscar" @keyup.enter="buscarDNI()">
				<div class="alert alert-danger " v-if="noExiste" role="alert">El DNI que acaba de ingresar no existe. Puedes registrarlo <span data-bs-dismiss="modal" @click="cerrarModal()" >desde acá <i class="bi bi-box-arrow-up-right"></i></span> </div>
				<hr>
				<p class="mb-0">Seleccione el apoderado</p>
				<ul class="list-group">
					<li class="list-group-item" v-for="(apoderado, indice) in apoderados" >
						<input class="form-check-input me-1" type="radio" name="listApoderados" value="" :id="'radApoderado'+apoderado.id" @click="idGlobal = apoderado.id; indexGlobal = indice" >
						<label class="form-check-label text-capitalize" :for="'radApoderado'+apoderado.id" @click="idGlobal = apoderado.id; indexGlobal = indice" >{{apoderado.apellidos}} {{ apoderado.nombres }}</label>
					</li>
				</ul>
				<p class="mb-0 mt-2">Rellene el monto abonado. Monto a abonar: S/ {{ parseFloat(deuda.monto).toFixed(2) }}</p>
				<input type="number" class="form-control" v-model="monto">
				<p class="mb-0 mt-2">¿Observaciones adicionales?</p>
				<input type="text" class="form-control" v-model="observacion">
      </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" @click="guardarPago()">Guardar pago</button>
			</div>
    </div>
  </div>
</div>
</template>
<script>
import moment from 'moment'
export default{
	data(){ return {
		deuda:[], dniABuscar:'', noExiste:false, asistentes:[], hijos:[], apoderados:[], idGlobal:-1, indexGlobal:-1, monto:0, observacion:''
	}},
	mounted(){
		this.cargarDatos()
	},
	methods:{
		cargarDatos(){
			let datos = new FormData()
			datos.append('pedir', 'detalles')
			datos.append('idDeuda', this.$route.params.idDeuda )
			this.axios.post(this.servidor+'Deudas.php', datos)
			.then(resp => {
				this.deuda = resp.data.deuda
				this.asistentes = resp.data.asistentes
			})
		},
		fechaLatam(fecha){
			return moment(fecha).format('DD/MM/YYYY');
		},
		queNivel(nivel){
			if(nivel == '1') return 'Inicial'
			if(nivel == '2') return 'Primaria'
			if(nivel == '3') return 'Secundaria'
		},
		guardarPago(){
			if( this.indexGlobal==-1 )
				alertify.error('Debe buscar y seleccionar un apoderado', 10)
			else if( this.monto ==0 || this.monto =='' )
				alertify.error('Debe registrar un monto mayor a 0', 10)
			else{
				let datos = new FormData()
				datos.append('pedir', 'guardarPago')
				datos.append('idDeuda', this.$route.params.idDeuda )
				datos.append('idPadre', this.idGlobal )
				datos.append('monto', this.monto )
				datos.append('observacion', this.observacion )
				this.axios.post(this.servidor + 'Deudas.php', datos)
				.then(res => {
					if(res.data.mensaje == 'ok')
						alertify.message('Pago registrado correctamente', 10)
					const cerrar = document.querySelector('#modalAddPago .btn-close')
					cerrar.click()
					this.cargarDatos()
				})
			}
		},
		encontrarApoderado(id){
			const index = this.asistentes.findIndex(x=> x.id == id)
			if(index == -1) return true
			else return false
		},
		buscarDNI(){
			this.idGlobal=-1; this.indexGlobal=-1;
			if(this.dniABuscar!=''){
				let datos = new FormData()
				datos.append('pedir', 'buscarApoderados')
				datos.append('texto', this.dniABuscar)
				this.axios.post(this.servidor + 'Apoderado.php', datos)
				.then(res => {
					this.apoderados = res.data.apoderados
				})
			}
		},
		cerrarModal(){
			this.$router.push({ name: 'RelacionPadres', params:{dniPadre: this.dniABuscar }});
		},
		borrar(id, index){
			alertify.confirm('Eliminar apoderado', `¿Desea borrar el pago del Sr(a). ${this.asistentes[index].apellidos} por S/ ${this.asistentes[index].monto}? <br><strong>Dato:</strong> Aún se conservará el registro en la base de datos`, ()=>{
				let datos = new FormData()
				datos.append('pedir', 'borrar')
				datos.append('idPago', id)
				this.axios.post(this.servidor+'Deudas.php', datos)
				.then(resp => this.asistentes.splice(index,1) )
				alertify.success('Datos actualizados', 5);
			}, ()=>{});
		},
		textoDivido(texto){
			if(texto)
				return texto.replace('\n', '<br>')
		},
		imprimir(index){
			let datos = new FormData();
			datos.append('fecha', this.fechaLatam(this.asistentes[index].fecha) )
			datos.append('dni', this.asistentes[index].dni)
			datos.append('cliente', this.asistentes[index].apellidos + ' '+ this.asistentes[index].nombres )
			datos.append('motivo', this.deuda.motivo + ' del ' + this.deuda.grado + ' de '+ this.queNivel(this.deuda.idNivel) )
			datos.append('monto', this.asistentes[index].monto)
			datos.append('total', this.deuda.monto)
			datos.append('observacion', this.asistentes[index].observacion)
			this.axios.post(this.servidor+'printComprobante.php', datos)
		}
	},
	computed:{
		sumaMontos(){
			let suma = 0
			this.asistentes.forEach(apoderado => suma += parseFloat(apoderado.monto) )
			return suma.toFixed(2)
		}
	}
}
</script>