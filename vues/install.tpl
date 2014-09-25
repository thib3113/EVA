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
        <small>nb : toutes les commandes doivent être lancé dans le dossier <kbd>{$smarty.const.ROOT}</kbd>, pour y aller vous pouvez utilisé <kbd>cd {$smarty.const.ROOT}</kbd></small>
      </div>
    </div>
    {/if}
        <form class="form-horizontal" role="form">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Username</label>
                <div class="col-sm-10">
                    <input type="text" name="username" class="form-control" id="inputEmail3" placeholder="Username">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">Mot de passe</label>
                <div class="col-sm-10">
                    <input type="password" name="pass" class="form-control" id="inputPassword3" placeholder="Password">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">Confirmation</label>
                <div class="col-sm-10">
                    <input type="password" name="pass_confirm" class="form-control" id="inputPassword3" placeholder="Password">
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
                <div class="col-sm-10">
                    <input type="email" name="email" class="form-control" id="inputEmail3" placeholder="Email">
                </div>
            </div>
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
                <button class="btn btn-lg btn-primary btn-block " {if $erreurs} disabled {/if} type="submit">Installation</button>
            </div>
            <div class="col-sm-4"></div>
        </form>
    </div>


    <ul>
    {foreach from=$taskList item=task}
        <li>{$task}</li>
    {/foreach}
    </ul>


</div>
{include "{$smarty.const.ROOT}/vues/footer.tpl"}