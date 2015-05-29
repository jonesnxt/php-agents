<?php 
// derived from plugin777.c
// SuperNET API Extension
// crypto777

// Ported By Jones on 28/5/15

use NanoMsg/Socket as NanoSocket;
use NanoMsg/Nano as Nano;

class plugin_info
{
	$
}
	
function plugin_result($retbuf, $json, $tag)
{

}

function init_pluginsocks(plugin_info $plugin, $permanentflag, $bindaddr, $connectaddr, $instanceid, $daemonid, $timeout)
{
	$errs = 0;
	$socks = $plugin->all->socks;
	if($DebugLevel > 2)
	{
		echo $plugin->name.".".$plugin." <<<<<<<<<<<<<<< init_permpairsocks bind.(".$bindaddr.") connect. (".$connectaddr.")\n";
	}
}


?>