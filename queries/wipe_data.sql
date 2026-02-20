-- DESTRUCTIVE: WIPE ALL DATA
-- This script deletes all data from all tables and resets auto-increment counters.
-- Use this before running a seed script if you want a clean slate.

SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE prescriptions;
TRUNCATE TABLE lab_requests;
TRUNCATE TABLE appointments;
TRUNCATE TABLE inventory;
TRUNCATE TABLE patients;
TRUNCATE TABLE users;

SET FOREIGN_KEY_CHECKS = 1;
