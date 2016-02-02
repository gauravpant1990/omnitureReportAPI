i=0
while [[ $i -lt 5 ]];
do
php fillBrand.php $i $1
((i++))
done