# Bazarnao - E-commerce Platform

A complete solution for E-commerce Business with exclusive features & super responsive layout.

## 🚀 Features

- **Complete E-commerce Solution**
  - Product management with variants
  - Shopping cart and checkout
  - Order management system
  - Customer and seller dashboards
  - Multi-vendor support

- **Accounting System**
  - Chart of Accounts
  - General Ledger
  - Trial Balance
  - Balance Sheet
  - Income Statement
  - Bank Reconciliation
  - Cash Book & Bank Book
  - Day Book
  - Receipt & Payment Reports

- **Point of Sale (POS)**
  - Barcode generation and scanning
  - Offline order management
  - Invoice generation

- **Payment Integration**
  - Multiple payment gateways (Bkash, Nagad, SSLCommerz, Stripe, PayPal, etc.)
  - Wallet system
  - Cash on Delivery

- **Additional Features**
  - Affiliate system with referral codes
  - OTP verification system
  - Multi-language support (RTL support)
  - Refund request management
  - Club points system
  - Product offers and deals
  - Mobile app integration

## 📋 Requirements

- PHP >= 8.2.0
- Composer
- MySQL/MariaDB
- Node.js & NPM (for frontend assets)

## 🛠️ Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/mesbah-meju/bazarnao.git
   cd bazarnao
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   - Update `.env` file with your database credentials
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Build assets**
   ```bash
   npm run dev
   # or for production
   npm run build
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

## 📁 Project Structure

```
bazarnao/
├── app/
│   ├── Exports/          # Excel export classes
│   ├── Http/
│   │   ├── Controllers/  # Application controllers
│   │   └── Resources/    # API resources
│   ├── Models/           # Eloquent models
│   └── Services/         # Business logic services
├── config/               # Configuration files
├── database/
│   ├── migrations/       # Database migrations
│   └── seeders/          # Database seeders
├── public/               # Public assets
├── resources/
│   ├── js/               # JavaScript files
│   ├── sass/             # Stylesheets
│   └── views/            # Blade templates
└── routes/               # Route definitions
```

## 🔧 Configuration

### Payment Gateways
Configure payment gateways in `config/` directory:
- `flutterwave.php`
- `nagad.php`
- `rave.php`

### Storage
Configure storage in `config/filesystems.php` for:
- Local storage
- AWS S3 (optional)

## 📱 Mobile App

The platform supports a mobile app. Referral codes and app integration are available.

## 🌐 Multi-language Support

The platform supports multiple languages with RTL (Right-to-Left) support for languages like Arabic, Bengali, etc.

## 📊 Reports & Exports

The system includes comprehensive reporting:
- Financial reports (Balance Sheet, Income Statement, etc.)
- Sales reports
- Product reports
- Excel export functionality

## 🔐 Security

- Laravel Sanctum for API authentication
- Spatie Laravel Permission for role-based access control
- CSRF protection
- SQL injection prevention (Eloquent ORM)

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📝 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 👨‍💻 Developed By

**4axiz IT Ltd**
- Website: [https://fouraxiz.com](https://fouraxiz.com)

👨‍💻 Backend Developer

**Mesbah Uddin Meju**

📧 Email: uddin.mesbaah@gmail.com  
🌐 Website: mesbahuddin.info  
💼 LinkedIn: mesbah-uddin-meju  
🐙 GitHub: mesbah-meju

## 📞 Support

For support, please open an issue in the repository.

---

**Note**: Make sure to configure all environment variables in `.env` file before running the application.
