document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("orderSearch");
    if (!searchInput) return;
  
    searchInput.addEventListener("input", function () {
      const value = this.value.toLowerCase();
      const rows = document.querySelectorAll("#orderTableBody tr");
  
      rows.forEach((row) => {
        const userText = row.cells[1].textContent.toLowerCase();
        const contentTitle = row.cells[3].textContent.toLowerCase();
        const match = userText.includes(value) || contentTitle.includes(value);
        row.style.display = match ? "" : "none";
      });
    });
  });  