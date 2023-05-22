Content-Type: application/octet-stream; name="javascripts.js"
Content-Transfer-Encoding: base64
Content-Disposition: attachment; filename="javascripts.js"

var ventanaCalendario=false
var ventanaCalendario2=false

function muestraCalendario(raiz,formulario_destino,campo_destino,mes_destino,ano_destino){
	//funcion para abrir una ventana con un calendario.
	//Se deben indicar los datos del formulario y campos que se desean editar con el calendario, es decir, los campos donde va la fecha.
	if (typeof ventanaCalendario.document == "object") {
		ventanaCalendario.close()
	}
	ventanaCalendario = window.open("calendario/index.php?formulario=" + formulario_destino + "&nomcampo=" + campo_destino,"calendario","width=230,height=300,left=100,top=100,scrollbars=no,menubars=no,statusbar=NO,status=NO,resizable=YES,location=NO")
}
function muestraContacto(raiz,formulario_destino,campo_destino,mes_destino,ano_destino){
	//funcion para abrir una ventana con un calendario.
	//Se deben indicar los datos del formulario y campos que se desean editar con el calendario, es decir, los campos donde va la fecha.
	if (typeof ventanaCalendario.document == "object") {
		ventanaCalendario.close()
	}
	ventanaCalendario = window.open("contactos.php?formulario=" + formulario_destino + "&nomcampo=" + campo_destino,"calendario","width=700,height=300,left=100,top=100,scrollbars=yes,menubars=no,statusbar=NO,status=NO,resizable=YES,location=NO")
}
function muestraEditarContacto(raiz,formulario_destino,id_empresa,ano_destino){
	if (typeof ventanaCalendario.document == "object") {
		ventanaCalendario.close()
	}
	ventanaCalendario = window.open("editarcontactos.php?formulario=" + formulario_destino + "&id_empresa=" + id_empresa,"calendario","width=700,height=300,left=100,top=100,scrollbars=yes,menubars=no,statusbar=NO,status=NO,resizable=YES,location=NO")
}

function muestraLicencia(raiz,id_empresa,ano_destino){
	if (typeof ventanaCalendario2.document == "object") {
		ventanaCalendario2.close()
	}
	ventanaCalendario2 = window.open("licencia.php?id_empresa=" + id_empresa,"licencia","width=600,height=500,left=100,top=100,scrollbars=yes,menubars=no,statusbar=NO,status=NO,resizable=YES,location=NO")
}

function muestraContacto(raiz,formulario_destino,campo_destino,mes_destino,ano_destino){
	if (typeof ventanaCalendario.document == "object") {
		ventanaCalendario.close()
	}
	ventanaCalendario = window.open("contactos.php?formulario=" + formulario_destino + "&nomcampo=" + campo_destino,"calendario","width=700,height=300,left=100,top=100,scrollbars=yes,menubars=no,statusbar=NO,status=NO,resizable=YES,location=NO")
}
