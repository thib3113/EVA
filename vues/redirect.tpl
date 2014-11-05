{* SMARTY TEMPLATE *}
{include "{$smarty.const.ROOT}/vues/header.tpl"}
<meta http-equiv="refresh" content="{$time};URL={$to}">
<div class="container-fluid">
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <p class="navbar-brand logo visible-*-*">
                    <a href="index.php">
                        {$smarty.const.PROGRAM_NAME} <i class="fa fa-github-alt fa-2x"></i> {$smarty.const.PROGRAM_VERSION}   
                    </a>
                </p>
            </div>
        </div><!-- /.container-fluid -->
    </nav>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Redirection en cours ...</h3>
        </div>
        <div class="panel-body">
            <p id="redirect_text">{$text}
            <noscript><br>
                Vous serez redirigé dans {$time} seconde{if $time>1}s{/if} <br>
                <a href="{$to}">Cliquez ici si votre navigateur ne vous redirige pas</a>
            </noscript>
            </p>
            <script type="text/javascript">
            document.write('\t\t\t<div id="redirect_progress" class="progress">\n\t\t\t\t\n<div class="progress-bar progress-bar-striped active no-transition" role="progressbar"style="">\n\t\t\t\t\t<span class="sr-only">0% Complete</span>\n\t\t\t\t</div>\n\t\t\t</div>');
            </script>
        </div>
    </div>
</div>
<script type="text/javascript">
    timer = {$time};
    toLink = "{$to}";
</script>
{include "{$smarty.const.ROOT}/vues/footer.tpl"}