{* SMARTY TEMPLATE *}
        {if {$smarty.const.DEBUG}==1}
{include file="{$smarty.const.ROOT}/vues/debug.tpl"}
        {/if}
        <div id="clear_footer"></div>
        <div class="navbar navbar-default navbar-fixed-bottom text-center" id="footer" style="line-height:4em;">
                Généré en {$template_infos.executionTime} - Licence CC-by-nc-sa - {$smarty.const.PROGRAM_NAME} {$smarty.const.PROGRAM_VERSION}
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <!-- /Bootstrap -->
        <script src="vues/js/libs.js"></script>

        {foreach from=$template_infos.externjs item=script}
        <script src="{$script}"></script>
        {/foreach}
        {if {$smarty.const.DEBUG}==1}
            <script src="vues/js/debug.js"></script>
        {/if}

    </body>
</html>