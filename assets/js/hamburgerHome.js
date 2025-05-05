document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.getElementById("sidebar-home");
  const btn = document.getElementById("hamburgerButton");
  if (!sidebar || !btn) return;

  // Toggle sidebar
  btn.addEventListener("click", () => {
    const isOpen = sidebar.classList.toggle("active");
    btn.innerText = isOpen ? "✖" : "☰";
  });

  // Close on link click
  sidebar.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      if (sidebar.classList.contains("active")) {
        sidebar.classList.remove("active");
        btn.innerText = "☰";
      }
    });
  });

  // Close on outside click
  document.addEventListener("click", (e) => {
    if (
      sidebar.classList.contains("active") &&
      !sidebar.contains(e.target) &&
      !btn.contains(e.target)
    ) {
      sidebar.classList.remove("active");
      btn.innerText = "☰";
    }
  });

  // Close on Escape
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && sidebar.classList.contains("active")) {
      sidebar.classList.remove("active");
      btn.innerText = "☰";
    }
  });

  // ➕ Hide sidebar and button on scroll down, show on scroll up
  let lastScrollTop = 0;
  window.addEventListener("scroll", () => {
    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

    if (currentScroll > lastScrollTop) {
      // Scroll down
      if (sidebar.classList.contains("active")) {
        sidebar.classList.remove("active");
        btn.innerText = "☰";
      }
    }
    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
  });
});