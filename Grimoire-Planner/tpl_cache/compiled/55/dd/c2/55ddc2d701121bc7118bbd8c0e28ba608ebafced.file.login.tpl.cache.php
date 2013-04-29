<?php /* Smarty version Smarty-3.1.11, created on 2013-04-29 19:18:43
         compiled from "/var/www/html/git/Grimoire-Planner/app/user/view/login.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1091663004517effd347bab6-78421232%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '55ddc2d701121bc7118bbd8c0e28ba608ebafced' => 
    array (
      0 => '/var/www/html/git/Grimoire-Planner/app/user/view/login.tpl',
      1 => 1352139160,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1091663004517effd347bab6-78421232',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_517effd350c977_26439471',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_517effd350c977_26439471')) {function content_517effd350c977_26439471($_smarty_tpl) {?><form name="login" method="POST">
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