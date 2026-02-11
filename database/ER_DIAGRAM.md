# LEMS – Entity Relationship Diagram & Architecture

## 📊 ER Diagram (Entity Relationship)

```
┌─────────────────────┐
│      USERS          │
│─────────────────────│
│ PK id               │
│    fullname         │
│ UK username         │
│    password (hash)  │
│    role             │◄────────────────┐
│    status           │                 │
│    last_login       │                 │
│    created_at       │                 │
│    updated_by       │                 │
└─────────────────────┘                 │
         │                              │
         │ 1:N                          │
         ▼                              │
┌─────────────────────┐                 │
│   LOGIN_LOGS        │                 │
│─────────────────────│                 │
│ PK id               │                 │
│ FK user_id          │                 │
│    username         │                 │
│    ip_address       │                 │
│    user_agent       │                 │
│    login_time       │                 │
│    logout_time      │                 │
│    status           │                 │
└─────────────────────┘                 │
                                        │
┌─────────────────────┐                 │
│   ACTIVITY_LOGS     │                 │
│─────────────────────│                 │
│ PK id               │                 │
│ FK user_id          │◄────────────────┤
│    username         │                 │
│    action_type      │                 │
│    module           │                 │
│    reference_id     │                 │
│    description      │                 │
│    old_data (JSON)  │                 │
│    new_data (JSON)  │                 │
│    ip_address       │                 │
│    created_at       │                 │
└─────────────────────┘                 │
                                        │
┌─────────────────────┐                 │
│       CASES         │                 │
│─────────────────────│                 │
│ PK id               │                 │
│    product          │                 │
│    debtor_name      │◄────┐           │
│    citizen_id       │     │           │
│    address          │     │           │
│    contract_no      │     │           │
│    contract_date    │     │           │
│    court            │     │           │
│    black_case       │     │           │
│    red_case         │     │           │
│    filing_date      │     │           │
│    judgment_date    │     │           │
│    enforcement_status│    │           │
│    principal_amount │     │           │
│    total_amount     │     │           │
│    status           │     │           │
│ FK created_by       │─────┼───────────┤
│ FK updated_by       │     │           │
└─────────────────────┘     │           │
         │                  │           │
         │ 1:N              │           │
         ▼                  │           │
┌─────────────────────┐     │           │
│   ASSIGNMENTS       │     │           │
│─────────────────────│     │           │
│ PK id               │     │           │
│ FK case_id          │─────┘           │
│ FK officer_id       │─────────────────┤
│    assigned_date    │                 │
│    work_date        │                 │
│    status           │                 │
│    remarks          │                 │
│ FK created_by       │─────────────────┤
│ FK updated_by       │                 │
└─────────────────────┘                 │
         │                              │
         │ 1:1                          │
         ▼                              │
┌─────────────────────┐                 │
│  FIELD_REPORTS      │                 │
│─────────────────────│                 │
│ PK id               │                 │
│ FK assignment_id    │                 │
│    asset_investigation│               │
│    seized_asset_type│                 │
│    enforcement_status│                │
│    report_detail    │                 │
│    extra_detail     │                 │
│    latitude         │                 │
│    longitude        │                 │
│    location_accuracy│                 │
│    approved_by_admin│                 │
│ FK approved_by      │─────────────────┤
│    approved_at      │                 │
│ FK created_by       │─────────────────┤
│ FK updated_by       │                 │
└─────────────────────┘                 │
         │                              │
         │ 1:N                          │
         ▼                              │
┌─────────────────────┐                 │
│  REPORT_IMAGES      │                 │
│─────────────────────│                 │
│ PK id               │                 │
│ FK report_id        │                 │
│    image_path       │                 │
│    image_name       │                 │
│    file_size        │                 │
│    created_at       │                 │
│ FK created_by       │─────────────────┘
└─────────────────────┘
```

## 🔗 Relationships Summary

| Relationship | Type | Description |
|-------------|------|-------------|
| users → login_logs | 1:N | ผู้ใช้หนึ่งคนมีหลาย login records |
| users → activity_logs | 1:N | ผู้ใช้หนึ่งคนมีหลาย activity records |
| users → cases (created_by) | 1:N | ผู้ใช้หนึ่งคนสร้างหลายคดี |
| users → cases (updated_by) | 1:N | ผู้ใช้หนึ่งคนแก้ไขหลายคดี |
| cases → assignments | 1:N | คดีหนึ่งมีหลาย assignments |
| users (officer) → assignments | 1:N | เจ้าหน้าที่หนึ่งคนรับหลายงาน |
| assignments → field_reports | 1:1 | แต่ละ assignment มีรายงาน 1 รายงาน |
| field_reports → report_images | 1:N | รายงานหนึ่งมีหลายรูปภาพ |
| users (admin) → field_reports (approved_by) | 1:N | Admin อนุมัติหลายรายงาน |

## 📦 Indexes Strategy (For 100,000+ Records)

### Primary Indexes
- `users`: username, role, status
- `cases`: debtor_name, citizen_id, contract_no, black_case, red_case, filing_date
- `assignments`: case_id, officer_id, assigned_date, status
- `activity_logs`: user_id, action_type, module, reference_id, created_at
- `login_logs`: user_id, login_time, status

### Composite Indexes
- `activity_logs`: (module, reference_id) - สำหรับดึง audit trail ของแต่ละ record
- `assignments`: (officer_id, status) - สำหรับดูงานของเจ้าหน้าที่แต่ละคน

### Full-Text Search
- `cases`: FULLTEXT(debtor_name, contract_no, black_case, red_case) - สำหรับค้นหาแบบ full-text

## 🗄️ Data Volume Planning

| Table | Expected Records | Growth Rate |
|-------|-----------------|-------------|
| users | 100-500 | Low |
| cases | 100,000+ | Medium-High |
| assignments | 200,000+ | Medium-High |
| field_reports | 150,000+ | Medium |
| report_images | 500,000+ | High |
| activity_logs | 1,000,000+ | Very High |
| login_logs | 50,000+ | Medium |

## 🔧 Performance Optimization

### 1. Partitioning Strategy (Future)
- `activity_logs`: Partition by created_at (monthly)
- `login_logs`: Partition by login_time (monthly)

### 2. Archiving Strategy
- Archive activity_logs older than 2 years
- Archive login_logs older than 1 year
- Keep all cases and field_reports (legal requirement)

### 3. Query Optimization
- Use pagination with LIMIT/OFFSET
- Avoid SELECT * - specify columns
- Use prepared statements (PDO)
- Use transactions for multi-table operations

## 🛡️ Security Measures

### Database Level
- Foreign keys with appropriate ON DELETE actions
- Indexes on sensitive lookup fields
- JSON columns for flexible audit data
- Password hashing (never store plain text)

### Application Level
- PDO prepared statements (prevent SQL injection)
- CSRF tokens on all forms
- Role-based access control (RBAC)
- Session management with timeout
- Input validation and sanitization

## 📈 Scalability Considerations

### Current Design Supports:
- ✅ 100,000+ cases
- ✅ Multiple concurrent users
- ✅ Complex reporting queries
- ✅ Full audit trail
- ✅ Large file uploads (images)

### Future Enhancements:
- 🔄 Read replicas for reporting
- 🔄 Caching layer (Redis)
- 🔄 API endpoints (REST/GraphQL)
- 🔄 Mobile app integration
- 🔄 Elasticsearch for advanced search
- 🔄 Message queue for async tasks

## 🎯 View Usage

### vw_assignment_details
Combines data from assignments, cases, users, and field_reports for easy reporting.

**Use cases:**
- Officer workload report
- Case status dashboard
- Export to Excel/Word

### Stored Procedures

1. `sp_get_user_stats()` - User role distribution
2. `sp_get_case_stats()` - Case status summary
3. `sp_get_officer_workload()` - Officer assignment count

## 🔄 Data Flow

```
1. Admin creates CASE
   ↓
2. Admin assigns CASE to OFFICER → creates ASSIGNMENT
   ↓
3. Officer views assigned work
   ↓
4. Officer clicks "เริ่มงาน" → updates work_date
   ↓
5. Officer fills FIELD_REPORT
   ↓
6. Officer uploads REPORT_IMAGES
   ↓
7. Officer captures GPS location
   ↓
8. Officer submits report
   ↓
9. Admin reviews and APPROVES
   ↓
10. Status updated to "completed"
    ↓
11. All actions logged in ACTIVITY_LOGS
```

## 🚀 Best Practices

1. **Always use transactions** for multi-table operations
2. **Log everything** - audit trail is critical
3. **Index wisely** - balance between read and write performance
4. **Use prepared statements** - security first
5. **Regular backups** - legal data must be preserved
6. **Monitor slow queries** - optimize as needed
7. **Archive old data** - keep database lean
