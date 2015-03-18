{* SMARTY TEMPLATE *}
        {if {$smarty.const.DEBUG}==1}
        {include file="{$smarty.const.ROOT}/vues/debug.tpl"}
        {/if}
        <div id="clear_footer"></div>
        <div class="navbar navbar-default navbar-fixed-bottom text-center" id="footer" style="line-height:4em;">
                <span class="hidden-xs">Généré en </span>{$template_infos.executionTime} - Licence CC-by-nc-sa - {$smarty.const.PROGRAM_NAME} {$smarty.const.PROGRAM_VERSION}
        </div>
        {if isset($template_infos.externjs.end_body)}{foreach from=$template_infos.externjs.end_body item=script}
        <script src="{$script}"></script>
        {/foreach}{/if}

    </body>
</html>