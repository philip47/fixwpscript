# NextScripts SNAP Plugin - Security and Compatibility Fixes

## Version 4.4.7 (June 21, 2025)

### Security Fixes
- **Fixed SQL Injection vulnerabilities**: Replaced improper use of `wpdb->prepare()` with table names as parameters
- **Added input sanitization**: All `$_POST`, `$_GET`, and `$_REQUEST` data is now properly sanitized
- **Added direct access protection**: All PHP files now check for `ABSPATH` to prevent direct access
- **Improved email security**: Added `sanitize_email()` and `sanitize_text_field()` for email notifications
- **Fixed charset encoding**: Updated to UTF-8 for better security

### Database Compatibility Fixes
- **MariaDB/MySQL compatibility**: Fixed SQL syntax errors with table names in prepared statements
- **Removed deprecated datetime defaults**: Replaced `'0000-00-00 00:00:00'` with `CURRENT_TIMESTAMP`
- **Updated data types**: Removed deprecated `bigint(20)` syntax, now uses `bigint`
- **Added proper PRIMARY KEY**: Replaced `UNIQUE KEY id (id)` with `PRIMARY KEY (id)`
- **Added database indexes**: Improved query performance with proper indexing

### WordPress Compatibility
- **Updated WordPress requirements**: Now requires WordPress 5.0+, tested up to 6.6
- **PHP 7.4+ compatibility**: Updated minimum PHP version requirement
- **Fixed deprecated functions**: Updated for newer WordPress versions
- **Network/Multisite support**: Added proper network plugin support

### Specific Bug Fixes
1. **Fixed "Unknown column 'id' in 'SELECT'"**: Corrected SQL query structure in `nxs_checkQuery()`
2. **Fixed "error in your SQL syntax"**: Removed incorrect table name parameterization in `wpdb->prepare()`
3. **Fixed table creation errors**: Updated CREATE TABLE statements for MariaDB compatibility
4. **Fixed cron job errors**: Improved error handling in scheduled tasks

### Technical Improvements
- **Database table version bumps**: 
  - `nxs_log` table: v1.5 → v1.6
  - `nxs_query` table: v1.3 → v1.4
- **Improved error handling**: Better error messages and logging
- **Code cleanup**: Removed duplicate and problematic query sections
- **Performance improvements**: Added database indexes for better query performance

### Files Modified
- `NextScripts_SNAP.php` - Main plugin file, version update, security headers
- `inc/nxs_functions_wp.php` - Database functions, table creation, security fixes
- `inc/nxs_functions_engine.php` - Query engine, SQL fixes, input sanitization

### Migration Notes
- Database tables will be automatically updated on plugin activation
- No manual intervention required for existing installations
- Backup recommended before updating

### Testing
- Tested with MariaDB 10.6+
- Tested with MySQL 8.0+
- Tested with PHP 7.4, 8.0, 8.1, 8.2
- Tested with WordPress 5.0 through 6.6

### Security Audit
All identified security vulnerabilities have been addressed:
- ✅ SQL injection prevention
- ✅ Input validation and sanitization
- ✅ Direct file access protection
- ✅ Proper error handling
- ✅ Secure database operations