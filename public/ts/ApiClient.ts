/**
 * Opções para configurar a requisição
 */
interface RequestOptions {
    headers?: Record<string, string>;
    params?: Record<string, string>;
    data?: any;
    token?: string;
    timeout?: number;
    onProgress?: (progress: number) => void;
    responseType?: 'json' | 'text' | 'blob' | 'arrayBuffer';
}

/**
 * Resposta da API
 */
interface ApiResponse<T = any> {
    data: T;
    status: number;
    headers: Headers;
    ok: boolean;
}

/**
 * Classe para gerenciar requisições HTTP com suporte a:
 * - Autenticação via token
 * - Upload de arquivos com barra de progresso
 * - Timeout
 * - Tratamento de erros
 * - Diferentes tipos de resposta
 */
export default class ApiClient {
    private readonly baseUrl: string;
    private readonly defaultHeaders: Record<string, string>;
    private readonly defaultTimeout: number;
    private authToken?: string;

    /**
     * Cria uma nova instância do ApiClient
     * @param baseUrl URL base para todas as requisições
     * @param defaultHeaders Headers padrão para todas as requisições
     * @param defaultTimeout Timeout padrão em milissegundos
     */
    constructor(
        baseUrl?: string,
        defaultHeaders: Record<string, string> = {},
        defaultTimeout: number = 30000
    ) {
        this.baseUrl = baseUrl ? (baseUrl.endsWith('/') ? baseUrl.slice(0, -1) : baseUrl) : this.getBaseUrl();
        this.defaultHeaders = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...defaultHeaders
        };
        this.defaultTimeout = defaultTimeout;
    }

    private getBaseUrl(): string {
        const base = document.head.querySelector('base');
        if (base !== null) {
            return base.getAttribute('href') as string;
        }
        throw new Error('No base url found.');
    }

    /**
     * Define o token de autenticação para todas as requisições
     * @param token Token de autenticação
     */
    setAuthToken(token: string): void {
        this.authToken = token;
    }

    /**
     * Remove o token de autenticação
     */
    clearAuthToken(): void {
        this.authToken = undefined;
    }

    /**
     * Realiza uma requisição GET
     * @param endpoint Endpoint da API
     * @param options Opções da requisição
     */
    async get<T = any>(endpoint: string, options: RequestOptions = {}): Promise<ApiResponse<T>> {
        return this.request<T>('GET', endpoint, options);
    }

    /**
     * Realiza uma requisição POST
     * @param endpoint Endpoint da API
     * @param options Opções da requisição
     */
    async post<T = any>(endpoint: string, options: RequestOptions = {}): Promise<ApiResponse<T>> {
        return this.request<T>('POST', endpoint, options);
    }

    /**
     * Realiza uma requisição PUT
     * @param endpoint Endpoint da API
     * @param options Opções da requisição
     */
    async put<T = any>(endpoint: string, options: RequestOptions = {}): Promise<ApiResponse<T>> {
        return this.request<T>('PUT', endpoint, options);
    }

    /**
     * Realiza uma requisição PATCH
     * @param endpoint Endpoint da API
     * @param options Opções da requisição
     */
    async patch<T = any>(endpoint: string, options: RequestOptions = {}): Promise<ApiResponse<T>> {
        return this.request<T>('PATCH', endpoint, options);
    }

    /**
     * Realiza uma requisição DELETE
     * @param endpoint Endpoint da API
     * @param options Opções da requisição
     */
    async delete<T = any>(endpoint: string, options: RequestOptions = {}): Promise<ApiResponse<T>> {
        return this.request<T>('DELETE', endpoint, options);
    }

    /**
     * Faz upload de um arquivo com barra de progresso
     * @param endpoint Endpoint da API
     * @param file Arquivo a ser enviado
     * @param fieldName Nome do campo do arquivo
     * @param extraData Dados adicionais para enviar junto com o arquivo
     * @param onProgress Callback para acompanhar o progresso do upload
     */
    async uploadFile<T = any>(
        endpoint: string,
        file: File,
        fieldName: string = 'file',
        extraData: Record<string, any> = {},
        onProgress?: (progress: number) => void
    ): Promise<ApiResponse<T>> {
        const formData = new FormData();
        formData.append(fieldName, file);

        // Adiciona dados extras ao FormData
        Object.entries(extraData).forEach(([key, value]) => {
            formData.append(key, value);
        });

        return this.request<T>('POST', endpoint, {
            data: formData,
            headers: {}, // Remover Content-Type para que o navegador defina o boundary correto
            onProgress
        });
    }

    /**
     * Faz upload de múltiplos arquivos com barra de progresso
     * @param endpoint Endpoint da API
     * @param files Arquivos a serem enviados
     * @param fieldName Nome do campo dos arquivos
     * @param extraData Dados adicionais para enviar junto com os arquivos
     * @param onProgress Callback para acompanhar o progresso do upload
     */
    async uploadFiles<T = any>(
        endpoint: string,
        files: File[],
        fieldName: string = 'files',
        extraData: Record<string, any> = {},
        onProgress?: (progress: number) => void
    ): Promise<ApiResponse<T>> {
        const formData = new FormData();

        // Adiciona múltiplos arquivos com o mesmo nome de campo
        files.forEach(file => {
            formData.append(fieldName, file);
        });

        // Adiciona dados extras ao FormData
        Object.entries(extraData).forEach(([key, value]) => {
            formData.append(key, value);
        });

        return this.request<T>('POST', endpoint, {
            data: formData,
            headers: {}, // Remover Content-Type para que o navegador defina o boundary correto
            onProgress
        });
    }

    /**
     * Faz download de um arquivo
     * @param endpoint Endpoint da API
     * @param filename Nome do arquivo para download
     * @param options Opções da requisição
     */
    async downloadFile(
        endpoint: string,
        filename: string,
        options: RequestOptions = {}
    ): Promise<void> {
        const response = await this.request<Blob>('GET', endpoint, {
            ...options,
            responseType: 'blob'
        });

        // Cria um link temporário para download
        const url = window.URL.createObjectURL(response.data);
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();

        // Limpa recursos
        link.parentNode?.removeChild(link);
        window.URL.revokeObjectURL(url);
    }

    /**
     * Método principal para realizar requisições HTTP
     * @param method Método HTTP
     * @param endpoint Endpoint da API
     * @param options Opções da requisição
     */
    private async request<T = any>(
        method: string,
        endpoint: string,
        options: RequestOptions = {}
    ): Promise<ApiResponse<T>> {
        const url = this.buildUrl(endpoint, options.params);
        const headers = this.buildHeaders(options.headers, options.token);
        const timeout = options.timeout || this.defaultTimeout;

        // Configuração da requisição
        const config: RequestInit = {
            method,
            headers,
            //credentials: 'include', // Inclui cookies nas requisições cross-origin
        };

        // Adiciona corpo da requisição para métodos não-GET
        if (method !== 'GET' && options.data !== undefined) {
            if (options.data instanceof FormData) {
                config.body = options.data;
            } else {
                config.body = JSON.stringify(options.data);
            }
        }

        try {
            // Cria um AbortController para timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), timeout);
            config.signal = controller.signal;

            // Realiza a requisição com suporte a progresso
            const response = await this.fetchWithProgress(url, config, options.onProgress);
            clearTimeout(timeoutId);

            // Processa a resposta de acordo com o tipo solicitado
            const data = await this.parseResponse<T>(response, options.responseType || 'json');

            return {
                data: data,
                status: response.status,
                headers: response.headers,
                ok: response.ok
            };
        } catch (error) {
            if (error instanceof DOMException && error.name === 'AbortError') {
                throw new Error(`Request timeout after ${timeout}ms`);
            }

            throw error;
        }
    }

    /**
     * Constrói a URL completa com parâmetros de consulta
     * @param endpoint Endpoint da API
     * @param params Parâmetros de consulta
     */
    private buildUrl(endpoint: string, params?: Record<string, string>): string {
        const base = this.baseUrl.replace(/\/+$/, ''); // remove barras no final
        const path = endpoint.startsWith('/') ? endpoint : `/${endpoint}`;
        const url = `${base}${path}`;

        if (!params) return url;

        const queryParams = new URLSearchParams();
        Object.entries(params).forEach(([key, value]) => {
            queryParams.append(key, value);
        });

        return `${url}?${queryParams.toString()}`;
    }

    /**
     * Constrói os headers da requisição
     * @param customHeaders Headers personalizados
     * @param token Token de autenticação específico para esta requisição
     */
    private buildHeaders(
        customHeaders?: Record<string, string>,
        token?: string
    ): Record<string, string> {
        const headers = {...this.defaultHeaders, ...customHeaders};

        // Adiciona token de autenticação se disponível
        const authToken = token || this.authToken;
        if (authToken) {
            headers['Authorization'] = `Bearer ${authToken}`;
        }

        return headers;
    }

    /**
     * Realiza uma requisição fetch com suporte a progresso
     * @param url URL da requisição
     * @param config Configuração da requisição
     * @param onProgress Callback para acompanhar o progresso
     */
    private async fetchWithProgress(
        url: string,
        config: RequestInit,
        onProgress?: (progress: number) => void
    ): Promise<Response> {
        // Se não precisamos monitorar o progresso, use fetch diretamente
        if (!onProgress || !config.body || !(config.body instanceof FormData)) {
            return fetch(url, config);
        }

        // Para upload com progresso, usamos XMLHttpRequest
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open(config.method || 'GET', url);

            // Adiciona headers
            if (config.headers) {
                Object.entries(config.headers).forEach(([key, value]) => {
                    xhr.setRequestHeader(key, value);
                });
            }

            // Configura eventos
            xhr.onload = () => {
                const response = new Response(xhr.response, {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    headers: this.parseXHRHeaders(xhr)
                });
                resolve(response);
            };

            xhr.onerror = () => {
                reject(new Error('Network request failed'));
            };

            xhr.ontimeout = () => {
                reject(new Error('Request timeout'));
            };

            // Monitora o progresso do upload
            xhr.upload.onprogress = (event) => {
                if (event.lengthComputable && onProgress) {
                    const progress = Math.round((event.loaded / event.total) * 100);
                    onProgress(progress);
                }
            };

            // Envia a requisição
            xhr.send(config.body as FormData);
        });
    }

    /**
     * Converte os headers do XMLHttpRequest para um objeto Headers
     * @param xhr Instância do XMLHttpRequest
     */
    private parseXHRHeaders(xhr: XMLHttpRequest): Headers {
        const headersString = xhr.getAllResponseHeaders();
        const headersArray = headersString.trim().split(/[\r\n]+/);
        const headers = new Headers();

        headersArray.forEach(line => {
            const parts = line.split(': ');
            const header = parts.shift();
            const value = parts.join(': ');
            if (header) {
                headers.append(header, value);
            }
        });

        return headers;
    }

    /**
     * Processa a resposta conforme o tipo solicitado
     * @param response Resposta da requisição
     * @param responseType Tipo de resposta desejado
     */
    private async parseResponse<T>(
        response: Response,
        responseType: 'json' | 'text' | 'blob' | 'arrayBuffer'
    ): Promise<T> {
        if (!response.ok && response.status < 500) {
            return await response.json();
        }

        switch (responseType) {
            case 'json':
                return response.json();
            case 'text':
                return await response.text() as unknown as T;
            case 'blob':
                return await response.blob() as unknown as T;
            case 'arrayBuffer':
                return await response.arrayBuffer() as unknown as T;
            default:
                return response.json();
        }
    }
}