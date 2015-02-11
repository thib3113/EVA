{* SMARTY TEMPLATE *}
<!DOCTYPE html>
<html lang="fr">
  <head>

    {if isset($template_infos.externjs.start_head)}{foreach from=$template_infos.externjs.start_head item=script}
    <script src="{$script}"></script>
    {/foreach}{/if}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>{$template_infos.title}</title>

    <!-- meta -->
    <link rel="apple-touch-icon" sizes="57x57" href="{$template_infos.configs.base_url}/vues/img/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="{$template_infos.configs.base_url}/vues/img/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="{$template_infos.configs.base_url}/vues/img/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="{$template_infos.configs.base_url}/vues/img/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="{$template_infos.configs.base_url}/vues/img/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="{$template_infos.configs.base_url}/vues/img/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="{$template_infos.configs.base_url}/vues/img/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="{$template_infos.configs.base_url}/vues/img/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="{$template_infos.configs.base_url}/vues/img/apple-touch-icon-180x180.png">
    <link rel="icon" type="image/png" href="{$template_infos.configs.base_url}/vues/img/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="{$template_infos.configs.base_url}/vues/img/favicon-194x194.png" sizes="194x194">
    <link rel="icon" type="image/png" href="{$template_infos.configs.base_url}/vues/img/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="{$template_infos.configs.base_url}/vues/img/android-chrome-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="{$template_infos.configs.base_url}/vues/img/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="{$template_infos.configs.base_url}/vues/img/android-chrome-manifest.json">
    <link rel="shortcut icon" href="{$template_infos.configs.base_url}/vues/img/favicon.ico">
    <meta name="msapplication-TileColor" content="#00aba9">
    <meta name="msapplication-TileImage" content="{$template_infos.configs.base_url}/vues/img/mstile-144x144.png">
    <meta name="msapplication-config" content="{$template_infos.configs.base_url}/vues/img/browserconfig.xml">
    <meta name="theme-color" content="#009688">
    <!-- /meta -->

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

    <link rel="stylesheet" href="vues/css/font-awesome.min.css">
    <link rel="stylesheet" href="vues/css/jquery-ui.min.css">

    <link rel="stylesheet" href="vues/css/style.css">
{foreach from=$template_infos.externcss item=style}
    <link rel="stylesheet" href="{$style}">
{/foreach}
    <script>
    base_url = "{$template_infos.configs.base_url}";
    program_version  = "{$smarty.const.PROGRAM_VERSION}";
    </script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    {if isset($template_infos.externjs.end_head)}{foreach from=$template_infos.externjs.end_head item=script}
    <script src="{$script}"></script>
    {/foreach}{/if}
  </head>
  <body>
    {if isset($template_infos.externjs.start_body)}{foreach from=$template_infos.externjs.start_body item=script}
    <script src="{$script}"></script>
    {/foreach}{/if}
