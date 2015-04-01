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
          <form id="profil_update" role="form" method="POST" action="?page=configs#profil">
              <table class="table table-striped">
                  <tr>
                    <td>
                      <label for="username">Username</label>
                    </td>
                    <td>
                      <div class="input-group">
                        <input type="text" class="form-control" id="username" value="{$myUser.username}" placeholder="Username">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <label for="email">Email</label>
                    </td>
                    <td>
                      <div class="input-group">
                        <input type="text" class="form-control" id="email" value="{$myUser.email}" placeholder="Email">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <label for="pass">Mot de passe</label>
                    </td>
                    <td>
                      <div class="input-group">
                        <input type="password" class="form-control" id="pass" placeholder="Mot de passe">
                      </div>
                      <span style="font-size:10pt;color:red;">laissez vide pour ne pas modifier</span>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <label for="pass_conf">Confirmation</label>
                    </td>
                    <td>
                      <div class="input-group">
                        <input type="password" class="form-control" id="pass_conf" placeholder="Mot de passe">
                      </div>
                    </td>
                  </tr>
              </table>
              <button type="submit" class="btn btn-primary">Sauvegarder</button>
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
              <div id="check_update_result"></div>
              <button id="check_update" class="btn btn-primary">Rechercher une mise à jour</button>
      </div>
  </div>
</div>
{include "{$smarty.const.ROOT}/vues/footer.tpl"}