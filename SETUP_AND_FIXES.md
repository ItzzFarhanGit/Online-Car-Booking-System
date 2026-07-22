# Online Car Booking System — Fixes & New Features (Updated Jul 2026)

## How to run this project
1. Copy the project folder into your `htdocs` (XAMPP) or `www` (WAMP) folder — or upload it to
   your real hosting (cPanel, etc.) when you're ready to go live.
2. Open phpMyAdmin, create a database named `car_booking` (or just let `db.php` create it for
   you automatically the first time any page runs — it also creates every table on its own).
3. **Before you do anything else, open `config.php` and fill in:**
   - Your Gmail address + Gmail **App Password** (for sending OTP emails) — instructions are
     written directly inside `config.php`.
   - Your PayHere **Sandbox Merchant ID** + **Merchant Secret** (for real/test payments) —
     instructions are also written directly inside `config.php`.
   Nothing involving email or payments will work until you fill these in.
4. Visit `http://localhost/<your-folder>/home.php` in your browser.
5. **Admin Panel**: go to `admin/login.php` and log in with:
   - Username: `Farhan`
   - Password: `Farhan1234`
   This account is created automatically the first time the database is set up (see `db.php`).
   **Change this password after your first login if the site will be public** — there's currently
   no "change password" page in admin, so the easiest way for now is to open phpMyAdmin, go to
   the `admin` table, and replace the password field with a new bcrypt hash (you can generate one
   with `password_hash('yournewpassword', PASSWORD_DEFAULT)` in a throwaway PHP script).

## New: Email OTP Verification (Signup + Forgot Password)
- **Signup** (`signup.php`) now creates the account as *unverified*, emails a 6-digit code, and
  sends the user to `verify_otp.php`. The account only becomes usable (and the user gets logged
  in) after the correct code is entered. Codes expire after 10 minutes, allow 5 wrong attempts,
  and can be resent (60-second cooldown).
- **Forgot Password** (`forgotpassword.php`) sends the same style of 6-digit code by email.
  `reset.php` will only let someone set a new password if that code was verified in the same
  browser session first — the old "type the email in the URL" method has been removed since it
  wasn't actually secure.
- Emails are sent using **PHPMailer** (bundled in `includes/PHPMailer/`, no Composer needed)
  through Gmail's SMTP server. See `config.php` for the two values you must fill in.
- If the SMTP credentials aren't filled in yet, signup will show a clear error instead of silently
  failing, and won't leave a half-created account behind.

### Troubleshooting: "SMTP Error: Could not authenticate"
This means Gmail rejected the username/password — almost always one of these:
1. **You used your normal Gmail password instead of an App Password.** Gmail requires a special
   16-character "App Password", not your regular login password.
2. **2-Step Verification isn't turned on** for the Google account — App Passwords only appear
   once it's on. Turn it on at https://myaccount.google.com/security first.
3. Generate the App Password at https://myaccount.google.com/apppasswords (choose "Mail"), then
   paste it into `SMTP_PASSWORD` in `config.php`. (Spaces in the code Google shows you are stripped
   automatically by `includes/mailer.php`, so you don't need to remove them yourself.)
4. Make sure `SMTP_USERNAME` is the exact same Gmail address the App Password was generated for.
5. If your host blocks port 587 (some do), change `SMTP_PORT` to `465` and `SMTP_SECURE` to
   `'smtps'` in `config.php` — no other file needs to change.

## New: Real Payment Processing (PayHere, Sandbox)
- `booking.php` now also asks for phone / address / city (required by PayHere) and calculates
  the total price (`number of days × the car's daily rate`).
- After booking, the customer is sent to `payment.php`, which shows an order summary and a
  **Pay with PayHere** button. This submits a properly-signed request to PayHere's Sandbox
  Checkout page — no real money moves in Sandbox mode.
- `payhere_notify.php` is the server-to-server callback PayHere calls once the payment finishes.
  It verifies PayHere's signature before trusting the result, then marks the booking as
  Paid / Failed / Cancelled in the database.
- `bookingsuccess.php` now reflects the *real* payment status from the database (auto-refreshing
  while a payment is still being confirmed), instead of always saying "Booking Successful."
- **Important:** PayHere's server-to-server notification can only reach a publicly hosted domain
  with HTTPS — it will not work on `localhost`/XAMPP. Test the full payment step after uploading
  this project to real hosting. Everything else (booking form, OTP, admin panel) works fine on
  localhost.
- To go live for real (not sandbox): create a real PayHere merchant account, get it approved,
  set `PAYHERE_SANDBOX` to `false` in `config.php`, and use your live Merchant ID/Secret.

## Bugs that were fixed
1. **login.php** — was reading `$row['id']` but the `users` table's primary key is `user_id`.
   This meant `$_SESSION['user_id']` was always empty after logging in, breaking anything
   tied to "the logged-in user" (like My Bookings).
2. **reset.php** — redirected to a non-existent `forgot.php` instead of `forgotpassword.php`
   when no email was supplied in the URL.
3. **reset.php** — the email from the URL was inserted into a SQL query without escaping
   (SQL injection risk), and there was no check that the email actually belonged to a real
   account before allowing the password to be changed. (This whole flow has since been replaced
   by the OTP-verified reset flow described above, which fixes this properly.)
4. **login.php / signup.php** — the username/email/fullname typed by the user were inserted
   into SQL queries without escaping (SQL injection risk). Fixed using `mysqli_real_escape_string`.
5. **cars table was empty** — `caravailable.php` queried the `cars` table for the car listing,
   but no cars had ever been inserted into it, so the dynamic part of the page always showed
   "No cars available." The 4 cars you saw were actually hardcoded HTML duplicated below it.
   The table is now seeded with the 4 cars, and the hardcoded duplicate listing was removed.
6. **booking.php** — the car selection dropdown only had "sedan / suv / luxury" and had no
   connection to the actual `cars` table, so a booking didn't record which real car was booked,
   and clicking "Book Now" on a specific car (`caravailable.php?car_id=2`) did nothing.
   Booking now pulls live cars from the database and pre-selects the chosen car.
7. **contact.php** — the success/error message was being printed twice on the page, and the
   page had no site navigation header like every other page.
8. **db.php on PHP 8.1+** — mysqli throws exceptions on errors by default since PHP 8.1, which
   broke this project's `if (!mysqli_query(...))`-style error checks (they'd never be reached
   because PHP would crash with an uncaught exception first instead). Fixed by explicitly setting
   `mysqli_report(MYSQLI_REPORT_OFF)` at the top of `db.php`.
9. **db.php seeding logic** — the check that auto-inserts the default cars/admin account on a
   brand-new database had a bug (`!empty($row['total'])` is actually `false` when the count is
   `0`), so on a truly empty database the seed data silently never got inserted. Fixed.
10. **admin/db.php** — used to guess between several possible database names instead of sharing
    the same connection/schema logic as the main site, which meant the `admin` table (and the
    default Farhan account) might not exist yet if someone opened the admin panel before ever
    visiting the main site. It now simply reuses the root `db.php`.

## New pages added
- **admin/** folder — a full Admin Panel:
  - `admin/setup_admin.php` — one-time setup to create the first admin login (not needed anymore
    since `Farhan` / `Farhan1234` is created automatically, but kept as a fallback).
  - `admin/login.php` / `admin/logout.php` — admin authentication.
  - `admin/dashboard.php` — quick stats (total cars, bookings, pending bookings, users, messages,
    and total revenue from paid bookings).
  - `admin/cars.php` — add, edit, delete cars (the **cars** table is now fully usable from the UI).
  - `admin/bookings.php` — view all bookings (now including amount + live payment status), change
    booking status (Pending/Confirmed/Completed/Cancelled), delete.
  - `admin/messages.php` — view and delete contact form messages.
- **mybookings.php** — logged-in users can see their own booking history, amount, and payment
  status, with a "Pay Now" link for anything still unpaid.
- **verify_otp.php** — shared OTP entry page used by both signup and forgot-password.
- **payment.php** — order summary + PayHere payment button.
- **payhere_notify.php** — PayHere's server-to-server payment confirmation webhook.
- **paymentcancelled.php** — shown if the customer cancels out of the PayHere payment page.

## Database changes
- `cars` table seeded with the 4 cars already used on the site.
- `bookings` table: added `user_id` (links a booking to the logged-in user), `car_id`
  (links to the actual car booked), `status` (Pending/Confirmed/Completed/Cancelled),
  and now also `phone`, `address`, `city`, `total_amount`, `order_id`, `payment_status`,
  `payment_id` for the payment integration.
- `users` table: added `is_verified` (0/1) for the OTP email-verification flow.
- New `otps` table storing OTP codes (email, code, purpose, expiry, attempt count).
- New `admin` table for the Admin Panel login, auto-seeded with `Farhan` / `Farhan1234`.
- `home`, `login`, `forgot_password` tables were left in place (unused by the PHP code,
  kept only for backward compatibility) — they're harmless leftovers from earlier development.

