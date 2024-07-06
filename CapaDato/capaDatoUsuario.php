<?php
include_once("conexion.php");
include('Kairos.php');
	class capaDatoUsuario{
		private $objetoConexion;


		public function __construct(){
			$this->objetoConexion=new conexion();
		}


		public function insertar($nombre, $apellido,$cedula,$correo,$nit,$sexo,$telefono,$fotoBase64){
		file_put_contents("../Public/Imagen/".$nombre.' '.$apellido.'.jpg', base64_decode($fotoBase64));
		$destinofoto= "../Public/Imagen/".$nombre.' '.$apellido.'.jpg';
		$password_encriptado=base64_encode($cedula);
		$kairos = new Kairos();
		$params = array(
			"image" => $fotoBase64,
			"gallery_name" => "profile",
			"subject_id" => (string)$nombre.' '.$apellido
		);
		$kairos->enroll($params);
		$this->objetoConexion->conectar();
		$this->objetoConexion->ejecutar(
			"insert into usuario (nombre, apellido,cedula, correo,password,nit,sexo,telefono,foto,tipo) values ('$nombre', '$apellido','$cedula', ' $correo', '$password_encriptado', '$nit','$sexo','$telefono','$destinofoto','C')");

		$this->objetoConexion->desconectar();		
		}

		public function insertarCliente($nombre, $apellido,$cedula,$correo,$nit,$sexo,$telefono,$fotoBase64){
		file_put_contents("Public/Imagen/".$nombre.' '.$apellido.'.jpg', base64_decode($fotoBase64));
		$destinofoto= "../Public/Imagen/".$nombre.' '.$apellido.'.jpg';
		$password_encriptado=base64_encode($cedula);
		
		$this->objetoConexion->conectar();
		$this->objetoConexion->ejecutar(
			"insert into usuario ( nombre, apellido,cedula, correo,password,nit,sexo,telefono,foto,tipo) values ('$nombre', '$apellido','$cedula', ' $correo', '$password_encriptado', '$nit','$sexo','$telefono','$destinofoto','C')");

		$this->objetoConexion->desconectar();	
		}

		public function eliminar($id){
			$this->objetoConexion->conectar();
			$this->objetoConexion->ejecutar(
			"delete from usuario where id='$id'");

			$this->objetoConexion->desconectar();
		}

		public function actualizar($id,$nombre, $apellido,$cedula,$correo,$nit,$sexo,$telefono){
			$password_encriptado=base64_encode($cedula);
			$this->objetoConexion->conectar();
			$this->objetoConexion->ejecutar(
			"update usuario set nombre='$nombre',apellido='$apellido',cedula='$cedula',correo='$correo',nit='$nit' ,sexo='$sexo',telefono='$telefono',password='$password_encriptado' where id='$id'");
			
			$this->objetoConexion->desconectar();
		}

		public function mostrar(){
			$this->objetoConexion->conectar();
			$resultado=$this->objetoConexion->ejecutar(
			"select * from usuario");

			$this->objetoConexion->desconectar();
			return $resultado;
		}

		public function buscar($b){
			$this->objetoConexion->conectar();
			$resultado=$this->objetoConexion->ejecutar("SELECT * FROM usuario WHERE nombre LIKE '%".$b."%' LIMIT 10");
			$this->objetoConexion->desconectar();
			return $resultado;
		}
	
		public function getUsuario(){
			$this->objetoConexion->conectar();
			$resultado=$this->objetoConexion->ejecutar(
			"select * from usuario");

			$this->objetoConexion->desconectar();
			return $resultado;
		}


		
	}


?>