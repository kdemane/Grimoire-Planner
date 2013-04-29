<?php /* Smarty version Smarty-3.1.11, created on 2013-01-10 20:09:52
         compiled from "/var/www/html/Grimoire-Planner/app/home/view/home.tpl" */ ?>
<?php /*%%SmartyHeaderCode:163725089950ef6660c6c2c5-45990623%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '159d251e965cb976c52e5c2ab9b99016ba65d3f9' => 
    array (
      0 => '/var/www/html/Grimoire-Planner/app/home/view/home.tpl',
      1 => 1352137666,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '163725089950ef6660c6c2c5-45990623',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50ef6660cac974_59132777',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50ef6660cac974_59132777')) {function content_50ef6660cac974_59132777($_smarty_tpl) {?>WELCOME TO GRIMOIRE PLANNER
<br />
<br />

<?php if (@USERID){?>
Welcome, $smarty.const.USERNAME
<?php }else{ ?>
Log in: <?php echo $_smarty_tpl->getSubTemplate ('user/view/login.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array(), 0);?>

<?php }?>
<?php }} ?>