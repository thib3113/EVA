{* SMARTY TEMPLATE *}
{include "{$smarty.const.ROOT}/vues/header.tpl"}
{include "{$smarty.const.ROOT}/vues/menu.tpl"}
<style type="text/css">
    .legend div{
        text-align: center;
    }
    .gpio_left{
        text-align:center;
    }

    .gpio_right{
        text-align:center;
    }
    .POWER{
      color: #fff;
      background-color: #d9534f;
      border-color: #d43f3a;
    }
    .POWER:hover,
    .POWER:focus,
    .POWER.focus,
    .POWER:active,
    .POWER.active,
    .open > .dropdown-toggle.POWER {
      color: #fff;
      background-color: #c9302c;
      border-color: #ac2925;
    }
    .POWER:active,
    .POWER.active,
    .open > .dropdown-toggle.POWER {
        background-image: none;   
    }
    .POWER.disabled,
    .POWER[disabled],
    fieldset[disabled] .POWER,
    .POWER.disabled:hover,
    .POWER[disabled]:hover,
    fieldset[disabled] .POWER:hover,
    .POWER.disabled:focus,
    .POWER[disabled]:focus,
    fieldset[disabled] .POWER:focus,
    .POWER.disabled.focus,
    .POWER[disabled].focus,
    fieldset[disabled] .POWER.focus,
    .POWER.disabled:active,
    .POWER[disabled]:active,
    fieldset[disabled] .POWER:active,
    .POWER.disabled.active,
    .POWER[disabled].active,
    fieldset[disabled] .POWER.active {
      background-color: #d9534f;
      border-color: #d43f3a;
    }
    .POWER .badge {
      color: #d9534f;
      background-color: #fff;
    }
    .I2C{
      color: #fff;
      background-color: #337ab7;
      border-color: #2e6da4;  
    }
    .I2C:hover,
    .I2C:focus,
    .I2C.focus,
    .I2C:active,
    .I2C.active,
    .open > .dropdown-toggle.I2C {
          color: #fff;
          background-color: #286090;
          border-color: #204d74;
    }
    .I2C:active,
    .I2C.active,
    .open > .dropdown-toggle.I2C {
      background-image: none;
    }
    .I2C.disabled,
    .I2C[disabled],
    fieldset[disabled] .I2C,
    .I2C.disabled:hover,
    .I2C[disabled]:hover,
    fieldset[disabled] .I2C:hover,
    .I2C.disabled:focus,
    .I2C[disabled]:focus,
    fieldset[disabled] .I2C:focus,
    .I2C.disabled.focus,
    .I2C[disabled].focus,
    fieldset[disabled] .I2C.focus,
    .I2C.disabled:active,
    .I2C[disabled]:active,
    fieldset[disabled] .I2C:active,
    .I2C.disabled.active,
    .I2C[disabled].active,
    fieldset[disabled] .I2C.active {
      background-color: #337ab7;
      border-color: #2e6da4;
    }
    .I2C .badge {
      color: #337ab7;
      background-color: #fff;
    }
    .GND{
      color: #fff;
      background-color: #000;
      border-color: #000;  
    }
    .GND:hover,
    .GND:focus,
    .GND.focus,
    .GND:active,
    .GND.active,
    .open > .dropdown-toggle.GND {
          color: #fff;
          background-color: #000;
          border-color: #000;
    }
    .GND:active,
    .GND.active,
    .open > .dropdown-toggle.GND {
      background-image: none;
    }
    .GND.disabled,
    .GND[disabled],
    fieldset[disabled] .GND,
    .GND.disabled:hover,
    .GND[disabled]:hover,
    fieldset[disabled] .GND:hover,
    .GND.disabled:focus,
    .GND[disabled]:focus,
    fieldset[disabled] .GND:focus,
    .GND.disabled.focus,
    .GND[disabled].focus,
    fieldset[disabled] .GND.focus,
    .GND.disabled:active,
    .GND[disabled]:active,
    fieldset[disabled] .GND:active,
    .GND.disabled.active,
    .GND[disabled].active,
    fieldset[disabled] .GND.active {
      background-color: #000;
      border-color: #000;
    }
    .GND .badge {
      color: #000;
      background-color: #fff;
    }
    .GPIO{
      color: #fff;
      background-color: #eb9316;
      border-color: #eb9316;  
    }
    .GPIO:hover,
    .GPIO:focus,
    .GPIO.focus,
    .GPIO:active,
    .GPIO.active,
    .open > .dropdown-toggle.GPIO {
          color: #fff;
          background-color: #eb9316;
          border-color: #eb9316;
    }
    .GPIO:active,
    .GPIO.active,
    .open > .dropdown-toggle.GPIO {
      background-image: none;
    }
    .GPIO.disabled,
    .GPIO[disabled],
    fieldset[disabled] .GPIO,
    .GPIO.disabled:hover,
    .GPIO[disabled]:hover,
    fieldset[disabled] .GPIO:hover,
    .GPIO.disabled:focus,
    .GPIO[disabled]:focus,
    fieldset[disabled] .GPIO:focus,
    .GPIO.disabled.focus,
    .GPIO[disabled].focus,
    fieldset[disabled] .GPIO.focus,
    .GPIO.disabled:active,
    .GPIO[disabled]:active,
    fieldset[disabled] .GPIO:active,
    .GPIO.disabled.active,
    .GPIO[disabled].active,
    fieldset[disabled] .GPIO.active {
      background-color: #eb9316;
      border-color: #eb9316;
    }
    .GPIO .badge {
      color: #eb9316;
      background-color: #fff;
    }
    .UART{
      color: #fff;
      background-color: #3e8f3e;
      border-color: #3e8f3e;  
    }
    .UART:hover,
    .UART:focus,
    .UART.focus,
    .UART:active,
    .UART.active,
    .open > .dropdown-toggle.UART {
          color: #fff;
          background-color: #3e8f3e;
          border-color: #3e8f3e;
    }
    .UART:active,
    .UART.active,
    .open > .dropdown-toggle.UART {
      background-image: none;
    }
    .UART.disabled,
    .UART[disabled],
    fieldset[disabled] .UART,
    .UART.disabled:hover,
    .UART[disabled]:hover,
    fieldset[disabled] .UART:hover,
    .UART.disabled:focus,
    .UART[disabled]:focus,
    fieldset[disabled] .UART:focus,
    .UART.disabled.focus,
    .UART[disabled].focus,
    fieldset[disabled] .UART.focus,
    .UART.disabled:active,
    .UART[disabled]:active,
    fieldset[disabled] .UART:active,
    .UART.disabled.active,
    .UART[disabled].active,
    fieldset[disabled] .UART.active {
      background-color: #3e8f3e;
      border-color: #3e8f3e;
    }
    .UART .badge {
      color: #3e8f3e;
      background-color: #fff;
    }
    .SPI{
      color: #fff;
      background-color: #7029CA;
      border-color: #7029CA; 
    }
    .SPI:hover,
    .SPI:focus,
    .SPI.focus,
    .SPI:active,
    .SPI.active,
    .open > .dropdown-toggle.SPI {
          color: #fff;
          background-color: #7029CA;
          border-color: #7029CA;
    }
    .SPI:active,
    .SPI.active,
    .open > .dropdown-toggle.SPI {
      background-image: none;
    }
    .SPI.disabled,
    .SPI[disabled],
    fieldset[disabled] .SPI,
    .SPI.disabled:hover,
    .SPI[disabled]:hover,
    fieldset[disabled] .SPI:hover,
    .SPI.disabled:focus,
    .SPI[disabled]:focus,
    fieldset[disabled] .SPI:focus,
    .SPI.disabled.focus,
    .SPI[disabled].focus,
    fieldset[disabled] .SPI.focus,
    .SPI.disabled:active,
    .SPI[disabled]:active,
    fieldset[disabled] .SPI:active,
    .SPI.disabled.active,
    .SPI[disabled].active,
    fieldset[disabled] .SPI.active {
      background-color: #7029CA;
      border-color: #7029CA;
    }
    .SPI .badge {
      color: #7029CA;
      background-color: #fff;
    }
@media (max-width: 1200px) {
  .SPI, .I2C, .UART {
     background-image:      -webkit-linear-gradient(bottom left, rgba(235, 147, 22, 0.75) 25%, transparent 25%, transparent 50%, rgba(235, 147, 22, 0.75) 50%, rgba(235, 147, 22, 0.75) 75%, transparent 75%, transparent);
     background-image:      -o-linear-gradient(bottom left, rgba(235, 147, 22, 0.75) 25%, transparent 25%, transparent 50%, rgba(235, 147, 22, 0.75) 50%, rgba(235, 147, 22, 0.75) 75%, transparent 75%, transparent);
     background-image:      linear-gradient(to top right, rgba(235, 147, 22, 0.75) 25%, transparent 25%, transparent 50%, rgba(235, 147, 22, 0.75) 50%, rgba(235, 147, 22, 0.75) 75%, transparent 75%, transparent);
  
     -webkit-background-size: 40px 40px;
          background-size: 40px 40px;
  }
}
</style>
<div class="container-fluid" style="margin-bottom: 10px;">
  <div class="panel panel-default legend">
    <div class="panel-heading">Legende</div>
    <div class="panel-body">
        <div class="row legend">
          <div class="col-md-2"><button disabled class="btn POWER">Pin Fournissant de l'énergie</button></div>
          <div class="col-md-2"><button disabled class="btn GND">Pin de masse</button></div>
          <div class="col-md-2"><button disabled class="btn I2C">Pin I2C</button></div>
          <div class="col-md-2"><button disabled class="btn SPI">Pin SPI</button></div>
          <div class="col-md-2"><button disabled class="btn UART">Pin UART</button></div>
          <div class="col-md-2"><button disabled class="btn GPIO">Pin GPIO</button></div>
      </div>
    </div>
    <div class="panel-footer">Pour une correspondance des Pins, tenez votre raspberry pi avec les Pin en haut à droite<br><i class="fa fa-exclamation-triangle"></i>Si le pin n'est pas relié à un + ou un -, il oscillera
    </div>
  </div>
{foreach from=$pins key=key item=pin}
{if $key%2!=0}
            <div class="row">
                <div class="gpio_left">
                    <div class="col-md-2 col-xs-2 col-sm-2 col-xs-offset-0">
                            <p>
{if $pin.wiringPin !== null}
                        <button type="button" data-state="{$pin.value}" data-wiringpin="{$pin.wiringPin}" class="change_state btn btn-{if $pin.value}success{else}warning{/if}">{if $pin.value}on{else}off{/if}</button>
{/if}
                            </p>
                    </div>
                    <div class="col-md-1 hidden-xs col-sm-1">
                        <p>
{$key}
                        </p>
                    </div>
                    <div class="col-md-2 col-xs-4 col-sm-3">
                        <p>
                            <button disabled type="button" class="btn {$pin.type}">{$pin.nameOfPin}</button>{if $pin.wiringPin !== null && $pin.type != "GPIO"}<span class="hidden-sm hidden-xs hidden-md "> / <button disabled type="button" class="btn GPIO">GPIO {$pin.wiringPin}</button></span>{/if}
                        </p>
                    </div>
                    <div class="col-md-1 hidden-sm hidden-xs">
                        <i class="fa fa-dot-circle-o"></i>
                    </div>
                </div>
{else}
                <div class="gpio_right">
                    <div class="col-md-1 hidden-sm hidden-xs">
                        <i class="fa fa-dot-circle-o"></i>
                    </div>

                    <div class="col-md-2 col-xs-4 col-sm-3">
                        <p>
                            <button disabled type="button" class="btn {$pin.type}">{$pin.nameOfPin}</button>{if $pin.wiringPin !== null && $pin.type != "GPIO"}<span class="hidden-sm hidden-xs hidden-md"> / <button disabled type="button" class="btn GPIO">GPIO {$pin.wiringPin}</button></span>{/if}
                        </p>
                    </div>
                    <div class="col-md-1 col-sm-1 hidden-xs">
                        <p>
{$key}
                        </p>
                    </div>
                    <div class="col-md-2 col-xs-2 col-sm-2">
                        <p>
{if $pin.wiringPin !== null}
                        <button type="button" data-state="{$pin.value}" data-wiringpin="{$pin.wiringPin}" class="change_state btn btn-{if $pin.value}success{else}warning{/if}">{if $pin.value}on{else}off{/if}</button>
{/if}
                        </p>
                    </div>
                </div>
            </div>
{/if}
{/foreach}
{if $key%2!=0}
            </div>
{/if}
    </div>
</div>
{include "{$smarty.const.ROOT}/vues/footer.tpl"}