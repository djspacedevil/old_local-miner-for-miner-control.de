#!/bin/bash
i=1
while [ $i -le 5 ]
do
	php /var/www/miner-control/index.php &> /dev/null
	i=`expr $i + 1`
	sleep 10
done