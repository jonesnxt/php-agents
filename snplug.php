<?php 
// derived from plugin777.c
// SuperNET API Extension
// crypto777

// Ported By Jones on 28/5/15

use NanoMsg/Socket as NanoSocket;
use NanoMsg/Nano as Nano;


class sendendpoints
{
	public $push;
	public $rep;
	public $pub;
	public $survey;
}

class recvendpoints
{
	public $pull;
	public $req;
	public $sub;
	public $respond;
}

class biendpoints
{
	public $bus;
	public $pair;
}

class allendpoints
{
	public $send;
	public $recv;
	public $both;
}

class endpoints
{
	public $all = array();
	public $socks; // allendpoints;
}

class plugin_info
{
	public $binaddr; // 64 character
	public $connectaddr; // 64 char
	public $ipaddr; // 64 char
	public $name; // 64 char;
	public $NXTADDR; // 64 char;

	public $daemonid; // 64bit int;
	public $myid; // 64 bit int
	public $nxt64bits // 64 bit int

	public $all; // endpoints type
	public $permanentflag,$ppid,$transportid,$extrasize,$timeout,$numrecv,$numsend,$bundledflag,$registered,$sleepmillis,$allowremote; // 32 bits int
	public $port; // 16 bit int;
	public $pluginspace = array(); // 8 bit array;
}
	
function plugin_result($json, $tag)
{
	$json = json_decode($json);
	$error = $json->error;
	$result = $json->result;
    if ( $error != 0 || $result != 0 )
    {
        echo '{"result":"completed","tag":"'.$tag.'"}';
        return 1;
    }
    return 0;
}

function init_pluginsocks(plugin_info $plugin, $permanentflag, $bindaddr, $connectaddr, $instanceid, $daemonid, $timeout)
{
	$errs = 0;
	$socks = $plugin->all->socks;
	if($DebugLevel > 2)
	{
		echo $plugin->name.".".$plugin." <<<<<<<<<<<<<<< init_permpairsocks bind.(".$bindaddr.") connect. (".$connectaddr.")\n";
	}
	$socks->both->pair = init_socket(".pair","pair",$NN_PAIR, 0, $connectaddr, $timeout)) < 0) $errs++;

	return $errs;
}

function process_json($retbuf, $max, plugin_info $plugin, $jsonargs, $initflag)
{
	// string
	$filename;
	$myipaddr;
	$jsonstr=0;
	// json
	$obj = STDClass();
	$json = STDClass();
	// int64s
	$allocsize;
	$nxt64bits;
	$tag=0;
	if($jsonargs != 0)
	{
		$json = json_decode($jsonargs);
		if(json_last_error() == JSON_ERROR_NONE)
		{
			if(is_array($json) && count($json) == 2)
			{
				$obj = $json[0];
			} 
			else $obj = $json;
			$filename = $obj->filename;
			if($filename[0] != 0)
			{
				$jsonstr = file_get_contents($filename);
				if(strlen($jsonstr) != 0)
				{
					$tmp = json_decode($jsonstr);
					if(json_last_error() == JSON_ERROR_NONE)
					{
						$obj = $tmp;
					}
					else $jsonstr = 0;
				}
			}
			if($jsonstr == 0)
			{
				$jsonstr = json_encode($obj);
			}
		}
	}

	

}


?>