#!/bin/bash
export TERM=xterm
export domain=$(cat /etc/xray/domain)
MYIP=$(wget -qO- icanhazip.com)
clear
read -p "   Username: " Login

read -p "   Expired (days): " masaaktif
uuid=$(cat /proc/sys/kernel/random/uuid)


exp=`date -d "$masaaktif days" +"%Y-%m-%d"`
sed -i '/#vmess$/a\### '"$user $exp"'\
},{"id": "'""$uuid""'","alterId": '"0"',"email": "'""$user""'"' /etc/xray/config.json

VmessTLS=`cat<<EOF
      {
      "v": "2",
      "ps": "${user}",
      "add": "${domain}",
      "port": "443",
      "id": "${uuid}",
      "aid": "0",
      "net": "ws",
      "path": "/vmess",
      "type": "none",
      "host": "",
      "tls": "tls"
}
EOF`


VmessNTLS=`cat<<EOF
      {
      "v": "2",
      "ps": "${user}",
      "add": "${domain}",
      "port": "80",
      "id": "${uuid}",
      "aid": "0",
      "net": "ws",
      "path": "/vmess",
      "type": "none",
      "host": "",
      "tls": "none"
}
EOF`

vmess_base641=$( base64 -w 0 <<< $vmess_json1)
vmess_base642=$( base64 -w 0 <<< $vmess_json2)

https="vmess://$(echo $VmessTLS | base64 -w 0)"
http="vmess://$(echo $VmessNTLS | base64 -w 0)"

systemctl restart xray

echo -e "$Login"
echo -e "$exp"
echo -e "$https"
echo -e "$http"


# incomplet code
