<?php
// CHECK IF FILE EXISTS
if (isset($_GET['id']))
{
	switch ($_GET['id'])
	{
		case 'raspi-config':
			echo 'raspi_available';
				break;
		case 'echo':
			echo 'successful';
				break;
	}
}
?>