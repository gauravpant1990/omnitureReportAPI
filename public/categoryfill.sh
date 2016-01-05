#!/usr/bin/env bash
i=0
while [[ $i -lt 1000 ]];
do
php index.php $i
((i++))
done