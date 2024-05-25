-- =========================================
-- DROP statement
-- =========================================
DROP TABLE Have;
DROP TABLE AdoptionRecord;
DROP TABLE RetiredAnimal_Adopt;
DROP TABLE OfferTo;
DROP TABLE TherapyPatient;
DROP TABLE ConductBy;
DROP TABLE TherapySession_Assigned_R2;
DROP TABLE TherapySession_Assigned_R1;
DROP TABLE TherapyAnimal;
DROP TABLE DisabledPerson;
DROP TABLE QualifiedDog;
DROP TABLE RescueOrganization;
DROP TABLE Breeder;
DROP TABLE Adopter;
DROP TABLE UnqualifiedDog_Train_R1;
DROP TABLE UnqualifiedDog_Train_R2;
DROP TABLE UnqualifiedDog_Train_R3;
DROP TABLE UnqualifiedDog_Train_R4;
DROP TABLE ServiceDog;
DROP TABLE Animal_From;
DROP TABLE Origin;
DROP TABLE Staff;


-- =========================================
-- CREATE statement
-- =========================================
CREATE TABLE Origin ( 
  OriginID INT, 
  ContactNumber CHAR(20), 
  Name CHAR(50), 
  Location CHAR(60), 
  PRIMARY KEY (OriginID)
);

CREATE TABLE Breeder ( 
  OriginID INT, 
  LicenseNumber INT NOT NULL, 
  PRIMARY KEY (OriginID), 
  FOREIGN KEY (OriginID) REFERENCES Origin(OriginID)
    ON DELETE CASCADE
    ON UPDATE CASCADE, 
  UNIQUE (LicenseNumber)
);

CREATE TABLE RescueOrganization ( 
  OriginID INT, 
  OrganizationID INT NOT NULL, 
  PRIMARY KEY (OriginID), 
  FOREIGN KEY (OriginID) REFERENCES Origin(OriginID)
    ON DELETE CASCADE
    ON UPDATE CASCADE, 
  UNIQUE (OrganizationID)
);

CREATE TABLE Animal_From ( 
  MicrochipNumber INT, 
  BirthDate DATE, 
  HealthStatus CHAR(20), 
  Species CHAR(20), 
  Age INT, 
  Temperament CHAR(20), 
  Name CHAR(20), 
  OriginID INT NOT NULL, 
  PRIMARY KEY (MicrochipNumber), 
  FOREIGN KEY (OriginID) REFERENCES Origin(OriginID)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE ServiceDog ( 
  MicrochipNumber INT, 
  ServiceCertification CHAR(20), 
  PRIMARY KEY (MicrochipNumber), 
  FOREIGN KEY (MicrochipNumber) REFERENCES Animal_From(MicrochipNumber)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE QualifiedDog ( 
  MicrochipNumber INT, 
  QualificationDate DATE, 
  SkillSet CHAR(50), 
  PRIMARY KEY (MicrochipNumber), 
  FOREIGN KEY (MicrochipNumber) REFERENCES ServiceDog(MicrochipNumber)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE DisabledPerson ( 
  Name CHAR(20), 
  ContactNumber CHAR(20), 
  Age INT, 
  MedicalCondition CHAR(255), 
  MicrochipNumber INT, 
  PRIMARY KEY (Name, ContactNumber), 
  FOREIGN KEY (MicrochipNumber) REFERENCES QualifiedDog(MicrochipNumber)
    ON DELETE SET NULL
    ON UPDATE CASCADE, 
  UNIQUE (MicrochipNumber)
);

CREATE TABLE Staff ( 
  StaffID INT, 
  Name CHAR(20), 
  Role CHAR(20), 
  PRIMARY KEY (StaffID)
);

CREATE TABLE UnqualifiedDog_Train_R1 ( 
  MicrochipNumber INT, 
  TrainingStatus CHAR(20), 
  PRIMARY KEY (MicrochipNumber), 
  FOREIGN KEY (MicrochipNumber) REFERENCES ServiceDog(MicrochipNumber)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE UnqualifiedDog_Train_R2( 
  MicrochipNumber INT,
  StartDate DATE, 
  PRIMARY KEY (MicrochipNumber), 
  FOREIGN KEY (MicrochipNumber) REFERENCES ServiceDog(MicrochipNumber)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE UnqualifiedDog_Train_R3(
  MicrochipNumber INT,
  StaffID INT, 
  PRIMARY KEY (MicrochipNumber), 
  FOREIGN KEY (MicrochipNumber) REFERENCES ServiceDog(MicrochipNumber)
    ON DELETE CASCADE
    ON UPDATE CASCADE, 
  FOREIGN KEY (StaffID) REFERENCES Staff(StaffID)
    ON DELETE SET NULL
    ON UPDATE CASCADE
);

CREATE TABLE UnqualifiedDog_Train_R4( 
  StartDate DATE, 
  DaysRemaining INT, 
  PRIMARY KEY (StartDate)
);

CREATE TABLE TherapyAnimal ( 
  MicrochipNumber INT, 
  TherapyCertification INT, 
  PRIMARY KEY (MicrochipNumber), 
  FOREIGN KEY (MicrochipNumber) REFERENCES Animal_From(MicrochipNumber)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- TherapySession has total participation

CREATE TABLE TherapySession_Assigned_R1 ( 
  SessionType CHAR(20), 
  SessionLength CHAR(20), 
  MaxCapacity INT,
  PRIMARY KEY (SessionType)
);

CREATE TABLE TherapySession_Assigned_R2 ( 
  SessionDate DATE, 
  SessionType CHAR(20), 
  MaxCapacity INT,
  MicrochipNumber INT NOT NULL, 
  PRIMARY KEY (SessionDate, MicrochipNumber), 
  FOREIGN KEY (MicrochipNumber) REFERENCES TherapyAnimal(MicrochipNumber)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (SessionType) REFERENCES TherapySession_Assigned_R1(SessionType)
);


CREATE TABLE ConductBy ( 
  StaffID INT, 
  SessionDate DATE, 
  MicrochipNumber INT, 
  PRIMARY KEY (StaffID, SessionDate, MicrochipNumber), 
  FOREIGN KEY (StaffID) REFERENCES Staff(StaffID), 
  FOREIGN KEY (SessionDate, MicrochipNumber) REFERENCES TherapySession_Assigned_R2 (SessionDate, MicrochipNumber)
);

CREATE TABLE TherapyPatient ( 
  Name CHAR(20), 
  ContactNumber CHAR(20), 
  Age INT, 
  TherapyReason CHAR(60), 
  PRIMARY KEY (Name, ContactNumber)
);

CREATE TABLE OfferTo ( 
  Name CHAR(20), 
  ContactNumber CHAR(20), 
  SessionDate DATE, 
  MicrochipNumber INT, 
  PRIMARY KEY (Name, ContactNumber, SessionDate, MicrochipNumber), 
  FOREIGN KEY (Name, ContactNumber) REFERENCES TherapyPatient(Name, ContactNumber)
    ON DELETE CASCADE
    ON UPDATE CASCADE, 
  FOREIGN KEY (SessionDate, MicrochipNumber) REFERENCES TherapySession_Assigned_R2 (SessionDate, MicrochipNumber)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE Adopter ( 
  Address CHAR(60), 
  ContactNumber CHAR(20), 
  AdopterName CHAR(20), 
  MicrochipNumber INT,
  PRIMARY KEY (ContactNumber, AdopterName),
  FOREIGN KEY (MicrochipNumber) REFERENCES Animal_From(MicrochipNumber)
    ON DELETE SET NULL
    ON UPDATE CASCADE
);

CREATE TABLE RetiredAnimal_Adopt ( 
  MicrochipNumber INT, 
  RetiredDate Date, 
  ReasonForRetirement CHAR(60), 
  ContactNumber CHAR(20), 
  AdopterName CHAR(20), 
  PRIMARY KEY (MicrochipNumber), 
  FOREIGN KEY (MicrochipNumber) REFERENCES Animal_From(MicrochipNumber)
    ON DELETE CASCADE
    ON UPDATE CASCADE, 
  FOREIGN KEY (ContactNumber, AdopterName) REFERENCES Adopter(ContactNumber, AdopterName)
    ON DELETE SET NULL
    ON UPDATE CASCADE
);

CREATE TABLE AdoptionRecord ( 
  RecordID INT, 
  AdoptionDate DATE, 
  MicrochipNumber INT NOT NULL, 
  ContactNumber CHAR(20) NOT NULL, 
  AdopterName CHAR(20) NOT NULL, 
  PRIMARY KEY (RecordID), 
  FOREIGN KEY (MicrochipNumber) REFERENCES RetiredAnimal_Adopt(MicrochipNumber)
    ON DELETE CASCADE
    ON UPDATE CASCADE, 
  FOREIGN KEY (ContactNumber, AdopterName) REFERENCES Adopter(ContactNumber, AdopterName)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  UNIQUE (MicrochipNumber, ContactNumber, AdopterName)
);

CREATE TABLE Have ( 
  RecordID INT, 
  MicrochipNumber INT,  
  PRIMARY KEY (RecordID, MicrochipNumber), 
  FOREIGN KEY (RecordID) REFERENCES AdoptionRecord(RecordID)
    ON DELETE CASCADE
    ON UPDATE CASCADE, 
  FOREIGN KEY (MicrochipNumber) REFERENCES RetiredAnimal_Adopt(MicrochipNumber)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);


-- =========================================
-- INSERT statement
-- =========================================
INSERT INTO Origin (OriginID, ContactNumber, Name, Location) VALUES 
(101, '654-321-9870', 'Lone Star Beagle Ranch', 'Dallas, TX'),
(102, '876-543-2109', 'Paws and Tails Shelter', 'Houston, TX'),
(103, '987-654-3210', 'Safe Haven Canine Care', 'New York, NY'),
(104, '098-765-4321', 'Loyal Companions Rescue', 'Atlanta, GA'),
(105, '109-876-5432', 'Woof Warriors Association', 'Chicago, IL'),
(106, '210-555-6789', 'Happy Tails Cat Sanctuary', 'Los Angeles, CA'),
(107, '321-555-9876', 'Bunny Buddies', 'San Francisco, CA'),
(108, '432-555-7654', 'Gentle Giants Horse Rescue', 'Lexington, KY'),
(109, '543-555-6543', 'Cuddly Capybaras', 'Miami, FL'),
(110, '654-555-5432', 'Feathered Friends Aviary', 'Seattle, WA'),
(201, '765-555-4321', 'Critter Companions', 'Portland, OR'),
(202, '876-555-3210', 'Meadow Haven Rabbitry', 'Denver, CO'),
(203, '987-555-2109', 'Purrfect Feline Friends', 'Boston, MA'),
(204, '098-555-1098', 'Canine Country Club', 'Phoenix, AZ'),
(205, '109-555-0987', 'Urban Paws', 'Philadelphia, PA'),
(206, '210-555-0876', 'Rustic Rabbit Retreat', 'Austin, TX'),
(207, '321-555-0765', 'Horse Haven Farm', 'Nashville, TN'),
(208, '432-555-0654', 'Whisker Wonderland', 'Las Vegas, NV'),
(209, '543-555-0543', 'Feathers and Fur Sanctuary', 'Orlando, FL'),
(210, '654-555-0432', 'Pawsitive Companions', 'Minneapolis, MN');

INSERT INTO Breeder (OriginID, LicenseNumber) VALUES                         
(101, 1001),
(102, 1002),
(103, 1003),
(104, 1004),
(105, 1005),
(106, 1006),
(107, 1007),
(108, 1008),
(109, 1009),
(110, 1010);

INSERT INTO RescueOrganization (OriginID, OrganizationID) VALUES
(201, 2101),
(202, 2102),
(203, 2103),
(204, 2104),
(205, 2105),
(206, 2106),
(207, 2107),
(208, 2108),
(209, 2109),
(210, 2110);

INSERT INTO Animal_From (MicrochipNumber, BirthDate, HealthStatus, Species, Age, Temperament, Name, OriginID) VALUES 
(5001, '2022-01-15', 'Healthy', 'Dog', 1, 'Playful', 'Buddy', 101),
(5002, '2020-06-10', 'Healthy', 'Dog', 3, 'Calm', 'Snow', 102),
(5003, '2019-12-25', 'Requires Medication', 'Dog', 4, 'Energetic', 'Dottie', 103),
(5004, '2021-03-05', 'Healthy', 'Dog', 2, 'Friendly', 'Charlie', 104),
(5005, '2022-05-20', 'Healthy', 'Dog', 1, 'Shy', 'Lulu', 105),
(5006, '2021-08-11', 'Special Diet', 'Cat', 2, 'Independent', 'Whiskers', 106),
(5007, '2023-01-01', 'Healthy', 'Rabbit', 0, 'Curious', 'Thumper', 107),
(5008, '2016-04-30', 'Old Age Concerns', 'Cat', 3, 'Affectionate', 'Mittens', 108),
(5009, '2019-11-09', 'Healthy', 'Dog', 4, 'Patient', 'Baxter', 109),
(5010, '2022-02-22', 'Healthy', 'Rabbit', 1, 'Playful', 'Oreo', 110),
(5011, '2021-09-14', 'Healthy', 'Dog', 2, 'Gentle', 'Max', 201),
(5012, '2020-07-19', 'Healthy', 'Cat', 3, 'Sociable', 'Bella', 202),
(5013, '2018-12-05', 'Healthy', 'Dog', 5, 'Patient', 'Rocky', 203),
(5014, '2022-06-30', 'Healthy', 'Cat', 1, 'Quiet', 'Daisy', 204),
(5015, '2021-11-11', 'Healthy', 'Dog', 2, 'Loyal', 'Maggie', 205),
(5016, '2019-05-20', 'Healthy', 'Horse', 4, 'Calm', 'Spirit', 206),
(5017, '2020-10-31', 'Healthy', 'Horse', 3, 'Steady', 'Storm', 207),
(5018, '2021-07-14', 'Healthy', 'Capybara', 2, 'Docile', 'Cappy', 208),
(5019, '2018-03-22', 'Healthy', 'Rabbit', 5, 'Friendly', 'Nibbles', 209),
(5020, '2022-04-11', 'Healthy', 'Capybara', 1, 'Gentle', 'Paddy', 210),
(5021, '2018-07-11', 'Healthy', 'Capybara', 5, 'Friendly', 'Gaby', 101),
(5022, '2021-03-15', 'Healthy', 'Dog', 3, 'Alert', 'Scout', 101),
(5023, '2017-08-21', 'Special Diet', 'Rabbit', 6, 'Active', 'Bouncer', 101),
(5024, '2020-09-11', 'Healthy', 'Horse', 3, 'Strong', 'Blaze', 101),
(5025, '2019-01-25', 'Healthy', 'Cat', 4, 'Mysterious', 'Shadow', 102),
(5026, '2014-05-30', 'Old Age Concerns', 'Dog', 9, 'Calm', 'Buddy', 102),
(5027, '2021-10-07', 'Healthy', 'Rabbit', 2, 'Lively', 'Hop', 102),
(5028, '2022-07-19', 'Healthy', 'Dog', 1, 'Playful', 'Spark', 110),
(5029, '2021-11-09', 'Healthy', 'Cat', 2, 'Independent', 'Misty', 110),
(5030, '2020-12-31', 'Healthy', 'Rabbit', 3, 'Quiet', 'Fluffy', 110),
(5031, '2021-05-25', 'Healthy', 'Capybara', 2, 'Docile', 'Chewie', 210),
(5032, '2019-04-17', 'Healthy', 'Horse', 4, 'Trained', 'Ace', 210),
(5033, '2018-02-28', 'Healthy', 'Dog', 5, 'Energetic', 'Bolt', 210),
(5034, '2019-10-22', 'Requires Checkup', 'Cat', 4, 'Adventurous', 'Zelda', 101),
(5035, '2021-02-14', 'Healthy', 'Dog', 1, 'Friendly', 'Bingo', 101),
(5036, '2018-07-30', 'Healthy', 'Rabbit', 5, 'Timid', 'Coco', 102),
(5037, '2020-01-11', 'Healthy', 'Dog', 2, 'Protective', 'Rex', 102),
(5038, '2017-05-19', 'Healthy', 'Cat', 6, 'Playful', 'Milo', 103),
(5039, '2021-08-09', 'Healthy', 'Rabbit', 2, 'Curious', 'Patches', 103),
(5040, '2020-03-23', 'Healthy', 'Dog', 3, 'Obstinate', 'Tank', 104),
(5041, '2022-07-04', 'Healthy', 'Horse', 1, 'Gentle', 'Spirit', 104),
(5042, '2021-09-17', 'Healthy', 'Capybara', 2, 'Social', 'Chomper', 105),
(5043, '2018-12-12', 'Special Diet', 'Cat', 5, 'Independent', 'Salem', 105),
(5044, '2020-11-05', 'Healthy', 'Dog', 3, 'Vigilant', 'Duke', 106),
(5045, '2019-04-18', 'Healthy', 'Rabbit', 4, 'Quiet', 'Snowball', 106),
(5046, '2021-06-21', 'Healthy', 'Horse', 2, 'Majestic', 'Dawn', 107),
(5047, '2019-03-15', 'Healthy', 'Dog', 4, 'Loyal', 'King', 107),
(5048, '2022-08-30', 'Healthy', 'Cat', 1, 'Mischievous', 'Boots', 108),
(5049, '2017-07-07', 'Healthy', 'Rabbit', 6, 'Energetic', 'Thumper', 108),
(5050, '2021-10-31', 'Healthy', 'Dog', 2, 'Brave', 'Scout', 109),
(5051, '2020-02-20', 'Healthy', 'Horse', 3, 'Tranquil', 'Misty', 109),
(5052, '2018-09-09', 'Healthy', 'Capybara', 5, 'Playful', 'Splash', 110),
(5053, '2021-12-25', 'Healthy', 'Cat', 2, 'Cuddly', 'Ginger', 110),
(5054, '2022-03-08', 'Healthy', 'Dog', 1, 'Intelligent', 'Ace', 201),
(5055, '2019-06-11', 'Healthy', 'Rabbit', 4, 'Active', 'Dusty', 201),
(5056, '2020-05-14', 'Healthy', 'Cat', 3, 'Aloof', 'Princess', 202),
(5057, '2021-07-22', 'Healthy', 'Horse', 2, 'Composed', 'Belle', 202),
(5058, '2017-11-30', 'Healthy', 'Dog', 6, 'Alert', 'Shadow', 203),
(5059, '2022-04-01', 'Healthy', 'Rabbit', 1, 'Mellow', 'Fudge', 203),
(5060, '2021-08-10', 'Healthy', 'Dog', 3, 'Protective', 'Chief', 204),
(5061, '2020-05-20', 'Healthy', 'Cat', 2, 'Aloof', 'Sable', 204),
(5062, '2019-03-15', 'Healthy', 'Rabbit', 1, 'Gentle', 'Peanut', 204),
(5063, '2021-07-22', 'Healthy', 'Dog', 2, 'Eager', 'Scout', 205),
(5064, '2022-01-30', 'Healthy', 'Cat', 4, 'Lazy', 'Boots', 205),
(5065, '2018-11-11', 'Healthy', 'Horse', 5, 'Majestic', 'Buddy', 205),
(5066, '2020-09-09', 'Healthy', 'Capybara', 2, 'Curious', 'Munch', 206),
(5067, '2019-04-18', 'Healthy', 'Rabbit', 3, 'Alert', 'Sprout', 206),
(5068, '2021-06-24', 'Healthy', 'Dog', 1, 'Playful', 'Wags', 206),
(5069, '2022-03-05', 'Healthy', 'Cat', 2, 'Affectionate', 'Misty', 207),
(5070, '2020-12-12', 'Healthy', 'Horse', 4, 'Stoic', 'Noble', 207),
(5071, '2021-05-29', 'Healthy', 'Dog', 2, 'Watchful', 'Alert', 208),
(5072, '2019-07-07', 'Healthy', 'Capybara', 3, 'Playful', 'Splash', 208),
(5073, '2020-10-20', 'Healthy', 'Rabbit', 1, 'Skittish', 'Binky', 208),
(5074, '2018-02-22', 'Healthy', 'Cat', 6, 'Independent', 'Whisper', 209),
(5075, '2021-09-01', 'Healthy', 'Dog', 2, 'Loyal', 'Brave', 209),
(5076, '2020-06-06', 'Healthy', 'Horse', 3, 'Bold', 'Gallop', 209),
(5077, '2019-01-01', 'Healthy', 'Rabbit', 4, 'Quiet', 'Velvet', 210),
(5078, '2022-08-15', 'Healthy', 'Cat', 1, 'Pensive', 'Gaze', 210),
(5079, '2018-04-04', 'Healthy', 'Dog', 5, 'Companionable', 'Pal', 210),
(5080, '2019-01-15', 'Healthy', 'Dog', 4, 'Calm', 'Buddy', 101),
(5082, '2018-05-20', 'Healthy', 'Dog', 5, 'Energetic', 'Max', 102),
(5084, '2017-03-10', 'Healthy', 'Dog', 6, 'Friendly', 'Bella', 103),
(5086, '2020-07-22', 'Healthy', 'Dog', 3, 'Playful', 'Lucy', 104),
(5090, '2019-11-08', 'Healthy', 'Dog', 4, 'Shy', 'Charlie', 105),
(5092, '2021-02-14', 'Healthy', 'Dog', 2, 'Loyal', 'Daisy', 106),
(5093, '2020-09-30', 'Healthy', 'Dog', 3, 'Agile', 'Rocky', 107),
(5094, '2019-12-25', 'Healthy', 'Dog', 4, 'Intelligent', 'Molly', 108),
(5095, '2018-04-18', 'Healthy', 'Dog', 5, 'Gentle', 'Coco', 109),
(5096, '2021-06-10', 'Healthy', 'Dog', 2, 'Bold', 'Bailey', 110),
(5097, '2017-08-05', 'Healthy', 'Dog', 6, 'Docile', 'Sadie', 101),
(5098, '2020-01-20', 'Healthy', 'Dog', 3, 'Curious', 'Toby', 102);

INSERT INTO ServiceDog (MicrochipNumber, ServiceCertification) VALUES
(5001, 'Cert-A1'),
(5002, 'Cert-B2'),
(5003, 'Cert-C3'),
(5004, 'Cert-D4'),
(5005, 'Cert-E5'),
(5009, 'Cert-A2'),
(5011, 'Cert-A3'),
(5013, 'Cert-B3'),
(5015, 'Cert-B4'),
(5022, 'Cert-C1'),
(5026, 'Cert-A4'),
(5028, 'Cert-D1'),
(5033, 'Cert-D2'),
(5035, 'Cert-D3'),
(5037, 'Cert-E1'),
(5080, NULL),
(5082, NULL),
(5084, NULL),
(5086, NULL),
(5090, NULL),
(5092, NULL),
(5093, NULL),
(5094, NULL),
(5095, NULL),
(5096, NULL),
(5097, NULL),
(5098, NULL);

INSERT INTO QualifiedDog (MicrochipNumber, QualificationDate, SkillSet) VALUES
(5001, '2022-07-15', 'Guidance, Protection'),
(5002, '2021-08-10', 'Assistance, Therapy'),
(5003, '2021-11-05', 'Detection, Assistance'),
(5004, '2022-02-25', 'Protection, Guarding'),
(5005, '2023-01-12', 'Guidance, Detection'),
(5028, '2020-01-01', 'Guidance, Detection'),
(5033, '2022-02-02', 'Assistance, Therapy'),
(5035, '2021-10-10', 'Detection, Assistance'),
(5037, '2023-02-10', 'Assistance, Therapy');

INSERT INTO DisabledPerson(Name, ContactNumber, Age, MedicalCondition, MicrochipNumber) VALUES
('Jane Doe', '111-222-3333', 35, 'Visual Impairment', 5001),
('John Smith', '222-333-4444', 45, 'Physical Disability', 5002),
('Alice Brown', '333-444-5555', 40, 'Hearing Impairment', 5003),
('Tom White', '444-555-6666', 50, 'Mobility Issues', 5004),
('Ella Green', '555-666-7777', 28, 'Anxiety Disorder', 5005);

INSERT INTO Staff (StaffID, Name,  Role) VALUES
(6001, 'Sarah Mitchell', 'Trainer'),
(6002, 'Mike Anderson', 'Coordinator'),
(6003, 'Lily Johnson', 'Supervisor'),
(6004, 'Daniel Roberts', 'Assistant'),
(6005, 'Emma Turner', 'Manager');

INSERT INTO Staff (StaffID, Name, Role) VALUES
(6006, 'Olivia Brown', 'Trainer'),
(6007, 'James Wilson', 'Coordinator'),
(6008, 'Sophia Martinez', 'Supervisor'),
(6009, 'William Garcia', 'Assistant'),
(6010, 'Charlotte Lee', 'Manager');

INSERT INTO UnqualifiedDog_Train_R1 (MicrochipNumber, TrainingStatus) VALUES
(5009, 'In Progress'),
(5011, 'In Progress'),
(5013, 'Completed'),
(5015, 'Completed'),
(5022, 'In Progress'),
(5026, 'In Progress'),
(5080, 'In Progress'),
(5082, 'In Progress'),
(5084, 'Completed'),
(5086, 'In Progress'),
(5090, 'Completed'),
(5092, 'In Progress'),
(5093, NULL),  -- Training NOT started
(5094, NULL),
(5095, 'In Progress'),
(5096, NULL),
(5097, 'Completed'),
(5098, NULL);

INSERT INTO UnqualifiedDog_Train_R2 (MicrochipNumber, StartDate) VALUES
(5009, '2023-01-01'),
(5011, '2023-01-15'),
(5013, '2023-01-20'),
(5015, '2023-02-01'),
(5022, '2023-02-15'),
(5026, '2023-03-01'),
(5080, '2023-03-15'),
(5082, '2023-04-01'),
(5084, '2023-04-15'),
(5086, '2023-05-01'),
(5090, '2023-05-15'),
(5092, '2023-06-01'),
(5095, '2023-06-15'),
(5097, '2023-07-01'),
-- Dogs with NULL training status in R1, indicating training not started
(5093, NULL),
(5094, NULL),
(5096, NULL),
(5098, NULL);

INSERT INTO UnqualifiedDog_Train_R3 (MicrochipNumber, StaffID) VALUES
(5009, 6001),
(5011, 6002),
(5013, 6003),
(5015, 6004),
(5022, 6005),
(5026, 6005),
(5080, 6005),
(5082, 6004),
(5084, 6003),
(5086, 6002),
(5090, 6006),
(5092, 6007),
(5093, 6007),
(5094, 6008),
(5095, 6009),
(5096, 6010),
(5097, 6009),
(5098, 6010);

INSERT INTO UnqualifiedDog_Train_R4 (StartDate, DaysRemaining) VALUES
('2023-01-01', 30),
('2023-01-15', 45),
('2023-01-20', 10),
('2023-02-01', 20),
('2023-02-15', 60),
('2023-03-01', 30),
('2023-03-15', 40),
('2023-04-01', 35),
('2023-04-15', 0),  -- Training completed
('2023-05-01', 25),
('2023-05-15', 0),  -- Training completed
('2023-06-01', 50),
('2023-06-15', 55),
('2023-07-01', 0);  -- Training completed

INSERT INTO TherapyAnimal (MicrochipNumber, TherapyCertification) VALUES
(5017, 1), 
(5018, 1),
(5020, 0),
(5024, 0),
(5025, 1),
(5006, 0),
(5007, 0),
(5008, 1),
(5010, 1),
(5012, 1),
(5014, 0),
(5016, 1),
(5027, 1),
(5029, 1),
(5030, 1),
(5031, 1),
(5032, 1),
(5034, 0),
(5036, 0),
(5039, 1),
(5040, 1),
(5041, 0),
(5042, 1),
(5043, 1),
(5044, 1),
(5046, 1),
(5047, 1),
(5048, 0),
(5050, 1),
(5051, 1),
(5053, 0),
(5054, 0),
(5056, 1),
(5057, 1),
(5059, 0),
(5060, 1),
(5061, 1),
(5062, 0),
(5063, 1),
(5064, 1),
(5065, 0),
(5066, 0),
(5067, 1),
(5068, 0),
(5069, 0),
(5070, 1),
(5071, 1),
(5072, 1),
(5073, 1),
(5075, 1),
(5077, 1),
(5078, 1),
(5079, 1);

INSERT INTO TherapySession_Assigned_R1 (SessionType, SessionLength, MaxCapacity) VALUES
('Dog Therapy', '1 hour', 15),
('Horse Therapy', '1.5 hours', 10),
('Bunny Therapy', '2 hours', 30),
('Cat Therapy', '1 hour', 20),
('Capybara Therapy', '2.5 hours', 10);

INSERT INTO TherapySession_Assigned_R2 (SessionDate, SessionType, MaxCapacity, MicrochipNumber) VALUES
('2023-09-15', 'Dog Therapy', 15, 5047),
('2023-08-10', 'Horse Therapy', 10, 5017),
('2023-10-05', 'Bunny Therapy', 30, 5010),
('2023-11-12', 'Cat Therapy', 20, 5025),
('2023-12-14', 'Capybara Therapy', 10, 5031),
('2023-12-22', 'Horse Therapy', 10, 5057),
('2023-12-22', 'Horse Therapy', 10, 5017);



INSERT INTO ConductBy (StaffID, SessionDate, MicrochipNumber) VALUES
(6001, '2023-09-15', 5047),
(6001, '2023-08-10', 5017),
(6001, '2023-10-05', 5010),
(6001, '2023-11-12', 5025),
(6001, '2023-12-14', 5031),
(6002, '2023-12-22', 5057),
(6002, '2023-12-22', 5017);

INSERT INTO TherapyPatient (Name, ContactNumber, Age, TherapyReason) VALUES
('Alex Morgan', '111-222-1234', 28, 'Stress Relief'),
('Jesse Lingard', '111-222-5678', 29, 'Emotional Support'),
('Christen Press', '111-222-2345', 32, 'Mental Support'),
('Bruno Fernandes', '111-222-6789', 27, 'Anxiety Relief'),
('Megan Rapinoe', '111-222-3456', 36, 'Emotional Support'),
('John Doe', '123-456-7890', 15, 'Mental Support'), 
('Jane Smith', '234-567-8901', 57, 'Mental Support'), 
('Alice Johnson', '345-678-9012', 33, 'Emotional Support'), 
('Bob Williams', '456-789-0123', 18, 'Emotional Support'), 
('Charlie Brown', '567-890-1234', 22, 'Stress Relief');

INSERT INTO OfferTo (Name, ContactNumber, SessionDate, MicrochipNumber) VALUES 
('John Doe', '123-456-7890', '2023-09-15', 5047), 
('Jane Smith', '234-567-8901', '2023-08-10', 5017), 
('Alice Johnson', '345-678-9012', '2023-10-05', 5010), 
('Bob Williams', '456-789-0123', '2023-11-12', 5025), 
('Charlie Brown', '567-890-1234', '2023-12-14', 5031);


INSERT INTO Adopter (Address, ContactNumber, AdopterName, MicrochipNumber) VALUES
('123 Oak St, Denver, CO', '666-777-8888', 'Rachel Adams', 5038),
('456 Maple Dr, Austin, TX', '777-888-9999', 'Ryan Carter', 5045),
('789 Elm Ln, Boston, MA', '888-999-0000', 'Sophia Nelson', 5049),
('101 Pine Ave, Phoenix, AZ', '999-000-1111', 'Oliver King', 5074),
('202 Cedar Pl, Miami, FL', '000-111-2222', 'Isabella Queen', 5076),
('303 Oak St, Denver, CO', '123-111-2222', 'Günel Manoj',NULL),
('404 Maple Dr, Austin, TX', '000-123-2222', 'Sara Priscille', NULL),
('505 Elm Ln, Boston, MA', '000-254-2222', 'Pencho Garbi', NULL),
('606 Pine Ave, Phoenix, AZ', '000-111-3254', 'Gyneth Hagit', NULL),
('707 Cedar Pl, Miami, FL', '345-000-1111', 'Roland Derbáil', NULL),
('808 Oak St, Denver, CO', '999-768-1111', 'Sveinn Rosemary', NULL);


INSERT INTO RetiredAnimal_Adopt (MicrochipNumber, RetiredDate, ReasonForRetirement, ContactNumber, AdopterName) VALUES
(5038, '2023-01-10', 'Aged', '666-777-8888', 'Rachel Adams'),
(5045, '2022-12-15', 'Medical Condition', '777-888-9999', 'Ryan Carter'),
(5049, '2023-02-05', 'Aged', '888-999-0000', 'Sophia Nelson'),
(5074, '2023-02-20', 'Aged', '999-000-1111', 'Oliver King'),
(5076, '2022-11-10', 'Behavioral Issues', '000-111-2222', 'Isabella Queen'),
(5019, '2023-02-20', 'Aged', NULL, NULL),
(5021, '2019-08-08', 'Aged', NULL, NULL),
(5023, '2018-12-15', 'Aged', NULL, NULL),
(5052, '2021-11-01', 'Aged', NULL, NULL),
(5055, '2022-06-01', 'Aged', NULL, NULL),
(5058, '2022-03-01', 'Aged', NULL, NULL);

INSERT INTO AdoptionRecord (RecordID, AdoptionDate, MicrochipNumber, ContactNumber, AdopterName) VALUES
(7001, '2023-01-15', 5038, '666-777-8888', 'Rachel Adams'),
(7002, '2022-12-20', 5045, '777-888-9999', 'Ryan Carter'),
(7003, '2023-02-10', 5049, '888-999-0000', 'Sophia Nelson'),
(7004, '2023-02-25', 5074, '999-000-1111', 'Oliver King'),
(7005, '2022-11-15', 5076, '000-111-2222', 'Isabella Queen');

INSERT INTO Have (RecordID, MicrochipNumber) VALUES
(7001, 5038),
(7002, 5045),
(7003, 5049),
(7004, 5074),
(7005, 5076);



