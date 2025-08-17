const slides = document.querySelectorAll(".slide");
const prevBtn = document.querySelector(".prev");
const nextBtn = document.querySelector(".next");
let currentIndex = 0;

function showSlide(index) {
  slides.forEach((slide, i) => {
    slide.classList.remove("active");
    if (i === index) {
      slide.classList.add("active");
    }
  });
}

function nextSlide() {
  currentIndex = (currentIndex + 1) % slides.length;
  showSlide(currentIndex);
}

function prevSlideFunc() {
  currentIndex = (currentIndex - 1 + slides.length) % slides.length;
  showSlide(currentIndex);
}

nextBtn.addEventListener("click", nextSlide);
prevBtn.addEventListener("click", prevSlideFunc);

// Otomatik geçiş (opsiyonel)
setInterval(nextSlide, 6000);

// Menü linkine tıklayınca menüyü kapat
document.querySelectorAll(".navbar-links a").forEach(link => {
  link.addEventListener("click", () => {
    document.getElementById("navbar-toggle").checked = false;
  });
});

