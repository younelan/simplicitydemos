<?php 
namespace Opensitez\Simplicity;
$DONTLOG=1;
$PHP_SELF=$_SERVER["PHP_SELF"];	
$base_dir=dirname(__file__);

require_once (dirname(__DIR__) . "/vendor/autoload.php");

require_once(__DIR__ . "/log_config.php");

function showAllGraphs($results,$framework)
{
    $loghelper = $framework->get_plugin("loghelper");
    $output = $loghelper->getCss();

    $output .= "<div class='graphs-container'>";
    $pieChart = $framework->get_plugin('piechartblock');
    $barChart = $framework->get_plugin('barchartblock');
    $vbarChart = $framework->get_plugin('vbarchartblock');
    $lineChart = $framework->get_plugin('linechartblock');
    
        // Custom graphs - check type, default to pie
        foreach ($results ?? [] as $key => $data) {
            $title = $data['label'] ?? "Custom Graph";
            $type = $data['type'] ?? 'pie';
            $xlabel = $data['xlabel'] ?? '';
            $ylabel = $data['ylabel'] ?? '';

            //$data =  $this->results[$key] ?? [];
            // $data['title'] = $title;
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
    $log_file = $log_files[pathinfo($default_file, PATHINFO_FILENAME)] ?? $default_file; // Use default file if no file is specified
} elseif (!array_key_exists($log_file_name, $log_files)) {
    die("Error: Specified log file '$log_file_name' is not valid."); // Show error for invalid file names
} else {
    $log_file = $log_files[$log_file_name];
}

$output = "<a href=$PHP_SELF>Reset Filter</a>";

$output .= "
<p>Filter:
<form method=get action=\"" . $_SERVER["PHP_SELF"] . "\">
";

$navigation = "<select name=\"filter_name\" value=\".  htmlentities($filter_name) . \"/>";
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
$fullpath = $config["paths"]["log"] . "/" . $log_file;
//print $fullpath . "<br>";
//$config["file"] = $fullpath;
$config_object->set("file", $fullpath);

$mylog = $framework->get_plugin("loghelper");
$mylog->init();
if (!$mylog) {
    die("Error: Log reader plugin not found.");
}

if(strlen($filter_value)>0)	$mylog->setFilter($filter_name,$filter_type,$filter_value);

$mylog->setColumns(  $config["columns"] );
$mylog->setCustomGraphs( $config["customgraphs"] );

$mylog->loadRules("engines.txt",'engines');
$mylog->loadRules("families.txt",'families');
$mylog->parseLog();
$output .= "<div>";
$results = $mylog->getResults();

if($show_navigation??true)
{
    $output .= "<p>Navigation: " . $navigation . "</p>";
}


$output .= showAllGraphs($results,$framework);

$output .= "</div>\n";
print $output;
//$mylog->showCustomGraphs();
//print("<p>\n");
//$mylog->printLog();

