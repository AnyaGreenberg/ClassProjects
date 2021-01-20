-- USERS
CREATE TABLE USERS (
    Email varchar(255) NOT NULL PRIMARY KEY,
    Password varchar(255) NOT NULL,
    User_type varchar(255) NOT NULL
);

-- ADOPTER 
CREATE TABLE ADOPTER (
    Ssn varchar(255) NOT NULL PRIMARY KEY,
    First_name varchar(255) NOT NULL,
    Last_name varchar(255) NOT NULL,
    Address varchar(255) NOT NULL,
    Current_pets int NOT NULL,
    Budget int NOT NULL,
    Landlord_permission boolean NOT NULL
);

-- ADOPTER_EMAIL /
CREATE TABLE ADOPTER_EMAIL (
    Email varchar(255) NOT NULL PRIMARY KEY,
    Ssn varchar(255) NOT NULL,
    FOREIGN KEY(Email) REFERENCES USERS(Email),
    FOREIGN KEY(Ssn) REFERENCES ADOPTER(Ssn)
);

-- PHONE_NUMBER - assumes every adopter has a different phone number /
CREATE TABLE PHONE_NUMBER (
    Phone_number varchar(255) NOT NULL PRIMARY KEY,
    Ssn varchar(255) NOT NULL,
    FOREIGN KEY(Ssn) REFERENCES ADOPTER(Ssn)
);

-- ADMINS /
CREATE TABLE ADMINS (
    Admin_id int NOT NULL PRIMARY KEY,
    Name_of_shelter varchar(255) NOT NULL
);

-- ADMIN_EMAIL /
CREATE TABLE ADMIN_EMAIL (
    Email varchar(255) NOT NULL PRIMARY KEY,
    Admin_id int NOT NULL,
    FOREIGN KEY(Email) REFERENCES USERS(Email),
    FOREIGN KEY(Admin_id) REFERENCES ADMINS(Admin_id)
);

-- ADMIN_ADDRESS /
CREATE TABLE ADMIN_ADDRESS (
    Address varchar(255) NOT NULL PRIMARY KEY,
    Admin_id int NOT NULL,
    FOREIGN KEY(Admin_id) REFERENCES ADMINS(Admin_id)
);

-- PET_INFO /
CREATE TABLE PET_INFO (
    Pet_id int NOT NULL PRIMARY KEY,
    Ptype varchar(255) NOT NULL,
    Pname varchar(255) NOT NULL DEFAULT 'unnamed',
    Age_year int NOT NULL DEFAULT 0,
    Age_month int NOT NULL DEFAULT 0,
    Origin varchar(255) NOT NULL,
    Neutered_spade boolean NOT NULL,
    Diet varchar(255) NOT NULL,
    Personality varchar(255) NOT NULL,
    Admin_id int NOT NULL,
    FOREIGN KEY(Admin_id) REFERENCES ADMINS(Admin_id)
);

-- ADOPTION_REQUESTS /
CREATE TABLE ADOPTION_REQUESTS (
    Pet_id int NOT NULL,
    Ssn varchar(255) NOT NULL,
    FOREIGN KEY(Pet_id) REFERENCES PET_INFO(Pet_id),
    FOREIGN KEY(Ssn) REFERENCES ADOPTER(Ssn),
    PRIMARY KEY (Pet_id, Ssn)
);

-- ADOPTER_HABITS /
CREATE TABLE ADOPTER_HABITS (
    Ssn varchar(255) NOT NULL,
    Habit varchar(255) NOT NULL,
    FOREIGN KEY(Ssn) REFERENCES ADOPTER(Ssn),
    PRIMARY KEY (Ssn, Habit)
);

-- PET_IMAGES /
CREATE TABLE PET_IMAGES (
    Pet_id int NOT NULL,
    Image varchar(255) NOT NULL,
    FOREIGN KEY(Pet_id) REFERENCES PET_INFO(Pet_id),
    PRIMARY KEY (Pet_id, Image)
);

-- BREED /
CREATE TABLE BREED (
    Pet_id int NOT NULL,
    Breed varchar(255) NOT NULL,
    FOREIGN KEY(Pet_id) REFERENCES PET_INFO(Pet_id),
    PRIMARY KEY(Pet_id, Breed)
);
