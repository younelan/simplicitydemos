<?php
$basedir = dirname(__FILE__);
$config = [
    "paths"=> [
        "basedir" => $basedir,
        "log" => "$basedir/logs",
        "engines" => "$basedir/engines",
        "customgraphs" => "$basedir/customgraphs"
    ],
    "columns" => [
        "date" => "Date",
        "ip" => "IP Address",
        "hostname" => "Hostname",
        "path" => "Path",
        "referer" => "Referer",
        "agent" => "User Agent"
    ],
    "file" => "visitors.txt",
    "logfolder" => "$basedir/logs",
    "engines"=>"engines.txt",
    "customgraphs" => array(
        "ip" => "IP Address",
        "path" => "Path",
        "agent" => "User Agent",
        "hostname" => "Hostname",
        "referer" => "Referer"
    )
];


