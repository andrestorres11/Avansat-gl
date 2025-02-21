// Muestra/oculta el dropdown al hacer clic en el bot�n
function toggleAlerts() {
    let dropdown = document.getElementById("alert-dropdown");
    if (dropdown.style.display === "block") {
      dropdown.style.display = "none";
    } else {
      dropdown.style.display = "block";
    }
  }
  
  // Cierra el dropdown si se hace clic fuera de �l
  document.addEventListener("click", function(event) {
    let button = document.querySelector(".alert-button-pri");
    let dropdown = document.getElementById("alert-dropdown");
    if (button && dropdown && !button.contains(event.target) && !dropdown.contains(event.target)) {
      dropdown.style.display = "none";
    }
  });
  
  // Actualiza la fecha/hora cada segundo
  function updateClock() {
    const now = new Date();
    const dateTimeStr = now.toLocaleString();
    const navbarDateElem = document.getElementById("navbarDate");
    if (navbarDateElem) {
      navbarDateElem.innerText = dateTimeStr;
    }
  }
  setInterval(updateClock, 1000);
  updateClock();
  
  // Variable global para almacenar la cantidad previa de alertas
  let lastBadgeCount = 0;
  
  // Variable para determinar si el usuario ya interactu� (permitiendo reproducir audio)
  let audioAllowed = false;
  document.addEventListener('click', function() {
    audioAllowed = true;
  });
  
  // Funci�n para reproducir el sonido de notificaci�n (solo si se ha interactuado)
  function playNotificationSound() {
    if (!audioAllowed) return;
    const audio = new Audio('../satt_standa/sounds/notification-sound.mp3');
    console.log("Reproduciendo audio");
    audio.play().catch(error => console.error('Error al reproducir el audio:', error));
  }
  
  // Funci�n para actualizar las alertas mediante AJAX
  function updateAlerts() {
    fetch('../satt_standa/despac/navbar_ajax.php?Option=GetBadge')
      .then(response => response.json())
      .then(data => {
        // Se espera que 'data' tenga dos claves: "badge" y "alerts"
        const newCount = parseInt(data.badge, 10);
        
        // Si hay nuevas alertas, reproduce el sonido
        if (newCount > lastBadgeCount) {
          playNotificationSound();
        }
        lastBadgeCount = newCount;
        
        // Actualiza el badge del bot�n de alertas
        const badgeElem = document.querySelector('.alert-button-pri .badge');
        if (badgeElem) {
          badgeElem.innerText = newCount;
        }
        
        // Actualiza el contenido del dropdown con la lista de alertas
        const dropdown = document.getElementById('alert-dropdown');
        if (dropdown) {
          // Limpia el contenido anterior
          dropdown.innerHTML = '';
          data.alerts.forEach(alert => {
            // Crea un contenedor para cada alerta
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert-item';
            
            // Crea el badge interno para la alerta
            const spanBadge = document.createElement('span');
            spanBadge.className = 'item-badge';
            spanBadge.innerText = alert.tot_psoluc; // Muestra el tot_psoluc
            alertDiv.appendChild(spanBadge);
            
            // Crea el enlace que redirecciona a la URL deseada
            const link = document.createElement('a');
            link.href = "index.php?cod_servic=3302&window=central&despac=" + alert.num_despac + "&opcion=1";
            // Estilos en l�nea para el enlace
            link.style.color = "#FFF";
            link.style.fontWeight = "bold";
            link.style.backgroundColor = "#2c368d";
            link.style.padding = "2px 9px";
            link.style.borderRadius = "5px";
            
            // Si el nombre de la transportadora es muy largo, trunca a 45 caracteres y a�ade "..."
            let displayNomTransp = alert.nom_transp || "";
            if (displayNomTransp.length > 45) {
              displayNomTransp = displayNomTransp.substring(0, 45) + "...";
            }
            // Texto del enlace: num_despac - nom_transp (truncado)
            link.innerText = alert.num_despac + " - " + displayNomTransp;
            
            // Agrega el enlace al contenedor de la alerta
            alertDiv.appendChild(link);
            
            // Agrega el contenedor de la alerta al dropdown
            dropdown.appendChild(alertDiv);
          });
        }
      })
      .catch(error => console.error('Error al obtener notificaciones:', error));
  }
  
  // Ejecuta updateAlerts cada 10 segundos (ajusta el intervalo seg�n tus necesidades)
  setInterval(updateAlerts, 10000);
  updateAlerts();
  