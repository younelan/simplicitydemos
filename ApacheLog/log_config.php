<?php
$basedir = dirname(__FILE__);
$config = [
    "show_navigation" => false,
    "trim-lead-www" => true,
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
    "defaultfile" => ["access.log","ssl_access.log","ssl_access_1.log", "access_1.log"],
    "logfolder" => "$basedir/logs",
    "customgraphs" => [
         "path" => ["field" => "path", "label" => "Path", "type" => "vbar"],
         "referer" => ["field" => "referer", "label" => "Referer"],
         "vhost"=> ["field" => "host", "label" => "Virtual Host"],
         "port"=> ["field" => "port", "label" => "Port"],
         "verb" => ["field" => "verb", "label" => "Method"],
        "protocol" => ["field" => "protocol", "label" => "Protocol"],
        "status_code"=> ["field" => "status_code", "label" => "Status Code"],
         "ip" => ["field" => "ip", "label" => "IP Address"],
         "day"=> ["field" => "day", "label" => "Date"],
         "hour"=> ["field" => "hour", "label" => "Hour", "type" => "vbar"],
         "statperhour" => [
            "x" => 'hour',
            "y" => 'status_code', 
            "type" => "line", 
            "label" => "Busiest Hours",
            "xlabel" => "Hour", "ylabel" => "Visits",
            "key-synonyms" => [
                "404" => "Not Found",
                "200" => "OK",
                "500" => "Server Error",
                "403" => "Forbidden",
                "301" => "Moved Permanently",
                "206" => "Partial Content"
            ]

        ],
         "daily" => [
            "x" => 'day',
            "y" => 'status_code', 
            "type" => "line", 
            "label" => "Daily Visits",
            "xlabel" => "Day", "ylabel" => "Visits"
        ],
    ]
];


$config["columns"]=array(["date"],["ip"],["hostname"],["path"],["referer"],["agent"],["host"]);
$config["columns"]=array(0=>["date"],6=>["host"],3=>["path"],2=>["hostname"],4=>["referer"],1=>["ip"],5=>["agent"]);