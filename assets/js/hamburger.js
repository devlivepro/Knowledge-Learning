document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.getElementById("sidebar");
  const hamburgerBtn = document.querySelector(".hamburger");

  if (!sidebar || !hamburgerBtn) return;

  // Toggle sidebar on hamburger click
  hamburgerBtn.addEventListener("click", () => {
    const isOpen = sidebar.classList.toggle("active");
    hamburgerBtn.textContent = isOpen ? "✖" : "☰";
  });

  // Close sidebar on outside click
  document.addEventListener("click", (e) => {
    if (
      sidebar.classList.contains("active") &&
      !sidebar.contains(e.target) &&
      !hamburgerBtn.contains(e.target)
    ) {
      sidebar.classList.remove("active");
      hamburgerBtn.textContent = "☰";
    }
  });

  // Close on Escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && sidebar.classList.contains("active")) {
      sidebar.classList.remove("active");
      hamburgerBtn.textContent = "☰";
    }
  });

  // Close on scroll down
  let lastScroll = 0;
  window.addEventListener("scroll", () => {
    const currentScroll = window.scrollY;
    if (currentScroll > lastScroll && sidebar.classList.contains("active")) {
      sidebar.classList.remove("active");
      hamburgerBtn.textContent = "☰";
    }
    lastScroll = currentScroll;
  });
});