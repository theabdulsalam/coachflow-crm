-- CoachFlow CRM Database Schema
-- Run this file in phpMyAdmin or MySQL CLI before launching the app

CREATE DATABASE IF NOT EXISTS coachflow_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE coachflow_crm;

-- =============================================
-- TABLE: users
-- =============================================
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Demo admin user — password: password
INSERT INTO users (name, email, password) VALUES
('Abdul Salam', 'admin@coachflow.com', '$2y$10$al7oKwmI5D4II/jtSJMZZuF7GXlPbGhyKdCT2QZNdwXkCtG3VrEh.');

-- =============================================
-- TABLE: leads
-- =============================================
CREATE TABLE IF NOT EXISTS leads (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    full_name           VARCHAR(150)  NOT NULL,
    email               VARCHAR(150)  DEFAULT NULL,
    phone               VARCHAR(30)   DEFAULT NULL,
    whatsapp            VARCHAR(30)   DEFAULT NULL,
    country             VARCHAR(80)   DEFAULT NULL,
    service_interest    VARCHAR(120)  DEFAULT NULL,
    lead_source         VARCHAR(80)   DEFAULT NULL,
    status              ENUM(
        'New',
        'Contacted',
        'Follow-up Scheduled',
        'Booked',
        'Won',
        'Lost'
    ) NOT NULL DEFAULT 'New',
    next_followup_date  DATE          DEFAULT NULL,
    notes               TEXT          DEFAULT NULL,
    created_at          DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- DEMO DATA — 15 Sample Leads
-- =============================================
INSERT INTO leads (full_name, email, phone, whatsapp, country, service_interest, lead_source, status, next_followup_date, notes) VALUES
('Sarah Johnson',    'sarah.j@email.com',    '+1-555-0101', '+1-555-0101', 'United States', 'Business Coaching',     'Instagram',  'New',                  DATE_ADD(CURDATE(), INTERVAL 1 DAY),  'Interested in 3-month program. High intent.'),
('Mohammed Al-Farsi','m.alfarsi@email.com',  '+971-50-1234','71-50-1234',  'UAE',            'Life Coaching',         'LinkedIn',   'Contacted',            DATE_ADD(CURDATE(), INTERVAL 2 DAY),  'Had initial call. Needs to discuss budget.'),
('Priya Sharma',     'priya.s@email.com',    '+91-9810011', '+91-9810011', 'India',          'Mindset Coaching',      'YouTube',    'Follow-up Scheduled',  CURDATE(),                            'Watched 3 videos. Very engaged on IG.'),
('James O''Brien',   'james.ob@email.com',   '+44-7911-123','+44-7911-123','UK',             'Executive Coaching',    'Referral',   'Booked',               DATE_ADD(CURDATE(), INTERVAL 5 DAY),  'Discovery call booked for next week.'),
('Liu Wei',          'liu.wei@email.com',    '+86-138-0013','+86-138-0013','China',          'Business Coaching',     'Facebook',   'Won',                  NULL,                                 'Signed 6-month contract. Start date confirmed.'),
('Amina Hassan',     'amina.h@email.com',    '+254-722-001','+254-722-001','Kenya',          'Career Coaching',       'Instagram',  'New',                  DATE_ADD(CURDATE(), INTERVAL 3 DAY),  'DM lead from story. Wants info pack.'),
('Carlos Mendez',    'c.mendez@email.com',   '+52-55-1234', '+52-55-1234', 'Mexico',         'Business Strategy',     'Website',    'Contacted',            DATE_SUB(CURDATE(), INTERVAL 1 DAY),  'Email sent. No reply yet.'),
('Emily White',      'emily.w@email.com',    '+1-212-5500', '+1-212-5500', 'USA',            'Health Coaching',       'LinkedIn',   'Follow-up Scheduled',  CURDATE(),                            'Follow-up call scheduled for today.'),
('Ahmed Youssef',    'ahmed.y@email.com',    '+20-100-001', '+20-100-001', 'Egypt',          'Mindset Coaching',      'YouTube',    'Lost',                 NULL,                                 'Went with competitor. Budget constraints.'),
('Natalie Brown',    'natalie.b@email.com',  '+61-400-555', '+61-400-555', 'Australia',      'Business Coaching',     'Referral',   'Won',                  NULL,                                 'Paid in full. Excellent client.'),
('David Kim',        'david.k@email.com',    '+82-10-1234', '+82-10-1234', 'South Korea',    'Executive Coaching',    'Facebook',   'New',                  DATE_ADD(CURDATE(), INTERVAL 4 DAY),  'Came from FB ad. Requested callback.'),
('Fatima Al-Zahra',  'fatima.az@email.com',  '+212-6-001',  '+212-6-001',  'Morocco',        'Life Coaching',         'Instagram',  'Contacted',            DATE_ADD(CURDATE(), INTERVAL 1 DAY),  'Voice note sent on WhatsApp. Awaiting reply.'),
('Thomas Müller',    'thomas.m@email.com',   '+49-170-001', '+49-170-001', 'Germany',        'Business Strategy',     'LinkedIn',   'Booked',               DATE_ADD(CURDATE(), INTERVAL 7 DAY),  'Strategy session booked. Send prep questionnaire.'),
('Aisha Kamara',     'aisha.k@email.com',    '+232-76-001', '+232-76-001', 'Sierra Leone',   'Career Coaching',       'Referral',   'New',                  DATE_ADD(CURDATE(), INTERVAL 2 DAY),  'Referred by Emily White. Very motivated.'),
('Ryan Patel',       'ryan.p@email.com',     '+1-415-9900', '+1-415-9900', 'USA',            'Health Coaching',       'Website',    'Follow-up Scheduled',  DATE_SUB(CURDATE(), INTERVAL 2 DAY),  'Filled out contact form. Overdue follow-up!');
