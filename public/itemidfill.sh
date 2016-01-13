#!/usr/bin/env bash
i=0
while [[ $i -lt 10 ]];
do
php fillItemId.php $i
((i++))
done