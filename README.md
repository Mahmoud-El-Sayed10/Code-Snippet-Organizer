## ✨ Features

- **Secure Authentication**: User accounts with JWT-based security
- **Snippet Management**: Create, edit, view, and delete your code snippets
- **Language Support**: Organize snippets by programming language
- **Tagging System**: Add custom tags to quickly categorize and find snippets
- **Search Functionality**: Find snippets by content, title, or description
- **Favorites**: Mark your most used snippets for quick access
- **Clean UI**: Modern, responsive interface built with React
- **Syntax Highlighting**: Code display with proper syntax highlighting

## 🛠️ Tech Stack

### Backend
- Laravel 10
- JWT Authentication
- MySQL/SQLite database
- RESTful API architecture

### Frontend
- React 18 (Vite)
- React Router for navigation
- Context API for state management
- Axios for API requests
- CSS with custom variables for theming

## 📦 Installation

### Prerequisites
- PHP 8.1+
- Composer
- Node.js & npm
- MySQL or SQLite

### Backend Setup

1. Clone this repository
```
git clone https://github.com/yourusername/codevault.git
cd codevault
```

2. Install PHP dependencies
```
cd backend
composer install
```

3. Configure environment variables
```
cp .env.example .env
# Edit .env with your database configuration
```

4. Generate application key and JWT secret
```bash
php artisan key:generate
php artisan jwt:secret
```

5. Run migrations and seed the database
```bash
php artisan migrate --seed
```

6. Start the backend server
```bash
php artisan serve
```

### Frontend Setup

1. Navigate to frontend directory
```
cd ../frontend
```

2. Install dependencies
```
npm install
```

3. Configure API endpoint
```
# Edit api.jsx to point to your backend URL if needed
```

4. Start the dev server
```
npm run dev
```

## 🧪 Demo Login

You can access the demo with these credentials:

- **Regular User**: 
  - Email: user@example.com 
  - Password: password
