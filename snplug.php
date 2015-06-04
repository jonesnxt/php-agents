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

	public function get_API_nxt64bits($obj)
	{
		$nxt64bits = 0;
		$buf = array();
		if( $obj != 0)
		{
			return $obj->valuedouble;
		}
		return calc_nxt64bits($obj);
	}


	public function calc_nxt64bits($NXTaddr)
	{
	    $nxt64bits = 0;
	    if ( $NXTadr == 0 )
	    {
	        echo "calling calc_nxt64bits with null ptr!\n";
	        return 0;
	    }
	    $n = strlen($NXTaddr);
	    if ( $n >= 22 )
	    {
	        echo "calc_nxt64bits: illegal NXTaddr.(".$NXTaddr.") too long\n";
	        return 0;
	    }
	    else if ( strcmp($NXTaddr,"0") == 0 || strcmp($NXTaddr,"false") == 0 )
	    {
	        // printf("zero address?\n"); getchar();
	        return 0;
	    }
	    $mult = 1;
	    $lastval = 0;
	    for ($i=$n-1; $i>=0; $i--,$mult*=10)
	    {
	        $c = $NXTaddr[$i];
	        if ( $c < '0' || $c > '9' )
	        {
	            echo "calc_nxt64bits: illegal char.(".$c." ".$c.") in (".$NXTaddr.").".$i."\n";
	            return 0;
	        }
	        $nxt64bits += $mult * ($c - '0');
	        if ( $nxt64bits < $lastval )
	            echo "calc_nxt64bits: warning: 64bit overflow ".$nxt64bits." < ".$lastval."\n";
	        $lastval = $nxt64bits;
	    }
	    if ( cmp_nxt64bits($NXTaddr,$nxt64bits) != 0 )
	        echo "error calculating nxt64bits: ".$NXTaddr." -> ".$nxt64bits."\n";
	    return $nxt64bits;
	}


	public function cmp_nxt64bits($str,$nxt64bits)
	{
	    $expanded = array();
	    if ( $str == 0 )//|| str[0] == 0 || nxt64bits == 0 )
	        return -1;
	    if ( $nxt64bits == 0 && $str[0] == 0 )
	        return 0;
	    $expanded = expand_nxt64bits($nxt64bits);
	    return strcmp($str,$expanded);
	}

	public function expand_nxt64bits($nxt64bits)
	{
	    $rev = array();
	    $NXTaddr = array();
	    for ($i=0; $nxt64bits!=0; i++)
	    {
	        $modval = $nxt64bits % 10;
	        $rev[i] = ($char)($modval + '0');
	        $nxt64bits /= 10;
	    }
	    $n = $i;
	    for ($i=0; $i<$n; $i++)
	        $NXTaddr[$i] = $rev[$n-1-$i];
	    $NXTaddr[$i] = 0;
	    return $NXTaddr;
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

class Agent {

	public function init_pluginsocks(plugin_info $plugin, $permanentflag, $bindaddr, $connectaddr, $instanceid, $daemonid, $timeout)
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

	public function process_json($retbuf, $max, plugin_info $plugin, $jsonargs, $initflag)
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
			if($obj != 0)
			{
				$nxt64bits = get_API_nxt64bits($obj->NXT);
				if($nxt64bits != 0)
				{
					$plugin->nxt64bits = $nxt64bits;
					$plugin->NXTADDR = expand_nxt64bits($plugin->nxt64bits);
				}
				$tag = get_API_nxt64bits($obj->tag);
				if($initflag > 0)
				{
					$myidaddr = $obj->ipaddr;
					if(is_ipaddr($myipaddr))
					{
						$plugin->ipaddr = $myipaddr;
					}
					$plugin->port = getAPI_int($obj->port), 0);
				}
			}

			if($jsonstr != 0 && $obj != 0)
			{
				$retval = process_json($plugin, $tag, $retbuf, $max, $jsonstr, $obj, $initflag);
			}
			else echo "error with JSON.(".$jsonstr.")\n";
			if($jsonstr != 0)
			{
				$jsonstr = undefined;
			}
			if($json != 0)
			{
				$json = undefined;
			}
			echo $retbuf;
			return $retbuf;
		}

		public function append_stdfields($retbuf, $max, $plugin, $tag, $allfields)
		{
			$tagstr = array();
			$json = json_decode($retbuf);
			if(json_last_error() == JSON_ERROR_NONE)
			{
				if($tag != 0 && get_API_nxt64bits($json->tag) == 0)
				{
					echo ",\"tag\":\"".$tag."\n}";
				}
				else $tagstr[0] = 0;
				$NXTaddr = $json->NXT;
				if($NXTaddr == 0 || $NXTaddr[0] == 0)
				{
					$NXTaddr = $SUPERNET->NXTADDR;
				}
				echo ",\"NXT\":\"".$SUPERNET->NXTADDR."\"}";
				if($allfields != 0)
				{
					if($SUPERNET->iamrelay != 0)
					{
						echo ",\"myipaddr\":\"".$plugin->ipaddr."\"}";
					}
					echo ",\"allowremote\":".$plugin->allowremote.$tagstr."\"}";
            		echo ",\"permanentflag\":".$plugin->permanentflag.",\"myid\":\"".$plugin->myid."\",\"plugin\":\"".$plugin->name."\",\"endpoint\":\"".."\",\"millis\":".milliseconds().",\"sent\":".$plugin->numsent.",\"recv\":".$plugin->numreciv."}";
		        }
		        else sprintf(retbuf+strlen(retbuf)-1,",\"allowremote\":%d%s}",plugin->allowremote,tagstr);
				}
			}
		}

		

	}


?>