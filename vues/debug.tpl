        <div class="container-fluid">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title">Debug</h3>
                </div>
                <div class="panel-body">
                {foreach from=$template_infos.debugList item=debugType key=name}
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <h3 class="panel-title">{$name}</h3>
                        </div>
                        <div class="panel-body">
                        <ul style="list-style:none;">
                    {foreach from=$debugType item=debug}
                            <li>{$debug.id} - {$debug.value} {if {$debug.time}}AT <span class="label label-info">{$debug.time}</span>{/if}</li>
                    {/foreach}
                        </ul>
                        </div>
                    </div>
                {/foreach}
                </div>
            </div>
        </div>