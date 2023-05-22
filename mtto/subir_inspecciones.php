<?
include("../application.php");
if(sizeof($_POST)>sizeof($_GET)) $frm=$_POST;
else $frm=$_GET;

switch(nvl($frm["mode"])){
	case "upload":
		upload($frm);
	break;
	default:
		print_form($frm);
	break;
}

function upload($frm){
GLOBAL $CFG,$db;

	include($CFG->dirroot."/templates/header_popup.php");
	require($CFG->common_libdir . "/php-excel-reader/excel_reader2.php");
	$data = new Spreadsheet_Excel_Reader($_FILES["archivo"]["tmp_name"],false);
	$filas=$data->rowcount();
	$columnas=$data->colcount();
	$frm["centro"]=$db->sql_field("SELECT centro FROM centros WHERE id='$frm[id_centro]'");
	echo "<table border=\"1\">\n";
	echo "<tr><th>#Fila</th>";
	for($col=1;$col<=$columnas;$col++) echo "<th>" . $data->val(1,$col) . "</th>";
	echo "<th>Observaciones</th>";
	echo "</tr>\n";
	for($row=2;$row<$filas;$row++){
		$vacio=true;
		$arrayFila=array();
		for($col=1;$col<=$columnas;$col++){
			if($data->val($row,$col)!="") $vacio=false;
			array_push($arrayFila,$data->val($row,$col));
		}
		if(!$vacio){
			echo "<tr>";
			echo "<td>$row</td>";
			evalua($frm,$arrayFila);
			echo "</tr>\n";
		}
	}

	echo "</table>\n";
	echo "<form><input type=\"button\" onclick=\"window.close()\" value=\"Cerrar\"></form>\n";

}

function evalua($frm,$arrayFila){
	GLOBAL $CFG,$db;

	foreach($arrayFila AS $key=>$val){
		echo "<td>" . $val . "</td>";
	}
	echo "<td>";
	$fecha=date("Y-m-d",strtotime($arrayFila[0]));

	$strQuery="SELECT * FROM llta.llantas WHERE numero='" . $arrayFila[1] . "' AND id_centro='$frm[id_centro]'";
	error_log($strQuery);
	if($llanta=$db->sql_row($strQuery)){
		//Verificar si ya existe el movimiento para esa llanta para ese día:
		$strQuery="SELECT * FROM llta.movimientos WHERE id_llanta='$llanta[id]' AND fecha='" . $fecha . "' AND id_tipo_movimiento='2'";//2=>Inspección
		error_log($strQuery);
		if($mov=$db->sql_row($strQuery)){
			//El movimiento ya existe, hay que actualizar los datos
			echo "OK, mov. actualizado.";
			$qUpdate=$db->sql_query("
				UPDATE llta.movimientos SET 
					id_vehiculo='$llanta[id_vehiculo]',
					posicion='$arrayFila[2]',
					km='$arrayFila[3]',
					prof_uno='$arrayFila[4]',
					prof_dos='$arrayFila[5]',
					prof_tres='$arrayFila[6]'
				WHERE id='$mov[id]'
			");
		}
		else{
			$mov["id_llanta"]=$llanta["id"];
			$mov["fecha"]=$fecha;
			$mov["id_tipo_movimiento"]=2;//2=>Inspección
			$mov["id_vehiculo"]=$llanta["id_vehiculo"];
			$mov["posicion"]=$arrayFila[2];
			$mov["km"]=$arrayFila[3];
			$mov["prof_uno"]=$arrayFila[4];
			$mov["prof_dos"]=$arrayFila[5];
			$mov["prof_tres"]=$arrayFila[6];
			$db->sql_insert("llta.movimientos",$mov);
			echo "OK, mov. creado.";
		}
	}
	else echo "No existe la llanta $arrayFila[1] en $frm[centro].";
	echo "</td>";
}

function print_form($frm){
GLOBAL $ME,$CFG,$db;

	$user=$_SESSION[$CFG->sesion]["user"];

	$centrosOptions = $db->sql_listbox("SELECT id, centro FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]') ORDER BY centro","Seleccione...");
	include($CFG->dirroot."/templates/header_popup.php");
	include("templates/subir_inspecciones.php");

}
?>
