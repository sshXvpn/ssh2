#!/bin/bash
export TERM=xterm
export domain=$(cat /etc/xray/domain)
MYIP=$(wget -qO- icanhazip.com)
clear
read -p "   Username: " Login
read -p "   Password: " Pass
read -p "   Expired (days): " masaaktif
if id "$Login" &>/dev/null; then
    echo "User '$Login' already exists."
    exit
  else
    if [ ! -d "/usr/local/sbin/xray" ]; then
     mkdir -p "/usr/local/sbin/xray"
    fi
    echo "### ${Login} ${Pass} ${masaaktif}" >> /usr/local/sbin/xray/ssh.txt
fi
useradd -e $(date -d "$masaaktif days" +"%Y-%m-%d") -s /bin/false -M $Login
exp="$(chage -l $Login | grep "Account expires" | awk -F": " '{print $2}')"
echo -e "$Pass\n$Pass\n" | passwd $Login &> /dev/null

echo -e "$MYIP" #var1
echo -e "$Login" #var2
echo -e "$Pass" #var3
echo -e "$masaaktif" #var4
echo -e "1-2288" #var5
echo -e "443" #var6
echo -e "80" #var7
echo -e "53" #var8
echo -e "1194" #var9

#var 1-9 output yang akan kita panggil dalam php, korang boleh tambah ikut keperluan

