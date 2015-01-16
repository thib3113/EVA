<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <div class="navbar-header" style="padding: 0px 30px;">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
        <a href="index.php" class="navbar-brand" style="padding: 5px;" id="header_title">
          {$smarty.const.PROGRAM_NAME} <i class="fa fa-github-alt fa-2x"></i> {$smarty.const.PROGRAM_VERSION}
        </a>
    </div>
    <div class="collapse navbar-collapse navbar-inner" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav nav-center">
{foreach from=$template_infos.menu_items item=menu_item}
{if isset($menu_item.sub_menu) && $menu_item.sub_menu ne ""}
            <li class="dropdown">
              <a href="{$menu_item.link}" class="dropdown-toggle" data-toggle="dropdown">{$menu_item.name}<span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
{foreach from=$menu_item.sub_menu item=sub_item}
{if {$sub_item.divider}}
                <li class="divider"></li>
{else}
                <li><a href="{$menu_item.link}">{$menu_item.name}</a></li>
{/if}
{/foreach}
              </ul>
            </li>
{else}
            <li {if {$menu_item.active}==1}class="active"{/if}>
                {if {$menu_item.custom_item} ne ""}
                {$menu_item.custom_item}
                {else}
                <a href="{$menu_item.link}"><i class="fa fa-{$menu_item.icon}"></i> {$menu_item.name}</a>
                {/if}
            </li>
{/if}
{/foreach}
        </ul>
    </div>
  </div>
</nav>