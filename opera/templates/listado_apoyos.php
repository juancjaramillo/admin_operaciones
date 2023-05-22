<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">

<table width="100%">
  <tr>
    <td height="40" colspan=3 align="center"><span class="azul_16"><strong>APOYOS DEL DIA<br> <?=strtoupper(strftime("%A %d de %B de %Y",strtotime($frm["fecha"])))?></strong></span></td>
  </tr>
	<tr>
		<td align="right"><a href="<?=$CFG->wwwroot?>/opera/movimientos_rec.php?mode=agregar_apoyo&fecha=<?=$frm["fecha"]?>" class="link_verde" title="Agregar Apoyo">Agregar Apoyo</a></td>
	</tr>
  <tr>
    <td valign="top">
      <table width="100%" cellpadding="5" cellspacing="3">
        <tr>
          <td>
            <table width="100%" border=1 bordercolor="#7fa840" align="center" id="tabla_mov">
              <tr>
								<td align="center">RUTA</td>
								<td align="center">VEHÍCULO</td>
								<td align="center">INICIO</td>
								<td align="center">FINAL</td>
								<td align="center">PESO TOTAL</td>
								<td align="center">KM INICIO</td>
								<td align="center">KM FINAL</td>
								<td align="center">OPCIONES</td>
							</tr>
							<?while($query = $db->sql_fetchrow($qid)){?>
              <tr>
								<td><?=$query["ruta"]?></td>
								<td><?=$query["codigo"]?></td>
								<td><?=$query["inicio"]?></td>
								<td><?=$query["final"]?></td>
								<td><?=$query["peso"]?></td>
								<td><?=$query["km_inicial"]?></td>
								<td><?=$query["km_final"]?></td>
								<td align="center"><a href="<?=$CFG->wwwroot?>/opera/movimientos_rec.php?mode=editar_apoyo&id=<?=$query["id"]?>" class="link_verde" title="Editar">A</a>&nbsp;&nbsp;<a href="<?=$CFG->wwwroot?>/opera/movimientos_rec.php?mode=eliminar_apoyo&id=<?=$query["id"]?>&fecha=<?=$frm["fecha"]?>" class="link_verde" title="Borrar">B</a></td>
							</tr>
							<?}?>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>


