-- Guruvision Matrimony Seed Data
-- Password for all test users is 'Password123'
-- Hash generated via password_hash('Password123', PASSWORD_BCRYPT)

INSERT INTO users (
    id, profile_id, email, password_hash, is_premium, created_by, full_name,
    gender, dob, marital_status, children, mother_tongue, about_me,
    religion, caste, physical_status, height, weight, body_type, complexion, blood_group, diet,
    edu_category, edu_detail, employed_in, occupation_category, occu_detail, working_firm, work_place, monthly_income,
    native_place, native_district, citizenship, living_place, living_country,
    contact_name, contact_relation, contact_phone, alternate_phone, com_address
) VALUES
(
    1, 'GV1001', 'rahul.s@example.com', '$2y$10$FuiPzXtBbNAmVyWfpD8NS.Kc6zirbIQGkh1A5XIQdPkdCTGzG1agC', 1, 'Self', 'Rahul Santhosh',
    'Male', '1994-06-15', 'Unmarried', 'No', 'Malayalam', 'Senior Software Engineer working at an MNC in Kochi. Family-oriented, calm and enjoys traveling.',
    'Hindu', 'Ezhava', 'Normal', 178, 72, 'Athletic', 'Fair', 'O+ve', 'Non Vegetarian',
    'Bachelors', 'B.Tech in Computer Science', 'MNC', 'Engineer', 'Senior Full Stack Lead', 'Infosys Ltd', 'Ernakulam', 'Rs. 1,20,000',
    'Cherthala', 'Alappuzha', 'India', 'Ernakulam', 'India',
    'Santhosh Kumar', 'Father', '+919447123456', '+91484223344', 'Santha Bhavan, Cherthala, Alappuzha, Kerala'
),
(
    2, 'GV1002', 'ananya.m@example.com', '$2y$10$FuiPzXtBbNAmVyWfpD8NS.Kc6zirbIQGkh1A5XIQdPkdCTGzG1agC', 1, 'Parents', 'Ananya Mohan',
    'Female', '1997-03-22', 'Unmarried', 'No', 'Malayalam', 'Dentist practicing at a reputable hospital in Kozhikode. Enjoys classical music and reading.',
    'Hindu', 'Thiyya', 'Normal', 162, 54, 'Slim', 'Fair', 'B+ve', 'Vegetarian',
    'Masters', 'MDS Orthodontics', 'Private', 'Doctor', 'Consultant Orthodontist', 'Malabar Medical Centre', 'Kozhikode', 'Rs. 95,000',
    'Thalassery', 'Kannur', 'India', 'Kozhikode', 'India',
    'Mohanan K.', 'Father', '+919847112233', '+914972334455', 'Anugraha, Near Temple Gate, Thalassery, Kannur'
),
(
    3, 'GV1003', 'arun.k@example.com', '$2y$10$FuiPzXtBbNAmVyWfpD8NS.Kc6zirbIQGkh1A5XIQdPkdCTGzG1agC', 1, 'Self', 'Arun Kumar V.',
    'Male', '1991-11-10', 'Unmarried', 'No', 'Malayalam', 'Civil Services aspirant and high school educator. Simple, honest, grounded values.',
    'Hindu', 'Ezhava', 'Normal', 172, 68, 'Average', 'Medium', 'A+ve', 'Non Vegetarian',
    'Masters', 'M.Sc Physics, B.Ed', 'Government', 'Teacher', 'HSST Physics', 'Govt Model HSS', 'Thrissur', 'Rs. 75,000',
    'Irinjalakuda', 'Thrissur', 'India', 'Thrissur', 'India',
    'Venu Gopal', 'Father', '+919745889900', NULL, 'Kizhakkedath House, Irinjalakuda, Thrissur'
),
(
    4, 'GV1004', 'dr.meera@example.com', '$2y$10$FuiPzXtBbNAmVyWfpD8NS.Kc6zirbIQGkh1A5XIQdPkdCTGzG1agC', 1, 'Self', 'Dr. Meera Pradeep',
    'Female', '1993-08-14', 'Unmarried', 'No', 'Malayalam', 'MBBS, MD General Medicine. Looking for an understanding partner who respects professional commitment.',
    'Hindu', 'Ezhava', 'Normal', 165, 58, 'Slim', 'Very Fair', 'O+ve', 'Non Vegetarian',
    'Masters', 'MD General Medicine', 'Government', 'Doctor', 'Assistant Professor', 'Govt Medical College', 'Thiruvananthapuram', 'Rs. 1,10,000',
    'Attingal', 'Thiruvananthapuram', 'India', 'Thiruvananthapuram', 'India',
    'Pradeep V.', 'Father', '+919446223344', '+914712334411', 'Pradeep Nivas, Attingal, Thiruvananthapuram'
),
(
    5, 'GV1005', 'vineeth.p@example.com', '$2y$10$FuiPzXtBbNAmVyWfpD8NS.Kc6zirbIQGkh1A5XIQdPkdCTGzG1agC', 0, 'Sibling', 'Vineeth Pavithran',
    'Male', '1992-04-05', 'Unmarried', 'No', 'Malayalam', 'Chartered Accountant working with an audit firm in Dubai. Returning to Kerala soon.',
    'Hindu', 'Thiyya', 'Normal', 180, 78, 'Athletic', 'Fair', 'AB+ve', 'Non Vegetarian',
    'Masters', 'FCA, B.Com', 'Abroad', 'Accountant', 'Senior Audit Manager', 'KPMG Middle East', 'Dubai', 'Rs. 2,50,000',
    'Payyanur', 'Kannur', 'India', 'Dubai', 'UAE',
    'Pavithran P.', 'Father', '+919846332211', NULL, 'Pavithram, Payyanur, Kannur'
),
(
    6, 'GV1006', 'sneha.r@example.com', '$2y$10$FuiPzXtBbNAmVyWfpD8NS.Kc6zirbIQGkh1A5XIQdPkdCTGzG1agC', 0, 'Self', 'Sneha Radhakrishnan',
    'Female', '1996-01-28', 'Unmarried', 'No', 'Malayalam', 'Architect with passion for sustainable vernacular architecture and landscape design.',
    'Hindu', 'Ezhava', 'Normal', 160, 52, 'Slim', 'Fair', 'A-ve', 'Vegetarian',
    'Bachelors', 'B.Arch', 'Own Firm', 'Architect', 'Principal Architect', 'Studio Vruksha', 'Kollam', 'Rs. 80,000',
    'Karunagappally', 'Kollam', 'India', 'Kollam', 'India',
    'Radhakrishnan T.', 'Father', '+919447556677', NULL, 'Radha Nilayam, Karunagappally, Kollam'
),
(
    7, 'GV1007', 'midhun.m@example.com', '$2y$10$FuiPzXtBbNAmVyWfpD8NS.Kc6zirbIQGkh1A5XIQdPkdCTGzG1agC', 0, 'Relative', 'Midhun M.',
    'Male', '1989-12-02', 'Divorced', 'No', 'Malayalam', 'Branch Manager at Federal Bank. Short marriage ended amicably. Seeking a supportive life partner.',
    'Hindu', 'Ezhava', 'Normal', 175, 75, 'Average', 'Medium', 'O-ve', 'Non Vegetarian',
    'Masters', 'MBA Finance', 'Private', 'Bank Officer', 'Branch Manager', 'Federal Bank Ltd', 'Palakkad', 'Rs. 85,000',
    'Ottapalam', 'Palakkad', 'India', 'Palakkad', 'India',
    'Manoj Kumar', 'Brother', '+919846998877', NULL, 'Meenakshi Mandiram, Ottapalam, Palakkad'
),
(
    8, 'GV1008', 'kavya.s@example.com', '$2y$10$FuiPzXtBbNAmVyWfpD8NS.Kc6zirbIQGkh1A5XIQdPkdCTGzG1agC', 1, 'Self', 'Kavya Suresh',
    'Female', '1995-09-18', 'Unmarried', 'No', 'Malayalam', 'Software Developer at an IT park in Kakkanad. Enjoys badminton, baking, and Kerala cuisine.',
    'Hindu', 'Ezhava', 'Normal', 163, 56, 'Average', 'Fair', 'B+ve', 'Non Vegetarian',
    'Bachelors', 'B.Tech IT', 'Private', 'Engineer', 'Software Engineer', 'Experion Technologies', 'Ernakulam', 'Rs. 65,000',
    'Vaikom', 'Kottayam', 'India', 'Ernakulam', 'India',
    'Suresh K.', 'Father', '+919447334455', NULL, 'Kavyam, Vaikom, Kottayam'
);

-- Partner Preferences
INSERT INTO partner_preferences (
    user_id, pref_age_from, pref_age_to, pref_height_from, pref_height_to,
    pref_marital_status, pref_complexion, pref_body_type, pref_physical_status,
    pref_districts, pref_living_place, pref_living_country, pref_edu_category, pref_employed_in, pref_occupation
) VALUES
(1, 22, 29, 155, 172, 'Unmarried', 'Fair', 'Slim', 'Normal', '["Alappuzha","Ernakulam","Kottayam"]', 'Kerala', 'India', 'Bachelors', 'Private', 'Engineer'),
(2, 27, 34, 170, 188, 'Unmarried', 'Fair', 'Athletic', 'Normal', '["Kannur","Kozhikode","Ernakulam"]', 'India', 'India', 'Masters', 'Professionals', 'Doctor'),
(3, 24, 30, 155, 170, 'Unmarried', 'Any', 'Any', 'Normal', '["Thrissur","Palakkad","Ernakulam"]', 'Kerala', 'India', 'Masters', 'Government', 'Teacher'),
(4, 28, 35, 172, 190, 'Unmarried', 'Fair', 'Average', 'Normal', '["Thiruvananthapuram","Kollam","Ernakulam"]', 'Kerala', 'India', 'Masters', 'Professionals', 'Doctor');

-- Success Stories
INSERT INTO success_stories (groom_name, bride_name, marriage_date, story, image_path) VALUES
('Sujith Soman', 'Arya Vijayan', '2024-01-18', 'We found each other through Guruvision Matrimony. Both our families from Alappuzha hit it off instantly. Forever grateful for this wonderful platform!', 'assets/images/story1.jpg'),
('Deepak Thampi', 'Nayana Rajan', '2023-11-26', 'Our profiles matched on Guruvision with 95% compatibility. From the first phone call, we knew it was meant to be. Thank you Guruvision!', 'assets/images/story2.jpg'),
('Dr. Gokuldas', 'Dr. Reshma R.', '2024-04-12', 'Being both medical professionals in Kannur, Guruvision made finding the right life partner seamless and authentic.', 'assets/images/story3.jpg');
