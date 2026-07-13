# Ciclo SGP (Agency Operations & Project Management ERP)
### A production-grade, highly customized Project Management and Agency Resource Planning ERP system designed to streamline creative workflows, budget quoting, and operational governance.

💻 **Live Production URL:** [https://sistemaciclo.com.br](https://sistemaciclo.com.br)

---

## 📐 Architecture & Security Overview
Ciclo SGP is an enterprise-level management platform built to orchestrate project lifecycles within fast-paced creative environments. 

Architected with a robust monolithic paradigm using **Laravel 8**, the application focuses heavily on **operational security**, **granular data isolation**, and automated workflow generation. The platform handles end-to-end agency lifecycles—from initial client onboarding and brief generation to financial quoting and project asset delivery.

---

## 🚀 Key Engineering & Security Patterns

- **Custom RBAC (Role-Based Access Control) Engine:** Engineered a centralized authorization middleware (`authWeb`) integrated with custom security mapping handlers (`App\Helpers\Permissions`). Every system trajectory enforces automated route-name permission structural checks before execution.
- **Obfuscated Relational Pointers (`codedId`):** Implemented security data parameters by masking and encrypting internal record database primary keys (`{codedId}`) across all client, job, and financial public-facing URLs, effectively mitigating Insecure Direct Object Reference (IDOR) vulnerabilities.
- **Project Asset & Document Management System:** Programmed an asynchronous multi-part upload pipeline (`JobFile` engine) powered by `intervention/image` and `symfony/filesystem` for secure multimedia brief attachments and image mutation.
- **Financial Compilation & Dynamic PDF Generation:** Developed a tight, compliance-ready quotation module allowing dynamic field mapping from projects into financial quotes. Used `tecnickcom/tcpdf` to handle transactional data printing and compilation into professional, exportable financial contract PDFs.
- **Data Ingestion Reliability:** Standardized complex UI reporting structures utilizing data table layout providers (`okipa/laravel-table`) to manage high-volume paginations and records rendering efficiently.

---

## 🛠️ Production Tech Stack & Core Tooling

- **Core Engine & Framework:** PHP (^7.3 | ^8.0) & Laravel Framework (^8.75)
- **Security & Middleware:** Route-Based RBAC Permissions, Data Masking Pointers (`codedId`)
- **Document Rendering:** TCPDF Core, Symfony Filesystem Architectures
- **Media Optimization:** Intervention Image Processing Pipeline
- **Relational Databases:** Layer configurations optimized for relational consistency (MySQL / PostgreSQL)
