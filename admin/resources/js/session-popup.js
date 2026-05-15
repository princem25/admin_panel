document.addEventListener('DOMContentLoaded', () => {
    const successMessage = document.body.getAttribute('data-session-success');
    const errorMessage = document.body.getAttribute('data-session-error');

    if (successMessage && successMessage !== '') {
        showPopup(successMessage, 'success');
    } else if (errorMessage && errorMessage !== '') {
        showPopup(errorMessage, 'error');
    }

    function showPopup(message, type) {
        const popup = document.createElement('div');
        popup.className = `fixed top-5 left-1/2 transform -translate-x-1/2 z-[9999] p-3 px-6 rounded-xl shadow-2xl border backdrop-blur-md transition-all duration-500 flex items-center justify-between gap-4 ${
            type === 'success' 
                ? 'bg-green-100 text-green-700 border-green-300 dark:bg-green-300/20 dark:text-green-200 dark:border-green-300/30 font-semibold' 
                : 'bg-red-100 text-red-700 border-red-300 dark:bg-red-300/20 dark:text-red-200 dark:border-red-300/30 font-semibold'
        }`;

        popup.innerHTML = `
            <div class="flex items-center gap-2">
                <span class="text-xl">${type === 'success' ? '✅' : '❌'}</span>
                <span class="whitespace-nowrap">${message}</span>
            </div>
            <button class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors duration-200" id="close-popup-btn">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;

        document.body.appendChild(popup);

        // Add event listener to close button
        const closeBtn = popup.querySelector('#close-popup-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                popup.style.opacity = '0';
                popup.style.transform = 'translate(-50%, -20px)';
                setTimeout(() => popup.remove(), 500);
            });
        }

        // Auto-hide after 3 seconds
        setTimeout(() => {
            if (popup.parentElement) {
                popup.style.opacity = '0';
                popup.style.transform = 'translate(-50%, -20px)';
                setTimeout(() => popup.remove(), 500);
            }
        }, 3000);
    }
});
