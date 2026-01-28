# DevPOS - Point of Sale System

## 🛠️ Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd devPOS
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy the environment file and configure your settings:

```bash
cp .env.example .env
```

Edit `.env` file with your database and application settings:

```env
APP_NAME=DevPOS
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=devpos
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Other configurations...
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Database Setup

Create a database in MySQL and run migrations:

```bash
# Create database in MySQL
# Then run:
php artisan migrate
```

### 7. Seed Database

If you want to populate the database with sample data:

```bash
php artisan db:seed
```

### 8. Build Frontend Assets

```bash
# For development
npm run dev

# For production
npm run build
```

## 🚀 Running the Application

### Development Server

Start the Laravel development server:

```bash
php artisan serve
```

The application will be available at: `http://localhost:8000`

### Using XAMPP

If using XAMPP:

1. Copy the project to `C:\xampp\htdocs\devPOS`
2. Start Apache and MySQL in XAMPP Control Panel
3. Access via: `http://localhost/devPOS/public`

### Using Artisan Serve with Custom Host/Port

```bash
php artisan serve 
```

### Default Login Credentials

After installation, you can log in with:

- **Email**: admin@gmail.com
- **Password**: 123456
