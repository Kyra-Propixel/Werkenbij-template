
      const checkbox = document.getElementById("check");
  
      checkbox.addEventListener("change", function() {
        if (this.checked) {
          document.body.classList.add("no-scroll");
        } else {
          document.body.classList.remove("no-scroll");
        }
      });
      
// zorgt ervoor dat uitklapmenu sluit bij klikken van een link
const navLinks = document.querySelectorAll('.nav-center ul li a');

// Voeg een klik event listener toe aan elk van deze links
navLinks.forEach(link => {
  link.addEventListener('click', () => {
    // Vink de checkbox uit en verwijder de no-scroll klasse
    checkbox.checked = false;
    document.body.classList.remove('no-scroll');
  });
});


window.addEventListener("scroll", function() {
  var navbar = document.querySelector("nav");

  // Controleer of de gebruiker meer dan 300px heeft gescrold
  if (window.scrollY > 200) {
    navbar.classList.add("scrolled");
  } else {
    navbar.classList.remove("scrolled");
  }
});

    document.addEventListener("DOMContentLoaded", function() {
        const accordionButtons = document.querySelectorAll(".accordion-button");

        accordionButtons.forEach(button => {
            button.addEventListener("click", function() {
                const isExpanded = this.getAttribute("aria-expanded") === "true";
                const content = this.nextElementSibling;

                // Sluit alle andere accordeon-items
                accordionButtons.forEach(b => {
                    if (b !== this) {
                        b.setAttribute("aria-expanded", "false");
                        const otherContent = b.nextElementSibling;
                        otherContent.classList.remove('open');
                    }
                });

                // Open de huidige accordeon alleen als deze nog niet geopend is
                if (!isExpanded) {
                    this.setAttribute("aria-expanded", "true");
                    content.classList.add('open');
                } else {
                    this.setAttribute("aria-expanded", "false");
                    content.classList.remove('open');
                }
              });
            });
        });


document.addEventListener('DOMContentLoaded', function() {
  var openFormBtn = document.getElementById('open-form-btn');
  var openSolForm = document.getElementById('open-sol-form');

  openFormBtn.addEventListener('click', function(event) {
    event.preventDefault(); // Voorkom standaard linkgedrag

    var isExpanded = openFormBtn.getAttribute('aria-expanded') === 'true';

    // Toggle the `aria-expanded` attribute
    openFormBtn.setAttribute('aria-expanded', !isExpanded); // Wissel tussen true en false
    openSolForm.setAttribute('aria-expanded', !isExpanded); // Wissel tussen true en false
  });
});


function openPopup(detailsId) {
    const details = document.getElementById(detailsId).innerHTML; // Huidige details laden
    document.getElementById('popup-text').innerHTML = details;

    const popup = document.getElementById('popup');
    const popupContent = popup.querySelector('.popup-content');
    popup.style.display = 'flex'; // Show the popup

    // Begin met de animatie na een korte vertraging
    setTimeout(() => {
        popup.classList.add('show'); // Add show class for fade in
        popupContent.classList.add('show'); // Add show class for scale in
    }, 10); // Kleine vertraging om de weergave toe te laten
}

function closePopup(event) {
    if (event) {
        event.stopPropagation(); // Voorkom dat de popup sluit als je op de inhoud klikt
    }
    const popup = document.getElementById('popup');
    const popupContent = popup.querySelector('.popup-content');
    popupContent.classList.remove('show'); // Remove scale-in class
    popup.classList.remove('show'); // Remove fade-in class

    // Verberg de popup na de animatie
    setTimeout(() => {
        popup.style.display = 'none'; // Hide the popup after animation
    }, 400); // Dit moet overeenkomen met de duur van de overgang
}






