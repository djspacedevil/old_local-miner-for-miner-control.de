<?php
// classes.php
class Logging
{
	private $log_file, $fp, $log_limit = -1, $log_file_all;
	
	public function setFile($file = '')
	{
		$this->log_file = $file;
	}
	
	public function setLimit($limit = -1)
	{
		$this->log_limit = $limit;
	}
	
	public function add($message = '')
	{
		if (!is_resource($this->fp))
			$this->open();
		
		if ($this->log_limit > -1)
			$this->shortLog();
		
		fwrite($this->fp, $message.PHP_EOL);
	}
	
	public function getAll()
	{		
		if (empty($this->log_file_all))
			$this->log_file_all = file($this->log_file);
		
		return array_map(array($this, 'cube'), $this->log_file_all);
	}
	
	public function getLast()
	{
		if (empty($this->log_file_all))
			$this->log_file_all = file($this->log_file);
		
		return $this->cube($this->log_file_all[count($this->log_file_all)-1]);
	}
	
	private function shortLog()
	{
		if ($this->log_limit == -1)
			return false;
		
		$log_fileL = file($this->log_file);
		
		if (count($log_fileL) >= $this->log_limit)
		{
			$unset_line_count = count($log_fileL) - $this->log_limit;
			
			for ($i = 0; $i <= $unset_line_count; $i++)
			{
				unset($log_fileL[$i]);
			}
			
			$short_fp = fopen($this->log_file, 'w+') or exit('Konnte Log-Datei nicht öffnen: '.$this->log_file);
			fwrite($short_fp, implode('', $log_fileL));
			fclose($short_fp);
		}
	}
	
	public function deleteLog()
	{
		if (is_file($this->log_file))
		{
			if (unlink($this->log_file) or exit('Konnte Log-Datei nicht löschen: '.$this->log_file))
				return true;
		}
		else
			return false;
	}
	
	public function close()
	{
		fclose($this->fp);
	}
	
	private function open()
	{
		$this->fp = fopen($this->log_file, 'a+') or exit('Konnte Log-Datei nicht öffnen: '.$this->log_file);
	}
	
	private function cube($n)
	{
		return explode('~', $n);
	}
}

/* **************************************** */

class Cron
{
	private $cron_file, $cron_path = CRON_PATH, $interval, $files, $source = '';
	
	public function setFile($file = '')
	{
		$this->cron_file = $file;
	}
	
	public function setPath($path = '')
	{
		$this->cron_path = $path;
	}
	
	public function readFile()
	{
		if (empty($this->files))
			$this->allFiles();
		
		if (!empty($this->cron_file) && array_key_exists($this->cron_file, $this->files))
			$this->interval = $this->files[$this->cron_file];
	}
	
	public function ifExist()
	{
		if (empty($this->files))
			$this->allFiles();
		
		if (!empty($this->cron_file) && array_key_exists($this->cron_file, $this->files))
			return true;
		else
			return false;
	}
	
	public function getInterval()
	{
		if (empty($this->files))
			$this->allFiles();
		
		if (!empty($this->cron_file) && array_key_exists($this->cron_file, $this->files))
			return $this->files[$this->cron_file];
	}
	
	public function setInterval($interval = 60)
	{
		$this->interval = $interval;
	}
	
	public function save()
	{
		if ($this->source != '' && $this->interval != '' && $this->cron_file != '')
		{
			if (copy($this->source, $this->cron_path.'/'.$this->interval.'-'.$this->cron_file.'.php'))
			{
				$this->files = '';
				return true;
			}
			else
				return false;
		}
		else
			return false;
	}
	
	public function setSource($file = '')
	{
		$this->source = $file;
	}
	
	public function delete()
	{
		if (unlink($this->cron_path.'/'.$this->interval.'-'.$this->cron_file.'.php'))
		{
			$this->files = '';
			return true;
		}
		else
			return false;
	}
	
	public function getAllFiles()
	{
		if (empty($this->files))
			$this->allFiles();
		
		return $this->files;
	}
	
	private function allFiles()
	{
		if(!function_exists('cube'))
		{
			function cube($n)
			{
				$exp = explode('-', $n);
				$int = $exp[0];
				unset($exp[0]);
				
				return array($int, substr(implode('', $exp), 0, -4));
			}
		}
		
		$folder = $this->cron_path;
		$fileArray = array();
		
		foreach (@scandir($folder) as $file)
		{
			if ($file[0] != '.')
			{
				if (is_file($folder.'/'.$file) && $file != 'init.php')
				{
					$fileArray[] = $file;
				}
			}
		}
		
		foreach(array_map('cube', $fileArray) as $arr_map)
			$this->files[$arr_map[1]] = $arr_map[0];
	}
}
?>