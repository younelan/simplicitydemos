# Simplicity Demo
Demo Projects for the simplicity framework

## folders 
- **SimpleAuth** - simple demo of the auth class. Implements a **Login** then shows variables using **SimpleTemplate** and **Debug** object
- **Qrcode** - simple demo of generating a Qr Cod
- **Tasks**: task scheduler demo
- **ApacheLog**: Parse apache Logs

## Classes Used
- **SimpleDebug.php** - simple debug class to print arrays in a much more user Friendly Format
    - require SimpleDebug.php
    - $debug = new SimpleDebug()
    - $debug-> printArray($array)
- **Auth.php** - a demo of the auth clas in use, basically include and $auth->require_login() will password protect the page
- **index.php** - example of using SimpleAuth for simple password protected, supports templates

- **editpasswd.php** - Edit your password form, in progress
- **SimpleForm.php** - Needs update, here mostly until I extract a more modern version. a simple forms generation engine with the option to validate input
- **CompiledTemplate.php** - experimental Template that compiles templates to php and handles recursive blocks for more complex scenarios. Works but needs to be vetted

## For More info
Checkout git repos:
    - [Simplicity Repo](https://github.com/younelan/simplicity) for more details
    - [Simplicity Basecamp](https://github.com/younelan/basecamp) for simple barebone project
