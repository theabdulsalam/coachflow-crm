-- ============================================================
-- CoachFlow CRM — Extended Demo Data
-- Leads from Scandinavia, USA, EU, Japan, Korea, Singapore
-- Date range: January – April 2026
-- Run this in phpMyAdmin AFTER importing database.sql
-- ============================================================

USE coachflow_crm;

INSERT INTO leads
  (full_name, email, phone, whatsapp, country, service_interest, lead_source, status, next_followup_date, notes, created_at, updated_at)
VALUES

-- ============================================================
-- JANUARY 2026
-- ============================================================

-- Scandinavia
('Erik Johansson',      'erik.j@consulting.se',    '+46-70-123-4567', '+46-70-123-4567', 'Sweden',      'Executive Coaching',   'LinkedIn',    'Won',                  NULL,           'Signed 6-month retainer. Stockholm-based tech CEO.', '2026-01-05 09:14:00', '2026-02-10 11:30:00'),
('Astrid Lindqvist',    'astrid.l@email.no',        '+47-912-34567',   '+47-912-34567',   'Norway',      'Business Coaching',    'Instagram',   'Contacted',            '2026-04-22',   'Runs a design studio in Oslo. Interested in scaling from 3 to 10 staff.', '2026-01-07 10:42:00', '2026-01-07 10:42:00'),
('Lars Eriksen',        'lars.e@startup.dk',        '+45-22-334-455',  '+45-22-334-455',  'Denmark',     'Business Strategy',    'Referral',    'Won',                  NULL,           'Copenhagen fintech founder. Referred by Erik Johansson. Closed quickly.', '2026-01-09 14:22:00', '2026-02-05 09:00:00'),
('Ingrid Bergström',    'ingrid.b@email.se',        '+46-73-456-7890', '+46-73-456-7890', 'Sweden',      'Mindset Coaching',     'YouTube',     'Lost',                 NULL,           'Watched webinar series. Decided to go with a local coach due to language preference.', '2026-01-11 16:05:00', '2026-02-01 08:00:00'),
('Ole Hansen',          'ole.h@enterprise.dk',      '+45-31-445-566',  '+45-31-445-566',  'Denmark',     'Executive Coaching',   'LinkedIn',    'Follow-up Scheduled',  '2026-04-20',   'Director at a Copenhagen logistics company. Needs leadership coaching for his team.', '2026-01-14 08:30:00', '2026-01-14 08:30:00'),
('Freya Andersen',      'freya.a@email.no',         '+47-934-56789',   '+47-934-56789',   'Norway',      'Life Coaching',        'Instagram',   'Booked',               '2026-04-25',   'Discovery call booked. Career transition from banking to entrepreneurship.', '2026-01-16 11:15:00', '2026-03-20 14:00:00'),
('Magnus Nilsson',      'magnus.n@agency.se',       '+46-76-234-5678', '+46-76-234-5678', 'Sweden',      'Business Coaching',    'Facebook',    'Contacted',            '2026-04-23',   'Marketing agency owner. Wants to build a team and reduce personal hours.', '2026-01-18 15:40:00', '2026-01-18 15:40:00'),
('Sigrid Thorvaldsen',  'sigrid.t@email.is',        '+354-820-1234',   '+354-820-1234',   'Iceland',     'Career Coaching',      'LinkedIn',    'New',                  '2026-04-28',   'Reykjavik-based product manager exploring executive transition.', '2026-01-20 09:55:00', '2026-01-20 09:55:00'),
('Mikael Korhonen',     'mikael.k@tech.fi',         '+358-40-123-4567','+358-40-123-4567','Finland',     'Business Strategy',    'Referral',    'Won',                  NULL,           'Helsinki SaaS founder. 3-month strategy package purchased upfront.', '2026-01-22 13:10:00', '2026-02-28 10:00:00'),
('Linnea Holm',         'linnea.h@email.se',        '+46-72-567-8901', '+46-72-567-8901', 'Sweden',      'Health Coaching',      'Instagram',   'Contacted',            '2026-04-26',   'Wellness entrepreneur in Gothenburg. Wants accountability coaching.', '2026-01-25 10:20:00', '2026-01-25 10:20:00'),

-- USA
('Michael Thompson',    'm.thompson@corp.com',      '+1-312-555-0181', '+1-312-555-0181', 'USA',         'Executive Coaching',   'LinkedIn',    'Won',                  NULL,           'VP at Chicago consulting firm. Completed onboarding. Strong referral source.', '2026-01-06 14:00:00', '2026-02-12 09:00:00'),
('Jennifer Davis',      'j.davis@startup.io',       '+1-415-555-0192', '+1-415-555-0192', 'USA',         'Business Coaching',    'Website',     'Booked',               '2026-04-24',   'San Francisco startup founder. Series A, 12 employees. Wants founder coaching.', '2026-01-10 11:30:00', '2026-03-18 16:00:00'),
('Robert Wilson',       'r.wilson@firm.com',        '+1-212-555-0143', '+1-212-555-0143', 'USA',         'Business Strategy',    'Referral',    'Follow-up Scheduled',  '2026-04-19',   'New York lawyer transitioning to consulting. Referred by Michael Thompson.', '2026-01-13 09:45:00', '2026-01-13 09:45:00'),
('Ashley Martinez',     'ashley.m@brand.co',        '+1-305-555-0167', '+1-305-555-0167', 'USA',         'Life Coaching',        'Instagram',   'Contacted',            '2026-04-27',   'Miami brand strategist. Wants clarity on career direction after burnout.', '2026-01-17 16:50:00', '2026-01-17 16:50:00'),
('Daniel Brooks',       'd.brooks@media.us',        '+1-323-555-0174', '+1-323-555-0174', 'USA',         'Mindset Coaching',     'YouTube',     'Won',                  NULL,           'LA-based content creator with 200k followers. Paid 6 months upfront.', '2026-01-21 12:00:00', '2026-02-20 08:30:00'),

-- EU
('Sophie Müller',       's.mueller@gmbh.de',        '+49-171-234-5678','+49-171-234-5678','Germany',     'Executive Coaching',   'LinkedIn',    'Won',                  NULL,           'Berlin startup COO. Leadership coaching for scaling phase.', '2026-01-08 10:00:00', '2026-02-15 14:00:00'),
('Pierre Dubois',       'p.dubois@conseil.fr',      '+33-6-12-34-5678','+33-6-12-34-5678','France',      'Business Strategy',    'Referral',    'Follow-up Scheduled',  '2026-04-21',   'Paris-based management consultant. Wants to launch his own firm.', '2026-01-12 14:30:00', '2026-01-12 14:30:00'),
('Hans Van der Berg',   'h.vdberg@nl-biz.nl',       '+31-6-1234-5678', '+31-6-1234-5678', 'Netherlands', 'Business Coaching',    'LinkedIn',    'Booked',               '2026-04-23',   'Amsterdam e-commerce entrepreneur. Discovery call confirmed for next week.', '2026-01-15 11:00:00', '2026-03-22 10:00:00'),
('Isabella Rossi',      'i.rossi@studio.it',        '+39-335-123-4567','+39-335-123-4567','Italy',       'Life Coaching',        'Instagram',   'Contacted',            '2026-04-29',   'Milan fashion brand owner. Work-life balance and delegation issues.', '2026-01-19 15:15:00', '2026-01-19 15:15:00'),
('Carlos García',       'c.garcia@empresa.es',      '+34-612-345-678', '+34-612-345-678', 'Spain',       'Career Coaching',      'YouTube',     'New',                  '2026-04-30',   'Barcelona tech professional. Watched growth mindset video series.', '2026-01-23 09:00:00', '2026-01-23 09:00:00'),

-- Asia
('Kenji Tanaka',        'k.tanaka@corp.jp',         '+81-90-1234-5678','+81-90-1234-5678','Japan',       'Executive Coaching',   'LinkedIn',    'Won',                  NULL,           'Tokyo-based division head at a manufacturing firm. 4-month engagement completed.', '2026-01-06 08:00:00', '2026-03-10 10:00:00'),
('Ji-hoon Park',        'jihoon.p@ventures.kr',     '+82-10-9876-5432','+82-10-9876-5432','South Korea', 'Business Coaching',    'LinkedIn',    'Booked',               '2026-04-22',   'Seoul VC-backed startup founder. Pre-Series A. Discovery call next week.', '2026-01-10 10:30:00', '2026-03-25 09:00:00'),
('Wei Chen',            'wei.c@holdings.sg',        '+65-9123-4567',   '+65-9123-4567',   'Singapore',   'Business Strategy',    'Referral',    'Won',                  NULL,           'Singapore family office manager. Wants clarity on personal brand and leadership.', '2026-01-14 13:00:00', '2026-02-28 11:00:00'),
('Yuki Nakamura',       'yuki.n@design.jp',         '+81-80-2345-6789','+81-80-2345-6789','Japan',       'Career Coaching',      'Instagram',   'Contacted',            '2026-04-25',   'Osaka UX designer considering going independent. High intent, needs reassurance.', '2026-01-18 11:45:00', '2026-01-18 11:45:00'),
('Min-ji Kim',          'minji.k@agency.kr',        '+82-10-5678-1234','+82-10-5678-1234','South Korea', 'Life Coaching',        'YouTube',     'Follow-up Scheduled',  '2026-04-20',   'Busan marketing agency founder. Burnout and growth ceiling issues.', '2026-01-26 14:30:00', '2026-01-26 14:30:00'),

-- ============================================================
-- FEBRUARY 2026
-- ============================================================

-- Scandinavia
('Bjørn Larsen',        'bjorn.l@invest.no',        '+47-998-76543',   '+47-998-76543',   'Norway',      'Business Strategy',    'LinkedIn',    'Won',                  NULL,           'Oslo angel investor pivoting to full-time advisory. Paid in full.', '2026-02-03 09:00:00', '2026-03-05 10:00:00'),
('Hanna Virtanen',      'hanna.v@coach.fi',         '+358-50-876-5432','+358-50-876-5432','Finland',     'Mindset Coaching',     'Referral',    'Contacted',            '2026-04-24',   'Turku therapist expanding into coaching. Referred by Mikael Korhonen.', '2026-02-07 10:15:00', '2026-02-07 10:15:00'),
('Rasmus Christensen',  'rasmus.c@tech.dk',         '+45-42-123-456',  '+45-42-123-456',  'Denmark',     'Executive Coaching',   'Website',     'Follow-up Scheduled',  '2026-04-21',   'Aarhus CTO at a scale-up. First-time exec needing leadership structure.', '2026-02-10 14:00:00', '2026-02-10 14:00:00'),
('Solveig Dahl',        'solveig.d@email.no',       '+47-456-78901',   '+47-456-78901',   'Norway',      'Life Coaching',        'Instagram',   'Lost',                 NULL,           'Bergen solopreneur. Chose a lower-priced group program instead.', '2026-02-12 11:30:00', '2026-03-01 09:00:00'),
('Viktor Lindgren',     'viktor.l@firm.se',         '+46-70-987-6543', '+46-70-987-6543', 'Sweden',      'Business Coaching',    'LinkedIn',    'Booked',               '2026-04-26',   'Malmö consultancy owner. Wants to productise services and exit client work.', '2026-02-17 15:00:00', '2026-03-28 16:00:00'),

-- USA
('Laura Chen',          'laura.c@finance.us',       '+1-646-555-0188', '+1-646-555-0188', 'USA',         'Executive Coaching',   'LinkedIn',    'Won',                  NULL,           'NYC hedge fund manager exploring leadership transition. Quick close.', '2026-02-04 10:00:00', '2026-03-10 14:00:00'),
('Marcus Johnson',      'm.johnson@growth.io',      '+1-512-555-0155', '+1-512-555-0155', 'USA',         'Business Strategy',    'Referral',    'Contacted',            '2026-04-22',   'Austin B2B SaaS founder. 8-figure revenue, wants to step back from operations.', '2026-02-09 13:00:00', '2026-02-09 13:00:00'),
('Natalie Foster',      'n.foster@agency.co',       '+1-617-555-0122', '+1-617-555-0122', 'USA',         'Career Coaching',      'Instagram',   'Follow-up Scheduled',  '2026-04-19',   'Boston creative director. Wants to launch own agency within 12 months.', '2026-02-14 09:30:00', '2026-02-14 09:30:00'),

-- EU
('Tobias Becker',       't.becker@beratung.de',     '+49-162-345-6789','+49-162-345-6789','Germany',     'Business Coaching',    'LinkedIn',    'Won',                  NULL,           'Frankfurt management consultant. Coaching for own personal brand launch.', '2026-02-05 11:00:00', '2026-03-15 10:00:00'),
('Camille Laurent',     'camille.l@startup.fr',     '+33-7-56-78-9012','+33-7-56-78-9012','France',      'Life Coaching',        'YouTube',     'Contacted',            '2026-04-28',   'Lyon tech entrepreneur post-exit. Figuring out what comes next.', '2026-02-11 14:45:00', '2026-02-11 14:45:00'),
('Pieter Vermeer',      'p.vermeer@consultancy.nl', '+31-6-8765-4321', '+31-6-8765-4321', 'Netherlands', 'Executive Coaching',   'Referral',    'Booked',               '2026-04-24',   'Rotterdam shipping company director. 360 leadership review requested.', '2026-02-18 10:00:00', '2026-03-30 09:00:00'),
('Anna Kowalski',       'anna.k@company.pl',        '+48-601-234-567', '+48-601-234-567', 'Poland',      'Career Coaching',      'LinkedIn',    'New',                  '2026-05-02',   'Warsaw product manager targeting director-level roles at EU tech firms.', '2026-02-22 09:15:00', '2026-02-22 09:15:00'),
('Luca De Angelis',     'luca.d@studio.it',         '+39-348-765-4321','+39-348-765-4321','Italy',       'Business Strategy',    'Instagram',   'Follow-up Scheduled',  '2026-04-23',   'Rome architect launching a design consultancy. Needs positioning strategy.', '2026-02-25 13:30:00', '2026-02-25 13:30:00'),

-- Asia
('Hiroshi Yamamoto',    'h.yamamoto@mgmt.jp',       '+81-90-8765-4321','+81-90-8765-4321','Japan',       'Business Coaching',    'LinkedIn',    'Won',                  NULL,           'Nagoya family business owner, 2nd generation. Transition and modernisation coaching.', '2026-02-06 09:30:00', '2026-03-20 10:00:00'),
('Soo-jin Lee',         'soojin.l@media.kr',        '+82-10-2345-6789','+82-10-2345-6789','South Korea', 'Business Strategy',    'YouTube',     'Contacted',            '2026-04-25',   'Seoul media production company founder. Wants international expansion roadmap.', '2026-02-13 11:00:00', '2026-02-13 11:00:00'),
('Priya Ramasamy',      'priya.r@consulting.sg',    '+65-8234-5678',   '+65-8234-5678',   'Singapore',   'Executive Coaching',   'LinkedIn',    'Booked',               '2026-04-22',   'Singapore MNC regional director. Preparing for global C-suite move.', '2026-02-19 14:00:00', '2026-03-26 10:00:00'),
('Takeshi Fujimoto',    't.fujimoto@biz.jp',        '+81-70-3456-7890','+81-70-3456-7890','Japan',       'Mindset Coaching',     'Referral',    'Follow-up Scheduled',  '2026-04-20',   'Tokyo entrepreneur. Anxiety around growth and decision-making. Warm referral.', '2026-02-24 10:30:00', '2026-02-24 10:30:00'),
('Marcus Tan',          'm.tan@ventures.sg',        '+65-9876-5432',   '+65-9876-5432',   'Singapore',   'Business Coaching',    'Website',     'Lost',                 NULL,           'Fintech founder. Went with a group mastermind instead. Budget constraints.', '2026-02-27 15:00:00', '2026-03-15 09:00:00'),

-- ============================================================
-- MARCH 2026
-- ============================================================

-- Scandinavia
('Emilia Strand',       'emilia.s@brand.se',        '+46-73-678-9012', '+46-73-678-9012', 'Sweden',      'Business Coaching',    'Instagram',   'Won',                  NULL,           'Stockholm D2C brand founder. Revenue stalled at £400k. Scaled to £700k in 90 days.', '2026-03-02 09:00:00', '2026-04-01 10:00:00'),
('Nils Andersen',       'nils.a@law.dk',            '+45-51-234-567',  '+45-51-234-567',  'Denmark',     'Executive Coaching',   'LinkedIn',    'Booked',               '2026-04-25',   'Copenhagen senior lawyer considering partnership. Leadership readiness.', '2026-03-05 11:30:00', '2026-04-05 14:00:00'),
('Tuuli Mäkinen',       'tuuli.m@design.fi',        '+358-40-987-6543','+358-40-987-6543','Finland',     'Career Coaching',      'YouTube',     'Contacted',            '2026-04-26',   'Helsinki UX lead at a Nordic bank. Wants to go independent in 2026.', '2026-03-08 14:00:00', '2026-03-08 14:00:00'),
('Bjørg Haugen',        'bjorg.h@invest.no',        '+47-912-56789',   '+47-912-56789',   'Norway',      'Business Strategy',    'Referral',    'Follow-up Scheduled',  '2026-04-19',   'Bergen property investor diversifying into coaching business. Referred by Bjørn Larsen.', '2026-03-12 10:00:00', '2026-03-12 10:00:00'),

-- USA
('Tyler Adams',         't.adams@pe.com',           '+1-214-555-0193', '+1-214-555-0193', 'USA',         'Executive Coaching',   'LinkedIn',    'Won',                  NULL,           'Dallas PE-backed CEO preparing for board-level conversations.', '2026-03-03 09:00:00', '2026-04-02 09:00:00'),
('Samantha Price',      's.price@agency.us',        '+1-404-555-0166', '+1-404-555-0166', 'USA',         'Business Coaching',    'Instagram',   'Booked',               '2026-04-24',   'Atlanta marketing agency owner. Call booked. Revenue doubled last year — needs structure.', '2026-03-07 10:30:00', '2026-04-07 16:00:00'),
('Kevin O\'Brien',      'kevin.ob@tech.us',         '+1-206-555-0178', '+1-206-555-0178', 'USA',         'Mindset Coaching',     'Referral',    'Contacted',            '2026-04-28',   'Seattle CTO dealing with imposter syndrome after big promotion.', '2026-03-11 13:30:00', '2026-03-11 13:30:00'),
('Rachel Kim',          'rachel.k@media.us',        '+1-310-555-0145', '+1-310-555-0145', 'USA',         'Life Coaching',        'YouTube',     'New',                  '2026-05-01',   'LA content strategist. Post-burnout. Watched full free training.', '2026-03-16 11:00:00', '2026-03-16 11:00:00'),

-- EU
('Franz Huber',         'f.huber@kanzlei.de',       '+49-176-234-5678','+49-176-234-5678','Germany',     'Executive Coaching',   'LinkedIn',    'Won',                  NULL,           'Munich law firm partner. Leadership presence and partner-level positioning.', '2026-03-04 10:00:00', '2026-04-03 10:00:00'),
('Amélie Fontaine',     'amelie.f@conseil.fr',      '+33-6-87-65-4321','+33-6-87-65-4321','France',      'Business Strategy',    'Referral',    'Contacted',            '2026-04-23',   'Bordeaux wine export business. Wants to expand DTC channel across EU.', '2026-03-09 09:30:00', '2026-03-09 09:30:00'),
('Jan Novak',           'jan.n@company.cz',         '+420-731-234-567','+420-731-234-567','Czech Republic','Career Coaching',    'LinkedIn',    'Follow-up Scheduled',  '2026-04-22',   'Prague senior manager targeting regional director role at a German MNC.', '2026-03-13 14:00:00', '2026-03-13 14:00:00'),
('Marta Sorensen',      'marta.s@nordic.dk',        '+45-61-345-678',  '+45-61-345-678',  'Denmark',     'Health Coaching',      'Instagram',   'Contacted',            '2026-04-27',   'CPH wellness brand founder. Energy management + strategic focus coaching.', '2026-03-17 11:00:00', '2026-03-17 11:00:00'),
('Lukas Hoffmann',      'lukas.h@startup.at',       '+43-676-123-4567','+43-676-123-4567','Austria',     'Business Coaching',    'Website',     'Booked',               '2026-04-25',   'Vienna SaaS founder. First B2B product reaching €1M ARR. Needs scale strategy.', '2026-03-21 10:00:00', '2026-04-10 15:00:00'),

-- Asia
('Haruto Sato',         'haruto.s@tech.jp',         '+81-90-5678-1234','+81-90-5678-1234','Japan',       'Business Strategy',    'LinkedIn',    'Won',                  NULL,           'Kyoto tech startup CTO turned CEO. First-time CEO coaching package signed.', '2026-03-06 09:00:00', '2026-04-04 10:00:00'),
('Jae-won Choi',        'jaewon.c@corp.kr',         '+82-10-7654-3210','+82-10-7654-3210','South Korea', 'Executive Coaching',   'Referral',    'Booked',               '2026-04-23',   'Samsung supply chain director. Preparing for overseas posting.', '2026-03-10 13:00:00', '2026-04-08 09:00:00'),
('Mei-Ling Ho',         'meiling.h@private.sg',     '+65-8765-4321',   '+65-8765-4321',   'Singapore',   'Life Coaching',        'Instagram',   'Contacted',            '2026-04-26',   'Singapore private banker. High-earning but unfulfilled. Exploring career pivot.', '2026-03-14 14:30:00', '2026-03-14 14:30:00'),
('Rin Yoshida',         'rin.y@marketing.jp',       '+81-80-6789-0123','+81-80-6789-0123','Japan',       'Career Coaching',      'YouTube',     'Follow-up Scheduled',  '2026-04-21',   'Osaka marketing manager. Wants to transition into a coaching role herself.', '2026-03-19 10:00:00', '2026-03-19 10:00:00'),
('Daniel Wong',         'd.wong@fund.sg',           '+65-9234-5678',   '+65-9234-5678',   'Singapore',   'Business Coaching',    'LinkedIn',    'New',                  '2026-05-03',   'Singapore hedge fund analyst launching a financial coaching brand.', '2026-03-25 11:30:00', '2026-03-25 11:30:00'),
('Hyun-soo Jung',       'hyunsoo.j@startup.kr',     '+82-10-3456-7891','+82-10-3456-7891','South Korea', 'Business Strategy',    'Website',     'Contacted',            '2026-04-29',   'Busan logistics startup. Pre-revenue pivot. Needs business model clarity.', '2026-03-28 15:00:00', '2026-03-28 15:00:00'),

-- ============================================================
-- APRIL 2026
-- ============================================================

-- Scandinavia
('Anders Holmberg',     'anders.h@fund.se',         '+46-70-345-6789', '+46-70-345-6789', 'Sweden',      'Business Strategy',    'LinkedIn',    'Booked',               '2026-04-28',   'Gothenburg PE associate going independent. Strategy session booked.', '2026-04-01 09:00:00', '2026-04-12 10:00:00'),
('Siv Engström',        'siv.e@coach.se',           '+46-73-789-0123', '+46-73-789-0123', 'Sweden',      'Executive Coaching',   'Referral',    'New',                  '2026-05-05',   'Stockholm HR director. Referred by Emilia Strand. Wants to move into coaching.', '2026-04-03 11:00:00', '2026-04-03 11:00:00'),
('Kristoffer Berg',     'kristoffer.b@ops.no',      '+47-456-12345',   '+47-456-12345',   'Norway',      'Business Coaching',    'LinkedIn',    'Contacted',            '2026-04-30',   'Stavanger ops manager at an energy company. Wants to launch consulting practice.', '2026-04-05 14:00:00', '2026-04-05 14:00:00'),
('Aino Laitinen',       'aino.l@digital.fi',        '+358-44-345-6789','+358-44-345-6789','Finland',     'Career Coaching',      'Instagram',   'Follow-up Scheduled',  '2026-04-22',   'Tampere digital marketer. Considering pivot to product management.', '2026-04-08 10:30:00', '2026-04-08 10:30:00'),

-- USA
('Jordan Williams',     'jordan.w@vc.us',           '+1-650-555-0132', '+1-650-555-0132', 'USA',         'Executive Coaching',   'LinkedIn',    'Booked',               '2026-04-29',   'Palo Alto VC principal. Wants to build thought leadership and personal brand.', '2026-04-02 09:30:00', '2026-04-14 11:00:00'),
('Megan Scott',         'm.scott@brand.com',        '+1-720-555-0149', '+1-720-555-0149', 'USA',         'Business Coaching',    'Referral',    'Contacted',            '2026-05-01',   'Denver brand consultant. Post-corporate. Building her first online program.', '2026-04-04 13:00:00', '2026-04-04 13:00:00'),
('Carlos Rivera',       'carlos.r@tech.us',         '+1-787-555-0163', '+1-787-555-0163', 'USA',         'Mindset Coaching',     'YouTube',     'New',                  '2026-05-06',   'Puerto Rico-based developer transitioning to tech leadership. High engagement online.', '2026-04-07 11:00:00', '2026-04-07 11:00:00'),
('Diane Hoffman',       'd.hoffman@corp.us',        '+1-202-555-0156', '+1-202-555-0156', 'USA',         'Life Coaching',        'Instagram',   'Contacted',            '2026-04-30',   'DC policy advisor. Exploring purpose-led career pivot. Highly reflective.', '2026-04-10 14:30:00', '2026-04-10 14:30:00'),

-- EU
('Stefan Zimmermann',   's.zimm@group.de',          '+49-179-876-5432','+49-179-876-5432','Germany',     'Business Strategy',    'LinkedIn',    'Booked',               '2026-04-26',   'Hamburg family business. 3rd generation. Strategy for digital transformation.', '2026-04-02 10:00:00', '2026-04-13 09:00:00'),
('Élise Moreau',        'elise.m@biz.fr',           '+33-6-23-45-6789','+33-6-23-45-6789','France',      'Executive Coaching',   'Website',     'New',                  '2026-05-07',   'Paris startup CMO. Filled out contact form after reading the free guide.', '2026-04-06 09:00:00', '2026-04-06 09:00:00'),
('Daan Visser',         'd.visser@tech.nl',         '+31-6-5432-1098', '+31-6-5432-1098', 'Netherlands', 'Business Coaching',    'Referral',    'Contacted',            '2026-05-02',   'Utrecht tech entrepreneur. 2nd business. Wants faster growth than first time.', '2026-04-09 13:30:00', '2026-04-09 13:30:00'),
('Katarzyna Wiśniewska','k.wisniewska@media.pl',    '+48-695-876-543', '+48-695-876-543', 'Poland',      'Career Coaching',      'Instagram',   'Follow-up Scheduled',  '2026-04-24',   'Warsaw TV journalist. Considering transition to online personal brand.', '2026-04-11 10:00:00', '2026-04-11 10:00:00'),
('Miklos Varga',        'm.varga@ventures.hu',      '+36-30-123-4567', '+36-30-123-4567', 'Hungary',     'Business Strategy',    'LinkedIn',    'New',                  '2026-05-08',   'Budapest startup ecosystem builder. Wants to position as regional advisor.', '2026-04-14 14:00:00', '2026-04-14 14:00:00'),

-- Asia
('Ryota Kimura',        'ryota.k@corp.jp',          '+81-90-9012-3456','+81-90-9012-3456','Japan',       'Executive Coaching',   'LinkedIn',    'Booked',               '2026-04-27',   'Fukuoka regional GM. Competing for national VP role. Executive presence.', '2026-04-03 09:30:00', '2026-04-15 10:00:00'),
('Chae-young Yoon',     'chaeyoung.y@brand.kr',     '+82-10-8901-2345','+82-10-8901-2345','South Korea', 'Business Coaching',    'Instagram',   'Contacted',            '2026-05-03',   'Seoul K-beauty brand co-founder. Wants to lead independently from her co-founder.', '2026-04-06 11:00:00', '2026-04-06 11:00:00'),
('Jonathan Ng',         'jonathan.n@advisory.sg',   '+65-9345-6789',   '+65-9345-6789',   'Singapore',   'Business Strategy',    'LinkedIn',    'Follow-up Scheduled',  '2026-04-23',   'Singapore M&A advisor moving to independent practice. IB background.', '2026-04-09 14:00:00', '2026-04-09 14:00:00'),
('Sakura Inoue',        'sakura.i@wellness.jp',     '+81-80-1234-5670','+81-80-1234-5670','Japan',       'Health Coaching',      'YouTube',     'New',                  '2026-05-05',   'Kyoto wellness influencer. 80k followers. Wants business coaching to monetise audience.', '2026-04-12 10:00:00', '2026-04-12 10:00:00'),
('Tae-yang Shin',       'taeyang.s@startup.kr',     '+82-10-6789-0123','+82-10-6789-0123','South Korea', 'Mindset Coaching',     'Referral',    'Contacted',            '2026-05-01',   'Daejeon deep tech founder. First-time CEO. Confidence and decision fatigue.', '2026-04-15 09:00:00', '2026-04-15 09:00:00'),
('Li Wei Zhang',        'liwei.z@holdings.sg',      '+65-8901-2345',   '+65-8901-2345',   'Singapore',   'Executive Coaching',   'LinkedIn',    'New',                  '2026-05-09',   'Singapore holding company director. Board-level communication skills.', '2026-04-16 14:00:00', '2026-04-16 14:00:00');
