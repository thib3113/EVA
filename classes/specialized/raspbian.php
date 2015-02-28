<?php

$specialized_list = array(
    //connaitre la version de la distrib courrante
    "getCurrentDistribVersion" => "uname -r",

    //permet de récupérer la liste des interfaces réseaux
    "getListNetworkInterfaces" => "ifconfig -s | grep -v Iface | cut -d ' '  -f1",

    //////////////////
    //regex réseaux //
    //////////////////
    "network-ethernetNetwork" => "~eth~",
    "network-wirelessNetwork" => "~wlan~",
    "network-localLoopbackNetwork" => "~Local Loopback~",
    "network-macAddress" => "~HWaddr ([a-z0-9]++:[a-z0-9]++:[a-z0-9]++:[a-z0-9]++:[a-z0-9]++:[a-z0-9]++)~",
    "network-localeIP" => "~inet addr:([0-9]++\.[0-9]++\.[0-9]++\.[0-9]++)~",
    "network-broadcastIP" => "~Bcast:([0-9]++\.[0-9]++\.[0-9]++\.[0-9]++)~",
    "network-netMask" => "~Mask:([0-9]++\.[0-9]++\.[0-9]++\.[0-9]++)~",
    "network-interfaceUp" => "~^\s+UP~",
    "network-broadcastUp" => "~BROADCAST~",
    "network-multicastUp" => "~MULTICAST~",
    "network-loopbackUp" => "~LOOPBACK~",
    "network-MTU" => "~MTU:([0-9]*)~",
    "network-metric" => "~Metric:([0-9]*)~",
    "network-receivedTransmitted" => "~^\s*([R|T]X)~",
    "network-receivedTransmittedPacket" => "~packets:([0-9]+)~",
    "network-receivedTransmittedErrors" => "~errors:([0-9]+)~",
    "network-receivedTransmittedDropped" => "~dropped:([0-9]+)~",
    "network-receivedTransmittedOverruns" => "~overruns:([0-9]+)~",
    "network-receivedTransmittedBytes" => "~^\s*RX\s*bytes:([0-9]*)\s*\(([0-9]*\.*[0-9]* [a-zA-z]*)\)\s*TX\s*bytes:([0-9]*)\s*\(([0-9]*\.*[0-9]*\s*[a-zA-z]*)\)~",
);