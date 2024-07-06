<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("location: /centraldent/index.php");
}
include_once("CapaDato/capaDatoEnfermedadSintoma.php");


if(isset($_POST['si'])){
$_SESSION["inferencia"][$_SESSION["indicePregunta"]] = "V";

siguientePregunta();
$datos = new stdClass();
$datos->inferencia = $_SESSION["inferencia"];
$datos->indicePregunta = $_SESSION["indicePregunta"];
$datos->indiceEnfermedad = $_SESSION["indiceEnfermedad"];
$datos->preguntaEnfermedadSintoma = $_SESSION["preguntaEnfermedadSintoma"];
//$datos->verticesInferenciaEnfermedad = $_SESSION["verticesInferenciaEnfermedad"];
//$datos->antecedente = $_SESSION["antecedente"];
$datos->diagnostico = $_SESSION["diagnostico"];
$datos->trabajo =$_SESSION["trabajo"];
$datos->precio =$_SESSION["precio"];
$datos->trabajoEnfermedad =$_SESSION["trabajoEnfermedad"];
$datos->descripcionEnfermedad = $_SESSION["descripcionEnfermedad"];
$datos->nombreEnfermedad = $_SESSION["nombreEnfermedad"];
$respuesta = json_encode($datos);

echo $respuesta;

}
if(isset($_POST['no'])){

siguientePregunta();	
$datos = new stdClass();
$datos->inferencia = $_SESSION["inferencia"];
$datos->indicePregunta = $_SESSION["indicePregunta"];
$datos->indiceEnfermedad = $_SESSION["indiceEnfermedad"];
$datos->preguntaEnfermedadSintoma = $_SESSION["preguntaEnfermedadSintoma"];
//$datos->verticesInferenciaEnfermedad = $_SESSION["verticesInferenciaEnfermedad"];
//$datos->antecedente = $_SESSION["antecedente"];
$datos->diagnostico = $_SESSION["diagnostico"];
$datos->trabajo =$_SESSION["trabajo"];
$datos->precio =$_SESSION["precio"];
$datos->trabajoEnfermedad =$_SESSION["trabajoEnfermedad"];
$datos->descripcionEnfermedad = $_SESSION["descripcionEnfermedad"];
$datos->nombreEnfermedad = $_SESSION["nombreEnfermedad"];
$respuesta = json_encode($datos);

echo $respuesta;
}	


function siguientePregunta(){

	if ($_SESSION["indicePregunta"] < $_SESSION["maximoPregunta"]) {
		$_SESSION["indicePregunta"] = $_SESSION["indicePregunta"]+1;
	}else{

		motorDeInferencia();

	}
}

function motorDeInferencia(){

$aristasEnfermedad = $_SESSION["AristaEnfermedadSintoma"][$_SESSION["indiceEnfermedad"]-1];
$arregloVerticesInferenciaEnfermedad = Array();
$indiceVerticesInferenciaEnfermedad = 0;
	for ($i=0; $i < count($aristasEnfermedad); $i++) { 
		if ($_SESSION["inferencia"][$i] == "V") {
			$arregloVerticesInferenciaEnfermedad[$indiceVerticesInferenciaEnfermedad] = $aristasEnfermedad[$i];
			$indiceVerticesInferenciaEnfermedad++;

		}
	}

	if (count($arregloVerticesInferenciaEnfermedad)>0) {


		$_SESSION["verticesInferenciaEnfermedad"] = $arregloVerticesInferenciaEnfermedad;

		$porcentaje = diagnostico($_SESSION["verticesInferenciaEnfermedad"]);


		if ($porcentaje >49) {
			$_SESSION["diagnostico"] = 1;



		}else{
			$indice = porcentajeMayor($_SESSION["verticesInferenciaEnfermedad"]);
			$_SESSION["antecedente"] = buscarAntecedente($_SESSION["verticesInferenciaEnfermedad"][$indice],$_SESSION["indiceEnfermedad"]);

			if ($_SESSION["antecedente"] == -1) {
				$_SESSION["indiceEnfermedad"] = $_SESSION["indiceEnfermedad"]+1;
			}else{
				$_SESSION["indiceEnfermedad"] = $_SESSION["antecedente"]+1;
				$_SESSION["antecedente"] = -1;
			}
			generarPreguntas();
		}

	}else{
		$_SESSION["indiceEnfermedad"] = $_SESSION["indiceEnfermedad"]+1;
		generarPreguntas();
	}

}

function buscarAntecedente($elemento,$indiceEnfermedad){

$antecedente = -1;

$_SESSION["elemento"] = $elemento;
	for ($i=$indiceEnfermedad; $i < count($_SESSION["AristaEnfermedadSintoma"]); $i++) { 
		$aristasEnfermedad = $_SESSION["AristaEnfermedadSintoma"][$i];
		for ($f=0; $f < count($aristasEnfermedad); $f++) { 
			if ($aristasEnfermedad[$f] == $_SESSION["elemento"]) {
				$antecedente = $i;
				$f = count($aristasEnfermedad);
				$i = count($_SESSION["AristaEnfermedadSintoma"]);
			}
		}
	}			

return $antecedente;
}


function generarPreguntas(){
$enfermedadSintomas = new capaDatoEnfermedadSintoma();


$resultado = $enfermedadSintomas->getNombreSintomasByEnfermedadId($_SESSION["indiceEnfermedad"]);


$i = 0;
$c = 0;
$arregloNombreSintoma = Array();
$arregloIdSintoma = Array();
$arregloInferencia = Array();
if ($_SESSION["elemento"] != -1) {
	while ($i < count($resultado)) {

			if ($_SESSION["elemento"] != $resultado[$i]["id"]) {
				$arregloNombreSintoma[$c] = $resultado[$i]["nombre"];
				$arregloIdSintoma[$c] = $resultado[$i]["id"];
				$arregloInferencia[$c] = "F";
				$c++;
			}


		$i++;
	}
	$_SESSION["maximoPregunta"] = count($arregloNombreSintoma)-1;
	$_SESSION["preguntaEnfermedadSintoma"] = $arregloNombreSintoma;
	$_SESSION["preguntaIdSintoma"] = $arregloIdSintoma;
	$_SESSION["inferencia"] = $arregloInferencia;
	$_SESSION["indicePregunta"] = 0;
	$_SESSION["AristaEnfermedadSintoma"][$_SESSION["indiceEnfermedad"]-1] = $arregloIdSintoma;
}else{
	while ($i < count($resultado)) {

				$arregloNombreSintoma[$i] = $resultado[$i]["nombre"];
				$arregloIdSintoma[$i] = $resultado[$i]["id"];
				$arregloInferencia[$i] = "F";

		$i++;
	}
	$_SESSION["maximoPregunta"] = count($arregloNombreSintoma)-1;
	$_SESSION["preguntaEnfermedadSintoma"] = $arregloNombreSintoma;
	$_SESSION["preguntaIdSintoma"] = $arregloIdSintoma;
	$_SESSION["inferencia"] = $arregloInferencia;
	$_SESSION["indicePregunta"] = 0;

}



}



function diagnostico($verticeInferenciaEnfermedad){
$porcentaje = 0;
$enfermedadSintomas = new capaDatoEnfermedadSintoma(); 
	for ($i=0; $i < count($verticeInferenciaEnfermedad); $i++) {

		$resultado =  $enfermedadSintomas->getPonderacionById($verticeInferenciaEnfermedad[$i],$_SESSION["indiceEnfermedad"]);
		$porcentaje = $porcentaje + $resultado[0]["ponderacion"];
	}

return $porcentaje;
}


function porcentajeMayor($verticeInferenciaEnfermedad){
$porcentaje = 0;
$indice = -1;
$enfermedadSintomas = new capaDatoEnfermedadSintoma(); 
	for ($i=0; $i < count($verticeInferenciaEnfermedad); $i++) { 

		$resultado =  $enfermedadSintomas->getPonderacionById($verticeInferenciaEnfermedad[$i],$_SESSION["indiceEnfermedad"]);
		if ($resultado[0]["ponderacion"] > $porcentaje) {
			$porcentaje = $resultado[0]["ponderacion"];
			$indice = $i;

		}
	}

return $indice;
}



?>