<?php
$vendor_dir = dirname(__DIR__) . "/vendor";
/* demo implementation of the auth Class */
$simplicity_dir = dirname(dirname(__DIR__)) . "/src";

require_once("$vendor_dir/autoload.php");


//require_once("$simplicity_dir/SimpleAuth.php");
/* list of users, for the demo an array of user */
require_once(__DIR__ .  "/users.php");
/* translations for the demo and template*/
require_once(__DIR__ . "/config.php");

class Auth
{
	private $users = [];
	private $simple_auth = null;
	public function __construct(private $config)
	{
		$this->config = $config;
	}
	function require_login()
	{
		$this->simple_auth->require_login();
	}
	public function on_init()
	{
		$config = $this->config;
		$users = $this->users ?? $config['users'] ?? [];
		$vars = $this->config['vars'] ?? [];
		$auth_translations = $config['translations'] ?? [];

		$this->simple_auth = new \Opensitez\Simplicity\SimpleAuth($config);
		$login_template = $config['login_template'] ?? "login.html";
		$this->simple_auth->set_template(file_get_contents(__DIR__ . "/templates/$login_template"));
	}
}
$config['users'] = $pref_users;
$config['vars'] = $vars ?? [];

$auth = new Auth($config);
$auth->on_init();
