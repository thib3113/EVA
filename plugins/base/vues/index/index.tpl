{* SMARTY TEMPLATE *}
{include "{$smarty.const.ROOT}/vues/header.tpl"}
{include "{$smarty.const.ROOT}/vues/menu.tpl"}
<div class="container-fluid">
    <div class="row">

        <!-- Add Dashboard -->
        <div id="dashboard">
            <div class="col-sm-1 tiers_height dashboard_element cursor_pointer" id="add_dashboard">
                <div class="panel full_height panel-default">
                    <div class="panel-body">
                        <i class="fa fa-plus fa-5x"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Add Dashboard -->

        <div class="modal fade" id="width_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Choisissez la taille du widget <span class="modal_widget_name"></span></h4>
              </div>
              <div class="modal-body">
                <div class="container-fluid">
                  <div class="row">
                    <div id="change_width_select" class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <select class="form-control" name="minbeds" id="change_width">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                            <option>6</option>
                            <option>7</option>
                            <option>8</option>
                            <option>9</option>
                            <option>10</option>
                            <option>11</option>
                            <option>12</option>
                          </select>
                    </div>
                    
                  </div>
                    <form class="form-inline" id="reservation">
                      <div class="form-group col-md-12">
                      </div>
                    </form>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                <button type="button" data-loading-text="Loading..." data-role="valid_width" class="btn btn-primary">Sauvegarder</button>
              </div>
            </div><!-- /.modal-content -->
          </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

    </div>
</div>
{include "{$smarty.const.ROOT}/vues/footer.tpl"}