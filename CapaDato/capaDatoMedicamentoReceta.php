<?php
include_once("conexion.php");
	class capaDatoMedicamentoReceta{
		private $objetoConexion;


		public function __construct(){
			$this->objetoConexion=new conexion();
		}

		public function insertar($idreceta,$idmedicamento,$horafrecuencia){
			
			$this->objetoConexion->conectar();

			$this->objetoConexion->ejecutar(
				"insert into medicamentoreceta (idreceta, idmedicamento,horafrecuencia) values ('$idreceta', '$idmedicamento','$horafrecuencia')");

			$this->objetoConexion->desconectar();	
		}


		public function getMedicamentoRecetaByIdReceta($id){
			$this->objetoConexion->conectar();
			$resultado=$this->objetoConexion->ejecutar(
			"select medicamento.id,medicamento.nombre,medicamentoreceta.horafrecuencia as horafrecuencia,usuario.apellido,usuario.nombre as nombrep,tratamiento.descripcion,receta.recomendacion from medicamento,medicamentoreceta,receta,tratamiento,usuario where medicamentoreceta.idmedicamento=medicamento.id and medicamentoreceta.idreceta=receta.id and receta.idtratamiento=tratamiento.id and tratamiento.idusuario=usuario.id and receta.id='$id'");

			$this->objetoConexion->desconectar();
			return $resultado;
		}




		
	}


?>