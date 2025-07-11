// let scrollPosition = 0;

// document.getElementById('btnStarter').addEventListener('click', function(e) {
//   e.preventDefault();
  

//   const paketType = this.getAttribute('data-paket');
  
//   const overlay = document.getElementById('transactionOverlay');
//   scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
  
//   // 1. Lock scroll position
//   document.body.style.position = 'fixed';
//   document.body.style.top = `-${scrollPosition}px`;
//   document.body.style.width = '100%';
  
//   // 2. Show overlay
//   overlay.style.display = 'flex';

  
//  // 3. Load konten dari transaction.html (diubah untuk 1 file)
//     fetch('transaction.html')
//       .then(response => response.text())
//       .then(html => {
//         // Parse HTML untuk ekstrak konten spesifik paket
//         const parser = new DOMParser();
//         const doc = parser.parseFromString(html, 'text/html');
//         const paketContent = doc.querySelector(`.overlay-content[data-paket="${paketType}"]`);
        
//         if (paketContent) {
//           overlay.innerHTML = paketContent.outerHTML;
//           overlay.style.display = 'flex';
      
//       // 4. Reset overlay scroll
//       const content = overlay.querySelector('.overlay-content');
//       if (content) content.scrollTop = 0;
      
//             // 5. Initialize interactive elements
//           initOverlayComponents();
//         } else {
//           throw new Error(`Konten paket ${paketType} tidak ditemukan`);
//         }
//       })
//       .catch(error => {
//         console.error('Error loading overlay:', error);
//         closeOverlay();
//     });
// });

// document.getElementById('btnExclusive').addEventListener('click', function(e) {
//   e.preventDefault();
  

//   const paketType = this.getAttribute('data-paket');
  
//   const overlay = document.getElementById('transactionOverlay');
//   scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
  
//   // 1. Lock scroll position
//   document.body.style.position = 'fixed';
//   document.body.style.top = `-${scrollPosition}px`;
//   document.body.style.width = '100%';
  
//   // 2. Show overlay
//   overlay.style.display = 'flex';

  
//  // 3. Load konten dari transaction.html (diubah untuk 1 file)
//     fetch('transaction.html')
//       .then(response => response.text())
//       .then(html => {
//         // Parse HTML untuk ekstrak konten spesifik paket
//         const parser = new DOMParser();
//         const doc = parser.parseFromString(html, 'text/html');
//         const paketContent = doc.querySelector(`.overlay-content[data-paket="${paketType}"]`);
        
//         if (paketContent) {
//           overlay.innerHTML = paketContent.outerHTML;
//           overlay.style.display = 'flex';
      
//       // 4. Reset overlay scroll
//       const content = overlay.querySelector('.overlay-content');
//       if (content) content.scrollTop = 0;
      
//             // 5. Initialize interactive elements
//           initOverlayComponents();
//         } else {
//           throw new Error(`Konten paket ${paketType} tidak ditemukan`);
//         }
//       })
//       .catch(error => {
//         console.error('Error loading overlay:', error);
//         closeOverlay();
//     });
// });

// document.getElementById('btnPremium').addEventListener('click', function(e) {
//   e.preventDefault();
  

//   const paketType = this.getAttribute('data-paket');
  
//   const overlay = document.getElementById('transactionOverlay');
//   scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
  
//   // 1. Lock scroll position
//   document.body.style.position = 'fixed';
//   document.body.style.top = `-${scrollPosition}px`;
//   document.body.style.width = '100%';
  
//   // 2. Show overlay
//   overlay.style.display = 'flex';

  
//  // 3. Load konten dari transaction.html (diubah untuk 1 file)
//     fetch('transaction.html')
//       .then(response => response.text())
//       .then(html => {
//         // Parse HTML untuk ekstrak konten spesifik paket
//         const parser = new DOMParser();
//         const doc = parser.parseFromString(html, 'text/html');
//         const paketContent = doc.querySelector(`.overlay-content[data-paket="${paketType}"]`);
        
//         if (paketContent) {
//           overlay.innerHTML = paketContent.outerHTML;
//           overlay.style.display = 'flex';
      
//       // 4. Reset overlay scroll
//       const content = overlay.querySelector('.overlay-content');
//       if (content) content.scrollTop = 0;
      
//             // 5. Initialize interactive elements
//           initOverlayComponents();
//         } else {
//           throw new Error(`Konten paket ${paketType} tidak ditemukan`);
//         }
//       })
//       .catch(error => {
//         console.error('Error loading overlay:', error);
//         closeOverlay();
//     });
// });

// function initOverlayComponents() {
//   // Interval selector
//   const decreaseBtn = document.getElementById('decreaseBtn');
//   const increaseBtn = document.getElementById('increaseBtn');
//   const intervalInput = document.getElementById('intervalInput');
  
//   if (decreaseBtn && increaseBtn && intervalInput) {
//     decreaseBtn.addEventListener('click', () => {
//       let val = parseInt(intervalInput.value);
//       intervalInput.value = val > 2 ? val - 1 : 2;
//     });
    
//     increaseBtn.addEventListener('click', () => {
//       let val = parseInt(intervalInput.value);
//       intervalInput.value = val < 5 ? val + 1 : 5;
//     });
    
//     intervalInput.addEventListener('input', () => {
//       let val = parseInt(intervalInput.value) || 2;
//       intervalInput.value = Math.min(Math.max(val, 2), 5);
//     });
//   }
  
//   // Close button
//    const closeBtn = document.getElementById('btnCloseOverlay');
//       if (closeBtn) {
//         closeBtn.addEventListener('click', closeOverlay);
//       } else {
//         console.error('Tombol close tidak ditemukan!');
//       }
      
// }

// function closeOverlay() {
//   const overlay = document.getElementById('transactionOverlay');
  
//   // 1. Hide overlay
//   overlay.style.display = 'none';
  
//   // 2. Restore scroll
//   document.body.style.position = '';
//   document.body.style.top = '';
//   document.body.style.width = '';
//   window.scrollTo(0, scrollPosition);
  
//   // 3. Force reflow for mobile browsers
//   setTimeout(() => {
//     document.documentElement.style.scrollBehavior = 'auto';
//     window.scrollTo(0, scrollPosition);
//     document.documentElement.style.scrollBehavior = '';
//   }, 20);
// }

// // Event delegation untuk tombol close
// document.addEventListener('click', function(e) {
//   if (e.target.closest('#btnCloseOverlay')) {
//     closeOverlay();
//   }
// });

// // Di fungsi utama Anda
// async function showPackageWizard(packageType) {
//     const config = packageConfigs[packageType];
//     const steps = ["1", "2", "3"];
    
//     const Queue = Swal.mixin({
//         progressSteps: steps,
//         scrollbarPadding: false, // 1. Nonaktifkan padding scrollbar
//         backdrop: 'rgba(0,0,0,0.4)', // 2. Gunakan backdrop biasa
//         showClass: {
//             popup: '',
//             backdrop: ''
//         },
//         hideClass: {
//             popup: '',
//             backdrop: ''
//         },
//         didDestroy: () => {
//             // 3. Pastikan reset saat wizard selesai
//             document.body.style.overflow = 'auto';
//             document.body.style.paddingRight = '0';
//         }
//     });

//     // ... langkah-langkah wizard Anda
// }