{* SMARTY TEMPLATE *}
{include "{$smarty.const.ROOT}/vues/header.tpl"}
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

    <div class="container">
    {if $erreurs}  
    <div class="panel panel-danger">
      <div class="panel-heading">
        <h3 class="panel-title">Les erreurs suivantes empèche l'installation</h3>
      </div>
      <div class="panel-body">
        <ul>
        {foreach from=$erreurs item=erreur}
            <li>{$erreur}</li>
        {/foreach}
        </ul>
        <small>nb : toutes les commandes doivent être lancé dans le dossier <kbd>{$smarty.const.ROOT}</kbd>, pour y aller vous pouvez utilisé <kbd>cd {$smarty.const.ROOT}</kbd></small><br>
        <small>vous pouvez trouver plus d'informations sur le forum : <a href="{$smarty.const.PROGRAMM_FORUM}/viewtopic.php?id=2">{$smarty.const.PROGRAMM_FORUM}/viewtopic.php?id=2</a></small>
      </div>
    </div>
    {/if}
    {if $notices}  
    <div class="panel panel-warning">
      <div class="panel-heading">
        <h3 class="panel-title">Informations</h3>
      </div>
      <div class="panel-body">
        <ul>
        {foreach from=$notices item=notice}
            <li>{$notice}</li>
        {/foreach}
        </ul>
      </div>
    </div>
    {/if}
    {if $erreurs && !$error_form || $taskList}
    {else}
        <form class="form-horizontal" method="post" role="form">
        <h3 class="col-md-offset-1">Création d'un compte administrateur</h3>
            <div class="form-group {if $error_form.username == 1}has-error{/if}">
                <label for="inputEmail3" class="col-sm-2 control-label">Username</label>
                <div class="col-sm-10">
                    <input type="text" name="username" class="form-control" id="inputEmail3" placeholder="Username">
                </div>
            </div>
            <div class="form-group {if $error_form.pass == 1}has-error{/if}">
                <label for="inputPassword3" class="col-sm-2 control-label">Mot de passe</label>
                <div class="col-sm-10">
                    <input type="password" name="pass" class="form-control" id="inputPassword3" placeholder="Password">
                </div>
            </div>
            <div class="form-group {if $error_form.pass_confirm == 1}has-error{/if}">
                <label for="inputPassword3" class="col-sm-2 control-label">Confirmation</label>
                <div class="col-sm-10">
                    <input type="password" name="pass_confirm" class="form-control" id="inputPassword3" placeholder="Password">
                </div>
            </div>
            <div class="form-group {if $error_form.email == 1}has-error{/if}">
                <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
                <div class="col-sm-10">
                    <input type="email" name="email" class="form-control" id="inputEmail3" placeholder="Email">
                </div>
            </div>
            <input type="hidden" name="launch_install" value="1">
            <div class="col-md-offset-4 col-sm-4">
                <button class="btn btn-lg btn-primary btn-block" type="submit">Installation</button>
            </div>
        </form>
        {/if}
    </div>

    {if $taskList}
    <ul>
    {foreach from=$taskList item=task}
        <li>{$task}</li>
    {/foreach}
    {if $all_is_good}
        <li>Tout c'est passé sans problème, <a href="index.php">cliquez ici pour commencer à utiliser EVA</a></li>
    {else}
        <li>{$all_is_not_good_message}</li>
        <li>Une erreur s'est produite, merci de la reporter sur le <a href="{$smarty.const.PROGRAM_FORUM}">forum</a>, et de <a href="install.php?token=aaaaaa">recommencer</a></li>
    {/if}
    </ul>
    {/if}


</div>
{include "{$smarty.const.ROOT}/vues/footer.tpl"}