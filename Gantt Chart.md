

| ID | Task/Feature | Week |  |  |  |  |
| ----- | :---: | :---: | :---: | :---: | :---: | :---: |
|  |  | **Wk 13** | **Wk 14** | **Wk 15** | **Wk 16** | **Wk 17** |
|           SETUP & ARCHITECTURE |  |  |  |  |  |  |
| \- | Repo, branch model, .env setup | **All** |  |  |  |  |
| \- | DB schema & all migrations | **Rolf** | **Rolf** |  |  |  |
|  | Multi-guard auth config | **Rolf** |  |  |  |  |
| \- | Initiate API routes in routes/api.php | **Rolf** |  |  |  |  |
|            AUTHENTICATION  |  |  |  |  |  |  |
| SID\_01-03 | User registration, login, logout | **Rolf** | **Rolf** |  |  |  |
| SID\_04 | Vendor registration endpoint |  | **Rolf** |  |  |  |
| \- | Auth pages UI (login, register) | **Norman** | **Norman** |  |  |  |
|           HOME PAGE |  |  |  |  |  |  |
| SID\_05 API | api/vendors/search endpoint |  | **Rolf** |  |  |  |
| SID\_05 JS | Hero search bar — fetch() logic |  | **Gian** |  |  |  |
| SID\_06 | Category tiles — Blade \+ Tailwind |  | **Norman** |  |  |  |
| SID\_07 API | Trending spots ranking endpoint |  | **Rold** |  |  |  |
| SID\_07 JS | Trending spots — fetch() \+ render |  | **Gian** | **Gian** |  |  |
|             EXPLORE PAGE |  |  |  |  |  |  |
| SID\_08 API | Search & filter API endpoint |  | **Rolf** | **Rolf** |  |  |
| SID\_08 JS | Search & filter — fetch() \+ render |  |  | **Gian** |  |  |
| SID\_09 | Map/list toggle — Blade \+ JS |  |  | **Norman** |  |  |
| SID\_10 JS | Filter panel — fetch() logic |  |  | **Gian** |  |  |
| SID\_11 | Google Maps JS embed |  |  | **Norman** | **Norman** |  |
|            ESTABLISHMENT DETAIL |  |  |  |  |  |  |
| SID\_12 | Detail page — Blade template |  | **Norman** | **Norman** |  |  |
| SID\_13 API | Menu items endpoint |  |  | **Rolf** |  |  |
| SID\_13 JS | Menu highlights — fetch() \+ render |  |  | **Gian** |  |  |
| SID\_14 |  Get Directions button |  |  | **Norman** |  |  |
| SID\_15 API | Bookmarks API (POST / DELETE) |  |  | **Rolf** |  |  |
| SID\_15 JS | Bookmark toggle — fetch() logic |  |  | **Gian** | **Gian** |  |
| SID\_16 | Share link button |  |  | **Gian** |  |  |
|           REVIEW SYSTEM |  |  |  |  |  |  |
| SID\_17 API | POST /api/reviews |  |  | **Rolf** | **Rolf** |  |
| SID\_17 JS | Submit review — fetch() \+ form |  |  | **Gian** | **Gian** |  |
| SID\_18 API | PUT / DELETE /api/reviews/{id} |  |  |  | **Rolf** |  |
| SID\_18 JS | Edit / delete review — fetch() |  |  |  | **Gian** |  |
| SID\_19 JS | Reviews list — fetch() \+ render |  |  | **Gian** | **Gian** |  |
| SID\_20 | Aggregate rating — server-side |  |  | **Rolf** |  |  |
| \-  | Review section UI |  |  | **Norman** | **Norman** |  |
|         USER PROFILES |  |  |  |  |  |  |
| SID\_21 | Profile page — Blade template |  |  | **Norman** | **Norman** |  |
| SID\_22 API | GET /api/users/{id}/bookmarks |  |  |  | **Rolf** |  |
| SID\_22 JS | Saved places tab — fetch() \+ render |  |  |  | **Gian** |  |
| SID\_23 JS | My reviews tab — fetch() \+ edit/delete |  |  |  | **Gian** |  |
| SID\_24 API | PUT /api/users/{id} |  |  |  | **Rolf** |  |
| SID\_24 JS | Edit profile — fetch() \+ form |  |  |  | **Gian** |  |
| \- | Edit profile page — Blade template |  |  |  | **Norman** |  |
|           VENDOR DASHBOARD |  |  |  |  |  |  |
| SID\_25 API | GET /api/vendor/metrics |  |  | **Rolf** | **Rolf** |  |
| SID\_25 JS | KPI dashboard — fetch() \+ render |  |  |  | **Gian** |  |
| SID\_26 API | POST /api/vendor/reviews/{id}/reply |  |  |  | **Rolf** |  |
| SID\_26 JS | Review reply — fetch() \+ inline form |  |  |  | **Gian** |  |
| SID\_27 API | Menu CRUD endpoints |  |  |  | **Rolf** | **Rolf** |
| SID\_27 JS | Menu CRUD — fetch() \+ form validation |  |  |  | **Gian** | **Gian** |
| SID\_28 API | POST /api/vendor/photos |  |  |  | **Rolf** |  |
| SID\_28 JS | Photo upload — fetch() \+ preview |  |  |  | **Gian** |  |
| SID\_29 API | POST /api/vendor/promotions |  |  |  |  | **Rolf** |
| SID\_29 JS | Promotions form — fetch() \+ validation |  |  |  |  | **Gian** |
| SID\_30 API | PUT /api/vendor/profile |  |  |  | **Rolf** | **Rolf** |
| SID\_30 JS | Vendor settings form — fetch() |  |  |  |  | **Gian** |
| \- | Vendor dashboard Blade templates |  |  | **Norman** | **Norman** |  |
|        ADMIN PANEL |  |  |  |  |  |  |
| SID\_31 API | Vendor approval endpoints |  |  |  | **Rolf** | **Rolf** |
| SID\_31 JS | Approval UI — fetch() actions |  |  |  |  | **Gian** |
| SID\_32 API | DELETE /api/admin/reviews/{id} |  |  |  |  | **Rolf** |
| SID\_32 JS | Review moderation — fetch() \+ UI |  |  |  |  | **Gian** |
| SID\_33 API | PATCH /api/admin/users/{id}/status |  |  |  |  | **Rolf** |
| SID\_34 API | Toggle is\_featured on vendors |  |  |  |  | **Rolf** |
| \- | Admin panel Blade templates |  |  |  | **Norman** | **Norman** |
|          INTEGRATION, QA & DEMO PREP |  |  |  |  |  |  |
| – | End-to-end AJAX flow testing |  |  |  | **Gian** | **Gian** |
| – | Security & validation pass |  |  |  |  | **Rolf** |
| – | UI polish & mobile responsiveness |  |  |  |  | **Norman** |
| – | PDF documentation report |  |  |  |  | **All** |

