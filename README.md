## Subscription Billing & Usage Metering System

A Laravel-based Subscription Billing and Usage Metering System developed as a Senior Laravel Developer take-home assignment.

The application manages merchants, plans, customers, subscriptions, subscription periods, usage events, daily usage aggregation, billing, invoices, plan changes and merchant-level usage dashboards.

The system is designed with scalability, idempotent usage ingestion, queued processing, billing accuracy and maintainability in mind.

## 1. Project Overview

This project implements a SaaS subscription billing and usage-metering system.

A merchant can:
Create subscription plans
Define included usage units
Configure overage pricing
Create customers
Subscribe customers to plans
Track customer usage
Aggregate daily usage
Generate billing invoices
Apply overage charges
Apply proration for partial billing periods
Change plans during a billing cycle
View usage and revenue-related dashboard information

The application provides both:
Authenticated Web UI
REST APIs

The REST API is protected using Laravel Sanctum.

The application also uses:
Laravel Services
Repository pattern
Form Requests
API Resources
Queue Jobs
Scheduler
Cache
Database transactions
Database indexes and constraints
Automated Feature tests

## 2. Business Problem

SaaS applications often charge customers based on a subscription plan and their actual usage.

Example:

Plan: Basic
Base Price: ₹499
Included Units: 1,000
Overage Rate: ₹0.50 per unit

If the customer uses 1,300 units:

Included Usage = 1,000
Actual Usage   = 1,300
Overage        = 300

Overage Amount = 300 × ₹0.50 = ₹150

Final billing before tax:

Base Price = ₹499
Overage    = ₹150
Subtotal   = ₹649

The system must also support duplicate usage requests, large usage volumes, daily aggregation, end-of-cycle billing, mid-cycle plan changes, proration, dashboard reporting and API rate limiting.

## 3. Main Features

### Merchant Management

Create, update, view and delete merchants
Merchant-specific currency
Merchant timezone
Merchant status

### Plan Management

Create, update, view and delete plans
Monthly/yearly billing cycles
Included usage units
Overage rate
Plan status
Plan pricing cache

### Customer Management

Create, update, view and delete customers
Merchant-specific customer code

### Subscription Management

Create and update subscriptions
Cancel subscriptions
Track current billing period
Associate customer with plan

### Subscription Periods

Store billing period
Store plan pricing snapshot
Store included units
Store overage rate
Track usage against the correct period

### Usage Metering

Record usage events
Idempotent usage ingestion
Validate subscription relationships
Validate billing period
Prevent invalid usage
Rate-limit usage endpoint

### Daily Usage Aggregation

Aggregate daily usage
Store total usage units
Store event count
Process usage using queue jobs
Avoid repeatedly scanning all raw events

### Billing

Generate invoices
Calculate base price
Calculate overage
Calculate proration
Calculate tax
Store invoice items
Issue/pay/void invoices

### Plan Changes

Upgrade plan
Downgrade plan
Effective date
Split billing periods
Preserve old/new pricing for billing

### Dashboard

Top 5 customers by current-month usage
Projected overage revenue
Customers whose usage dropped by more than 50% month-over-month

## 4. How the Project Works

Complete business flow:

Merchant
↓
Plan
↓
Customer
↓
Subscription
↓
Subscription Period
↓
Usage Events
↓
Idempotency + Validation
↓
Queued Daily Aggregation
↓
Daily Usage Aggregate
↓
Billing Cycle Ends
↓
Invoice Generation
↓
Base Price + Proration + Overage + Tax
↓
Final Invoice

## 5. Example End-to-End Scenario

Merchant: Demo SaaS
Customer: Ranjith Kumar
Plan: Basic
Base Price: ₹499
Included Units: 1,000
Overage Rate: ₹0.50
Billing Cycle: Monthly

Subscription period:
Start: 2026-10-01
End: 2026-11-01

Usage:
100 + 200 + 150 + 300 + 600 = 1,350 units

Overage:
1,350 - 1,000 = 350 units

Overage Amount:
350 × ₹0.50 = ₹175

Invoice before tax:
Base Amount = ₹499
Overage     = ₹175
Subtotal    = ₹674

## 6. Application Architecture

The application follows a layered architecture:

HTTP Request
↓
Controller
↓
Form Request / Validation
↓
Service Interface
↓
Service
↓
Repository Interface
↓
Repository
↓
Eloquent Model
↓
MySQL

Example:

SubscriptionController
↓
SubscriptionServiceInterface
↓
SubscriptionService
↓
SubscriptionRepositoryInterface
↓
SubscriptionRepository
↓
Subscription Model
↓
subscriptions table

## 7. Why Service + Repository Pattern?

Controller:
Receives HTTP requests
Calls services
Returns HTTP responses

Form Request:
Request validation
Validation rules
Validation messages

Service:
Business logic
Business validations
Transactions
Cross-model rules

Repository:
Database access
Queries
CRUD operations
Reusable data-access logic

Model:
Database representation
Relationships
Attribute casting

This keeps controllers thin and makes business logic easier to test and maintain.

## 8. Project Structure

subscription-billing/
├── app/
│   ├── Console/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   └── Web/
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Jobs/
│   ├── Models/
│   ├── Repositories/
│   └── Services/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
├── routes/
│   ├── api.php
│   ├── console.php
│   └── web.php
├── tests/
│   └── Feature/
├── .env.example
├── composer.json
├── package.json
└── README.md

## 9. Database Design

Main entities:
merchants
plans
customers
subscriptions
subscription_periods
usage_events
daily_usage_aggregates
plan_changes
invoices
invoice_items
users

Relationship overview:

Merchant
├── Plans
├── Customers
└── Subscriptions
├── Subscription Periods
├── Usage Events
├── Plan Changes
└── Invoices

## 10. Merchant

Important fields:
id
name
code
email
status
timezone
currency
phone
metadata

Merchant code is unique.

Merchant status:
active
inactive

## 11. Plan

A plan belongs to a merchant.

Important fields:
merchant_id
name
code
description
currency
base_price
billing_cycle
included_units
overage_rate
unit_name
status

Example:
Basic
₹499
1,000 included units
₹0.50 overage
Monthly

Plan code is unique within a merchant.

## 12. Customer

A customer belongs to a merchant.

Important fields:
merchant_id
code
name
email
phone
status

Customer code is unique within a merchant.

The same email address can exist for customers belonging to different merchants.

## 13. Subscription

A subscription connects:
Merchant
Customer
Plan

Important fields:
merchant_id
customer_id
plan_id
status
started_at
current_period_start
current_period_end
cancelled_at

Subscription status:
active
cancelled
expired

The service validates that merchant, customer and plan belong to the correct merchant.

## 14. Subscription Period

A subscription period represents the billing period applicable to a subscription.

Important fields:
subscription_id
plan_id
starts_at
ends_at
billing_cycle
base_price
included_units
overage_rate

The plan pricing is stored as a snapshot.

Historical billing should not change simply because the current plan price was modified.

## 15. Usage Event Flow

Usage is submitted through:

POST /api/usage

Flow:

API
↓
Authentication
↓
Rate Limiting
↓
Validation
↓
Relationship Validation
↓
Idempotency Check
↓
Usage Event Creation

## 16. Usage Event Data

Example request:

{
"merchant_id": 1,
"customer_id": 1,
"subscription_id": 1,
"subscription_period_id": 1,
"idempotency_key": "usage-10001",
"usage_units": 150,
"unit_name": "API Calls",
"occurred_at": "2026-10-05 10:30:00",
"metadata": {
"source": "api"
}
}

## 17. Usage Validation

Before creating a usage event, the system validates:
Merchant exists
Customer exists
Subscription exists
Subscription period exists
Customer belongs to merchant
Subscription belongs to merchant
Subscription belongs to customer
Subscription period belongs to subscription
Usage units are valid
Usage timestamp belongs to the applicable period
Idempotency key is valid
Subscription is eligible for usage

## 18. Idempotency

Usage ingestion must be idempotent because clients can retry requests.

First request:
idempotency_key = usage-10001
→ Usage created

Retry:
idempotency_key = usage-10001
→ Duplicate detected
→ Usage is not duplicated

The idempotency key is enforced per merchant.

## 19. Usage Rate Limiting

Main usage endpoint:

POST /api/usage

Current limit:
60 requests per minute

If exceeded:
HTTP 429 Too Many Requests

Rate limiting protects the API from accidental request loops, client bugs, excessive retries, abuse and traffic spikes.

## 20. Daily Usage Aggregation

Raw usage events can become very large.

Instead of repeatedly scanning all usage events, the system creates daily aggregates.

Flow:

usage_events
↓
Queued Aggregation Job
↓
daily_usage_aggregates

Aggregate fields:
merchant_id
customer_id
subscription_id
subscription_period_id
usage_date
total_usage_units
event_count

Example:
Date: 2026-10-05
Total Usage: 1,350
Event Count: 25

## 21. Queue-Based Processing

Daily aggregation is designed to run asynchronously.

Large usage datasets are processed in chunks rather than loading all rows into application memory.

Start the worker:

php artisan queue:work

## 22. Billing Flow

When a subscription period reaches its end:

Subscription Period
↓
Usage Aggregation
↓
Calculate Usage
↓
Calculate Base Price
↓
Calculate Proration
↓
Calculate Overage
↓
Calculate Tax
↓
Create Invoice
↓
Create Invoice Items

## 23. Invoice Calculation

Invoice:

Base Amount
+
Proration Amount
+
Overage Amount
=
Subtotal

Subtotal
+
Tax
=
Total

Invoice statuses:
draft
issued
paid
void

## 24. Overage Calculation

Example:

Base Price: ₹499
Included Units: 1,000
Usage: 1,350
Overage Rate: ₹0.50

Overage Units:
1,350 - 1,000 = 350

Overage Amount:
350 × ₹0.50 = ₹175

Invoice:
Base Amount = ₹499
Overage = ₹175
Subtotal = ₹674

## 25. No Overage Scenario

If:
Included Units = 1,000
Usage = 800

Then:
Overage Units = 0
Overage Amount = ₹0

## 26. Proration

Proration is required when a billing period represents only part of a full billing cycle.

Conceptual calculation:

Prorated Amount
=
Full Cycle Price
×
Applicable Period Duration
÷
Full Cycle Duration

Example:
Full Plan Price = ₹999
Applicable Duration = 10 days
Full Cycle Duration = 30 days

₹999 × 10 / 30 = ₹333

Proration is stored separately in the invoice breakdown.

## 27. Invoice Items

Invoice item types:
base
proration
overage

Example:

Base       ₹499
Proration  ₹100
Overage    ₹175
Subtotal   ₹774

Tax is then applied.

## 28. Mid-Cycle Upgrade / Downgrade

Example:

October 1
|
| Basic Plan
|
October 15
|
| Upgrade
|
| Premium Plan
|
November 1

Old plan applies before the effective time.
New plan applies after the effective time.

Separate applicable subscription periods allow usage to be billed against the correct plan.

## 29. Plan Change Validation

The system validates:
Subscription exists
Current plan is correct
Destination plan exists
Destination plan belongs to the same merchant
Destination plan is active
Destination plan differs from current plan
Effective date is valid
Duplicate plan changes are prevented

Recorded fields:
from_plan_id
to_plan_id
effective_at
change_type
reason
metadata

## 30. Merchant Dashboard

Endpoint:

GET /api/merchants/{id}/dashboard

Provides:
Top 5 customers by current-month usage
Projected current-cycle overage revenue
Customers whose usage dropped by more than 50% compared with the previous month

## 31. Caching Strategy

Plan and pricing information is frequently read.

Flow:

Request
↓
Plan Cache
├── Cache Hit → Return Plan
└── Cache Miss → Database → Store Cache

The cache has a defined TTL.

When a plan is updated or deleted, the related cache is invalidated.

Redis is recommended for production.

## 32. Database Indexing

Important indexed columns include:
merchant_id
customer_id
subscription_id
subscription_period_id
status
occurred_at
idempotency_key
billing_period_start
billing_period_end

Composite indexes are used for common multi-column query patterns.

## 33. Unique Constraints

Important business rules are enforced at database level.

Merchant:
code unique

Plan:
merchant_id + code unique

Customer:
merchant_id + code unique

Invoice:
merchant_id + invoice_number unique

Daily Usage Aggregate:
subscription_id + subscription_period_id + usage_date unique

## 34. Scaling to 50L+ Usage Events

50L = 5,000,000 usage events.

At this scale, repeatedly scanning the complete usage_events table would not be efficient.

The system addresses this using:

### Indexes

Indexes reduce scanned rows for common queries.

### Chunked Processing

Queue jobs process events in chunks.

### Daily Aggregates

Reporting and billing can use aggregates instead of repeatedly scanning raw events.

### Queue Workers

Aggregation and billing can be distributed across multiple workers.

### Database Partitioning

For production, usage_events can be partitioned by date.

Example:
usage_events_2026_01
usage_events_2026_02
usage_events_2026_03

### Archiving

Old raw usage data can be archived based on retention requirements.

### Read Replicas

Reporting workloads can use read replicas.

## 35. Normalized Data vs Denormalization

The transactional database remains normalized to avoid unnecessary duplication.

For high-volume reporting, daily aggregates are intentionally maintained.

Raw usage remains the source of truth.

Aggregates are optimized for:
Reporting
Dashboard queries
Billing calculations
Usage summaries

## 36. Authentication

### Web Authentication

The web application uses Laravel session authentication.

Demo credentials:

Email:
admin@example.com

Password:
password

### API Authentication

The API uses Laravel Sanctum.

Clients authenticate using:

Authorization: Bearer <token>

## 37. API Response Format

Successful response:

{
"success": true,
"message": "Operation completed successfully.",
"data": {}
}

Error response:

{
"success": false,
"message": "Unable to process the request.",
"data": null
}

## 38. Merchant APIs

GET    /api/merchants
POST   /api/merchants
GET    /api/merchants/{id}
PUT    /api/merchants/{id}
DELETE /api/merchants/{id}

Example:

{
"name": "Demo Merchant",
"code": "DEMO001",
"email": "demo@example.com",
"status": "active"
}

## 39. Plan APIs

GET    /api/plans
POST   /api/plans
GET    /api/plans/{id}
PUT    /api/plans/{id}
DELETE /api/plans/{id}

Example:

{
"merchant_id": 1,
"name": "Basic",
"code": "BASIC",
"description": "Basic subscription plan",
"currency": "INR",
"base_price": 499,
"billing_cycle": "monthly",
"included_units": 1000,
"overage_rate": 0.50,
"unit_name": "API Calls",
"status": "active"
}

## 40. Customer APIs

GET    /api/customers
POST   /api/customers
GET    /api/customers/{id}
PUT    /api/customers/{id}
DELETE /api/customers/{id}

Example:

{
"merchant_id": 1,
"code": "CUST001",
"name": "Ranjith Kumar",
"email": "ranjith@example.com",
"phone": "9876543210",
"status": "active"
}

## 41. Subscription APIs

GET    /api/subscriptions
POST   /api/subscriptions
GET    /api/subscriptions/{id}
PUT    /api/subscriptions/{id}
DELETE /api/subscriptions/{id}

Example:

{
"merchant_id": 1,
"customer_id": 1,
"plan_id": 1,
"status": "active",
"started_at": "2026-10-01 00:00:00",
"current_period_start": "2026-10-01 00:00:00",
"current_period_end": "2026-11-01 00:00:00"
}

## 42. Subscription Period APIs

GET    /api/subscription-periods
POST   /api/subscription-periods
GET    /api/subscription-periods/{id}
PUT    /api/subscription-periods/{id}
DELETE /api/subscription-periods/{id}

Example:

{
"subscription_id": 1,
"starts_at": "2026-10-01 00:00:00",
"ends_at": "2026-11-01 00:00:00"
}

## 43. Usage APIs

Main endpoint:

POST /api/usage

Example:

{
"merchant_id": 1,
"customer_id": 1,
"subscription_id": 1,
"subscription_period_id": 1,
"idempotency_key": "usage-10001",
"usage_units": 150,
"unit_name": "API Calls",
"occurred_at": "2026-10-05 10:30:00",
"metadata": {
"source": "api"
}
}

Other usage management endpoints:

GET    /api/usage-events
GET    /api/usage-events/{id}
PUT    /api/usage-events/{id}
DELETE /api/usage-events/{id}

Usage deletion is restricted because usage records are billing-related records.

## 44. Daily Usage Aggregate APIs

GET  /api/daily-usage-aggregates
GET  /api/daily-usage-aggregates/{id}
POST /api/daily-usage-aggregates/generate

Example:

{
"subscription_period_id": 1,
"usage_date": "2026-10-05"
}

## 45. Invoice APIs

Invoice generation:

POST /api/invoices/generate

Invoice lookup:

GET /api/invoices/{id}

Invoice lifecycle operations:
Issue
Pay
Void

Invoice contains:
invoice_number
billing_period_start
billing_period_end
base_amount
overage_amount
proration_amount
subtotal
tax_rate
tax_amount
total
currency
status

## 46. Plan Change APIs

Plan changes support:
upgrade
downgrade

Example:

{
"subscription_id": 1,
"from_plan_id": 1,
"to_plan_id": 2,
"effective_at": "2026-10-15 00:00:00",
"change_type": "upgrade",
"reason": "Customer requested higher usage limit"
}

## 47. Dashboard API

GET /api/merchants/{id}/dashboard

Returns:
Top 5 customers by usage
Projected overage revenue
Customers with more than 50% usage drop month-over-month

## 48. Web Application

The Web UI makes the system easier to demonstrate and manage without manually sending every API request.

Typical flow:

Login
↓
Dashboard
↓
Merchants
↓
Plans
↓
Customers
↓
Subscriptions
↓
Subscription Periods
↓
Usage
↓
Aggregation
↓
Invoices
↓
Plan Changes

## 49. Setup Instructions

### Requirements

PHP 8.2+
Composer
MySQL 8+
Node.js
npm

Check versions:

php -v
composer -V
mysql --version
node -v
npm -v

## 50. Install the Project

Clone:

git clone <repository-url>

Enter:

cd subscription-billing

Install PHP dependencies:

composer install

Install frontend dependencies:

npm install

Create environment:

cp .env.example .env

Windows PowerShell:

Copy-Item .env.example .env

Generate key:

php artisan key:generate

## 51. Database Configuration

Create database:

CREATE DATABASE subscription_billing;

Configure .env:

APP_NAME="Subscription Billing"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=subscription_billing
DB_USERNAME=root
DB_PASSWORD=

Update credentials for the local environment.

## 52. Run Migrations

php artisan migrate

For a development database reset:

php artisan migrate:fresh

For reset and seed:

php artisan migrate:fresh --seed

WARNING: migrate:fresh deletes existing tables and data. Use only for development/testing.

## 53. Seed Demo Data

php artisan db:seed

Demo data contains:
Admin user
Merchant
Plans
Customers
Subscriptions
Subscription periods
Usage events

Demo login:

admin@example.com
password

## 54. Run the Application

php artisan serve

Application:
http://127.0.0.1:8000

Login:
http://127.0.0.1:8000/login

## 55. Run Queue Worker

php artisan queue:work

## 56. Run Scheduler

php artisan schedule:work

## 57. Billing Dispatch Command

php artisan billing:dispatch

For a specific date:

php artisan billing:dispatch --date=2026-10-05

## 58. Recommended Local Development Setup

Terminal 1:
php artisan serve

Terminal 2:
php artisan queue:work

Terminal 3:
php artisan schedule:work

Terminal 4:
npm run dev

## 59. Postman Testing Flow

Recommended sequence:

1. Login
2. Copy Sanctum token
3. Configure Bearer token
4. Create Merchant
5. Create Plan
6. Create Customer
7. Create Subscription
8. Create Subscription Period
9. Create Usage Event
10. Repeat same usage request
11. Verify idempotency
12. Generate Daily Aggregate
13. Generate Invoice
14. Verify Overage
15. Test Plan Change
16. Check Dashboard

## 60. Testing

Run all tests:

php artisan test

Important test areas:
Authentication
Authorization
Merchant management
Plan management
Customer management
Subscription rules
Subscription period validation
Usage validation
Idempotency
Relationship validation
Daily aggregation
Invoice generation
Overage calculation
Tax calculation
Proration
Duplicate invoice prevention
Rate limiting

## 61. Billing Edge Cases

Full billing cycle:
Full base price
Overage
Tax

Usage below included limit:
Overage = 0

Usage above included limit:
Overage = Usage - Included Units

Partial period:
Proration applied

Zero usage:
No overage

Invalid tax:
Rejected

Duplicate invoice:
Prevented

## 62. Assumptions

### Raw Usage is the Source of Truth

usage_events remains the detailed source of truth.

Daily aggregates are derived data used to improve reporting and billing performance.

### Usage is Idempotent

Every usage ingestion request should contain an idempotency key.

### Pricing is Snapshotted

Subscription periods store:
base_price
included_units
overage_rate
billing_cycle

This protects historical billing accuracy.

### Overage Calculation

Overage Units = max(0, Actual Usage - Included Units)

Overage Amount = Overage Units × Overage Rate

### Usage Must Belong to a Billing Period

Usage events must belong to the appropriate subscription period.

### Mid-Cycle Plan Change

A mid-cycle plan change is represented through separate applicable subscription periods.

### Rate Limiting

The usage endpoint is rate limited to 60 requests/minute.

## 63. Design Decisions

### Service Layer

Business rules are kept inside services rather than controllers.

### Repository Layer

Database access is separated from business logic using repositories and repository interfaces.

### Form Requests

Request validation is handled using Laravel Form Requests.

### API Resources

API Resources control API response structure.

### Database Constraints

Important business rules are enforced at both application and database levels.

### Queue Processing

Usage aggregation and billing-related work can be processed asynchronously.

### Cache

Frequently accessed plan information is cached and invalidated when pricing-related plan data changes.

## 64. Ambiguous Requirements and Decisions

Where the assignment did not specify an exact implementation, reasonable assumptions were made instead of blocking development.

Cache:
Laravel Cache is used for development; Redis is recommended for production.

Queue:
Laravel Queue abstraction is used so the application can move to Redis or another production queue backend.

Usage aggregation:
Daily aggregation is used to optimize reporting and billing.

Rate limit:
60 requests/minute was selected for the usage endpoint.

Billing data:
Pricing values are snapshotted at subscription-period level.

## 65. Security Considerations

The application uses:
Password hashing
Session authentication
Sanctum API authentication
CSRF protection
Form Request validation
Authentication middleware
Admin authorization
Rate limiting
Eloquent parameter binding
Mass-assignment protection
Database foreign keys

Never commit real credentials.

Do not commit .env.

Commit .env.example.

## 66. Production Scaling Considerations

For production:
Redis
Laravel Horizon
Multiple queue workers
MySQL read replicas
Database partitioning
Usage data archiving
Load balancing
Centralized logging
Application monitoring
Queue monitoring

Possible architecture:

Load Balancer
↓
App 1 / App 2 / App 3
↓
MySQL + Read Replicas

Usage API
↓
Redis / Queue
↓
Multiple Workers

## 67. Cache Invalidation

Plan data is cached to reduce database reads.

When a plan is updated or deleted, related cache is invalidated.

For multiple application servers, Redis is recommended as shared cache storage.

## 68. Useful Artisan Commands

php artisan optimize:clear
php artisan migrate
php artisan migrate:fresh
php artisan migrate:fresh --seed
php artisan serve
php artisan queue:work
php artisan schedule:work
php artisan billing:dispatch
php artisan test

## 69. AI-Assisted Development - Prompt Log

AI-assisted development tools were used during the development of this project.

Actual prompts used during development should be provided under:

/prompts

Recommended structure:

prompts/
├── 01-project-architecture.png
├── 02-database-design.png
├── 03-merchant-module.png
├── 04-plan-module.png
├── 05-customer-module.png
├── 06-subscription-module.png
├── 07-usage-module.png
├── 08-aggregation-module.png
├── 09-invoice-module.png
├── 10-plan-change-module.png
├── 11-dashboard-module.png
├── 12-testing.png
└── 13-readme.png

The screenshots should contain the actual prompts used during development.

## 70. Submission Structure

subscription-billing/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
├── prompts/
├── .env.example
├── composer.json
├── package.json
├── phpunit.xml
└── README.md

Do not commit:
.env
vendor/
node_modules/

## 72. Final Submission Checklist

### Codebase

Full Laravel application included
GitHub repository created
Repository access shared with evaluator
.env is not committed
.env.example included

### Application

Application starts successfully
Database migrations work
Seeders work
Web login works
API authentication works
Merchant module works
Plan module works
Customer module works
Subscription module works
Subscription period module works
Usage endpoint works
Idempotency works
Rate limiting works
Daily aggregation works
Queue processing works
Billing works
Invoice generation works
Overage calculation works
Proration works
Plan change works
Dashboard works

### Tests

Full test suite passes
Billing edge cases tested
Aggregation tested
Usage validation tested
Idempotency tested
Authentication tested
Authorization tested
Rate limiting tested

### README

Architecture explained
Setup instructions included
Project workflow explained
API usage documented
Assumptions documented
Design decisions documented
Scaling strategy documented
Testing strategy documented

### Prompt Log

/prompts folder created
Actual AI prompts captured
Prompt screenshots included

### Screen Recording

5–10 minute recording
Application walkthrough
Architecture explanation
Billing explanation
Usage explanation
Plan change explanation
Dashboard explanation
Scaling explanation
Narration included

## 73. Final Project Flow

MERCHANT
|
+---- PLANS
|
+---- CUSTOMERS
|
SUBSCRIPTIONS
|
SUBSCRIPTION PERIOD
|
USAGE EVENTS
|
Idempotency + Validation
|
QUEUE JOB
|
DAILY USAGE AGGREGATE
|
BILLING CYCLE END
|
INVOICE SERVICE
|
+-----+-----+-----+
|           |     |
BASE      PRORATION OVERAGE
|           |     |
+-----+-----+-----+
|
TAX
|
FINAL INVOICE
|
DASHBOARD

## 74. Conclusion

This project demonstrates a Laravel-based subscription billing and usage metering architecture with:

Clean layered architecture
Service and Repository patterns
REST APIs
Sanctum authentication
Web authentication
Idempotent usage ingestion
Rate limiting
Queue-based aggregation
Daily usage aggregation
Overage billing
Proration
Invoice generation
Mid-cycle plan changes
Plan/pricing caching
Merchant dashboard
Automated testing
Database indexing and constraints
Scalability considerations for 50L+ usage events

The implementation focuses on maintaining billing correctness while keeping the application maintainable and scalable for larger usage volumes.
