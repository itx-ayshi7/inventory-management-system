# 🚀 Deployment Documentation

## 📦 Project: Inventory Management System
*Team Members:*
- Ayesha Eman
- Syeda Nabia Ali
- Saira Sohail

---

## ⚙ Deployment Overview

The deployment process uses *GitHub Actions* for CI and potential FTP-based deployment to a remote server. For local testing, the project is run using *XAMPP* with a MySQL database.

---

## 🛠 Prerequisites

Before deploying, ensure the following:

1. *XAMPP Installed (for local testing)*
   - Apache & MySQL services enabled.
2. *MySQL Database Setup*
   - Database schema must be imported (provided in docs/database_schema.sql).
3. *PHP 8.1+ Installed* (for local or CI compatibility)
4. *Remote Hosting (Optional)* if deploying publicly:
   - FTP/SFTP access
   - cPanel or custom web server setup

---

## 📁 Directory Structure

```bash
.
├── src/                # PHP + HTML + JS source code
├── docs/               # Documentation files (ERD, mockups, etc.)
├── tests/              # Test scripts and results
├── deployment/         # Deployment scripts and documentation
│   └── deploy.yml      # GitHub Actions workflow
│   └── deployment.md
```
## Final Deployement Link
https://itx-ayshi7.github.io/inventory-management-system
