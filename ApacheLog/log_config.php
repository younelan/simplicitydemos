<?php
$basedir = dirname(__FILE__);
$config = [
    "paths"=> [
        "basedir" => $basedir,
        "log" => "$basedir/logs",
        "engine_file" => "$basedir/config/engines.yaml", // Update to YAML file
        "customgraphs" => "$basedir/customgraphs"
    ],
    "columns" => [
        ["date", "Date"],
        ["ip", "IP Address"],
        ["hostname", "Hostname"],
        ["path", "Path"],
        ["referer", "Referer"],
        ["agent", "User Agent"]
    ],
    "defaultfile" => "access.log",
    "logfolder" => "$basedir/logs",
    "customgraphs" => [
         "path" => ["field" => "path", "label" => "Path"],
         "agent" => ["field" => "agent", "label" => "User Agent"],
         "hostname" => ["field" => "hostname", "label" => "Hostname"],
         "referer" => ["field" => "referer", "label" => "Referer"],
         "vhost"=> ["field" => "vhost", "label" => "Virtual Host"],
         "verb" => ["field" => "verb", "label" => "Method"],
         "ip" => ["field" => "ip", "label" => "IP Address"],
         "day"=> ["field" => "day", "label" => "Date"],
         "hour"=> ["field" => "hour", "label" => "Hour"]
    ]
];


$config["columns"]=array(["date"],["ip"],["hostname"],["path"],["referer"],["agent"],["host"]);
$config["columns"]=array(0=>["date"],6=>["host"],3=>["path"],2=>["hostname"],4=>["referer"],1=>["ip"],5=>["agent"]);