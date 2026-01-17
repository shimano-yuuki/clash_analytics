import axios from 'axios';

const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000';

const apiClient = axios.create({
    baseURL: `${API_URL}/api`,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    withCredentials: true,
});

// リクエストインターセプター（認証トークンなど）
apiClient.interceptors.request.use(
    (config) => {
        // 必要に応じてトークンを追加
        // const token = localStorage.getItem('token');
        // if (token) {
        //   config.headers.Authorization = `Bearer ${token}`;
        // }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// レスポンスインターセプター（エラーハンドリング）
apiClient.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // 未認証エラーの処理
            // window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default apiClient;

// API関数の例
export const videoApi = {
    getAll: () => apiClient.get('/videos'),
    getById: (id: string | number) => apiClient.get(`/videos/${id}`),
    upload: (formData: FormData) => apiClient.post('/videos/upload', formData),
    analyze: (id: string | number) => apiClient.post(`/videos/${id}/analyze`),
    delete: (id: string | number) => apiClient.delete(`/videos/${id}`),
};

