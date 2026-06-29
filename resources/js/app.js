import axios from 'axios';
import Alpine from 'alpinejs';

// Bind Alpine globally
window.Alpine = Alpine;
Alpine.start();

// Mock Axios API responses to demonstrate Async states & loading animations
const mockApiDelay = 1200; // 1.2 seconds delay

const mockAxiosHandler = (method, url, data = null) => {
    console.log(`%c[Mock Axios API] %c${method.toUpperCase()} %c-> ${url}`, 'color: #6366f1; font-weight: bold', 'color: #10b981; font-weight: bold', 'color: #475569', data);
    
    return new Promise((resolve) => {
        setTimeout(() => {
            resolve({
                status: 200,
                statusText: 'OK',
                data: {
                    success: true,
                    message: `Simulasi request ${method.toUpperCase()} berhasil diproses oleh backend.`,
                    timestamp: new Date().toISOString(),
                    data: data
                }
            });
        }, mockApiDelay);
    });
};

// Replace standard axios calls with mocked handlers for frontend demo safety
window.axios = {
    post: (url, data) => mockAxiosHandler('post', url, data),
    put: (url, data) => mockAxiosHandler('put', url, data),
    delete: (url, data) => mockAxiosHandler('delete', url, data),
    get: (url, params) => mockAxiosHandler('get', url, params),
    
    // Maintain standard configuration defaults structure
    defaults: {
        headers: {
            common: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }
    }
};

console.log('SmartBiz ERP App loaded. Axios Mock and AlpineJS initialized successfully.');
