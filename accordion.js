// script.js
const headers = document.querySelectorAll('.accordion-header');

headers.forEach(header => {
  header.addEventListener('click', () => {
    const content = header.nextElementSibling;

    // Tutup semua konten lain jika hanya satu yang boleh terbuka
    headers.forEach(h => {
      if (h !== header) {
        h.nextElementSibling.style.maxHeight = null;
      }
    });

    // Toggle konten
    if (content.style.maxHeight) {
      content.style.maxHeight = null;
    } else {
      content.style.maxHeight = content.scrollHeight + "px";
    }
  });
});
