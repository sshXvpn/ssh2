#!/bin/bash
export TERM=xterm
export domain=$(cat /etc/xray/domain)
MYIP=$(wget -qO- icanhazip.com)
clear
read -p "   Username: " Login

read -p "   Expired (days): " masaaktif
uuid=$(cat /proc/sys/kernel/random/uuid)

echo -e "$Login"
echo -e "$exp"
echo -e "$https" #port 443

echo -e "$http" #port 80

# incomplet code
