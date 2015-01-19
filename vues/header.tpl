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
