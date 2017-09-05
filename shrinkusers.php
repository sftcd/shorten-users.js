<?php

/* 
 * Copyright (c) 2017 Tolerant Networks Limited
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

function merge_elems(&$accum,$latest)
{
	// elem will be the decoded varsion of something like this
	// {"mac":"xx:xx:xx:xx:xx:xx","ip":"","ip6":"2001:bb6:5e02:ef00:4df7:4c32:a9ed:9de7,2001:bb6:5e02:ef00:e4b6:cff6:d20a:49,2001:bb6:5e02:ef00:7952:e65e:4435:5403,2001:bb6:5e02:ef00:e9c5:9c62:78a3:1578,2001:bb6:5e02:ef00:20f5:7a6:1d03:f17,2001:bb6:5e02:ef00:6cc3:90ac:adbe:b105,fe80::c8cd:51ae:81ba:421c,2001:bb6:5e02:ef00:68ab:3c9a:eec1:2d26,2001:bb6:5e02:ef00:9ce9:adf0:c1ff:83ea","owner":"Unknown","name":"newhp","colour":"","added":"2017-09-05 19:33:27","updated":"2017-09-05 19:33:27","last-seen":"2017-09-05 19:33:27"}
	// return the best of both, where the MACs are the same
	if ($accum['mac']!=$latest['mac']) return(1); 
	if ($latest['ip']!="" && $accum['ip']!=$latest['ip']) {
		// merge lists of even IPv4 addresses to a unique list
		// can see >1 addr, e.g. due to config changes, even if rare
		$a1=explode(",",str_replace(" (dup)","",$accum['ip']));
		$a2=explode(",",str_replace(" (dup)","",$latest['ip']));
		$a3=array_merge($a1,$a2);
		$a4=array_unique($a3);
		$a5=implode(",",$a4);
		$accum['ip']=$a5;
	}
	if ($latest['ip6']!="" && $accum['ip6']!=$latest['ip6']) {
		// merge lists of IPv6 addresses to a unique list
		$a1=explode(",",str_replace(" (dup)","",$accum['ip6']));
		$a2=explode(",",str_replace(" (dup)","",$latest['ip6']));
		$a3=array_merge($a1,$a2);
		$a4=array_unique($a3);
		$a5=implode(",",$a4);
		$accum['ip6']=$a5;
	}
	if ($latest['owner']!="Unknown" && $accum['owner']!=$latest['owner']) {
		$accum['owner']=$latest['owner'];
	}
	if ($latest['name']!="Unknown" && $accum['name']!=$latest['name']) {
		$accum['name']=$latest['name'];
	}
	if ($latest['colour']!="" && $accum['colour']!=$latest['colour']) {
		$accum['colour']=$latest['colour'];
	}
	// we don't overwrite added timestamp
	if ($latest['updated']!="" && $accum['updated']!=$latest['updated']) {
		$accum['updated']=$latest['updated'];
	}
	if ($latest['last-seen']!="" && $accum['last-seen']!=$latest['last-seen']) {
		$accum['last-seen']=$latest['last-seen'];
	}
	return(0);
}

function accumulate(&$set,$elem)
{

	// check if we've seen MAC before
	// Note: MAC address randomisation will come soon, so trying to 
	// prepare for that here, for now though, we expect the MAC to
	// be the main identifier
	$index=0;
	$seen=false;
	foreach($set as &$selem){
		if ($selem['mac']==$elem['mac']) {
			$seen=true;
			// just replace for now, merge in a bit
			merge_elems($selem,$elem);
		}
	}
	if (!$seen) {
		// add to end
		$set[]=$elem;
	}


	return(0);
}

function shrinkusers($file)
{
	$set=array();
	$fp=fopen($file,"r");
	// read and output first 2 lines as-is
	$line=fgets($fp);
	print $line;
	$line=fgets($fp);
	print $line;
	// read and process each line
	$lc=0;
	$line=fgets($fp);
	while (!feof($fp)) {
		// remove leading ("ud_a(" and trailing ")\n" stuff around json
		$len=strlen($line);
		//print "length: $len\n";
		$bline=substr($line,5,$len-7);
		//print "$bline\n";
		$lstruct=json_decode($bline,true);
		$rv=accumulate($set,$lstruct);
		// deliberately ignore errors here I think
		//var_dump($lstruct);
		$lc++;
		$line=fgets($fp);
	}
	print "read $lc lines\n";
	fclose($fp);
	// print out $set
	var_dump($set);
	return(0);
}

if ($argc!=2) {
	print "usage: $argv[0] <users.js>\n";
	print "\toutput is shrunken version of same\n";
	exit(1);
}

$ufile=$argv[1];
if (!file_exists($ufile)) {
	print "Can't read $ufile dude, try again\n";
	exit(2);
}

$rv=shrinkusers($ufile);
if ($rv!=0) {
	print "shrinkusers($ufile) returned $rv. Bummer.\n";
	exit(3);
}
print "shrinkusers($ufile) worked (I think:-)\n";

?>
