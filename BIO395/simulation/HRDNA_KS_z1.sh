#!/bin/bash
#./HRDNA_KS_z1.sh 5000 500 0.0000001 1000 1000
empname=hrdna_emp
progname=hrdna_KS1
data_name=HRDNA_Stephan
exchg_emp=0.10000
sim_emp=sim1
trial=1

#parameters -- arguments: 1-popsize_2N(5000), 2-iniCN(random), 3-selCoeff(0.0000001), 4-time_g(1000), 5-n(1)?
popsize_2N=$1
iniCN=$2
selCoeff=$3
time_g=$4
n=$5 

#conditional variables
nLines=1 #initial condition
jump_prt=1000 #save every ...

# module load gsl
# module load r/3.6.2/b1

#compile programs (emp and sim)
gcc -Wall -o hrdna_emp HRDNA_Stephan_KS_emp.c -lgsl -lgslcblas -lm -w
gcc -Wall -o hrdna_KS1 HRDNA_Stephan_KS1.c -lgsl -lgslcblas -lm -w

#create empty file named $file1 for storing (+)
./hrdna_emp $popsize_2N $iniCN 0.1 $selCoeff $time_g 1
file1=${data_name}_pvalue_popsize_2N${popsize_2N}_iniCN${iniCN}_selCoeff${selCoeff}_gen${time_g}_trial${trial}_${sim_emp}_emp_exchgR${exchg_emp}.txt
touch $file1
echo -e "sim_n\tp_val\tmean\tmedian\tmode\tsd" >> $file1 

#print columns to console
echo -e "sim_n\tseed\tpopsize_2N\tiniCN\tselCoeff\ttime_g\texchg_r\tmean\tmedian\tmode\tsd\talpha\tbeta\tp_val\tclass"

#continue until number of sims=n?
i=1
while :
do

#run program
./$progname $popsize_2N $iniCN $selCoeff $time_g $trial $i
Rscript KS_test_b1.R $data_name $popsize_2N $iniCN $exchg_emp $selCoeff $time_g $trial $i

#store number of lines in file1
lines=$(wc -l < $file1)

#create file2 var
file2=${data_name}_dist_popsize_2N${popsize_2N}_iniCN${iniCN}_selCoeff${selCoeff}_gen${time_g}_trial${trial}_sim${i}.txt

#file cleanup
if [[ $lines -eq $nLines ]]; then #n=1000
if [[ $(($i % $jump_prt)) -ne 0 ]]; then #save every jump_prt file
rm $file2
fi

else
if [[ $lines -gt 50 ]]; then 
rm $file2
fi
nLines=$lines
fi

#end condition
if [[ $nLines -eq $n ]]; then 
break
fi

i=$(($i+1)) #increment
done
