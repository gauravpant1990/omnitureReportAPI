#!/usr/bin/env bash
i=0
while [[ $i -lt 1000 ]];
do
php fillCategory.php $i
((i++))
done