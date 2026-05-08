<p align="center"><a href="https://bitespot.duckdns.org/" target="_blank"><img src="public/logo.png" width="100" alt="BiteSpot Logo"></a></p>

# BiteSpot: A Hyperlocal Food Discovery Platform

**Live Website:** [https://bitespot.duckdns.org/](https://bitespot.duckdns.org/)

## About BiteSpot
BiteSpot is a web-based food discovery application designed specifically to digitize the local culinary scene in Tacloban City. The platform bridges the digital divide for micro-food businesses—such as street food stalls, night market vendors, and neighborhood eateries—that often lack the technical or financial resources to maintain a presence on major global platforms like Google Maps or GrabFood. By providing a dedicated local space, BiteSpot allows users to find authentic dining experiences while empowering small vendors with digital visibility.

The system serves three primary audiences: everyday diners looking for affordable meals, tourists seeking authentic local culture, and food vendors needing low-barrier digital marketing tools.

### SDG Alignment
BiteSpot is purposefully designed to align with the United Nations Sustainable Development Goals (SDGs), focusing on economic empowerment and community sustainability:
* **SDG 8 (Decent Work and Economic Growth):** The platform provides free digital marketing tools to informal street vendors and MSMEs (Micro, Small, and Medium Enterprises). This helps increase their sales, formalize their digital footprint, and supports sustainable local economic growth.
* **SDG 11 (Sustainable Cities and Communities):** BiteSpot promotes local cultural heritage by highlighting authentic culinary traditions rather than just large commercial chains. This fosters a more inclusive and resilient local food ecosystem.
* **SDG 17 (Partnerships for the Goals):** The platform architecture supports potential co-branding and partnerships with Local Government Units (LGUs) and tourism offices to promote curated food trails.

## Features

### Core User Features
* **Hero Search & Discovery:** A centralized search board on the home page allowing users to find food near them by name, cuisine, or specific cravings.
* **Category Filtering:** Interactive tiles (e.g., Street Food, Cafes, Restaurants) for quick-access browsing.
* **Interactive Map & List View:** Users can toggle between a card-based list and a Google Maps-integrated view showing establishment locations.
* **Establishment Detail Pages:** Comprehensive views including menus, ratings, operating hours, and community reviews.
* **Community Submission:** A feature allowing users to submit "undiscovered" spots to the platform for admin verification, ensuring the directory remains community-driven.
* **Personalized Profiles:** Authenticated users can save favorite establishments and manage their personal review history.

### Core Vendor Features
* **Self-Service Dashboard:** A minimalist portal for business owners to manage their listing status and view basic visibility analytics like total views and average ratings.
* **Menu & Promotion Management:** Tools to upload menu items with photos and prices, and create time-limited discounts to attract customers.
* **Customer Engagement:** Vendors can view and publicly reply to customer reviews to build rapport and address feedback.

## Tech Stack
The backend is built using Laravel 11, exposing several RESTful endpoints to handle data for the various user roles. The system utilizes a relational database (MySQL 8.0) to manage the complex relationships between users, vendors, and discovery content. All database interactions are handled via the Eloquent ORM, ensuring built-in protection against SQL injection through PDO prepared statements.

## Setup and Installation

### Prerequisites
Ensure your local development environment meets the following requirements:
* **PHP:** Version 8.3 or higher
* **Composer:** Dependency manager for PHP
* **Node.js & NPM:** For frontend asset compilation (Vite & TailwindCSS)
* **Database:** MySQL 8.0+ or SQLite (default in local development)

### 1. Installation
You can use the custom setup script defined in the project to streamline the installation process. Run the following command in the root directory:

```bash
composer setup
