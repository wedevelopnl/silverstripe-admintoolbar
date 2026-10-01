-- SilverStripe's test framework creates temporary databases named ss_tmpdb_*,
-- which needs CREATE/DROP DATABASE rights. Grant those per database pattern
-- rather than as ALL PRIVILEGES ON *.*, which also hands out global
-- administrative privileges. `_` is a wildcard in MySQL grant patterns.
GRANT ALL PRIVILEGES ON `ss\_tmpdb\_%`.* TO 'silverstripe'@'%';
GRANT ALL PRIVILEGES ON `silverstripe%`.* TO 'silverstripe'@'%';
FLUSH PRIVILEGES;
