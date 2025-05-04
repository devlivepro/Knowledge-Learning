document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.getElementById("sidebar-home");
  const btn = document.getElementById("hamburgerButton");
  if (!sidebar || !btn) return;

  // Toggle the sidebar visibility when the hamburger button is clicked
  btn.addEventListener("click", () => {
    const isOpen = sidebar.classList.toggle("active");
    btn.innerText = isOpen ? "✖" : "☰";
  });

  // Close the sidebar when any link inside it is clicked
  sidebar.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      if (sidebar.classList.contains("active")) {
        sidebar.classList.remove("active");
        btn.innerText = "☰";
      }
    });
  });

  // Close the sidebar when clicking outside of it or the toggle button
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

  // Optionally close the sidebar on pressing the Escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && sidebar.classList.contains("active")) {
      sidebar.classList.remove("active");
      btn.innerText = "☰";
    }
  });
});
