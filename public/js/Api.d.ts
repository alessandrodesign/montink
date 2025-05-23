declare const api: (baseUrl?: string) => {
    create: (endpoint: string, data?: any, method?: string) => Promise<any>;
    submit: (formElement: HTMLFormElement, afterSubmit: (responseData: any) => void, onError?: ((error: any) => void) | undefined) => Promise<void>;
};
export default api;
