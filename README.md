# SewerBot PHP Web Application

Converted from the Kotlin Android MVP project to PHP 8.1+ using the same
architecture (Model-View-Presenter) and the same Firebase back-end.

## Requirements

- PHP 8.1 or higher
- Composer
- Firebase service account JSON file

## Installation

1. Install dependencies:

   ```bash
   cd sewerbot-php
   composer install
   ```

2. Download your Firebase service-account JSON key:

   - Go to Firebase Console → Project Settings → Service Accounts
   - Click "Generate New Private Key"
   - Save the file as `config/firebase-credentials.json`

3. Start the PHP built-in server (for development):

   ```bash
   php -S localhost:8080 -t public
   ```

4. Open `http://localhost:8080` in your browser.

## Project Structure

```
sewerbot-php/
├── composer.json
├── config/
│   └── firebase-credentials.json   ← you provide this
├── public/                          ← web root (point your server here)
│   ├── index.php                    ← landing page (IndexFragment)
│   ├── login.php                    ← login page (LoginFragment)
│   ├── register.php                 ← registration page (RegisterFragment)
│   ├── forgot.php                   ← forgot password (ForgotPassFragment)
│   ├── dashboard.php                ← dashboard (DashboardFragment)
│   ├── controller.php               ← robot controller (ControllerFragment)
│   ├── change_password.php          ← change password (ChangePassFragment)
│   └── api_get_led_status.php       ← AJAX endpoint for live polling
└── src/
    ├── base/
    │   └── BaseContract.php         ← BaseContract.kt
    ├── model/
    │   ├── SewerReport.php           ← SewerReport.kt
    │   ├── User.php                  ← User.kt
    │   ├── LedData.php               ← LedData (in LedRepository.kt)
    │   ├── FirebaseService.php       ← centralised Firebase factory
    │   ├── UserRepository.php         ← UserRepository.kt
    │   ├── ReportRepository.php      ← ReportRepository.kt
    │   ├── RealtimeReportRepository.php ← RealtimeReportRepository.kt
    │   └── LedRepository.php         ← LedRepository.kt
    └── ui/
        ├── login/
        │   ├── LoginContract.php     ← LoginContract.kt
        │   └── LoginPresenter.php    ← LoginPresenter.kt
        ├── register/
        │   ├── RegisterContract.php  ← RegisterContract.kt
        │   └── RegisterPresenter.php ← RegisterPresenter.kt
        ├── forgot/
        │   ├── ForgotPassContract.php ← ForgotPassContract.kt
        │   └── ForgotPassPresenter.php ← ForgotPassPresenter.kt
        ├── change/
        │   ├── ChangePassContract.php ← ChangePassContract.kt
        │   └── ChangePassPresenter.php ← ChangePassPresenter.kt
        ├── dashboard/
        │   ├── DashboardContract.php ← DashboardContract.kt
        │   └── DashboardPresenter.php ← DashboardPresenter.kt
        ├── controller/
        │   ├── ControllerContract.php ← ControllerContract.kt
        │   └── ControllerPresenter.php ← ControllerPresenter.kt
        └── index/
            ├── IndexContract.php     ← IndexContract.kt
            └── IndexPresenter.php    ← IndexPresenter.kt
```

## Kotlin → PHP Mapping

| Kotlin                    | PHP                          |
|--------------------------|------------------------------|
| `interface`              | `interface`                  |
| `data class`             | plain class with constructor  |
| `object` (singleton)    | `static` methods on a class   |
| `Fragment` (View)       | `.php` page file             |
| `Presenter`              | `*Presenter.php` class        |
| `Contract` interface     | `*Contract.php` interfaces    |
| Firebase Auth SDK        | kreait/firebase-php Auth      |
| Firebase RTDB SDK        | kreait/firebase-php Database  |
| Firebase Firestore SDK   | kreait/firebase-php Firestore |
| `ValueEventListener`     | snapshot read (polling via AJAX) |
| `Handler` watchdog        | `setInterval` AJAX polling    |

## Key Differences from Kotlin

1. **Real-time updates**: The Kotlin app used Firebase `ValueEventListener`
   and a `Handler` watchdog for live updates. PHP is request-based, so the
   dashboard and controller pages poll an AJAX endpoint every 2–3 seconds
   using JavaScript `setInterval` + `fetch()`.

2. **Navigation**: Android Navigation Component graph navigation is replaced
   by plain HTTP redirects (`header('Location: ...')`).

3. **Sessions**: Firebase Auth state is stored in PHP `$_SESSION` instead of
   the Firebase Auth SDK's internal state.

4. **UI**: Android XML layouts + ViewBinding are replaced by inline HTML/CSS
   in each `.php` page file.
