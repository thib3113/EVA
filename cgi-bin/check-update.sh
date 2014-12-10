#! /bin/bash
apt-get -s -o Debug::NoLocking=true update
apt-get -s -o Debug::NoLocking=true upgrade | grep ^Inst 