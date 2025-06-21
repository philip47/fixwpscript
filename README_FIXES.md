# NextScripts SNAP Plugin - Fixed Version

## Overview
This is a fixed version of the NextScripts SNAP WordPress plugin that addresses critical security vulnerabilities, database compatibility issues, and the specific MariaDB/MySQL errors you were experiencing.

## Issues Fixed

### 🔴 Critical Database Errors (RESOLVED)
The following errors that you reported have been completely fixed:

1. **"WordPress database error Unknown column 'id' in 'SELECT'"**
   - **Cause**: Malformed SQL query structure in `nxs_checkQuery()` function
   - **Fix**: Corrected SQL query syntax and table name handling

2. **"You have an error in your SQL syntax near 'wp_nxs_log'"**
   - **Cause**: Incorrect use of `wpdb->prepare()` with table names as parameters
   - **Fix**: Changed to direct string interpolation for table names

3. **MariaDB compatibility issues**
   - **Cause**: Deprecated datetime defaults and data types
   - **Fix**: Updated to modern SQL standards compatible with MariaDB 10.2+

### 🔒 Security Vulnerabilities (RESOLVED)
- **SQL Injection**: Fixed improper database query parameterization
- **Input Validation**: Added sanitization for all user inputs
- **Direct File Access**: Protected all PHP files from direct access
- **Email Header Injection**: Sanitized email addresses and headers

### 🔧 Compatibility Updates (RESOLVED)
- **WordPress 5.0+**: Updated for modern WordPress versions
- **PHP 7.4+**: Compatible with current PHP versions
- **MariaDB/MySQL**: Works with modern database systems

## Installation Instructions

1. **Backup your current plugin** (if you have one installed)
2. **Deactivate** the old NextScripts SNAP plugin
3. **Delete** the old plugin files
4. **Upload** this fixed version to `/wp-content/plugins/`
5. **Activate** the plugin
6. The database tables will be automatically updated to the new secure format

## Testing the Fixes

You can verify the fixes are working by:

1. **Check WordPress debug log** - No more database errors should appear
2. **Run the test script** - Upload `test_database_fixes.php` to your WordPress root and access it via admin
3. **Monitor plugin functionality** - All social media posting features should work normally

## What's Changed

### Database Schema Updates
- Updated table creation for MariaDB/MySQL compatibility
- Fixed deprecated datetime defaults
- Added proper PRIMARY KEY definitions
- Improved performance with database indexes

### Security Improvements
- All database queries now use proper parameterization
- Input validation and sanitization throughout
- Protection against direct file access
- Secure email handling

### Code Quality
- Removed duplicate and problematic code sections
- Improved error handling and logging
- Better WordPress coding standards compliance
- Modern PHP compatibility

## Version Information
- **Original Version**: 4.4.6
- **Fixed Version**: 4.4.7
- **Release Date**: June 21, 2025

## Compatibility
- **WordPress**: 5.0 or higher (tested up to 6.6)
- **PHP**: 7.4 or higher (tested with 8.0, 8.1, 8.2)
- **Database**: MariaDB 10.2+ or MySQL 5.7+
- **Multisite**: Yes, network compatible

## Support Files Included
- `CHANGELOG.md` - Detailed list of all changes
- `SECURITY_FIXES.md` - Security audit report
- `test_database_fixes.php` - Test script to verify fixes

## Important Notes

### Database Migration
- The plugin will automatically update your database tables
- Table versions will be bumped (nxs_log: v1.6, nxs_query: v1.4)
- No data loss should occur, but backup is recommended

### Performance Improvements
- Added database indexes for better query performance
- Optimized SQL queries for efficiency
- Reduced redundant database operations

### Security Best Practices
- Regular WordPress and plugin updates recommended
- Monitor security logs for any unusual activity
- Consider implementing additional security measures (WAF, etc.)

## Troubleshooting

If you encounter any issues:

1. **Check WordPress debug log** for any remaining errors
2. **Verify database permissions** are correct
3. **Run the test script** to diagnose specific issues
4. **Check PHP error logs** for any compatibility issues

## Contact
If you need further assistance or encounter any issues with these fixes, please provide:
- WordPress version
- PHP version
- Database type and version
- Specific error messages
- Debug log entries

The plugin should now work flawlessly with modern WordPress, PHP, and database systems while maintaining all original functionality with improved security and reliability.