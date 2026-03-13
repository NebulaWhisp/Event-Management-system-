// === Theme Toggle ===
const themeToggle = document.getElementById("themeToggle");
if (themeToggle) {
  themeToggle.addEventListener("click", () => {
    document.body.classList.toggle("dark");
    localStorage.setItem("theme", document.body.classList.contains("dark") ? "dark" : "light");
  });

  if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark");
  }
}

// === Booking Modal ===
const modal = document.getElementById("bookingModal");
const closeModal = document.querySelector(".close-modal");
const bookButtons = document.querySelectorAll(".book-btn");
const eventTypeInput = document.getElementById("eventType");

if (bookButtons && modal) {
  bookButtons.forEach(button => {
    button.addEventListener("click", () => {
      const service = button.getAttribute("data-service");
      eventTypeInput.value = service;
      modal.style.display = "flex";
    });
  });
}

if (closeModal) {
  closeModal.addEventListener("click", () => {
    modal.style.display = "none";
  });
}

window.onclick = (e) => {
  if (e.target === modal) modal.style.display = "none";
};

// === Booking Form Submission (AJAX optional) ===
const bookingForm = document.getElementById("bookingForm");
if (bookingForm) {
  bookingForm.addEventListener("submit", (e) => {
    e.preventDefault();
    document.getElementById("formMessage").textContent = "✅ Booking submitted successfully!";
    bookingForm.reset();

    // Optional: hide modal after 2 seconds
    setTimeout(() => {
      modal.style.display = "none";
    }, 1500);
  });
}
