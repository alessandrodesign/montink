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
    private readonly baseUrl;
    private readonly defaultHeaders;
    private readonly defaultTimeout;
    private authToken?;
    /**
     * Cria uma nova instância do ApiClient
     * @param baseUrl URL base para todas as requisições
     * @param defaultHeaders Headers padrão para todas as requisições
     * @param defaultTimeout Timeout padrão em milissegundos
     */
    constructor(baseUrl?: string, defaultHeaders?: Record<string, string>, defaultTimeout?: number);
    private getBaseUrl;
    /**
     * Define o token de autenticação para todas as requisições
     * @param token Token de autenticação
     */
    setAuthToken(token: string): void;
    /**
     * Remove o token de autenticação
     */
    clearAuthToken(): void;
    /**
     * Realiza uma requisição GET
     * @param endpoint Endpoint da API
     * @param options Opções da requisição
     */
    get<T = any>(endpoint: string, options?: RequestOptions): Promise<ApiResponse<T>>;
    /**
     * Realiza uma requisição POST
     * @param endpoint Endpoint da API
     * @param options Opções da requisição
     */
    post<T = any>(endpoint: string, options?: RequestOptions): Promise<ApiResponse<T>>;
    /**
     * Realiza uma requisição PUT
     * @param endpoint Endpoint da API
     * @param options Opções da requisição
     */
    put<T = any>(endpoint: string, options?: RequestOptions): Promise<ApiResponse<T>>;
    /**
     * Realiza uma requisição PATCH
     * @param endpoint Endpoint da API
     * @param options Opções da requisição
     */
    patch<T = any>(endpoint: string, options?: RequestOptions): Promise<ApiResponse<T>>;
    /**
     * Realiza uma requisição DELETE
     * @param endpoint Endpoint da API
     * @param options Opções da requisição
     */
    delete<T = any>(endpoint: string, options?: RequestOptions): Promise<ApiResponse<T>>;
    /**
     * Faz upload de um arquivo com barra de progresso
     * @param endpoint Endpoint da API
     * @param file Arquivo a ser enviado
     * @param fieldName Nome do campo do arquivo
     * @param extraData Dados adicionais para enviar junto com o arquivo
     * @param onProgress Callback para acompanhar o progresso do upload
     */
    uploadFile<T = any>(endpoint: string, file: File, fieldName?: string, extraData?: Record<string, any>, onProgress?: (progress: number) => void): Promise<ApiResponse<T>>;
    /**
     * Faz upload de múltiplos arquivos com barra de progresso
     * @param endpoint Endpoint da API
     * @param files Arquivos a serem enviados
     * @param fieldName Nome do campo dos arquivos
     * @param extraData Dados adicionais para enviar junto com os arquivos
     * @param onProgress Callback para acompanhar o progresso do upload
     */
    uploadFiles<T = any>(endpoint: string, files: File[], fieldName?: string, extraData?: Record<string, any>, onProgress?: (progress: number) => void): Promise<ApiResponse<T>>;
    /**
     * Faz download de um arquivo
     * @param endpoint Endpoint da API
     * @param filename Nome do arquivo para download
     * @param options Opções da requisição
     */
    downloadFile(endpoint: string, filename: string, options?: RequestOptions): Promise<void>;
    /**
     * Método principal para realizar requisições HTTP
     * @param method Método HTTP
     * @param endpoint Endpoint da API
     * @param options Opções da requisição
     */
    private request;
    /**
     * Constrói a URL completa com parâmetros de consulta
     * @param endpoint Endpoint da API
     * @param params Parâmetros de consulta
     */
    private buildUrl;
    /**
     * Constrói os headers da requisição
     * @param customHeaders Headers personalizados
     * @param token Token de autenticação específico para esta requisição
     */
    private buildHeaders;
    /**
     * Realiza uma requisição fetch com suporte a progresso
     * @param url URL da requisição
     * @param config Configuração da requisição
     * @param onProgress Callback para acompanhar o progresso
     */
    private fetchWithProgress;
    /**
     * Converte os headers do XMLHttpRequest para um objeto Headers
     * @param xhr Instância do XMLHttpRequest
     */
    private parseXHRHeaders;
    /**
     * Processa a resposta conforme o tipo solicitado
     * @param response Resposta da requisição
     * @param responseType Tipo de resposta desejado
     */
    private parseResponse;
}
export {};
