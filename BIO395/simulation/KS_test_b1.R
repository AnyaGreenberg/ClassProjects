options(warn=-1)
args=(commandArgs(TRUE))

data_name <- args[1] #"HRDNA_Stephan"
trial <- args[7]
sim <- args[8] #

#parameters
pop.s <- args[2] 
iniCN <- args[3] 
exchg.r <- args[4] 
sel.coeff <- args[5] 
gen <- args[6] 

suppressMessages(library(dgof))
suppressMessages(library(fitdistrplus))

sim_emp <- "sim1"
thres <- 0.01 #alpha for KS test

#### READ EMPIRICAL DATA ###
emp_vec <- 1:as.integer(pop.s)*0

file2_name <- paste(data_name, "_dist_popsize_2N", pop.s, "_iniCN", iniCN, "_exchgR", exchg.r, "_selCoeff", sel.coeff, "_gen", gen, "_", sim_emp, "_emp.txt", sep="")
file2 <- read.table(file2_name, header=TRUE)
file2 <- file2[file2$gen == as.integer(gen),]

#get total copy number frequency
cnt <- 1
for (i in 1:length(file2$nIndiv)) {
  ind <- file2$nIndiv[i]
  CN <- file2$nCN[i]
  
  for (j in cnt:(cnt+ind-1)) { 
    emp_vec[j] <- CN
  }
  cnt <- cnt+ind
}

### READ SIMULATED DATA ###
cmp_vec <- 1:as.integer(pop.s)*0

file1_name <- paste(data_name, "_dist_popsize_2N", pop.s, "_iniCN", iniCN, "_selCoeff", sel.coeff, "_gen", gen, "_trial", trial, "_sim", sim, ".txt", sep="")
file1 <- read.table(file1_name, header=TRUE)

file1 <- file1[file1$gen == as.integer(gen),]

cnt <- 1
for (i in 1:length(file1$nIndiv)) {
  ind <- file1$nIndiv[i]
  CN <- file1$nCN[i]
  
  for (j in cnt:(cnt+ind-1)) {
    cmp_vec[j] <- CN
  }
  
  cnt <- cnt+ind
}

### DISTRIBUTION STATISTICS ###
mode <- function(x) {
  uniqx <- unique(x)
  uniqx[which.max(tabulate(match(x, uniqx)))]
}
mu <- mean(cmp_vec)
med <- median(cmp_vec)
mod <- mode(cmp_vec)
stdev <- sd(cmp_vec)
fit <- fitdist(cmp_vec, distr="gamma", method="mle", lower=c(0,0))
alpha <- summary(fit)$estimate[1]
beta <- summary(fit)$estimate[2]

### NORMALIZE ###
emp_vec <- (emp_vec-mean(emp_vec))/sd(emp_vec)
cmp_vec <- (cmp_vec-mean(cmp_vec))/sd(cmp_vec)

### KS-TEST ###
p_value <- ks.test(emp_vec, cmp_vec)$p.value
accept <- 0
if(p_value > thres) {#thres=0.01 -- ">" since we want to accept null hypothesis
  accept <- 1
}

#write out to table
file3_name <- paste(data_name, "_pvalue_popsize_2N", pop.s, "_iniCN", iniCN, "_gen", gen, "_trial", trial, "_", sim_emp, "_emp_exchgR", exchg.r, ".txt", sep="")
d <- data.frame(sim_n=sim, p_val=p_value, mean=mu, median=med, mode=mod, sd=stdev)
write.table(d, file3_name, append=TRUE, quote=FALSE, row.names=FALSE, col.names=FALSE, sep="\t")

#write out to console
cat(mu, "\t", med, "\t", mod, "\t", stdev, "\t", alpha, "\t", beta, "\t", p_value, "\t", accept, "\n")
