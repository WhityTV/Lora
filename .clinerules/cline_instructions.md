# Project Guide: CONTINUE.md (E-Commerce Platform)

## 📜 Project Language Rules

"Language Rule: You must write your entire response in German. Never switch languages mid-response. Exception: Only reply in English if the user's prompt is written in English. Keep explanations short."

## 🚀 Project Context & Overview (2026 Standards Assumption)
This platform is a modern, feature-rich **E-commerce Platform**. The core functionality revolves around managing and selling goods and services, utilizing an integrated system for user accounts, product catalogs, and content marketing via "Articles."

**Key Technology Assumptions:**
*   **Backend:** Modern PHP framework (Laravel/Symfony structure assumed due to '2026' context).
*   **Architecture Style:** Modular, but requires careful separation of concerns due to legacy procedural patterns.

**❗ Critical Advisory: Contextual Shift Implemented ❗**
The directory `cor/art` must now be treated as the **Article Management System**, not an art gallery. Any script referencing "artwork" must be understood to mean "Published Articles."

## 🧩 Directory Map & Module Functionality
This map is based on directory listings and contextual updates:

| Directory | Purpose (E-commerce Focus) | Key Files/Usage Notes |
| :--- | :--- | :--- |
| **`cor/inc/`** | Includes reusable header, footer, navigation elements. | `header.php`: Review this first for template structure decisions. |
| **`cor/reg/`** | User Account Registration flow. | Used upon new user sign-up. |
| **`cor/log/`** | User Authentication (Login, Logout). | Session management is critical here. |
| **`cor/set/`** | User Profile Settings Management. | Handles personalized user data updates. |
| **`cor/art/`** | **ARTICLE MANAGEMENT MODULE**. Manages article creation, listings, and viewing. | `create.php`: Article content submission (CMS). `last_art.php`: Featured/latest articles feed. |
| **`cor/art/ord/`** | Order Processing & Summaries. | Remains tied to checkout flow (Shopping Cart). |
| **`cor/ser/`** | Site-wide Search Utility. | Must search across Articles, Products, and Users. |

## 🛠️ Development Workflow: Recommendations for Maintenance (jQuery Context)
Since the project is intentionally built using jQuery and older structures for educational purposes, **we will not perform major framework overhauls.** Our focus must be on safely enhancing functionality within the existing paradigm.
1.  **Security First:** Security remains paramount. Verify all database interactions use parameterized queries, even when updating old code blocks.
2.  **jQuery Best Practices:** When adding new AJAX calls or DOM manipulations:
    *   Ensure proper event handling using delegated events (`$(document).on('click', '#selector', function() { ... });`) to support dynamic content loading within the jQuery structure.
    *   Avoid global scope pollution; wrap functionality in local functions or module patterns where possible, even if the core is procedural.
3.  **Systematic Enhancement:** Approach changes module-by-module (e.g., fixing a bug in login, then improving article submission) to ensure that one change doesn't inadvertently break another part of the established system flow.
## 🚨 Immediate Next Step
Now that we understand the constraints are educational—focusing on **maintenance and gradual enhancement** rather than modernization—please tell me which specific area or file you want to tackle next:
1.  A bug fix in the **Article Submission process** (`cor/art/create.php`)?
2.  Improving how search results link to **Articles** from `cor/ser/search.php`?
3.  Reviewing the **Checkout Workflow** (linking cart to article views)?