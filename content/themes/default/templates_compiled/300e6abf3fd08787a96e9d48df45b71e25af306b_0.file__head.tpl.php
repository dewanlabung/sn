<?php
/* Smarty version 5.8.4, created on 2026-09-18 12:56:13
  from 'file:_head.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.4',
  'unifunc' => 'content_6aad34edb386f0_98202516',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '300e6abf3fd08787a96e9d48df45b71e25af306b' => 
    array (
      0 => '_head.tpl',
      1 => 1788026317,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:_head.css.tpl' => 1,
  ),
))) {
function content_6aad34edb386f0_98202516 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/alphaome/nkhoj.com/content/themes/default/templates';
?><!doctype html>

<html data-lang="<?php echo $_smarty_tpl->getValue('system')['language']['code'];?>
" <?php if ($_smarty_tpl->getValue('system')['language']['dir'] == "RTL") {?> dir="RTL" <?php }?>>

  <head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="generator" content="Sngine">
    <meta name="version" content="<?php echo $_smarty_tpl->getValue('system')['system_version'];?>
">

    <!-- Title -->
    <title><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')($_smarty_tpl->getValue('page_title'),70);?>
</title>
    <!-- Title -->

    <!-- Meta -->
    <meta name="description" content="<?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')($_smarty_tpl->getValue('page_description'),300);?>
">
    <meta name="keywords" content="<?php echo $_smarty_tpl->getValue('system')['system_keywords'];?>
">
    <!-- Meta -->

    <!-- OG-Meta -->
    <meta property="og:title" content="<?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')($_smarty_tpl->getValue('page_title'),70);?>
" />
    <meta property="og:description" content="<?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')($_smarty_tpl->getValue('page_description'),300);?>
" />
    <meta property="og:site_name" content="<?php echo $_smarty_tpl->getSmarty()->getModifierCallback('__')($_smarty_tpl->getValue('system')['system_title']);?>
" />
    <meta property="og:image" content="<?php echo $_smarty_tpl->getValue('page_image');?>
" />
    <!-- OG-Meta -->

    <!-- Twitter-Meta -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')($_smarty_tpl->getValue('page_title'),70);?>
" />
    <meta name="twitter:description" content="<?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')($_smarty_tpl->getValue('page_description'),300);?>
" />
    <meta name="twitter:image" content="<?php echo $_smarty_tpl->getValue('page_image');?>
" />
    <!-- Twitter-Meta -->

    <!-- Favicon -->
    <?php if ($_smarty_tpl->getValue('system')['system_favicon_default']) {?>
      <link rel="shortcut icon" href="<?php echo $_smarty_tpl->getValue('system')['system_url'];?>
/content/themes/<?php echo $_smarty_tpl->getValue('system')['theme'];?>
/images/favicon.png" />
    <?php } elseif ($_smarty_tpl->getValue('system')['system_favicon']) {?>
      <link rel="shortcut icon" href="<?php echo $_smarty_tpl->getValue('system')['system_uploads'];?>
/<?php echo $_smarty_tpl->getValue('system')['system_favicon'];?>
" />
    <?php }?>
    <!-- Favicon -->

    <!-- Fonts [Poppins|Font-Awesome] -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet" crossorigin="anonymous" />
    <?php if ($_smarty_tpl->getValue('system')['language']['dir'] == "RTL") {?>
      <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100..900&display=swap" rel="stylesheet">
    <?php }?>
    <?php if ($_smarty_tpl->getValue('page') == "admin" && $_smarty_tpl->getValue('view') == "license") {?>
      <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php }?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css" integrity="sha512-QeR2VH+lsBE5LSAe1Q5EnTBbe7XTBubt8dG93Y7gidSgdMCr8nVqKcfKAMyN96SV8KDbZVTDXChatu5G2KQGzg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Fonts [Poppins|Font-Awesome] -->

    <!-- CSS -->
    <?php if ($_smarty_tpl->getValue('system')['language']['dir'] == "LTR") {?>
      <link href="<?php echo $_smarty_tpl->getValue('system')['system_url'];?>
/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
      <link href="<?php echo $_smarty_tpl->getValue('system')['system_url'];?>
/content/themes/<?php echo $_smarty_tpl->getValue('system')['theme'];?>
/css/style.min.css?v=<?php echo $_smarty_tpl->getValue('system')['system_version'];?>
" rel="stylesheet">
    <?php } else { ?>
      <link rel="stylesheet" href="<?php echo $_smarty_tpl->getValue('system')['system_url'];?>
/node_modules/bootstrap/dist/css/bootstrap.rtl.min.css">
      <link href="<?php echo $_smarty_tpl->getValue('system')['system_url'];?>
/content/themes/<?php echo $_smarty_tpl->getValue('system')['theme'];?>
/css/style.rtl.min.css?v=<?php echo $_smarty_tpl->getValue('system')['system_version'];?>
" rel="stylesheet">
    <?php }?>
    <!-- CSS -->

    <!-- CSS Customized -->
    <?php $_smarty_tpl->renderSubTemplate('file:_head.css.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
    <!-- CSS Customized -->

    <!-- Header Custom JavaScript -->
    <?php if ($_smarty_tpl->getValue('system')['custome_js_header']) {?>
      <?php echo '<script'; ?>
>
        <?php echo $_smarty_tpl->getSmarty()->getModifierCallback('html_entity_decode')($_smarty_tpl->getValue('system')['custome_js_header'],ENT_QUOTES);?>

      <?php echo '</script'; ?>
>
    <?php }?>
    <!-- Header Custom JavaScript -->

    <!-- PWA -->
    <?php if ($_smarty_tpl->getValue('system')['pwa_enabled']) {?>
      <link rel="manifest" href="<?php echo $_smarty_tpl->getValue('system')['system_url'];?>
/manifest.php">
      <!-- register service worker -->
      <?php echo '<script'; ?>
>
        if ('serviceWorker' in navigator) {
          navigator.serviceWorker.register('/sw.js').then((registration) => {
            console.log('Service Worker registered with scope:', registration.scope);
          }).catch((error) => {
            console.log('Service Worker registration failed:', error);
          });
        }
      <?php echo '</script'; ?>
>
      <!-- register service worker -->
    <?php } else { ?>
      <!-- unregister service worker -->
      <?php echo '<script'; ?>
>
        if ('serviceWorker' in navigator) {
          navigator.serviceWorker.getRegistrations().then(function(registrations) {
            for (let registration of registrations) {
              registration.unregister();
            }
          });
        }
      <?php echo '</script'; ?>
>
      <!-- unregister service worker -->
    <?php }?>
    <!-- PWA -->

    <!-- AgeVerif Checker -->
    <?php if (!$_smarty_tpl->getValue('user')->_is_admin && $_smarty_tpl->getValue('system')['age_restriction'] && $_smarty_tpl->getValue('system')['ageverif_enabled'] && $_smarty_tpl->getValue('system')['ageverif_api_key']) {?>
      <?php echo '<script'; ?>
 src="https://www.ageverif.com/checker.js?key=<?php echo $_smarty_tpl->getValue('system')['ageverif_api_key'];?>
"><?php echo '</script'; ?>
>
    <?php }?>
    <!-- AgeVerif Checker -->

</head><?php }
}
