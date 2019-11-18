
tty=$(/usr/bin/tty)
if [[ "$USER" = "root" ]] && [[ "${tty}" = /dev/tty* ]]; then
    echo ''
    echo '(!) Hint'
    echo '    In case of network troubles, run the "network-recovery" command '
    echo '    to quickly assign a temporary IP address'
    echo ''
fi
