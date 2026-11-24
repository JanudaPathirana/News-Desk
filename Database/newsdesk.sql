
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS subscriptions;
DROP TABLE IF EXISTS bookmarks;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS announcements;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;


-- Users Table
CREATE TABLE users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  student_id    VARCHAR(12)  NOT NULL UNIQUE,
  email         VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('admin','student') NOT NULL DEFAULT 'student',
  profile_photo VARCHAR(255) NULL DEFAULT NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Categories table
CREATE TABLE categories (
  id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Articles table
CREATE TABLE articles (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(200) NOT NULL,
  body        TEXT NOT NULL,
  image       VARCHAR(255) NULL,
  category_id INT UNSIGNED NOT NULL,
  author_id   INT UNSIGNED NOT NULL,
  status      ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_breaking TINYINT(1) NOT NULL DEFAULT 0,
  views       INT UNSIGNED NOT NULL DEFAULT 0,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_status_date (status, created_at),
  CONSTRAINT fk_art_cat  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
  CONSTRAINT fk_art_user FOREIGN KEY (author_id)   REFERENCES users(id)      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Bookmarks table
CREATE TABLE bookmarks (
  user_id    INT UNSIGNED NOT NULL,
  article_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, article_id),
  CONSTRAINT fk_bm_user FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
  CONSTRAINT fk_bm_art  FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Comments table
CREATE TABLE comments (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  article_id INT UNSIGNED NOT NULL,
  user_id    INT UNSIGNED NOT NULL,
  body       TEXT NOT NULL,
  status     ENUM('visible','hidden') NOT NULL DEFAULT 'visible',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_com_art  FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
  CONSTRAINT fk_com_user FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Announcements table
CREATE TABLE announcements (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title      VARCHAR(200) NOT NULL,
  body       TEXT NOT NULL,
  is_active  TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Subscriptions table
CREATE TABLE subscriptions (
  user_id     INT UNSIGNED NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (user_id, category_id),
  CONSTRAINT fk_sub_user FOREIGN KEY (user_id)     REFERENCES users(id)      ON DELETE CASCADE,
  CONSTRAINT fk_sub_cat  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Sample Data Insertion
INSERT INTO users (id, name, student_id, email, password_hash, role) VALUES
(1, 'NewsDesk Admin',   'ADMIN001', 'admin@nsbm.ac.lk',
  '$2y$10$WAJ0flu05QpVAiGIyg5ALugSxAqFei5czoIyVzEj9qnKGYDfJzJLG', 'admin'),
(2, 'Kasun Perera',     'ST10001',  'kasun@students.nsbm.ac.lk',
  '$2y$10$XKcGj1yFKq1J9Q5jKf3TOeXjKVa7fJFOQ8V6R4RJz3L5J6xmyDjKa', 'student'),
(3, 'Nimali Silva',     'ST10002',  'nimali@students.nsbm.ac.lk',
  '$2y$10$XKcGj1yFKq1J9Q5jKf3TOeXjKVa7fJFOQ8V6R4RJz3L5J6xmyDjKa', 'student'),
(4, 'Tharindu Bandara', 'ST10003',  'tharindu@students.nsbm.ac.lk',
  '$2y$10$XKcGj1yFKq1J9Q5jKf3TOeXjKVa7fJFOQ8V6R4RJz3L5J6xmyDjKa', 'student'),
(5, 'Ishara Fernando',  'ST10004',  'ishara@students.nsbm.ac.lk',
  '$2y$10$XKcGj1yFKq1J9Q5jKf3TOeXjKVa7fJFOQ8V6R4RJz3L5J6xmyDjKa', 'student');

-- Categories
INSERT INTO categories (id, name) VALUES
(1, 'Academic'), (2, 'Events'), (3, 'Sports'), (4, 'General'), (5, 'Clubs');


-- Sample Articles (approved)
INSERT INTO articles (id, title, body, category_id, author_id, status, is_featured, is_breaking, views, created_at) VALUES

(1, 'NSBM Final Examination Timetable Released for Semester 2',
'The Faculty of Computing has officially released the final examination timetable for Semester 2 of the 2025/2026 academic year. All students enrolled in undergraduate programmes under the Faculty of Computing are advised to review the timetable carefully and plan their study schedules accordingly.\n\nKey examination dates:\n- Software Engineering: December 2nd, 2025\n- Database Management Systems: December 5th, 2025\n- Web Application Development: December 8th, 2025\n- Data Structures and Algorithms: December 10th, 2025\n\nStudents should report to the examination hall at least 30 minutes before the scheduled start time. All examinations will be held at the NSBM main campus examination centre. Identity cards are mandatory for entry.\n\nThe Student Affairs office will remain open until 6:00 PM during the examination period to handle any queries or special requests. Students with medical or other valid concerns should submit their requests through the online portal before November 30th.\n\nWe wish all students the very best in their upcoming examinations!',
1, 1, 'approved', 1, 0, 245, '2025-11-15 09:00:00'),

(2, 'GreenFest 2025 — Annual Cultural Festival Coming This December',
'NSBM Green University is thrilled to announce GreenFest 2025, the university''s flagship annual cultural festival, scheduled to take place on December 15th and 16th at the main campus grounds.\n\nGreenFest has been a beloved tradition at NSBM for over a decade, bringing together students, staff, and alumni for two days of entertainment, creativity, and community. This year''s theme is "Innovate. Create. Celebrate." and promises to be the most exciting edition yet.\n\nHighlights include:\n- Live music performances by popular Sri Lankan artists\n- Inter-faculty talent show and dance battle\n- Tech exhibition showcasing student innovation projects\n- Food stalls featuring cuisines from across Sri Lanka\n- Gaming tournament with exciting prizes\n- Photography contest: "Campus Through My Lens"\n\nRegistrations for performance slots and stall bookings are now open. Students can register through the GreenFest portal on the NSBM student dashboard. Faculty representatives will be available at the Student Union office to assist with event coordination.\n\nAll NSBM students, faculty members, and their families are warmly invited. Entry is free with a valid university ID.',
2, 1, 'approved', 0, 1, 532, '2025-11-18 14:30:00'),

(3, 'NSBM Cricket Team Wins Inter-University Championship',
'The NSBM Green University cricket team has won the 2025 Inter-University Cricket Championship, defeating the University of Colombo by 42 runs in a thrilling final held at the SSC grounds in Colombo.\n\nBatting first, NSBM posted an impressive total of 267/8 in their allotted 50 overs. Captain Ravindu Senaratne led from the front with a magnificent 89 off 102 balls, supported by crucial contributions from Dinesh Kumara (56) and Sachith Pathirana (38). The bowling attack then restricted the University of Colombo to 225 all out, with Hasitha Perera taking 3 wickets for just 28 runs.\n\nThis marks NSBM''s third inter-university cricket championship title in the past five years, cementing the university''s reputation as a sporting powerhouse. The team will represent Sri Lanka in the South Asian University Games to be held in Dhaka next March.\n\n"I''m incredibly proud of this team," said Coach Mahela Udawatte. "Every player contributed to this victory. The dedication and hard work shown during training has paid off." Vice-Chancellor Professor Nilanthi de Silva congratulated the team and announced that NSBM would host a celebration dinner in their honour next week.',
3, 1, 'approved', 1, 0, 389, '2025-11-20 11:00:00'),

(4, 'New Library Extension — 24/7 Study Zone Now Open',
'The NSBM library has officially opened its new 24/7 study zone extension, providing students with round-the-clock access to a modern, climate-controlled study environment. The new wing, located on the second floor of the library building, features 120 individual study cubicles, 8 group discussion rooms, and high-speed Wi-Fi throughout.\n\nFacilities in the new zone include:\n- Individual study carrels with built-in power outlets and USB ports\n- Soundproof group discussion rooms (bookable via the library app)\n- A self-service coffee and refreshments station\n- A silent reading section with comfortable seating\n- Access to digital resources and online journals\n\nThe extension was developed as part of the university''s ongoing commitment to enhancing student facilities. Access is available to all registered students with a valid library card. Security personnel will be stationed at the entrance throughout the night for student safety.\n\n"We understand that students have different study schedules," said Chief Librarian Mrs. Kumari Jayasinghe. "This new facility ensures that every student has access to a productive study environment, regardless of the hour."',
4, 1, 'approved', 0, 0, 178, '2025-11-21 08:15:00'),

(5, 'Faculty of Computing — New AI and Machine Learning Specialisation Announced',
'The Faculty of Computing at NSBM Green University has announced a new specialisation track in Artificial Intelligence and Machine Learning, available starting from the next academic intake in March 2026.\n\nThe new specialisation will be offered as part of the BSc (Hons) in Computer Science programme, delivered in partnership with the University of Plymouth, UK. Students who choose this track will cover advanced topics including:\n\n- Deep Learning and Neural Networks\n- Natural Language Processing\n- Computer Vision\n- Reinforcement Learning\n- MLOps and Deployment at Scale\n- Ethics in AI\n\nThe specialisation includes a mandatory industry internship in the final year, with partner companies including WSO2, Virtusa, IFS, and Axiata Digital Labs already expressing interest in hosting NSBM students.\n\nDr. Samantha Rajapakse, Head of the Department of Computer Science, stated: "AI is reshaping every industry. This specialisation will equip our students with the skills and practical experience needed to lead in this rapidly evolving field."\n\nInterested students can attend the information session on December 1st at Lecture Hall A3, or visit the Faculty of Computing office for more details.',
1, 2, 'approved', 0, 0, 312, '2025-11-22 10:00:00'),

(6, 'Campus Hostel Facilities Upgrade Complete',
'NSBM Green University has completed a comprehensive upgrade of its on-campus hostel facilities, with renovated rooms now available for the January 2026 intake. The upgrade includes modernised bathroom facilities, air conditioning in all rooms, new furniture, and improved common areas.\n\nThe hostel can now accommodate up to 350 students across single and twin-sharing rooms. New amenities include a fully equipped gymnasium, a rooftop recreational area, a laundry facility with self-service machines, and a mini supermarket on the ground floor.\n\nMonthly rates remain unchanged, with single rooms at LKR 35,000 and twin-sharing rooms at LKR 22,000 per person (inclusive of utilities). Priority will be given to students from outside the Western Province and international students.\n\nApplications for hostel accommodation are now open through the Student Portal. The deadline for the January intake is December 20th, 2025.\n\n"We want our students to feel at home while they pursue their education," said Student Affairs Director Mr. Pradeep Wickramasinghe. "These upgrades reflect our commitment to providing a comfortable and supportive living environment."',
4, 3, 'approved', 0, 0, 156, '2025-11-23 16:45:00');


-- Pending article (for testing approval workflow)
INSERT INTO articles (id, title, body, category_id, author_id, status, views, created_at) VALUES

(7, 'Computing Society — Workshop on Cloud Computing with AWS',
'The NSBM Computing Society is organising a hands-on workshop on Cloud Computing using Amazon Web Services (AWS). The workshop is designed for students who want to gain practical experience with cloud platforms and is open to all faculties.\n\nTopics covered will include setting up EC2 instances, working with S3 storage, deploying web applications using Elastic Beanstalk, and an introduction to serverless computing with AWS Lambda. Each participant will receive a free AWS Educate account with credits to practice after the workshop.\n\nDate: December 12th, 2025\nTime: 9:00 AM - 4:00 PM\nVenue: Computer Lab 3, Faculty of Computing\nRegistration Fee: Free for Computing Society members, LKR 500 for others\n\nSeats are limited to 40 participants. Register through the Computing Society''s Instagram page or visit the society''s notice board outside Lab 2.',
2, 4, 'pending', 0, '2025-11-23 20:00:00'),

(8, 'Photography Club Seeking New Members for 2026',
'The NSBM Photography Club is looking for enthusiastic new members for the 2026 academic year. Whether you are a seasoned photographer or just starting out with your smartphone, the club welcomes everyone who has a passion for visual storytelling.\n\nMembership benefits include access to the club''s equipment (including DSLR cameras and lenses), weekly photo walks around campus and nearby locations, monthly competitions with prizes, and guidance from professional photographers who serve as club mentors.\n\nThe membership fee for 2026 is LKR 1,000, which includes a club T-shirt and access to all events. Interested students can fill out the membership form available at the Student Union office or message the club''s Facebook page.\n\nAn introductory meeting will be held on December 5th at 3:00 PM in Room B201. All are welcome!',
4, 5, 'pending', 0, '2025-11-24 09:30:00');


-- Announcements
INSERT INTO announcements (id, title, body, is_active, created_at) VALUES
(1, 'Library Notice: Extended Hours During Exam Period',
'The NSBM library will operate on extended hours (7:00 AM — 11:00 PM) from November 25th through December 15th to support students during the examination period. The 24/7 study zone remains open as usual.',
1, '2025-11-20 09:00:00'),

(2, 'Vehicle Registration Deadline — December 1st',
'All students who bring vehicles to campus must complete vehicle registration by December 1st, 2025. Unregistered vehicles will not be permitted entry after this date. Registration forms are available at the Security Office.',
1, '2025-11-22 10:00:00'),

(3, 'Holiday Notice: University Closed for Unduvap Poya',
'NSBM Green University will be closed on December 15th (Monday) for the Unduvap Poya public holiday. Regular operations will resume on December 16th. Students with examination queries should email studentaffairs@nsbm.ac.lk.',
1, '2025-11-23 14:00:00');


-- Comments
INSERT INTO comments (article_id, user_id, body, status, created_at) VALUES
(1, 2, 'Thanks for sharing the timetable! Time to start revising. Good luck everyone!', 'visible', '2025-11-15 12:30:00'),
(1, 3, 'Does anyone know if the WAD exam will be open-book? The notice doesn''t mention it.', 'visible', '2025-11-16 09:00:00'),
(2, 4, 'GreenFest is always amazing! Can''t wait for the gaming tournament this year 🎮', 'visible', '2025-11-18 16:00:00'),
(2, 5, 'Is the photography contest open to phone cameras too? Would love to enter!', 'visible', '2025-11-19 11:30:00'),
(2, 2, 'Last year''s food stalls were incredible. Hope we get the kottu stall again!', 'visible', '2025-11-19 15:45:00'),
(3, 3, 'Congratulations to the cricket team! 🏏 NSBM pride!', 'visible', '2025-11-20 13:00:00'),
(3, 4, 'Ravindu played brilliantly. Well deserved win!', 'visible', '2025-11-20 14:20:00'),
(4, 2, 'The 24/7 study zone is a game changer during exam season. Really appreciate this facility.', 'visible', '2025-11-21 10:00:00'),
(5, 5, 'An AI specialisation is exactly what we needed! Definitely switching to this track.', 'visible', '2025-11-22 12:00:00'),
(5, 3, 'Dr. Rajapakse''s courses are excellent. This specialisation will be top notch.', 'visible', '2025-11-22 15:30:00');

-- Bookmarks
INSERT INTO bookmarks (user_id, article_id) VALUES
(2, 1), (2, 5), (3, 2), (3, 3), (4, 2), (4, 5), (5, 1), (5, 4);

-- Subscriptions
INSERT INTO subscriptions (user_id, category_id) VALUES
(2, 1), (2, 3), (3, 2), (3, 4), (4, 1), (4, 2), (5, 3), (5, 4);
