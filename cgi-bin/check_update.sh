#! /bin/bash
sudo -u eva apt-get -o Debug::NoLocking=true update
sudo -u eva apt-get -s -o Debug::NoLocking=true upgrade | grep ^Inst 2> /dev/null