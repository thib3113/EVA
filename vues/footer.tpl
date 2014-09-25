{* SMARTY TEMPLATE *}
        
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <!-- /Bootstrap -->
        <script src="vues/js/libs.js"></script>

        {foreach from=$template_infos.js item=script}
        <script src="{$script}"></script>
        {/foreach}
        {if {$smarty.const.DEBUG}==1}
        <script src="vues/js/debug.js"></script>
        {/if}
         <div class="navbar navbar-bottom" id="footer">
                {$executionTime} seconde{if {$executionTime} > 1}s{/if}

        </div>
    </body>
</html>