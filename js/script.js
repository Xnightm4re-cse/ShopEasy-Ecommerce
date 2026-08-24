/* =====================================================================
   ShopEasy - Main JavaScript (vanilla JS, no libraries)
   ---------------------------------------------------------------------
   Handles small interactive features in the browser:
     1. Mobile navigation menu toggle (customer + admin)
     2. Registration form validation
     3. Login form validation
     4. Contact form validation
     5. Checkout form validation
     6. Quantity inputs cannot go above the available stock
   All important checks are ALSO done in PHP on the server, because
   JavaScript can be disabled. This just gives quick browser feedback.
   ===================================================================== */

// Wait until the whole page is loaded before running our code.
document.addEventListener('DOMContentLoaded', function () {

    /* ---------- 1. MOBILE MENU TOGGLES ---------- */

    // Customer navigation bar
    var navToggle = document.getElementById('navToggle');
    var navLinks  = document.getElementById('navLinks');
    if (navToggle && navLinks) {
        navToggle.addEventListener('click', function () {
            navLinks.classList.toggle('open');
        });
    }

    // Admin sidebar
    var adminNavToggle = document.getElementById('adminNavToggle');
    var adminSidebar   = document.getElementById('adminSidebar');
    if (adminNavToggle && adminSidebar) {
        adminNavToggle.addEventListener('click', function () {
            adminSidebar.classList.toggle('open');
        });
    }

    /* ---------- 2. REGISTRATION FORM VALIDATION ---------- */
    var registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function (event) {
            var name     = registerForm.name.value.trim();
            var email    = registerForm.email.value.trim();
            var password = registerForm.password.value;
            var confirm  = registerForm.confirm_password.value;
            var messages = [];

            if (name.length < 2) {
                messages.push('Please enter your full name.');
            }
            if (!isValidEmail(email)) {
                messages.push('Please enter a valid email address.');
            }
            if (password.length < 6) {
                messages.push('Password must be at least 6 characters long.');
            }
            if (password !== confirm) {
                messages.push('Passwords do not match.');
            }

            if (messages.length > 0) {
                event.preventDefault(); // stop the form from submitting
                alert(messages.join('\n'));
            }
        });
    }

    /* ---------- 3. LOGIN FORM VALIDATION ---------- */
    var loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function (event) {
            var email    = loginForm.email.value.trim();
            var password = loginForm.password.value;

            if (!isValidEmail(email) || password === '') {
                event.preventDefault();
                alert('Please enter a valid email and password.');
            }
        });
    }

    /* ---------- 4. CONTACT FORM VALIDATION ---------- */
    var contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function (event) {
            var name    = contactForm.name.value.trim();
            var email   = contactForm.email.value.trim();
            var message = contactForm.message.value.trim();

            if (name === '' || !isValidEmail(email) || message === '') {
                event.preventDefault();
                alert('Please fill in all fields with a valid email.');
            }
        });
    }

    /* ---------- 5. CHECKOUT FORM VALIDATION ---------- */
    var checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (event) {
            var phone   = checkoutForm.phone.value.trim();
            var address = checkoutForm.address.value.trim();

            if (phone === '' || address === '') {
                event.preventDefault();
                alert('Please enter your phone number and delivery address.');
            }
        });
    }

    /* ---------- 6. QUANTITY CANNOT EXCEED STOCK ---------- */
    // Any number input with data-max-stock is limited to that value.
    var qtyInputs = document.querySelectorAll('input[data-max-stock]');
    qtyInputs.forEach(function (input) {
        input.addEventListener('change', function () {
            var max = parseInt(input.getAttribute('data-max-stock'), 10);
            var val = parseInt(input.value, 10);

            if (isNaN(val) || val < 1) {
                input.value = 1;
            } else if (val > max) {
                input.value = max;
                alert('Only ' + max + ' item(s) are available in stock.');
            }
        });
    });

});

/* Small helper: checks a basic email pattern. */
function isValidEmail(email) {
    var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(email);
}
