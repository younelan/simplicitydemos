<?php 
namespace Opensitez\Simplicity;

$vendor_dir = dirname(__DIR__) . "/vendor";
$simplicity_dir = dirname(dirname(__DIR__)) . "/src";
require_once("$vendor_dir/autoload.php");

use Opensitez\Simplicity\LogHelper;

require_once("log_config.php");


//see if we want to filter out something
$filter_name = $_GET["filter_name"] ?? "host";
$filter_type = $_GET["filter_type"] ?? "like";
$filter_value = $_GET["filter_value"] ?? "";
$log_file_name = $_GET["log_file_name"] ?? "";

// isset($_GET["filter_name"])? $filter_name=$_GET["filter_name"]:$filter_name="host";
// isset($_GET["filter_type"])? $filter_type=$_GET["filter_type"]:$filter_type="like";
// isset($_GET["filter_value"])? $filter_value=$_GET["filter_value"]:$filter_value="";
// isset($_GET["log_file_name"])? $log_file_name=$_GET["log_file_name"]:$log_file_name="";

$log_folder = $config["paths"]["log"];
$log_files = [];
foreach (scandir($log_folder) as $file) {
    if (is_file("$log_folder/$file")) {
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

print("<a href=" . $_SERVER["PHP_SELF"] .">Reset Filter</a>");

?>

<p>Filter:
<form method=get action="<?php echo $_SERVER["PHP_SELF"] ?>">  <select name="filter_name" value="<?php print htmlentities($filter_name) ?>"/>
<?php 
foreach($config["columns"] as $field=>$value)
{
	if(is_array($value))
		$tmp_filter_field=$field;
	else
		$tmp_filter_field=$value;
		
	if($tmp_filter_field==$filter_name)
		 echo("   <option value='$tmp_filter_field' selected>$tmp_filter_field</option>\n");
	else
		 echo("   <option value='$tmp_filter_field' >$tmp_filter_field</option>\n");

}
print("  </select> <select name='log_file_name' value='log_file_name'>");

foreach($log_files as $field=>$value)
{
        if($log_file_name==$field)
                 echo("   <option value='$field' selected>$field</option>\n");
        else
                 echo("   <option value='$field' >$field</option>\n");

}
print("  </select>");

print("	\n\n<select name='filter_type' /> \n");
foreach(array("like","exclude","ignore") as $tmp_filter_field)
{
	if($tmp_filter_field==$filter_type)
		 echo("   <option value='$tmp_filter_field' selected>$tmp_filter_field</option>\n");
	else
		 echo("   <option value='$tmp_filter_field' >$tmp_filter_field</option>\n");
}
print("  </select>");
?>
   <input type=text name=filter_value value="<?php print htmlentities($filter_value) ?>"/>
  <input type=submit />
  </form>
</p>
<p><hr /></p>
<?php


$enginestotal=0; $regulartotal=0;

 
$fullpath = $config["paths"]["log"] . "/" . $log_file;
//print $fullpath . "<br>";
$mylog = new LogHelper($fullpath);

if(strlen($filter_value)>0)	$mylog->setFilter($filter_name,$filter_type,$filter_value);

$mylog->setColumns(  $config["columns"] );
$mylog->setCustomGraphs( $config["customgraphs"] );

$mylog->loadRules("engines.txt");
$mylog->parseLog();
$mylog->showGraphs();
print("<p>");
$mylog->showCustomGraphs();
print("<p>");
$mylog->printLog();
 exit;
 $engines=array("google.com"=>"Google","googlebot.com"=>"Google","yahoo.com"=>"Yahoo!","yahoo.net"=>"Yahoo!","as13448.com"=>"AS 13448","onlinehome-server.com"=>"Online homeserver","onlinehome-server.info"=>"Online homeserver","theplanet.com"=>"The planet",
				"webazilla.com"=>"Webazilla","msn.com"=>"Bing","whois.sc"=>"whois.sc","amazonaws.com"=>"Amazon aws","yandex.ru"=>"Yandex","baidu.com"=>"Baidu","elandaloussi.net"=>"Self");
  $engine_referers= array("Wget"=>"wget","webspider"=>"Soso Spider","example\/1.0"=>"g7.in","larbin"=>"larbin","majestic12\.co"=>"Majestic","bingbot"=>"Bing",
						  "marketwirebot"=>"Market Wire",
						  "libwww-perl"=>"Lib www","tencenttraveler"=>"Tencent Traveler","fairshare"=>"Fairshare","Baiduspider"=>"Baidu");
 
 include_once("domain_helper.php");
 include_once("pagerank.php");
 
$i=0;
//$ranks=updateranks($domaincount);
//print_r($ranks);

//print_ranks($domaincount,$ranks)

function print_ranks($domaincount,$ranks)
{
	foreach($domaincount as $mydomain => $total)
	{
		$i++;
		$mydomain=trim($mydomain);
		if($i<5 && $mydomain<>"")
		{
			//print "<br>Domain: '$mydomain' " . getpr($mydomain);
			if(isset($ranks[$mydomain]))  
				$pagerank=$ranks[$mydomain];  
			else 
				$pagerank="";	
		}
		else
			$pagerank="";
		if($pagerank>0) $pagerank="<font color=green>$pagerank</font>";	
		print "<tr><td>$i</td><td>$mydomain</td><td>$total</td><td>" . $pagerank . "</td></tr>";
	}
}

function updateranks($rankset)
{
	$i=0;
	foreach($rankset as $mydomain => $total)
	{
		$i++;
		if($i<7)
		{
			$rank=getpr($mydomain);
			$ranks[$mydomain]['rank']=$rank;
			$ranks[$mydomain]['is']=$i;
		}
	}
	return $ranks;
}
/*foreach($hostcount as $host => $total) 
{
  	asort($hostdomaincount[$host]);
	$hostdomaincount[$host]=array_reverse($hostdomaincount[$host],true);
	$hostlog="";
	$hosttotal=0;
	$logcount=0;
	
	foreach($hostdomaincount[$host] as $domain => $count) 
	{
		$hosttotal += $count;
		$logcount++;
		if($logcount%5==0) $hostlog .= "<br>";
		if($hostlog<>"")
		{
			$hostlog .= "<font color=darkblue>$domain</font>: $count ";
		}
		else
		{
				$hostlog = "<font color=darkblue>$domain:</font> $count ";
		}
	}
	print "<tr><td>$host</td><td>$total</td><td>" . $hostlog . "<br>Total: $hosttotal</td></tr>";
}*/

print ("</table>");

/*while(!feof($file)){
	$line = fgets($file);
	$line = substr($line, 1, -1);
	$line = explode(",", $line);
	array_push($num_files, $line[0]);
}
 
 
$num_cv = array_count_values($num_files);
 
foreach($num_cv as $f => $c) {
	echo 'file: ' . $f . ' - ' . $c . '<br>';
}
 */
 
?>
