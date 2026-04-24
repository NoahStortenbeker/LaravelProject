import gsap from "gsap";


document.addEventListener("DOMContentLoaded", () => {
  // 1. Setup Container and Bars (Stairs)
  const numberOfBars = 5;
  
  const transitionContainer = document.createElement("div");
  transitionContainer.style.position = "fixed";
  transitionContainer.style.top = "0";
  transitionContainer.style.left = "0";
  transitionContainer.style.width = "100vw";
  transitionContainer.style.height = "100vh";
  transitionContainer.style.zIndex = "10000";
  transitionContainer.style.pointerEvents = "none"; // Allow clicks through when hidden
  transitionContainer.style.display = "flex";
  
  document.body.appendChild(transitionContainer);

  for (let i = 0; i < numberOfBars; i++) {
    const bar = document.createElement("div");
    bar.classList.add("transition-bar");
    bar.style.width = `${100 / numberOfBars}%`;
    bar.style.height = "100%";
    bar.style.backgroundColor = "#000000ff";
    bar.style.transform = "translateY(0)"; // Start covering
    transitionContainer.appendChild(bar);
  }

  // 2. Make body visible immediately
  gsap.set("body", { opacity: 1, visibility: "visible" });

  // --- Element Specific Animations (Login/Register) ---
  const isLoginOrRegister = document.querySelector('.login_wrapper, .register_wrapper');
  let formElements = [];
  let imageContainer = null;

  if (isLoginOrRegister) {
      // Select elements to animate
      // We want: h2, labels, inputs, image container
      const h2 = document.querySelector('h2');
      const formGroups = document.querySelectorAll('.form_group');
      const submitBtn = document.querySelector('.submit_btn');
      const links = document.querySelectorAll('.login_link, .signup_link');
      
      // Collect form elements in order
      if(h2) formElements.push(h2);
      formGroups.forEach(group => {
          const label = group.querySelector('label');
          const input = group.querySelector('input, .password_input_wrapper');
          if(label) formElements.push(label);
          if(input) formElements.push(input);
          // hint is handled separately by userlogin.js and should not auto-show on enter
      });
      if(submitBtn) formElements.push(submitBtn);
      links.forEach(link => formElements.push(link));

      imageContainer = document.querySelector('.login_image_container, .register_image_container');

      // Initial State: Hidden and slightly shifted
      if (formElements.length > 0) {
          gsap.set(formElements, { opacity: 0, y: 20 });
      }
      if (imageContainer) {
          gsap.set(imageContainer, { opacity: 0, x: 20 });
      }
  }

  // 3. Enter Animation: Slide bars UP to reveal content
  // Stairs effect: Staggered delay
  const skipEnter = !!document.getElementById('statusUpdatedFlag');
  if (skipEnter) {
    gsap.set(".transition-bar", { y: "-100%" });
  } else {
    gsap.to(".transition-bar", {
      y: "-100%",
      duration: 1.0,
      stagger: 0.1,
      ease: "power3.inOut",
      onComplete: () => {
         // Keep container but ensure it doesn't block interactions
      }
    });
  }

  // Animate Elements IN (after bars start moving)
  if (isLoginOrRegister) {
      if (formElements.length > 0) {
          gsap.to(formElements, {
              opacity: 1,
              y: 0,
              duration: 0.8,
              stagger: 0.05, // Fast stagger for typing-like effect
              ease: "power3.out",
              delay: 0.5 // Wait for stairs to clear a bit
          });
      }
      if (imageContainer) {
          gsap.to(imageContainer, {
              opacity: 1,
              x: 0,
              duration: 1.0,
              ease: "power3.out",
              delay: 0.6
          });
      }
  }

  // 4. Handle Navigation Links
  const navLinks = document.querySelectorAll("a.transition-link");

  navLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      const href = link.getAttribute("href");

      // Reset bars for exit animation
      transitionContainer.style.pointerEvents = "auto"; // Block clicks during transition

      // Animate Elements OUT (Exit Animation)
      if (isLoginOrRegister) {
          if (formElements.length > 0) {
              gsap.to(formElements, {
                  opacity: 0,
                  y: -20, // Move up slightly while fading out
                  duration: 0.5,
                  stagger: 0.03, // Faster stagger for exit
                  ease: "power3.in"
              });
          }
          if (imageContainer) {
              gsap.to(imageContainer, {
                  opacity: 0,
                  x: -20, // Slide out to left
                  duration: 0.5,
                  ease: "power3.in"
              });
          }
      }

      gsap.to(".transition-bar", {
        y: "0%",
        duration: 1.0,
        stagger: 0.1,
        ease: "power3.inOut",
        onComplete: () => {
          window.location.href = href;
        },
      });
    });
  });
});
