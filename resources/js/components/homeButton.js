import gsap from "gsap";

document.addEventListener("DOMContentLoaded", () => {
  const buttons = document.querySelectorAll(".btn_home, .confirm_btn");
  if (!buttons.length) return;

  buttons.forEach((btn) => {
    let hoverTimeline = null;

    function updatePosition(e) {
      const rect = btn.getBoundingClientRect();
      const x = ((e.clientX - rect.left) / rect.width) * 100;
      const y = ((e.clientY - rect.top) / rect.height) * 100;
      btn.style.setProperty("--mx", x + "%");
      btn.style.setProperty("--my", y + "%");
    }

    btn.addEventListener("mouseenter", (e) => {
      updatePosition(e);
      if (hoverTimeline) hoverTimeline.kill();
      hoverTimeline = gsap.timeline();
      hoverTimeline.to(btn, {
        duration: 0.6,
        "--r": "300px",
        ease: "power2.out",
      });
    });

    btn.addEventListener("mousemove", (e) => {
      updatePosition(e);
    });

    btn.addEventListener("mouseleave", () => {
      if (hoverTimeline) hoverTimeline.kill();
      hoverTimeline = gsap.timeline();
      hoverTimeline.to(btn, {
        duration: 0.4,
        "--r": "0px",
        ease: "power2.inOut",
      });
    });
  });
});
