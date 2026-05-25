import axios from "axios";

const http = axios.create({
    baseURL: "/api",
    headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
    },
});

export class ApiError extends Error {
    constructor(message, status, errors = {}) {
        super(message);
        this.name = "ApiError";
        this.status = status;
        this.errors = errors;
    }
}

export const apiRequest = async (path, options = {}) => {
    const { method = "GET", body, token } = options;
    const headers = token ? { Authorization: `Bearer ${token}` } : {};

    try {
        const response = await http.request({
            url: path,
            method,
            headers,
            data: body,
        });

        return response.data;
    } catch (error) {
        if (error.response) {
            throw new ApiError(
                error.response.data?.message || "Có lỗi xảy ra khi gọi API.",
                error.response.status,
                error.response.data?.errors || {},
            );
        }

        throw new ApiError(
            "Không thể kết nối API, vui lòng kiểm tra server backend.",
            0,
        );
    }
};
