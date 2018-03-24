#!/usr/bin/php
<?php

include("../phpagi.php");
$agi = new AGI;

$agi->verbose("Consulting the Overlord...");

include "db_info.php";
$db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

$DDI=$argv[1];
$UNIQUEID = $argv[2];
$CALLERID = $argv[3];
$FULLIVRINPUT = $argv[4];
$ENTRYPOINT = $argv[5];
$COMMANDSTATUS = $argv[6];

if (!db) {
        $agi->verbose("Can not connect to MySQL: {$db->connect_error}");
        $agi->set_variable('WHATNEXTRESULT', 'NODATABASE');
} else {
	$sql = "call WhatNextOverlord('{$DDI}', '{$UNIQUEID}', '{$CALLERID}', '{$FULLIVRINPUT}', '{$ENTRYPOINT}', '{$COMMANDSTATUS}')";
        $agi->verbose("${sql}");
        $res = $db->query($sql);
	if ($res) {
		if ($res->num_rows > 0) {
			$answer = $res->fetch_object();
			$agi->set_variable('COMMAND', $answer->command);
                        $agi->set_variable('VOICEFILENAME', $answer->voicefilename);
                        $agi->set_variable('NUMERICVALUE', $answer->numericvalue);
                        $agi->set_variable('BREAKOUTNUMBER', $answer->breakoutnumber);
                        $agi->set_variable('WHATNEXTRESULT', 'OK');

                        $agi->verbose("COMMAND: ".$answer->command);
                        $agi->verbose("VOICEFILENAME: ".$answer->voicefilename);
                        $agi->verbose("NUMERICVALUE: ".$answer->numericvalue);
                        $agi->verbose("BREAKOUTNUMBER: ".$answer->breakoutnumber);
                        $agi->verbose("WHATNEXTRESULT: OK");
                } else {
                        $agi->verbose("The overlord did not answer");
                        $agi->set_variable('WHATNEXTRESULT', 'NOANSWER');
                }
		$res->free();
        } else {
                $agi->verbose("Overlord Query failed: {$db->error}");
                $agi->set_variable('WHATNEXTRESULT', 'NOQUERY');
        }
	$db->close();
}

$agi->verbose("...The Overlord has spoken!");

?>
