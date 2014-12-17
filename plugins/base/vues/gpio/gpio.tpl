{* SMARTY TEMPLATE *}
{include "{$smarty.const.ROOT}/vues/header.tpl"}
{include "{$smarty.const.ROOT}/vues/menu.tpl"}
<div class="container-fluid full_height">
    <div class="col-md-offset-3 col-md-6">
        <table>

         {foreach from=$pins key=key item=pin}
            {if $key%2!=0}
            <tr>
                <td>{if $pin.type=="GPIO"}<i data-wpin="{$pin.wiringPin}" class="fa fa-toggle-{if $pin.state}on{else}off{/if} cursor_pointer switch_pin"></i>{/if}&nbsp;</td><td>
                 {$pin.nameOfPin}&nbsp;</td><td  class="{$pin.type}"><i class="fa fa-dot-circle-o"></i>
                </td>
            {else}
                <td class="{$pin.type}"><i class="fa fa-dot-circle-o"></i></td><td>&nbsp;{$pin.nameOfPin} </td><td>&nbsp; {if $pin.type=="GPIO"}<i data-wpin="{$pin.wiringPin}" class="fa fa-toggle-{if $pin.state}on{else}off{/if} cursor_pointer switch_pin"></i>{/if}</td>
            </tr>
            {/if}
        {/foreach}
        </table>
    </div>
</div>
{include "{$smarty.const.ROOT}/vues/footer.tpl"}