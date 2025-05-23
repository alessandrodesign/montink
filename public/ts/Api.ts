import ApiClient from './ApiClient.js';

const api = (baseUrl?: string) => {
    const apiClient = new ApiClient(baseUrl);

    let submitButton: any[] = [];

    function loadingButton() {
        let content = document.createElement('span'),
            icon = document.createElement('span'),
            span = document.createElement('span');
        icon.classList.add('spinner-border', 'spinner-border-sm', 'me-2');
        icon.setAttribute('aria-hidden', 'true');
        span.setAttribute('role', 'status');
        span.innerHTML = 'Loading...';
        content.appendChild(icon);
        content.appendChild(span);
        return content;
    }

    function nowLoading(formElement: HTMLFormElement) {
        let tmp = formElement.querySelector('[type="submit"]') as HTMLButtonElement,
            tmpId = 'btn_' + Date.now();

        submitButton.push({
            id: tmpId,
            content: tmp.innerHTML,
            width: tmp.offsetWidth,
            height: tmp.offsetHeight,
            attributes: tmp.attributes
        });

        tmp.disabled = true;
        tmp.textContent = null;
        tmp.setAttribute('data-loading', tmpId);
        tmp.appendChild(loadingButton());
    }

    function stopLoading() {
        submitButton.map(button => {
            let tmp = document.querySelector(`[data-loading="${button.id}"]`) as HTMLButtonElement;
            if (tmp) {
                tmp.disabled = false;
                tmp.textContent = button.content;
            }
        });
    }

    /**
     * Envia dados para o endpoint usando POST.
     */
    async function create(endpoint: string, data?: any, method: string = 'post') {
        let response;

        switch (method.toLowerCase()) {
            case 'get':
                const queryString = data ? `?${new URLSearchParams(data).toString()}` : '';
                response = await apiClient.get(`${endpoint}${queryString}`);
                break;
            case 'post':
                response = await apiClient.post(endpoint, { data });
                break;
            case 'put':
                response = await apiClient.put(endpoint, { data });
                break;
            case 'delete':
                response = await apiClient.delete(endpoint, { data });
                break;
            default:
                throw new Error(`Método HTTP não suportado: ${method}`);
        }

        stopLoading();
        return response.data;
    }

    /**
     * Registra um listener de submit em um formulário, envia os dados via POST,
     * e executa funções de callback após envio ou em erro.
     */
    async function submit(
        formElement: HTMLFormElement,
        afterSubmit: (responseData: any) => void,
        onError?: (error: any) => void
    ) {
        formElement.addEventListener('submit', async (e) => {
            e.preventDefault();

            nowLoading(formElement);

            const action = formElement.action;
            const method = formElement.method.toUpperCase();
            let endpoint;

            try {
                let url = new URL(action);
                endpoint = url.pathname;
            } catch (error) {
                endpoint = action;
            }

            switch (method) {
                case 'POST': {
                    const formData = new FormData(formElement);
                    const data: Record<string, any> = {};

                    formData.forEach((value, key) => {
                        data[key] = value;
                    });

                    try {
                        const response = await create(endpoint, data);
                        afterSubmit(response);
                    } catch (error) {
                        if (onError) onError(error);
                        else alert(`Falha ao enviar para ${endpoint}: ${error}`);
                    }
                    break;
                }

                default:
                    console.warn(`Método ${method} não implementado.`);
                    break;
            }
        });
    }

    return {
        create,
        submit
    };
};

export default api;
