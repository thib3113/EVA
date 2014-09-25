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
    <form id="form_signin" class="form-signin" role="form">
        <h2 class="form-signin-heading">Identification requise</h2>
        <input type="text" id="user" class="form-control" placeholder="Utilisateur" required autofocus>
        <input type="password" id="pass" class="form-control" placeholder="Mot de passe" required>
        <div class="form-group form-group-lg">
            <div class="col-xs-2 col-sm-2 col-md-2">
                <input type="checkbox" class="cursor_pointer" name="remember_me" id="remember_me" value="remember-me">
            </div>
            <label class="col-xs-10 col-sm-10 col-md-10 control-label cursor_pointer" for="remember_me" class="checkbox">Se souvenir de moi</label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Connexion</button>
    </form>

</div>
{include "{$smarty.const.ROOT}/vues/footer.tpl"}