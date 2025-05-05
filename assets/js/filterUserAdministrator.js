document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("userSearch");
    if (!searchInput) return;
  
    searchInput.addEventListener("input", function () {
      const search = this.value.toLowerCase();
      const rows = document.querySelectorAll("table tbody tr");
  
      rows.forEach((row) => {
        const username = row.cells[1].textContent.toLowerCase();
        const firstname = row.cells[2].textContent.toLowerCase();
        const lastname = row.cells[3].textContent.toLowerCase();
        const email = row.cells[4].textContent.toLowerCase();
  
        if (
          username.includes(search) ||
          firstname.includes(search) ||
          lastname.includes(search) ||
          email.includes(search)
        ) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    });
  });  