<?php 
namespace Opensitez\Simplicity;
$DONTLOG=1;
$PHP_SELF=$_SERVER["PHP_SELF"];	
$base_dir=dirname(__file__);

require_once (dirname(__DIR__) . "/vendor/autoload.php");

use Opensitez\Simplicity\LogHelper;

require_once(__DIR__ . "/log_config.php");

//see if we want to filter out something
$filter_name = $_GET["filter_name"] ?? "host";
$filter_type = $_GET["filter_type"] ?? "like";
$filter_value = $_GET["filter_value"] ?? "";
$log_file_name = $_GET["log_file_name"] ?? "";

$log_folder = $config["paths"]["log"];
$log_files = [];
foreach (scandir($log_folder) as $file) {
    if (is_file("$log_folder/$file") && pathinfo($file, PATHINFO_EXTENSION) === 'txt') {
        $log_files[pathinfo($file, PATHINFO_FILENAME)] = $file;
    }
}

if (isset($log_files[$log_file_name])) {
    $log_file = $log_files[$log_file_name];
} elseif (!empty($log_files)) {
    $log_file = reset($log_files); // Use the first file in the array
} else {
    $log_file = $config["file"]; // Default filename from log_config
}

print("<a href=$PHP_SELF>Reset Filter</a>");

?>
<p>Filter:
<form method=get action="<?php echo $_SERVER["PHP_SELF"] ?>">  

<?php 
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
                 $navigation .= "   <option value='$value' selected>$field</option>\n";
		} else {
                $navigation .= "   <option value='$value' >$field</option>\n";
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
print($navigation);


$enginestotal=0; $regulartotal=0;
$fullpath = $config["paths"]["log"] . "/" . $log_file;
//print $fullpath . "<br>";
$config["file"] = $fullpath;
$mylog = new LogHelper($config);

if(strlen($filter_value)>0)	$mylog->setFilter($filter_name,$filter_type,$filter_value);

$mylog->setColumns(  $config["columns"] );
$mylog->setCustomGraphs( $config["customgraphs"] );

$mylog->loadRules("engines.txt");
$mylog->loadRules("families.txt",'families');
$mylog->parseLog();
$mylog->showGraphs();
print("<p>\n");
$mylog->showCustomGraphs();
print("<p>\n");
$mylog->printLog();
