<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=$titulo?></strong></span></td>
	</tr>
	<tr>
		<td align="center">
			<table width="98%" border=1 bordercolor="#7fa840" id="tabla_actividades">
				<?if($frm["tipo"] == "por_tipo"){?>
				<tr>
					<td align="center"><strong><?=$tituloDos?></strong></td>
					<td align="center">Número</td>
					<td align="center">Valor</td>
				</tr>
				<?
				$total=array();
				$total["tot"]=$total["num"]=0;
				foreach($tpmttos as $idTipo => $dx){
					echo "<tr>
						<td>".$dx."</td>";
					if(isset($datos[$idTipo]))
					{
						echo "<td align='right'>".$datos[$idTipo]["numero"]."</td>\n<td align='right'>".number_format($datos[$idTipo]["valor"],2)."</td>";
						$total["tot"]+=$datos[$idTipo]["valor"];
						$total["num"]+=$datos[$idTipo]["numero"];
					}
					else
						echo "<td>&nbsp;</td>\n<td>&nbsp;</td>";
					echo "</tr>";					
				}

				echo "<tr><td>TOTAL</td>";
				echo "<td align=\"right\">".$total["num"]."</td>\n";
				echo "<td align=\"right\">".number_format($total["tot"],2)."</td>\n";
				echo "</tr>";

				}else{?>
				<tr>
					<td align="center" rowspan=2><strong><?=$tituloDos?></strong></td>
					<?foreach($tpmttos as $dx){
					echo "<td align=\"center\" colspan=2>".$dx."</td>\n";
					}?>
				</tr>
				<tr>
				<?
				$total=array();
				foreach($tpmttos as $key => $dx){
					echo "<td align=\"center\">Número</td>\n";
					echo "<td align=\"center\">Valor</td>\n";
					$total[$key]=array("num"=>0,"tot"=>0);
				}?>
				</tr>
				<?

				foreach($datos as $key => $valores)
				{
					echo "<tr>
						<td>".$key."</td>";
					foreach($tpmttos as $idTipo => $dx){
						if(isset($valores[$idTipo]))
						{
							echo "<td align='right'>".$valores[$idTipo]["numero"]."</td>\n<td align='right'>".number_format($valores[$idTipo]["valor"],2)."</td>";
							$total[$idTipo]["tot"]+=$valores[$idTipo]["valor"];
							$total[$idTipo]["num"]+=$valores[$idTipo]["numero"];
						}
						else
							echo "<td>&nbsp;</td>\n<td>&nbsp;</td>";
					}
					echo "</tr>";
				}
				
				echo "<tr><td>TOTAL</td>";
				foreach($tpmttos as $key => $dx){
					echo "<td align=\"right\">".$total[$key]["num"]."</td>\n";
					echo "<td align=\"right\">".number_format($total[$key]["tot"],2)."</td>\n";
				}
				echo "</tr>";
				?>

				<?}?>
			</table>
		</td>
	</tr>
</table>
