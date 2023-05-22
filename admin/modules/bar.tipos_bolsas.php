<?
	require($CFG->objectPath . "/object.php");

class bolsas extends entity
{
	function delete()
	{
		$qid = $this->db->sql_row("select count(id) as num FROM bar.movimientos_bolsas WHERE id_tipo_bolsa=".$this->id);
		if($qid["num"] != 0)
		{
			echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.outerHeight=200;\nwindow.outerWidth=300;\n</script>\n";
			echo "No se puede borrar, porque tiene elementos relacionados.<br><br>\n";
			echo "<input type=\"button\" onClick=\"window.close();\" value=\"Cerrar\">";
			die();
		}

		parent::delete();
	}	
}


	$entidad =& new bolsas();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Tipos Bolsas");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","tipo");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------


// ---------- ATRIBUTOS          ----------------


	$atributo=new attribute($entidad);
	$atributo->set("field","tipo");
	$atributo->set("label","Tipo");
	$atributo->set("sqlType","character varying(50)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","dimensiones");
	$atributo->set("label","Dimensiones");
	$atributo->set("sqlType","character varying(50)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","capacidad");
	$atributo->set("label","Capacidad");
	$atributo->set("sqlType","character varying(50)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","color");
	$atributo->set("label","Color");
	$atributo->set("sqlType","character varying(50)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
		
	$entidad->checkSqlStructure();

?>
 
