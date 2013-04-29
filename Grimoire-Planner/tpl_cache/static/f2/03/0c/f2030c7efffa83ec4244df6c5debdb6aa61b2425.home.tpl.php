<?php /*%%SmartyHeaderCode:1317219489517effd33ae7b6-40381782%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f2030c7efffa83ec4244df6c5debdb6aa61b2425' => 
    array (
      0 => '/var/www/html/git/Grimoire-Planner/app/home/view/home.tpl',
      1 => 1352137666,
      2 => 'file',
    ),
    '55ddc2d701121bc7118bbd8c0e28ba608ebafced' => 
    array (
      0 => '/var/www/html/git/Grimoire-Planner/app/user/view/login.tpl',
      1 => 1352139160,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1317219489517effd33ae7b6-40381782',
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_517effd3510694_78155992',
  'cache_lifetime' => 0,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_517effd3510694_78155992')) {function content_517effd3510694_78155992($_smarty_tpl) {?>WELCOME TO GRIMOIRE PLANNER
<br />
<br />

Log in: <form name="login" method="POST">
Username:
<input type="text">
<br />
Password:
<input type="password">
<br />
<input type="submit" value="Go">
</form>
<br />
OR <a href="/register">Sign up</a>

<?php }} ?>