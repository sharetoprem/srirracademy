// ===================================
// Sri RR Academy - Main Script
// ===================================

// Initialize AOS Animation (only if AOS is loaded)
if (window.AOS) {
    AOS.init({
        duration: 800,
        once: true
    });
}

// Popup Form Functions
function openPopup() {
    const popup = document.getElementById('popupForm');
    if (!popup) return;
    popup.classList.add('active');
    document.body.style.overflow = 'hidden';
    document.body.style.overflowX = 'hidden'; // Prevent horizontal scroll
}

function closePopup() {
    const popup = document.getElementById('popupForm');
    if (!popup) return;
    popup.classList.remove('active');
    document.body.style.overflow = '';
    document.body.style.overflowX = '';
}

// Expose popup helpers for inline HTML usage
window.openPopup = openPopup;
window.closePopup = closePopup;

// Popup init (only on pages that have popup)
function initPopup() {
    const popup = document.getElementById('popupForm');
    if (!popup) {
        // Retry after a short delay if popup not found
        setTimeout(initPopup, 100);
        return;
    }

    // Close popup when clicking outside
    popup.addEventListener('click', function (e) {
        if (e.target === popup) {
            closePopup();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closePopup();
    });

    // Close button click handler
    const closeBtn = popup.querySelector('.popup-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', closePopup);
    }

    // Show popup after 5 seconds
    setTimeout(function () {
        openPopup();
    }, 1000); // Changed to 1 second for testing
}

// Initialize popup with retry mechanism
setTimeout(initPopup, 100);

// Utility: Set alert status
function setStatus(elementId, type, message) {
    const el = document.getElementById(elementId);
    if (!el) return;
    el.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
    el.classList.add('alert-' + type);
    el.textContent = message;
}

// Popup Form Handler
(function initPopupForm() {
    const form = document.getElementById('enquiryForm');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (!form.checkValidity()) {
            setStatus('popupStatus', 'warning', 'Please fill all required fields correctly.');
            return;
        }

        const btn = form.querySelector('button[type="submit"]');
        const originalHtml = btn ? btn.innerHTML : '';

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
        }

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'Accept': 'application/json' }
            });

            if (res.status === 405) {
                setStatus('popupStatus', 'danger', 'Server error: PHP not enabled. Please use a PHP-enabled server.');
                return;
            }

            const data = await res.json().catch(() => null);

            if (res.ok && data && data.ok) {
                setStatus('popupStatus', 'success', data.message);
                form.reset();
                setTimeout(() => closePopup(), 3000);
            } else {
                setStatus('popupStatus', 'danger', data?.message || 'Submission failed. Please try again or call us.');
            }
        } catch (err) {
            setStatus('popupStatus', 'danger', 'Network error. Please try again later or call us directly.');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }
    });
})();

// Contact Form Handler
(function initContactForm() {
    const form = document.getElementById('contactForm');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (!form.checkValidity()) {
            setStatus('contactStatus', 'warning', 'Please fill all required fields correctly.');
            return;
        }

        const btn = form.querySelector('button[type="submit"]');
        const originalHtml = btn ? btn.innerHTML : '';

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
        }

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'Accept': 'application/json' }
            });

            if (res.status === 405) {
                setStatus('contactStatus', 'danger', 'Server error: PHP not enabled. Please use a PHP-enabled server.');
                return;
            }

            const data = await res.json().catch(() => null);

            if (res.ok && data && data.ok) {
                setStatus('contactStatus', 'success', data.message);
                form.reset();
            } else {
                setStatus('contactStatus', 'danger', data?.message || 'Failed to send message. Please try again.');
            }
        } catch (err) {
            setStatus('contactStatus', 'danger', 'Network error. Please try again later or call us directly.');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }
    });
})();

// Admission Form Handler
(function initAdmissionForm() {
    const form = document.getElementById('admissionForm');
    const statusEl = document.getElementById('admissionStatus');

    if (!form || !statusEl) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (!form.checkValidity()) {
            setStatus('admissionStatus', 'warning', 'Please fill all required fields correctly.');
            return;
        }

        const btn = form.querySelector('button[type="submit"]');
        const originalHtml = btn ? btn.innerHTML : '';

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
        }

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'Accept': 'application/json' }
            });

            if (res.status === 405) {
                setStatus('admissionStatus', 'danger', 'Server error: PHP not enabled. Please use a PHP-enabled server (XAMPP/WAMP). VS Code Live Server cannot run PHP.');
                return;
            }

            const data = await res.json().catch(() => null);

            if (res.ok && data && data.ok) {
                setStatus('admissionStatus', 'success', data.message);
                form.reset();
                statusEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                setStatus('admissionStatus', 'danger', data?.message || 'Submission failed. Please try again or call us.');
            }
        } catch (err) {
            setStatus('admissionStatus', 'danger', 'Network error. Please try again later or call us directly.');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }
    });
})();

// Normalize Admission Form links across the site
(function normalizeAdmissionLinks() {
    const admissionPage = 'admission.html';
    const selectors = [
        'a[href="#admission"]',
        'a[href="index.html#admission"]',
        'a[href="./index.html#admission"]',
        'a[href="/index.html#admission"]'
    ];
    document.querySelectorAll(selectors.join(',')).forEach(a => {
        a.setAttribute('href', admissionPage);
    });
})();

// Smooth Scroll (same-page # links)
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (!href || href === '#') return;

        const target = document.querySelector(href);
        if (!target) return;

        e.preventDefault();
        target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    });
});

// Navbar Scroll Effect
window.addEventListener('scroll', function () {
    const header = document.querySelector('.main-header');
    if (!header) return;

    if (window.scrollY > 100) {
        header.style.boxShadow = '0 4px 20px rgba(0,0,0,0.1)';
    } else {
        header.style.boxShadow = '0 4px 6px -1px rgba(0,0,0,0.1)';
    }
}, { passive: true });

// Back To Top Button
(function initBackToTop() {
    const btn = document.getElementById('backToTop');
    if (!btn) return;

    window.addEventListener('scroll', function () {
        if (window.pageYOffset > 300) {
            btn.classList.add('show');
        } else {
            btn.classList.remove('show');
        }
    }, { passive: true });

    btn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();