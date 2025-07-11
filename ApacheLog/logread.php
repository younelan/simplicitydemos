<?php 
namespace Opensitez\Simplicity;
$DONTLOG=1;
$PHP_SELF=$_SERVER["PHP_SELF"];	
$base_dir=dirname(__file__);

require_once (dirname(__DIR__) . "/vendor/autoload.php");

require_once(__DIR__ . "/log_config.php");
$output = "";
function showAllGraphs($results,$framework)
{
    $loghelper = $framework->get_component("loghelper");
    $output = $loghelper->get("css");

    $output .= "<div class='graphs-container'>";
    $pieChart = $framework->get_component('piechartblock');
    $barChart = $framework->get_component('barchartblock');
    $vbarChart = $framework->get_component('vbarchartblock');
    $lineChart = $framework->get_component('linechartblock');
    
        foreach ($results ?? [] as $key => $data) {
            $title = $data['label'] ?? "Custom Graph";
            $type = $data['type'] ?? 'pie';
            $xlabel = $data['xlabel'] ?? '';
            $ylabel = $data['ylabel'] ?? '';
            $data['graphId'] = "customgraph_$key";
            $data['limit'] = 10;
            switch($type) {
                case 'pie':
                    $output .= $pieChart->render($data);
                    break;
                case 'bar':
                    $output .= $barChart->render($data);
                    break;
                case 'vbar':
                    $output .= $vbarChart->render($data);
                    break;
                case 'line':
                    $data = $results[$key] ?? [];
                    $data['xlabel'] = $xlabel;
                    $data['ylabel'] = $ylabel;
                    $output .= $lineChart->render($data);
                    break;
                default:
                case 'pie':
                    $output .= $pieChart->render($data);
                    break;

            }   
           

        }

    $output .= "</div>";
    return $output;
    }


require_once(__DIR__ . "/init.php");
//see if we want to filter out something
$filter_name = $_GET["filter_name"] ?? "host";
$filter_type = $_GET["filter_type"] ?? "like";
$filter_value = $_GET["filter_value"] ?? "";
$log_file_name = $_GET["log_file_name"] ?? "";

$log_folder = $config["paths"]["log"];
$log_files = [];
foreach (scandir($log_folder) as $file) {
    if (is_file("$log_folder/$file") && pathinfo($file, PATHINFO_EXTENSION) === 'log') {
        $log_files[pathinfo($file, PATHINFO_FILENAME)] = $file; // Use filename without extension as key
    }
}

$config_files = array_keys($log_files);
$default_file = $config["defaultfile"];

if (isset($log_files[$log_file_name])) {
    $log_file = $log_files[$log_file_name];
} elseif (empty($log_file_name)) {
    $log_file = $default_file; // Use default file if no file is specified
} elseif (!array_key_exists($log_file_name, $log_files)) {
    die("Error: Specified log file '$log_file_name' is not valid."); // Show error for invalid file names
} else {
    $log_file = $log_files[$log_file_name];
}

$navigation = "<a href=$PHP_SELF>Reset Filter</a>";

$navigation .= "
<p>Filter:
<form method=get action=\"" . $_SERVER["PHP_SELF"] . "\">
";

$navigation .= "<select name=\"filter_name\" value=\".  htmlentities($filter_name) . \"/>";
foreach($config["columns"] as $field=>$value)
{
	if(is_array($value))
		$tmp_filter_field=$value[1]?? $value[0]; // Use the second element if available, otherwise the first
	else
		$tmp_filter_field=$value;
		
	if($tmp_filter_field==$filter_name)
		 $navigation .= "   <option value='$tmp_filter_field' selected>$tmp_filter_field</option>\n";
	else
		 $navigation .= "   <option value='$tmp_filter_field' >$tmp_filter_field</option>\n";

}
$navigation .= "  </select> 
<select name='log_file_name' value='log_file_name'>
";


foreach($log_files as $field=>$value)
{
        if($log_file_name==$field) {
                 $navigation .= "   <option value='$field' selected>$field</option>\n";
		} else {
                $navigation .= "   <option value='$field' >$field</option>\n";
        }

}
$navigation .= "  </select>";

$navigation .= "	\n\n<select name='filter_type' /> \n";
foreach(array("like","exclude","ignore") as $tmp_filter_field)
{
	if($tmp_filter_field==$filter_type)
		$navigation .= "   <option value='$tmp_filter_field' selected>$tmp_filter_field</option>\n";
	else
		$navigation .= "   <option value='$tmp_filter_field' >$tmp_filter_field</option>\n";
}
$navigation .= "  </select>
   <input type=text name=filter_value value=\"" . htmlentities($filter_value) . "\"/>
  <input type=submit />
  </form>
</p>
<p><hr /></p>
";
//print($navigation);

$enginestotal=0; $regulartotal=0;
$fullpaths = [];
if (!is_array($log_file)) {
    $log_file = [$log_file]; // Ensure log_file is an array
}
$log_folder = $config["paths"]["log"];
foreach ($log_file as $file) {
    if (is_file("$log_folder/$file")) {
        $fullpaths[] = "$log_folder/$file";
    } else {
        print("Error: Log file '$log_folder/$file' does not exist in the log folder.");
    }
}
//print $fullpath . "<br>";
//$config["file"] = $fullpath;
$config_object->set("file", $fullpaths);

$mylog = $framework->get_component("loghelper");
$mylog->init();
if (!$mylog) {
    die("Error: Log reader plugin not found.");
}

if(strlen($filter_value)>0)	$mylog->setFilter($filter_name,$filter_type,$filter_value);

$output .= "<div>";
$show_navigation = $config["show_navigation"] ?? false;


$mylog->setColumns(  $config["columns"] );
$mylog->setCustomGraphs( $config["customgraphs"] );
$mylog->loadRules("engines.txt",'engines');
$mylog->loadRules("families.txt",'families');
$mylog->parseLog();
$results = $mylog->get("results");

if($show_navigation)
{
    $output .= "<p>Navigation: " . $navigation . "</p>";
}


$output .= showAllGraphs($results,$framework);

$output .= "</div>\n";
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<?php
$show_logs = $config["show_logs"] ?? false;
print $output;
if($show_logs) {

    $logView = $framework->get_component('logviewblock');
    echo $logView->render([
        'log_entries' => $mylog->get("filtered_log"),
        'columns' => $mylog->get("columns"),
        'filter_count' => $mylog->get("filter_count") ?? 0,
        'color_cycle' => $mylog->get('colorCycle') ?? $mylog->get("defaultcolors")
    ]);
}
?>

</body>
</html>

