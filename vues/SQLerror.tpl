{* SMARTY TEMPLATE *}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Erreur SQL</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

    <link rel="stylesheet" href="vues/css/font-awesome.min.css">

    <link rel="stylesheet" href="vues/css/style.css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <div class="container-fluid">
        {if {$smarty.const.DEBUG}==1}
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">Erreur sql dans le fichier {$errorInfos.file} ligne {$errorInfos.line}</h3>
            </div>
            <div class="panel-body">
            {$errorInfos.query} ({foreach from=$errorInfos.params item=params}{$params}{foreachelse}aucun paramètre{/foreach})
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <h3 class="panel-title">Erreur {if {$errorInfos.error|is_array}}{$errorInfos.error.0} code {$errorInfos.error.1}{/if}</h3>
                    </div>
                    <div class="panel-body">
                        {$errorInfos.error.2}
                    </div>
                </div>
            </div>
        </div>
        {else}
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">Erreur sql</h3>
            </div>
            <div class="panel-body">
                Une erreur SQL est intervenue
            </div>
        </div>
    {/if}
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <!-- /Bootstrap -->
</body>