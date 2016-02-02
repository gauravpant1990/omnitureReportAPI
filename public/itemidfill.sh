#!/usr/bin/env bash
i=0
while [[ $i -lt 2 ]];
do
php fillItemId.php $i $1
((i++))
done