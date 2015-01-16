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
  <div id="current_tab">
      <div style="display:none;" id="tab_accueil">
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
      </div>
      <div style="display:none;" id="tab_profil">
          <form role="form" method="POST" action="?page=configs#profil">
              <table class="table table-striped">
                  <tr>
                      <td>Pseudo</td>
                      <td><input type="text" value="{$myUser.username}"></td>
                  </tr>
                  <tr>
                      <td>Email</td>
                      <td><input type="email" value="{$myUser.email}"></td>
                  </tr>
                  <tr>
                    <td>Mot de passe</td>
                    <td><input type="password"></td>
                  </tr>
                  <tr>
                    <td>Confirmation du mot de passe</td>
                    <td><input type="password"></td>
                  </tr>
              </table>
              <button type="submit" class="btn btn-primary">Submit</button>
          </form>
      </div>
      <div style="display:none;" id="tab_plugins">

              <div class="input-group" style="min-height:40px;">
                <span class="input-group-addon">
                  <span id="market_search_loading">
                    <span class="load hide">
                      <svg class="loader" width="20px" height="20px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                         <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
                      </svg>
                    </span>
                    <span class="no_load">
                      <i class="fa fa-search" style="transition: font-size 0.3s"></i>
                    </span>
                  </span>
                </span>
                <input type="text" class="form-control" placeholder="Recherche" aria-describedby="market_search_loading" style="min-height: 40px;">
              </div>
      </div>
      <div style="display:none;" id="tab_mise_a_jour">
          {if !$erreur_maj}
          
          {else}
        <div class="panel panel-danger">
          <div class="panel-heading">
              <h3 class="panel-title">Les erreurs suivantes sont apparus</h3>
          </div>
          <div class="panel-body">
            <ul>
            {foreach from=$erreur_maj item=erreur}
              <li>{$erreur}</li>
            {/foreach}
            </ul>
          </div>
        </div>
          {/if}
      </div>
  </div>
</div>
{include "{$smarty.const.ROOT}/vues/footer.tpl"}