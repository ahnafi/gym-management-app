import type { route as routeFn } from 'ziggy-js';

export {}; // 👈 Important: make this a module

declare global {
    const route: typeof routeFn;

    interface SnapResult {
        transactionStatus?: string;
        transactionId?: string;
        // Add more fields as needed
    }

    interface Window {
        snap: {
            pay: (
                snapToken: string,
                callbacks: {
                    onSuccess?: (result: SnapResult) => void;
                    onPending?: (result: SnapResult) => void;
                    onError?: (result: SnapResult) => void;
                }
            ) => void;
        };
    }
}
