<?php
/******************************************************************
*			       		Your Miner					  	          *
*					A: Sven Goessling							  *
*					W: Miner-Control.de							  *
*						V: 1.0.0								  *
******************************************************************/

class LocalMiner {
  private $socket;

  private $addr, $port;
  private $version, $summary;

  function __construct($addr, $port) {
    $this->addr = $addr;
    $this->port = $port;
  }

  /* VERSION */

  function version() {
    if ($this->version == null)
      $this->version = $this->request(array('command' => 'version', 'parameter' => ''));

    return 'v' . $this->version->VERSION[0]->CGMiner;
  }

  function apiversion() {
    if ($this->version == null)
      $this->version = $this->request(array('command' => 'version', 'parameter' => ''));

    return 'v' . $this->version->VERSION[0]->API;
  }

  /* SUMMARY */

  function uptime() {
    if ($this->summary == null)
      $this->summary = $this->request(array('command' => 'summary', 'parameter' => ''));

    return $this->summary->SUMMARY[0]->Elapsed;
  }

  function hashrate() {
    if ($this->summary == null)
      $this->summary = $this->request(array('command' => 'summary', 'parameter' => ''));
    return array('Average' 	=> $this->summary->SUMMARY[0]->{'MHS av'}, 
				 '5s' 		=> $this->summary->SUMMARY[0]->{'MHS 5s'},
				 'Accepted' => $this->summary->SUMMARY[0]->{'Accepted'},
				 'Rejected' => $this->summary->SUMMARY[0]->{'Rejected'},
				 'Stale' 	=> $this->summary->SUMMARY[0]->{'Stale'}
				 );
  }

  function shares() {
    if ($this->summary == null)
      $this->summary = $this->request(array('command' => 'summary', 'parameter' => ''));

    $total = $this->summary->SUMMARY[0]->Accepted + $this->summary->SUMMARY[0]->Rejected + $this->summary->SUMMARY[0]->Stale;

    if ($total == 0)
      $percent = 0;
    else
      $percent = round($this->summary->SUMMARY[0]->Accepted / $total * 100, 2);

    if ($percent > 95)
      $this->summary->SUMMARY[0]->alert = 'success';
    else if ($percent > 90)
      $this->summary->SUMMARY[0]->alert = 'warning';
    else
      $this->summary->SUMMARY[0]->alert = 'danger';

    $this->summary->SUMMARY[0]->percentage = $percent;

    return $this->summary->SUMMARY[0];
  }

  function devices() {
    return $this->request(array('command' => 'devs', 'parameter' => ''))->DEVS;
  }

  function pools() {
    return $this->request(array('command' => 'pools', 'parameter' => ''))->POOLS;
  }
  
  function restart() {
    return $this->request(array('command' => 'restart', 'parameter' => ''));
  }
  
  function switchpool($pool_id) {
	return $this->request(array('command' => 'switchpool', 'parameter' => $pool_id));
  }
  function addpool($url, $user, $pass) {
	  return $this->request(array('command' => 'addpool', 'parameter' => 'stratum+tcp://'.$url.','.$user.','.$pass));
  }
  function removepool($pool_id) {
	  return $this->request(array('command' => 'removepool', 'parameter' => $pool_id));
  }
  
  
  function stats($pool_id) {
	return $this->request(array('command' => 'stats', 'parameter' => $pool_id));
  }
  function allinfos() {
	return $this->summary = $this->request(array('command' => 'summary', 'parameter' => ''));
  }
  function coin() {
	  return $this->coin = $this->request(array('command' => 'coin' , 'parameter' => ''))->COIN;
  }
  function config() {
	return $this->config = $this->request(array('command' => 'config' , 'parameter' => ''))->CONFIG;
  }

  /* Private functions */

  private function getsock() {
    $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

    if ($this->socket === false || $this->socket === null || socket_connect($this->socket, $this->addr, $this->port) === false) {
      return false;
    }

    return true;
  }

  # Slow ...
  private function readsockline() {
    $line = '';
    while (true) {
      $byte = socket_read($this->socket, 1);
      if ($byte === false || $byte === ''|| $byte === "\0")
        break;
      $line .= $byte;
    }
    return $line;
  }

  private function request($request) {
    if (!array_key_exists('command', $request) || !array_key_exists('parameter', $request))
      return null;
    if ($this->socket == null && !$this->getsock())
      return null;

    $cmd = json_encode($request);
    socket_write($this->socket, $cmd, strlen($cmd));

    $line = $this->readsockline();

    socket_close($this->socket);
    $this->socket = null;

    return json_decode($line);
    //return $line;
  }
}
?>