# VendorBridge 🌉

VendorBridge is an automated procurement lifecycle management system designed to bridge the gap between RFQ (Request for Quotation) generation and final Invoicing. By enforcing relational integrity, the system ensures a seamless audit trail for all procurement activities.

## 🚀 Key Features
- **Automated Workflow:** Seamless transition from RFQ → Quotation → Purchase Order → Invoice.
- **Relational Integrity:** Strictly enforced foreign key constraints ensure that no procurement record is ever "orphaned" or unlinked.
- **Role-Based Access:** Integrated security for Admin, Procurement Officers, Managers, and Vendors.
- **Real-Time Dashboard:** A centralized view of active liabilities, pending approvals, and procurement status.

## 🛠 Tech Stack
- **Backend:** PHP 8.0+
- **Database:** MySQL / MariaDB (via phpMyAdmin)
- **Frontend:** HTML5, CSS3 (Tailwind CSS)
- **Architecture:** Relational Database with strict constraint enforcement.

## 📂 Project Structure
```text
/vendorbridge
├── /assets           # CSS, JS, and Images
├── /config           # Database connection (db.php)
├── /sql              # Database schema and initial data (schema.sql)
├── /includes         # Reusable components (header, footer, nav)
├── /modules          # Core logic (auth, pos, invoices, rfq)
└── README.md         # Project Documentation
