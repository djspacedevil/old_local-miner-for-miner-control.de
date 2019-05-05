<?php
// functions_rpi.php
function rpi_getRuntime()
{
	$runtime = trim(@shell_exec('cat /proc/uptime | awk -F \'.\' \'{print $1}\''));
	return $runtime;
}

function rpi_getHostname()
{
	$host = trim(@shell_exec('cat /proc/sys/kernel/hostname'));
	return $host;
}

function rpi_getHostAddr()
{
	if (!isset($_SERVER['SERVER_ADDR']))
	{
		return 'unknown';
	}
	
	if (!$ip = $_SERVER['SERVER_ADDR'])
	{
		return gethostbyname($this->getHostname());
	}
	return $ip;
}

function rpi_getCoreTemprature()
{	
	$file = 85000;
	
	while ($file == 85000)
		$file = @shell_exec('cat /sys/class/thermal/thermal_zone0/temp');
	
	if ($file != false)
		return round((trim($file)/1000), 2);
	
	return 0;
}

function rpi_getCpuClock()
{
	$file = shell_exec('cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq');
	if ($file != false)
	{
		return round(trim($file)/1000);
	}
	return 0;
}

function rpi_getCpuMinClock()
{
	$file = shell_exec('cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_min_freq');
	if ($file != false)
	{
		return round(trim($file)/1000);
	}
	return 0;
}

function rpi_getCpuMaxClock()
{
	$file = shell_exec('cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_max_freq');
	if ($file != false)
	{
		return round(trim($file)/1000);
	}
	return 0;
}

function rpi_getCPUType()
{
	$file = shell_exec('cat /proc/cpuinfo');
	preg_match('#Hardware\s*:\s*([^\s]+)#i', $file, $match);
	
	if (isset($match[1]))
	{	
		return $match[1];
	}
	return NULL;
}

function rpi_getCpuModel()
{
	$model = trim(@shell_exec('cat /proc/cpuinfo | grep -m 1 "model name" | tr -d " " | cut -d ":" -f 2'));
	return $model;
}

function rpi_getCPULoad()
{
	$file = shell_exec('top -bn2 -d 0.7 | grep ^%Cpu | tail -n1');
	preg_match('#([\d\.]+) id#i', $file, $match);
	
	if (isset($match[1]))
	{	
		return round(100-trim($match[1]), 0);
	}
	return NULL;
}

function rpi_getDistribution()
{
	$distribution = trim(@shell_exec('cat /etc/issue | cut -d " " -f 1-3'));
	return $distribution;
}

function rpi_getKernelVersion()
{
	$kernel = trim(@shell_exec('cat /proc/version | cut -d " " -f 1,3'));
	return $kernel;
}

function rpi_getRpiRevision()
{
	$revision = array();
	$revision[2] = array('revision' => '0002', 'model' => 'B', 'pcb' => 1, 'memory' => 256, 'manufacturer' => '');
	$revision[3] = array('revision' => '0003', 'model' => 'B', 'pcb' => 1, 'memory' => 256, 'manufacturer' => '');
	$revision[4] = array('revision' => '0004', 'model' => 'B', 'pcb' => 2, 'memory' => 256, 'manufacturer' => 'Sony');
	$revision[5] = array('revision' => '0005', 'model' => 'B', 'pcb' => 2, 'memory' => 256, 'manufacturer' => 'Qisda');
	$revision[6] = array('revision' => '0006', 'model' => 'B', 'pcb' => 2, 'memory' => 256, 'manufacturer' => 'Egoman');
	$revision[7] = array('revision' => '0007', 'model' => 'A', 'pcb' => 2, 'memory' => 256, 'manufacturer' => 'Egoman');
	$revision[8] = array('revision' => '0008', 'model' => 'A', 'pcb' => 2, 'memory' => 256, 'manufacturer' => 'Sony');
	$revision[9] = array('revision' => '0009', 'model' => 'A', 'pcb' => 2, 'memory' => 256, 'manufacturer' => 'Qisda');
	$revision[13] = array('revision' => '000d', 'model' => 'B', 'pcb' => 2, 'memory' => 512, 'manufacturer' => 'Egoman');
	$revision[14] = array('revision' => '000e', 'model' => 'B', 'pcb' => 2, 'memory' => 512, 'manufacturer' => 'Sony');
	$revision[15] = array('revision' => '000f', 'model' => 'B', 'pcb' => 2, 'memory' => 512, 'manufacturer' => 'Qisda');
	$revision[16] = array('revision' => '0010', 'model' => 'B+', 'pcb' => 1, 'memory' => 512, 'manufacturer' => 'Sony');
	$revision[17] = array('revision' => '0011', 'model' => 'Compute Module', 'pcb' => 1, 'memory' => 512, 'manufacturer' => 'Sony');
	$revision[18] = array('revision' => '0012', 'model' => 'A+', 'pcb' => 1, 'memory' => 256, 'manufacturer' => 'Sony');
	
	$file = shell_exec('cat /proc/cpuinfo');
	preg_match('#\nRevision\s*:\s*([\da-f]+)#i', $file, $match);
	
	if (isset($match[1]))
	{
		if (strlen($match[1]) == 4)
		{
			return $revision[hexdec($match[1])];
		}
		elseif (strlen($match[1]) == 6)
		{
			$revision_model = array(0 => 'A', 1 => 'B', 2 => 'A+', 3 => 'B+', 4 => 'Pi 2 B', 5 => 'Alpha', 6 => 'Compute Module');
			$revision_memory = array(0 => 256, 1 => 512, 2 => 1024);
			$revision_manufacturer = array(0 => 'Sony', 1 => 'Egoman', 2 => 'Embest', 3 => 'Unbekannt', 4 => 'Embest');
			
			return array('revision' => $match[1],
						 'model' => $revision_model[hexdec(substr($match[1], 3, 2))],
						 'pcb' => hexdec(substr($match[1], 5)),
						 'memory' => $revision_memory[bindec(substr(decbin(hexdec(substr($match[1], 0, 1))), 1))],
						 'manufacturer' => $revision_manufacturer[hexdec(substr($match[1], 1, 1))]);
		}
		elseif (strlen($match[1]) == 7)
		{
			$revision_model = array(0 => 'A', 1 => 'B', 2 => 'A+', 3 => 'B+', 4 => 'Pi 2 B', 5 => 'Alpha', 6 => 'Compute Module');
			$revision_memory = array(0 => 256, 1 => 512, 2 => 1024);
			$revision_manufacturer = array(0 => 'Sony', 1 => 'Egoman', 2 => 'Embest', 3 => 'Unbekannt', 4 => 'Embest');
			
			return array('revision' => $match[1],
						 'model' => $revision_model[hexdec(substr($match[1], 4, 2))],
						 'pcb' => hexdec(substr($match[1], 6)),
						 'memory' => $revision_memory[bindec(substr(decbin(hexdec(substr($match[1], 1, 1))), 1))],
						 'manufacturer' => $revision_manufacturer[hexdec(substr($match[1], 2, 1))]);
		}
	}
	
	return NULL;
}

function rpi_getRpiSerial()
{
	$file = shell_exec('cat /proc/cpuinfo');
	preg_match('#\nSerial\s*:\s*([\da-f]+)#i', $file, $match);
	
	return $match[1];
}

function rpi_getMemorySplit()
{
	$rev = $this->getRpiRevision();
	if ($rev >= 7)
	{
		// 512MB
		$config = @shell_exec('cat /boot/config.txt');
		preg_match('#gpu_mem=([0-9]+)#i', $config, $match);
		$total = intval($match[1]);
		
		if ($total == 16)
		{
			return array('system' => '496 MiB', 'video' => '16 MiB');
		}
		elseif ($total == 32)
		{
			return array('system' => '480 MiB', 'video' => '32 MiB');
		}
		elseif ($total == 64)
		{
			return array('system' => '448 MiB', 'video' => '64 MiB');
		}
		elseif ($total == 128)
		{
			return array('system' => '384 MiB', 'video' => '128 MiB');
		}
		return array('system' => '256 MiB', 'video' => '256 MiB');
	}
		
	// 256MB		
	$mem   = $this->getMemoryUsage();
	$total = round($mem['total'] / 1024 / 1024, 0);
	
	if ($total <= 128)
	{
		return array('system' => '128 MiB', 'video' => '128 MiB');
	}
	elseif ($total > 128 && $total <= 192)
	{
		return array('system' => '192 MiB', 'video' => '64 MiB');
	}
	elseif ($total > 192 && $total <= 224)
	{
		return array('system' => '224 MiB', 'video' => '32 MiB');
	}
	return array('system' => '240 MiB', 'video' => '16 MiB');
}

function rpi_getMemoryUsage()
{
	exec('free -bo', $data);
	list($type, $total, $used, $free, $shared, $buffers, $cached) = preg_split('#\s+#', $data[1]);
	$usage = round(($used - $buffers - $cached) / $total * 100);
	
	return array('percent' => $usage, 'total' => $total, 'free' => ($free + $buffers + $cached), 'used' => ($used - $buffers - $cached));
}

function rpi_getSwapUsage()
{
	exec('free -bo', $data);
	list($type, $total, $used, $free) = preg_split('#\s+#', $data[2]);
	$usage = round($used / $total * 100);
	
	return array('percent' => $usage, 'total' => $total, 'free' => $free, 'used' => $used);
}

/*function rpi_getSwapUsage()
{
	exec('/sbin/swapon -s | grep -E "[[:digit:]].+[[:digit:]]" -o | tr -s [:blank:] "|"', $data);
	
	if (!isset($data[0]))
		return 0;
	
	list($total, $used) = preg_split('/|/', $data[0]);
	$usage = @round($used / $total * 100);
	
	return array('percent' => $usage, 'total' => $total, 'free' => ($total - $used), 'used' => $used);
}*/

function rpi_getMemoryInfo()
{
	exec('df -lT | grep -vE "tmpfs|rootfs|Filesystem|Dateisystem"', $data);
	
	$devices = array();
	$totalSize = 0;
	$usedSize = 0;
	foreach ($data as $row)
	{
		list($device, $type, $blocks, $use, $available, $used, $mountpoint) = preg_split('#[^\dA-Z/]+#i', $row);
		
		$totalSize += $blocks * 1024;
		$usedSize  += $use * 1024;
		$devices[] = array(
						'device'     => $device,
						'type'       => $type,
						'total'      => $blocks * 1024,
						'used'       => $use * 1024,
						'free'       => $available * 1024,
						'percent'    => round(($use * 100 / $blocks), 0),
						'mountpoint' => $mountpoint
						);
	}
	
	$devices[] = array('total' => $totalSize, 'used' => $usedSize, 'free' => $totalSize - $usedSize, 'percent' => round(($usedSize * 100 / $totalSize), 0));
	
	return $devices;
}

function rpi_getUsbDevices()
{
	exec('lsusb', $data);
	$devices = array();
	
	foreach ($data as $row)
	{
		preg_match('#[0-9a-f]{4}:[0-9a-f]{4}\s+(.+)#i', $row, $match);
		$devices[] = trim($match[1]);
	}
	
	return $devices;
}
?>