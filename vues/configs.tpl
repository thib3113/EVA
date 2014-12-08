{* SMARTY TEMPLATE *}
{include "{$smarty.const.ROOT}/vues/header.tpl"}
{include "{$smarty.const.ROOT}/vues/menu.tpl"}
<div class="container-fluid full_height">
    <ul class="nav nav-tabs nav-justified">
      <li role="tabs" data-tab="accueil" class="active"><a href="javascript:void(0)">Accueil</a></li>
      <li role="tabs" data-tab="profil"><a href="javascript:void(0)">Profil</a></li>
      <li role="tabs" data-tab="plugins"><a href="javascript:void(0)">Plugins</a></li>
      <li role="tabs" data-tab="mise_a_jour"><a href="javascript:void(0)">Mise à jour</a></li>
  </ul>
  <style>
    td{
        width: 50%;
    }
</style>
    <div id="tab_accueil">
        <form role="form">
            <table class="table table-striped">
                <tr>
                    <td>Votre Raspberry Pi</td>
                    <td>{$yourRaspberryPi}</td>
                </tr>
                <tr>
                    <td>Votre version</td>
                    <td>{$smarty.const.PROGRAM_NAME} {$smarty.const.PROGRAM_VERSION}</td>
                </tr>
                <tr>
                    <td>Votre serveur web</td>
                    <td>{$server_software}</td>
                </tr>
                <tr>
                  <td>Version de php</td>
                  <td>{$phpversion}</td>
                </tr>
            </table>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
{include "{$smarty.const.ROOT}/vues/footer.tpl"}