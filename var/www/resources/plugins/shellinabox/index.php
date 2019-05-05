<div>
	<div class="box">
		<div class="inner-header">
			<span>Shell in a box</span>
		</div>
		<div class="inner">
			<?php 
				$connection = @fsockopen($_SERVER['SERVER_ADDR'], 4200);
				if (is_resource($connection)) {
					echo '<iframe src="https://'. $_SERVER['SERVER_ADDR'].':4200/" style="width:100%; height: 600px;"></iframe>';
					fclose($connection);
				} else {
					echo '	<center>
								Shell in a Box is not installed. Please install it.<br /><br /> sudo apt-get update && sudo apt-get install shellinabox -y
							</center>';
				}
			?>
			<center><br>Standard User: pi<br>
			Standard Password: raspberry<br>
			Firefox Bug: Minus Symbole (-) dont work, use the - from keypad.</center>
			<div style="width:100%; text-align:right; font-size:11px;">Created by <a href="https://sven-goessling.de" target="_blank">Sven Goessling</a></div>
		</div>
	</div>
</div>