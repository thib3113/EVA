<nav class="navbar navbar-default" role="navigation">
  <div class="container">
    <div class="collapse navbar-collapse navbar-inner" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav nav-center">
{foreach from=$template_infos.menu_items item=menu_item}
{if {$menu_item.sub_menu} ne ""}
            <li {if {$menu_item.active}==1}class="active"{/if}>
                {if {$menu_item.custom_item} ne ""}
                {$menu_item.custom_item}
                {else}
                <a href="{$menu_item.link}"><i class="fa fa-{$menu_item.icon}"></i> {$menu_item.name}</a>
                {/if}
            </li>
{else}
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
{/if}
{/foreach}
        </ul>
    </div>
  </div>
</nav>