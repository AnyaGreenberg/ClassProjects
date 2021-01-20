setwd("C:/Users/durbe/Documents/Repos/Learning/STT212")

data <- read.csv("./data.csv")


### ANOVA ###

price <- data[,17:28]
colnames(price) <- c("Alternative", "Classical", "Country", "Dance/Electronic", 
                     "Hip-Hop/Rap", "Jazz", "K-pop", "Metal/Rock", "Pop",
                     "RnB/Soul", "Trap", "Other")

price[price == ""] <- 0
price[price == "$0"] <- 0
price[price == "$1-25"] <- 25
price[price == "$26-50"] <- 50
price[price == "$51-75"] <- 75
price[price == "$76-100"] <- 100
price[price == "$101-125"] <- 125
price[price == "$126-150"] <- 150
price[price == "$151-175"] <- 175
price[price == "$176-200"] <- 200
price[price == "$200+"] <- 375

g <- NULL
p <- NULL

for (i in 1:length(colnames(price))) {
  for (j in 1:length(rownames(price))) {
    g <- append(g, colnames(price)[i])
    p <- append(p, price[j,i])
  }
}

l <- data.frame(genre=g, price=p)

fit <- aov(l$price~l$genre, data=l)
summary(fit)

tukey <- TukeyHSD(fit)
write.csv(as.data.frame(tukey[["l$genre"]]), "./tukey.csv")
