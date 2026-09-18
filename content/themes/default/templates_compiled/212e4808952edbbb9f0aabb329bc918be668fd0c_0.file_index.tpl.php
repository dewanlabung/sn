<?php
/* Smarty version 5.8.4, created on 2026-09-18 12:56:13
  from 'file:index.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.4',
  'unifunc' => 'content_6aad34edb01ed9_32277478',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '212e4808952edbbb9f0aabb329bc918be668fd0c' => 
    array (
      0 => 'index.tpl',
      1 => 1710768816,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:index.landing.tpl' => 1,
    'file:index.newsfeed.tpl' => 1,
  ),
))) {
function content_6aad34edb01ed9_32277478 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/alphaome/nkhoj.com/content/themes/default/templates';
if (!$_smarty_tpl->getValue('user')->_logged_in && !$_smarty_tpl->getValue('system')['newsfeed_public']) {?>
  <?php $_smarty_tpl->renderSubTemplate('file:index.landing.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
} else { ?>
  <?php $_smarty_tpl->renderSubTemplate('file:index.newsfeed.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
}
}
}
