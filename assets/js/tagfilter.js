document.addEventListener("DOMContentLoaded", () => {
  console.log("Theme filtering initialized");

  const buttons = document.querySelectorAll(".theme-filters button");
  const sections = document.querySelectorAll(".theme-section");

  buttons.forEach((button) => {
    // Listen for clicks on each filter button
    button.addEventListener("click", () => {
      const themeId = button.dataset.themeId;

      // Remove 'active' class from all buttons, then add it to the clicked one
      buttons.forEach((btn) => btn.classList.remove("active"));
      button.classList.add("active");

      // Show or hide sections based on the selected theme
      sections.forEach((section) => {
        const isMatch =
          themeId === "all" || section.dataset.themeId === themeId;
        section.style.display = isMatch ? "block" : "none";
      });
    });
  });
});