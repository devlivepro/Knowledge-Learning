document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.getElementById("sidebar");
  const btn = document.getElementById("hamburgerButton");

  if (sidebar && btn) {
    // Toggle sidebar visibility on button click
    btn.addEventListener("click", () => {
      const opened = sidebar.classList.toggle("active");
      btn.innerHTML = opened ? "✖" : "☰";
    });

    // Close sidebar when clicking outside of it or the toggle button
    document.addEventListener("click", (e) => {
      if (
        sidebar.classList.contains("active") &&
        !sidebar.contains(e.target) &&
        !btn.contains(e.target)
      ) {
        sidebar.classList.remove("active");
        btn.innerHTML = "☰";
      }
    });

    // (Optional) Close sidebar on Escape key press
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && sidebar.classList.contains("active")) {
        sidebar.classList.remove("active");
        btn.innerHTML = "☰";
      }
    });
  }

  // Theme filtering logic
  document.querySelectorAll(".filter-btn").forEach((filterBtn) => {
    filterBtn.addEventListener("click", () => {
      // Remove 'active' class from all filter buttons
      document
        .querySelectorAll(".filter-btn")
        .forEach((btn) => btn.classList.remove("active"));

      // Add 'active' class to the clicked button
      filterBtn.classList.add("active");

      // Show or hide theme sections based on the selected filter
      const id = filterBtn.dataset.themeId;
      document.querySelectorAll(".theme-section").forEach((section) => {
        section.style.display =
          id === "all" || section.dataset.themeId === id ? "block" : "none";
      });
    });
  });
});
