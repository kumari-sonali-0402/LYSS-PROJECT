document.addEventListener("DOMContentLoaded", () => {
  const menuList = document.getElementById("menuList");
  if (menuList) {
    const menu = [
      {
        meal: "Breakfast",
        items: "Poha, Boiled Eggs, Tea",
        emoji: "🍳"
      },
      {
        meal: "Lunch",
        items: "Rice, Dal, Mixed Veg, Chapati, Salad",
        emoji: "🍛"
      },
      {
        meal: "Dinner",
        items: "Paneer Curry, Jeera Rice, Roti, Kheer",
        emoji: "🍲"
      }
    ];

    menuList.innerHTML = menu.map(m =>
      `<div class="menu-card">
        <h3>${m.emoji} ${m.meal}</h3>
        <p>${m.items}</p>
      </div>`
    ).join("");
  }
});
