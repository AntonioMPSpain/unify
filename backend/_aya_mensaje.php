		<? 
		
		if ($est=="ko"){ ?> 
				<h2 class="titulonoticia">Error:</h2>
				<div class="mensaje ko">
					<? if ($est_texto<>""){ ?> 
						<h3><?=$est_texto?></h3>	
					<? }?>	
					<? if ($est_texto2<>""){ ?> 
						<ul>
							<li><?=$est_texto2?></li>
						</ul>
					<? }?>	
				</div>		
				<br />
				<?
		} //fin  if ($est=="ko"){ 				
		?>
		<? if ($est=="ok"){ ?> 
				<h2 class="titulonoticia">OK</h2>
				<div class="mensaje ok">
					<? if ($est_texto<>""){ ?> 
						<h3><?=$est_texto?></h3>	
					<? }?>	
					<? if ($est_texto2<>""){ ?> 
						<ul>
							<li><?=$est_texto2?></li>
						</ul>
					<? }?>	
				</div>		
				<br />
		<? }?>	
