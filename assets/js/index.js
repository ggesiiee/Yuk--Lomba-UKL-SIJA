document.addEventListener("DOMContentLoaded", () => {
  const menuItems = document.querySelectorAll(".menu-item");

  menuItems.forEach((item) => {
    item.addEventListener("click", function (e) {
      e.preventDefault();

      menuItems.forEach((i) => i.classList.remove("active"));

      this.classList.add("active");
    });
  });
});
