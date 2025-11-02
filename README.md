# Overtime/Meal Allowance Claim System

A comprehensive Laravel 12 application for managing overtime and meal allowance claims with Livewire and Tailwind CSS.

## Features

### 🎯 Core Features
- **Multi-role System**: Staff, Approvers, HR Admins, and Payroll staff
- **Claim Management**: Submit, approve, and process overtime/meal allowance claims
- **Document Upload**: Support for attendance records and supporting documents
- **Automated Calculations**: Overtime hours and meal allowance calculations
- **Approval Workflow**: Complete claim lifecycle from submission to payment
- **Audit Trail**: Complete history of all claim actions
- **Email Notifications**: Automated notifications for all stakeholders
- **Reporting**: Comprehensive reports with export capabilities

### 👥 User Roles

#### Staff
- Submit overtime and meal allowance claims
- Upload required documents (attendance records, supporting docs)
- Track claim status and deadlines
- View personal claim history and statistics

#### Approvers (Managers)
- Review and approve/reject team member claims
- Add approval remarks and feedback
- Monitor team overtime patterns
- Dashboard with pending approvals

#### HR Admins
- Process approved claims for payroll
- Manage system settings and configurations
- Generate organization-wide reports
- Oversee all claims across departments

#### Payroll
- Update payment status for processed claims
- Export data for payroll integration
- Track payment completion

### 🏢 Business Logic

#### Working Hours & Overtime
- Configurable standard working hours (default: 8:00 AM - 5:00 PM)
- Weekday overtime rate: $25/hour
- Weekend overtime rate: $35/hour 
- Public holiday overtime rate: $50/hour
- Travel time exclusion (unless driving is core job function)

#### Meal Allowance
- $15 meal allowance for overtime extending beyond 7:00 PM
- Minimum 2 hours overtime required for eligibility
- Additional allowance for overtime exceeding 4 hours

#### Submission Rules
- Claims must be submitted within 2 months of duty date
- Monthly processing deadline: 9th of each month
- Automated reminders 7 days before deadline
- Monthly reminder on 5th of each month

#### Document Requirements
- Attendance record (Mac Check-In for out-of-office, Teams-HR for in-office)
- Supporting documents (program memos, itineraries, emails)
- Maximum file size: 5MB per file
- Supported formats: PDF, JPG, PNG, DOCX

## Technology Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Livewire 3, Tailwind CSS
- **Database**: SQLite (development), MySQL/PostgreSQL (production)
- **Authentication**: Laravel Breeze
- **File Storage**: Local storage with secure access
- **Email**: Laravel Mail with configurable templates

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- Git

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd claim-system
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database Setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build Frontend Assets**
   ```bash
   npm run build
   ```

7. **Start Development Server**
   ```bash
   php artisan serve
   ```

### Default Users

After running the seeders, you can login with these accounts:

| Role | Email | Password | Description |
|------|-------|----------|-------------|
| HR Admin | hr@company.edu | password | HR Administrator |
| Payroll | payroll@company.edu | password | Payroll Staff |
| Approver | it.manager@company.edu | password | IT Manager |
| Staff | it.staff@company.edu | password | IT Staff |
| Staff | ops.staff@company.edu | password | Operations Staff (with driving duties) |

## Configuration

### System Settings
Configure business rules through the `system_settings` table:

- Working hours (start/end times)
- Overtime rates (weekday/weekend/holiday)
- Meal allowance amounts and thresholds
- Submission deadlines and reminder schedules
- File upload limits and allowed types

## Security Features

- **Role-based Access Control**: Middleware enforces user permissions
- **Secure File Storage**: Documents stored outside public directory
- **Audit Logging**: All actions logged with user and IP tracking
- **Input Validation**: Comprehensive validation on all forms
- **CSRF Protection**: Laravel's built-in CSRF protection
- **Authentication**: Laravel Breeze with secure password hashing

## License

This project is licensed under the MIT License.

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
