<?php /* Smarty version Smarty-3.1.11, created on 2013-01-10 20:09:52
         compiled from "/var/www/html/Grimoire-Planner/app/user/view/login.tpl" */ ?>
<?php /*%%SmartyHeaderCode:58483577550ef6660cafca1-19355937%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '180e4ad9fdd898c703157f02b72ec583e9816146' => 
    array (
      0 => '/var/www/html/Grimoire-Planner/app/user/view/login.tpl',
      1 => 1352139160,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '58483577550ef6660cafca1-19355937',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50ef6660cb0f10_75459527',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50ef6660cb0f10_75459527')) {function content_50ef6660cb0f10_75459527($_smarty_tpl) {?><form name="login" method="POST">
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