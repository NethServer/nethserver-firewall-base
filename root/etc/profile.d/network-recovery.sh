
tty=$(/usr/bin/tty)
if [[ "$USER" = "root" ]] && [[ "${tty}" = /dev/tty* ]]; then
    echo ''
    echo '(!) Hint'
    echo '    Run the "network-recovery" command '
    echo '    to quickly assign a temporary IP address'
    echo ''
fi
