//gcc -Wall -o hrdna_emp HRDNA_Stephan.c -lgsl -lgslcblas -lm
//./hrdna_emp $popsize_2N $iniCN $exchg_r $sel_coeff $time_g $sim
//./hrdna_emp 10000 100 0.10000 0.00010 1000 1

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <math.h>
#include <time.h>
#include <gsl/gsl_rng.h>
#include <gsl/gsl_randist.h>
#define gen_jump 100

void Setup_CNgroup(FILE* outF1);
void Rand_mating(void);
int Which_CNgroup(int ind);
int Exchg_copy(int g_ind1, int g_ind2);
void CNgroup_new(int new_CN);
void Build_nextGen(void);
void Print_file(int gen, FILE* outF1);
void Elim_empty_g(int g_index);

typedef struct {
    int nIndiv_o;
    int nIndiv_n;
    int nCN;
} group_CN;

int CN_LLimit;
double CN_ULimit;
int sim;

group_CN* group;
int nGroup = 1;

//randomization variables
unsigned long my_seed;
const gsl_rng_type* T;
gsl_rng* r;

//parameters
int popsize_2N, popsize_N;
int ini_CN;
double exchg_r;  //rate of exchange per cluster and generation for a certain pair of chromosomes
double sel_coeff; //per repeat unit
int time_g;

int main(int argc, char* argv[]) {
    popsize_2N = atoi(argv[1]); 
    ini_CN = atoi(argv[2]); //ini_CN is always far away from CN_ULimit
    exchg_r = atof(argv[3]); 
    sel_coeff = atof(argv[4]); 
    time_g = atoi(argv[5]);
    sim = atoi(argv[6]);

    int h, i; 
    FILE *outFile1;
    char fName[200];

    //randomization initializations
    srand(time(NULL));

    gsl_rng_env_setup();
    T = gsl_rng_default;
    r = gsl_rng_alloc(T);

    my_seed = rand();
    gsl_rng_set(r, my_seed);

    //set limits for copy number
    popsize_N = (int)(popsize_2N / 2.0);
    CN_LLimit = 1; CN_ULimit = 1 / sel_coeff + 1;

    //error if starting CN is out of limit bounds
    if (ini_CN < CN_LLimit || ini_CN > CN_ULimit) {
        printf("EXIT: initial copy number (%d) is out of range.\n", ini_CN);
        exit(0);
    } else { 
        //print gen, nCN, and nIndiv to outFile1
        sprintf(fName, "HRDNA_Stephan_dist_popsize_2N%d_iniCN%d_exchgR%.5lf_selCoeff%.7lf_gen%d_sim%d_emp.txt", popsize_2N, ini_CN, exchg_r, sel_coeff, time_g, sim);
        outFile1 = fopen(fName, "w"); fputs("gen\tnCN\tnIndiv\n", outFile1);
        
        Setup_CNgroup(outFile1); //initialize first generation

        //loop for each generation
        for (h = 0; h < time_g; h++) {
            Rand_mating(); //random mating simulation
            Build_nextGen(); //create next generation

            for (i = nGroup - 1; i >= 0; i--) Elim_empty_g(i); //remove empty generations

            Print_file(h + 1, outFile1); //print gen, nCN, and nIndiv (for that nCN) to outFile1
    
            //reset nIndiv for each group
            for (i = 0; i < nGroup; i++) {
                group[i].nIndiv_o = group[i].nIndiv_n;
                group[i].nIndiv_n = 0;
            }
        }
    }

   if (nGroup != 0) free(group);

   gsl_rng_free(r); 
   fclose(outFile1);

   return 0;
}

//print gen, nCN, and nIndiv (for that nCN) to outF1
void Print_file(int gen, FILE *outF1) {    
    int j;

    if (gen % gen_jump == 0) {
        for (j = 0; j < nGroup; j++) {
            fprintf(outF1, "%d\t%d\t%d\n", gen, group[j].nCN, group[j].nIndiv_n);
        }
    }
 
    return;
}

//???
void Build_nextGen(void) {
    double add_p = 0;
    double g_fit;
    int k, h;

    double* multinom_p = (double*)malloc(sizeof(double) * nGroup);
    unsigned int* g_size = (unsigned int*)malloc(sizeof(unsigned int) * nGroup);


    for (k = 0; k < nGroup; k++) {
        g_fit = 1 - sel_coeff*(group[k].nCN - 1);
        if (g_fit < 0) g_fit = 0;

        multinom_p[k] = g_fit * group[k].nIndiv_n;
        add_p += multinom_p[k];
    }

    for (h = 0; h < nGroup; h++) multinom_p[h] /= add_p; 

    gsl_ran_multinomial(r, nGroup, popsize_2N, multinom_p, g_size);
    
    for (h = 0; h < nGroup; h++) {
        group[h].nIndiv_n = (int)g_size[h];
    }
        
    free(multinom_p); free(g_size);
    return;
}

//remove empty generations
void Elim_empty_g(int g_index) {
    if (group[g_index].nIndiv_n == 0) {
        if (g_index < nGroup - 1) memmove(&group[g_index], &group[g_index + 1], sizeof(group_CN) * (nGroup - g_index - 1));
        nGroup--; 
        group = (group_CN*)realloc(group, sizeof(group_CN) * nGroup);
    }
 
    return;
}

//initialize first generation
void Setup_CNgroup(FILE* outF1) {
    group = (group_CN*)malloc(sizeof(group_CN) * nGroup);
    
    group[0].nCN = ini_CN;
    group[0].nIndiv_o = popsize_2N;
    group[0].nIndiv_n = 0;

    fprintf(outF1, "%d\t%d\t%d\n", 0, group[0].nCN, group[0].nIndiv_o);

    return;
}

//determine which CN group each parent will give to child???
int Which_CNgroup(int ind) {
    int j, cnt = 0;
    int group_index;

    for (j = 0; j < nGroup; j++) {
        cnt += group[j].nIndiv_o;

        if (cnt > ind) {
            group_index = j;
            break;
        }
    }

    return group_index;
}

//echange occurs if prob is less than the adding probability
int Exchg_copy(int g_ind1, int g_ind2) {
    int j, k, i;
    double exchg_prob;
    double c;
    double prob, add_p = 0;
    int cn1;    

    j = group[g_ind1].nCN;
    k = group[g_ind2].nCN;

    prob = gsl_rng_uniform(r);

    //calculate c
    if ((j + k) % 2 == 0) c = 2.0 / (j + k);
    else c = 2.0 * (j + k) / (pow(j + k, 2) - 1);

    for (i = 1; i < (j + k); i++) {
        exchg_prob = c * (1 - fabs(2.0 * i / (j + k) - 1));
        add_p += exchg_prob;

        if (add_p > prob) {
            cn1 = i; 
            break;
        }
    }
    
    return cn1;
}

//create new CN group with 1 individual
void CNgroup_new(int new_CN) {
    int h;
    int cnt = 0;

    for (h = 0; h < nGroup; h++) {
        if (new_CN == group[h].nCN) group[h].nIndiv_n++;
        else cnt++;
    }
    
    if (cnt == nGroup) {
        if (new_CN < CN_ULimit) {
            nGroup++; 
            group = (group_CN*)realloc(group, sizeof(group_CN) * nGroup);
            group[nGroup - 1].nCN = new_CN;
            group[nGroup - 1].nIndiv_o = 0;
            group[nGroup - 1].nIndiv_n = 1;
        }
    }
    
    return;
}

//random mating
void Rand_mating(void) {
    int i1, i2;
    unsigned long int newsize = popsize_2N;
    int g1_index, g2_index;
    int n_CN, n_CN1, n_CN2;
    int addsize = 0;
    double a;
    
    //do until 2N created
    do {
        //random selection of parent CN size
        i1 = newsize*gsl_rng_uniform(r);
        i2 = newsize*gsl_rng_uniform(r);
       
        if (i1 != i2) {   
            //determine which CN group each parent will give to child???
            g1_index = Which_CNgroup(i1);
            g2_index = Which_CNgroup(i2);

            if (gsl_rng_uniform(r) < exchg_r) { //if a random number is less than exchg_r, accept exchange           
                n_CN = Exchg_copy(g1_index, g2_index);
                CNgroup_new(n_CN); 
            } else {
                n_CN1 = group[g1_index].nCN;
                n_CN2 = group[g2_index].nCN;
                
                a = gsl_rng_uniform(r);
                
                if (a < 0.5) n_CN = n_CN1;
                else n_CN = n_CN2;
                
                CNgroup_new(n_CN);
            }
            
            addsize++;
        }  
    } while (addsize < newsize);
    
    return;
}
