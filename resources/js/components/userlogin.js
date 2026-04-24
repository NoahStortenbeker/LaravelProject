import gsap from "gsap";

// -----------------------------
// Password Toggle
// -----------------------------
document.querySelectorAll('.toggle-password').forEach(icon => {
    icon.addEventListener('click', function () {
        const wrapper = this.parentElement;
        const input = wrapper.querySelector('input');
        
        if (input) {
            if (input.type === "password") {
                input.type = "text";
                this.classList.remove('ri-eye-off-line');
                this.classList.add('ri-eye-line');
            } else {
                input.type = "password";
                this.classList.remove('ri-eye-line');
                this.classList.add('ri-eye-off-line');
            }
        }
    });
});

// -----------------------------
// LOGIN FORM ANIMATION
// -----------------------------
const loginForm = document.querySelector('.login_form');
if (loginForm) {
    loginForm.addEventListener('submit', () => {
        const submitBtn = document.querySelector('.submit_btn');
        const imageContainer = document.querySelector('.login_image_container');
        const formElements = [...loginForm.querySelectorAll('h2, .form_group, .input_hint, .login_link, .signup_link')];
        formElements.push(submitBtn);

        gsap.to(formElements, {
            opacity: 0,
            y: -20,
            duration: 0.5,
            stagger: 0.03,
            ease: "power3.in"
        });

        if (imageContainer) {
            gsap.to(imageContainer, {
                opacity: 0,
                x: -20,
                duration: 0.5,
                ease: "power3.in"
            });
        }

        // Form submits normally via Laravel, no JS redirect
    });
}

// -----------------------------
// REGISTER FORM ANIMATION
// -----------------------------
const registerForm = document.querySelector('.register_form');
if (registerForm) {
    registerForm.addEventListener('submit', (e) => {
        const submitBtn = document.querySelector('.submit_btn');
        if (submitBtn) {
            submitBtn.disabled = true;
        }
        const imageContainer = document.querySelector('.register_image_container');
        const formElements = [...registerForm.querySelectorAll('h2, .form_group, .input_hint, .login_link, .signup_link')];
        formElements.push(submitBtn);

        gsap.to(formElements, {
            opacity: 0,
            y: -20,
            duration: 0.5,
            stagger: 0.03,
            ease: "power3.in"
        });

        if (imageContainer) {
            gsap.to(imageContainer, {
                opacity: 0,
                x: -20,
                duration: 0.5,
                ease: "power3.in"
            });
        }

        // Form submits normally via Laravel
    });
}

// -----------------------------
// Input Validation Highlight Clear
// -----------------------------
['name','fullname','email','password','password_confirmation'].forEach(id => {
    const input = document.getElementById(id);
    if (input) {
        input.addEventListener('input', () => {
            input.style.borderColor = '';
            input.style.boxShadow = '';
        });
    }
});

// Username hint visibility (show only when >8 chars)
const usernameInputEl = document.getElementById('username');
const usernameHintEl = usernameInputEl ? usernameInputEl.closest('.form_group')?.querySelector('.input_hint') : null;
if (usernameHintEl && typeof gsap !== 'undefined') {
    gsap.set(usernameHintEl, { autoAlpha: 0 });
}
if (usernameInputEl && usernameHintEl) {
    const updateHint = () => {
        if (usernameInputEl.value.length > 8) {
            if (typeof gsap !== 'undefined') {
                gsap.to(usernameHintEl, { autoAlpha: 1, duration: 0.2 });
            } else {
                usernameHintEl.style.opacity = '1';
                usernameHintEl.style.visibility = 'visible';
            }
        } else {
            if (typeof gsap !== 'undefined') {
                gsap.to(usernameHintEl, { autoAlpha: 0, duration: 0.2 });
            } else {
                usernameHintEl.style.opacity = '0';
                usernameHintEl.style.visibility = 'hidden';
            }
        }
    };
    usernameInputEl.addEventListener('input', updateHint);
    usernameInputEl.addEventListener('blur', updateHint);
    // initialize state
    updateHint();
}
