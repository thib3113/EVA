{* SMARTY TEMPLATE *}
{include "{$smarty.const.ROOT}/vues/header.tpl"}
{include "{$smarty.const.ROOT}/vues/menu.tpl"}
<div class="container-fluid">
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
        <div id="market_conteneur">
          <style>
            #load_market, .circular{
              height:20px;
              width:20px;
            }
            #addon_market{
              min-width:45px;
            }
          </style>
              <div class="input-group" style="margin-bottom: 20px;">
                <span class="input-group-addon" id="addon_market">
                  <div id="load_market" class="loader" style="display:none;background:inherit;">
                    <svg class="circular">
                      <circle class="path" cx="10" cy="10" r="8" fill="none" stroke-width="3" stroke-miterlimit="10"/>
                    </svg>
                  </div>
                  <span id="market_search_loading">
                    <i class="fa fa-search"></i>
                  </span>
                </span>
                <input type="text" class="form-control floating-label" id="market_search" placeholder="Recherche">
              </div>
              <!-- <div style="float:left;margin-left:50%;transition:top 1.5s;" class="load"> -->
              <!-- </div> -->
              <div id="market_container">
                <ul id="market_list_plugins" class="no_puce"></ul>
                <div id="pagination_plugins"></div>
              </div>
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