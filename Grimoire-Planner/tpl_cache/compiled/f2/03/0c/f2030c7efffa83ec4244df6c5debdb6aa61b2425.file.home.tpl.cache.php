<?php /* Smarty version Smarty-3.1.11, created on 2013-04-29 19:18:43
         compiled from "/var/www/html/git/Grimoire-Planner/app/home/view/home.tpl" */ ?>
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
  ),
  'nocache_hash' => '1317219489517effd33ae7b6-40381782',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_517effd34793a9_64189183',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_517effd34793a9_64189183')) {function content_517effd34793a9_64189183($_smarty_tpl) {?>WELCOME TO GRIMOIRE PLANNER
<br />
<br />

<?php if (@USERID){?>
Welcome, $smarty.const.USERNAME
<?php }else{ ?>
Log in: <?php echo $_smarty_tpl->getSubTemplate ('user/view/login.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array(), 0);?>

<?php }?>
<?php }} ?>