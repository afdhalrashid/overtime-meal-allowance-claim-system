# Claim System - Project Summary

## Overview
A comprehensive overtime/meal allowance claim system built with Laravel 12, Livewire, and Tailwind CSS for university staff management.

## Tech Stack
- **Framework**: Laravel 12
- **Frontend**: Livewire 3 + Tailwind CSS v4
- **Database**: SQLite (development)
- **Authentication**: Laravel Breeze
- **Real-time**: Livewire reactive components
- **Notifications**: Laravel Notifications (Email + Database)

## User Roles & Features

### 🧑‍💼 Staff
- Submit overtime/meal allowance claims
- Upload attendance records and supporting documents
- Real-time calculation of overtime amounts and meal allowances
- Track claim status and history
- Receive notifications for claim updates

### 👨‍💼 Approvers
- Review team member claims
- Approve or reject claims with reasons
- Dashboard with pending approvals
- Email notifications for new submissions

### 👩‍💼 HR Administrators
- Process approved claims
- View comprehensive statistics
- Filter and search claims
- Manage system settings

### 💰 Payroll Staff
- Mark processed claims as paid
- View payment reports
- Track payment history

## Key Features

### 📋 Claim Management
- **Draft System**: Save and edit claims before submission
- **Automatic Calculations**: Real-time overtime and meal allowance calculations
- **File Uploads**: Secure document attachment system
- **Deadline Tracking**: 2-month submission deadline with warnings
- **Audit Trail**: Complete history of all claim actions

### 🔔 Notification System
- **Email Notifications**: Status updates, new submissions, deadline reminders
- **Database Notifications**: In-app notification dropdown
- **Real-time Updates**: Livewire-powered reactive interface
- **Smart Notifications**: Context-aware messages with claim details

### 🛡️ Security & Access Control
- **Role-based Middleware**: Separate access for each user type
- **Secure File Storage**: Protected document uploads
- **CSRF Protection**: Laravel security features
- **Input Validation**: Comprehensive form validation

### 📊 Reporting & Analytics
- **Dashboard Statistics**: Claims by status, amounts, hours
- **Filter Options**: By date, department, status
- **Export Capabilities**: Ready for CSV/PDF export
- **Department Analytics**: Team-based reporting

## Database Schema

### Core Tables
1. **users** - User accounts with roles and departments
2. **claims** - Main claim records with status workflow
3. **claim_documents** - File attachments
4. **departments** - Organizational structure
5. **holidays** - Holiday calendar integration
6. **audit_logs** - Complete action history
7. **system_settings** - Configurable rates and rules
8. **notifications** - In-app notification storage

### Relationships
- Users belong to departments
- Claims belong to users and have approvers/processors
- Documents belong to claims
- Audit logs track all changes

## Livewire Components

### 🏠 Dashboard Components
- **StaffDashboard**: Claim overview and quick actions
- **ApproverDashboard**: Team claim management
- **HrDashboard**: System-wide claim processing

### 📝 Forms & Interactions
- **ClaimForm**: Smart form with real-time calculations
- **FileUpload**: Drag-and-drop document handling
- **Notifications**: Real-time notification dropdown

## Business Logic

### 💰 Calculation Rules
- **Overtime Rate**: $25/hour (configurable)
- **Meal Allowance**: $15 for 3+ hours overtime
- **Holiday Multipliers**: Enhanced rates for holidays
- **Department Rates**: Different rates per department

### 📅 Workflow
1. **Draft** → Staff creates and edits claim
2. **Pending Approval** → Submitted to approver
3. **Approved/Rejected** → Approver decision
4. **Processed** → HR processes approved claims
5. **Paid** → Payroll marks as paid

### ⏰ Deadlines
- **Submission**: 2 months from duty date
- **Approval**: Configurable SLA
- **Processing**: Monthly batch processing
- **Payment**: Next payroll cycle

## File Structure

```
app/
├── Http/Middleware/         # Role-based access control
├── Livewire/               # Interactive components
├── Models/                 # Eloquent models with relationships
├── Notifications/          # Email and database notifications
└── Console/Commands/       # Deadline reminder commands

resources/views/
├── livewire/              # Livewire component views
├── layouts/               # App layout with notifications
└── components/            # Reusable UI components

database/migrations/       # Complete schema migrations
```

## Setup Instructions

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+

### Installation
```bash
# Clone and setup
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Start server
php artisan serve
```

### Default Users
```
Admin: admin@university.edu / password
Approver: approver@university.edu / password
Staff: staff@university.edu / password
HR: hr@university.edu / password
Payroll: payroll@university.edu / password
```

## Deployment Considerations

### Production Setup
- Configure email SMTP settings
- Setup queue workers for notifications
- Configure file storage (S3/local)
- Setup scheduled commands for reminders
- Enable HTTPS and security headers

### Scaling
- Queue system for heavy operations
- Database indexing for large datasets
- CDN for file storage
- Redis for sessions and cache

## Future Enhancements

### 📱 Mobile App
- React Native/Flutter app
- Push notifications
- Offline claim drafting

### 🔗 Integrations
- LDAP/Active Directory authentication
- Payroll system integration
- Calendar system integration
- HR management system sync

### 📈 Advanced Features
- Machine learning for approval predictions
- Advanced reporting with charts
- Multi-language support
- API for external integrations

## Support & Maintenance

### Monitoring
- Application logs in `storage/logs/`
- Error tracking with detailed stack traces
- Performance monitoring for database queries
- File upload monitoring

### Backup Strategy
- Database backups (automated)
- File storage backups
- Configuration backups
- Regular system health checks

---

**Built with ❤️ using Laravel 12, Livewire 3, and Tailwind CSS v4**

*This system provides a complete solution for overtime and meal allowance claim management with modern web technologies and best practices.*
